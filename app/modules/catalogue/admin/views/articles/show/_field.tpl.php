<?php
$field 		= $object['field'];
$form 		= $object['form'];
$id_chp 	= $object['id_chp'];
$id_lang 	= $object['id_lang'];
$scope 		= $object['scope'];

$view = view::getInstance();
$translations = $view->get('translations');
$value = isset($translations[$id_lang]) ? $translations[$id_lang] : null;

?>
<tr>
	<td><?= $field->fields['libelle'];?></td>
	<td class="center">
		<?php
		if($field->fields['fiche']){
			$couleur = 'verte';
			$title = 'FIELD_ON_TECHNICAL_RECORD';
		}
		else{
			$couleur = 'rouge';
			$title = 'FIELD_NOT_ON_TECHNICAL_RECORD';
		}
		?>
		<img src="<?= $view->getTemplateWebPath('gfx/pastille_'.$couleur.'12.png');?>" title="<?= dims_constant::getVal($title);?>"/>
	</td>
	<td class="center">
		<?php
		if($field->fields['filtre']){
			$couleur = 'verte';
			$title = 'FIELD_USED_IN_FILTER';
		}
		else{
			$couleur = 'rouge';
			$title = 'FIELD_NOT_USED_IN_FILTER';
		}
		?>
		<img src="<?= $view->getTemplateWebPath('gfx/pastille_'.$couleur.'12.png');?>" title="<?= dims_constant::getVal($title);?>"/>
	</td>
	<td>
		<?php
		switch($field->fields['type']){
			case cata_champ::TYPE_TEXT:
				echo $form->text_field(array(
						'name'		=> 'champs_libres['.$scope.']['.$id_lang.'][fields'.$id_chp.']',
						'id'		=> 'champ_libre_'.$scope.'_'.$id_lang.'_'.$id_chp,
						'classes'	=> 'w295p',
						'value'		=> ( ! empty($value->fields['fields'.$id_chp])) ? $value->fields['fields'.$id_chp] : ''
					));
				break;
			case cata_champ::TYPE_LIST:
				$list_values = $field->getLightAttribute('values');
				$cur_values = $list_values[$id_lang];
				echo $form->select_field(array(
						'name'			=> 'champs_libres['.$scope.']['.$id_lang.'][fields'.$id_chp.']',
						'id'			=> 'champ_libre_'.$scope.'_'.$id_lang.'_'.$id_chp,
						'options'		=> $cur_values,
						'empty_message'	=> dims_constant::getVal('SELECT_A_VALUE'),
						'value'			=> ( isset($value->fields['fields'.$id_chp]) ) ? $value->fields['fields'.$id_chp] : ''
					));
				break;
		}
		?>
	</td>
</tr>
