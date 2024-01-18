<?php $company = $this->Xin_model->read_company_setting_info(1); ?>
<?php $favicon = base_url() . 'uploads/logo/favicon/' . $company[0]->favicon; ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title; ?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">
	<style>
		*,
		*::before,
		*::after {
			box-sizing: border-box
		}

		html {
			/* font-family: 'Alice', serif; */
			font-family: 'Plus Jakarta Sans', sans-serif !important;
			line-height: 1.15;
			-webkit-text-size-adjust: 100%;
			-webkit-tap-highlight-color: rgba(24, 28, 33, 0)
		}

		body {
			margin: 0 auto;
			/* font-family: 'Plus Jakarta Sans', "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif !important; */
			font-size: .894rem;
			font-weight: 400;
			line-height: 1.47;
			color: #4E5155;
			text-align: left;

			/* set size to a4 */
			width: 210mm !important;
			height: 297mm !important;
			/* background: #e8e8e9; */

		}

		[tabindex="-1"]:focus {
			outline: 0 !important
		}

		hr {
			border: none;
			height: 1px !important;
			background-color: rgba(24, 28, 33, 0.000001);
		}

		table {
			border-collapse: collapse
		}

		th {
			text-align: inherit
		}

		.container {
			width: 100%;
		}

		.table {
			width: 100%;
			margin-bottom: 0.6rem;
			color: #4E5155
		}

		.table th,
		.table td {
			padding: .325rem;
			vertical-align: top;
		}

		.table thead th {
			vertical-align: bottom;
		}

		.table tbody+tbody {
			border-top: 2px solid #e8e8e9
		}

		.card {
			position: relative;
			display: -ms-flexbox;
			display: flex;
			-ms-flex-direction: column;
			flex-direction: column;
			min-width: 0;
			word-wrap: break-word;
			background-color: #fff;
			background-clip: border-box;
			border: 1px solid rgba(24, 28, 33, 0.6);
			border-radius: .75rem
		}

		.card>hr {
			margin-right: 0;
			margin-left: 0
		}

		.card>.list-group:first-child .list-group-item:first-child {
			border-top-left-radius: .25rem;
			border-top-right-radius: .25rem
		}

		.card>.list-group:last-child .list-group-item:last-child {
			border-bottom-right-radius: .25rem;
			border-bottom-left-radius: .25rem
		}

		.card-body {
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;
			padding: 1.5rem
		}
	</style>
	<style>
		.justify-content-start {
			-ms-flex-pack: start !important;
			justify-content: flex-start !important;
		}

		.justify-content-end {
			-ms-flex-pack: end !important;
			justify-content: flex-end !important;
		}

		.justify-content-center {
			-ms-flex-pack: center !important;
			justify-content: center !important;
		}

		.justify-content-between {
			-ms-flex-pack: justify !important;
			justify-content: space-between !important;
		}

		.justify-content-around {
			-ms-flex-pack: distribute !important;
			justify-content: space-around !important;
		}

		.align-items-start {
			-ms-flex-align: start !important;
			align-items: flex-start !important;
		}

		.align-items-end {
			-ms-flex-align: end !important;
			align-items: flex-end !important;
		}

		.align-items-center {
			-ms-flex-align: center !important;
			align-items: center !important;
		}

		.align-items-baseline {
			-ms-flex-align: baseline !important;
			align-items: baseline !important;
		}

		.align-items-stretch {
			-ms-flex-align: stretch !important;
			align-items: stretch !important;
		}

		.align-content-start {
			-ms-flex-line-pack: start !important;
			align-content: flex-start !important;
		}

		.align-content-end {
			-ms-flex-line-pack: end !important;
			align-content: flex-end !important;
		}

		.align-content-center {
			-ms-flex-line-pack: center !important;
			align-content: center !important;
		}

		.align-content-between {
			-ms-flex-line-pack: justify !important;
			align-content: space-between !important;
		}

		.align-content-around {
			-ms-flex-line-pack: distribute !important;
			align-content: space-around !important;
		}

		.align-content-stretch {
			-ms-flex-line-pack: stretch !important;
			align-content: stretch !important;
		}

		.d-flex {
			display: -ms-flexbox !important;
			display: flex !important;
		}

		.text-danger {
			color: #d9534f !important
		}

		.text-warning {
			color: #FFD950 !important
		}

		.text-success {
			color: #02BC77 !important
		}
	</style>

	<style>
		.td-70 {
			width: 70% !important;
		}

		.td-30 {
			width: 30% !important;
		}

		.td-20 {
			width: 20% !important;
		}

		.td-50 {
			width: 50% !important;
		}

		.td-40 {
			width: 40% !important;
		}

		.text-title {
			color: #0084CE !important;
		}

		.px-3 {
			padding: 0 1mm;
		}
	</style>
</head>

<body>
	<div class="container">
		<table>
			<tr>
				<td>
					<img src="https://i.ibb.co/Dg85Lc9/Header-Print.png" alt="Header Print" width="20mm">
				</td>
				<td>
					<span style="color:#2E3192;font-family:'Alice';font-size:xx-large;">&nbsp;&nbsp;CV. PERKASATAMA TEKNIK</span>
				</td>
			</tr>
		</table>
		<br>
		<hr>
		<br>
		<table class="table px-3">
			<tr>
				<td class="td-60">
					<strong class="text-title"><?= $this->lang->line('ms_title_proof_transfer'); ?></strong>
					<br>
					<?= $record->status == "paid" ? "<span class='text-success'>" . strtoupper($record->status) . "</span>" : "<span class='text-warning'>" . strtoupper($record->status)  . "</span>"; ?>
				</td>
				<td style="align-items: center;" class="td-40">
					<table>
						<tr>
							<td><?= $this->lang->line('ms_title_number_document'); ?></td>
							<td><strong><?= $record->trans_number; ?></strong></td>
						</tr>
						<tr>
							<td><?= $this->lang->line('ms_title_date'); ?></td>
							<td><strong><?= $this->Xin_model->set_date_format($record->date); ?></strong></td>
						</tr>
					</table>
				</td>
			</tr>

		</table>
		<br>
		<br>
		<div class="card">
			<div class="card-body">
				<table class="table">
					<tr>
						<td class="td-40"><strong><?= $this->lang->line('ms_title_source_account'); ?></strong></td>
						<td rowspan="2" class="td-20">
							<img src="https://i.ibb.co/p0s4Kpb/transfer.png" alt="transfer" width="25mm">
						</td>
						<td class="td-40">
							<strong><?= $this->lang->line('ms_title_terget_account'); ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?= $record->source_account; ?>
						</td>
						<td>
							<?= $record->target_account; ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br>
		<br>
		<table class="table px-3">
			<tr>
				<td class="td-60">
					<strong>Amount</strong>
				</td>
				<td class="td-40">
					<?= $this->Xin_model->currency_sign($record->amount); ?>
				</td>
			</tr>
			<tr>
				<td class="td-60">
					<strong>Nilai Terbilang</strong>
				</td>
				<td class="td-34">
					<?= formatRupiah($record->amount); ?>
				</td>
			</tr>
			<tr>
				<td class="td-60">
					<strong>Reference</strong>
				</td>
				<td class="td-40">
					<?= $record->ref; ?>
				</td>
			</tr>
			<tr>
				<td class="td-60">
					<strong>Note</strong>
				</td>
				<td class="td-40">
					<?= $record->note; ?>
				</td>
			</tr>
		</table>
		<br>
		<hr>
		<br>
		<br>
		<table class="table">
			<tr>
				<td align="center" class="td-40">
					<strong><?= $this->lang->line('ms_title_trans_made_by'); ?></strong>
					<br><br><br><br>
					<span><?= $record->user_created; ?></span>
				</td>
				<td class="td-20"></td>
				<td align="center" class="td-40">
					<strong><?= $this->lang->line('ms_title_trans_approved_by'); ?></strong>
					<br><br><br><br>
					<span><?= $record->user_approved; ?></span>
				</td>
			</tr>
		</table>
	</div>
</body>


</html>