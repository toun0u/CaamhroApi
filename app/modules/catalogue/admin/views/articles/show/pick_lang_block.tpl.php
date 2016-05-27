<?php
$view = view::getInstance();
$form = $this->getForm();
?>
<div class="sub_bloc">
	<div class="sub_bloc_form pick_language">
		<label for="pick_language"><?= dims_constant::getVal('WORKING_LANGUAGE'); ?></label>
		<?= $this->get_field_html('pick_language'); ?>
	</div>
	<?= $this->get_field_html('fields_scope'); ?>
</div>