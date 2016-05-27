<?php
$view = view::getInstance();
$article = $view->get('article');
?>
<h1>
	<?= dims_constant::getVal('RESTRICT_TO_CERTAIN_CUSTOMERS'); ?>
</h1>

<?php
	show_guide(dims_constant::getVal('RESTRICT_CUSTOMERS'));
	$form = $this->getForm();
?>

<div class="sub_bloc">
	<div class="sub_bloc_form">
		<?= $form->text_field(array(
				'name'	=> 'search_customers',
				'classes'	=> 'search_input_text',
				'value'	=> 'Recherchez un client'
			)); ?>
			<table class="tableau">
				<tr>
					<td class="w2 title_tableau">
						&nbsp
					</td>
					<td class="w30 title_tableau">
						<?= dims_constant::getVal('CLIENT'); ?>
					</td>
					<td class="w2 title_tableau">
						<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/bic.png'); ?>" />
					</td>
					<td>
						BIC France
					</td>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" title="Suppression" />
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/pages_jaunes.png'); ?>" />
					</td>
					<td>
						Pages jaunes
					</td>
					<td>
						<img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" title="Suppression" />
					</td>
				</tr>
			</table>
	</div>
</div>

