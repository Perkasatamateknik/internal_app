<div class="card">
	<?php $attributes = array('name' => 'type_form', 'id' => 'type_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
	<?php $hidden = array('type' => 'type'); ?>
	<?php echo form_open('admin/contacts/store_type', $attributes, $hidden); ?>
	<div class="card-body">
		<div class="form-group">
			<label for="contact_type"><?= $this->lang->line('ms_title_contact_type'); ?></label>
			<input type="text" name="contact_type" id="contact_type" class="form-control" required>
		</div>
		<br>
		<button type="submit" class="btn btn-primary btn-block"> <?php echo $this->lang->line('xin_save'); ?> </button>

	</div>
	<?php echo form_close(); ?>
</div>