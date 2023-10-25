<?php
defined('BASEPATH') or exit('No direct script access allowed');
$uoms = $this->Xin_model->get_all_uoms();
$sub_categories = $this->Xin_model->get_all_product_sub_categories();
$kd_number = $this->Product_model->kd_number();
?>
<!-- Modal -->
<div class="modal fade" id="addItemProduct" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $this->lang->line('xin_close'); ?>"> <span aria-hidden="true">×</span> </button>
				<h5 class="modal-title" id="insert-modal"><strong><?= $this->lang->line('xin_add_new') ?></strong> <?= $this->lang->line('ms_products') ?></h5>
			</div>
			<?php $attributes = array('name' => 'products', 'id' => 'products', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
			<?php $hidden = array('_method' => 'INSERT', 'type' => 'products'); ?>
			<?php echo form_open('admin/products/add_product/', $attributes, $hidden); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label"><?php echo $this->lang->line('ms_product_number'); ?></label>
							<input type="text" class="form-control" name="product_number" id="product_number" placeholder="<?php echo $this->lang->line('ms_product_number'); ?>" value="<?= $kd_number; ?>" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label"><?php echo $this->lang->line('ms_product_name'); ?></label>
							<input type="text" class="form-control" name="product_name" id="product_name" placeholder="<?php echo $this->lang->line('ms_product_name'); ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label"><?php echo $this->lang->line('ms_product_uom'); ?></label>
							<select class="form-control" name="uom_id" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_measurement_units'); ?>">
								<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
								<?php foreach ($uoms->result() as $u) { ?>
									<option value="<?php echo $u->uom_id; ?>"> <?php echo $u->uom_name; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label"><?php echo $this->lang->line('ms_product_sub_categories'); ?></label>
							<select class="form-control" name="sub_category_id" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_product_sub_categories'); ?>">
								<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
								<?php foreach ($sub_categories->result() as $c) { ?>
									<option value="<?php echo $c->sub_category_id; ?>"> <?php echo $c->sub_category_name; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label"><?php echo $this->lang->line('ms_product_price'); ?></label>
							<input type="number" class="form-control" name="price" placeholder="<?php echo $this->lang->line('ms_product_price'); ?>" value="0">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="form-label"><?php echo $this->lang->line('ms_product_desc'); ?></label>
							<input type="text" class="form-control" name="product_desc" placeholder="<?php echo $this->lang->line('ms_product_desc'); ?>">
						</div>
					</div>
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