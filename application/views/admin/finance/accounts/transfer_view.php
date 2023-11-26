<?php $id = $this->input->get('id');

if ($id == '') {
	redirect('admin/finance/accounts');
}
?>

<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>
<div class="row">
	<div class="col-md-12 mb-3">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-auto">
						<a href="#" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
					</div>
					<div class="col-md-auto">
						<button class="btn btn-primary btn-sm"><i class="fa fa-print fa-fw" aria-hidden="true"></i><?= $this->lang->line('xin_print'); ?> </button> <button class="btn btn-transparent btn-sm"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-12">
						<table class="table table-borderless">
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_source_account'); ?></span><br>
									<strong>2-98987</strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_number_document'); ?></span><br>
									<strong>TR-0000</strong>
								</td>
								<td rowspan="3">
									<span><?= $this->lang->line('ms_title_attachment'); ?></span><br>
									<strong>Example.pdf</strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_terget_account'); ?></span><br>
									<strong>1-00872</strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_ref'); ?></span><br>
									<strong>TR-0000</strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_date'); ?></span><br>
									<strong>1-00872</strong>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<table class="table table-borderless">
							<thead>
								<tr>
									<th>
										<strong><?= $this->lang->line('ms_title_desc'); ?></strong>
									</th>
									<th>
										<strong><?= $this->lang->line('ms_title_note'); ?></strong>
									</th>
									<th>
										<strong><?= $this->lang->line('ms_title_amount'); ?></strong>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Transfer dari X ke Y</td>
									<td>Lorem ipsum dolot sit amet.</td>
									<td>000:000:000</td>
								</tr>
								<tr>
									<td colspan="3"></td>
								</tr>
								<tr>
									<td colspan="3"></td>
								</tr>
							</tbody>
							<tfoot>
								<tr style="border-top: 1px solid black;">
									<td colspan="2" align="center"><strong><?= $this->lang->line('xin_amount'); ?></strong></td>
									<td><strong>000:000:000</strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>