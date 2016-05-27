<?php
$view = view::getInstance();
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');
$folder = $view->get('new_folder');
?>
<h2><?= dims_constant::getVal('_DOC_NEWFOLDER'); ?></h2>
<?php

$form = new Dims\form(array(
	'name'			=> 'folder',
	'object'		=> $folder,
	'action'		=> get_path('show', 'show', array('cc' => 'documents','id' => $objcourant->get('id'),  'aa' => ($folder->isNew()) ? 'create_folder' : 'update_folder')),
	'submit_value'	=> dims_constant::getVal('_DIMS_SAVE'),
	'back_name'		=> dims_constant::getVal('_DIMS_CANCEL'),
	'back_url'		=> get_path('show', 'show', array('id' => $objcourant->get('id'), 'cc' => 'documents'))
	));

$form->add_hidden_field(array(//pour être sûr qu'un malin essaye pas de créer une action sur une convention qu'il n'a pas le droit
	'name'		=> 'id',
	'value'		=> $objcourant->get('id')//ça passera par le token validator
	));

$form->add_hidden_field(array(//pour être sûr qu'un malin essaye pas de créer une action sur une convention qu'il n'a pas le droit
	'name'		=> 'fold_id_folder',
	'db_field'	=> 'id_folder'
));

if( ! $folder->isNew() ){
	$form->add_hidden_field(array(
		'name'		=> 'foldid',
		'value'		=> $folder->get('id')
		));
}

$form->add_text_field(array(
	'name'			=> 'fold_name',
	'db_field'		=> 'name',
	'label'			=> dims_constant::getVal('LABEL'),
	'mandatory'		=> true
));

$form->build();
