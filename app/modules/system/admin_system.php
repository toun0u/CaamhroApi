<?php
$toolbar = array();

if (!isset($_SESSION['dims']['moduleicon']) || $_SESSION['dims']['moduleicon']=="") $_SESSION['dims']['moduleicon']=_SYSTEM_ICON_SYSTEM_INSTALLMODULES;


if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
	$toolbar[_SYSTEM_ICON_SYSTEM_INSTALLMODULES] = array(
										'title'		=> $_DIMS['cste']['_DIMS_LABEL_INSTALLMODULES'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_INSTALLMODULES,
										'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_install_module.png"
									);

	$toolbar[_SYSTEM_ICON_SYSTEM_DOMAINS] = array(
										'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_DOMAINS'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_DOMAINS,
										'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_domain.png"
									);

	/*
		 $toolbar[_SYSTEM_ICON_SYSTEM_PARAMS] = array(

										'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_PARAMS'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_PARAMS,
										'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_systemparams.png"
									);


	$toolbar[_SYSTEM_ICON_SYSTEM_TOOLS] = array(
										'title'		=> $_DIMS['cste']['_DIMS_LABEL_TOOLS'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_TOOLS,
										'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_tools.png"
									);
		*/
	$toolbar[_SYSTEM_ICON_SYSTEM_LOGS] = array(
										'title'		=> $_DIMS['cste']['_DIMS_ADMIN_LOGS'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_LOGS,
										'icon'	=> "./common/img/stats.png"
									);
	$toolbar[_SYSTEM_ICON_SYSTEM_INDEX] = array(
										'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_INDEX'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_INDEX,
										'icon'	=> "./common/img/search.png"
									);
	$toolbar[_SYSTEM_ICON_SYSTEM_MAILBOX] = array(
										'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_MAILBOX'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_MAILBOX,
										'icon'	=> "./common/img/icon_tickets.gif"
									);
	$toolbar[_SYSTEM_ICON_SYSTEM_LANG] = array(
										'title'		=> $_DIMS['cste']['_DIMS_LABEL_CODE_OF_CONDUCT'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_LANG,
										'icon'	=> "./common/img/user.png"
									);

	/*$toolbar[_SYSTEM_ICON_SYSTEM_TRADUCTION] = array(
										'title'		=> $_DIMS['cste']['_DIMS_LABEL_TRADUCTION'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_TRADUCTION,
										'icon'	=> "./common/img/traduction.png"
									);
		 */
	$toolbar[_SYSTEM_ICON_SYSTEM_JABBER] = array(
										'title'		=> "Admin jabber",
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_JABBER,
										'icon'	=> "./common/img/social.png"
									);

	$toolbar[_SYSTEM_ICON_SYSTEM_CATEGORY] = array(
										'title'		=> $_DIMS['cste']['_DIMS_LABEL_CATEGORY'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_CATEGORY,
										'icon'	=> "./common/img/social.png"
										);

	$toolbar[_SYSTEM_ICON_SYSTEM_SERVER] = array(
										'title'		=> $_DIMS['cste']['_SERVER'],
										'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_SYSTEM_SERVER,
										'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_systemparams.png"
										);
}


echo $skin->create_toolbar($toolbar,$_SESSION['dims']['moduleicon']);
echo $skin->close_toolbar();
?>

<div>
	<?php
	switch($_SESSION['dims']['moduleicon']) {
		// ---------------------------------
		// ONGLET "INSTALLATION DE MODULES"
		// ---------------------------------
		default:
		case _SYSTEM_ICON_SYSTEM_INSTALLMODULES:
			switch($op) {
				case 'update':
					include(DIMS_APP_PATH . "/modules/system/admin_system_installmodules_updateproc.php");
				break;

				case 'install':
					include(DIMS_APP_PATH . "/modules/system/admin_system_installmodules_installproc.php");
				break;

				case 'uninstall':
					global $admin_redirect;
					$admin_redirect	= true;

					include(DIMS_APP_PATH . "/modules/system/admin_system_installmodules_uninstallproc.php");

					if ($admin_redirect) dims_redirect("$scriptenv?reloadsession");
					else {
						echo "
								</TD>
							</TR>
							<TR>
								<TD ALIGN=\"RIGHT\">";
						?>
								<INPUT TYPE="Button" CLASS="flatbutton" VALUE="<?php echo $_DIMS['cste']['_DIMS_CONTINUE']; ?>" OnClick="javascript:document.location.href='<? echo "$scriptenv?reloadsession"; ?>'">
						<?php
							echo "</TD>
							</TR>
							</TABLE>";

						echo $skin->close_simplebloc();
					}

				break;

				case 'addnewmodule':
					include(DIMS_APP_PATH . "/modules/system/admin_system_addnewmodule.php");
					//dims_redirect("$scriptenv");
				break;

				case 'uploadmodule':
					// zip file ?
					if (strstr($_FILES['system_modulefile']['name'],'.zip')) {
						$install_path = realpath('.')._DIMS_SEP.'install'._DIMS_SEP;
						$newpath = $install_path.$_FILES['system_modulefile']['name'];

						if (move_uploaded_file($_FILES['system_modulefile']['tmp_name'],$newpath))
						{
							//unzip
							exec("unzip -o -qq ".escapeshellarg("$newpath")." -d ".escapeshellarg("$install_path"));
							exec("chmod -R 755 ".escapeshellarg("$install_path"));
							//delete
							unlink($newpath);
						}
					}
					dims_redirect("$scriptenv");

				break;

				// update metabase
				case 'updatemb':
					$idmoduletype=dims_load_securvalue('idmoduletype',dims_const::_DIMS_NUM_INPUT,true,false,false);
					if (isset($idmoduletype) && $idmoduletype>0) {
						require_once (DIMS_APP_PATH . '/modules/system/xmlparser_mb.php');

						$module_type = new module_type();
						$module_type->open($idmoduletype);

						dims_create_user_action_log(_SYSTEM_ACTION_UPDATEMETABASE, $module_type->fields['label']);

						//$res=$db->query("DELETE FROM dims_mb_field WHERE id_module_type = {$idmoduletype}");
						$tabfieldobject= array();

						$res=$db->query("select * FROM dims_mb_field WHERE id_module_type = :idmoduletype ", array(':idmoduletype' => $idmoduletype));
						while ($fields = $db->fetchrow($res)) {
							$tabfieldobject[$fields['id']]=$fields;
						}

						$res=$db->query("DELETE FROM dims_mb_relation WHERE id_module_type = :idmoduletype ", array(':idmoduletype' => $idmoduletype));
						$res=$db->query("DELETE FROM dims_mb_schema WHERE id_module_type = :idmoduletype ", array(':idmoduletype' => $idmoduletype));
						$res=$db->query("DELETE FROM dims_mb_table WHERE id_module_type = :idmoduletype ", array(':idmoduletype' => $idmoduletype));
						$res=$db->query("DELETE FROM dims_mb_object WHERE id_module_type = :idmoduletype ", array(':idmoduletype' => $idmoduletype));
						//$db->query("DELETE FROM dims_mb_wce_object WHERE id_module_type = {$idmoduletype}");
						$tabwceobject= array();

						$res=$db->query("select * FROM dims_mb_wce_object WHERE id_module_type = :idmoduletype ", array(':idmoduletype' => $idmoduletype));
						while ($fields = $db->fetchrow($res)) {
							$tabwceobject[$fields['id']]=$fields;
						}

						$mbfile = DIMS_APP_PATH . "/install/{$module_type->fields['label']}/mb.xml";


						if (file_exists($mbfile)) {
							if (!(list($xml_parser, $fp) = xmlparser_mb($mbfile))) {
								echo "<TD>Erreur de lecture du fichier XML ($mbfile)</TD>";
							}
							else {
								$stop = '';
								while (($data = fread($fp, 4096)) && $stop == '') {
									if (!xml_parse($xml_parser, $data, feof($fp))) {
										$stop = sprintf("Erreur XML: %s  la ligne %d dans '$mbfile'\n", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser));
									}
								}
								xml_parser_free($xml_parser);
								//xmlparser_mod_free($xml_parser);
							}

							// on parcourt pour mettre a jour les elements que l'on supprime
							if (sizeof($tabwceobject)>0) {
								$res=$db->query("delete FROM dims_mb_wce_object WHERE id_module_type = :idmoduletype and id in (".implode(",",$tabwceobject), array(':idmoduletype' => $idmoduletype));
							}

							// on parcourt pour mettre a jour les elements que l'on supprime
							if (sizeof($tabfieldobject)>0) {
								$res=$db->query("delete FROM dims_mb_field WHERE id_module_type = :idmoduletype and id in (".implode(",",$tabfieldobject), array(':idmoduletype' => $idmoduletype));
							}
						}
					}
					dims_redirect($scriptenv);
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/admin_system_installmodules.php');
				break;

			}
		break;

		case _SYSTEM_ICON_SYSTEM_INSTALLSKINS:
			switch($op)
			{
				case "install":
					if (isset($skinlabel))
					{
						$select =	"SELECT	value
									FROM	dims_param_choice
									WHERE	id_param_type = 15
									AND	value = :value ";
						$res=$db->query($select, array(':value' => addslashes($skinlabel) ) );
						if (!$db->numrows())
						{
							dims_create_user_action_log(_SYSTEM_ACTION_INSTALLSKIN, $skinlabel);

							require_once (DIMS_APP_PATH . '/modules/system/class_param_choice.php');
							$param_choice = new param_choice();
							$param_choice->fields['id_param_type'] = 15;
							$param_choice->fields['value'] = $skinlabel;
							$param_choice->fields['displayed_value'] = $skinlabel;
							$param_choice->save();
						}
					}
					dims_redirect("$scriptenv");
				break;

				case "uninstall":
					$skinlabel=dims_load_securvalue("skinlabel",dims_const::_DIMS_CHAR_INPUT,true,true,true);
					if ($skinlabel!="")
					{
						dims_create_user_action_log(_SYSTEM_ACTION_UNINSTALLSKIN, $skinlabel);

						$select =	"DELETE
									FROM	dims_param_choice
									WHERE	id_param_type = 15
									AND	value = :value ";
						$res=$db->query($select, array(':value' => dims_sql_filter($skinlabel) ) );
					}
					dims_redirect("$scriptenv");
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/admin_system_installskins.php');
				break;

			}
		break;

		case _SYSTEM_ICON_SYSTEM_TOOLS:
			switch($op)
			{
				case "phpinfo":
					ob_start();
					phpinfo();
					$info = ob_get_contents();
					ob_end_clean();
					$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
					$info = strip_tags($info,'<table><td><tr><h1><h2><h3><a><br>');
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_PHPINFO'],'100%');
					echo "<p align=\"left\">$info</p>";
					echo $skin->close_simplebloc();

				break;
							/*
				case "diagnostic":
					include(DIMS_APP_PATH . "/modules/system/tools_diagnostic.php");
				break;

				case "sqldump":
					include(DIMS_APP_PATH . "/modules/system/tools_sqldump.php");
				break;

				case "zip":
					include(DIMS_APP_PATH . "/modules/system/tools_zip.php");
				break;

				case "backup":
					include(DIMS_APP_PATH . "/modules/system/tools_backup.php");
				break;

				case "cleandb":
					include(DIMS_APP_PATH . "/modules/system/tools_cleandb.php");
					dims_redirect("$scriptenv");
				break;

				case "updateaction":
					include(DIMS_APP_PATH . "/modules/system/tools_updateaction.php");
					dims_redirect("$scriptenv");
				break ;

								case "updateactiontag":
					include(DIMS_APP_PATH . "/modules/system/tools_updateactiontag.php");
					dims_redirect("$scriptenv");
				break ;
				case "updateurml":
					include(DIMS_APP_PATH . "/modules/system/tools_updateurml.php");
					dims_redirect("$scriptenv");
				break ;
				default:
					include(DIMS_APP_PATH . '/modules/system/admin_system_tools.php');
				break;
								*/
			}
		break;

		case _SYSTEM_ICON_SYSTEM_LOGS:
			switch($op)
			{
				case "delete_logs":
					$res=$db->query('truncate dims_user_action_log');
					dims_redirect("$scriptenv?op=actionhistory");
				break;

				case "connectedusers":
					include(DIMS_APP_PATH . "/modules/system/logs_connectedusers.php");
				break;

				case "actionhistory":
					include(DIMS_APP_PATH . "/modules/system/logs_actionhistory.php");
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/admin_system_logs.php');
				break;
			}
		break;
		case _SYSTEM_ICON_SYSTEM_INDEX:
			switch($op)
			{
				case "indexrun":
					include(DIMS_APP_PATH . "/modules/system/index_run.php");
				break;

				default:
					include(DIMS_APP_PATH . '/modules/system/admin_system_index.php');
				break;
			}
		break;
		// -------------------------------------------------
		// ONGLET DE GESTION DES PARAMETRES GENERAUX DE DIMS
		// -------------------------------------------------
		case _SYSTEM_ICON_SYSTEM_PARAMS :
			$param_module = new param();

			switch($op) {
				case "save":
					$idmodule=dims_load_securvalue("idmodule",dims_const::_DIMS_NUM_INPUT,false,true,true);
					if ($dimodule>0) {
						$module = new module();
						$module->open($idmodule);
						dims_create_user_action_log(_SYSTEM_ACTION_PARAMMODULE, $module->fields['label']);

						$param_module->open($idmodule);
						$param_module->setvalues($_POST);
						$param_module->save();
					}
					dims_redirect("$scriptenv?idmodule=$idmodule&reloadsession");
				break;

				default:
					require_once DIMS_APP_PATH . '/modules/system/admin_system_param.php';
				break;
			}
		break;
		case _SYSTEM_ICON_SYSTEM_DOMAINS :
			$domain = new domain();

			switch($op) {
				case "switch_ssl":
					$domainid=dims_load_securvalue('domain_id',dims_const::_DIMS_NUM_INPUT,true,false,false);

					if ($domainid>0) {
						$domain->open($domainid);
						$domain->fields['ssl']=!$domain->fields['ssl'];
						$domain->save();
					}
					dims_redirect("$scriptenv");
					break;
				case "switch_mobile":
					$domainid=dims_load_securvalue('domain_id',dims_const::_DIMS_NUM_INPUT,true,false,false);

					if ($domainid>0) {
						$domain->open($domainid);
						$domain->fields['mobile']=!$domain->fields['mobile'];
						$domain->save();
					}
					dims_redirect("$scriptenv");
					break;
				case "save_domain":
					$domainid=dims_load_securvalue('domain_id',dims_const::_DIMS_NUM_INPUT,false,true,false);
					$erreurcode=0;
					if ($domainid>0) {
						$domain->open($domainid);
					}
					else {
						$domain = dims_load_securvalue('domain_domain', dims_const::_DIMS_CHAR_INPUT, true, true, true);
						$select =	"SELECT	id
									FROM	dims_domain
									WHERE	domain like :domain ";

						$res=$db->query($select, array(':domain' => $domain) );

						if ($db->numrows($res)>0) {
							$erreurcode=1;
						} else {
							$domain = new domain();
							$domain->init_description(true);
						}
					}
					if ($erreurcode==0) {
						$domain->setvalues($_POST,'domain_');
						if (empty($_POST['domain_ssl'])) $domain->fields['ssl'] = 0;
						if (empty($_POST['domain_mobile'])) $domain->fields['mobile'] = 0;

						$domain->save();
						dims_redirect($dims->getScriptEnv());
					}
					else dims_redirect("$scriptenv?op=add_domain&error=1");
				break;

				case "modify":
					$domainid=dims_load_securvalue('id_domain',dims_const::_DIMS_NUM_INPUT,true,false,false);
					if ($domainid>0) {
						$domain->open($domainid);
						require_once DIMS_APP_PATH . '/modules/system/admin_system_domain_form.php';
					}
					else dims_redirect("$scriptenv");
				break;

				case "delete":
				$domainid=dims_load_securvalue('id_domain',dims_const::_DIMS_NUM_INPUT,true,true,false);
				if ($domainid>0) {
					$domain->open($domainid);
					$domain->delete();
				}
				dims_redirect($scriptenv);
				break;

				case "add_domain":
					$domain->init_description();
					require_once DIMS_APP_PATH . '/modules/system/admin_system_domain_form.php';
				break;

				default:
					require_once DIMS_APP_PATH . '/modules/system/admin_system_domain.php';
				break;
			}
		break;

		/***
		 * Gestion des mailbox
		 ***/
		case _SYSTEM_ICON_SYSTEM_MAILBOX :
			require_once DIMS_APP_PATH.'modules/system/class_webmail_inbox.php';
			require_once DIMS_APP_PATH.'modules/system/class_webmail_email.php';

			$mailBox = new webmail_inbox();

			switch($op) {
				case 'modify_mailbox':
					$mailBoxId = dims_load_securvalue('id_mailbox', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($mailBoxId > 0) {
						$mailBox->open($mailBoxId);
						require_once DIMS_APP_PATH.'modules/system/admin_system_mailbox_form.php';
					} else {
						dims_redirect($scriptenv);
					}
					break;

				case 'add_mailbox':
					$mailBox->init_description();
					require_once DIMS_APP_PATH.'modules/system/admin_system_mailbox_form.php';
					break;

				case 'delete_mailbox':
					$mailBoxId = dims_load_securvalue('id_mailbox', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($mailBoxId > 0) {
						$mailBox->open($mailBoxId);
						$mailBox->delete();
					}
					dims_redirect($scriptenv);
					break;

				case 'save_mailbox':
					$mailBoxId = dims_load_securvalue('id_mailbox', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($mailBoxId > 0) {
						$mailBox->open($mailBoxId);
					} else {
						$mailBox->init_description();
					}

					// ne pas changer le mdp si il est vide
                    if (isset($_POST['inbox_password']) && empty($_POST['inbox_password']))
                        unset($_POST['inbox_password']);

					$mailBox->setvalues($_POST, 'inbox_');
					$mailBox->save();

					dims_redirect($dims->getScriptEnv());

					break;

				case 'check_mailbox':
					$mailBoxId = dims_load_securvalue('id_mailbox', dims_const::_DIMS_NUM_INPUT, true, true, false);
					$mailRetrieve = -1;
					if($mailBoxId > 0) {
						$mailBox->open($mailBoxId);
						$mailBox->connect();
						$mailRetrieve = $mailBox->updateMailBox();
						/*echo $mailRetrieve;
						$mailBox->save();
						dims_print_r($mailBox); die();*/
					}

					dims_redirect($dims->getScriptEnv().'?mailRetrieve='.$mailRetrieve);

					break;
				case 'manual_attach':
					require_once DIMS_APP_PATH . '/modules/system/crm_cron_webmail.php';
					break;

				default:
					require_once DIMS_APP_PATH.'modules/system/admin_system_mailbox.php';
				break;
			}
		break;

		case _SYSTEM_ICON_SYSTEM_LANG:
			require_once DIMS_APP_PATH.'modules/system/class_lang.php';

			$objlang = new lang();

			switch($op) {
				case 'modify_lang':
					$langId = dims_load_securvalue('id_lang', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($langId > 0) {
						$objlang->open($langId);
						require_once DIMS_APP_PATH.'modules/system/admin_system_lang_form.php';
					}
					break;
				case 'save_lang':
					$langId = dims_load_securvalue('id_lang', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($langId > 0) {
						$objlang->open($langId);
						$objlang->setvalues($_POST, 'lang_');
						$objlang->save();
					}
					dims_redirect($scriptenv);
					break;
				default:
					require_once DIMS_APP_PATH.'modules/system/admin_system_lang.php';
					break;
			}
			break;
		case _SYSTEM_ICON_SYSTEM_TRADUCTION:
			if (!isset($_SESSION['dims']['current_adminlang'])) {
				$_SESSION['dims']['current_adminlang']=1;
			}

			$admin_lang=dims_load_securvalue('admin_lang', dims_const::_DIMS_NUM_INPUT, true, true, false);
			if ($admin_lang>0) {
				$_SESSION['dims']['current_adminlang']=$admin_lang;
			}

			require_once DIMS_APP_PATH.'include/class_constant.php';

			$cstelang = new dims_constant();

			$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true, false);
			switch($op) {
				case 'active_cstelang' :
					require_once DIMS_APP_PATH.'modules/system/class_lang.php';
					$lang = new lang();
					$lang->open($_SESSION['dims']['current_adminlang']);
					$lang->fields['isactive'] = !$lang->fields['isactive'];
					$lang->save();
					dims_redirect($dims->getScriptEnv());
					break;
				case 'modify_cstelang':
					$langId = dims_load_securvalue('id_cstelang', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($langId > 0) {
						$cstelang->open($langId);
						require_once DIMS_APP_PATH.'modules/system/admin_system_cstelang_form.php';
					}
					break;
				case 'save_cstelang':
					$langId = dims_load_securvalue('id_cstelang', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if($langId > 0) {
						$cstelang->open($langId);
						$cstelang->setvalues($_POST, 'cstelang_');

						$cstelang->save();
					}
					dims_redirect($scriptenv);
					break;

				case 'save_check_cstelang':
					$cstelang->init_description();
					$cstelang->fields['id_lang']=$_SESSION['dims']['current_adminlang'];
					$cstelang->setvalues($_POST, 'cstelang_');
					$cstelang->save();
					dims_redirect('/admin.php?op=needed_traduction');
					break;
				case 'needed_traduction':
					require_once DIMS_APP_PATH.'modules/system/admin_system_cstelang_checkform.php';
					break;
				default:
					require_once DIMS_APP_PATH.'modules/system/admin_system_cstelang.php';
					break;
			}
			break;

				case _SYSTEM_ICON_SYSTEM_JABBER:
						require_once(DIMS_APP_PATH . "/include/class_dims_intercom.php");

						$hostname=dims_load_securvalue('host_name',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['ejabber']['host_name']);
						$hostip=dims_load_securvalue('host_ip',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['ejabber']['host_ip']);
						$dimsname=dims_load_securvalue('dims_name',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['ejabber']['dims_name']);

						$intercom = new dims_intercom($db);

						// verification si existence
						if ($dimsname!='') {
							/*$dimsname=$_SESSION['ejabber']['dims_name'];
							$hostip=$_SESSION['ejabber']['host_ip'];
							$hostname=$_SESSION['ejabber']['host_name'];*/
							// si 1 existe deja
							$alreadyexists=$intercom->verifyConnexion($hostip,$hostname,$dimsname);
						}

						switch($op) {
							case 'result_request':
								ob_end_clean();
								if ((isset($_SESSION['ejabber']['success']) && $_SESSION['ejabber']['success']!= '' ) || (isset($_SESSION['ejabber']['error']) && $_SESSION['ejabber']['error']!= '') ) {
									echo "<script language=\"javascript\">window.location.href='/admin.php';</script>";
								}
								else {
									//echo "<script language=\"javascript\">stopQuery();</script>";
									require_once DIMS_APP_PATH.'modules/system/admin_system_jabber_result.php';
								}
								die();
								break;
							case 'execute_request':
								ob_end_clean();

								if ((isset($_SESSION['ejabber']['success']) && $_SESSION['ejabber']['success']!= '' ) || (isset($_SESSION['ejabber']['error']) && $_SESSION['ejabber']['error']!= '') ) {
									echo "<script language=\"javascript\">window.location.href='/admin.php';</script>";
								}
								else {
									$action=dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true, false);
									switch ($action) {
										case 'initDims':
											require_once DIMS_APP_PATH.'modules/system/admin_system_jabber_execute.php';
											break;
									}
									echo "<table width=\"100%\" height=\"400\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
								}
								die();
								break;
							case 'initDims':
							case 'InitDimsContact':

								if ($alreadyexists) {
									dims_redirect('/admin.php');
								}
								else {
									unset($_SESSION['ejabber']['success']);
									unset($_SESSION['ejabber']['message']);
									$_SESSION['ejabber']['op']= 'execute_request';
									$_SESSION['ejabber']['reply']=0;
									$_SESSION['ejabber']['action']= $op;
									$_SESSION['ejabber']['error']='';
									$_SESSION['ejabber']['host_name']=dims_load_securvalue('host_name',dims_const::_DIMS_CHAR_INPUT,true,true);
									$_SESSION['ejabber']['host_ip']=dims_load_securvalue('host_ip',dims_const::_DIMS_CHAR_INPUT,true,true);
									$_SESSION['ejabber']['dims_name']=dims_load_securvalue('dims_name',dims_const::_DIMS_CHAR_INPUT,true,true);
									$_SESSION['ejabber']['host_name']=dims_load_securvalue('host_name',dims_const::_DIMS_CHAR_INPUT,true,true);
									$_SESSION['ejabber']['host_securitykey']=dims_load_securvalue('host_securitykey',dims_const::_DIMS_CHAR_INPUT,true,true);
									require_once DIMS_APP_PATH.'modules/system/admin_system_jabber_javascript.php';
								}
								break;
							default:

								require_once DIMS_APP_PATH.'modules/system/admin_system_jabber.php';
								break;
							break;
						}
						break;
		case _SYSTEM_ICON_SYSTEM_SYNCHRO:
			 switch($op) {
				case 'save_synchro':

					if (dims_isadmin()) {
						$db->query("truncate dims_mb_table_synchro");
						if (isset($_POST['sel_synctable'])) {
							$selsynctable = dims_load_securvalue('sel_synctable', dims_const::_DIMS_CHAR_INPUT, true, true, true);
							foreach ($selsynctable as $tablename) {
								$db->query("INSERT into dims_mb_table_synchro set tablename= :tablename , destination=''", array(':tablename' => dims_sql_filter($tablename) ) );
								echo "insert into dims_mb_table_synchro set tablename='".dims_sql_filter($tablename)."', destination=''";
							}
						}
					}
					dims_redirect('/admin.php?op=dims_moduleicon='._SYSTEM_ICON_SYSTEM_SYNCHRO);
				break;

				default:
					// list of constants
					require_once DIMS_APP_PATH.'modules/system/admin_system_synchro.php';
				break;
			 }
		break;
		case _SYSTEM_ICON_SYSTEM_CATEGORY :
			require_once DIMS_APP_PATH.'modules/system/class_category.php';

			$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true, false);
			switch($op) {
				default :
				case 'view':
					require_once DIMS_APP_PATH.'modules/system/admin_system_category.php';
					break;
				case 'saveNew' :
					$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					$label = dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT, true, true, true);
					$level = dims_load_securvalue('level',dims_const::_DIMS_NUM_INPUT, true, true, true);
					$categ = new category();
					$categ->init_description();
					if ($id > 0){
						// on crée une sous-catég
						$categ->open($id);
						$categ->addSubCategory($label, $level);
					}else{
						// on crée une racine
						$categ->setugm();
						$categ->setLabel($label);
						$categ->setLevel($level);
						$categ->save();

						if (isset($_SESSION['dims']['categFiltre']['obj']) && $_SESSION['dims']['categFiltre']['obj'] > 0){
							// module + object
							$rule = new category_object();
							$rule->create($categ->getID(),$_SESSION['dims']['categFiltre']['obj'], $_SESSION['dims']['categFiltre']['module']);
							$rule->save();
						}else{
							// module
							$rule = new category_module_type();
							$rule->create($categ->getID(), $_SESSION['dims']['categFiltre']['module']);
							$rule->save();
						}
					}
					dims_redirect($dims->getScriptEnv());
					break;
				case 'saveEdit':
					$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id != '' && $id > 0){
						$label = dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT, true, true, true);
						$level = dims_load_securvalue('level',dims_const::_DIMS_NUM_INPUT, true, true, true);
						$categ = new category();
						$categ->open($id);
						$categ->setLabel($label);
						$categ->setLevel($level);
						$categ->save();
					}
					dims_redirect($dims->getScriptEnv());
					break;
				case 'deleteCateg':
					$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id != '' && $id > 0){
						$categ = new category();
						$categ->open($id);
						$categ->delete();
					}
					dims_redirect($dims->getScriptEnv());
					break;
			}

			break;
		case _SYSTEM_ICON_SYSTEM_SERVER :
			require_once DIMS_APP_PATH.'modules/system/class_server.php';

			$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true, false);
			switch($op) {
				default :
				case 'view':
					require_once DIMS_APP_PATH.'modules/system/admin_system_server.php';
					break;
				case 'add_server':
				case 'modify_server':
					$id_server = dims_load_securvalue('id_server', dims_const::_DIMS_NUM_INPUT, true, true, true);

					$server = new dims_server();

					if(!empty($id_server)) {
						$server->open($id_server);
					}
					else {
						$server->init_description();
					}

					$server->display(DIMS_APP_PATH.'modules/system/admin_system_server_form.php');
					break;
				case 'save_server':
					$id_server = dims_load_securvalue('id_server',dims_const::_DIMS_NUM_INPUT,true,true,true);

					$server = new dims_server();
					if(!empty($id_server)) {
						$server->open($id_server);
					}
					else {
						$server->init_description();
					}

					// Reset boolean var
					$server->fields['ssh'] = 0;
					$server->fields['ssl'] = 0;

					$server->setvalues($_POST, 'server_');

					$server->save();

					dims_redirect($dims->getScriptEnv());
					break;
				case 'check_server':
					$id_server = dims_load_securvalue('id_server',dims_const::_DIMS_NUM_INPUT,true,true,true);

					$server = new dims_server();
					if(!empty($id_server)) {
						$server->open($id_server);

						$server->checkConnection();

						$server->save();
					}
					dims_redirect($dims->getScriptEnv());
					break;
				case 'delete_server':
					$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id != '' && $id > 0){
						$server = new dims_server();
						$server->open($id);
						$server->delete();
					}
					dims_redirect($dims->getScriptEnv());
					break;
			}

			break;
	}

	?>

</div>
