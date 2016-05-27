<?php
$family = $this->get('family');
$selections = $this->get('selections');
$selection_id = $this->get('selection_id');

$additional_js = <<< ADDITIONAL_JS
$('#reference_search').dims_autocomplete( { c : 'articles', a : 'ac_articles' }, 3, 500, '#id_article', '#ac_references', '#ul_ac_references', '<li>${reference} - ${label}</li>', 'YEN A PAS', null );
ADDITIONAL_JS;
?>

<div class="objects_content">
	<?php
	$form = new Dims\form(array(
		'name' 				=> 'form_selection',
		'action'			=> get_path('familles', 'show', array('sa' => 'selection_save', 'id' => $family->get('id'))),
		'back_name'			=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'			=> get_path('familles', 'show', array('sa' => 'selections', 'id' => $family->get('id'))),
		'submit_value'		=> dims_constant::getVal('CATA_SAVE_THE_SELECTION'),
		'continue'			=> true,
		'additional_js' 	=> $additional_js
	));

	$form->addBlock('default', dims_constant::getVal('CATA_FAMILY_SELECTION_EDITION'), $this->getTemplatePath('familles/selections_edit_form.tpl.php'));

	$form->add_hidden_field(array('name' => 'id_article'));

	// Liste des sélections
	$a_selections = array();
	foreach ($selections as $selection) {
		$a_selections[$selection->get('id')] = $selection->getTitle();
	}

	$form->add_select_field(array(
		'name'				=> 'selection_id',
		'label'				=> dims_constant::getVal('CATA_SELECTION'),
		'options' 			=> $a_selections,
		'value' 			=> $selection_id
	));

	$form->build();
	?>
</div>
