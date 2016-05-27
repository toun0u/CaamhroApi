<?php
dims_init_module('catalogue');

//Class
include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_famille.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_filter.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';

$db->query("SET NAMES 'UTF8'");//temporaire ? qui dure ?

$mods = $dims->getModuleByType('catalogue');

$oCatalogue = new catalogue();
$oCatalogue->open($mods[0]['instanceid']);

$oCatalogue->loadParams();

switch($dims_op) {
	case 'call_autoresponse':
		$smarty->assign('tpl_name', 'catalogue_content');

		$page['TITLE'] = 'Catalogue / E-payement';
		$page['META_DESCRIPTION'] = 'Interface de payement en ligne.';
		$page['META_KEYWORDS'] = 'Catalogue, produits, articles, cyberplus';
		$page['CONTENT'] = '';

		ob_start();
		$op = $dims_op;
		include_once './plugins/epayment/gateway.php';
		$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));
		ob_end_clean();
		die();
		break;
	case 'code_client_unique':
		$code_client = dims_load_securvalue('code_client', dims_const::_DIMS_CHAR_INPUT, true, false, true);
		if ($code_client != '') {
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
			echo client::codeExists($code_client);
		}
		die();
		break;
	case 'add_new_city':
		ob_clean();
		$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($val != '' && $id != '' && $id > 0){
			include_once DIMS_APP_PATH.'modules/system/class_city.php';
			$city = new city();
			$city->init_description();
			$city->fields['id_country'] = $id;
			$city->fields['label'] = $val;
			if ($city->save()){
				$elem = array();
				$elem['id'] = $city->fields['id'];
				$elem['label'] = $city->fields['label'];
				die(json_encode($elem));
			}
		}
		die();
		break;
}
