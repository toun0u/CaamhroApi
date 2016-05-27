<?php
$view = view::getInstance();
?>
<div class="sub_bloc" id="<?= $this->getId(); ?>">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3><?= $title; ?></h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<table>
			<?php
			$first = true;
			foreach ($view->get('languages') as $id_lang => $lang) {
				?>
				<tr <?= (!$first)?'style="display:none"':''; ?> class="selection_title title_<?= $id_lang; ?>">
					<td class="label_field">
						<label for="title_<?= $id_lang; ?>"><?= $this->get_field_label('title_'.$id_lang); ?></label>
					</td>
					<td class="value_field"><?= $this->get_field_html('title_'.$id_lang); ?></td>
				</tr>
				<?php
				$first = false;
			}
			?>
			<tr>
				<td class="label_field"><label for="template_id"><?= $this->get_field_label('template_id'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('template_id'); ?></td>
			</tr>
		</table>
	</div>
</div>

<?= $this->get_field_html('id'); ?>
