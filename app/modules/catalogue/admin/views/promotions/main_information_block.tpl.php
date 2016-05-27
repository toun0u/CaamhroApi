<?php
$view = view::getInstance();

$promo = $view->get('promo');

echo $this->get_field_html('id')
?>



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
						<td class="label_field w100p"><label for="intitule"><?= $this->get_field_label('intitule'); ?></label></td>
						<td class="value_field w100p" colspan="5"><?= stripslashes($this->get_field_html('intitule')); ?></td>
					</tr>
					<tr>
						<td class="label_field w100p"><label for="code_activation"><?= $this->get_field_label('code_activation'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('code_activation'); ?></td>
					</tr>
					<tr>
						<td class="label_field w100p"><label for="date_debut"><?= $this->get_field_label('date_debut'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('date_debut'); ?></td>
						<td class="label_field w100p"><label for="date_fin"><?= $this->get_field_label('date_fin'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('date_fin'); ?></td>
					</tr>
					<tr>
						<td class="label_field w100p"><label for="image"><?= $this->get_field_label('image'); ?></label></td>
						<td class="value_field w100p"><?= $this->get_field_html('image'); ?></td>
					</tr>
					<?php
					$image = $promo->getImage();
					if ($image) {
						?>
						<tr>
							<td class="label_field w100p"><label><?= 'Image actuelle'; ?></label></td>
							<td class="value_field w100p">
								<div class="thumb">
									<img src="<?= $image->getThumbnail(150); ?>" />
								</div>
								<?= $image->fields['name']; ?><br/>
							</td>
						</tr>
						<?php
					}
					?>
				</table>

			</div>
		</div>
	</div>
</div>
