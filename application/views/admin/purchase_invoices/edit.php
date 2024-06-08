<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php if (in_array('521', $role_resources_ids)) { ?>
	<?php $attributes = array('name' => 'purchase_invoices', 'id' => 'purchase_invoices', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
	<?php $hidden = array('purchase_invoices' => 'UPDATE', 'pi_number' => $pi_number); ?>
	<?php echo form_open('admin/purchase_invoices/update', $attributes, $hidden); ?>
	<div class="card <?php echo $get_animate; ?>">
		<div id="accordion">
			<div class="card-header with-elements"> <span class="card-header-title mr-2">
					<a name="" id="" class="btn btn-sm btn-transparent pl-2" href="<?= base_url('admin/purchase_invoices'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i>
						<?php echo $this->lang->line('ms_title_back'); ?>
					</a>
			</div>
			<div id="add_role_form" class="collapse show add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
				<div class="card-body">
					<div class="row m-b-1">
						<div class="col-md-12">
							<div class="form-body">
								<div class="row mb-3">
									<div class="col-md-6">
										<div class="form-group">
											<label for="contact_id" class="control-label"><?php echo $this->lang->line('ms_title_contact'); ?></label><br>
											<select class="form-control" name="contact_id" data-plugin="select_contact" data-placeholder="<?php echo $this->lang->line('ms_title_select_contact'); ?>" required>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="pd_number"><?php echo $this->lang->line('ms_invoice_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_invoice_number'); ?>" id="pi_number_show" type="text" value="" readonly required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="faktur_number"><?php echo $this->lang->line('xin_invoice_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_number'); ?>" id="faktur_number" name="faktur_number" type="text" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="warehouse_assign"><?php echo $this->lang->line('ms_warehouse_assign'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_warehouse_assign'); ?>" id="warehouse_assign" name="warehouse_assign" type="text" value="" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="date"><?php echo $this->lang->line('ms_purchase_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_date'); ?>" id="date" name="date" type="date" required>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="due_date"><?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>" id="due_date" name="due_date" type="date" required>
										</div>
									</div>
									<div class=" col-md-4">
										<div class="form-group">
											<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
											<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
												<option value="0" selected><?= $this->lang->line('ms_custom'); ?></option>
												<option value="1" data-type="days">1 <?= $this->lang->line('xin_day'); ?></option>
												<option value="3" data-type="days">3 <?= $this->lang->line('xin_day'); ?></option>
												<option value="7" data-type="days">7 <?= $this->lang->line('xin_day'); ?></option>
												<option value="10" data-type="days">10 <?= $this->lang->line('xin_day'); ?></option>
												<option value="15" data-type="days">15 <?= $this->lang->line('xin_day'); ?></option>
												<option value="20" data-type="days">20 <?= $this->lang->line('xin_day'); ?></option>
												<option value="1" data-type="months">1 <?= $this->lang->line('xin_month'); ?></option>
												<option value="3" data-type="months">3 <?= $this->lang->line('xin_month'); ?></option>
												<option value="6" data-type="months">6 <?= $this->lang->line('xin_month'); ?></option>
												<option value="9" data-type="months">9 <?= $this->lang->line('xin_month'); ?></option>
												<option value="1" data-type="years">1 <?= $this->lang->line('xin_year'); ?></option>
												<option value="2" data-type="years">2 <?= $this->lang->line('xin_year'); ?></option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="reference"><?php echo $this->lang->line('ms_reference'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_reference'); ?>" id="reference" name="reference" type="text" value="" required>
										</div>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-12">
										<h5><?php echo $this->lang->line('ms_purchase_shipping_information'); ?></h5>
										<hr>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="delivery_date"><?php echo $this->lang->line('ms_delivery_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_date'); ?>" id="delivery_date" name="delivery_date" type="date" value="">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="delivery_name"><?php echo $this->lang->line('ms_delivery_name'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_name'); ?>" id="delivery_name" name="delivery_name" value="" type="text">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="delivery_number"><?php echo $this->lang->line('ms_delivery_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_number'); ?>" id="delivery_number" name="delivery_number" type="text" value="">
										</div>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-12">
										<label class="h5"><?php echo $this->lang->line('ms_purchase_items'); ?></label>
										<hr>
									</div>
									<div class="col-md-12">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-cell-input" id="item_product">
												<thead class="thead-light">
													<tr>
														<th style="min-width:100px"><?php echo $this->lang->line('xin_title_item'); ?></th>
														<th style="min-width:100px"><?php echo $this->lang->line('xin_project'); ?></th>
														<th><?php echo $this->lang->line('xin_title_taxes'); ?></th>
														<th><?php echo $this->lang->line('xin_discount'); ?></th>
														<th style="min-width:100px;max-width:200px"><?php echo $this->lang->line('xin_title_qty'); ?></th>
														<th style="min-width:100px"><?php echo $this->lang->line('ms_title_unit_price'); ?></th>
														<th style="min-width:150px" class="text-center"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
														<th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th>
													</tr>
												</thead>
												<tbody id="formRow">
												</tbody>
												<tfoot>
													<tr>
														<td>
															<button type="button" data-repeater-create="" class="btn btn-success" id="add-invoice-item" onclick="addRow()"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('xin_title_add_item'); ?></button>
														</td>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="notes"><?php echo $this->lang->line('ms_notes'); ?></label>
											<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_notes'); ?>" name="notes" id="notes" rows="3"></textarea>
										</div>
									</div>
									<div class="col-md-6">
										<table class="table table-borderless">
											<tr>
												<td class="text-right"><strong><?php echo $this->lang->line('ms_delivery_fee'); ?></strong></td>
												<td class="text-right">
													<input type="number" min="0" class="form-control delivery_fee" value="0" id="delivery_fee" name="delivery_fee">
											</tr>
											<tr>
												<td class="text-right"><strong><?php echo $this->lang->line('xin_amount'); ?></strong>
												</td>
												<td class="text-right">
													<input type="hidden" name="amount" value="0" id="amount">
													<strong id="amount_show" class="currency">0</strong>
												</td>
											</tr>
											<tr>
												<td></td>
												<td>
													<button type="submit" class="btn btn-primary btn-block"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
<?php } ?>