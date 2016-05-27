<?php
dims_init_module('forms');

$view = view::getInstance();
$view->set_tpl_webpath('modules/forms/views/');
$view->setLayout('layouts/empty_layout.tpl.php');

if(!isset($_SESSION['flashes']))$_SESSION['flashes'] = array();
$view->initFlashStructure($_SESSION['flashes']);

$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true);

$forms_id = dims_load_securvalue('form_id', dims_const::_DIMS_NUM_INPUT, true, true, true,$obj['object_id']);
$form = forms::find_by(array('id'=>$forms_id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
if(!empty($form)){
	$view->assign('form',$form);
	switch($op){
		case 'list':
			include_once DIMS_APP_PATH.'modules/forms/controllers/cms_list.php';
			break;
		case 'display':
			include_once DIMS_APP_PATH.'modules/forms/controllers/cms_display.php';
			break;
	}
}
$view->compute();
