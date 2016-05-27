<?php
$id_popup = $this->get('id_popup');
$workflow = $this->get('workflow');
$step = $this->get('step');
$nbSteps = $this->get('nbSteps');

$form = new Dims\form(array(
	'name' 			=> "edit_step",
	'object'		=> $step,
	'action'		=> Gescom\get_path(array('c'=>'admin','a'=>'save_workflow_step')),
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
	'back_url'		=> "javascript:void(0);\" onclick=\"javascript:dims_closeOverlayedPopup('$id_popup');",
));

$form->addBlock('default',($step->isNew()?'Création d\'une étape':'Modification d\'une étape').'<a class="icon-close right mt10" href="javascript:void(0);" onclick="javascript:dims_closeOverlayedPopup(\''.$id_popup.'\');"></a>');

$form->add_hidden_field(array(
	'name' => 'id',
	'db_field' => 'id',
));

$form->add_hidden_field(array(
	'name' => 'ws_id_workflow',
	'db_field' => 'id_workflow',
	'mandatory' => true,
));

$v = range(1,$nbSteps);
$form->add_select_field(array(
	'name' => "ws_position",
	'db_field' => 'position',
	'mandatory' => true,
	'options' => array_combine($v, $v),
	'label' => "Position",
));

$form->add_text_field(array(
	'name' => 'ws_label',
	'db_field' => 'label',
	'mandatory' => true,
	'label' => $_SESSION['cste']['_DIMS_LABEL'],
));

$form->add_select_field(array(
	'name' => "ws_type",
	'db_field' => 'type',
	'options' => array(
		gescom_workflow_step::_TYPE_WAITING => 'En cours',
		gescom_workflow_step::_TYPE_FINISHED => 'Fini',
		gescom_workflow_step::_TYPE_CANCELLED => 'Annulé',
	),
	'label' => "Type",
));

$form->build();