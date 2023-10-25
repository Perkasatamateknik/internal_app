<style>
	input[type="date"] {
		outline: none !important;
		border: 0px solid !important;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_account_balance_conversion'); ?></strong></span>
				<div class="card-body">
					<form action="#">
						<div class="row">
							<div class="col-md-auto">
								<input type="date" name="tgl_awal" id="tgl_awal" class="form-control">
							</div>
							<div class="col-md-auto">
								s/d
							</div>
							<div class="col-md-auto">
								<input type="date" name="tgl_akhir" id="tgl_awal" class="form-control">
							</div>
						</div>
					</form>
					<hr>
					<input type="hidden" name="account_id" id="account_id" value=" <?= $id; ?>">
					<table class="table table-stripped table-hover" id="xin_table">
						<thead>
							<tr>
								<th><?= $this->lang->line('ms_title_account_code'); ?></th>
								<th><?= $this->lang->line('ms_title_account_name'); ?></th>
								<th><?= $this->lang->line('ms_title_account_category'); ?></th>
								<th><?= $this->lang->line('ms_title_account_balance'); ?></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>