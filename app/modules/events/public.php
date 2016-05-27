<?php
/*
 *		Copyright 2010 Netlor SAS <contact@netlor.fr>
 *
 *		This program is free software; you can redistribute it and/or modify
 *		it under the terms of the GNU General Public License as published by
 *		the Free Software Foundation; either version 2 of the License, or
 *		(at your option) any later version.
 *
 *		This program is distributed in the hope that it will be useful,
 *		but WITHOUT ANY WARRANTY; without even the implied warranty of
 *		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *		GNU General Public License for more details.
 *
 *		You should have received a copy of the GNU General Public License
 *		along with this program; if not, write to the Free Software
 *		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

dims_init_module('events');

require_once DIMS_APP_PATH.'modules/events/classes/includes.php';
require_once DIMS_APP_PATH.'modules/system/include/business.php';

//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF'], '', '', '');
?>
<link type="text/css" rel="stylesheet" href="/common/modules/events/css/styles.css" media="screen" />
<div class="zone_title_events">
	<div class="title_events">
		<img src="/common/modules/events/img/mod32.png" border="0" /><h1><?php echo $_SESSION['cste']['_DIMS_EVENT_ADMIN_SELF']; ?></h1>
	</div>

</div>

<?php
require_once(DIMS_APP_PATH . '/include/functions/mail.php');
require_once(DIMS_APP_PATH . '/modules/events/include/global.php');

$action = null;

if (!isset($_SESSION['events']['action'])) $_SESSION['events']['action']='';
if (!isset($_SESSION['events']['submenu'])) $_SESSION['events']['submenu']='';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true,true);

//echo $_SESSION['events']['action']." ".$action;die();
//$ssubmenu = dims_load_securvalue('ssubmenu', dims_const::_DIMS_CHAR_INPUT, true,true,true,&$_SESSION['events']['submenu']);

$enabledAdminEvent =false;
$enableeventsteps = false;

$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
if ($currentworkspace['activeevent']) {
	$enabledAdminEvent=true;
}
if ($currentworkspace['activeeventstep']) {
	$enableeventsteps=true;
}

switch($action) {
	case 'updateEventSession':
		$id=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_contact=dims_load_securvalue('id_contact',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_etape=dims_load_securvalue('id_etape',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($id_etape==0) {
			if (isset($_SESSION['eventsaction']['events'][$id]['status'])) {
				$_SESSION['eventsaction']['events'][$id]['status']=!$_SESSION['eventsaction']['events'][$id]['status'];
			}
			else {
				$_SESSION['eventsaction']['events'][$id]['status']=1;
			}
		}
		else {
			// on est sur une entreprise
			if (isset($_SESSION['eventsaction']['events'][$id]['contacts'][$id_etape]['status'])) {
				$_SESSION['eventsaction']['events'][$id]['contacts'][$id_etape]['status']=!$_SESSION['eventsaction']['events'][$id]['contacts'][$id_etape]['status'];
			}
			else {
				$_SESSION['eventsaction']['events'][$id]['contacts'][$id_etape]['status']=1;
			}
		}
		die();
		break;
	case 'import_zip_form':
		require_once DIMS_APP_PATH . '/modules/events/public_events_convert_import_files.php';
		break;
	case 'import_zip_files':
		$id_event=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if (($enabledAdminEvent || $enableeventsteps) && (isset($_SESSION['dims']['eventsfilesla']) || isset($_SESSION['dims']['eventsfileodt'])) && $id_event>0) {
			if (isset($_FILES['importfile']) && !empty($_FILES['importfile'])) {
				$filetemp=$_FILES['importfile'];

				$extension = strtolower(substr(strrchr($_FILES['importfile']['name'], "."),1));
				if (isset($_SESSION['dims']['eventsfileodt']) && !empty($_SESSION['dims']['eventsfileodt'])) {
					$tabfilessources =$_SESSION['dims']['eventsfileodt'];
				}
				else {
					$tabfilessources = $_SESSION['dims']['eventsfilesla'];
				}
				$tabfilespdf = $_SESSION['dims']['eventsfilespdf'];

				// creation du zip
				ini_set('max_execution_time',0);
				ini_set('memory_limit',"1024M");
				if ($extension=="zip") {
					$zip_path = DIMS_TMP_PATH . '/filessla_'.$_SESSION['dims']['currentaction']."/";
					if (!is_dir($zip_path)) mkdir($zip_path);

					//$zip_file=
					$zip_downloadfile= DIMS_TMP_PATH . '/filessla_'.$_SESSION['dims']['currentaction']."/".$_SESSION['dims']['currentaction'].".zip";

					if (!move_uploaded_file($_FILES['importfile']['tmp_name'], $zip_downloadfile)) {
						echo "error";
					}
					$exec="unzip ".$zip_downloadfile." -d ".$zip_path;

					$tabres = array();
					$return=0;
					exec(escapeshellcmd($exec),$tabres,$return);

					// on boucle sur les fichiers pour trouver les pdf, pour chaque on regarde si il y en a un si oui on attache
					// si il existe deja un pdf, on delete et on remet le nouveau
					if (is_dir($zip_path) && is_dir($zip_path)) {
						if ($dh = opendir($zip_path)) {
							while (($filename = readdir($dh)) !== false) {
								if ($filename!="." && $filename!="..") {
									$extension = strtolower(substr(strrchr($filename, "."),1));
									if ($extension=="pdf") {
										$filename2=substr($filename,0,strlen($filename)-strlen($extension)-1);
										// on a un pdf,

										foreach ($tabfilessources as $k=>$file) {

											if ($file['name2']==$filename2) {
												//echo $file['name2']." ".$filename2."<br>";
												// on va regarder le contexte
												$id_etap=$file['id_etap'];
												$id_event=$file['id_event'];

												$found=false;
												// on regarde si on a pas deja un fichier du meme nom en PDF
												foreach ($tabfilespdf as $id_file_etap=>$file2) {

													if ($file2['name']==$filename) {
														// on supprime le fichier actuel
														$etapfile = new etap_file();
														$etapfile->open($id_file_etap);
														$etapfile->delete();
													}
												}

												// on ajoute maintenant le fichier dans l'étape
												$docfile = new docfile();
												$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
												$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
												$docfile->fields['id_folder'] = -1;
												$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
												$docfile->tmpuploadedfile = $zip_path.$filename;
												$docfile->fields['name'] = $filename;
												$docfile->fields['size'] = filesize($zip_path.$filename);
												$error = $docfile->save();

												$id_doc=$docfile->fields['id'];

												// on cr�� l'association entre le doc et l'etape
												$etap_file = new etap_file();
												$etap_file->fields['id_action']=$_SESSION['dims']['currentaction'];
												$etap_file->fields['id_etape']=$id_etap;
												$etap_file->fields['id_doc']=$id_doc;
												$etap_file->save();
											}
										}
									} // fin de extension
								} // . et ..
							} // boucle sur le repertoire
						}
					}

					/*foreach ($tabfilessla as $k=>$file) {
						if (file_exists($zip_path.$file['name'])) {
							unlink($zip_path.$file['name']);
						}
					}*/
					unlink($zip_downloadfile);
					dims_redirect("/admin.php?dims_mainmenu=events&submenu=8&dims_desktop=block&dims_action=public&action=add_evt&id=".$_SESSION['dims']['currentaction']);
					//die();
				}
				else {
					dims_redirect("/admin.php?dims_mainmenu=events&action=import_zip_form&id_event=".$_SESSION['dims']['currentaction']);
				}
			}
		}
		break;
	case 'export_sla_zip':
		ob_end_clean();
		$id_event=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if (($enabledAdminEvent || $enableeventsteps) && isset($_SESSION['dims']['eventsfilesla']) && $id_event>0) {
			$tabfilessla = $_SESSION['dims']['eventsfilesla'];

			// creation du zip
			ini_set('max_execution_time',0);
			ini_set('memory_limit',"2048M");

			$zip_path = DIMS_TMP_PATH . '/filessla_'.$_SESSION['dims']['currentaction']."/";
			if (!is_dir($zip_path)) mkdir($zip_path);
			else {
				dims_deletedir($zip_path);
				mkdir($zip_path);
			}
			$zip_file=$zip_path.$_SESSION['dims']['currentaction'].".zip";
			$zip_downloadfile= DIMS_TMP_PATH . '/filessla_'.$_SESSION['dims']['currentaction']."/".$_SESSION['dims']['currentaction'].".zip";

			foreach ($tabfilessla as $k=>$file) {
				//echo $file." ".$zip_path;
				if (file_exists($zip_path.$file['name'])) {
					unlink($zip_path.$file['name']);
				}

				copy($file['path'],$zip_path.$file['name']);

				$id_doc=$file['id_doc'];
				$id_event=$file['id_event'];
				$id_etap=$file['id_etap'];
				// on a le fichier, on remplace le contenu
				include DIMS_APP_PATH . '/modules/events/public_events_convert_sla_files.php';
			}



			$exec="zip -1 -j -r -D ".$zip_file." ".$zip_path;

			$tabres = array();
			$return=0;
			exec(escapeshellcmd($exec),$tabres,$return);

			if (file_exists($zip_file)) {
				dims_downloadfile($zip_file,$_SESSION['dims']['currentaction'].".zip",true, true);
			}
		}
		die();
		break;
	case 'export_odt_zip':
		ob_end_clean();
		$id_event=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if (($enabledAdminEvent || $enableeventsteps) && isset($_SESSION['dims']['eventsfileodt']) && $id_event>0) {
			$tabfilesodt = $_SESSION['dims']['eventsfileodt'];

			// creation du zip
			ini_set('max_execution_time',0);
			ini_set('memory_limit',"2048M");

			$zip_path = DIMS_TMP_PATH . '/filesodt_'.$_SESSION['dims']['currentaction']."/";
			if (!is_dir($zip_path)) mkdir($zip_path);
			else {
				dims_deletedir($zip_path);
				mkdir($zip_path);
			}
			$zip_file=$zip_path.$_SESSION['dims']['currentaction'].".zip";
			$zip_downloadfile= DIMS_TMP_PATH . '/filesodt_'.$_SESSION['dims']['currentaction']."/".$_SESSION['dims']['currentaction'].".zip";

			foreach ($tabfilesodt as $k=>$file) {
				//echo $file." ".$zip_path;
				if (file_exists($zip_path.$file['name'])) {
					unlink($zip_path.$file['name']);
				}

				copy($file['path'],$zip_path.$file['name']);

				$id_doc=$file['id_doc'];
				$id_event=$file['id_event'];
				$id_etap=$file['id_etap'];
				// on a le fichier, on remplace le contenu

				include DIMS_APP_PATH . '/modules/events/public_events_convert_odt_files.php';

				// on supprime le modele odt
				if (file_exists($zip_path.$file['name'])) {
					unlink($zip_path.$file['name']);
				}
			}



			$exec="zip -1 -j -r -D ".$zip_file." ".$zip_path;

			$tabres = array();
			$return=0;
			exec(escapeshellcmd($exec),$tabres,$return);

			if (file_exists($zip_file)) {
				dims_downloadfile($zip_file,$_SESSION['dims']['currentaction'].".zip",true, true);
			}
		}
		die();
		break;
	case 'export_sla':
				ob_end_clean();
				require_once DIMS_APP_PATH . '/modules/events/public_events_convert_sla.php';
				die();
				break;
	case 'view_events_summary':
		$ssubmenu = _DIMS_ADMIN_EVENTS_SUMMARY;
	break;
	case 'change_doc_recept_state':
	case 'view_admin_events':
		$ssubmenu = _DIMS_ADMIN_EVENTS_INSCR;
		break;
	case 'adm_insc':
	case 'adm_evt':
		$ssubmenu = _DIMS_VIEW_EVENTS_DETAILS;
		$_SESSION['events']['action']=$action;
		break;
	case 'view_models_events':
		$ssubmenu = _DIMS_ADMIN_FAIRS_MODELS;
		break;
	case 'view_old_events':
		$ssubmenu = _DIMS_ADMIN_OLD_EVENTS;
		break;
	case 'save_event':
	case 'add_evt':
		$ssubmenu = _DIMS_ADMIN_EVENTS_ADD;
		$_SESSION['events']['action']=$action;
		break;
	case 'list_events':
		$ssubmenu = _DIMS_VIEW_EVENTS_LIST;
		break;
	case 'view_admin_mails':
		$ssubmenu = _DIMS_ADMIN_EVENTS_MAILS;
		break;
	default:
		//cas des personnes n'ayant aucun droit de gestionhttp://www.moselle.gouv.fr/
		if($enabledAdminEvent == false && $enableeventsteps == false) {
			//on affiche par défaut la liste des events
			$ssubmenu = _DIMS_VIEW_EVENTS_LIST;
		}
		else {
			//on affiche le todo (summary)
			$ssubmenu = _DIMS_ADMIN_EVENTS_SUMMARY;
		}
	break;
}
//onglets
$tab_onglet = array();

if($enabledAdminEvent || $enableeventsteps) {
	//onglet todo
	$tab_onglet[_DIMS_ADMIN_EVENTS_SUMMARY]['title'] = $_DIMS['cste']['_DIMS_LABEL_VALIDATE_REGISTRATION'];
	$tab_onglet[_DIMS_ADMIN_EVENTS_SUMMARY]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_events_summary&ssubmenu="._DIMS_ADMIN_EVENTS_SUMMARY;
	$tab_onglet[_DIMS_ADMIN_EVENTS_SUMMARY]['icon'] = "./common/modules/events/img/tab_todo.png";
	$tab_onglet[_DIMS_ADMIN_EVENTS_SUMMARY]['width'] = 205;
	$tab_onglet[_DIMS_ADMIN_EVENTS_SUMMARY]['position'] = 'left';
}

//ongelt overview (visible par tous, juste visu, pas de droit de modif)
if (!$enableeventsteps	&& $enabledAdminEvent) {
	$tab_onglet[_DIMS_VIEW_EVENTS_LIST]['title'] = $_DIMS['cste']['_DIMS_LABEL_OVERVIEW'];
	$tab_onglet[_DIMS_VIEW_EVENTS_LIST]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=list_events&ssubmenu="._DIMS_VIEW_EVENTS_LIST;
	$tab_onglet[_DIMS_VIEW_EVENTS_LIST]['icon'] = "./common/img/view.png";
	$tab_onglet[_DIMS_VIEW_EVENTS_LIST]['width'] = 180;
	$tab_onglet[_DIMS_VIEW_EVENTS_LIST]['position'] = 'left';
}

if($enabledAdminEvent) {
	//onglet admin des events
	$tab_onglet[_DIMS_ADMIN_EVENTS_INSCR]['title'] = $_DIMS['cste']['_DIMS_LABEL_EVT_MANAGEMENT'];
	$tab_onglet[_DIMS_ADMIN_EVENTS_INSCR]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_INSCR;
	$tab_onglet[_DIMS_ADMIN_EVENTS_INSCR]['icon'] = "./common/modules/events/img/fairs.png";
	$tab_onglet[_DIMS_ADMIN_EVENTS_INSCR]['width'] = 195;
	$tab_onglet[_DIMS_ADMIN_EVENTS_INSCR]['position'] = 'left';

}

if ($enabledAdminEvent || $enableeventsteps) {
	$tab_onglet[_DIMS_ADMIN_EVENTS_ADD]['title'] = $_DIMS['cste']['_DIMS_PLANNING_ADD_EVT'];
	$tab_onglet[_DIMS_ADMIN_EVENTS_ADD]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=add_evt&ssubmenu="._DIMS_ADMIN_EVENTS_ADD."&type=".dims_const::_PLANNING_ACTION_EVT."&id=0";
	$tab_onglet[_DIMS_ADMIN_EVENTS_ADD]['icon'] = "./common/img/add.gif";
	$tab_onglet[_DIMS_ADMIN_EVENTS_ADD]['width'] = 180;
	$tab_onglet[_DIMS_ADMIN_EVENTS_ADD]['position'] = 'right';
}

if($enableeventsteps) {
	//onglet admin des foires
	/*$tab_onglet[_DIMS_ADMIN_OLD_EVENTS]['title'] = $_DIMS['cste']['_DIMS_LABEL_FAIRS_MANAGEMENT'];
	$tab_onglet[_DIMS_ADMIN_OLD_EVENTS]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_old_events&ssubmenu="._DIMS_ADMIN_OLD_EVENTS;
	$tab_onglet[_DIMS_ADMIN_OLD_EVENTS]['icon'] = "./common/modules/events/img/fairs.png";
	$tab_onglet[_DIMS_ADMIN_OLD_EVENTS]['width'] = 180;
	$tab_onglet[_DIMS_ADMIN_OLD_EVENTS]['position'] = 'left';
	*/
	//onglet admin des models
	$tab_onglet[_DIMS_ADMIN_FAIRS_MODELS]['title'] = $_DIMS['cste']['_DIMS_LABEL_FAIRS_MODELS_MGT'];
	$tab_onglet[_DIMS_ADMIN_FAIRS_MODELS]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_models_events&ssubmenu="._DIMS_ADMIN_FAIRS_MODELS;
	$tab_onglet[_DIMS_ADMIN_FAIRS_MODELS]['icon'] = "./common/modules/events/img/models.png";
	$tab_onglet[_DIMS_ADMIN_FAIRS_MODELS]['width'] = 175;
	$tab_onglet[_DIMS_ADMIN_FAIRS_MODELS]['position'] = 'right';

}

switch($action) {
	case 'adm_insc':
	case 'adm_evt':
		$tab_onglet[_DIMS_VIEW_EVENTS_DETAILS]['title'] = $_DIMS['cste']['_DIMS_LABEL_REGISTRATION'];
		$tab_onglet[_DIMS_VIEW_EVENTS_DETAILS]['url'] = "admin.php?action=adm_evt&id_evt=".dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$tab_onglet[_DIMS_VIEW_EVENTS_DETAILS]['icon'] = "./common/img/btn_edit.png";
		$tab_onglet[_DIMS_VIEW_EVENTS_DETAILS]['width'] = 130;
		$tab_onglet[_DIMS_VIEW_EVENTS_DETAILS]['position'] = 'left';
		break;

	case 'import_insc':
		$evt = new action();
		$evt->open(dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true, true, true));

		$link = 'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_PLANNING.'&dims_desktop=block&dims_action=public&op=xml_planning_modifier_action&id='.$evt->fields['id'];

		break;
}

if(($enabledAdminEvent || $enableeventsteps) && dims_ismanager()) {
	//menu pour la gestion des emails
	$tab_onglet[_DIMS_ADMIN_EVENTS_MAILS]['title'] = $_DIMS['cste']['_DIMS_ADMIN_EVENTS_MAILS'];
	$tab_onglet[_DIMS_ADMIN_EVENTS_MAILS]['url'] = "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_admin_mails&ssubmenu="._DIMS_ADMIN_EVENTS_MAILS;
	$tab_onglet[_DIMS_ADMIN_EVENTS_MAILS]['icon'] = "./common/img/configure.png";
	$tab_onglet[_DIMS_ADMIN_EVENTS_MAILS]['width'] = 200;
	$tab_onglet[_DIMS_ADMIN_EVENTS_MAILS]['position'] = 'right';
}


//echo $skin->create_onglet($tab_onglet,$ssubmenu,true,'0',"onglet");
if ($action != 'import_zip_form') {

?>
<div style="float:right;" class="sous_rubrique">
<ul>
	<?php
	foreach ($tab_onglet as $id =>$elem) {
		if ($id==$ssubmenu) $selected="class=\"selected\"";
		else $selected='';
		echo "<li><a ".$selected." href=\"".$elem['url']."\">".$elem['title'].'</a></li>';
	}
	?>
</ul>
</div>
<div style="clear:both;"></div>

<?php
	//echo $skin->create_onglet($tab_onglet,$ssubmenu,true,0,"onglet");
	//echo $skin->create_toolbar($tab_onglet,$ssubmenu);

	//echo $skin->close_toolbar();
//die($action);
	switch($action) {
			case 'view_admin_mails':
					require_once(DIMS_APP_PATH . "/modules/events/public_events_admin_mails.php");
			break;
			case 'save_workspace_events':
					$workspace_id = 0;
					$workspace_id = dims_load_securvalue('workspace_id', dims_const::_DIMS_NUM_INPUT, true, true);

					if($workspace_id > 0) {
							$work = new workspace();

							$work->open($workspace_id);
							$work->setvalues($_POST,'workspace_');
							$work->save();

							dims_redirect("admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_admin_mails&ssubmenu="._DIMS_ADMIN_EVENTS_MAILS);
					}
					else {
							echo "Error : data is missing";
					}
			break;
			case 'export_insc_evt':
					require_once(DIMS_APP_PATH . "/modules/events/public_events_export_insc.php");
			break;
		case 'view_details_evt':
			@ob_end_clean();
			ob_start();

					require_once(DIMS_APP_PATH . '/modules/system/class_action.php');

			$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true);

			$evt = new action();
			$evt->open($id_evt);

			$title = $_DIMS['cste']['_DIMS_LABEL_EVENT'].' : '.$evt->fields['libelle'];
					// A modifier car code en dur PAT_IMPORTANT
			echo $skin->open_simplebloc($title);
			$rootpath="";
			if ($evt->fields['host']!='') {
					$rootpath=$evt->fields['host'];
			}
			else {
					$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
					$rootpath=$dims->getProtocol().$http_host;
			}
			echo '<div style="overflow:auto;">
				   <iframe src="'.$rootpath.'/index.php?id_event='.$evt->fields['id'].'" style="border: 0pt none ; margin: 0pt; padding: 0pt; width: 100%; height: 800px;"></iframe>';
			//echo $evt->fields['description'];
			echo '<p><input type="button" value="'.$_DIMS['cste']['_DIMS_CLOSE'].'" onclick="javascript:dims_switchdisplay(\'view_details\');"/></p>';
			echo '</div>';
			echo $skin->close_simplebloc();

			ob_end_flush();
			die();
			break;

		case 'list_events':
			require_once(DIMS_APP_PATH . '/modules/events/public_events_watch.php');
			break;
			case 'add_evt':
					$_SESSION['dims']['currentaction'] = 0;
					require_once(DIMS_APP_PATH . '/modules/events/public_events_modifier_action.php');

					break;
			case 'view_models_events':
			case 'view_old_events':
		case 'view_admin_events':
					// filtre de securite
					if ($dims->isModuleTypeEnabled('events') && ($enabledAdminEvent || $enableeventsteps)) {
							require_once(DIMS_APP_PATH . '/modules/events/public_events_admin.php');
					}
					else {
							require_once(DIMS_APP_PATH . '/modules/events/public_events_watch.php');
					}
			break;

			case 'view_events_summary':
			default:
					// filtre de securite
					if ($dims->isModuleTypeEnabled('events') && ($enabledAdminEvent || $enableeventsteps)) {
							require_once(DIMS_APP_PATH . '/modules/events/public_events_summary.php');
					}
					else {
							require_once(DIMS_APP_PATH . '/modules/events/public_events_watch.php');
					}
			break;

			case 'model_admin_events':
			require_once(DIMS_APP_PATH . '/modules/events/public_events_model.php');
			break;

			case 'modeletap_admin_events':
					$id_evt_model = dims_load_securvalue('id_evt_model', dims_const::_DIMS_NUM_INPUT, true,true,0,$_SESSION['dims']['tmp_event_model']);
					if ($id_evt_model>0) {
							$_SESSION['dims']['tmp_event_model']=$id_evt_model;
							require_once(DIMS_APP_PATH . '/modules/events/public_events_model_etap.php');
					}
			break;
			case 'save_model_etap_events':
					if (isset($_SESSION['dims']['tmp_event_model']) && $_SESSION['dims']['tmp_event_model']>0) {
							// on peut enregistrer en ajout ou mise à jour
							$actionetap = new action_etap();

							if (isset($_SESSION['dims']['currentactionetap']) && $_SESSION['dims']['currentactionetap']>0) {
									$actionetap->open($_SESSION['dims']['currentactionetap']);
							}
							$actionetap->setvalues($_POST, "actionetap_");
							$actionetap->fields['id_model']=$_SESSION['dims']['tmp_event_model'];
							if (!isset($_SESSION['dims']['currentactionetap'])) {
									$res=$db->query("select * from dims_mod_business_event_etap where id_model= :idmodel",
													array(':idmodel' => $_SESSION['dims']['tmp_event_model'])
													);
									$position=$db->numrows($res);
									$actionetap->fields['position']=$position+1;
							}
							$actionetap->save();
					}
					dims_redirect("admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=modeletap_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL);
					break;

			case 'add_model_admin_events':
					$eventm=new event_model();
					$eventm->init_description();
					$_SESSION['dims']['tmp_event_model']=0;
					require_once(DIMS_APP_PATH . '/modules/events/public_events_model_form.php');
			break;

			case 'open_model_admin_events':
					$eventm=new event_model();
					$id_evt_model = dims_load_securvalue('id_evt_model', dims_const::_DIMS_NUM_INPUT, true);
					$_SESSION['dims']['tmp_event_model']=$id_evt_model;
					$eventm->open($_SESSION['dims']['tmp_event_model']);
					require_once(DIMS_APP_PATH . '/modules/events/public_events_model_form.php');
			break;

			case 'save_model_admin_events':
					$eventm=new event_model();
					$isnew=true;
					if ($_SESSION['dims']['tmp_event_model']>0) {
							$eventm->open($_SESSION['dims']['tmp_event_model']);
							$isnew=false;
					}
					else {
							$eventm->setugm();
					}
					$eventm->setvalues($_POST, "event_model_");

					// verification si nom existe deja
					$sql = 'SELECT count(id) as cpte FROM dims_mod_business_event_model WHERE label = :label';


			$ress = $db->query($sql, array(':label' => dims_sql_filter($eventm->fields['label']) ));

			$t = $db->fetchrow($ress);
					if ($t['cpte']>0) {
							$eventm->fields['label'].="_".($t['cpte']+1);
					}

					$eventm->save();

					$_SESSION['dims']['tmp_event_model']=$eventm->fields['id'];

					if (!$isnew) {
							// on redirige vers la liste des modeles
							unset($_SESSION['dims']['tmp_event_model']);
							dims_redirect("admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=model_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL);
					}
					else {
							// on redirige vers la liste des etapes model
							dims_redirect("admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=modeletap_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL);
					}

			break;

			case 'delete_model_admin_events':
					// verification si events utilisent ou non
					$eventm=new event_model();
					$id_evt_model = dims_load_securvalue('id_evt_model', dims_const::_DIMS_NUM_INPUT, true);
					if ($id_evt_model>0) {
							$eventm->open($id_evt_model);
							$eventm->delete();
					}
					dims_redirect("admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=model_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL);
			break;

		case 'open_inscription' :
			case 'close_inscription' :

					$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true);

					$evt = new action();
					$evt->open($id_evt);

					if($action == 'close_inscription') {
							$evt->fields['close'] = 1;
					}
					else {
							$evt->fields['close'] = 0;
					}

					$evt->save();

					break;

			case 'unvalid_niv2':
			case 'valid_niv2':
					//require_once(DIMS_APP_PATH . '/modules/system/class_inscription.php');
					//require_once(DIMS_APP_PATH . '/modules/system/class_action.php');

					$id_insc = dims_load_securvalue('id_insc', dims_const::_DIMS_NUM_INPUT, true);

					$inscription = new inscription();
					$inscription->open($id_insc);

			$contact = new contact();
			$contact->open($inscription->fields['id_contact']);

			$event = new action();
			$event->open($inscription->fields['id_action']);

					if($action == 'valid_niv2') {
							$inscription->fields['validate'] = 2;
							$inscription->fields['date_validate'] = date('YmdHis');

				//mail
				$from	= array();
				$to		= array();
				$subject= '';
				$message= '';

				$to[0]['name']	   = $contact->fields['lastname'].' '.$contact->fields['firstname'];
				$to[0]['address']  = $contact->fields['email'];

							$work = new workspace();
							$work->open($_SESSION['dims']['workspaceid']);
							$email = $work->fields['email'];
							if ($email=="") $email=_DIMS_ADMINMAIL;

							$from[0]['name'] = '';
							$from[0]['address'] = $email;
							//$from[0]['name']	 = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
							//$from[0]['address']= $_SESSION['dims']['user']['email'];

							$rootpath="";
							if ($event->fields['host']!='') {
									$rootpath=$event->fields['host'];
							}
							else {
									$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
									$rootpath=$dims->getProtocol().$http_host;
							}

							$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$event->fields['id'],true).'">link</a>';

							$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{filename}', '{unvalid_content}');
							$tab_val = array($contact->fields['firstname'],$contact->fields['lastname'],$event->fields['libelle'],$contact->fields['country'],'2', $micro_link, '', '');

							$subject_br = $work->fields['events_mail10_subject'];
							$mail_brouil = $work->fields['events_mail10_content'];

							//on fait le remplacement des tags
							$subject = str_replace($tab_rep, $tab_val, $subject_br);
							$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
							$mail_content .= '<br/><br/>';
							if($work->fields['events_signature'] != '') {
									$mail_content .= $work->fields['events_signature'];
							}
							elseif($work->fields['signature'] != '') {
									$mail_content .= $work->fields['signature'];
							}

				dims_send_mail($from,$to, $subject, nl2br($mail_content));

					}
					else {
							$inscription->fields['validate'] = 1;
							$inscription->fields['date_validate'] = '';
					}

					$inscription->save();

					break;
			case 'valid_paiement':
					//require_once(DIMS_APP_PATH . '/modules/system/class_inscription.php');

					$id_insc = dims_load_securvalue('id_insc', dims_const::_DIMS_NUM_INPUT, true);
					$id_etape = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true);
					$date_f = dims_load_securvalue('date_f', dims_const::_DIMS_CHAR_INPUT, true);
					$date_p = dims_load_securvalue('date_p', dims_const::_DIMS_CHAR_INPUT, true);
					$date_j = date("Ymd")."000000";

					$inscription = new inscription();
					$inscription->open($id_insc);

					if($date_f != "jj/mm/aaaa") {
							$inscription->fields['date_facturation'] = dims_local2timestamp($date_f);
							if($date_f != "")
									$db->query("UPDATE dims_mod_business_event_etap_user SET valide_etape = '1' WHERE id_ee_contact = :idcontact AND id_etape = :idetape",
												array(':idcontact' => $inscription->fields['id_contact'], ':idetape' => $id_etape)
											);
							else
									$db->query("UPDATE dims_mod_business_event_etap_user SET valide_etape = '0' WHERE id_ee_contact = :idcontact AND id_etape = :idetape",
												array(':idcontact' => $inscription->fields['id_contact'], ':idetape' => $id_etape)
											);
					}
					if($date_p != "jj/mm/aaaa") {
							$inscription->fields['date_paiement'] = dims_local2timestamp($date_p);
							if($date_p != "") {
									$db->query("UPDATE dims_mod_business_event_etap_user SET valide_etape = '2' WHERE id_ee_contact = :idcontact AND id_etape = :idetape",
												array(':idcontact' => $inscription->fields['id_contact'], ':idetape' => $id_etape)
											);
									$db->query("UPDATE dims_mod_business_event_etap_user SET date_validation_etape = '".$date_j."' WHERE id_ee_contact = {$inscription->fields['id_contact']} AND id_etape = $id_etape");
									$inscription->fields['paiement'] = 1;
							}
							elseif($date_f != "" && $date_f != "jj/mm/aaaa") {
									$db->query("UPDATE dims_mod_business_event_etap_user SET valide_etape = '1' WHERE id_ee_contact = :idcontact AND id_etape = :idetape",
												array(':idcontact' => $inscription->fields['id_contact'], ':idetape' => $id_etape)
											);
									$inscription->fields['paiement'] = 0;
							}
							else {
									$db->query("UPDATE dims_mod_business_event_etap_user SET valide_etape = '0' WHERE id_ee_contact = :idcontact AND id_etape = :idetape",
												array(':idcontact' => $inscription->fields['id_contact'], ':idetape' => $id_etape)
											);
									$inscription->fields['paiement'] = 0;
							}
					}

					$inscription->save();

					break;
			case 'unvalid_paiement':
					//require_once(DIMS_APP_PATH . '/modules/system/class_inscription.php');

					$id_insc = dims_load_securvalue('id_insc', dims_const::_DIMS_NUM_INPUT, true);

					$inscription = new inscription();
					$inscription->open($id_insc);

					$inscription->fields['paiement'] = 0;

					$inscription->save();

					break;
			case 'unvalid_document' :

					$id_file = dims_load_securvalue('id_file', dims_const::_DIMS_NUM_INPUT, true);

					$file = new etap_file_ct();
					$file->open($id_file);

			$etape = new action_etap();
			$etape->open($file->fields['id_etape']);

			$contact = new contact();
			$contact->open($file->fields['id_contact']);

			$doc = new docfile();
			$doc->open($file->fields['id_doc']);

					$file->fields['valide'] = 0;
					$file->fields['date_validation'] = '';
					$file->save();

			$to = array();
			$from = array();
			$subject = '';
			$message = '';

					break;
			case 'valid_document' :

					$date_v = null;

					$id_file = dims_load_securvalue('id_file', dims_const::_DIMS_NUM_INPUT, true);
					$date_v = dims_load_securvalue('date_valid', dims_const::_DIMS_CHAR_INPUT, true, false,true, $date_v, dims_getdate());

					$file = new etap_file_ct();
					$file->open($id_file);

					$etape = new action_etap();
					$etape->open($file->fields['id_etape']);

					$contact = new contact();
					$contact->open($file->fields['id_contact']);

					$doc = new docfile();
					$doc->open($file->fields['id_doc']);

					$file->fields['date_validation'] = dims_local2timestamp($date_v);
					$file->fields['valide'] = 1;
					$file->save();

					// test si on a maintenant l'étape validée ou non
					$result=$file->checkValidEtap();

					if ($result) {
						// on valide automatiquement l'etape
						// recherche de l'étape liée à l'utilisateur
						$id_etapuser=$etape->getEtapeFromContact($file->fields['id_contact']);

						if ($id_etapuser>0) {
							$etape_user = new action_etap_ct();
							$etape_user->open($id_etapuser);
							$etape_user->fields['valide_etape'] = 2;
							$etape_user->fields['date_validation_etape'] = date('YmdHis');
							$etape_user->save();

							$work = new workspace();
							$work->open($_SESSION['dims']['workspaceid']);

							$rootpath="";
							if ($evt->fields['host']!='') {
									$rootpath=$evt->fields['host'];
							}
							else {
									$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
									$rootpath=$dims->getProtocol().$http_host;
							}
							$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$etape->fields['id_action'],true).'">link</a>';

							$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{filename}', '{unvalid_content}', '{step_label}');
							$tab_val = array($contact->fields['firstname'],$contact->fields['lastname'],$evt->fields['libelle'],$contact->fields['country'],'2', $micro_link, '', '', $etape->fields['label']);

							$subject_br = $work->fields['events_mail8_subject'];
							$mail_brouil = $work->fields['events_mail8_content'];

							$from	= array();
							$to		= array();
							$subject= '';
							$message= '';

							//on fait le remplacement des tags
							$subject = str_replace($tab_rep, $tab_val, $subject_br);
							$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
							$mail_content .= '<br/><br/>';
							if($work->fields['events_signature'] != '') {
									$mail_content .= $work->fields['events_signature'];
							}
							elseif($work->fields['signature'] != '') {
									$mail_content .= $work->fields['signature'];
							}

							$to[0]['name']	   = $contact->fields['lastname'].' '.$contact->fields['firstname'];
							$to[0]['address']  = $contact->fields['email'];

							$cc[0]['name']	   = 'patrick';
							$cc[0]['address']  = 'patrick@netlor.fr';
							$cc[1]['name']	   = 'Andre Hansen';
							$cc[1]['address']  = 'Andre.Hansen@eco.etat.lu';

							$from[0]['name']   = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
							$from[0]['address']= $_SESSION['dims']['user']['email'];

							dims_send_mail($from,$to, $subject, nl2br($mail_content),$cc);

							// on redirige sur la validation de l'etape
							//dims_redirect("/admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&action=valid_etape&id_etape=".$id_etapuser);
						}
					}
					break;
			case 'change_doc_recept_state':

					$id_file = dims_load_securvalue('id_file', dims_const::_DIMS_NUM_INPUT, true);
					$cancel_content = dims_load_securvalue('content', dims_const::_DIMS_CHAR_INPUT, false, true, false);
					$cancel_content = nl2br($cancel_content);

					$file_ct = new etap_file_ct();
					$file_ct->open($id_file);

					$doc = new docfile();
					$doc->open($file_ct->fields['id_doc']);

					$doc_user = new docfile();
					$doc_user->open($file_ct->fields['id_doc_frontoffice']);

					$contact = new contact();
					$contact->open($file_ct->fields['id_contact']);

					$evt = new action();
					$evt->open($file_ct->fields['id_action']);

					/*$etape = new action_etap();
					$etape->open($etape_user->fields['id_etape']);*/

					$sql = 'SELECT id, host FROM dims_mod_business_event_inscription WHERE id_contact = :idcontact AND id_action = :idaction';

					$ress = $db->query($sql,array(':idcontact' => $file_ct->fields['id_contact'], ':idaction' => $file_ct->fields['id_action']));

					$t = $db->fetchrow($ress);

					// mail
					$from	= array();
					$to		= array();
					$subject= '';
					$message= '';

					$to[0]['name']	   = $contact->fields['lastname'].' '.$contact->fields['firstname'];
					$to[0]['address']  = $contact->fields['email'];

					$work = new workspace();
					$work->open($_SESSION['dims']['workspaceid']);
					$email = $work->fields['email'];
					if ($email=="") $email=_DIMS_ADMINMAIL;

					$from[0]['name'] = '';
					$from[0]['address'] = $email;
			//$from[0]['name']	 = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
			//$from[0]['address']= $_SESSION['dims']['user']['email'];

					$rootpath="";
					if ($evt->fields['host']!='') {
							$rootpath=$evt->fields['host'];
					}
					else {
							$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
							$rootpath=$dims->getProtocol().$http_host;
					}

					$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$file_ct->fields['id_action'],true).'">link</a>';

					$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{filename}', '{unvalid_content}');
					$tab_val = array($contact->fields['firstname'],$contact->fields['lastname'],$evt->fields['libelle'],$contact->fields['country'],'2', $micro_link, $doc->fields['name'], $cancel_content);

					$subject_br = $work->fields['events_mail7_subject'];
					$mail_brouil = $work->fields['events_mail7_content'];

					//on fait le remplacement des tags
					$subject = str_replace($tab_rep, $tab_val, $subject_br);
					$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
					$mail_content .= '<br/><br/>';
					if($work->fields['events_signature'] != '') {
							$mail_content .= $work->fields['events_signature'];
					}
					elseif($work->fields['signature'] != '') {
							$mail_content .= $work->fields['signature'];
					}

			//$subject = 'Sorry, we can not proceed your document '.$doc->fields['name'].' for '.$evt->fields['libelle'];
			//
			//$content = 'Dear '.$contact->fields['firstname'].' '.$contact->fields['lastname'].',<br /><br />';
			//$content.= 'Sorry,<br />';
			//$content.= 'The document '.$doc->fields['name'].' you have upload cannot be proceeded.<br /><br />';
			//$content.= 'Reason :<br />';

			if(!empty($cancel_content)) {
				//$content.= $cancel_content.'<br />';
				$file_ct->fields['invalid_content'] = $cancel_content;
			}

	//		$rootpath="";
	//		$rootpath="https://";
	//		$rootpath.=$_SERVER['HTTP_HOST'];
	//
	//		  $content.= 'Please add the required chanes, and upload your document(s) again. ';
	//		  $content.= '<br /><a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$evt->fields['id'],true).'">link</a><br /><br />';
	//		  $content .= 'Best regards,';
	//		$content.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);

			dims_send_mail($from,$to, $subject, nl2br($mail_content));

					if($evt->fields['typeaction'] != '_DIMS_PLANNING_FAIR') {
							$doc_user->delete();

							$file_ct->fields['date_reception'] = 0;
							$file_ct->fields['valide'] = 0;
							$file_ct->fields['id_doc_frontoffice'] = 0;
					}
					else {
							$file_ct->fields['valide'] = -1;
					}

					$file_ct->save();

			dims_redirect('admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_insc&id_evt='.$file_ct->fields['id_action'].'&id_insc='.$t['id']);

					break;
			case 'valid_doc_recept':

			$date_r = null;

					$id_file = dims_load_securvalue('id_file', dims_const::_DIMS_NUM_INPUT, true);
					$date_r = dims_load_securvalue('date_recept', dims_const::_DIMS_CHAR_INPUT, true, false, true, $date_r, dims_getdate());

					$file = new etap_file_ct();
					$file->open($id_file);

					$file->fields['date_reception'] = dims_local2timestamp($date_r);
					$file->save();

					break;
			case 'save_doc_prov':

					$id_file = dims_load_securvalue('id_file', dims_const::_DIMS_NUM_INPUT, true);
					$val = dims_load_securvalue('val', dims_const::_DIMS_CHAR_INPUT, true);

					$file = new etap_file_ct();
					$file->open($id_file);

					$file->fields['provenance'] = $val;
			//dims_print_r($file->fields);
					$file->save();

					break;

		case 'message_annule_doc':
					@ob_end_clean();
			ob_start();

			$id_file = dims_load_securvalue('id_file', dims_const::_DIMS_NUM_INPUT, true);

					$etape_user = new action_etap_ct();
					$etape_user->open($id_file);

			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ADD_MESSAGE']);

			echo '<form name="etape_event" action="admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=change_doc_recept_state&id_file='.$id_file.'" method="post">';
			echo '<p>'.$_DIMS['cste']['_DIMS_CONTENT_EXPLAIN_OPTIONAL'].'</p>';
			echo '<p><textarea name="content" style="width:530px" rows="5">';
			if(!empty($etape_user->fields['invalid_content']))
				echo str_replace('<br />','', $etape_user->fields['invalid_content']);
			echo '</textarea></p>';
			echo '<p style="margin: 0px 0px 0px 0px;">';
			echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png','javascript:document.etape_event.submit();','','');
					echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/undo.gif','javascript:document.getElementById(\'popup_valid\').innerHTML=\' \';','','');
					echo '</p>&nbsp;';
					echo '</form>';

			echo $skin->close_simplebloc();

			ob_end_flush();
			die();
			break;

			case 'annule_etape_fairs':

							$id_etape = dims_load_securvalue('id_etape', dims_const::_DIMS_NUM_INPUT, true);

							$etape_user = new action_etap_ct();
							$etape_user->open($id_etape);

							$etape_user->fields['valide_etape'] = -1;
							$etape_user->fields['date_validation_etape'] = '';

							$etape_user->save();
					break;
			case 'valid_etape_fairs':

					$id_etape = dims_load_securvalue('id_etape', dims_const::_DIMS_NUM_INPUT, true);

					$etape_user = new action_etap_ct();
					$etape_user->open($id_etape);

					$etape_user->fields['valide_etape'] = 2;
					$etape_user->fields['date_validation_etape'] = date('YmdHis');

					$etape_user->save();

					break;
			case 'annule_etape':
			case 'valid_etape':

					$id_etape = dims_load_securvalue('id_etape', dims_const::_DIMS_NUM_INPUT, true);

					$etape_user = new action_etap_ct();
					$etape_user->open($id_etape);

					$etape = new action_etap();
					$etape->open($etape_user->fields['id_etape']);

					$evt = new action();
					$evt->open($etape->fields['id_action']);

					$contact = new contact();
					$contact->open($etape_user->fields['id_ee_contact']);


					$sql = 'SELECT
						u.login,
						ei.host,
						ei.id AS id_inscrip
					FROM
						dims_user u
					INNER JOIN
						dims_mod_business_event_inscription ei
						ON
							ei.id_contact = u.id_contact
						AND
							ei.id_action = :idaction
					WHERE
						u.id_contact = :idcontact
					LIMIT 1';

			$result = $db->query($sql, array(':idaction' => $etape->fields['id_action'], ':idcontact' => $etape_user->fields['id_ee_contact']) );
			$user = $db->fetchrow($result);

			//mail
			$from	= array();
			$to		= array();
			$subject= '';
			$message= '';

			$to[0]['name']	   = $contact->fields['lastname'].' '.$contact->fields['firstname'];
			$to[0]['address']  = $contact->fields['email'];

			$from[0]['name']   = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
			$from[0]['address']= $_SESSION['dims']['user']['email'];

			$cc[0]['name']	   = 'patrick';
			$cc[0]['address']  = 'patrick@netlor.fr';
			$cc[1]['name']	   = 'Andre Hansen';
			$cc[1]['address']  = 'Andre.Hansen@eco.etat.lu';

			if($action == 'valid_etape') {
					$etape_user->fields['valide_etape'] = 2;
					$etape_user->fields['date_validation_etape'] = date('YmdHis');

					$work = new workspace();
					$work->open($_SESSION['dims']['workspaceid']);

					$rootpath="";
					if ($evt->fields['host']!='') {
							$rootpath=$evt->fields['host'];
					}
					else {
							$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
							$rootpath=$dims->getProtocol().$http_host;
					}
					$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$etape->fields['id_action'],true).'">link</a>';

					$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{filename}', '{unvalid_content}', '{step_label}');
					$tab_val = array($contact->fields['firstname'],$contact->fields['lastname'],$evt->fields['libelle'],$contact->fields['country'],'2', $micro_link, '', '', $etape->fields['label']);

					$subject_br = $work->fields['events_mail8_subject'];
					$mail_brouil = $work->fields['events_mail8_content'];

					//on fait le remplacement des tags
					$subject = str_replace($tab_rep, $tab_val, $subject_br);
					$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
					$mail_content .= '<br/><br/>';
					if($work->fields['events_signature'] != '') {
							$mail_content .= $work->fields['events_signature'];
					}
					elseif($work->fields['signature'] != '') {
							$mail_content .= $work->fields['signature'];
					}

					dims_send_mail($from,$to, $subject, nl2br($mail_content),$cc);

					$etape_user->save();
			}
			else {
				$etape_user->fields['valide_etape'] = -1;
				$etape_user->fields['date_validation_etape'] = '';

				$work = new workspace();
				$work->open($_SESSION['dims']['workspaceid']);

				$rootpath="";
				if ($evt->fields['host']!='') {
						$rootpath=$evt->fields['host'];
				}
				else {
						$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
						$rootpath=$dims->getProtocol().$http_host;
				}
				$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$etape->fields['id_action'],true).'">link</a>';

				$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{filename}', '{unvalid_content}');
				$tab_val = array($contact->fields['firstname'],$contact->fields['lastname'],$evt->fields['libelle'],$contact->fields['country'],'2', $micro_link, '', '');

				$subject_br = $work->fields['events_mail9_subject'];
				$mail_brouil = $work->fields['events_mail9_content'];

				//on fait le remplacement des tags
				$subject = str_replace($tab_rep, $tab_val, $subject_br);
				$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
				$mail_content .= '<br/><br/>';
				if($work->fields['events_signature'] != '') {
						$mail_content .= $work->fields['events_signature'];
				}
				elseif($work->fields['signature'] != '') {
						$mail_content .= $work->fields['signature'];
				}

				$content.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);

				dims_send_mail($from,$to, $subject, nl2br($mail_content));

				$etape_user->save();

				dims_redirect('admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_insc&id_evt='.$etape->fields['id_action'].'&id_insc='.$user['id_inscrip']);
			}
			break;

			case 'verif_valid':
				@ob_end_clean();
				ob_start();

				/* Popup de verification de validation
				  Dans le cas de la crÃ©ation d'un user/contact :
					- VÃ©rification des existants (Levenshtein)
					- Proposition de rattachement ou CrÃ©ation
				  Permet l'annulation d'un "mauvais clique"
				*/

				$id_evt = 0;
				$id_insc= 0;

				$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true);
				$id_insc= dims_load_securvalue('id_inscrip', dims_const::_DIMS_NUM_INPUT, true);

				$sql = 'SELECT
							a.niveau,
							ei.*
						FROM
							dims_mod_business_action a
						INNER JOIN
							dims_mod_business_event_inscription ei
							ON
								ei.id_action = a.id
						WHERE
							a.id = :idevt

						AND
							ei.id = :idinsc';


				$ressource = $db->query($sql, array(':idevt' => $id_evt, ':idinsc' => $id_insc) );

				if($db->numrows($ressource) == 1) {
					$result = $db->fetchrow($ressource);


					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_VALID_REGISTER'].' : '.$result['lastname'].' '.$result['firstname']);
					echo '<div style="max-height: 350px; overflow: auto;background-color:#FFFFFF;">
							<form name="etape_event" action="admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=valide_insc&id_evt='.$id_evt.'&id_inscrip='.$id_insc.'" method="post">';

						if($result['validate'] == -1) {
							echo '<p>'.$_DIMS['cste']['_DIMS_LABEL_ADMIN_INSC_ANNULE'].'</p>';
							echo '<p style="float: left;">';
													echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png','javascript:document.etape_event.submit();','','');
													echo '</p>&nbsp;';
						}
						elseif($result['validate'] == 2) {
							echo '<p>'.$_DIMS['cste']['_DIMS_LABEL_INSCR_ALREADY_VALID'].'</p>';
						}
						else {
							if($result['niveau'] == 1 && empty($result['id_contact'])) {
								$tab_corresp = array();

								//Recherche d'un contact similaire
								$sql = 'SELECT
											ct.id as id_contact,
											ct.lastname,
											ct.firstname
										FROM
											dims_mod_business_contact ct';

								$ress = $db->query($sql);

								if($db->numrows($ress) > 0) {
									$nom	= strtoupper($result['lastname']);
									$prenom = strtoupper($result['firstname']);

									while($rslt = $db->fetchrow($ress)) {

										$lev_nom = 0;
										$lev_pre = 0;

										$coef_nom = 0;
										$coef_pre = 0;

										$coef_tot = 0;

										$lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
										$coef_nom = $lev_nom - (ceil(strlen($nom)/4));

										$lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
										$coef_pre = $lev_pre - (ceil(strlen($prenom)/4));

										$coef_tot = $coef_nom + $coef_pre;

										$lev_nom2 = 0;
										$lev_pre2 = 0;

										$coef_nom2 = 0;
										$coef_pre2 = 0;

										$coef_tot2 = 0;

										$lev_nom2 = levenshtein($nom, strtoupper($rslt['firstname']));
										$coef_nom2 = $lev_nom2 - (ceil(strlen($nom)/4));

										$lev_pre2 = levenshtein($prenom, strtoupper($rslt['lastname']));
										$coef_pre2 = $lev_pre2 - (ceil(strlen($prenom)/4));

										$coef_tot2 = $coef_nom2 + $coef_pre2;

										if($coef_tot < 4 || $coef_tot2 < 4) {
											$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
											$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
											$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
											$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
										}

									}
									sort($tab_corresp);
								}

								echo '<p>';
									if(count($tab_corresp) > 0)
									{
										echo $_DIMS['cste']['_DIMS_LABEL_ASK_FOR_ATTACH']."<br>";

										foreach($tab_corresp as $corresp)
										{
											echo '<input type="radio" name="user_rattach" value="'.$corresp['id_contact'].'" />';
											echo $corresp['lastname'].' '.$corresp['firstname'].'&nbsp;';
											echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$corresp['id_contact'].'" target="_BLANK">';
											echo $_DIMS['cste']['_DIMS_LABEL_SEE_CONTACT_SHEET'].'</a>.<br />';
										}

										echo '<font style="font-weight:bold;">'.$_DIMS['cste']['_DIMS_OR'].' :</font><br />';
									}
									else
										echo $_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR_CONTACT'];


									echo '<input type="radio" name="user_rattach" value="-1" />'.$_DIMS['cste']['_DIRECTORY_ADDNEWCONTACT'].'<br />';
									echo '<font style="font-weight:bold;">'.$_DIMS['cste']['_DIMS_OR'].' :</font><br />';
									echo '<input type="radio" name="user_rattach" value="0" checked="checked" />'.$_DIMS['cste']['_DIMS_LABEL_NOT_DO_SHEET'].'<br />';
								echo '</p>';
								echo '<p style="float: left;">';
															echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png','javascript:document.etape_event.submit();','','');
															echo '</p>&nbsp;';

							}
							elseif($result['niveau'] == 1 && !empty($result['id_contact'])) {
								echo '<input type="hidden" name="user_rattach" value="'.$result['id_contact'].'"/>
																			<p>'.$_DIMS['cste']['_DIMS_LABEL_SHEET_EXIST'].'<br /></p>';
								echo '<p style="float: left;">';
															echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png','javascript:document.etape_event.submit();','','');
															echo '</p>&nbsp;';
							}
							elseif($result['niveau'] == 2) {

								if($result['validate'] == 0 && ($result['id_contact'] == '' || $result['id_contact'] == 0)) {
									$tab_corresp = array();

									//Recherche d'un contact similaire
									$sql = 'SELECT
												u.id,
												u.id_contact,
												ct.lastname,
												ct.firstname
											FROM
												dims_user u
											INNER JOIN
												dims_mod_business_contact ct
												ON
													ct.id = u.id_contact';

									$ress = $db->query($sql);

									if($db->numrows($ress) > 0)
									{
										$nom	= strtoupper($result['lastname']);
										$prenom = strtoupper($result['firstname']);

										while($rslt = $db->fetchrow($ress)) {

											$lev_nom = 0;
											$lev_pre = 0;

											$coef_nom = 0;
											$coef_pre = 0;

											$coef_tot = 0;

											$lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
											$coef_nom = $lev_nom - (ceil(strlen($nom)/4));

											$lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
											$coef_pre = $lev_pre - (ceil(strlen($prenom)/4));

											$coef_tot = $coef_nom + $coef_pre;

																					$lev_nom2 = 0;
																					$lev_pre2 = 0;

																					$coef_nom2 = 0;
																					$coef_pre2 = 0;

																					$coef_tot2 = 0;

																					$lev_nom2 = levenshtein($nom, strtoupper($rslt['firstname']));
																					$coef_nom2 = $lev_nom2 - (ceil(strlen($nom)/4));

																					$lev_pre2 = levenshtein($prenom, strtoupper($rslt['lastname']));
																					$coef_pre2 = $lev_pre2 - (ceil(strlen($prenom)/4));

																					$coef_tot2 = $coef_nom2 + $coef_pre2;

																					if($coef_tot < 4 || $coef_tot2 < 4) {

												$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
												$tab_corresp[$rslt['id_contact']]['id_user']	= $rslt['id'];
												$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
												$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
												$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
											}

										}
										sort($tab_corresp);
									}

									echo '<p>';
										if(count($tab_corresp) > 0)
										{
											echo $_DIMS['cste']['_DIMS_LABEL_ASK_FOR_ATTACH'];

											foreach($tab_corresp as $corresp)
											{
												echo '<input type="radio" name="user_rattach" value="'.$corresp['id_user'].'" />';
												echo $corresp['lastname'].' '.$corresp['firstname'].'&nbsp;';
												echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&dims_desktop=block&dims_action=public&contact_id='.$corresp['id_contact'].'" target="_BLANK">';
												echo $_DIMS['cste']['_DIMS_LABEL_SEE_CONTACT_SHEET'].'</a>.<br />';
											}
											echo $_DIMS['cste']['_DIMS_OR'].' :<br />';
																			}
																			else
																					echo $_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR_CONTACT'];


										echo '<input type="radio" name="user_rattach" value="-1" checked="checked" />'.$_DIMS['cste']['_DIRECTORY_ADDNEWCONTACT'].'<br />';
									echo '</p>';
									echo '<p style="float: left;"><input type="submit" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_VALID'].'" /></p>&nbsp;';

								}
								elseif($result['validate'] == 0 && !empty($result['id_contact']))
								{
									echo '<input type="hidden" name="user_rattach" value="'.$result['id_contact'].'"/>
																					<p>'.$_DIMS['cste']['_DIMS_LABEL_SHEET_EXIST'].'<br /></p>';
									echo '<p style="float: left;">';
																	echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png','javascript:document.etape_event.submit();','','');
																	echo '</p>&nbsp;';
								}
								elseif($result['validate'] == 1)
								{
									echo '<p>'.$_DIMS['cste']['_DIMS_LABEL_CONFIRM_NIV2'].'<b>'.strtoupper(substr($result['firstname'],0,1)).'. '.$result['lastname'].'</b></p>';
									echo '<p style="float: left;">';
																	echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png','javascript:document.etape_event.submit();','','');
																	echo '</p>&nbsp;';
								}
							}
						}

						//Bouton de fermeture de la popup
						echo '<p style="margin: 0px 0px 0px 50%;"><input type="button" onclick="javascript:document.getElementById(\'popup_valid\').innerHTML=\' \';" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" class="flatbutton"/></a>';

					echo '</form>
						</div>';
					echo $skin->close_simplebloc();
				}
			ob_end_flush();
			die();
			break;
		case 'cancel_insc':
		case 'valide_insc':

			switch($action) {
				case 'cancel_insc':
									$id_evt = 0;
					$id_insc= 0;

					$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true);
					$id_insc= dims_load_securvalue('id_insc', dims_const::_DIMS_NUM_INPUT, true);

					$sql = 'SELECT
								a.libelle,
								a.niveau,
								ei.*
							FROM
								dims_mod_business_action a
							INNER JOIN
								dims_mod_business_event_inscription ei
								ON
									ei.id_action = a.id
							WHERE
								a.id = :idevt

							AND
								ei.id = :id_insc';
													/*
													 * AND
								(
									a.id_user = '.$_SESSION['dims']['userid'].'
								OR
									a.id_organizer = '.$_SESSION['dims']['user']['id_contact'].'
								OR
									a.id_responsible = '.$_SESSION['dims']['user']['id_contact'].'
								)
													 */

					$ressource = $db->query($sql, array(':idevt' => $id_evt, ':idinsc' => $id_insc) );

					if($db->numrows($ressource) == 1) {
						$result = $db->fetchrow($ressource);

						if($result['validate'] != -1) {
							$sql = 'UPDATE dims_mod_business_event_inscription SET validate=-1 WHERE id= :idinsc';

							$db->query($sql, array(':idinsc' => $id_insc) );

							//mail
							$from	= array();
							$to		= array();
							$subject= '';
							$message= '';
													$work = new workspace();
													$work->open($_SESSION['dims']['workspaceid']);

													$email = $work->fields['events_sender_email'];
													if ($email=="") $email=_DIMS_ADMINMAIL;

							$from[0]['name']   = '';
							$from[0]['address']= $email;

													$to[0]['name']	   = $result['lastname'].' '.$result['firstname'];
							$to[0]['address']  = $result['email'];

													$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}');
													$tab_val = array($result['firstname'],$result['lastname'],$result['libelle'],$result['country'], $result['niveau']);

													$subject_br = $work->fields['events_mail4_subject'];
													$mail_brouil = $work->fields['events_mail4_content'];

													//on fait le remplacement des tags
													$subject = str_replace($tab_rep, $tab_val, $subject_br);
													$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
													$mail_content .= '<br/><br/>';
													if($work->fields['events_signature'] != '') {
															$mail_content .= $work->fields['events_signature'];
													}
													elseif($work->fields['signature'] != '') {
															$mail_content .= $work->fields['signature'];
													}
													if ($subject!='') {
															dims_send_mail($from,$to, $subject, nl2br($mail_content));
													}

							//$subject = 'Invalidation de votre inscription a l\'evenement '.$result['libelle'];
	//						$subject = 'Sorry, your registration was not successful';
	//
	//						$content = 'Dear '.$result['firstname'].' '.$result['lastname'].',<br /><br />';
	//						  $content.= 'You have registered for : '.$result['libelle'].'<br />';
	//						  $content.= 'We thank you for this registration but unfortunately we cannot proceed your registration any further.<br /><br />';
	//						  $content.= 'Please contact our office immediately via email <a href="mailto:info@luxembourgforbusiness.lu">info@luxembourgforbusiness.lu</a> or phone on +352 247-84116.<br /><br />';
	//						  $content.= 'Many thanks!<br />';
	//
	//						$content.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);
							//
							//dims_send_mail($from,$to, $subject, $content);

						}
					}
					break;

				case 'valide_insc':

					if($convmeta == '' || !isset($_SESSION['dims']['contact_fields_mode'])) {
					//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
							$sql =	"
													SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
																			mb.protected,mb.name as namefield,mb.label as titlefield
													FROM		dims_mod_business_meta_field as mf
													INNER JOIN	dims_mb_field as mb
													ON			mb.id=mf.id_mbfield
													RIGHT JOIN	dims_mod_business_meta_categ as mc
													ON			mf.id_metacateg=mc.id
													WHERE		  mf.id_object = :idobject
													AND			mc.admin=1
													AND			mf.used=1
													ORDER BY	mc.position, mf.position
													";
							$rs_fields=$db->query($sql, array(':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT) );

							$rubgen=array();
							$convmeta = array();

							while ($fields = $db->fetchrow($rs_fields)) {
									if (!isset($rubgen[$fields['id_cat']]))  {
											$rubgen[$fields['id_cat']]=array();
											$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
											$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
											if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
									}

									// on ajoute maintenant les champs dans la liste
									$fields['use']=0;// par defaut non utilise
									$fields['enabled']=array();
									if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

									$_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

									// enregistrement de la conversion
									$convmeta[$fields['namefield']]=$fields['id'];
							}
					}

					$id_evt = 0;
					$id_insc= 0;

					$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true);
					$id_insc= dims_load_securvalue('id_inscrip', dims_const::_DIMS_NUM_INPUT, true);

					$sql = 'SELECT
							a.id AS id_evt,
							a.libelle,
							a.datejour,
							a.heuredeb,
							a.heurefin,
							a.niveau,
							ei.*,
							COUNT(ee.id) AS nb_etap
						FROM
							dims_mod_business_action a
						INNER JOIN
							dims_mod_business_event_inscription ei
							ON
								ei.id_action = a.id
						LEFT JOIN
							dims_mod_business_event_etap ee
							ON ee.id_action = a.id
						WHERE
							a.id = :idevt

						AND
							ei.id = :idinsc
						GROUP BY
							ee.id_action';

					$ressource = $db->query($sql, array(':idevt' => $id_evt, ':idinsc' => $id_insc) );

					if($db->numrows($ressource) == 1) {
						$result = $db->fetchrow($ressource);

						if($result['niveau'] == 1) {
							if($result['validate'] == -1) {
								$sql = 'UPDATE dims_mod_business_event_inscription SET validate=0 WHERE id= :idinsc';
								$db->query($sql, array(':idinsc' => $id_insc));
							}
							elseif($result['validate'] == 0 || $result['validate'] == 1) {

								$id_contact = 0;
								$id_contact = dims_load_securvalue('user_rattach', dims_const::_DIMS_NUM_INPUT, false, true, true, $id_contact);

								if($id_contact == -1) {
									//Creation d'une fiche
									$new_contact = new contact();
									$ct_layer = new contact_layer();
									$maj_ly = 0;

									$new_contact->fields['lastname']	= (!empty($result['lastname'])) ? $result['lastname'] : '';
									$new_contact->fields['firstname']	= (!empty($result['firstname'])) ? $result['firstname'] : '';
									$new_contact->fields['id_module']	= $_SESSION['dims']['moduleid'];
									$new_contact->fields['id_workspace']= 0;//$_SESSION['dims']['workspaceid']

									$ct_layer->init_description();
									$ct_layer->fields['type_layer'] = 1;
									$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
											if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
													//c'est un champ generique -> on enregistre dans contact
													if(!empty($result['address'])) {
															$contact->fields['address'] = $result['address'];
															$maj_ct = 1;
													}
											}
											else {
													//c'est un champ metier -> on enregistre dans un layer
													if(!empty($result['address'])) {
															$ct_layer->fields['address'] = $result['address'];
															$maj_ly = 1;
													}
											}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
											if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
													//c'est un champ generique -> on enregistre dans contact
													if(!empty($result['postalcode'])) {
															$contact->fields['postalcode'] = $result['postalcode'];
															$maj_ct = 1;
													}
											}
											else {
													//c'est un champ metier -> on enregistre dans un layer
													if(!empty($result['postalcode'])) {
															$ct_layer->fields['postalcode'] = $result['postalcode'];
															$maj_ly = 1;
													}
											}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
											if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
													//c'est un champ generique -> on enregistre dans contact
													if(!empty($result['city'])) {
															$contact->fields['city'] = $result['city'];
															$maj_ct = 1;
													}
											}
											else {
													//c'est un champ metier -> on enregistre dans un layer
													if(!empty($result['city'])) {
															$ct_layer->fields['city'] = $result['city'];
															$maj_ly = 1;
													}
											}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
											if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
													//c'est un champ generique -> on enregistre dans contact
													if(!empty($result['postalcode'])) {
															$contact->fields['country'] = $result['country'];
															$maj_ct = 1;
													}
											}
											else {
													//c'est un champ metier -> on enregistre dans un layer
													if(!empty($result['postalcode'])) {
															$ct_layer->fields['country'] = $result['country'];
															$maj_ly = 1;
													}
											}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
											if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
													//c'est un champ generique -> on enregistre dans contact
													if(!empty($result['phone'])) {
															$contact->fields['phone'] = $result['phone'];
															$maj_ct = 1;
													}
											}
											else {
													//c'est un champ metier -> on enregistre dans un layer
													if(!empty($result['phone'])) {
															$ct_layer->fields['phone'] = $result['phone'];
															$maj_ly = 1;
													}
											}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
											if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
													//c'est un champ generique -> on enregistre dans contact
													if(!empty($result['email'])) {
															$contact->fields['email'] = $result['email'];
															$maj_ct = 1;
													}
											}
											else {
													//c'est un champ metier -> on enregistre dans un layer
													if(!empty($result['email'])) {
															$ct_layer->fields['email'] = $result['email'];
															$maj_ly = 1;
													}
											}
									}



									$new_contact->save();
									$new_id_contact = $new_contact->fields['id'];

									if($maj_ly == 1) {
											$ct_layer->fields['id'] = $new_id_contact;
											$ct_layer->save();
									}

									//Rattachement + validation
									$sql = 'UPDATE dims_mod_business_event_inscription SET validate=2, id_contact= :idcontact WHERE id= :idinsc';
									$param = array(':idcontact' => $new_id_contact, ':idinsc' => $id_insc);
								}
								elseif($id_contact == 0) {
									//validation
									$sql = 'UPDATE dims_mod_business_event_inscription SET validate=2 WHERE id= :idinsc';
									$param = array(':idinsc' => $id_insc);
								}
								else {
									//Rattachement + validation
									$sql = 'UPDATE dims_mod_business_event_inscription SET validate=2, id_contact= :idcontact WHERE id= :idinsc';
									$param = array(':idcontact' => $new_id_contact, ':idinsc' => $id_insc);
								}

								$db->query($sql, $param);

								$date_evt = array();
								ereg('^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$',$result['datejour'],$date_evt);

								//mail
								$from	= array();
								$to		= array();
								$subject= '';
								$message= '';

								$to[0]['name']	   = $result['lastname'].' '.$result['firstname'];
								$to[0]['address']  = $result['email'];

								//$from[0]['name']	 = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
								//$from[0]['address']= $_SESSION['dims']['user']['email'];

								$work = new workspace();
								$work->open($_SESSION['dims']['workspaceid']);
								$email = $work->fields['events_sender_email'];
								if ($email=="") $email=_DIMS_ADMINMAIL;

								// on recupere le @ et on prend le reste
								$pos=strpos($email,"@");
								if ($pos>0) $name=substr($email,$pos+1);
								else $name=$email;
								$from[0]['name']   = $name;//$email;
								$from[0]['address'] = $email;

								$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}');
								$tab_val = array($result['firstname'],$result['lastname'],$result['libelle'],$result['country'],'1');

								$subject_br = $work->fields['events_mail3_subject'];

								$mail_brouil = $work->fields['events_mail3_content'];

								if ($mail_brouil!='') {
									//on fait le remplacement des tags
									$subject = str_replace($tab_rep, $tab_val, $subject_br);
									$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
									$mail_content .= '<br/><br/>';
									if($work->fields['events_signature'] != '') {
											$mail_content .= $work->fields['events_signature'];
									}
									elseif($work->fields['signature'] != '') {
											$mail_content .= $work->fields['signature'];
									}

									dims_send_mail($from,$to, $subject, nl2br($mail_content));
								}
							}
						}
						elseif($result['niveau'] == 2) {
							$password = '';
							if($result['validate'] == -1) {
								$sql = 'UPDATE dims_mod_business_event_inscription SET validate=0 WHERE id= :idinsc';

								$db->query($sql, array(':idinsc' => $id_insc) );
							}
							elseif($result['validate'] == 0) {
								//$id_user = 0;
								//$id_user = dims_load_securvalue('user_rattach', dims_const::_DIMS_NUM_INPUT, false, true, true, $id_user);

								$id_contact = 0;
								$id_contact = dims_load_securvalue('user_rattach', dims_const::_DIMS_NUM_INPUT, false, true, true, $id_contact);

								if($id_contact == 0 || $id_contact == -1) {
									$search_login	= true;
									$i_login		= 0;
									$new_user		= new user();

									// CrÃ©er un login non existant encore (En fonction du nom/prenom[+nombre aleatoire])
									// /!\ Risque de trop de requete :(
									// Autre solution ?
									$login = $result['lastname'].$result['firstname'];

									while($search_login) {
										$i_login++;

										$sql = 'SELECT id FROM dims_user WHERE login = :login';
										$ress = $db->query($sql, array(':login' => $login ) );

										if($db->numrows($ress) == 0)
											$search_login = false;
										else
											$login = $result['lastname'].$result['firstname'].$i_login;
									}

									// CrÃ©er un mot de passe [a-zA-Z0-9]
									$hash_pwd = '';

									$char_list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
									$size_list	= strlen($char_list)-1;

									for($i = 0; $i < 8; $i++)
									{
										$rand_nb	= mt_rand(0, $size_list);
										$password  .= $char_list[$rand_nb];
									}

									$hash_pwd = dims_getPasswordHash($password);

									$new_user->fields['login']		= $login;
									$new_user->fields['password']	= $hash_pwd;
									$new_user->fields['lastname']	= (!empty($result['lastname'])) ? $result['lastname'] : '';
									$new_user->fields['firstname']	= (!empty($result['firstname'])) ? $result['firstname'] : '';
									$new_user->fields['address']	= (!empty($result['address'])) ? $result['address'] : '';
									$new_user->fields['city']		= (!empty($result['city'])) ? $result['city'] : '';
									$new_user->fields['postalcode'] = (!empty($result['postalcode'])) ? $result['postalcode'] : '';
									$new_user->fields['country']	= (!empty($result['country'])) ? $result['country'] : '';
									$new_user->fields['phone']		= (!empty($result['phone'])) ? $result['phone'] : '';
									$new_user->fields['email']		= (!empty($result['email'])) ? $result['email'] : '';

									$new_user->save();

									$id_contact = $new_user->fields['id_contact'];

									//Rattachement + validation
									$sql = 'UPDATE dims_mod_business_event_inscription SET validate=1, id_contact= :idcontact WHERE id= :idinsc';
									$db->query($sql, array(':idcontact' => $id_contact, ':idinsc' => $id_insc) );

									//mail
									$from	= array();
									$to		= array();
									$subject= '';
									$message= '';

									$to[0]['name']	   = $result['lastname'].' '.$result['firstname'];
									$to[0]['address']  = $result['email'];

									$work = new workspace();
									$work->open($_SESSION['dims']['workspaceid']);
									$email = $work->fields['events_sender_email'];
									if ($email=="") $email=_DIMS_ADMINMAIL;

									$from[0]['name'] = '';
									$from[0]['address'] = $email;

									//$from[0]['name']	 = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
									//$from[0]['address']= $_SESSION['dims']['user']['email'];

									$rootpath="";
									if ($result['host']!='') {
											$rootpath=$result['host'];
									}
									else {
											$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
											$rootpath=$dims->getProtocol().$http_host;
									}
									$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$result['id_evt'],true).'">link</a>';

									$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}');
									$tab_val = array($result['firstname'],$result['lastname'],$result['libelle'],$result['country'],'2', $micro_link);

									$subject_br = $work->fields['events_mail5_subject'];
									$mail_brouil = $work->fields['events_mail5_content'];

									//on fait le remplacement des tags
									$subject = str_replace($tab_rep, $tab_val, $subject_br);
									$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
									$mail_content .= '<br/><br/>';
									$mail_content .= "<br />Login : <b>".$login."</b>
																	  <br />Password : <b>".$password."</b><br />";
									if($work->fields['events_signature'] != '') {
											$mail_content .= $work->fields['events_signature'];
									}
									elseif($work->fields['signature'] != '') {
											$mail_content .= $work->fields['signature'];
									}

									unset($password);

								}
								else {
										//$user_insc = new user();
										//$user_insc->open($id_user);

										$evt_insc = new event_insc();
										$evt_insc->open($id_insc);

										$evt_insc->fields['id_contact'] = $id_contact;//$user_insc->fields['id_contact'];
										$evt_insc->fields['validate'] = 1;

										$evt_insc->save();

										$evt_insc->verifStep($id_evt,$id_contact);

										$work = new workspace();

										$work->open($_SESSION['dims']['workspaceid']);

										$email = $work->fields['email'];
										if ($email=="") $email=_DIMS_ADMINMAIL;
										$from	= array();
										$from[0]['name'] = '';
										$from[0]['address'] = $email;

										//il faut avertir la personne qu'il est bien inscrit et lui donner ce nouveau mot de passe
										//il faut preciser dans le mail que ce mot de passe est valable pour tous les events aux quels il est inscrit
										//mail

									$to		= array();
									$subject= '';
									$message= '';

									$to[0]['name']	   = $user_insc->fields['lastname'].' '.$user_insc->fields['firstname'];
									$to[0]['address']  = $user_insc->fields['email'];

									//$from[0]['name']	 = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
									//$from[0]['address']= $_SESSION['dims']['user']['email'];

									$rootpath="";
										if ($result['host']!='') {
												$rootpath=$result['host'];
										}
										else {
												$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
												$rootpath=$dims->getProtocol().$http_host;
										}
										$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$result['id_evt'],true).'">link</a>';

										$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}');
										$tab_val = array($result['firstname'],$result['lastname'],$result['libelle'],$result['country'],'2', $micro_link);

										$subject_br = $work->fields['events_mail5_subject'];
										$mail_brouil = $work->fields['events_mail5_content'];

										//on fait le remplacement des tags
										$subject = str_replace($tab_rep, $tab_val, $subject_br);
										$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
										$mail_content .= '<br/><br/>';
										$mail_content .= "<br />Login : <b>".$user_insc->fields['login']."</b><br />";
										if($work->fields['events_signature'] != '') {
												$mail_content .= $work->fields['events_signature'];
										}
										elseif($work->fields['signature'] != '') {
												$mail_content .= $work->fields['signature'];
										}
								}

								dims_send_mail($from,$to, $subject, nl2br($mail_content));
								//dims_send_mail($from,$to, $subject, $content);
							}
						}
					}

					break;
			}

			//No Break : Permet de revenir sur la liste des users a la suite d'une validation/annulation niv1/niv2

		case 'adm_evt':
			require_once(DIMS_APP_PATH . '/modules/events/public_events_inscr.php');
			break;
		case 'adm_insc':
			require_once(DIMS_APP_PATH . '/modules/events/public_events_inscr_admin.php');
			break;
		case 'import_insc':
			require_once(DIMS_APP_PATH . '/modules/events/public_events_import_inscr.php');
			break;

			case 'alert_modif':

					$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true, true);

					$evt = new action();
			$evt->open($id_evt);

					//Recherche de l'evt + infos insc liées (verification que l'evt appartient bien a l'user)
			$sql = 'SELECT
						a.id AS id_evt,
						a.typeaction,
						a.libelle,
						a.description,
						a.datejour,
						a.heuredeb,
						a.heurefin,
						a.timestp_modify,
						a.timestamp_release,
						a.supportrelease,
						a.rub_nl,
						a.allow_fo,
						a.target,
						a.teaser,
						a.lieu,
						a.prix,
						a.conditions,
											a.close,
						a.niveau,
											a.alert_modif,
						ei.id AS id_insc,
						ei.id_contact,
						ei.validate,
						ei.lastname,
						ei.firstname,
						ei.address,
						ei.city,
						ei.postalcode,
						ei.country,
						ei.phone,
						ei.email,
						ei.company,
											ei.host,
						ei.function
					FROM
						dims_mod_business_action a
					INNER JOIN
						dims_user u
						ON
							u.id = a.id_user
					INNER JOIN
						dims_mod_business_event_inscription ei
						ON
							ei.id_action = a.id
											AND ei.validate > 0
					WHERE
						a.id = :idevt

					ORDER BY
						ei.validate DESC';

			$ressource	= $db->query($sql, array(':idevt' => $id_evt) );

					while($info = $db->fetchrow($ressource))
					{
							//Construction du tableau récpitulatif de l'evt
							$tab_evt['id_evt']				= $info['id_evt'];
							$tab_evt['libelle']				= $info['libelle'];
							$tab_evt['typeaction']			= $info['typeaction'];
							$tab_evt['description']			= $info['description'];
							$tab_evt['datejour']			= $info['datejour'];
							$tab_evt['heuredeb']			= $info['heuredeb'];
							$tab_evt['heurefin']			= $info['heurefin'];
							$tab_evt['timestp_modify']		= $info['timestp_modify'];
							$tab_evt['timestamp_release']	= $info['timestamp_release'];
							$tab_evt['supportrelease']		= $info['supportrelease'];
							$tab_evt['rub_nl']				= $info['rub_nl'];
							$tab_evt['allow_fo']			= $info['allow_fo'];
							$tab_evt['target']				= $info['target'];
							$tab_evt['teaser']				= $info['teaser'];
							$tab_evt['lieu']				= $info['lieu'];
							$tab_evt['prix']				= $info['prix'];
							$tab_evt['conditions']			= $info['conditions'];
							$tab_evt['niveau']				= $info['niveau'];
							$tab_evt['close']				= $info['close'];
							$tab_evt['alert_modif']			= $info['alert_modif'];

							//Si on a une inscription (ou plus) sur l'evt
							if(isset($info['id_insc']) && !empty($info['id_insc']) && $info['validate'] > 0)
							{
									//Construction du tableau des inscriptions (Id_ins en clé premier niveau)
									$tab_ins[$info['id_insc']]['id_insc']	= $info['id_insc'];
									$tab_ins[$info['id_insc']]['id_contact']= $info['id_contact'];
									$tab_ins[$info['id_insc']]['validate']	= $info['validate'];
									$tab_ins[$info['id_insc']]['lastname']	= $info['lastname'];
									$tab_ins[$info['id_insc']]['firstname'] = $info['firstname'];
									$tab_ins[$info['id_insc']]['address']	= $info['address'];
									$tab_ins[$info['id_insc']]['city']		= $info['city'];
									$tab_ins[$info['id_insc']]['postalcode']= $info['postalcode'];
									$tab_ins[$info['id_insc']]['country']	= $info['country'];
									$tab_ins[$info['id_insc']]['phone']		= $info['phone'];
									$tab_ins[$info['id_insc']]['email']		= $info['email'];
									$tab_ins[$info['id_insc']]['company']	= $info['company'];
									$tab_ins[$info['id_insc']]['function']	= $info['function'];
							}
					}

					$work = new workspace();

					$work->open($_SESSION['dims']['workspaceid']);

					$email = $work->fields['email'];
					if ($email=="") $email=_DIMS_ADMINMAIL;
					$from	= array();
					$from[0]['name'] = '';
					$from[0]['address'] = $email;

					$rootpath="";
					if ($result['host']!='') {
							$rootpath=$result['host'];
					}
					else {
							$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
							$rootpath=$dims->getProtocol().$http_host;
					}
					$micro_link = '<a href="'.dims_urlencode($rootpath.'/index.php?id_event='.$result['id_evt'],true).'">link</a>';

					$tab_rep = array('{firstname}', '{lastname}', '{event_libelle}', '{company}', '{event_level}', '{micro_link}', '{event_url}');

					$subject_br = $work->fields['events_mail11_subject'];
					$mail_brouil = $work->fields['events_mail11_content'];

					//$from[0]['name'] = 'I-net Portal';
					//$from[0]['address'] = '';

					//$subject = 'I-net Portal : '.$evt->fields['libelle'].' have changed';
					$evt_url = '<a href="http://www.luxembourgforbusiness.lu/'.str_replace(" ","-",strtolower(trim($tab_evt['libelle']))).'">clic here</a>';
					foreach($tab_ins as $id_insc => $tab_insc) {

							$to[0]['name'] = $tab_insc['firstname']." ".$tab_insc['lastname'];
							$to[0]['address'] = $tab_insc['email'];

							$tab_val = array($tab_insc['firstname'],$tab_insc['lastname'],$tab_evt['libelle'],'','2', $micro_link,$evt_url);

							//on fait le remplacement des tags
							$subject = str_replace($tab_rep, $tab_val, $subject_br);
							$mail_content = str_replace($tab_rep, $tab_val, $mail_brouil);
							$mail_content .= '<br/><br/>';
							if($work->fields['events_signature'] != '') {
									$mail_content .= $work->fields['events_signature'];
							}
							elseif($work->fields['signature'] != '') {
									$mail_content .= $work->fields['signature'];
							}

							dims_send_mail($from, $to, $subject, nl2br($mail_content));
					}

					$evt->fields['alert_modif'] = 0;
					$evt->save();

					dims_redirect("admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&action=adm_evt&id_evt=$id_evt");
					break;
			case 'save_event':
					require_once(DIMS_APP_PATH . '/modules/system/class_dossier.php');
					require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
					require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
					require_once(DIMS_APP_PATH . '/modules/system/class_user_planning.php');
					require_once(DIMS_APP_PATH . '/modules/system/class_business_metacateg.php');

					if($_SESSION['dims']['browser']['pda']) {
							$rech_dossier = -1;
							$rech_tiers = -1;
							$actiondetail_dossier_id = -1;
							$actiondetail_tiers_id	= -1;
					}
						$datedebl		= dims_load_securvalue("action_datejour",dims_const::_DIMS_CHAR_INPUT,true,true,false);
						$datefinl		= dims_load_securvalue("datefin",dims_const::_DIMS_CHAR_INPUT,true,true,false);
						$action_id		= dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$action_type	= dims_load_securvalue('action_type',dims_const::_DIMS_NUM_INPUT,true,true,true,$action_type,dims_const::_PLANNING_ACTION_RDV);

						if(empty($datefinl))
								$datefinl = $datedebl;

			// controle si choix du type extended
			$dateextended		= dims_load_securvalue('action_dateextended',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($dateextended==2) {
				// on reconvertit le mois et l'année saisi
				$month	= dims_load_securvalue("month",dims_const::_DIMS_NUM_INPUT,true,true,false);
				$year		= dims_load_securvalue("year",dims_const::_DIMS_NUM_INPUT,true,true,false);
				$datedebl= "01/".$month."/".$year;
				$datefinl= $datedebl;
			}
						$datedeb=explode("/",$datedebl);
						$datefin=explode("/",$datefinl);
						$datedeb_timestp=mktime(0,0,0,$datedeb[1],$datedeb[0],$datedeb[2]);
						$datefin_timestp=mktime(0,0,0,$datefin[1],$datefin[0],$datefin[2]);
						$nbjour=($datefin_timestp-$datedeb_timestp)/86400;

						if($action_type == dims_const::_PLANNING_ACTION_EVT) {
								$daterel_temp	= dims_load_securvalue("daterelease",dims_const::_DIMS_CHAR_INPUT,true,true,false);
								$dateopen_temp	= dims_load_securvalue("dateopen",dims_const::_DIMS_CHAR_INPUT,true,true,false);

								if(!empty($daterel_temp)) {
										$daterel		= explode("/",$daterel_temp);
										$daterel_timestp= mktime(0,0,0,$daterel[1],$daterel[0],$daterel[2]);
										//if($daterel_timestp < mktime(0,0,0))
										//	$daterel_timestp = mktime(0,0,0);
								}
								else
										$daterel_timestp=mktime(0,0,0);

								$daterel_timestp = date('Ymd000000',$daterel_timestp);

								if(!empty($dateopen_temp)) {
										$dateopen		= explode("/",$dateopen_temp);
										$dateopen_timestp= mktime(0,0,0,$dateopen[1],$dateopen[0],$dateopen[2]);
								}
								else
										$dateopen_timestp=mktime(0,0,0);

								$dateopen_timestp = date('Ymd000000',$dateopen_timestp);


						}

						$actionheuredeb = dims_load_securvalue('actionx_heuredeb_h', dims_const::_DIMS_NUM_INPUT, true, true, true);
						$actionminutedeb = dims_load_securvalue('actionx_heuredeb_m', dims_const::_DIMS_NUM_INPUT, true, true, true);
						if ($datedeb==$datefin) {
							if ($action_id>0) {
								$ticket_title = "Planning modifie le ".$datedebl." à ".$actionheuredeb.":".$actionheunutedeb;
							} else {
								$ticket_title = "RDV ajoute le ".$datedebl." à ".$actionheuredeb.":".$actionheunutedeb;
							}
						}
						else {
							if ($action_id>0) {
								$ticket_title = "Planning modifie du ".$datedebl." au ".$datefinl." à ".$actionheuredeb.":".$actionheunutedeb;
							} else {
								$ticket_title = "RDV ajoute du ".$datedebl." au ".$datefinl." à ".$actionheuredeb.":".$actionminutedeb;
							}
						}

						//Le premier est forcement un parent
						unset($_SESSION['dims']['business_parent']);
						$parent = true;

						//Si c'est une modification d'evenement
						//ET qu'il n'y a pas d'erreur dans les date
						if ($action_id>0 && $nbjour > 0) {
								//Il faut d&eacute;truire les enfants
								//Pour les reconstruire ensuite
								$sql = 'DELETE FROM dims_mod_business_action
												WHERE id_parent = :idparent';

								$db->query($sql,array(':idparent' => $action_id) );
						}

						$datefin_insc		= dims_load_securvalue("datefin_insc",dims_const::_DIMS_CHAR_INPUT,true,true,false);
						//$dendinsc=date("d/m/Y",$datefin_insc);

						for($i=0;$i<=$nbjour;$i++) {
								$id_action=0;

								// calcul du pas de jour
								$datedeb_timestp=mktime(0,0,0,$datedeb[1],$datedeb[0]+$i,$datedeb[2]);
								// calcul si jour coche
								if (1) {
										$action = new action();

										$actionx_heuredeb_h=dims_load_securvalue('actionx_heuredeb_h',dims_const::_DIMS_CHAR_INPUT,false,true,true);
										$actionx_heuredeb_m=dims_load_securvalue('actionx_heuredeb_m',dims_const::_DIMS_CHAR_INPUT,false,true,true);

										$actionx_heurefin_h=dims_load_securvalue('actionx_heurefin_h',dims_const::_DIMS_CHAR_INPUT,false,true,true);
										$actionx_heurefin_m=dims_load_securvalue('actionx_heurefin_m',dims_const::_DIMS_CHAR_INPUT,false,true,true);

										$is_model = dims_load_securvalue('action_is_model', dims_const::_DIMS_NUM_INPUT, false, true);

										//Si on edite ET que c'est un parent -> ouverture action
										if ($action_id>0 && $parent) {
												$action->open($action_id);

												//dans le cas d'une modif, il faut indiquer si les champs date, heure ou description sont modifi&eacute;s
												//afin d'avertir les admins et users

												//DATES
												$tab_datedeb_base = explode("-", $action->fields['datejour']);
												$date_deb_base = $tab_datedeb_base[0].$tab_datedeb_base[1].$tab_datedeb_base[2]."000000";
												$date_deb_form = date("Ymd", $datedeb_timestp)."000000";
												$tab_datefin_base = explode("-", $action->fields['datefin']);
												$date_fin_base = $tab_datefin_base[0].$tab_datefin_base[1].$tab_datefin_base[2]."000000";
												$date_fin_form = date("Ymd", $datefin_timestp)."000000";

												//HEURES
												$hdeb_base = $action->fields['heuredeb'];
												$hdeb_form = $actionx_heuredeb_h.":".$actionx_heuredeb_m.":00";
												$hfin_base = $action->fields['heurefin'];
												$hfin_form = $actionx_heurefin_h.":".$actionx_heurefin_m.":00";

												//DESCRIPTION
												$desc_base = strip_tags($action->fields['description']);
												$desc_form = strip_tags(dims_load_securvalue('action_description',dims_const::_DIMS_CHAR_INPUT,false,true));

												if($date_deb_base != $date_deb_form ||
												   $date_fin_base != $datefin_timestp ||
												   $hdeb_base != $hdeb_form ||
												   $hfin_base != $hfin_form ||
												   $desc_base != $desc_form
												   ) {
														$action->fields['alert_modif'] = 1;
												}

												//GESTION DE L'UTILISATION D'UN MODELE
												if($is_model > 0) {

														//si un modele existe dejà, on verifie s'il est identique ou non
														if($action->fields['is_model'] != 0) {

																if($action->fields['is_model'] != $is_model) {
																		//s'il est different, on supprime les anciens rattachements puis on recré le model
																		$sql_d = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

																		$res_d = $db->query($sql_d, array(':idaction' => $action->fields['id'] ));
																		while($tab_d = $db->fetchrow($res_d)) {

																				if(!isset($tab_ee[$tab_d['id']])) $tab_ee[$tab_d['id']] = array();

																				//on supprime les liens (action_etap_file)
																				$del_ee = new etap_file();
																				$del_ee->open($tab_d['id']);
																				$tab_ee[$tab_d['id']][] = $del_ee->fields['id_doc'];
																				$del_ee->delete();

																		}
																		foreach($tab_ee as $etap_id => $tab_doc) {
																				foreach($tab_doc as $key => $id_doc_del) {
																						if($id_doc_del > 0) {
																								//on supprime les docs
																								$del_doc = new docfile();
																								$del_doc->open($id_doc_del);
																								$del_doc->delete();
																						}
																				}

																				//on supprime les etapes
																				$del_act_etap = new action_etap();
																				$del_act_etap->open($etap_id);
																				$del_act_etap->delete();
																		}

																		if(		$is_model > 0) {

																				//on crée le modèle
																				$sql_act_etap = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

																				$res_act_etap = $db->query($sql_act_etap,array(':idaction' => $is_model));
																				while($tab_actionetap = $db->fetchrow($res_act_etap)) {
																						//on duplique l'etape
																						$actionetap = new action_etap();
																						$actionetap->fields = $tab_actionetap;
																						$actionetap->fields['id'] = '';
																						$actionetap->fields['id_action'] = $action_id;


																						// gestion des dates de fin
																						switch($actionetap->fields['position']) {
																							case 1 : // inscription
																								// date - 6 mois
																								$actionetap->fields['date_fin']=mktime(0,0,0,$datefin[1],$datefin[0]-180,$datefin[2]);
																								break;
																							case 4 : // delegue
																							case 5 : // transport
																								// date - 1 mois
																								$actionetap->fields['date_fin']=mktime(0,0,0,$datefin[1],$datefin[0]-30,$datefin[2]);
																								break;
																							case 2 : // brochure
																							case 3 : // materiel
																								  // date - 3 mois
																								$actionetap->fields['date_fin']=mktime(0,0,0,$datefin[1],$datefin[0]-90,$datefin[2]);
																								break;
																						}

																						$actionetap->save();

																						// on va rechercher la modification de l'objet principal pour attacher les docs à celui-ci
																						require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
																						$dims_action = new dims_action();
																						$title=$action->fields['libelle'];

																						$dims_action->searchByObjectAction($_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_EVENT,$action_id,$title,dims_const::_ACTION_MODIFY_EVENT);

																						//on recherche les documents attaches au model
																						$sql_doc_etap = "SELECT * FROM dims_mod_business_event_etap_file WHERE id_etape = :idetape AND id_action = :idaction";

																						$res_doc_etap = $db->query($sql_doc_etap, array(':idetape' => $tab_actionetap['id'], ':idaction' => $is_model));

																						if($db->numrows($res_doc_etap) > 0) {
																								while($tab_docetap = $db->fetchrow($res_doc_etap)) {
																										$id_new_doc = 0;
																										if($tab_docetap['id_doc'] > 0) {
																												//on cree une copie du document
																												$doc_tocopy = new docfile();
																												$doc_tocopy->open($tab_docetap['id_doc']);

																												$doc_tocp_path = $doc_tocopy->getfilepath();

																												$newaction_doc = new docfile();

																												$newaction_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
																												$newaction_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
																												$newaction_doc->fields['id_folder'] = -1;
																												$newaction_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
																												$newaction_doc->fields['name'] = $doc_tocopy->fields['name'];
																												$newaction_doc->fields['size'] = $doc_tocopy->fields['size'];

																												copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
																												$newaction_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

																												$newaction_doc->save();
																												$id_new_doc = $newaction_doc->fields['id'];
																										}
																										//on duplique l'action_etap_file
																										$action_etap_file = new etap_file();

																										$action_etap_file->fields = $tab_docetap;

																										$action_etap_file->fields['id'] = '';
																										$action_etap_file->fields['id_action'] = $action_id;
																										$action_etap_file->fields['id_etape'] = $actionetap->fields['id'];
																										if($id_new_doc > 0) $action_etap_file->fields['id_doc'] = $id_new_doc;

																										$action_etap_file->save();
																								}
																						}
																				}
																		}
																}
																//si c'est identique on a rien à faire
														}
														else {
																//il faut créer automatiquement les étapes à partir du model
																//recuperation des informations sur les etapes et documents rattachés

																$sql_act_etap = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

																$res_act_etap = $db->query($sql_act_etap, array(':idaction' => $is_model));
																while($tab_actionetap = $db->fetchrow($res_act_etap)) {
																		//on duplique l'etape
																		$actionetap = new action_etap();
																		$actionetap->fields = $tab_actionetap;
																		$actionetap->fields['id'] = '';
																		$actionetap->fields['id_action'] = $action_id;

																		 // gestion des dates de fin
																		switch($actionetap->fields['position']) {
																			case 1 : // inscription
																				// date - 6 mois
																				$actionetap->fields['date_fin']=date("Ymd", strtotime("-6 month", strtotime($datefin[2]."-".$datefin[1]."-".$datefin[0]))).'000000';
																				break;
																			case 4 : // delegue
																			case 5 : // transport
																				// date - 1 mois
																				$actionetap->fields['date_fin']=date("Ymd", strtotime("-1 month", strtotime($datefin[2]."-".$datefin[1]."-".$datefin[0]))).'000000';
																				break;
																			case 2 : // brochure
																			case 3 : // materiel
																					// date - 3 mois
																				$actionetap->fields['date_fin']=date("Ymd", strtotime("-3 month", strtotime($datefin[2]."-".$datefin[1]."-".$datefin[0]))).'000000';
																				break;
																		}

																		$actionetap->save();

																		//on recherche les documents attaches au model
																		$sql_doc_etap = "SELECT * FROM dims_mod_business_event_etap_file WHERE id_etape = :idetape AND id_action = :idaction";

																		$res_doc_etap = $db->query($sql_doc_etap, array(':idetape' => $tab_actionetap['id'], ':idaction' => $is_model) );

																		if($db->numrows($res_doc_etap) > 0) {
																				while($tab_docetap = $db->fetchrow($res_doc_etap)) {
																						$id_new_doc = 0;
																						if($tab_docetap['id_doc'] > 0) {
																								//on cree une copie du document
																								$doc_tocopy = new docfile();
																								$doc_tocopy->open($tab_docetap['id_doc']);

																								$doc_tocp_path = $doc_tocopy->getfilepath();

																								$newaction_doc = new docfile();

																								$newaction_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
																								$newaction_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
																								$newaction_doc->fields['id_folder'] = -1;
																								$newaction_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
																								$newaction_doc->fields['name'] = $doc_tocopy->fields['name'];
																								$newaction_doc->fields['size'] = $doc_tocopy->fields['size'];

																								copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
																								$newaction_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

																								$newaction_doc->save();
																								$id_new_doc = $newaction_doc->fields['id'];
																						}
																						//on duplique l'action_etap_file
																						$action_etap_file = new etap_file();

																						$action_etap_file->fields = $tab_docetap;

																						$action_etap_file->fields['id'] = '';
																						$action_etap_file->fields['id_action'] = $action_id;
																						$action_etap_file->fields['id_etape'] = $actionetap->fields['id'];
																						if($id_new_doc > 0) $action_etap_file->fields['id_doc'] = $id_new_doc;

																						$action_etap_file->save();
																				}
																		}
																}
														}
												}
												else {
														if($action->fields['is_model'] != 0 && isset($_POST['action_is_model'])) {
																//s'il est different, on supprime les anciens rattachements puis on recré le model
																$sql_d = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

																$res_d = $db->query($sql_d, array(':idaction' => $action->fields['id']) );
																while($tab_d = $db->fetchrow($res_d)) {

																		if(!isset($tab_ee[$tab_d['id']])) $tab_ee[$tab_d['id']] = array();

																		//on supprime les liens (action_etap_file)
																		$del_ee = new etap_file();
																		$del_ee->open($tab_d['id']);
																		$tab_ee[$tab_d['id']][] = $del_ee->fields['id_doc'];
																		$del_ee->delete();

																}
																foreach($tab_ee as $etap_id => $tab_doc) {
																		foreach($tab_doc as $key => $id_doc_del) {
																				if($id_doc_del > 0) {
																						//on supprime les docs
																						$del_doc = new docfile();
																						$del_doc->open($id_doc_del);
																						$del_doc->delete();
																				}
																		}

																		//on supprime les etapes
																		$del_act_etap = new action_etap();
																		$del_act_etap->open($etap_id);
																		$del_act_etap->delete();
																}
														}
												}
										}
										//Sinon on cr&eacute;e l'action dans tout les cas
										else $action->setugm(); // nouvelle action


										$action->setvalues($_POST,'action_');

										//GESTION DE L'UTILISATION D'UN MODELE
										if($is_model > 0 && $action_id > 0) {
												$act_model = new action();
												$act_model->open($is_model);
												//on copie les champs du model dans l'action
												$act_model->fields['libelle'] = $action->fields['libelle']; //on ne doit pas changer le libelle ... on recupere celui du post et on le copie dans le model pour ne pas le changer
												//on verifie si le teaser et la description ont étés modifiées par l'utilisateur
												if($action->fields['description'] != $act_model->fields['description'] && $action->fields['description'] != '')
														$act_model->fields['description'] = $action->fields['description'];
												if($action->fields['teaser'] != $act_model->fields['teaser'] && $action->fields['teaser'] != '')
														$act_model->fields['teaser'] = $action->fields['teaser'];
												//on reprend les infos sur les createurs / responsables ...
												$act_model->fields['id_user'] = $action->fields['id_user'];
												$act_model->fields['id_responsible'] = $action->fields['id_responsible'];
												$act_model->fields['id_organizer'] = $action->fields['id_organizer'];

												$action->fields = $act_model->fields;
												if($action_id > 0 && $parent) $action->fields['id'] = $action_id;
												else $action->fields['id'] = '';
												$action->fields['is_model'] = $is_model;

										}

										$djour=date("d/m/Y",$datedeb_timestp);
										$action->fields['datejour'] = business_datefr2us($djour);

										$djour=date("d/m/Y",$datefin_timestp);
										$action->fields['datefin'] = business_datefr2us($djour);

										$action->fields['datefin_insc'] = business_datefr2us($datefin_insc);
										//$action->fields['datejour'] =$datedeb_timestp;

										//gestion de la parentee (cas des evts sur pls jours : le premier a un id_parent = 0, les autres on met id_parent ? 1)
										if($i==0) {
												$action->fields['id_parent'] = 0;
										}
										else {
												$action->fields['id_parent'] = $_SESSION['dims']['business_parent'];
										}

										$action->fields['heuredeb'] = sprintf("%02d:%02d:00",$actionx_heuredeb_h,$actionx_heuredeb_m);

										/*if ($actionx_duree) {// > 0 => calcul heure de fin en fonction de dur?e
												$action->fields['temps_prevu'] = $actionx_duree;
												$heurefin = $actionx_heuredeb_h*60+$actionx_heuredeb_m+$action->fields['temps_prevu'];
												$heurefin_h = ($heurefin-$heurefin%60)/60;
												$heurefin_m = $heurefin%60;
												$action->fields['heurefin'] = sprintf("%02d:%02d:00",$heurefin_h,$heurefin_m);
										}
										else {*/// r?cup la saisie de l'heure de fin
										$action->fields['heurefin'] = sprintf("%02d:%02d:00",$actionx_heurefin_h,$actionx_heurefin_m);
										$action->fields['temps_prevu'] = ($actionx_heurefin_h-$actionx_heuredeb_h)*60+$actionx_heurefin_m-$actionx_heuredeb_m;
										//}

										if (!isset($action_personnel)) $action->fields['personnel'] = 0;
										if (!isset($action_conges)) $action->fields['conges'] = 0;
										if (!isset($action_interne)) $action->fields['interne'] = 0;

										$action->fields['temps_passe'] = $action->fields['temps_prevu'];
										$action->fields['timestp_modify']=dims_createtimestamp();

										if (!(isset($action_personnel) && $action_personnel) || (isset($action_conges) && $action_conges)) {
												if (isset($nouveau_dossier) && $nouveau_dossier = "1" && $rech_dossier != '') {
														$dossier = new dossier();
														$dossier->fields['objet_dossier'] = $rech_dossier;
														$dossier->setugm();
														$actiondetail_dossier_id = $dossier->save();
														dims_create_user_action_log(dims_const::_PLANNING_ACTION_OUVRIRDOSSIER, $actiondetail_dossier_id);
														$ticket_title .= "'{$rech_dossier}'";
												}
												else $action->fields['dossier_id']=0;

												if (isset($nouveau_tiers) && $nouveau_tiers = "1" && $rech_tiers != '') {
														$tiers = new tiers();
														$tiers->fields['intitule'] = $rech_tiers;
														$tiers->fields['actif'] = 'Oui';
														$tiers->setugm();
														$actiondetail_tiers_id = $tiers->save();
														dims_create_user_action_log(dims_const::_PLANNING_ACTION_OUVRIRTIERS, $actiondetail_tiers_id);
														$ticket_title .= "'{$rech_tiers}'";
												}
												else $action->fields['tiers_id']=0;
										}

										if (isset($_SESSION['dims']['planning']['currentactionusers'])) {
												$action->utilisateurs = $_SESSION['dims']['planning']['currentactionusers'];
												unset($_SESSION['dims']['planning']['currentactionusers']);
										}

										if (isset($_SESSION['dims']['planning']['currentactionpartner'])) {
												$action->partenaires = $_SESSION['dims']['planning']['currentactionpartner'];
												unset($_SESSION['dims']['planning']['currentactionpartner']);
										}

										//Enregistrement des infos evenements
										if($action_type == dims_const::_PLANNING_ACTION_EVT) {
												//if($is_model == 0) {

														$action->setvalues($_POST,'evt_');

														$action->fields['supportrelease'] = 0;

														if(isset($_POST['supportrelease']) && !empty($_POST['supportrelease']))
																$action->fields['supportrelease'] = 1;

														if(isset($_POST['form_level']) && $_POST['form_level'] == 'on')
																$action->fields['niveau'] = 2;
														else
																$action->fields['niveau'] = 1;
												//}
												$action->fields['timestamp_release']	= $daterel_timestp;
												$action->fields['timestp_open']			= $dateopen_timestp;

												// gestion de l'affectation automatique des inscriptions olbigatoires
												// Modification Patrick du 11/08/2010
												if ($action->fields['typeaction']=='_DIMS_PLANNING_FAIR_STEPS') {
														$action->fields['allow_fo']=1;
														$action->fields['niveau'] = 2;
												}

										}

										if(isset($_SESSION['dims']['planning']['currentactionusersPart']))
												$id_action = $action->save($_SESSION['dims']['planning']['currentactionusersPart']);
										else {
											if ($action->fields['id_parent']==0)
												$id_action = $action->save();
											else
												$id_action = $action->save(dims_const::_SYSTEM_OBJECT_EVENT);
										}


										//dims_print_r($action);die();
										if($action_type == dims_const::_PLANNING_ACTION_EVT) {
												// on regarde dans la liste des responsables en session
												$action->updateResps(dims_const::_SYSTEM_OBJECT_ACTION);
										}

										if(!isset($_SESSION['dims']['business_parent']))
												$_SESSION['dims']['business_parent'] = $id_action;

										if ($action_id>0)
												dims_create_user_action_log(_SYSTEM_ACTION_MODIFYACTION, "{$action->fields['libelle']} du $djour",dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM,$id_action,dims_const::_SYSTEM_OBJECT_ACTION);
										else
												dims_create_user_action_log(_SYSTEM_ACTION_ADDACTION, "{$action->fields['libelle']}  du $djour",dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM,$id_action,dims_const::_SYSTEM_OBJECT_ACTION);

										if($parent && $action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS' && $action_id == 0) {
												//INSCRIPTION AUTOMATIQUE DU CREATEUR
												$inscr = new event_insc();
												$inscr->init_description();

												$inscr->fields['id_action'] = $action->fields['id'];
												$inscr->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
												$inscr->fields['validate'] = 2;
												$inscr->fields['lastname'] = $_SESSION['dims']['user']['lastname'];
												$inscr->fields['firstname'] = $_SESSION['dims']['user']['firstname'];
												$inscr->fields['date_validate'] = date("YmdHis");
												$inscr->save();

										}

										//tout les ajouts suivant ne seront forc&eacute;ment QUE des enfants
										$parent = false;
										$_POST['action_id'] = '';
								}
						}

						//insertion des contacts directement rattaches
						if(!empty($_SESSION['dims']['planning_addct'])) {
								$inscr = new event_insc();
								foreach($_SESSION['dims']['planning_addct'] as $key => $id_ct) {
										//on recherche les infos du contact
										$cttoadd = new contact();
										$cttoadd->open($id_ct);

										$inscr->init_description();
										$inscr->fields['id_action'] = $id_action;
										$inscr->fields['id_contact'] = $id_ct;
										$inscr->fields['validate'] = 2;
										$inscr->fields['lastname'] = $cttoadd->fields['lastname'];
										$inscr->fields['firstname'] = $cttoadd->fields['firstname'];
										$inscr->fields['date_validate'] = date("YmdHis");
										$inscr->save();
								}
								unset($_SESSION['dims']['planning_addct']);
						}

						if(isset($_SESSION['dims']['business_parent'])) unset($_SESSION['dims']['business_parent']);
						// test si ticket a envoyer
						$dims_ticket_message=dims_load_securvalue("dims_ticket_message",dims_const::_DIMS_CHAR_INPUT,false,true);
						if ($dims_ticket_message!="") {
								// on construit la liste des personnes
								$_SESSION['dims']['tickets']['users_selected']=$action->utilisateurs;
								/*foreach ($action->utilisateurs as $u =>user) {
										[]=$user['id'];
								}*/
								require_once DIMS_APP_PATH . '/include/functions/tickets.php';
								dims_tickets_send($ticket_title, 'Envoi auto', true, 0, dims_const::_SYSTEM_OBJECT_ACTION, $id_action, $ticket_title,true);
								//dims_tickets_send("Demande de r&eacute;ponse ? la question : \"".dims_strcut($quest->fields['question'],60)."\" (module {$_SESSION['dims']['currentmodule']['label']})", "Ceci est un message automatique envoy? suite ? une demande de r&eacute;ponse ? la question \"".dims_strcut($quest->fields['question'],60)."\" du module {$_SESSION['dims']['currentmodule']['label']}<br /><br />Vous pouvez acc?der ? cette question pour y r&eacutepondre en cliquant sur le lien ci-dessous.", true, 0, _FAQ_OBJECT_QUESTION, $quest->fields['id'], dims_strcut($quest->fields['question'],60),true);
						}

						//dims_tickets_send(_BUSINESS_OBJECT_ACTION, $id_action, $ticket_title);
						if(is_array($_SESSION['dims']['browser']) && isset($_SESSION['dims']['browser']['pda'])) {
								if($action_type != dims_const::_PLANNING_ACTION_EVT) {
										?>
										<script language="javascript">
												history.go(-1);
										</script>
										<?php
								}
						}

						if($action_type == dims_const::_PLANNING_ACTION_EVT)
								dims_redirect("admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&submenu="._DIMS_VIEW_EVENTS_DETAILS."&action=add_evt&id=".$id_action);
								//dims_redirect($dims->getScriptEnv()."?op=xml_planning_modifier_action&id=".$action->fields['id']);
						else
								dims_redirect("admin.php");

						die();
					break;

	//////////FROM system/public_planning.php
					case 'search_action_contact_planning':
							ob_end_clean();
							if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
									$nomsearch	= dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true);
									$type_action= $_SESSION['dims']['planning']['currenttypeaction'];

									if($type_action == dims_const::_PLANNING_ACTION_RDV)
									{
											require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
											$dims_user= new user();
											$dims_user->open($_SESSION['dims']['userid']);

											$lstusers=$dims_user->getusersgroup($nomsearch,$_SESSION['dims']['planning']['currentworkspacesearch'],$_SESSION['dims']['planning']['currentprojectsearch']);

											if (isset($_SESSION['business']['usersselected']) && !empty($_SESSION['business']['usersselected'])) $lstusers+=$_SESSION['business']['usersselected'];
											if (isset($_SESSION['dims']['planning']['currentusertemp']) && !empty($_SESSION['dims']['planning']['currentusertemp'])) $lstusers+=$_SESSION['dims']['planning']['currentusertemp'];

											$lstuserssel=array();
											if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstuserssel+=$_SESSION['dims']['planning']['currentactionusers'];

											// affichage de la liste de r�sultat
											if (!empty($lstusers)) {
													// requete pour les noms
													$params = array();
													$res=$db->query("select id,firstname,lastname,color from dims_user where id in (".$db->getParamsFromArray($lstusers, 'users', $params).")", $params);
													if ($db->numrows($res)>0) {
															echo "<table style=\"width:100%;\">";
															while ($f=$db->fetchrow($res)) {
																	if (!in_array($f['id'],$lstuserssel)) {
																			$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
																			echo "<tr><td width=\"80%\">".$icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td><td>";
																			echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('addActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
																	}
															}
															echo "</table>";
													}
											}

											// calcul du la 2i�me liste de ceux selectionnes
											echo "||";
											echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_LABEL_CONCERNED']."</span>";
											$lstusers=array();
											if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstusers+=$_SESSION['dims']['planning']['currentactionusers'];
											if (!empty($lstusers)) {
													$params = array();
													$res=$db->query("select u.* from dims_user as u where id in (".$db->getParamsFromArray($lstusers, 'users', $params).")", $params);
													if ($db->numrows($res)>0) {

															echo "<table style=\"width:100%;\">";
															while ($f=$db->fetchrow($res)) {
																	//calcul de l'icon
																	$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
																	echo "<tr><td width=\"5%\">
																	<a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('deleteSelActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td>";
																	echo "<td  width=\"20%\">";
																	// calcul si participe ou non
																	if ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']]) {
																			$chselected="selected=\"selected\"";
																			$chunselected="";
																	}
																	else {
																			$chselected="";
																			$chunselected="selected=\"selected\"";
																	}

																	echo "<select onchange=\"updateUserActionFromSelectedPlanning('updatePartActionUserPlanning',".$f['id'].");\" name=\"part".$f['id'].".\">";
																	echo "<option $chselected value=\"0\">".$_DIMS['cste']['_DIMS_PARTICIPATES']."</option>";
																	echo "<option $chunselected value=\"1\">".$_DIMS['cste']['_DIMS_TOINFO']."</option>";
																	echo "</select></td><td>";
																	echo $icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td></tr>";
															}
															echo "</table>";
													}
											}
									}
									elseif($type_action == dims_const::_PLANNING_ACTION_EVT || $type_action == dims_const::_PLANNING_ACTION_RCT)
									{

											$lstusers = array();
											if(!empty($nomsearch))
											{
													$sql = "SELECT id FROM dims_mod_business_contact WHERE firstname LIKE '".$nomsearch."%' or lastname LIKE '".$nomsearch."%'";
													$res = $db->query($sql);

													while($f = $db->fetchrow($res))
															$lstusers[] =  $f['id'];
											}

											$lstuserssel=array();
											if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstuserssel+=$_SESSION['dims']['planning']['currentactionusers'];


											// affichage de la liste de r�sultat
											if (!empty($lstusers)) {
													// requete pour les noms
													$params = array();
													$res=$db->query("select id,firstname,lastname from dims_mod_business_contact where id in (".$db->getParamsFromArray($lstusers, 'users', $params).")", $params);
													if ($db->numrows($res)>0) {
															echo "<table style=\"width:100%;\">";
															while ($f=$db->fetchrow($res)) {
																	if (!in_array($f['id'],$lstuserssel)) {
																			$icon="<img src=\"./data/users/icon_EFEFEF.png\" alt=\"\" border=\"0\">";
																			echo "<tr><td width=\"80%\">".$icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td><td>";
																			echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('addActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
																	}
															}
															echo "</table>";
													}
											}
											// calcul du la 2i�me liste de ceux selectionnes
											echo "||";
											echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_EVT_CONTACT_PARTICIPATE']."</span>";
											$lstusers=array();
											if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstusers+=$_SESSION['dims']['planning']['currentactionusers'];
											if (!empty($lstusers)) {
													$params = array();
													$res=$db->query("select c.* from dims_mod_business_contact as c where id in (".$db->getParamsFromArray($lstusers, 'users', $params).")", $params);
													if ($db->numrows($res)>0) {

															echo "<table style=\"width:100%;\">";
															while ($f=$db->fetchrow($res)) {
																	//calcul de l'icon
																	$icon="<img src=\"./data/users/icon_EFEFEF.png\" alt=\"\" border=\"0\">";
																	echo "<tr><td width=\"5%\">
																	<a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('deleteSelActionUserPlanning',".$f['id'].");\" id=\"part".$f['id'].".\"><img src=\"./common/img/delete.png\" border=\"0\"></td>";
																	echo "<td  width=\"20%\">";

																	// calcul si participe ou non
																	if ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']] == 0)
																	{
																			$chselected_part ="selected=\"selected\"";
																			$chselected_orga = '';
																	}
																	elseif ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']] == 1)
																	{
																			$chselected_part = '';
																			$chselected_orga ="selected=\"selected\"";
																	}
																	echo '<select onchange="updateUserActionFromSelectedPlanning(\'updatePartActionUserPlanning\','.$f['id'].',\'part'.$f['id'].'\');" name="part'.$f['id'].'" id="part'.$f['id'].'">';
																	if($type_action == dims_const::_PLANNING_ACTION_EVT)
																	{
																			echo "<option $chselected_part value=\"0\">".$_DIMS['cste']['_DIMS_PARTICIPATES']."</option>";
																			echo "<option $chselected_orga value=\"1\">".$_DIMS['cste']['_ORGANIZE']."</option>";
																	}
																	elseif($type_action == dims_const::_PLANNING_ACTION_RCT)
																	{
																			echo "<option $chselected_part value=\"0\">".$_DIMS['cste']['_DIMS_MEETED']."</option>";
																			echo "<option $chselected_orga value=\"1\">".$_DIMS['cste']['_DIMS_ACCOMPANY']."</option>";
																	}
																	echo "</select></td><td>";
																	echo $icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td></tr>";
															}
															echo "</table>";
													}
											}
									}
							}
							die();
							break;

					case 'search_action_partner_planning':
							ob_end_clean();
							if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
									$nomsearch	= dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true);
									$type_action= $_SESSION['dims']['planning']['currenttypeaction'];

									$lstusers = array();
									$lstuserssel=array();

									//R�cup�ration de la liste des partenaire d�j� selectionn�s
									if (!empty($_SESSION['dims']['planning']['currentactionpartner']))
											$lstuserssel+=$_SESSION['dims']['planning']['currentactionpartner'];

									// affichage de la liste de r�sultat
									if(!empty($nomsearch))
									{
											$sql = "SELECT id, intitule,partenaire FROM dims_mod_business_tiers WHERE intitule LIKE '".$nomsearch."%'";
											$res=$db->query($sql);
											if ($db->numrows($res)>0) {
													echo "<table style=\"width:100%;\">";
													echo "<tr><td>".$_DIMS['cste']['_DIMS_LABEL_ENT_NAME']."</td><td>".$_DIMS['cste']['_DIMS_LABEL_PARTENAIRE']."</td><td>Select.</td></tr>";
													while ($f=$db->fetchrow($res)) {
															if (!in_array($f['id'],$lstuserssel)) {
																	$icon="<img src=\"./common/img/partenaire.png\" alt=\"\" border=\"0\">";
																	$i=($i==0) ? 1 : 0;
																	// test si partenaire ou non
																	if ($f['partenaire']) $iconpart="<img src=\"./common/img/checkdo2.png\" alt=\"\">";
																	else $iconpart="<img src=\"./common/img/checkdo1.png\" alt=\"\">";
																	echo "<tr class=\"trl$i\"><td width=\"80%\">".$icon."&nbsp;".$f['intitule']."</td><td>".$iconpart."</td><td>";
																	echo "<td><a href=\"javascript:void(0);\" onclick=\"updatePartnerActionFromSelectedPlanning('addActionPartnerPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
															}
													}
													echo "</table>";
											}
									}
									// calcul du la 2i�me liste de ceux selectionnes
									echo "||";
									echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_EVT_PARTNER']."</span>";
									$lstusers=array();
									if (!empty($_SESSION['dims']['planning']['currentactionpartner'])) $lstusers+=$_SESSION['dims']['planning']['currentactionpartner'];
									if (!empty($lstusers))
									{
											$params = array();
											$res=$db->query("select t.* from dims_mod_business_tiers as t where id in (".$db->getParamsFromArray($lstusers, 'users', $params).")", $params);
											if ($db->numrows($res)>0)
											{
													echo "<table style=\"width:100%;\">";
													while ($f=$db->fetchrow($res))
													{
															//calcul de l'icon
															$icon = "<img src=\"./common/img/partenaire.png\" alt=\"\" border=\"0\">";
															echo "<tr><td width=\"5%\">
															<a href=\"javascript:void(0);\" onclick=\"updatePartnerActionFromSelectedPlanning('deleteSelActionPartnerPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td><td>";
															echo $icon."&nbsp;".$f['intitule']."</td></tr>";
													}
													echo "</table>";
											}
									}

							}
							die();
							break;
					case 'delete_fileannonce':
									require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
									$id_file = dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true,false);

									if($id_file != '') {
											$doc = new docfile();
											$doc->open($id_file);
											$doc->delete();
									}

									break;
					case 'delete_preview':
									$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

									if($id_evt != '') {
											$action = new action();
											$action->open($id_evt);

											unlink($action->fields['preview_path']);
											$action->fields['preview_path'] = '';

											$action->save();
									}

									break;
					case 'delete_banner':
							$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

							if($id_evt != '') {
									$action = new action();
									$action->open($id_evt);

									unlink($action->fields['banner_path']);
									$action->fields['banner_path'] = '';

									$action->save();
							}

							break;

					case 'delete_match':
							$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

							if($id_evt != '') {
									$action = new action();
									$action->open($id_evt);

									unlink($action->fields['matchmaking_path']);
									$action->fields['matchmaking_path'] = '';

									$action->save();
							}

					break;

					case 'update_actionetap':
							ob_end_clean();
							$num = dims_load_securvalue('step',dims_const::_DIMS_NUM_INPUT,true,true,false);
							$_SESSION['dims']['eventstep']=$num;
							ob_flush();
							die();
					break;

					case 'refresh_etap':
							ob_end_clean();
							ob_start();

							require_once DIMS_APP_PATH . '/modules/events/public_events_etap.php';

							ob_flush();
							die();
					break;

					case 'delete_fileetap':
							$id_fileetap = dims_load_securvalue('id_fileetap',dims_const::_DIMS_NUM_INPUT,true,true,false);

							if ($id_fileetap>0) {
									$etapfile = new etap_file();

									$etapfile->open($id_fileetap);

									$etapfile->delete();
							}
							dims_redirect($scriptenv."?action=xml_planning_modifier_action&subaction="._DIMS_ACTION_ETAP);
					break;

					case 'xml_planning_modifier_action':
							include(DIMS_APP_PATH . '/modules/events/public_events_modifier_action.php');
					break;

					case "save_actionetap":
							$id_actionetap= dims_load_securvalue("id_actionetap",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['currentactionetap']);
							$label=  dims_load_securvalue("actionetap_label",dims_const::_DIMS_CHAR_INPUT,true,true,false);

							if(isset($_POST['actionetap_condition']) && $_POST['actionetap_condition'] == 1) {
								$_POST['actionetap_condition'] = 1;
							} else {
								$_POST['actionetap_condition'] = 0;
							}

							if($_POST['actionetap_date_fin']!=0) {
								$_POST['actionetap_date_fin'] = dims_local2timestamp(dims_load_securvalue('actionetap_date_fin', dims_const::_DIMS_CHAR_INPUT, true, true, true));
							}

							$actionetap = new action_etap();
							if ($label!="") {
								if ($id_actionetap>0) {
									$actionetap->open($id_actionetap);
									$actionetap->setvalues($_POST, "actionetap_");
								}
								else {
									$actionetap->setvalues($_POST, "actionetap_");
									// on compte le nb + 1
									$res=$db->query("select * from dims_mod_business_event_etap where id_action= :idaction", array(':idaction' => $_SESSION['dims']['currentaction']) );
									$position=$db->numrows($res);
									$actionetap->fields['position']=$position+1;
								}
								$actionetap->fields['id_action']=$_SESSION['dims']['currentaction'];

								$actionetap->save();
								unset($_SESSION['dims']['currentactionetap']);
							}

							dims_redirect($scriptenv."?dims_mainmenu=events&submenu=8&action=add_evt&id=".$_SESSION['dims']['currentaction']);
							//dims_redirect($scriptenv."?action=xml_planning_modifier_action&subaction="._DIMS_ACTION_ETAP);
					break;

					case 'save_etap_docreturn':
							$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);
							$idDoc = dims_load_securvalue('id_doc', dims_const::_DIMS_NUM_INPUT, true, true);
							$etap_file = new etap_file();

							if($idDoc == 0) {
											$etap_file->init_description();
							}
							else {
											$etap_file->open($idDoc);
							}

							$etap_file->setvalues($_POST, 'input_');

							$etap_file->fields['label'] = ($etap_file->fields['label']);
							$etap_file->fields['content'] = ($etap_file->fields['content']);

							$etap_file->fields['id_action']=$_SESSION['dims']['currentaction'];
							$etap_file->fields['id_etape']=$id_etap;
							$etap_file->fields['id_doc']=0;

							$etap_file->save();
					break;


					case "delete_actionalletapes":
						$id= dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
						if ($id>0) {
							$sql ="select		ee.*
					FROM		dims_mod_business_event_etap ee
					WHERE			ee.id_action= :idaction
					ORDEr BY	position";

							$res=$db->query($sql, array(':idaction' => $id));
							if ($db->numrows($res)>0) {
				while ($value=$db->fetchrow($res)) {
									$actionetap = new action_etap();
									$actionetap->open($value['id']);
									$position=$actionetap->fields['position'];
									// update position
									$db->query("update dims_mod_business_event_etap set position=position-1 where position> :position and id_action= :idaction",
												array(':position' => $position, ':idaction' => $_SESSION['dims']['currentaction'])
												);
									$actionetap->delete();

									$sql = 'SELECT id FROM dims_mod_business_event_etap_file WHERE id_etape= :idetape';

									$ress = $db->query($sql, array(':idetape' => $id_actionetap));

									if($db->numrows($ress) > 0)
									{
											$etap_file = new etap_file();
											while($f = $db->fetchrow($ress))
											{
													$etap_file->open($f['id']);
													$etap_file->delete();
											}
									}
								}
							}
							// on rebascule à zero
							require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
							$action = new action();
							$action->open($id);
							$action->fields['is_model']=0;
							$action->save();

						}
						die();
						break;
					case "delete_actionetap":
							$id_actionetap= dims_load_securvalue('id_actionetap',dims_const::_DIMS_NUM_INPUT,true,true,false);
							$actionetap = new action_etap();
							if ($id_actionetap>0) {
									$actionetap->open($id_actionetap);
									$position=$actionetap->fields['position'];
									// update position
									$db->query("update dims_mod_business_event_etap set position=position-1 where position> :position and id_action= :idaction",
												array(':position' => $position, ':idaction' => $_SESSION['dims']['currentaction'])
												);
									$actionetap->delete();

									$sql = 'SELECT id FROM dims_mod_business_event_etap_file WHERE id_etape= :idetape';

									$ress = $db->query($sql, array(':idetape' => $id_actionetap) );

									if($db->numrows($ress) > 0)
									{
											$etap_file = new etap_file();
											while($f = $db->fetchrow($ress))
											{
													$etap_file->open($f['id']);
													$etap_file->delete();
											}
									}
							}
							//dims_redirect($scriptenv."?op=xml_planning_modifier_action&subaction="._DIMS_ACTION_ETAP);
					break;

					case "saveetap_position":
							// construction de la structure courante
							$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true,true);
							$move = dims_load_securvalue('move', dims_const::_DIMS_NUM_INPUT, true,true);

							$res=$db->query("select	* from dims_mod_business_event_etap where id_action= :idaction order by position", array(':idaction' => $_SESSION['dims']['currentaction']) );

							$tabcateg = array();
							$tabcategused = array();

							while ($mod=$db->fetchrow($res)) {
									$tabcateg[$mod['id']]=$mod;
							}

							$nb_etap = count($tabcateg);

							if($move == 1 && $tabcateg[$id_etap]['position'] < $nb_etap) { //up

									$db->query("UPDATE dims_mod_business_event_etap
												SET position=position-1
												WHERE position= :position
												AND id_action= :idaction
												ORDER BY position",
												array(':position' => ($tabcateg[$id_etap]['position']+1) , ':idaction' => $_SESSION['dims']['currentaction'])
												);
									$db->query("UPDATE dims_mod_business_event_etap
												SET position= :position
												WHERE id = :idetape
												AND id_action= :idaction
												ORDER BY position",
												array(':position' => ($tabcateg[$id_etap]['position'] + 1), ':idetape' => $id_etap ,':idaction' => $_SESSION['dims']['currentaction'])
												);
							}
							elseif($move == -1 && $tabcateg[$id_etap]['position'] > 1) { //down

									$db->query("UPDATE dims_mod_business_event_etap
												SET position=position+1
												WHERE position= :position
												AND id_action= :idaction
												ORDER BY position",
												array(':position' => ($tabcateg[$id_etap]['position']-1), ':idaction' => $_SESSION['dims']['currentaction'])
												);
									$db->query("UPDATE dims_mod_business_event_etap
												SET position= :position
												WHERE id = :idetape
												AND id_action=  :idaction
												ORDER BY position",
												array(':position' => ($tabcateg[$id_etap]['position'] - 1), ':idetape' => $id_etap, ':idaction' => $_SESSION['dims']['currentaction'])
												);
							}

							die();
					break;
					case 'make_payement_etap':
							$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);

							$etap = new action_etap();
							$etap->open($id_etap);

							$etap->fields['paiement'] = 1;

							$etap->save();

							break;
					case 'unmake_payement_etap':
							$id_etap = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true);

							$etap = new action_etap();
							$etap->open($id_etap);

							$etap->fields['paiement'] = 0;

							$etap->save();

							break;

					case 'save_eventfile':

							$id_etap = 0;

							$id_etap = dims_load_securvalue('id_etap',dims_const::_DIMS_NUM_INPUT,true,true,false);

							if($id_etap != null)
							{
									// enregistrement des docs
									// on va regarder ce qu'il y a dans le r�pertoire temporaire du user courant
									$sid = session_id();
									$upload_dir = realpath(DIMS_APP_PATH . '/data/uploads/'.$sid).'/';
									if (is_dir( realpath(DIMS_APP_PATH . '/data/uploads/'.$sid)) && is_dir($upload_dir)){
											if ($dh = opendir($upload_dir)){
													while (($filename = readdir($dh)) !== false){
															if ($filename!="." && $filename!=".."){
																	$docfile = new docfile();
																	$docfile->setvalues($_POST,'docfile_');
																	$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
																	$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
																	$docfile->fields['id_folder'] = -1;
																	$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
																	$docfile->tmpuploadedfile = $upload_dir.$filename;
																	$docfile->fields['name'] = $filename;
																	$docfile->fields['size'] = filesize($upload_dir.$filename);
																	$error = $docfile->save();

																	$id_doc=$docfile->fields['id'];

																	// on cr�� l'association entre le doc et l'etape
																	$etap_file = new etap_file();
																	$etap_file->fields['id_action']=$_SESSION['dims']['currentaction'];
																	$etap_file->fields['id_etape']=$id_etap;
																	$etap_file->fields['id_doc']=$id_doc;
																	$etap_file->save();
															}
													}
													closedir($dh);
													unset($_SESSION['dims']['currentetapfile']);
											}
											rmdir($upload_dir);
									}
							}
					break;

					case "search_ct" :
							ob_clean();
							ob_start();

							//recuperation du parametre
							$search = dims_load_securvalue('search', dims_const::_DIMS_CHAR_INPUT, true, true, true);
							$id_action = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
							$tab_not = '';

							//contact venant d'être ajout&eacute;s
							if(isset($_SESSION['dims']['planning_addct'])) {
									$tab_not .= implode("','",$_SESSION['dims']['planning_addct']);
							}
							//on regarde d'abbord tous les contacts inscrits
							if(isset($id_action) && $id_action != 0) {
									$sqlinsc = "SELECT	id_contact
												FROM	dims_mod_business_event_inscription
												WHERE	id_action = :idaction";

									$resinsc = $db->query($sqlinsc, array(':idaction' => $id_action) );
									if($db->numrows($resinsc) > 0) {
											$tab_i = '';
											while($tab_insc = $db->fetchrow($resinsc)) {
													$tab_i[] = $tab_insc['id_contact'];
											}
											$tab_not .= implode("','",$tab_i);
									}
							}
							//requete de recherche : chaine%
							$params = array(':search1' => "$search%", ':search2' => "$search%");
							$sql = 'SELECT		ct.id AS id_ct,
												ct.lastname,
												ct.firstname,
												ct.email AS email_ct,
												u.id,
												u.email AS email_user
									FROM		dims_mod_business_contact ct
									LEFT JOIN	dims_user u
									ON			u.id_contact = ct.id
									WHERE';
							if($tab_not != '')
									$sql .=		' ct.id NOT IN ( '.$db->getParamsFromArray(explode(',', $tab_not), 'nots', $params).' ) AND (';
							$sql .=			' ct.lastname LIKE :search1
													OR
															ct.firstname LIKE :search2';
							if($tab_not != '')
									$sql .=		')';
							$ress = $db->query($sql, $params );

							if($db->numrows($ress) > 0) { //On a des contact

									//R&eacute;cup&eacute;ration de la liste des user du workspace
									$ws= new workspace();
									$ws->open($_SESSION['dims']['workspaceid']);

									$lstusers=$ws->getusers();

									echo '<table style="width: 100%; border-collapse: collapse;">';
									$class = 'trl1';
									while($info = $db->fetchrow($ress)) {
											//Si le contact(Avec user) n'est pas d&eacute;jà dans la liste des WSusers
											if(!(isset($info['id']) && isset($lstusers[$info['id']]))) {
													$icon = '';
													$js = '';

													echo '<tr class="'.$class.'">';
													echo '<td>';
													$icon = '<img src="./data/users/icon_EFEFEF.png" alt="" border="0" />';
													echo $icon;
													echo '&nbsp;'.strtoupper(substr($info['firstname'],0,1)).'. '.$info['lastname'];
													echo '</td>';
													echo '<td>';

													if(isset($info['email_user']) && !empty($info['email_user']))
															echo $info['email_user'];
													elseif(isset($info['email_ct']) && !empty($info['email_ct']))
															echo $info['email_ct'];

													echo '</td>';
													echo '<td>';

													if((isset($info['email_ct']) && !empty($info['email_ct'])) ||
													   isset($info['email_user'])&& !empty($info['email_user'])) {
																	//$js = "document.location.href='admin.php?dims_mainemenu=".dims_const::_DIMS_MENU_PLANNING."&op=add_ct&id_ct=".$info['id_ct']."';";
																	$js = "dims_xmlhttprequest('admin.php', 'dims_mainemenu=events&action=add_ct&id_ct=".$info['id_ct']."');upKeysearch()";
													   }

													echo '<a href="javascript: void(0);" onclick="javascript: '.$js.'">';
													echo '<img src="./common/img/add_user.png" alt="'.$_DIMS['cste']['_ATTACH'].'" border="0" />';
													echo '</a>';
													echo '</td>';
													echo '</tr>';

													$class = ($class == 'trl1') ? 'trl2' : 'trl1';
											}
									}
									echo '</table>';
							}

							ob_end_flush();
							die();
					break;
					case "add_ct_event":
						$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_NUM_INPUT, true, true, true);
						$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT, true, true, true);
						$evt_insc = new event_insc();

						$evt_insc->fields['id_contact'] = $id_ct;
						$evt_insc->fields['id_action'] = $id_evt;
						$evt_insc->fields['validate'] = 2;

						$contact = new contact();
						$contact->open($id_ct);
						$evt_insc->fields['lastname'] = $contact->fields['lastname'];
						$evt_insc->fields['firstname'] = $contact->fields['firstname'];
						$evt_insc->save();

						break;
					case "add_ct":
									if(!isset($_SESSION['dims']['planning_addct'])) $_SESSION['dims']['planning_addct'] = array();
									$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_CHAR_INPUT, true, true, true);
									//on met les id_ct en session, il seront enregistr&eacute;s dans le save du planning
									$_SESSION['dims']['planning_addct'][] = $id_ct;
					break;

					case 'xml_planning_action_supprimer':

							$action = new action();
							if (!empty($action_id)) {
									$action->open($action_id);
									$action->delete();
							}

							dims_redirect($scriptenv);

					break;

					case 'xml_planning_disabled_action' :
							$retour = dims_load_securvalue('retour', dims_const::_DIMS_CHAR_INPUT, true, false);
							$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true);

							$action = new action();
							$action->open($action_id);
							$action->fields['close'] = 1;
							$action->fields['supportrelease'] = 0;
							$action->save();

							switch($retour) {
									case 'events':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_admin_events";
											break;
									case 'fairs':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_old_events";
											break;
									case 'models':
											//cette fonction n'est pas utilisée dans ce cas
											break;
							}
							dims_redirect($redirect);
					break;

					case 'xml_planning_active_action':
							$retour = dims_load_securvalue('retour', dims_const::_DIMS_CHAR_INPUT, true, false);
							$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true);

							$action = new action();
							$action->open($action_id);
							$action->fields['close'] = 0;
							$action->fields['supportrelease'] = 1;
							$action->save();

							switch($retour) {
									case 'events':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_admin_events";
											break;
									case 'fairs':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_old_events";
											break;
									case 'models':
											//cette fonction n'est pas utilisée dans ce cas
											break;
							}
							dims_redirect($redirect);

					break;

					case 'xml_planning_delete_action' :
							$retour = dims_load_securvalue('retour', dims_const::_DIMS_CHAR_INPUT, true, false);
							$action_id=dims_load_securvalue('action_id',dims_const::_DIMS_NUM_INPUT,true,true);
							$action = new action();

							$action->open($action_id);

							// verification des inscriptions
							$res=$db->query("select id, id_contact from dims_mod_business_event_inscription where id_action= :idaction", array(':idaction' => $action->fields['id']) );
							if ($db->numrows($res)<=1 ) { //on prend cette valeur car on rattache automatiquement le createur lors de la creation de l'event
									// on delete completement
									$action->delete();
							}
							else {
									$action->fields['close'] = 1;
									$action->save();
							}

							switch($retour) {
									case 'events':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_admin_events";
											break;
									case 'fairs':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_old_events";
											break;
									case 'models':
											$redirect = "admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_models_events";
											break;
							}
							dims_redirect($redirect);

					break;

					case 'create_model':
							$id_action = dims_load_securvalue('action_id', dims_const::_DIMS_NUM_INPUT, true, true);

							$act_tocopy = new action();
							$act_tocopy->open($id_action);

							//creation de l'action modele parente
							$newmod_act = new action();
							$newmod_act->init_description();

							$newmod_act->fields = $act_tocopy->fields;

							$newmod_act->fields['id'] = '';
							$newmod_act->fields['id_parent'] = 0;
							$newmod_act->fields['is_model'] = 1;
							$newmod_act->fields['libelle'] = $act_tocopy->fields['libelle'].'_model';
							$newmod_act->fields['datejour'] = date("Y")."-".date("m")."-".date("d"); //la date sera modifiee par l'utilisateur
							$newmod_act->fields['datefin'] = date("Y")."-".date("m")."-".date("d");
							$newmod_act->fields['allow_fo'] = 0; //on met à 0 ici, mais il faudra remettre à 1 lorsque l'on créera un event à partir de ce model
							$newmod_act->fields['id_user'] = $_SESSION['dims']['userid'];
							$newmod_act->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

							$newmod_act->save();

							//recuperation des informations sur les etapes et documents rattachés
							$sql_act_etap = "SELECT * FROM dims_mod_business_event_etap WHERE id_action = :idaction";

							$res_act_etap = $db->query($sql_act_etap, array(':idaction' => $id_action) );
							while($tab_actionetap = $db->fetchrow($res_act_etap)) {
									//on duplique l'etape
									$actionetap = new action_etap();
									$actionetap->fields = $tab_actionetap;
									$actionetap->fields['id'] = '';
									$actionetap->fields['id_action'] = $newmod_act->fields['id'];
									$actionetap->save();

									//on recherche les documents attaches
									$sql_doc_etap = "SELECT * FROM dims_mod_business_event_etap_file WHERE id_etape = :idetape AND id_action = :idaction";

									$res_doc_etap = $db->query($sql_doc_etap, array(':idetape' => $tab_actionetap['id'], ':idaction' => $id_action) );

									if($db->numrows($res_doc_etap) > 0) {
											while($tab_docetap = $db->fetchrow($res_doc_etap)) {
													$id_new_doc = 0;
													if($tab_docetap['id_doc'] > 0) {
															//on cree une copie du document
															$doc_tocopy = new docfile();
															$doc_tocopy->open($tab_docetap['id_doc']);

															$doc_tocp_path = $doc_tocopy->getfilepath();

															$newmodel_doc = new docfile();

															$newmodel_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
															$newmodel_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
															$newmodel_doc->fields['id_folder'] = -1;
															$newmodel_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
															$newmodel_doc->fields['name'] = $doc_tocopy->fields['name'];
															$newmodel_doc->fields['size'] = $doc_tocopy->fields['size'];

															copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
															$newmodel_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

															$newmodel_doc->save();
															$id_new_doc = $newmodel_doc->fields['id'];
													}
													//on duplique l'action_etap_file
													$model_etap_file = new etap_file();

													$model_etap_file->fields = $tab_docetap;

													$model_etap_file->fields['id'] = '';
													$model_etap_file->fields['id_action'] = $newmod_act->fields['id'];
													$model_etap_file->fields['id_etape'] = $actionetap->fields['id'];
													if($id_new_doc > 0) $model_etap_file->fields['id_doc'] = $id_new_doc;

													$model_etap_file->save();
											}
									}
							}
							dims_redirect("admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&dims_desktop=block&dims_action=public&action=view_models_events");
					break;

					case 'create_clone':
							$id_action = dims_load_securvalue('action_id', dims_const::_DIMS_NUM_INPUT, true, true);

							$act_tocopy = new action();
							$act_tocopy->open($id_action);

							//creation de l'action modele parente
							$newmod_act = new action();
							$newmod_act->init_description();

							$newmod_act->fields = $act_tocopy->fields;

							$newmod_act->fields['id'] = '';
							$newmod_act->fields['id_parent'] = 0;
							$newmod_act->fields['is_model'] = 0;
							$newmod_act->fields['libelle'] = $act_tocopy->fields['libelle'].' (copy)';
							$newmod_act->fields['datejour'] = date("Y")."-".date("m")."-".date("d"); //la date sera modifiee par l'utilisateur
							$newmod_act->fields['datefin'] = date("Y")."-".date("m")."-".date("d");
							$newmod_act->fields['id_user'] = $_SESSION['dims']['userid'];
							$newmod_act->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

							$newmod_act->save();

							//on recherche les docs attachés (brochures, bandeau, matchmaking)
					//le bandeau
							if(!empty($act_tocopy->fields['banner_path'])) {
									//on crée une copie de l'image
									//on reprend l'ancien nom de l'image
									$tab_imgpath = explode('/',$act_tocopy->fields['banner_path']);
									$img_name_with_ext = end($tab_imgpath);
									$tab_img = explode('.',$img_name_with_ext);
									$img_ext = end($tab_img);
									$img_name = $tab_img[0];

									//on ajoute des details pour identifier la copie
									$new_img_name = $img_name.'_copy_'.$newmod_act->fields['id'];

									//on cré la copie
									copy($act_tocopy->fields['banner_path'], DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext);

									//on indique le path dans le clone
									$newmod_act->fields['banner_path'] = DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext;
									$newmod_act->save();
							}
					//les documents liés / brochures
							$id_module=$_SESSION['dims']['moduleid'];
							$id_object=dims_const::_SYSTEM_OBJECT_EVENT;
							$id_record=$act_tocopy->fields['id'];

							require_once DIMS_APP_PATH.'include/functions/files.php';
							// collecte des fichiers existants
							$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record);
							if(count($lstfiles)>0) {
									foreach ($lstfiles as $file) {
											$id_new_doc = 0;
											if($file['id'] > 0) {
													//on cree une copie du document
													$doc_tocopy = new docfile();
													$doc_tocopy->open($file['id']);

													$doc_tocp_path = $doc_tocopy->getfilepath();

													$newmodel_doc = new docfile();

													$newmodel_doc->fields['id_module'] = $_SESSION['dims']['moduleid'];
													$newmodel_doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
													$newmodel_doc->fields['id_record'] = $newmod_act->fields['id'];
													$newmodel_doc->fields['id_object'] = $id_object;
													$newmodel_doc->fields['id_folder'] = -1;
													$newmodel_doc->fields['id_user_modify'] = $_SESSION['dims']['userid'];
													$newmodel_doc->fields['id_user'] = $_SESSION['dims']['userid'];
													$newmodel_doc->fields['name'] = $doc_tocopy->fields['name'];
													$newmodel_doc->fields['size'] = $doc_tocopy->fields['size'];

													copy($doc_tocp_path, DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension']);
													$newmodel_doc->tmpuploadedfile = DIMS_TMP_PATH . '/docmodel'.$doc_tocopy->fields['extension'];

													$newmodel_doc->save();

													$id_new_doc = $newmodel_doc->fields['id'];
											}
									}
							}

					//document d'annonce
							if(!empty($act_tocopy->fields['preview_path'])) {
									//on crée une copie de l'image
									//on reprend l'ancien nom de l'image
									$tab_imgpath = explode('/',$act_tocopy->fields['preview_path']);
									$img_name_with_ext = end($tab_imgpath);
									$tab_img = explode('.',$img_name_with_ext);
									$img_ext = end($tab_img);
									$img_name = $tab_img[0];

									//on ajoute des details pour identifier la copie
									$new_img_name = $img_name.'_copy_prev_'.$newmod_act->fields['id'];

									//on cré la copie
									copy($act_tocopy->fields['preview_path'], DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext);

									//on indique le path dans le clone
									$newmod_act->fields['preview_path'] = DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext;
									$newmod_act->save();
							}
					//matchmaking
							if(!empty($act_tocopy->fields['matchmaking_path'])) {
									//on crée une copie de l'image
									//on reprend l'ancien nom de l'image
									$tab_imgpath = explode('/',$act_tocopy->fields['matchmaking_path']);
									$img_name_with_ext = end($tab_imgpath);
									$tab_img = explode('.',$img_name_with_ext);
									$img_ext = end($tab_img);
									$img_name = $tab_img[0];

									//on ajoute des details pour identifier la copie
									$new_img_name = $img_name.'_copy_ma_'.$newmod_act->fields['id'];

									//on cré la copie
									copy($act_tocopy->fields['matchmaking_path'], DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext);

									//on indique le path dans le clone
									$newmod_act->fields['matchmaking_path'] = DIMS_APP_PATH . '/data/event_file/'.$new_img_name.'.'.$img_ext;
									$newmod_act->save();
							}

							dims_redirect('admin.php?action=xml_planning_modifier_action&id='.$newmod_act->fields['id']);
					break;
	}
}
//echo $skin->close_simplebloc();

?>
