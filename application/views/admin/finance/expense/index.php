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
	<form action="#">
		<div class="form-group">
			<input type="search" name="" id="" class="form-control">
		</div>
	</form>
</div>