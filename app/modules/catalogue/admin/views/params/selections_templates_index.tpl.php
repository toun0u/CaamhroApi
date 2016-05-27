<div class="params_content">
	<h2><?= dims_constant::getVal('CATA_FAMILIES_SELECTIONS_TEMPLATES'); ?></h2>

	<div class="actions">
		<a href="<?= get_path('params', 'selections_templates', array('sa' => 'edit')); ?>" class="link_img" title="<?= dims_constant::getVal('ADD_TEMPLATE'); ?>">
			<img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" alt="<?= dims_constant::getVal('ADD_TEMPLATE'); ?>" />
			<span><?= dims_constant::getVal('ADD_TEMPLATE'); ?></span>
		</a>
	</div>

	<?php
	$templates = $this->get('templates');
	if(!empty($templates)) {
		?>
		<table class="tableau">
			<tr>
				<td class="title_tableau">
					<?= dims_constant::getVal('_DIMS_LABEL_TITLE'); ?>
				</td>
				<td class="title_tableau">
					<?= dims_constant::getVal('FILE'); ?>
				</td>
				<td class="w70p title_tableau">
					<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
				</td>
			</tr>
			<?php
			foreach($templates as $template){
				?>
				<tr>
					<td>
						<?= $template->getTitle(); ?>
					</td>
					<td>
						<?= $template->getDocName(); ?>
					</td>
					<td>
						<a href="<?= get_path('params', 'selections_templates', array('sa' => 'edit', 'id' => $template->get('id'))); ?>" title="<?= dims_constant::getVal('EDIT_THIS_TEMPLATE'); ?>">
							<img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" alt="<?= dims_constant::getVal('EDIT_THIS_TEMPLATE'); ?>" /></a>
						<a onclick="javascript:dims_confirmlink('<?= get_path('params', 'selections_templates', array('sa' => 'delete', 'id' => $template->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_TEMPLATE'); ?>');" href="javascript:void(0);" title="<?= dims_constant::getVal('CATA_DELETE_THIS_TEMPLATE'); ?>">
							<img src="<?php echo $this->getTemplateWebPath("/gfx/supprimer16.png"); ?>" alt="<?= dims_constant::getVal('CATA_DELETE_THIS_TEMPLATE'); ?>" /></a>
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
		<div class="div_no_elem"><?= dims_constant::getVal('CATA_ANY_SELECTION_TEMPLATE'); ?></div>
		<?php
	}
	?>
</div>
