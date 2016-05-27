<?php
include(DIMS_APP_PATH . '/modules/system/class_ent.php');
include(DIMS_APP_PATH . '/modules/system/class_contact.php');
include(DIMS_APP_PATH . '/modules/system/class_ent_contact.php');
include(DIMS_APP_PATH . '/modules/system/class_ent_services.php');

if (!isset($op)) $op = '';

$action=dims_load_securvalue("action",_DIMS_CHAR_INPUT,true,true);

$workspaces = dims_viewworkspaces($_SESSION['dims']['moduleid']);

require_once(DIMS_APP_PATH . "/modules/system/include/contact.js");

if (!isset($_SESSION['dims']['contact_type'])) $_SESSION['dims']['contact_type']=1;

// filtre pass� en param�tre
$filtre = dims_load_securvalue("contact_type",_DIMS_NUM_INPUT,true,true);
if ($filtre!="") {
	$_SESSION['dims']['contact_type']=$filtre;
}

echo "<div class=\"dims_menuleft\" style=\"width:400px;float:left;\">";
echo $skin->open_simplebloc(_SYSTEM_MANAGE_CONTACT,'100%','','',false);

echo "<table width=\"80%\"><tr><td width=\"50%\">";
echo dims_create_button(_SYSTEM_LABEL_CONTACTS,"","javascript:document.location.href='".$scriptenv."?contact_type=1'","","");
echo "</td><td width=\"50%\">";
echo dims_create_button(_SYSTEM_LABEL_ENTERPRISES,"","javascript:document.location.href='".$scriptenv."?contact_type=2'","");
echo "</td></table>";

if ($action!="refresh") {
	if ($_SESSION['dims']['contact_type']==1)
		require_once(DIMS_APP_PATH . '/modules/system/public_contact_list.php');
	else
		require_once(DIMS_APP_PATH . '/modules/system/public_ent_list.php');
}

echo $skin->close_simplebloc();

echo "</div><div style=\"overflow:auto;\">";

$title="";

echo $skin->open_simplebloc($title,'100%','','');
echo "<div id=\"profil_contact\">";

//$refresh=dims_load_securvalue("refresh",_DIMS_CHAR_INPUT,true,true);

switch($action) {
	 default: echo "<p sytle=\"text-align:center;\">Veuillez s&eacute;l&eacute;ctionner un contact ou entreprise</p>";
	 break;
	 case "view_services":
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			while(@ob_end_clean());
			//require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
			//$skin = new skin();
			//echo $skin->open_simplebloc("");
			echo "<script type=\"text/javascript\" src=\"/js/functions.js\"></script>";
			echo " <!--[if IE]><script type=\"text/javascript\" src=\"./js/excanvas.js\"></script><![endif]-->";
			echo "<br/><div id=\"containerServices\" style=\"overflow:auto;position:relative;\">";

			$id=dims_load_securvalue("id",_DIMS_NUM_INPUT,true,true);
			get_servicesMapview($id);
			echo "</div>";
			//echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
			//<input type=\"button\" onclick=\"\" value=\"+\" class=\"flatbutton\"/></div>";
			//echo $skin->close_simplebloc();
			die();
		}
		break;
	case 'add_service':
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			while(@ob_end_clean());

			require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
			$skin=new skin();
			echo $skin->open_simplebloc("","","","<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\">"._DIMS_CLOSE."</a>");
			echo "<div style=\"overflow:auto;position:relative;\">";

			?>
			<form name="f_addService" action="<?=$scriptenv;?>" method="post">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("action",		"save_service");
				$token->field("id_service",	$id_service);
				$token->field("label");
				$token->field("");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="action" value="save_service" />
			<input type="hidden" name="id_service" value="<?=$id_service;?>" />
			Libell&eacute; : <input name="label" maxlength="255" />
			<input type="submit" value="Ajouter" />
			</form>

			<script language="JavaScript">
				document.f_addService.label.focus();
			</script>
			<?

			echo "</div>";

			echo $skin->close_simplebloc();

			die();
		}
		break;
	case 'save_service':
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			if (!empty($_POST['id_service']) && is_numeric($_POST['id_service'])) {
				require_once DIMS_APP_PATH . '/modules/system/class_ent_service.php';
				$es_parent = new ent_service();
				if ($es_parent->open(dims_load_securvalue('id_service', dims_const::_DIMS_NUM_INPUT, true, true, true))) {
					$es_enfant = new ent_service();
					$es_enfant->fields['id_service'] = $es_parent->fields['id'];
					$es_enfant->fields['label'] = dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					$es_enfant->fields['parents'] = $es_parent->fields['parents'].';'.$es_parent->fields['id'];
					$es_enfant->fields['depth'] = $es_parent->fields['depth'] + 1;
					$es_enfant->save();
				}
			}
		}
		dims_redirect($scriptenv);
		break;
	case 'drop_service':
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			if (!empty($_GET['id_service']) && is_numeric($_GET['id_service'])) {
				require_once DIMS_APP_PATH . '/modules/system/class_ent_service.php';
				$es_parent = new ent_service();
				if ($es_parent->open(dims_load_securvalue('id_service', dims_const::_DIMS_NUM_INPUT, true, true, true))) {
					$es_parent->delete();
				}
			}
		}
		dims_redirect($scriptenv);
		break;
	case 'display_serviceContacts':
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			if (!empty($_GET['id_service']) && is_numeric($_GET['id_service'])) {
				require_once DIMS_APP_PATH . '/modules/system/class_ent_service.php';
				$es = new ent_service();
				if ($es->open(dims_load_securvalue('id_service', dims_const::_DIMS_NUM_INPUT, true, true, true))) {
					while(@ob_end_clean());
					require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
					$skin = new skin();
					echo $skin->open_simplebloc('Contacts du service "'. $es->fields['label'].'"','100%','','');
					$contacts = system_getServiceContacts($es->fields['id']);
					foreach ($contacts as $c) {
						echo '- '. $c['firstname'] .' '. $c['lastname'] .'<br/>';
					}
					echo $skin->close_simplebloc();
				}
			}
			die();
		}
		break;
	case "refresh":
		while(@ob_end_clean());

		if ($_SESSION['dims']['contact_type']==1)
			require_once(DIMS_APP_PATH . '/modules/system/public_contact_list.php');
		else
			require_once(DIMS_APP_PATH . '/modules/system/public_ent_list.php');

			die();
		break;

	case "list":
		if($refresh){
			while(@ob_end_clean());
			ob_start();
			if ($_SESSION['dims']['contact_type']==1)
				include(DIMS_APP_PATH . '/modules/system/public_contact_list.php');
			else
				include(DIMS_APP_PATH . '/modules/system/public_ent_list.php');
			ob_end_flush();
			die();
		}
		else {
			if ($_SESSION['dims']['contact_type']==1)
				include(DIMS_APP_PATH . '/modules/system/public_contact_list.php');
			else
				include(DIMS_APP_PATH . '/modules/system/public_ent_list.php');
		}
	break;

	case "save_contact" :
		$id = dims_load_securvalue("id",_DIMS_CHAR_INPUT,true,true);

		$cont = new contact();
		if(!empty($id)) {
			$cont->open($_SESSION['dims']['contactid']);
		}
		else {
			$cont->init_description();
		}

		$cont->setvalues($_POST, 'contact_');
		$cont->fields['id_user'] = $_SESSION['dims']['userid'];
		$cont->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$cont->save();
		include(DIMS_APP_PATH . '/modules/system/public_contact_profil.php');
	break;

	case "save_ent" :
		$id = dims_load_securvalue("id",_DIMS_CHAR_INPUT,true,true);

		$ent = new ent();
		if(!empty($id)) {
			$ent->open($_SESSION['dims']['ent_id']);
		}
		else {
			$ent->init_description();
		}

		$ent->setvalues($_POST, 'ent_');
		$ent->fields['id_user'] = $_SESSION['dims']['userid'];
		$ent->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$ent->save();
		include(DIMS_APP_PATH . '/modules/system/public_ent_profil.php');
	break;

	case "link_cont_to_ent" :
		$id = dims_load_securvalue("id",_DIMS_CHAR_INPUT,true,true);

		$ent_contact = new ent_contact();
		$ent_contact->init_description();

		$ent_contact->setvalues($_POST,"ent_");
		$ent_contact->fields['date_deb'] = dims_local2timestamp(dims_load_securvalue('ent_date_deb', dims_const::_DIMS_CHAR_INPUT, true, true, true));
		$ent_contact->fields['date_fin'] = dims_local2timestamp(dims_load_securvalue('ent_date_fin', dims_const::_DIMS_CHAR_INPUT, true, true, true));
		$ent_contact->fields['id_contact'] = $id;
		$ent_contact->save();
		include(DIMS_APP_PATH . '/modules/system/public_contact_profil.php');
	break;


	case "profil":
			while(@ob_end_clean());
			ob_start();
			include(DIMS_APP_PATH . '/modules/system/public_contact_profil.php');
			ob_end_flush();
			die();
	break;

	case "entreprise":
			while(@ob_end_clean());
			ob_start();
			include(DIMS_APP_PATH . '/modules/system/public_ent_profil.php');
			ob_end_flush();
			die();
	break;

	case "add":
			while(@ob_end_clean());
			ob_start();
			include(DIMS_APP_PATH . '/modules/system/public_contact_add.php');
			ob_end_flush();
			die();
	break;
	case "search":
			while(@ob_end_clean());
			ob_start();
			include(DIMS_APP_PATH . '/modules/system/public_contact_search.php');
			ob_end_flush();
			die();
	break;

	case "search_ent" :
		while(@ob_end_clean());
		ob_start();
		include(DIMS_APP_PATH . '/modules/system/public_ent_search.php');
		ob_end_flush();
		die();
	break;

}
echo "</div>";
echo $skin->close_simplebloc();
echo '<div id="service_contacts"></div>';
echo "</div>";
?>
