<!-- Button trigger modal -->
<div class="modal fade" id="modal-result" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" data-ke>
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content ">
			<div class="modal-header">
				<h5 class="modal-title"><?= $this->lang->line('xin_add'); ?> <?= $this->lang->line('ms_title_contact'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php $attributes = array('name' => 'liabilities', 'id' => 'liability_item_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
			<?php $hidden = array('liabilities' => 'UPDATE', '_token' => $_token); ?>
			<?php echo form_open('admin/contacts/liability_store_item', $attributes, $hidden); ?>
			<div class="form-body">
				<div class="modal-body">
					<table class="table table-borderless mx-0 mb-0" id="target_accounts_modal">
						<thead class="thead-light">
							<tr>
								<th style="width: 35%;"><?= $this->lang->line('ms_title_account'); ?></th>
								<th style="width: 20%;"><?= $this->lang->line('ms_title_note'); ?></th>
								<th style="width: 40%;"><?= $this->lang->line('ms_title_amount'); ?></th>
								<th style="width: 5%;"><?= $this->lang->line('xin_action'); ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<button type="button" class="btn btn-secondary ml-2 mt-3" onclick="addRow()"><?= $this->lang->line('xin_add_more'); ?></button>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" name="hrsale_form" class="btn btn-primary save"><?php echo $this->lang->line('xin_save'); ?> </button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<script>
	Ladda.bind('button[type=submit]');

	$("#liability_item_form").submit(function(e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		var formData = new FormData(obj[0]);
		formData.append("form", action);
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();
		jQuery.ajax({
			type: "POST",
			enctype: "multipart/form-data",
			url: e.target.action,
			data: formData,
			cache: false,
			processData: false, // Important for FormData
			contentType: false, // Important for FormData
			success: function(JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {

					$("#modal-result").modal("hide");
					toastr.options = {
						timeOut: 1000,
						onHidden: function() {
							window.location.reload();
						},
					};
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
			error: function(xhr, status, error) {
				toastr.error("Error: " + status + " | " + error);
				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				$(".icon-spinner3").hide();
				$(".save").prop("disabled", false);
				Ladda.stopAll();
			},
		});
	});
</script>

<script>
	/* ----------------------- CALCULATE ITEMS ----------------------------- */

	var formatter = new Intl.NumberFormat("id-ID", {
		style: "currency",
		currency: type_currency,
		minimumFractionDigits: 2,
	});

	function formatCurrency(number) {
		return formatter.format(number);
	}

	function formatCurrencyNumber(number) {
		return number.toLocaleString("id-ID", {
			minimumFractionDigits: 2
		});
	}

	// Function to add a new row to the table body
	function addRow() {
		// Get the table body element
		var tbody = $("#target_accounts_modal > tbody");

		// Get the number of rows in the table body
		var rowCount = tbody.children().length;

		var rowId = "row-" + rowCount;
		// Create a new row element with a unique id attribute
		var newRow = `<tr class="item-row" id="item-row-${rowCount}" data-id="${rowCount}">
		<td>
			<select class="form-control row_target_id" data-plugin="select_target_account_modal" name="row_target_id[]" id="row_target_id_${rowCount}" value="0" data-placeholder="Select Account" required>
			</select>
		</td>
		<td>
			<input type="text" name="row_note[]" id="row_note" class="form-control row_note" value="">
		</td>
		<td>
			<input type="number" min="0" class="row_amount form-control" name="row_amount[]" id="row_amount_${rowCount}" value="0">
		</td>
		<td style="text-align:center">
			<input type="hidden" class="row_type" name="row_type[]" value="INSERT">
			<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button>
		</td>
		</tr>`;

		// Add the row to the table body
		tbody.append(newRow);

		$('[data-plugin="select_target_account_modal"]').select2({
			ajax: {
				delay: 250,
				url: site_url + "ajax_request/get_accounts",
				data: function(params) {
					var queryParameters = {
						query: params.term,
					};
					return queryParameters;
				},
				processResults: function(data) {
					return {
						results: data,
					};
				},
				cache: false,
				transport: function(params, success, failure) {
					var $request = $.ajax(params);

					$request.then(success);
					$request.fail(failure);

					return $request;
				},
			},
			width: "100%",
		});

		var rowAmountSelect = $("#row_amount_" + rowCount);
		rowAmountSelect
			.closest("td")
			.find(".currency")
			.text(formatCurrency(rowAmountSelect.val()));

		// update_total();
	}

	// Calculate subtotal whenever row_qty or row_item_price is changed
	$(document).on("change click keyup load", ".row_amount", function() {
		update_total();
	});

	function update_total() {
		var total = 0;
		$(".row_amount").each(function() {
			var sub_total = $(this).val();
			total += parseFloat(sub_total);
		});

		$("#amount").val(total);
		$("#amount_show").text(formatCurrency(total));
	}

	addRow();
</script>