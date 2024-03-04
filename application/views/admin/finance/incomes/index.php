<?php
/* Incomedwds view
*/

?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<style>
	.scrollable-card {
		max-height: 300px;
		/* Enable horizontal scrolling */
		overflow-y: auto;
		/* Enable vertical scrolling */
	}
</style>

<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<?= $this->lang->line('xin_title_total'); ?> <?= $this->lang->line('ms_title_income'); ?>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-8">
						<h1><?= number_format(1000000); ?></h1>

					</div>
					<div class="col-md-4">
						<a href="<?= base_url('admin/finance/invoices/create'); ?>" id="" class="btn btn-primary" role="button">
							<i class="fa fa-file" aria-hidden="true"></i>
							<?= $this->lang->line('ms_title_project_invoice'); ?>
						</a>
						<a name="" id="" class="btn btn-outline-primary" href="#" role="button">
							<i class="fas fa-dollar-sign    "></i>
							<?= $this->lang->line('xin_add_new'); ?>
							<?= $this->lang->line('ms_title_sale'); ?>
						</a>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card ">
			<div class="card-body scrollable-card">
				<div class="row">
					<?php for ($i = 0; $i < 10; $i++) { ?>
						<div class="col-md-3">
							<div class="card rounded-3 shadow-sm mb-3">
								<div class="card-body">
									<div class="row">
										<div class="col-md-4">
											<img src="" alt="">
										</div>
										<div class="col-md-6">
											<strong>PT ADA</strong>
											<br>
											<small>INV-788678</small>
										</div>
										<div class="col-2">
											<i class="fa fa-arrow-right" aria-hidden="true"></i>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<strong><?= number_format(52374728); ?></strong>
								</div>
							</div>
						</div>
					<?php }; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				Add Title Here
			</div>
			<div class="card-body">
				<div style="width: 100%; height: 100%;">
					<canvas id="myDonutChart"></canvas>
				</div>

				<script>
					// Sample data
					var data = {
						labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
						datasets: [{
							data: [30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140],
							backgroundColor: ["red", "green", "blue", "yellow", "orange", "purple", "pink", "brown", "grey", "black", "white", "cyan"],
						}]
					};

					// Create the donut chart
					var ctx = document.getElementById("myDonutChart").getContext('2d');
					var myDonutChart = new Chart(ctx, {
						type: 'bar',
						data: data,
						options: {
							legend: false,
						}
					});
				</script>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				Add Title Here
			</div>
			<div class="card-body">
				<div style="width: 100%; height: 100%;">
					<canvas id="myDonutChart2"></canvas>
				</div>

				<script>
					// Sample data
					var data = {
						labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
						datasets: [{
							data: [30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140],
							backgroundColor: ["red", "green", "blue", "yellow", "orange", "purple", "pink", "brown", "grey", "black", "white", "cyan"],
						}]
					};

					// Create the donut chart
					var ctx = document.getElementById("myDonutChart2").getContext('2d');
					var myDonutChart = new Chart(ctx, {
						type: 'bar',
						data: data,
						options: {
							legend: false,
						}
					});
				</script>
			</div>
		</div>
	</div>

</div>

<div class="row mb-3">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				Add Title Here
			</div>
			<div class="card-body">
				<center>
					<h2><?= number_format(20000082); ?></h2>
					<hr>
					<div class="row justify-content-between">
						<div class="col-md-4">
							<div class="p-3">
								<i class="fa fa-home fa-2x" aria-hidden="true"></i>
								<br>
								<?= $this->lang->line('ms_title_expense'); ?>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-3">
								<i class="fa fa-file-alt fa-2x" aria-hidden="true"></i>
								<br>
								Tax
							</div>
						</div>
					</div>
				</center>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row">
			<?php for ($i = 1; $i < 6; $i++) { ?>
				<div class="col-md-4">
					<div class="card mb-3">
						<div class="card-body">
							<div class="text-center">
								<i class="fa fa-file-alt fa-2x" aria-hidden="true"></i>
								<br>
								Tax <?= $i; ?>
							</div>
						</div>
					</div>
				</div>
			<?php }; ?>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<?= $this->lang->line('ms_title_invoice'); ?>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-striped" id="ms_table">
						<thead>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>