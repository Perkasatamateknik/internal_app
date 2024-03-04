<div class="row pb-3">
	<div class="col-md-6">
		<div class="form-group">
			<h4><?= $breadcrumbs; ?></h4>
		</div>
	</div>
	<div class="col-md-6">
		<a href="<?= base_url('admin/finance/expense/create'); ?>" target="" class="btn btn-white float-right"><i class="fa fa-plus" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_expense'); ?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="card mb-4 flex-fill">
			<div class="card-header with-elements">
				<span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_paid_expenses'); ?></strong></span>
				<div class="card-header-elements ml-md-auto">
					<select class="form-control form-control-sm" data-hrm="select-hrm" name="" id="">
						<option>Month</option>
					</select>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-5">
						<div style="width: 100%; height: 100%;">
							<canvas id="myDonutChart"></canvas>
						</div>

						<script>
							// Sample data
							var data = {
								labels: ["Red", "Green"],
								datasets: [{
									data: [30, 40],
									backgroundColor: ["red", "green"],
								}]
							};

							// Create the donut chart
							var ctx = document.getElementById("myDonutChart").getContext('2d');
							var myDonutChart = new Chart(ctx, {
								type: 'doughnut',
								data: data,
								options: {
									legend: false,
								}
							});
						</script>
					</div>
					<div class="col-md-7">
						<strong><?= $this->lang->line('ms_amount'); ?></strong>
						<h2 class="my-2"><?= number_format(rand(100000000, 10000000000)); ?></h2>
						<small><?= number_format(rand(100000000, 10000000000)); ?> Transaction need to Pay</small>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card mb-4 flex-fill">
			<div class="card-header with-elements">
				<span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_paid_expenses'); ?></strong></span>
				<div class="card-header-elements ml-md-auto">
					<select class="form-control form-control-sm" data-hrm="select-hrm" name="" id="">
						<option>Month</option>
					</select>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-5">
						<div style="width: 100%; height: 100%;">
							<canvas id="myDonutChart2"></canvas>
						</div>

						<script>
							// Sample data
							var data = {
								labels: ["Pink", "Yellow"],
								datasets: [{
									data: [30, 40],
									backgroundColor: ["pink", "yellow"],
								}]
							};

							// Create the donut chart
							var ctx = document.getElementById("myDonutChart2").getContext('2d');
							var myDonutChart = new Chart(ctx, {
								type: 'doughnut',
								data: data,
								options: {
									legend: false,
								}
							});
						</script>
					</div>
					<div class="col-md-7">
						<strong><?= $this->lang->line('ms_amount'); ?></strong>
						<h2 class="my-2"><?= number_format(rand(100000000, 10000000000)); ?></h2>
						<small><?= number_format(rand(100000000, 10000000000)); ?> Transaction need to Pay</small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<h4>Expense List</h4>
<div class="row">
	<div class="col-md-3">
		<style>
			.has-search .form-control {
				padding-left: 2.375rem;
			}

			.has-search .form-control-feedback {
				position: absolute;
				z-index: 2;
				display: block;
				width: 2.375rem;
				height: 2.375rem;
				line-height: 2.375rem;
				text-align: center;
				pointer-events: none;
				color: #aaa;
			}
		</style>
		<div class="form-group has-search">
			<span class="fa fa-search form-control-feedback"></span>
			<input type="text" class="form-control" placeholder="Search">
		</div>
	</div>
	<div class="col-md-auto mx-0 px-1">
		<div class="dropdown open">
			<button class="btn btn-light dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Filter
			</button>
			<div class="dropdown-menu" aria-labelledby="triggerId">
				<button class="dropdown-item" href="#">Action</button>
				<button class="dropdown-item disabled" href="#">Disabled action</button>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				<div class="table-responsive">
					<table class="table table-hover table-striped" id="ms_table">
						<thead>
							<tr>
								<th class="align-middle"><?= $this->lang->line('ms_title_date'); ?></th>
								<th><?= $this->lang->line('ms_title_number_document'); ?></th>
								<th><?= $this->lang->line('ms_title_ref'); ?></th>
								<th><?= $this->lang->line('ms_title_account'); ?></th>
								<th><?= $this->lang->line('ms_title_status'); ?></th>
								<th><?= $this->lang->line('ms_title_beneficiary'); ?> </th>
								<th style="min-width: 150px;"><?= $this->lang->line('ms_purchase_balance_due'); ?></th>
								<th><?= $this->lang->line('xin_action'); ?> </th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>