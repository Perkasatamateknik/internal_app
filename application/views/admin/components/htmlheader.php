<?php $company = $this->Xin_model->read_company_setting_info(1); ?>
<?php $favicon = base_url() . 'uploads/logo/favicon/' . $company[0]->favicon; ?>
<?php $theme = $this->Xin_model->read_theme_info(1); ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title; ?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">

	<!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet"> -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,700;0,800;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

	<style>
		* {
			font-family: 'Plus Jakarta Sans', sans-serif;
		}
	</style>
	<!-- Icon fonts -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/fonts/fontawesome.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/fonts/ionicons.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/fonts/linearicons.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/fonts/open-iconic.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/fonts/pe-icon-7-stroke.css">

	<!-- Core stylesheets -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/bootstrap.css" class="theme-settings-bootstrap-css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/appwork.css" class="theme-settings-appwork-css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/theme-corporate.css" class="theme-settings-theme-css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/colors.css" class="theme-settings-colors-css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/uikit.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/css/demo.css">

	<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/js/polyfills.js"></script>

	<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/js/material-ripple.js"></script>
	<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/js/layout-helpers.js"></script>

	<!-- Theme settings -->
	<!-- This file MUST be included after core stylesheets and layout-helpers.js in the <head> section -->
	<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/js/theme-settings.js"></script>
	<script>
		window.themeSettings = new ThemeSettings({
			cssPath: '<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/',
			themesPath: '<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/rtl/'
		});
	</script>

	<!-- Core scripts -->
	<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/js/pace.js"></script>


	<!-- Libs -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">

	<!-- hrsale vendor -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/toastr/toastr.min.css">
	<link media="all" type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/css/animate.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/datatables/datatables.css">
	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/select2/select2.css"> old -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/select2/select2.css">
	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/select2/select2-bootstrap.css"> -->

	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/Trumbowyg/dist/ui/trumbowyg.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/smartwizard/smartwizard.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/jquery-ui/jquery-ui.css">

	<!-- Picker -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/flatpickr/flatpickr.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/timepicker/timepicker.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/pages/contacts.css">

	<!-- Conditions-->
	<?php if ($this->router->fetch_class() == 'roles') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/kendo/kendo.common.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/kendo/kendo.default.min.css">
	<?php } ?>
	<?php if ($this->router->fetch_class() == 'reports') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/pages/file-manager.css">
	<?php } ?>
	<?php if ($this->router->fetch_class() == 'chat') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/pages/chat.css">
	<?php } ?>
	<?php if ($this->router->fetch_class() == 'calendar' || $this->router->fetch_class() == 'timesheet' || $this->router->fetch_class() == 'dashboard' || $this->router->fetch_method() == 'timecalendar' || $this->router->fetch_method() == 'projects_calendar' || $this->router->fetch_method() == 'tasks_calendar' || $this->router->fetch_method() == 'quote_calendar' || $this->router->fetch_method() == 'invoice_calendar' || $this->router->fetch_method() == 'projects_dashboard' || $this->router->fetch_method() == 'calendar') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/fullcalendar/dist/fullcalendar.css">
		<link href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/fullcalendar/dist/scheduler.min.css" rel="stylesheet">
	<?php } ?>
	<?php if ($this->router->fetch_method() == 'tasks_scrum_board' || $this->router->fetch_method() == 'projects_scrum_board') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/dragula/dragula.css">
	<?php } ?>
	<?php if ($this->router->fetch_class() == 'events' || $this->router->fetch_class() == 'meetings') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/minicolors/minicolors.css">
	<?php } ?>
	<?php if ($this->router->fetch_class() == 'goal_tracking' || $this->router->fetch_method() == 'task_details' || $this->router->fetch_class() == 'project' || $this->router->fetch_class() == 'quoted_projects' || $this->router->fetch_method() == 'project_details') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/ion.rangeSlider/css/ion.rangeSlider.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css">
	<?php } ?>
	<?php if ($this->router->fetch_method() == 'notifications') { ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/css/pages/messages.css">
	<?php } ?>
	<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/chartjs/chartjs.js"></script>

	<!-- <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" /> -->
	<style>
		.purporse {
			background-color: #f1f1f2;
			/* Use the desired grey background color */
			padding: 10px;
			border: 1px solid #dedede;
			border-radius: 5px;
			font-size: 16px;
			color: #333;
			/* Set the desired text color */
		}

		.bottom-right {
			position: absolute;
			bottom: 0;
			right: 2vh;
			width: 200px;
		}
	</style>
	<!-- <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" /> -->

	<link rel="stylesheet" href="<?= base_url("skin/vendor/fileUpload/fileUpload.css"); ?>">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined">

</head>