<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php
// reports to 

$reports_to = get_reports_team_data($session['user_id']); ?>
<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/chartjs/chartjs.js"></script>

<div class="row">
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-6 d-flex align-items-stretch">
				<div class="card w-100 mb-3">
					<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_total_balance'); ?></strong>
							<div class="card-header-elements ml-md-auto">

							</div>
					</div>
					<div class="card-body">
						<h4>Rp34785873</h4>
						<hr>
						<h5>Account List</h5>
						<table class="table table-sm table-borderless w-100 ">
							<tr>
								<td class="text-left">BRI</td>
								<td class="text-right"><?= $this->Xin_model->currency_sign(140000); ?></td>
							</tr>
							<tr>
								<td class="text-left">BRI</td>
								<td class="text-right"><?= $this->Xin_model->currency_sign(140000); ?></td>
							</tr>
							<tr>
								<td class="text-left">BRI</td>
								<td class="text-right"><?= $this->Xin_model->currency_sign(140000); ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6 d-flex align-items-stretch">
				<div class="card mb-3">
					<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_income'); ?></strong>
							<div class="card-header-elements ml-md-auto">

							</div>
					</div>
					<div class="card-body">
						<h4>Rp34785873</h4>
						<hr>
						<h5>Account List</h5>
						<canvas id="lineChart" width="400" height="200"></canvas>

						<script>
							// Sample data (replace with your dynamic data)
							var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
							var data = [10, 25, 12, 30, 18];

							// Create a function to update the chart
							function updateChart() {
								var ctx = document.getElementById('lineChart').getContext('2d');
								var myChart = new Chart(ctx, {
									type: 'line',
									data: {
										labels: labels,
										datasets: [{
											label: 'Dynamic Line Chart',
											data: data,
											borderColor: 'rgba(75, 192, 192, 1)',
											borderWidth: 2,
											fill: false
										}]
									},
									options: {
										scales: {
											y: {
												beginAtZero: true
											}
										}
									}
								});
							}

							// Initial chart rendering
							updateChart();

							// Function to update chart data
							function updateData() {
								// Replace the data with your dynamic data
								labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
								data = [12, 28, 15, 35, 20, 18];

								// Destroy the existing chart (if any) and create a new one with updated data
								var chart = document.getElementById('lineChart');
								chart.parentNode.removeChild(chart);

								var newCanvas = document.createElement('canvas');
								newCanvas.id = 'lineChart';
								document.body.appendChild(newCanvas);

								updateChart();
							}

							// Example: Update the chart data every 5 seconds (for demonstration)
						</script>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-3">
					<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_income'); ?></strong>
							<div class="card-header-elements ml-md-auto">

							</div>
					</div>
					<div class="card-body">
						<div class="container"> <!-- Set the maximum width for the container -->
							<canvas id="s" style="width: 100%;height:400px"></canvas>
						</div>

						<script>
							// Sample data (replace with your dynamic data)
							var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
							var data = [1230, 1323, 2344, 4234, 4234];

							// Create a function to update the chart
							function updateChart() {
								var ctx = document.getElementById('s').getContext('2d');
								var myChart = new Chart(ctx, {
									type: 'line',
									data: {
										labels: labels,
										datasets: [{
											label: 'Dynamic Line Chart',
											data: data,
											borderColor: 'rgba(75, 192, 192, 1)',
											borderWidth: 2,
											fill: false
										}]
									},
									options: {
										scales: {
											y: {
												beginAtZero: true
											}
										}
									}
								});
							}

							// Initial chart rendering
							updateChart();

							// Function to update chart data
							function updateData() {
								// Replace the data with your dynamic data
								labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
								data = [12, 28, 15, 35, 20, 18];

								// Destroy the existing chart (if any) and create a new one with updated data
								var chart = document.getElementById('lineChart');
								chart.parentNode.removeChild(chart);

								var newCanvas = document.createElement('canvas');
								newCanvas.id = 'lineChart';
								document.body.appendChild(newCanvas);

								updateChart();
							}

							// Example: Update the chart data every 5 seconds (for demonstration)
						</script>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 d-flex align-items-stretch">
		<div class="card w-100 mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_income'); ?></strong>
					<div class="card-header-elements ml-md-auto">

					</div>
			</div>
			<div class="card-body">
				<h4>Rp34785873</h4>
				<hr>
				<h5>Account List</h5>

			</div>
		</div>
	</div>
</div>
