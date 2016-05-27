<div class="objects_content">
	<h2><?= dims_constant::getVal('CATA_FAMILIES_SELECTIONS'); ?></h2>

	<div class="actions">
		<a href="<?= get_path('objects', 'families_selections', array('sa' => 'edit')); ?>" class="link_img" title="<?= dims_constant::getVal('CATA_ADD_SELECTION'); ?>">
			<img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" alt="<?= dims_constant::getVal('CATA_ADD_SELECTION'); ?>" />
			<span><?= dims_constant::getVal('CATA_ADD_SELECTION'); ?></span>
		</a>
	</div>

	<?php
	$selections = $this->get('selections');
	if(!empty($selections)) {
		?>
		<table class="tableau">
			<tr>
				<td class="title_tableau">
					<?= dims_constant::getVal('_DIMS_LABEL_TITLE'); ?>
				</td>
				<td class="title_tableau">
					<?= dims_constant::getVal('_TEMPLATE'); ?>
				</td>
				<td class="w70p title_tableau">
					<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
				</td>
			</tr>
			<?php
			foreach($selections as $selection){
				?>
				<tr>
					<td>
						<?= $selection->getTitle(); ?>
					</td>
					<td>
						<?= $selection->getTemplateName(); ?>
					</td>
					<td>
						<a href="<?= get_path('objects', 'families_selections', array('sa' => 'edit', 'id' => $selection->get('id'))); ?>" title="<?= dims_constant::getVal('EDIT_THIS_SELECTION'); ?>">
							<img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" alt="<?= dims_constant::getVal('EDIT_THIS_SELECTION'); ?>" /></a>
						<a onclick="javascript:dims_confirmlink('<?= get_path('objects', 'families_selections', array('sa' => 'delete', 'id' => $selection->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_SELECTION'); ?>');" href="javascript:void(0);" title="<?= dims_constant::getVal('CATA_DELETE_THIS_SELECTION'); ?>">
							<img src="<?php echo $this->getTemplateWebPath("/gfx/supprimer16.png"); ?>" alt="<?= dims_constant::getVal('CATA_DELETE_THIS_SELECTION'); ?>" /></a>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	else{
		?>
		<div class="div_no_elem"><?= dims_constant::getVal('CATA_ANY_SELECTION'); ?></div>
		<?php
	}
	?>
</div>
