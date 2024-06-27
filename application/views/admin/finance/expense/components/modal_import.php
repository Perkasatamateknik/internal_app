		<!-- Modal -->
		<div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Import Excell</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<?php $attributes = array('name' => 'import_form', 'id' => 'imports_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
					<?php $hidden = array('type' => 'import'); ?>
					<?php echo form_open('admin/finance/expenses/import_excell', $attributes, $hidden); ?>
					<div class="modal-body">
						<div class="error">

						</div>
						<div class="form-group">
							<label for="">Import Data</label>
							<input type="file" class="form-control form-control-file" name="file" id="" placeholder="" aria-describedby="fileHelpId">
							<small id="fileHelpId" class="form-text text-muted">Format xlsx | 5Mb</small>
							<br><br>
						</div>
						<a href="<?= base_url('/uploads/unduh/template_import_biaya.xlsx') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-secondary"><i class="fa fa-download" aria-hidden="true"></i> Unduh Template Import</a>
					</div>
					<div class="modal-footer">
						<div class="float-left">

						</div>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>

		<script>
			Ladda.bind('button[type=submit]');

			$(document).ready(function() {
				var otable = $("#ms_table").DataTable({
					bDestroy: true,
					ajax: {
						url: site_url + "finance/expenses/get_ajax_expenses/",
						type: "GET",
						data: {
							filter: $('input[name="filter"]').val(),
						},
					},
					fnDrawCallback: function(settings) {
						$('[data-toggle="tooltip"]').tooltip();
					},
				});

				$("#import_form").submit(function(e) {
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
							} else if (JSON.validate != "") {
								$(".error").html(JSON.validate_error);
							}

							if (JSON.result != "") {
								$("#modal-import").modal("toggle");
								otable.ajax.reload(function() {
									toastr.success(JSON.result);
								}, true);
								toastr.success(JSON.result);
							}

							$(".icon-spinner3").hide();
							$(".save").prop("disabled", false);
							Ladda.stopAll();
							$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
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
			});
		</script>