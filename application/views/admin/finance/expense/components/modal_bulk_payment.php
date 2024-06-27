		<!-- Modal -->
		<div class="modal fade" id="modal-bulk" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Bulk Payment</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<?php $attributes = array('name' => 'bulk_form', 'id' => 'bulk_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
					<?php $hidden = array('type' => 'import'); ?>
					<?php echo form_open('admin/finance/expenses/bulk_store_payment', $attributes, $hidden); ?>
					<div class="modal-body">
						<table class="table table-bordered table-striped">
							<tr>
								<td>Dapat Dibayar</td>
								<td><?= count($can_pay); ?></td>
								<td><?= $this->Xin_model->currency_sign($amount['can_pay']); ?></td>

							</tr>
							<tr>
								<td>Tidak dapat dibayar</td>
								<td><?= count($cant_pay); ?></td>
								<td><?= $this->Xin_model->currency_sign($amount['cant_pay']); ?></td>
							</tr>
						</table>
						<hr>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="date"><?= $this->lang->line('ms_title_date'); ?></label>
									<input type="date" name="date" id="date" class="form-control" placeholder="" value="<?= date('Y-m-d'); ?>" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="payment_ref"><?= $this->lang->line('ms_title_ref'); ?></label>
									<input type="text" name="payment_ref" id="payment_ref" class="form-control" placeholder="<?= $this->lang->line('ms_title_ref'); ?>">
								</div>
							</div>
						</div>
						<a href="#" class="btn-transpernt" data-toggle="collapse" data-target="#dp_toggle" aria-expanded="false">
							<i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('ms_title_payment_amount'); ?>
						</a>
						<div class="collapse mt-2" id="dp_toggle">
							<table class="table table-bordered table-striped">
								<thead class="thead-light">
									<tr>
										<th><?= $this->lang->line('ms_title_number_document'); ?></th>
										<th><?= $this->lang->line('ms_title_contact'); ?></th>
										<th><?= $this->lang->line('ms_title_balance_due'); ?></th>
										<th><?= $this->lang->line('ms_title_amount'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($can_pay as $r) { ?>
										<tr>
											<td>
												<?= $r['trans_number']; ?>
												<input type="hidden" name="source_payment_account[]" value="<?= $r['account_id']; ?>">
												<input type="hidden" name="_token[]" value="<?= $r['trans_number']; ?>">
											</td>
											<td>
												<?= $r['contact']; ?>
												<input type="hidden" name="contact[]" value="<?= $r['contact']; ?>">
											</td>
											<td>
												<?= $this->Xin_model->currency_sign($r['sisa_tagihan']); ?>
												<input type="hidden" name="amount_due[]" value="<?= $r['sisa_tagihan']; ?>">
											</td>
											<td>
												<input type="number" min="0" max="<?= $r['sisa_tagihan']; ?>" class="form-control amount" name="amount_paid[]" id="amount_paid" value="<?= $r['sisa_tagihan']; ?>">
											</td>
										</tr>
									<?php }; ?>
								</tbody>
							</table>

						</div>
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
				var span = '<span class="text-danger">*</span>';
				var required = $(".form-control[required]");
				required.closest(".form-group").find("label").append(span);
			});

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

				$("#bulk_form").submit(function(e) {
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
							} else {

								$("#modal-bulk").modal("toggle");
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