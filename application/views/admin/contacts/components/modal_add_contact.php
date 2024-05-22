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
			<?php $attributes = array('name' => 'contacts', 'id' => 'contacts_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
			<?php $hidden = array('contacts' => 'UPDATE'); ?>
			<?php echo form_open('admin/contacts/store', $attributes, $hidden); ?>
			<div class="form-body">
				<div class="modal-body">
					<div class="row m-b-1">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_contact_type'); ?></label>
								<select class="form-control" name="contact_type" data-plugin="select_type" data-placeholder="<?php echo $this->lang->line('ms_title_contact_type'); ?>" required>
									<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>

								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_contact_name'); ?></label>
								<input type="text" class="form-control" name="contact_name" placeholder="<?php echo $this->lang->line('ms_title_contact_name'); ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_company'); ?></label>
								<input type="text" class="form-control" name="company_name" placeholder="<?php echo $this->lang->line('ms_title_company'); ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_billing_address'); ?></label>
								<input type="text" class="form-control" name="billing_address" placeholder="<?php echo $this->lang->line('ms_title_billing_address'); ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('xin_country'); ?></label>
								<select class="form-control" name="country" data-plugin="select_country" data-placeholder="<?php echo $this->lang->line('xin_country'); ?>" required>

								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label class="form-label"><?php echo $this->lang->line('xin_city'); ?></label>
										<input class="form-control" placeholder="<?php echo $this->lang->line('xin_city'); ?>" name="city" type="text">
									</div>
									<div class="col-md-6">
										<label class="form-label"><?php echo $this->lang->line('xin_state'); ?></label>
										<input class="form-control" placeholder="<?php echo $this->lang->line('xin_state'); ?>" name="province" type="text">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_email_address'); ?></label>
								<input type="text" class="form-control" name="email_address" placeholder="<?php echo $this->lang->line('ms_title_email_address'); ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('xin_phone'); ?></label>
								<input type="text" class="form-control" name="phone_number" placeholder="<?php echo $this->lang->line('xin_phone'); ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_tax_number'); ?></label>
								<input type="text" class="form-control" name="tax_number" placeholder="<?php echo $this->lang->line('ms_title_tax_number'); ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_title_date_of_birth'); ?></label>
								<input type="date" class="form-control" name="date_of_birth" placeholder="<?php echo $this->lang->line('ms_title_date_of_birth'); ?>">
							</div>
						</div>
					</div>
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
	$(document).ready(function() {
		$('[data-plugin="select_hrm"]').select2({
			width: "100%"
		});

		var span = '<span class="text-danger">*</span>';
		var required = $(".form-control[required]");
		required.closest(".form-group").find("label").append(span);
	});
</script>

<script>
	$('[data-plugin="select_type"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_contact_type",
			data: function(params) {
				var queryParameters = {
					query: params.term,
				};
				return queryParameters;
			},
			processResults: function(data) {
				// return {
				// 	results: data,
				// };
				var options = [];

				data.forEach(function(item) {
					options.push({
						id: item.id,
						text: item.text
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

	$('[data-plugin="select_country"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_country",
			data: function(params) {
				var queryParameters = {
					query: params.term,
				};
				return queryParameters;
			},
			processResults: function(data) {
				// return {
				// 	results: data,
				// };
				var options = [];

				data.forEach(function(item) {
					options.push({
						id: item.id,
						text: item.text
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
</script>

<script>
	Ladda.bind('button[type=submit]');

	$("#contacts_form").submit(function(e) {
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