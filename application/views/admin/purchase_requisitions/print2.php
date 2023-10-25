<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Contoh Kop Surat</title>
	<link rel="stylesheet" href="styles.css" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'Plus Jakarta Sans', sans-serif;
			background: #f2f2f2;
			/* margin: 0;
				padding: 20; */
		}

		.container {
			margin: 0 1cm;
		}

		.header img {
			width: 100%;
		}

		.card {
			/* background-color: #f9f9f9; */
			background-color: #ffffff;
			/* border: 1px solid #ccc; */
			border-radius: 10px;
			padding: 20px;
			margin-bottom: 20px;
		}

		.card h2 {
			margin-top: 0;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
			border-spacing: 6px;
		}

		.table th,
		.table td {
			border: 1px solid #ccc;
			padding: 8px;
			text-align: left;
		}

		.table th {
			background-color: #f2f2f2;
		}

		/* Add color to even rows */
		tr.even {
			background-color: #e6f7ff;
			/* You can change this color to your preference */
		}

		.text-primary {
			color: #007bff !important;
		}

		a.text-primary:focus,
		a.text-primary:hover {
			color: #0056b3 !important;
		}

		.text-secondary {
			color: #6c757d !important;
		}

		a.text-secondary:focus,
		a.text-secondary:hover {
			color: #494f54 !important;
		}

		.text-success {
			color: #28a745 !important;
		}

		a.text-success:focus,
		a.text-success:hover {
			color: #19692c !important;
		}

		.text-info {
			color: #17a2b8 !important;
		}

		a.text-info:focus,
		a.text-info:hover {
			color: #0f6674 !important;
		}

		.text-warning {
			color: #ffc107 !important;
		}

		a.text-warning:focus,
		a.text-warning:hover {
			color: #ba8b00 !important;
		}

		.text-danger {
			color: #dc3545 !important;
		}

		a.text-danger:focus,
		a.text-danger:hover {
			color: #a71d2a !important;
		}

		.text-light {
			color: #f8f9fa !important;
		}

		a.text-light:focus,
		a.text-light:hover {
			color: #cbd3da !important;
		}

		.text-dark {
			color: #343a40 !important;
		}

		a.text-dark:focus,
		a.text-dark:hover {
			color: #121416 !important;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="header">
			<img src="https://i.ibb.co/TMsnbtz/Header-1.png" alt="Kop Surat" />
		</div>
		<br />
		<div class="content">
			<div class="card">
				<h2><?= purchase_stats($record->purchase_status); ?></h2>

				<table cellspacing="10" cellpading="0">
					<tr>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_number'); ?></label><br />
							<strong><?= $record->pr_number; ?></strong>
						</td>
						<td>
							<label><?php echo $this->lang->line('xin_p_priority'); ?></label><br />
							<strong><?= priority_stats($record->priority_status); ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_issue_date');
									?></label><br />
							<strong><?= $this->Xin_model->set_date_format($record->issue_date);
									?></strong>
						</td>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_due_approval_date');
									?></label><br />
							<strong><?= $this->Xin_model->set_date_format($record->due_approval_date);
									?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_ref_expedition_name');
									?></label><br />
							<strong><?= $record->expedition ?? "--"; ?></strong>
						</td>
					</tr>
				</table>
			</div>

			<div class="card">
				<div class="row">
					<div class="col-md-12 mb-3">
						<h2 class="h5 mb-3"><?php echo $this->lang->line('ms_purchase_items'); ?></h2>
					</div>
					<table class="table table-striped table" id="ms_table_items">
						<thead class="thead-light">
							<tr>
								<th><?php echo $this->lang->line('xin_id_no'); ?></th>
								<th><?php echo $this->lang->line('xin_title_item'); ?></th>
								<th><?php echo $this->lang->line('xin_project'); ?></th>
								<th>
									<?php echo $this->lang->line('ms_ref_title_unit_price'); ?>
								</th>
								<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
								<th style="min-width: 150px">
									<?php echo $this->lang->line('xin_title_sub_total'); ?>
								</th>
								<!-- <th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th> -->
							</tr>
						</thead>
						<tbody id="formRow">
							<?php foreach ($records as $k => $r) { ?>
								<tr>
									<td><?= $k += 1; ?></td>
									<td><?= $r['item_name']; ?></td>
									<td><?= $r['project_name']; ?></td>
									<td><?= $r['ref_price']; ?></td>
									<td><?= $r['qty']; ?></td>
									<td><?= $r['amount']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>

						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var rows = document.querySelectorAll("#ms_table_items");

			for (var i = 0; i < rows.length; i++) {
				if (i % 2 === 1) { // Check if it's an even row (zero-based index)
					rows[i].classList.add("even");
				}
			}
		});
	</script>
	<script>
		// Function to add a new row to the table body
		function addRow2() {
			// Get the table body element
			var tbody = $("#item_product > tbody");

			// Get the number of rows in the table body
			var rowCount = tbody.children().length;

			var rowId = "row-" + rowCount;
			// Create a new row element with a unique id attribute
			var newRow = `<tr class="item-row-${rowCount}" id="item-row-${rowCount}" data-id="${rowCount}">
		<td>
		<select class="form-control row_item_id" data-plugin="select_item" name="row_item_id[]" data-placeholder="${ms_select_item}" id="row_item_id_${rowCount}" onchange="select_product(this)" value="0" required></select>
		<br><strong class="product_number" style="font-size:10px">No Selected</strong><input type="hidden" name="row_item_name[]" class="row_item_name" value="" required>
		</td>
		<td><select class="form-control row_project_id" data-plugin="select_project" name="row_project_id[]" data-placeholder="${ms_select_project}" id="row_project_id_${rowCount}" value="0"></select></td>
		<td>
		<select class="form-control row_tax_id" id="row_tax_id_${rowCount}" data-plugin="select_tax" name="row_tax_id[]" data-placeholder="${ms_select_tax}" onchange="select_tax(this)"></select>
		<input type="hidden" class="row_tax_rate" name="row_tax_rate[]" id="row_tax_rate_${rowCount}" value="0">
		<input type="hidden" class="data_tax_rate" value="0">
		<input type="hidden" class="data_tax_type" value="fixed"><br>
		<strong class="row_tax_rate_show currency" style="font-size:10px"></strong>
		</td>

		<td>
		<select class="form-control row_discount_id" id="row_discount_id_${rowCount}" data-plugin="select_discount" name="row_discount_id[]" data-placeholder="${ms_select_discount}" onchange="select_discount(this)"></select>
		<input type="hidden" class="row_discount_rate" name="row_discount_rate[]" id="row_discount_rate_${rowCount}" value="0">
		<input type="hidden" class="data_discount_type" value="0">
		<input type="hidden" class="data_discount_rate" value="0"><br>
		<strong class='row_discount_rate_show currency' style='font-size:10px'></strong>
		</td>

		<td><input type="number" class="form-control row_qty" name="row_qty[]" id="row_qty" min="1" value="1" required></td>
		<td><input type="number" class="form-control row_item_price" name="row_item_price[]" step="0.01" data-type="currency" id="row_item_price" min="1" value="0" required></td>
		<td class="text-right align-middle"><input type="hidden" class="row_amount" name="row_amount[]" id="row_amount_${rowCount}" value="0"><strong class="row_amount_show currency">0</strong></td>
		<td style="text-align:center"><button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button></td>
		</tr>`;

			// Add the row to the table body
			tbody.append(newRow);

			$('[data-plugin="select_item"]').select2({
				ajax: {
					delay: 250,
					url: site_url + "ajax_request/find_product",
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
				tags: true,
				width: "100%",
			});

			$('[data-plugin="select_project"]').select2({
				ajax: {
					delay: 250,
					url: site_url + "ajax_request/find_project",
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
				width: "200px",
			});

			$('[data-plugin="select_tax"]').select2({
				ajax: {
					delay: 250,
					url: site_url + "ajax_request/find_tax",
					data: function(params) {
						var queryParameters = {
							query: params.term,
						};
						return queryParameters;
					},
					processResults: function(data) {
						var options = [];

						data.forEach(function(item) {
							options.push({
								id: item.id,
								text: item.text,
								customAttribute: item.rate,
							});
						});

						return {
							results: options,
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

			$('[data-plugin="select_discount"]').select2({
				ajax: {
					delay: 250,
					url: site_url + "ajax_request/find_discount",
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

			update_total();
		}
	</script>

</body>

</html>