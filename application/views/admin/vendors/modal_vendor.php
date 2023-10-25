<?php
defined('BASEPATH') or exit('No direct script access allowed');
$all_countries = $this->Xin_model->get_countries();
?>
<!-- Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $this->lang->line('xin_close'); ?>"> <span aria-hidden="true">×</span> </button>
				<h5 class="modal-title" id="insert-modal"><strong><?= $this->lang->line('xin_add_new') ?></strong> <?= $this->lang->line('ms_vendors') ?></h5>
			</div>
			<?php $attributes = array('name' => 'vendors', 'id' => 'form_vendor', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
			<?php $hidden = array('vendors' => 'INSERT'); ?>
			<?php echo form_open('admin/vendors/create', $attributes, $hidden); ?>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label"><?php echo $this->lang->line('ms_vendor_name'); ?></label>
					<input type="text" class="form-control" name="vendor_name" placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>">
				</div>
				<div class="form-group">
					<label class="form-label"><?php echo $this->lang->line('ms_vendor_contact'); ?></label>
					<input type="text" class="form-control" name="vendor_contact" placeholder="<?php echo $this->lang->line('ms_vendor_contact'); ?>">
				</div>
				<div class="form-group">
					<label class="form-label"><?php echo $this->lang->line('ms_vendor_address'); ?></label>
					<textarea class="form-control" name="vendor_address" placeholder="<?php echo $this->lang->line('ms_vendor_address'); ?>"></textarea>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label class="form-label"><?php echo $this->lang->line('xin_city'); ?></label>
							<input class="form-control" placeholder="<?php echo $this->lang->line('xin_city'); ?>" name="city" type="text">
						</div>
						<div class="col-md-4">
							<label class="form-label"><?php echo $this->lang->line('xin_state'); ?></label>
							<input class="form-control" placeholder="<?php echo $this->lang->line('xin_state'); ?>" name="state" type="text">
						</div>
						<div class="col-md-4">
							<label class="form-label"><?php echo $this->lang->line('xin_zipcode'); ?></label>
							<input class="form-control" placeholder="<?php echo $this->lang->line('xin_zipcode'); ?>" name="zipcode" type="text">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="form-label"><?php echo $this->lang->line('xin_country'); ?></label>
					<select class="form-control" name="country" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_country'); ?>">
						<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
						<?php foreach ($all_countries as $country) { ?>
							<option value="<?php echo $country->country_id; ?>"> <?php echo $country->country_name; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('xin_close'); ?></button>
				<button type="submit" class="btn btn-primary"><i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?></button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>