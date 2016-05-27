<?php
$view = view::getInstance();
$obj = $view->get('obj');

$dateLimit = $_SESSION['cste']['_INFOS_LIMIT_DATE'];
$js = <<<JS
$('#datefin').datepicker({
	dateFormat: "dd/mm/yy",
	showOn: "both",
	minDate: 0,
	buttonImage: "/common/modules/invitation/planning16.png",
	buttonImageOnly: true,
	buttonText: "$dateLimit",
});
JS;

$form = new Dims\form(array(
	'name' 			=> "invitation_params",
	'object'		=> $obj,
	'action'		=> dims::getInstance()->getScriptEnv()."?c=obj&a=save_param",
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['REINITIALISER'],
	'back_url'		=> dims::getInstance()->getScriptEnv().'?c=obj&a=param&id='.$obj->get('id'),
	'additional_js'	=> $js,
));
$default = $form->getBlock('default');

$form->add_text_field(array(
	'name'						=> 'obj_max_allowed',
	'label'						=> $_SESSION['cste']['_ACCOMPANYING_PERSONS_MAX'],
	'db_field'					=> 'max_allowed',
	'revision'					=> 'number',
	'additionnal_attributes'	=> 'style="width:50px;"',
));

$form->add_text_field(array(
	'name'						=> 'datefin',
	'label'						=> $_SESSION['cste']['_INFOS_LIMIT_DATE'],
	'value'						=> implode('/',array_reverse(explode('-',$obj->get('datefin')))),
	'revision'					=> 'date_jj/mm/yyyy',
	'additionnal_attributes'	=> 'style="width:75px;"',
));

$form->build();
