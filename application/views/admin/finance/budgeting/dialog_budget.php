<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>


<div class="modal-header">
	<h5 class="modal-title"><?= $this->lang->line('ms_title_add_year_budgeting'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<?php $attributes = array('name' => 'budgeting', 'id' => 'add_budget', 'autocomplete' => 'off', 'class' => 'form'); ?>
<?php echo form_open('admin/finance/budgeting/insert_budget', $attributes); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-12">
			<div class="form-group">
				<label for="year"><?= $this->lang->line('ms_year'); ?></label>
				<select name="year" id="year" class="form-control" data-plugin="select_hrm" data-placeholder="<?= $this->lang->line('ms_title_select_year'); ?>">
					<?php
					for ($i = date('Y'); $i <= date('Y', strtotime('+10 years')); $i++) {
						$selected = '';
						if ($i == date('Y') and !in_array($i, $used_year)) {
							$selected = 'selected';
						}
						if (!in_array($i, $used_year)) {
							echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
						}
					} ?>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-primary save"><?= $this->lang->line('xin_save'); ?></button>
</div>
<?php echo form_close(); ?>

<script>
	$('[data-plugin="select_hrm"]').select2();
	$("#add_budget").submit(function(e) {
		var fd = new FormData(this);
		var obj = $(this),
			action = obj.attr("name");
		fd.append("form", action);
		e.preventDefault();
		$(".icon-spinner3").show();
		$(".save").prop("disabled", true);
		$.ajax({
			url: e.target.action,
			type: "POST",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function(JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					// On page load: datatable
					toastr.options = {
						timeOut: 500,
						onHidden: function() {
							window.location.href =
								site_url +
								"finance/budgeting/view?id=" +
								JSON.id;
						},
					};
					toastr.success(JSON.result);
					$("#add_ajax_modal").modal("hide");

					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$(".add-modal-data").modal("toggle");
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