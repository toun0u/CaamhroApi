<?php
dims_init_module('events');
require_once DIMS_APP_PATH.'modules/events/classes/includes.php';
require_once DIMS_APP_PATH.'modules/system/include/business.php';
require_once DIMS_APP_PATH.'include/functions/mail.php';
require_once DIMS_APP_PATH.'modules/system/class_workspace.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$old_lang=array();
$old_id_lang=0;

$dims_op = dims_load_securvalue('dims_op', dims_const::_DIMS_CHAR_INPUT, true);
if($dims_op != '') {
	require_once(DIMS_APP_PATH.'modules/events/public_events.php');
}
else {

	// test si on a change la langue, on  enregistre dans le profil
	$dimslang=dims_load_securvalue('dimslang',dims_const::_DIMS_NUM_INPUT,true,true,true);
	if ($_SESSION['dims']['connected']) {
		$user = new user();
		$user->open($_SESSION['dims']['user']['id']);

		if (isset($dimslang) && $dimslang>0) {
			$user->fields['lang']=$dimslang;
			$user->save();
		}
		$_SESSION['dims']['currentlang']=$user->fields['lang'];
	}
	else {
		if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']) &&
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']!=$_SESSION['dims']['currentlang']) {

			$old_lang=$_SESSION['cste'];
			$old_id_lang=$_SESSION['dims']['currentlang'];

			$dims->loadLanguage($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],$_SESSION['dims']['currentlang']);
		}
	}

	$_DIMS['cste']=array();
	//$_SESSION['dims']['currentlang']=2;

	if (isset($_SESSION['dims']['currentlang']) && $_SESSION['dims']['currentlang']>0)
		$res=$db->query("SELECT phpvalue,value FROM dims_constant WHERE id_lang = :idlang", array(':idlang' => $_SESSION['dims']['currentlang']));
	else {
		$res=$db->query("SELECT phpvalue,value FROM dims_constant ORDER BY id_lang ASC");
	}
	while ($f=$db->fetchrow($res)) {
		$_DIMS['cste'][$f['phpvalue']]=$f['value'];
	}
	$_SESSION['cste']=$_DIMS['cste'];

		if ($op!='fairs') ob_end_clean();

	?>
	<link rel="stylesheet" type="text/css" href="./common/modules/events/include/design.css" />

	<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" href="./common/modules/events/include/styles_ie.css" title="styles" />
	<![endif]-->
<?
	if ($op=='fairs') {
		?>

	<script language="JavaScript" type="text/JavaScript" src="<? echo str_replace('index.php','',$dims->getUrlPath()); ?>js/functions.js"></script>
	<script type="text/javascript" src="<? echo str_replace('index.php','',$dims->getUrlPath()); ?>js/prototype.js"></script>
	<script type="text/javascript" src="<? echo str_replace('index.php','',$dims->getUrlPath()); ?>js/effects.js"></script>
	<script type="text/javascript" src="<? echo str_replace('index.php','',$dims->getUrlPath()); ?>js/scriptaculous.js"></script>
	<?php
	}


		$style= '';
		switch ($op) {
			case 'event':
				$style = 'width:100%;margin:0px;';
				break;
			case 'fairs':
				$style = 'width:100%;margin:0px;';
				break;
		}

	echo '<div style="'.$style.' font-family:Trebuchet MS,Arial,Helvetica,sans-serif;">';

	$id_evt = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true);

	//affichage du resume de l'evt
	$evt = new action();
	if ($id_evt>0) {
		$evt->open($id_evt);
	}

	switch ($op) {
		case 'fairs':
		case 'event':
			$action = null;
			$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true);

			switch($action) {

				case 'sub_eventdetail':
					$id_event = dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT, true, true);
					$event = new action();
					$event->open($id_event);
					require_once DIMS_APP_PATH . '/modules/events/cms_event_describe.php';
					break;

				case 'sub_eventinscription':
					require_once DIMS_APP_PATH . '/modules/events/cms_event_inscription.php';
					break;
				case 'export_odt':
					ob_end_clean();
					require_once DIMS_APP_PATH . '/modules/events/public_events_convert_odt.php';
					die();
					break;
				case 'getPwd':
				case 'valid_niv1':
				case 'view_all_registration':
				default:
					if ($op=='fairs' || true) {
						require_once(DIMS_APP_PATH . "/modules/events/cms_fairs.php");
					}
					else {

						if ($id_evt>0) { // modification pour ma gestion d'une inscription standard
						 require_once DIMS_APP_PATH . '/modules/events/cms_event.php';
						}
					}
					break;
				case 'delete_fairs_delegue':

					$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_delegue = dims_load_securvalue('id_delegue', dims_const::_DIMS_NUM_INPUT, true, true);

					$delegue = new etap_delegue();
					$delegue->open($id_delegue);
					$delegue->delete();

					dims_redirect('index.php?id_event='.$id_event.'&id_etap='.$id_etap);

					break;
				case 'save_fairs_delegue':
					$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true, true);
					$id_contact = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true);

					$delegue = new etap_delegue();
					$delegue->init_description();

					$delegue->fields['id_action'] = $id_event;
					$delegue->fields['id_etap'] = $id_etap;
					$delegue->fields['id_contact'] = $id_contact;
					$delegue->fields['date_inscr'] = date("YmdHis");

					$delegue->setvalues($_POST, 'del_');

					$delegue->save();

					dims_redirect('index.php?id_event='.$id_event.'&id_etap='.$id_etap);
					break;
				case 'save_niv1':
					//require_once DIMS_APP_PATH . '/modules/events/curl_inet.php';
					require_once DIMS_APP_PATH . '/modules/events/cms_event_save_niv1.php';
					break;

				case 'show_upload':
					require_once DIMS_APP_PATH . '/modules/events/cms_event_show_upload.php';
					die();
					break;

				case 'save_eventfile':
					require_once DIMS_APP_PATH . '/modules/events/cms_event_save_eventfile.php';
					dims_redirect("index.php?id_event=".$id_evt);
					break;

				case 'delete_doc_user':
					require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
					require_once(DIMS_APP_PATH . '/modules/doc/include/global.php');

					$id_doc = dims_load_securvalue('id_doc_fo',dims_const::_DIMS_NUM_INPUT,true,true,false);
					$id_evt = dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,false);
					$id_file_u = dims_load_securvalue('id_file_u',dims_const::_DIMS_NUM_INPUT,true,true,false);

					//on supprime le lien dans dims_mod_business_etap_file_user
					$etap_file_u = new etap_file_ct();
					$etap_file_u->open($id_file_u);
					$etap_file_u->fields['id_doc_frontoffice'] = '';
					$etap_file_u->fields['date_reception'] = '';
					$etap_file_u->fields['valide'] = 0;
					$etap_file_u->fields['date_validation'] = '';
					$etap_file_u->fields['provenance'] = '';
					$etap_file_u->save();

					//on supprime le doc dans dims_mod_doc_file
					$doc_f = new docfile();
					$doc_f->open($id_doc);
					$doc_f->delete();

					//redirection
					dims_redirect("index.php?id_event=".$id_evt);

					break;
				case 'modif_ct':

					require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');

						$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, false, true);

						if (isset($_SESSION['dims']['user']['id_contact']) && $id_ct==$_SESSION['dims']['user']['id_contact']) {

							$ct = new contact();
							$ct->open($id_ct);
							$ct->setvalues($_POST, 'ct_');
							$ct->save();
						}
					//redirection
					dims_redirect("index.php");
					break;
			}
			break;
	}
}

if (!empty($old_lang)) {
	$_SESSION['cste']=$old_lang;
	$dims->loadLanguage($old_id_lang);
}
?>
</div>
