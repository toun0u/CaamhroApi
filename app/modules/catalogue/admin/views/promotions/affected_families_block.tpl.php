<?php
$view = view::getInstance();
$article = $view->get('article');
?>
<h1>
	<?= dims_constant::getVal('AFFECTED_FAMILIES'); ?>
</h1>

<?php
	show_guide(dims_constant::getVal('ALL_ITEMS_LISTED_FAMILIES_AND_THEIR_SUBFAMILIES'));
	$form = $this->getForm();
?>

<div class="sub_bloc">
	<div class="sub_bloc_form">
		<?= $form->text_field(array(
				'name'	=> 'search_family',
				'classes'	=> 'search_input_text',
				'value'	=> 'Recherchez une famille'
			)); ?>
		<?= $form->text_field(array(
				'name'	=> 'fam_qty',
				'classes'	=> 'w50p'
			)); ?>
			<table class="tableau">
				<tr>
					<td class="w25 title_tableau">
						<?= dims_constant::getVal('_FAMILY'); ?>
					</td>
					<td class="w30 title_tableau">
						<?= dims_constant::getVal('_DISCOUNT'); ?> (%)
					</td>
					<td class="w2 title_tableau">
						<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
					</td>
				</tr>
				<tr>
					<td>
						Emballage & Boîte pliante
					</td>
					<td>
						15
					</td>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/edit16.png'); ?>" title="Edition" /><img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" title="Suppression" />
					</td>
				</tr>
				<tr>
					<td>
						Porte-dépliant & présentoir
					</td>
					<td>
						15
					</td>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/edit16.png'); ?>" title="Edition" /><img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" title="Suppression" />
					</td>
				</tr>
				<tr>
					<td>
						PLV, Display & Urne
					</td>
					<td>
						15
					</td>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/edit16.png'); ?>" title="Edition" /><img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" title="Suppression" />
					</td>
				</tr>
				<tr>
					<td>
						Signalétique & ILV
					</td>
					<td>
						25
					</td>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/edit16.png'); ?>" title="Edition" /><img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" title="Suppression" />
					</td>
				</tr>
			</table>
	</div>
</div>
