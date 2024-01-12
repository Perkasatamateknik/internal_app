<!--  -->
<div class="row">
	<div class="col-md-12">
		<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Modal title</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						Body
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_cash_bank'); ?></strong></span>
				<div class="card-header-elements ml-md-auto">
					<a href="#" class="btn btn-primary btn-sm">
						Manage Account
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<span>1-221</span>
						<br><br>
						<h4>Balance</h4>
						<h2><?= $this->Xin_model->currency_sign(6152531878); ?></h2>
					</div>
					<div class="col-md-8">
						<div class="container"> <!-- Set the maximum width for the container -->
							<canvas id="s" style="width: 100%;height:200px"></canvas>
						</div>

						<script>
							// Sample data (replace with your dynamic data)
							var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
							var data = [1230, 1323, 2344, 4234, 4234, 1230, 1323, 2344, 4234, 4234, 1230, 1323];

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
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_cash_bank'); ?></strong></span>
				<div class="card-header-elements ml-md-auto">
					<a href="#" class="btn btn-primary btn-sm">
						Manage Account
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<span>1-221</span>
						<br><br>
						<h4>Balance</h4>
						<h2><?= $this->Xin_model->currency_sign(6152531878); ?></h2>
					</div>
					<div class="col-md-8">
						<div class="container"> <!-- Set the maximum width for the container -->
							<canvas id="ss" style="width: 100%;height:200px"></canvas>
						</div>

						<script>
							// Sample data (replace with your dynamic data)
							var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
							var data = [5542111, 2342323, 213123, 453252, 42342534, 2364765, 241246, 757642432, 346366654, 2342142, 345643, 6878543];

							// Create a function to update the chart
							function updateChart() {
								var ctx = document.getElementById('ss').getContext('2d');
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
</div>