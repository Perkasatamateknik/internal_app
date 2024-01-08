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
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alice&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1e70cd;
            --indigo: #6610f2;
            --purple: #6f42c1;
            --pink: #e83e8c;
            --red: #d9534f;
            --orange: #FEB744;
            --yellow: #FFD950;
            --green: #02BC77;
            --teal: #20c997;
            --cyan: #28c3d7;
            --white: #fff;
            --gray: rgba(24, 28, 33, 0.5);
            --gray-dark: rgba(24, 28, 33, 0.8);
            --primary: #6610f2;
            --secondary: #8897AA;
            --success: #02BC77;
            --info: #28c3d7;
            --warning: #FFD950;
            --danger: #d9534f;
            --light: rgba(24, 28, 33, 0.06);
            --dark: rgba(24, 28, 33, 0.9);
            --breakpoint-xs: 0;
            --breakpoint-sm: 576px;
            --breakpoint-md: 768px;
            --breakpoint-lg: 992px;
            --breakpoint-xl: 1200px;
            --font-family-sans-serif: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
            --font-family-monospace: "SFMono-Regular", Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        html {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(24, 28, 33, 0)
        }

        body {
            margin: 0 auto;
            font-family: 'Plus Jakarta Sans', "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif !important;
            font-size: .894rem;
            font-weight: 400;
            line-height: 1.47;
            color: #4E5155;
            text-align: left;

            /* set size to a4  */
            width: 210mm;
            height: 297mm;

        }

        [tabindex="-1"]:focus {
            outline: 0 !important
        }

        hr {
            border: none;
            height: 1px !important;
            background-color: rgba(24, 28, 33, 0.000001);
        }

        img {
            vertical-align: middle;
            border-style: none
        }

        svg {
            overflow: hidden;
            vertical-align: middle
        }

        table {
            border-collapse: collapse
        }

        th {
            text-align: inherit
        }

        .container {
            width: 100%;
            padding-right: .75rem;
            padding-left: .75rem;
            margin-right: auto;
            margin-left: auto
        }

        .container-fluid {
            width: 100%;
            /* width: 210mm;
			height: 297mm; */
            margin: 0 auto;
            padding-right: .75rem;
            padding-left: .75rem;
        }

        .row {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -.75rem;
            margin-left: -.75rem
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0
        }

        .no-gutters>.col,
        .no-gutters>[class*="col-"] {
            padding-right: 0;
            padding-left: 0
        }

        .col-1,
        .col-2,
        .col-3,
        .col-4,
        .col-5,
        .col-6,
        .col-7,
        .col-8,
        .col-9,
        .col-10,
        .col-11,
        .col-12,
        .col,
        .col-auto,
        .col-sm-1,
        .col-sm-2,
        .col-sm-3,
        .col-sm-4,
        .col-sm-5,
        .col-sm-6,
        .col-sm-7,
        .col-sm-8,
        .col-sm-9,
        .col-sm-10,
        .col-sm-11,
        .col-sm-12,
        .col-sm,
        .col-sm-auto,
        .col-md-1,
        .col-md-2,
        .col-md-3,
        .col-md-4,
        .col-md-5,
        .col-md-6,
        .col-md-7,
        .col-md-8,
        .col-md-9,
        .col-md-10,
        .col-md-11,
        .col-md-12,
        .col-md,
        .col-md-auto,
        .col-lg-1,
        .col-lg-2,
        .col-lg-3,
        .col-lg-4,
        .col-lg-5,
        .col-lg-6,
        .col-lg-7,
        .col-lg-8,
        .col-lg-9,
        .col-lg-10,
        .col-lg-11,
        .col-lg-12,
        .col-lg,
        .col-lg-auto,
        .col-xl-1,
        .col-xl-2,
        .col-xl-3,
        .col-xl-4,
        .col-xl-5,
        .col-xl-6,
        .col-xl-7,
        .col-xl-8,
        .col-xl-9,
        .col-xl-10,
        .col-xl-11,
        .col-xl-12,
        .col-xl,
        .col-xl-auto {
            position: relative;
            width: 100%;
            padding-right: .75rem;
            padding-left: .75rem
        }

        .col {
            -ms-flex-preferred-size: 0;
            flex-basis: 0;
            -ms-flex-positive: 1;
            flex-grow: 1;
            max-width: 100%
        }

        .col-auto {
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            width: auto;
            max-width: 100%
        }

        .col-1 {
            -ms-flex: 0 0 8.33333%;
            flex: 0 0 8.33333%;
            max-width: 8.33333%
        }

        .col-2 {
            -ms-flex: 0 0 16.66667%;
            flex: 0 0 16.66667%;
            max-width: 16.66667%
        }

        .col-3 {
            -ms-flex: 0 0 25%;
            flex: 0 0 25%;
            max-width: 25%
        }

        .col-4 {
            -ms-flex: 0 0 33.33333%;
            flex: 0 0 33.33333%;
            max-width: 33.33333%
        }

        .col-5 {
            -ms-flex: 0 0 41.66667%;
            flex: 0 0 41.66667%;
            max-width: 41.66667%
        }

        .col-6 {
            -ms-flex: 0 0 50%;
            flex: 0 0 50%;
            max-width: 50%
        }

        .col-7 {
            -ms-flex: 0 0 58.33333%;
            flex: 0 0 58.33333%;
            max-width: 58.33333%
        }

        .col-8 {
            -ms-flex: 0 0 66.66667%;
            flex: 0 0 66.66667%;
            max-width: 66.66667%
        }

        .col-9 {
            -ms-flex: 0 0 75%;
            flex: 0 0 75%;
            max-width: 75%
        }

        .col-10 {
            -ms-flex: 0 0 83.33333%;
            flex: 0 0 83.33333%;
            max-width: 83.33333%
        }

        .col-11 {
            -ms-flex: 0 0 91.66667%;
            flex: 0 0 91.66667%;
            max-width: 91.66667%
        }

        .col-12 {
            -ms-flex: 0 0 100%;
            flex: 0 0 100%;
            max-width: 100%
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #4E5155
        }

        .table th,
        .table td {
            padding: .625rem;
            vertical-align: top;
            border-top: 1px solid #e8e8e9
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #e8e8e9
        }

        .table tbody+tbody {
            border-top: 2px solid #e8e8e9
        }

        .table-sm th,
        .table-sm td {
            padding: .3125rem
        }

        .table-bordered {
            border: 1px solid #e8e8e9
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #e8e8e9
        }

        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px
        }

        .table-borderless th,
        .table-borderless td,
        .table-borderless thead th,
        .table-borderless tbody+tbody {
            border: 0
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(24, 28, 33, 0.025)
        }

        .table-hover tbody tr:hover {
            color: #4E5155;
            background-color: rgba(24, 28, 33, 0.035)
        }

        .table .thead-dark th {
            color: #fff;
            background-color: rgba(24, 28, 33, 0.9);
            border-color: #3f454a
        }

        .table .thead-light th {
            color: #4E5155;
            background-color: rgba(24, 28, 33, 0.03);
            border-color: #e8e8e9
        }

        .table-dark {
            color: #fff;
            background-color: rgba(24, 28, 33, 0.9)
        }

        .table-dark th,
        .table-dark td,
        .table-dark thead th {
            border-color: #3f454a
        }

        .table-dark.table-bordered {
            border: 0
        }

        .table-dark.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.03)
        }

        .table-dark.table-hover tbody tr:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05)
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch
        }

        .table-responsive>.table-bordered {
            border: 0
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
            border: 1px solid rgba(24, 28, 33, 0.06);
            border-radius: 1rem
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
    </style>

    <style>
        .td-70 {
            width: 70%;
        }

        .td-30 {
            width: 30%;
        }

        .td-50 {
            width: 50%;
        }

        .td-40 {
            width: 40%;
        }

        .text-title {
            color: #0084CE;
        }
    </style>
    <style>
        .container2 {
            display: grid;
            /* height: 100vh; */
            /* 100% of the viewport height */
            grid-template-columns: (auto-fill, 200px) 20%;
            /* Three columns with equal width */
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically */
        }

        .column {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <table cellspacing="10" cellpadding="10">
            <tr>
                <td>
                    <img src="https://i.ibb.co/Dg85Lc9/Header-Print.png" alt="Header Print" width="100px">
                </td>
                <td>
                    <span style="color:#2E3192;font-family:'Alice';font-size:xx-large">CV. PERKASATAMA TEKNIK</span>
                </td>
            </tr>
        </table>
        <hr />
        <!-- <div class="row d-flex justify-content-between">
			<div class="col-4">
				<strong class="text-title">Proof of Transfer</strong>
			</div>
			<div class="col-4">
				<table width="200px">
					<tr>
						<td>Number</td>
						<td>737453</td>
					</tr>
					<tr>
						<td>Date</td>
						<td><?= date("Ymd"); ?></td>
					</tr>
				</table>
			</div>
		</div> -->
        <table>
            <tr>
                <td class="td-70">
                    <strong class="text-title">Proof of Transfer</strong>
                </td>
                <td class="td-30">
                    <table width="200px">
                        <tr>
                            <td>Number</td>
                            <td>737453</td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td><?= date("Ymd"); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
        <br><br>
        <div class="row">
            <div class="col-md-12">
                <div class="card rounded-0">
                    <div class="card-body rounded-0">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong><?= $this->lang->line('ms_title_source_account'); ?></strong></td>
                                <td rowspan="2">
                                    <img src="https://i.ibb.co/p0s4Kpb/transfer.png" alt="transfer" width="100px">
                                </td>
                                <td>
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
                        <!-- <div class="row">
							<div class="col-12">
								<table class="table table-borderless">
									<tr>
										<td>
											<span></span><br>
											<strong><?= $record->source_account; ?></strong>
										</td>
										<td>
											<span><?= $this->lang->line('ms_title_number_document'); ?></span><br>
											<strong><?= $record->trans_number; ?></strong>
										</td>
									</tr>
									<tr>
										<td>
											<span><?= $this->lang->line('ms_title_terget_account'); ?></span><br>
											<strong><?= $record->target_account; ?></strong>
										</td>
										<td>
											<span><?= $this->lang->line('ms_title_ref'); ?></span><br>
											<strong><?= $record->ref; ?></strong>
										</td>
									</tr>
									<tr>
										<td>
											<span><?= $this->lang->line('ms_title_date'); ?></span><br>
											<strong><?= $this->Xin_model->set_date_format($record->date); ?></strong>
										</td>
									</tr>
								</table>
							</div>
						</div> -->
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row justify-content-end">
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
                                            <td><?= $record->description; ?></td>
                                            <td><?= $record->note; ?></td>
                                            <td><?= $this->Xin_model->currency_sign($record->amount); ?></td>
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
                                            <td><strong><?= $this->Xin_model->currency_sign($record->amount); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // window.print();
    </script>
</body>


</html>