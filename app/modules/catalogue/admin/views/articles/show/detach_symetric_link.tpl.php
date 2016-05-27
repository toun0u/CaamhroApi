<?php
$view = view::getInstance();
$link = $view->get('link');
$article = $view->get('article');

$form = new Dims\form(array(
	'name' 				=> 'form_detach',
	'action'			=> get_path('articles', 'show', array('sc' => 'links', 'sa' => 'detach_symetric', 'id' => $article->get('id'))),
	'back_url'			=> get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))),
	'submit_value'		=> dims_constant::getVal('_BUSINESS_LEGEND_CUT')
	));

echo $form->get_header();
#Cyril : note, je n'utilise pas le template par défaut parce que ça pousse les éléments complètement à droite
#et vu la taille du formulaire, ça ne valait pas le coup de redéfinir un tpl spécifique pour ce formulaire
?>
<div class="sub_bloc">
	<h3><?= dims_constant::getVal('SYMETRIC_DETACHMENT'); ?></h3>
	<div class="form_object_block">
		<div class="sub_bloc_form">
			<?= $form->hidden_field(array(
					'name'				=> 'id_link',
					'value'				=> $link->get('id')
					));
			?>

			<table>
				<tr>
					<td class="label_field label_left">
						<label for="sym"><?= dims_constant::getVal('DELETE_SYMETRIC_RELATION'); ?></label>
					</td>
					<td class="value_field">
						<?= $form->radio_field(array(
							'name'				=> 'symetric',
							'id'				=> 'sym',
							'value'				=> 'sym',
							'checked'			=> true
							));
						?>
					</td>
				</tr>
				<tr>
					<td class="label_field label_left">
						<label for="asym"><?= dims_constant::getVal('KEEP_FOREIGN_RELATION'); ?></label>
					</td>
					<td class="value_field">
						<?= $form->radio_field(array(
							'name'				=> 'symetric',
							'id'				=> 'asym',
							'value'				=> 'asym'
							));
						?>
					</td>
				</tr>
			</table>
		</div>
		<?php $form->displayActionsBlock(); ?>
	</div>
</div>
<?php
echo $form->close_form();
?>