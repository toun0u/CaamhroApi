<?php $view = view::getInstance(); ?>
<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3><?php echo $title; ?></h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<table>
			<tr>
				<td class="label_field"><label for="vigette"><?= $this->get_field_label('vignette'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('vignette'); ?></td>
			</tr>
			<tr><td></td><td><div class="legend"><?= dims_constant::getVal('GOOD_SIZE'); ?></div></td></tr>
			<tr><td></td><td><div class="mess_error" id="def_vignette"></div></td></tr>
		</table>

	</div>
</div>

