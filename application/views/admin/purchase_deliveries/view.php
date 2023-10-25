<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $vresult = $this->Vendor_model->gel_all_vendor()->result(); ?>
<?php $presult = $this->Product_model->gel_all_product()->result(); ?>
<?php if (in_array('516', $role_resources_ids)) { ?>
	<div class="card <?php echo $get_animate; ?>">
		<div class="card-header with-elements justify-content-end align-center">
			<span class="card-header-title mr-2 my-0">
				<a name="" id="" class="btn btn-sm btn-transparent pl-2" href="<?= base_url('admin/purchase_deliveries'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i>
					<?php echo $this->lang->line('ms_title_back'); ?>
				</a>
			</span>
			<div class="ml-md-auto">
				<?php if ($has_pi) {; ?>
					<a href="<?= base_url('admin/purchase_invoices?id=' . $record->pd_number); ?>" class="btn btn-sm btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('ms_create_purchase_invoice'); ?></a>
				<?php }; ?>
				<a href="<?= base_url('admin/purchase_deliveries/print/' . $record->pd_number) ?>" target="_blank" class="ml-2 btn btn-sm btn-info"> <span class="ion ion-md-print"></span> <?php echo $this->lang->line('xin_print'); ?></a>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<table class="table table-borderless">
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_vendors'); ?></label><br>
								<strong><?= $record->vendor; ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_purchase_number'); ?></label><br>
								<strong><?= $record->pd_number; ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_status'); ?></label><br>
								<strong id="pr_id" data-id="<?= $record->pd_number; ?>"><?= pd_stats($record->status); ?></strong>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_purchase_faktur_number'); ?></label><br>
								<strong><?= $record->faktur_number; ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_warehouse_assign'); ?></label><br>
								<strong><?= $record->warehouse_assign; ?></strong>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_purchase_date'); ?></label><br>
								<strong><?= $this->Xin_model->set_date_format($record->date); ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_reference'); ?></label><br>
								<strong><?= $record->reference ?? "--"; ?></strong>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-12">

					<table class="table table-borderless">
						<tr>
							<td colspan="3"><strong><?php echo $this->lang->line('ms_purchase_shipping_information'); ?></strong></td>
						</tr>
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_delivery_date'); ?></label><br>
								<strong><?= $record->delivery_date; ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_delivery_name'); ?></label><br>
								<strong><?= $record->delivery_name; ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_delivery_number'); ?></label><br>
								<strong><?= $record->delivery_number; ?></strong>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped" id="ms_table_items">
							<thead>
								<tr>
									<th><?php echo $this->lang->line('xin_id_no'); ?></th>
									<th><?php echo $this->lang->line('xin_title_item'); ?></th>
									<th><?php echo $this->lang->line('xin_project'); ?></th>
									<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
									<th><?php echo $this->lang->line('ms_uoms'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($records as $i => $r) {; ?>
									<tr>
										<td><?= $i += 1; ?></td>
										<td><?= $r[0]; ?></td>
										<td><?= $r[1]; ?></td>
										<td><?= $r[2]; ?></td>
										<td><?= $r[3]; ?></td>
									</tr>
								<?php }; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" class="text-right"><?= $this->lang->line('ms_total_item'); ?></td>
									<td colspan="2"><strong><?= $record->total_qty; ?></strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="col-md-12">
					<br><br>
				</div>
				<div class="col-md-8 col-sm-8">
					<div class="form-group">
						<label for=""><?php echo $this->lang->line('ms_notes'); ?></label>
						<div class="purporse">
							<?= $record->notes; ?>
						</div>
					</div>
					<?php if (!is_null($record->attachment)) { ?>
						<br>
						<div class="row">
							<div class="col-md-4">
								<label for=""><?php echo $this->lang->line('xin_attachment'); ?></label><br>
								<div class="card">
									<img class="card-img-top" src="<?= base_url('/uploads/purchase/deliveries/' . $record->attachment) ?>" alt="">
									<div class="card-body p-3">
										<span class="clearfix mt-1">
											<span><?php
													$fileSize = filesize('./uploads/purchase/deliveries/' . $record->attachment);
													$formattedSize = size($fileSize);
													echo $formattedSize; ?></span>
											<a href="#" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
						</div>
					<?php }; ?>
				</div>
				<div class="col-md-4 col-sm-4">
					<table class="table table-sm table-borderless">
						<tr>
							<th><?= $this->lang->line('ms_delivery_fee'); ?></th>
							<td><strong><?= $this->Xin_model->currency_sign($record->delivery_fee); ?></strong></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="card">
		<div class="card-header">
			<strong><?php echo $this->lang->line('ms_purchase_log'); ?></strong>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table" id="ms_table_items">
							<thead>
								<tr>
									<th><?php echo $this->lang->line('ms_purchase_date'); ?></th>
									<th><?php echo $this->lang->line('ms_purchase_pic'); ?></th>
									<th><?php echo $this->lang->line('ms_purchase_origin'); ?></th>
									<th><?php echo $this->lang->line('ms_purchase_number'); ?></th>
									<th><?php echo $this->lang->line('xin_amount'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($log as $key => $value) { ?>
									<tr>
										<td><?= $this->Xin_model->set_date_format($value->date); ?></td>
										<td><?= $value->pic; ?></td>
										<td><?= $value->origin; ?></td>
										<td><?= $value->number; ?></td>
										<td><?= $value->amount; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php } ?>