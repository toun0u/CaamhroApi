<?php
$view = view::getInstance();
$forms = $view->get('forms');
$listTypeForms = $view->get('listTypeForms');
$links = $view->get('links');

$form = new Dims\form(array(
	'name'          => "param_forms",
	'action'        => Gescom\get_path(array('c'=>'admin','a'=>'save_form')),
	'submit_value'  => $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'     => $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
	'back_url'      => Gescom\get_path(array('c'=>'admin','a'=>'form')),
));

foreach($listTypeForms as $k => $v){
	$form->add_select_field(array(
		'name'      => 'forms['.$k.']',
		'value'     => isset($links[$k])?$links[$k]:0,
		'label'     => $v,
		'options'   => $forms,
	));
}
$form->build();
