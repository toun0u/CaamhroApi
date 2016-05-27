<?php
$view = view::getInstance();
$workflow = $view->get('workflow');

$form = new Dims\form(array(
	'name' 			=> "edit_workflow",
	'object'		=> $workflow,
	'action'		=> Gescom\get_path(array('c'=>'admin','a'=>'save_workflow')),
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
	'back_url'		=> Gescom\get_path(array('c'=>'admin','a'=>'workflow')),
));

$form->addBlock('default',($workflow->isNew()?'Création d\'un workflow':('Modification d\'un workflow : '.$workflow->get('label'))));

$form->add_hidden_field(array(
	'name' => 'id',
	'db_field' => 'id',
));

$form->add_text_field(array(
	'name' => 'w_label',
	'db_field' => 'label',
	'mandatory' => true,
	'label' => $_SESSION['cste']['_DIMS_LABEL'],
));

$form->add_textarea_field(array(
	'name' => 'w_description',
	'db_field' => 'description',
	'mandatory' => false,
	'label' => $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION'],
));

$form->build();