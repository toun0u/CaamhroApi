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
		<div class="clients_price">
			<div class="sub_bloc_form_gauche">
				<table>
					<tr>
						<td class="label_field w100p"><label for="escompte"><?= $this->get_field_label('escompte'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('escompte'); ?></td>
						<td class="w100p"><img src="<?=  $view->getTemplateWebPath('gfx/pourcent16.png'); ?>" /></td>
						<td class="label_field w100p"><label for="minimum_order"><?= $this->get_field_label('minimum_order'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('minimum_order'); ?></td>
						<td class="w100p"><img src="<?=  $view->getTemplateWebPath('gfx/euro16.png'); ?>" /></td>
					</tr>
					<tr>
						<td class="label_field w100p"><label for="franco"><?= $this->get_field_label('franco'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('franco'); ?></td>
						<td class="w100p"><img src="<?=  $view->getTemplateWebPath('gfx/euro16.png'); ?>" /></td>
						<td class="label_field w100p label_top"><label for="means_of_payment[]"><?= $this->get_field_label('means_of_payment[]'); ?></label></td>
						<td class="value_field w100p" colspan="2"><?= $this->get_field_html('means_of_payment[]'); ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
