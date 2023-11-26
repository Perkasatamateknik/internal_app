<?php
$session = $this->session->userdata('username');
$theme = $this->Xin_model->read_theme_info(1);
// set layout / fixed or static
if ($theme[0]->right_side_icons == 'true') {
	$icons_right = 'expanded menu-icon-right';
} else {
	$icons_right = '';
}
if ($theme[0]->bordered_menu == 'true') {
	$menu_bordered = 'menu-bordered';
} else {
	$menu_bordered = '';
}
$user_info = $this->Xin_model->read_user_info($session['user_id']);


if ($user_info[0]->is_active != 1) {
	redirect('admin/');
}
$role_user = $this->Xin_model->read_user_role_info($user_info[0]->user_role_id);
if (!is_null($role_user)) {
	$role_resources_ids = explode(',', $role_user[0]->role_resources);
} else {
	$role_resources_ids = explode(',', 0);
}
?>

<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php $arr_mod = $this->Xin_model->select_module_class($this->router->fetch_class(), $this->router->fetch_method());
?>

<?php
if ($theme[0]->sub_menu_icons != '') {
	$submenuicon = $theme[0]->sub_menu_icons;
} else {
	$submenuicon = 'fa-circle-o';
}
// reports to 
$reports_to = get_reports_team_data($session['user_id']);
?>
<?php if ($user_info[0]->profile_picture != '' && $user_info[0]->profile_picture != 'no file') { ?>
	<?php $cpimg = base_url() . 'uploads/profile/' . $user_info[0]->profile_picture; ?>
<?php } else { ?>
	<?php if ($user_info[0]->gender == 'Male') { ?>
		<?php $de_file = base_url() . 'uploads/profile/default_male.jpg'; ?>
	<?php } else { ?>
		<?php $de_file = base_url() . 'uploads/profile/default_female.jpg'; ?>
	<?php } ?>
	<?php $cpimg = $de_file; ?>
<?php  } ?>

<ul class="sidenav-inner py-1">
	<!-- Dashboards -->
	<li class="sidenav-item <?php if (!empty($arr_mod['active'])) echo $arr_mod['active']; ?>"> <a href="<?php echo site_url('admin/dashboard'); ?>" class="sidenav-link"> <i class="sidenav-icon ion ion-md-speedometer"></i>
			<div><?php echo $this->lang->line('dashboard_title'); ?></div>
		</a>
	</li>


	<!-- // fitur keuangan  -->
	<li class="<?php if (!empty($arr_mod['cost_open'])) echo $arr_mod['cost_open']; ?> sidenav-item"> <a href="#" class="sidenav-link sidenav-toggle"> <i class="sidenav-icon ion ion-md-cash"></i>
			<div><?php echo $this->lang->line('ms_title_finance'); ?></div>
		</a>
		<ul class="sidenav-menu">

			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/dashboard'); ?>"> <?php echo $this->lang->line('ms_title_finance_dashboard'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/calendar'); ?>"> <?php echo $this->lang->line('ms_title_finance_calendar'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/cash_bank'); ?>"> <?php echo $this->lang->line('ms_title_cash_bank'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/accounts'); ?>"> <?php echo $this->lang->line('ms_title_accounts'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/expense'); ?>"> <?php echo $this->lang->line('ms_title_expense'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/transaction'); ?>"> <?php echo $this->lang->line('ms_title_transaction'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/income'); ?>"> <?php echo $this->lang->line('ms_title_income'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/payment'); ?>"> <?php echo $this->lang->line('ms_title_payment'); ?> </a> </li>
			<li class="sidenav-item <?php if (!empty($arr_mod['hremp_active'])) echo $arr_mod['hremp_active']; ?>"> <a class="sidenav-link" href="<?php echo site_url('admin/finance/budgeting'); ?>"> <?php echo $this->lang->line('ms_title_budgeting'); ?> </a> </li>

		</ul>
	</li>
</ul>