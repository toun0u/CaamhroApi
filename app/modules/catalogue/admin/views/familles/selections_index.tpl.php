<?php
$family = $this->get('family');
$a_selections = $this->get('a_selections');

if ($family->fields['display_mode'] == cata_famille::DISPLAY_MODE_LIST) {
	?>
	<table class="tableau">
		<thead>
			<tr>
				<th><?= dims_constant::getVal('_DIMS_LABEL_TITLE'); ?></th>
				<th><?= dims_constant::getVal('_TEMPLATE'); ?></th>
				<th><?= dims_constant::getVal('CATA_NB_ARTICLES'); ?></th>
				<th><?= dims_constant::getVal('_POSITION'); ?></th>
				<th class="w70p"><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($a_selections as $elem): ?>
			<tr>
				<td><?= $elem['selection']->getTitle(); ?></td>
				<td><?= $elem['selection']->getTemplateName(); ?></td>
				<td><?= $elem['nb_art']; ?></td>
				<td><?= $elem['position']; ?></td>
				<td>
					<a href="<?= get_path('familles', 'show', array('sa' => 'selection_edit', 'id' => $family->get('id'), 'selection_id' => $elem['selection']->get('id'))); ?>" title="<?= dims_constant::getVal('EDIT_THIS_SELECTION'); ?>">
						<img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" alt="<?= dims_constant::getVal('EDIT_THIS_SELECTION'); ?>" /></a>
					<a onclick="javascript:dims_confirmlink('<?= get_path('familles', 'show', array('sa' => 'selection_delete', 'id' => $family->get('id'), 'selection_id' => $elem['selection']->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_SELECTION'); ?>');" href="javascript:void(0);" title="<?= dims_constant::getVal('CATA_DELETE_THIS_SELECTION'); ?>">
						<img src="<?php echo $this->getTemplateWebPath("/gfx/supprimer16.png"); ?>" alt="<?= dims_constant::getVal('CATA_DELETE_THIS_SELECTION'); ?>" /></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}
else {
	?>
	<p class="error">
		<?php
		$message = dims_constant::getVal('CATA_ERROR_BAD_DISPLAY_MODE');
		$link = get_path('familles', 'show', array('sa' => 'properties', 'id' => $family->get('id')));
		printf($message, $link);
		?>
	</p>
	<?php
}

