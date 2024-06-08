<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php if (in_array('510', $role_resources_ids)) { ?>
	<div class="row">
		<div class="col-12 mb-3">
			<div class="card <?php echo $get_animate; ?>">
				<div class="card-header with-elements justify-content-end align-center">
					<span class="card-header-title mr-2 my-0">
						<a name="" id="" class="btn btn-sm pl-2" href="<?= base_url('admin/purchase_orders'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php echo $this->lang->line('ms_title_back'); ?></a>
					</span>
					<div class="ml-md-auto">
						<?php if (in_array('515', $role_resources_ids) and $has_pd and $has_pi) { ?>
							<a href="<?= base_url('admin/purchase_deliveries?id=' . $record->po_number); ?>" class="ml-2 btn btn-sm btn-warning"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('ms_create_purchase_delivery'); ?></a>
						<?php }; ?>
						<?php if (in_array('521', $role_resources_ids) and $has_pi) { ?>
							<a href="<?= base_url('admin/purchase_invoices?id=' . $record->po_number); ?>" class="ml-2 btn btn-sm btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('ms_create_purchase_invoice'); ?></a>
						<?php }; ?>
						<a href="<?= base_url('admin/purchase_orders/print/' . $record->po_number) ?>" target="_blank" class="ml-2 btn btn-sm btn-info"> <span class="ion ion-md-print"></span> <?php echo $this->lang->line('xin_print'); ?></a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-borderless">
								<tr>
									<td>
										<label><?php echo $this->lang->line('ms_title_contact'); ?></label><br>
										<a href="<?= base_url('admin/contacts/view/' . $record->contact_id) ?>" class="font-weight-bold"><?= $record->contact; ?></a>
									</td>
									<td>
										<label><?php echo $this->lang->line('ms_order_number'); ?></label><br>
										<strong><?= $record->po_number; ?></strong>
									</td>
									<td>
										<label><?php echo $this->lang->line('ms_status'); ?></label><br>
										<strong id="po_id" data-id="<?= $record->po_number; ?>"><?= po_stats($record->status); ?></strong>
									</td>

								</tr>
								<tr>
									<td>
										<label><?php echo $this->lang->line('ms_warehouse_assign'); ?></label><br>
										<strong><?= $record->warehouse_assign; ?></strong>
									</td>
									<td colspan="2">
										<label><?php echo $this->lang->line('ms_reference'); ?></label><br />
										<strong><?= $record->reference ?? "--"; ?></strong>
									</td>
								</tr>
								<tr>
									<td>
										<label><?php echo $this->lang->line('ms_purchase_date'); ?></label><br>
										<strong><?= $this->Xin_model->set_date_format($record->date); ?></strong>
									</td>
									<td>
										<label><?php echo $this->lang->line('xin_invoice_due_date'); ?></label><br>
										<strong><?= $this->Xin_model->set_date_format($record->due_date); ?></strong>
									</td>
									<td>
										<label><?php echo $this->lang->line('ms_title_termin'); ?></label><br>
										<strong><?= dateDiff($record->date, $record->due_date); ?></strong>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<label><?php echo $this->lang->line('ms_purchase_delivery_name'); ?></label><br>
										<strong><?= $record->delivery_name ?? "--"; ?></strong>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 mb-3">
			<div class="card mx-0">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped table w-100" id="ms_table_items">
								<thead>
									<tr>
										<th style="min-width:3%;"><?php echo $this->lang->line('xin_id_no'); ?></th>
										<th style="min-width:20%"><?php echo $this->lang->line('xin_title_item'); ?></th>
										<th style="min-width:17%"><?php echo $this->lang->line('xin_project'); ?></th>
										<th style="min-width:5%"><?php echo $this->lang->line('xin_title_qty'); ?></th>
										<th style="min-width:15%"><?php echo $this->lang->line('ms_title_unit_price'); ?></th>
										<th style="min-width:10%;"><?php echo $this->lang->line('xin_discount'); ?></th>
										<th style="min-width:10%;"><?php echo $this->lang->line('xin_title_taxes'); ?></th>
										<th style="min-width:15%" class="text-center"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($records as $i => $r) { ?>
										<tr>
											<td><?= $i += 1; ?></td>
											<td><?= $r[0]; ?></td>
											<td><?= $r[1]; ?></td>
											<td><?= $r[2]; ?></td>
											<td><?= $r[3]; ?></td>
											<td><?= $r[4]; ?></td>
											<td><?= $r[5]; ?></td>
											<td><?= $r[6]; ?></td>
										</tr>
									<?php }; ?>
								</tbody>
							</table>
						</div>
						<div class="col-md-12">
							<br><br>
						</div>
						<div class="col-md-8 col-sm-12">
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
										<div class="card shadow-sm">
											<img class="card-img-top" src="<?= base_url('/uploads/purchase/orders/' . $record->attachment) ?>" alt="">
											<div class="card-body p-3">
												<span class="clearfix mt-1">
													<span><?php
															$fileSize = filesize('./uploads/purchase/orders/' . $record->attachment);
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
						<div class="col-md-4 col-sm-12">
							<table class="table table-sm table-borderless">
								<tr>
									<td><strong><?php echo $this->lang->line('xin_title_sub_total'); ?></strong></td>
									<td>
										<strong id="discount_amount"><?= $this->Xin_model->currency_sign($record->subtotal); ?></strong>
									</td>
								</tr>
								<tr>
									<td><strong><?php echo $this->lang->line('xin_discount'); ?></strong></td>
									<td>
										<strong id="discount_amount"><?= $this->Xin_model->currency_sign($record->discount); ?></strong>
									</td>
								</tr>
								<tr>
									<td><strong><?php echo $this->lang->line('xin_title_taxes'); ?></strong></td>
									<td>
										<strong id="tax_amount"><?= $this->Xin_model->currency_sign($record->tax); ?></strong>
									</td>
								</tr>
								<tr>
									<td><strong><?php echo $this->lang->line('ms_delivery_fee'); ?></strong></td>
									<td>
										<strong id="delivery_fee"><?= $this->Xin_model->currency_sign($record->delivery_fee); ?></strong>
									</td>
								</tr>
								<tr>
									<td><strong><?php echo $this->lang->line('ms_title_service_fee'); ?></strong></td>
									<td>
										<strong id="service_fee"><?= $this->Xin_model->currency_sign($record->service_fee); ?></strong>
									</td>
								</tr>
								<?php if ($payment->jumlah_dibayar != 0) {
									foreach ($payment->log_payments as $key => $r) { ?>
										<tr class="text-underline">
											<td><strong><?php echo $this->lang->line('ms_payment_i'); ?> <?= $key += 1; ?></strong></td>
											<td>
												<strong id="payments"><?= $this->Xin_model->currency_sign($r->amount); ?></strong>
											</td>
										</tr>
								<?php }
								}; ?>
								<tr>
									<td><strong><?php echo $this->lang->line('xin_title_total'); ?></strong></td>
									<td>
										<strong class="text-danger" id="grand_total"><?= $this->Xin_model->currency_sign($record->total); ?></strong>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if (0 != 0) {; ?>
			<div class="col-12 mb-3">
				<div class="card">
					<div class="card-header">
						<strong><?php echo $this->lang->line('ms_title_purchase_payment'); ?></strong>
					</div>
					<div class="card-body">
						<?php $attributes = array('name' => 'payment_form', 'id' => 'payment_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
						<?php $hidden = array('type' => 'purchase_order', '_token' => $record->po_number); ?>
						<?php echo form_open('admin/purchasing/store_payment_po', $attributes, $hidden); ?>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="date"><?php echo $this->lang->line('ms_payment_date'); ?></label>
									<input type="date" name="date" id="date" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_date'); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="payment_ref"><?php echo $this->lang->line('ms_payment_ref'); ?></label>
									<input type="text" name="payment_ref" id="payment_ref" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_ref'); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="attachment"><?php echo $this->lang->line('xin_attachment'); ?></label>
									<input type="file" class="form-control" name="attachment" id="attachment">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="account_source"><?php echo $this->lang->line('ms_payment_account_source'); ?></label>
									<select name="account_id" id="account_id" class="form-control select2" data-plugin="select_account" data-placeholder="<?= $this->lang->line('ms_title_select_options'); ?>" required>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="amount_paid"><?php echo $this->lang->line('ms_payment_amount_paid'); ?></label>
									<input type="number" min="0" name="amount_paid" id="amount_paid" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_amount_paid'); ?>" value="<?= $payment->sisa_tagihan; ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="amount_paid">&nbsp;</label>
									<button type="submit" class="btn btn-primary btn-block"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		<?php }; ?>

		<?php if ($payment->jumlah_dibayar != 0) {; ?>
			<div class="col-12 mb-3">
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
												<th><?php echo $this->lang->line('ms_title_desc'); ?></th>
												<th><?php echo $this->lang->line('ms_title_accounts'); ?></th>
												<th><?php echo $this->lang->line('xin_amount'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($payment->log_payments as $key => $value) { ?>
												<tr>
													<td><?= $this->Xin_model->set_date_format($value->date); ?></td>
													<td><?= $value->first_name . "  " . $value->last_name; ?></td>
													<td><?= $value->note; ?></td>
													<td>
														<a href="<?= base_url('admin/finance/accounts/transactions?id=') . $r->account_id ?>" class='text-dark hoverable'><?= "<b>$value->account_name</b>" . "  " . $value->account_code; ?></a>
													</td>
													<td><?= $this->Xin_model->currency_sign($value->amount); ?></td>
													<td>
														<a href="#" class="text-secondary"><i class="fa fa-eye" style="opacity: 0.2;" aria-hidden="true"></i></a>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php }; ?>

		<div class="col-12 md-3">
			<div class="card">
				<div class="card-header">
					<strong><?php echo $this->lang->line('ms_document_log'); ?></strong>
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
		</div>
	</div>


	<br>

<?php } ?>