<?php

include_once './include/class_urlbuilder.php';

dims_init_module('sharefile');
dims_init_module('doc');

$view = new view();
$view->setLayout('layout.tpl.php'); //déclaration du layout principal

$view->set_tpl_webpath('modules/sharefile/views/');
$view->set_static_version('78cb42ab416b19e741aa324c60448651fdfc8103');

$url = new dims_urlBuilder(dims::getInstance()->getScriptEnv());
if(!empty($articleid)) $url->addParam('articleid', $articleid);

$view->assign('urlbase', $url);

$moduleid=$obj['module_id'];

// recherche des params par defaut
$sharefile_param = new sharefile_param();
$sharefile_param->verifParam($moduleid);

$wcework=$dims->getWebWorkspaces();

if (!isset($op)) $op = '';
$op = dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,true,$op);
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,true);

switch($op) {
	default:
		if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
		else dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
		break;
	case 'sharefile':
		switch($action) {
			case 'connection':
				if($_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
				$view->assign('home', true);
				$view->render('connection.tpl.php');
				break;
			case 'main_menu':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$view->render('main_menu_header.tpl.php', 'header');

				$db = dims::getInstance()->getDb();
				$arrayshare=array();
				$tab_share=array();
				$tab_histo=array();

				$sqlshare= "SELECT s.*, u.lastname,u.firstname,count(f.id_doc) as cptefile
						FROM 		dims_mod_sharefile_share as s
						LEFT JOIN 	dims_user as u
						ON 			u.id = s.id_user
						LEFT JOIN	dims_mod_sharefile_file as f
						ON			f.id_share=s.id
						WHERE		s.deleted=0
						AND s.id_user= :userid
						AND s.id_module= :moduleid
						GROUP BY	s.id order by timestp_create desc";
				$res_s = $db->query($sqlshare,array(':userid' => $_SESSION['dims']['userid'], ':moduleid' => $moduleid));
				while($value = $db->fetchrow($res_s)) {
					$tab_share[$value['id']] = $value;
					$arrayshare[]=$value['id'];
				}

				// comptage des users
				if (sizeof($arrayshare)>0) {
					$params = array();
					$sql_r = "SELECT 	h.id_share,count( distinct h.id) as cpte
							FROM 		dims_mod_sharefile_history as h
							inner join	dims_mod_sharefile_user as u
							on			h.id_share in (".$db->getParamsFromArray($arrayshare, 'share', $params).")
							and			h.id_share=u.id_share
							and			h.id_user>0
							inner join	dims_user as du
							on			du.id=u.id_user
							group by	h.id_share";

					$res_r = $db->query($sql_r, $params);
					$tab_histo =array();
					while($value = $db->fetchrow($res_r)) {
						$tab_histo[$value['id_share']] = $value['cpte'];
					}
				}
				// contact
				if (sizeof($arrayshare)>0) {
					$params = array();
					$sql_r = "SELECT 	h.id_share,count( distinct h.id) as cpte
							FROM 		dims_mod_sharefile_history as h
							inner join	dims_mod_sharefile_user as u
							on			h.id_share in (".$db->getParamsFromArray($arrayshare, 'share', $params).")
							and			h.id_share=u.id_share
							and			h.id_contact>0
							inner join	dims_mod_business_contact as c
							on			c.id=u.id_contact
							group by	h.id_share";

					$res_r = $db->query($sql_r, $params);

					while($value = $db->fetchrow($res_r)) {
						if (isset($tab_histo[$value['id_share']])) $tab_histo[$value['id_share']]+=$value['cpte'];
						else $tab_histo[$value['id_share']] = $value['cpte'];
					}
				}

				$view->assign('tab_histo', $tab_histo);
				$view->assign('tab_share', $tab_share);
				$view->assign('home', true);

				$view->render('share/list.tpl.php');
				break;
		}
		break;
	case 'contacts':
		if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
		switch($action) {
			default:
			case 'list':
				$contacts = array();
				$sqlshare= 'SELECT	*
							FROM 	dims_mod_business_contact
							WHERE 	id_user = :userid
							AND 	private = 1
							ORDER BY lastname,firstname';

				$res = $db->query($sqlshare,array(':userid' => $_SESSION['dims']['userid']));

				while($value = $db->fetchrow($res)) {
					$contacts[] = $value;
				}

				$view->assign('contacts', $contacts);

				$view->render('contacts/list.tpl.php');
				$view->render('contacts/header.tpl.php', 'header');

				break;
			case 'add':
			case 'modify':
				$id_contact = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true, true);

				$contact = new contact();
				if(!empty($id_contact))
					$contact->open($id_contact);
				else
					$contact->init_description();

				$view->assign('contact', $contact->fields);
				$view->render('contacts/form.tpl.php');

				break;
			case 'save':
				$id_contact = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true, true);

				$contact = new contact();
				if(!empty($id_contact))
					$contact->open($id_contact);
				else {
					$contact->init_description();
					$contact->setugm();
				}

				$contact->fields['private'] = 1;

				$contact->setvalues($_POST, 'ct_');

				if(!empty($contact->fields['firstname']) && !empty($contact->fields['lastname']) && !empty($contact->fields['email'])) {
					$contact->save();

					dims_redirect($url->addParams(array('op' => 'contacts', 'action' => 'list')));
				}
				else {
					$view->assign('errors', dims_constant::getVal('PLEASE_VERIFY_FIELDS'));
					$view->assign('contact', $contact->fields);

					$view->render('contacts/form.tpl.php');
				}
				break;
			case 'delete':
				$id_contact = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true, true);

				if(!empty($id_contact)) {
					$contact = new contact();
					$contact->open($id_contact);

					$contact->delete();
				}

				dims_redirect($url->addParams(array('op' => 'contacts', 'action' => 'list')));
				break;
		}
		break;
	case 'share':
		switch($action) {
			default:
				dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
				break;
			case "add":
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				unset($_SESSION['share']['object']);

				$from_entity=dims_load_securvalue('from_entity',dims_const::_DIMS_NUM_INPUT,true,true);

				$share = new sharefile_share();
				$share->init_description();
				$share->fields['id_module'] = $moduleid;

				$_SESSION['share']['object'] = serialize($share);
				$_SESSION['share']['currentsearch'] = '';

				dims_redirect($url->addParams(array('op' => 'share', 'action' => 'first_step')));
				break;
			case 'first_step':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$share = unserialize($_SESSION['share']['object']);

				$view->assign('share', $share->fields);
				$view->assign('share_param', $sharefile_param->fields);
				$view->assign('step', 1);

				$view->render('share/first_step.tpl.php');
				$view->render('share/header.tpl.php', 'header');
				break;
			case 'save_first_step':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$share->setvalues($_POST, 'share_');
				if(empty($share->fields['timestp_finished'])) {
					$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
					$share->fields['timestp_finished'] = date('YmdHis',$maxtoday);
				}
				else {
					$share->fields['timestp_finished'] = dims_local2timestamp($share->fields['timestp_finished']);
				}

				if(empty($share->fields['label'])) {
					$view->assign('share', $share->fields);
					$view->assign('share_param', $sharefile_param->fields);
					$view->assign('step', 1);

					$view->render('share/first_step.tpl.php');
					$view->render('share/header.tpl.php', 'header');
				}
				else {
					$_SESSION['share']['object'] = serialize($share);
					dims_redirect($url->addParams(array('op' => 'share', 'action' => 'second_step')));
				}
				break;
			case 'second_step':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$share = unserialize($_SESSION['share']['object']);

				$view->assign('share', $share->fields);
				$view->assign('share_param', $sharefile_param->fields);
				$view->assign('step', 2);

				$view->render('share/second_step.tpl.php');
				$view->render('share/header.tpl.php', 'header');
				break;
			case 'search_user':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$share = unserialize($_SESSION['share']['object']);

				if (!isset($_SESSION['share']['currentsearch'])) $_SESSION['share']['currentsearch']="";
				$nomsearch = dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['share']['currentsearch']);

				$searchResult = array( 'groups' => array(), 'users' => array(), 'contacts' => array());

				include_once("./common/modules/system/class_user.php");

/*
				$dims_user= new user();
				$dims_user->open($_SESSION['dims']['userid']);

				$lstusers=$dims_user->getusersgroup($nomsearch,0,0);

				$lstuserssel=array();

				if (!empty($_SESSION['share']['users'])) $lstuserssel+=$_SESSION['share']['users'];


				if (!empty($lstusers) && strlen($nomsearch)>=2) {
					$sqlUsers =
						'SELECT 	user.id 		AS user_id,
									user.firstname 	AS user_firstname,
									user.lastname 	AS user_lastname,

									groupe.id 		AS group_id,
									groupe.label 	AS group_label

						FROM 		dims_user AS user

						LEFT JOIN 	dims_group_user AS group_user
						ON 			user.id = group_user.id_user

						LEFT JOIN 	dims_group 		AS groupe
						ON 			group_user.id_group = groupe.id

						WHERE 		user.id IN ('.implode(',',$lstusers).')';

					$res = $db->query($sqlUsers);

					if ($db->numrows($res)) {
						while ($data = $db->fetchrow($res)) {
							if(!$share->isUserLinked($data['user_id'])) {
								$searchResult['users'][$data['user_id']]['id'] 			= $data['user_id'];
								$searchResult['users'][$data['user_id']]['firstname'] 	= $data['user_firstname'];
								$searchResult['users'][$data['user_id']]['lastname'] 	= $data['user_lastname'];

								if(!empty($data['group_id'])) {
									$searchResult['users'][$data['user_id']]['group'][$data['group_id']] 	= $data['group_label'];
								}
							}
						}
					}
				}
*/

				$sqlContact =
					'SELECT		contact.id,
								contact.lastname,
								contact.firstname,
								user.id AS id_user
					FROM 		dims_mod_business_contact contact
					LEFT JOIN	dims_user user
					ON 			contact.id = user.id_contact
					WHERE 		(contact.id_user = :userid
					OR			contact.private = 0)
					AND			(contact.lastname LIKE :nomsearch1
					OR 			contact.firstname LIKE :nomsearch2
					OR	 		contact.email LIKE :nomsearch3 )';

				$res = $db->query($sqlContact,
					array(
						':userid' => $_SESSION['dims']['userid'],
						':nomsearch1' => "%$nomsearch%",
						':nomsearch2' => "%$nomsearch%",
						':nomsearch3' => "%$nomsearch%"
						)
					);

				if ($db->numrows($res)) {
					while ($data = $db->fetchrow($res)) {
						if(empty($searchResult['users'][$data['id_user']]) && !$share->isContactLinked($data['id'])) {
							$searchResult['contacts'][$data['id']]['id'] 		= $data['id'];
							$searchResult['contacts'][$data['id']]['firstname'] = $data['firstname'];
							$searchResult['contacts'][$data['id']]['lastname'] 	= $data['lastname'];
						}
					}
				}

				ob_end_clean();
				echo json_encode($searchResult);
				ob_end_flush();
				die();
				break;
			case 'add_user':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$share->addUser(dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true, true));

				$_SESSION['share']['object'] = serialize($share);
				die();
				break;
			case 'remove_user':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$share->removeUser(dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT, true, true, true));

				$_SESSION['share']['object'] = serialize($share);
				die();
				break;
			case 'add_contact':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$share->addContact(dims_load_securvalue('id_contact',dims_const::_DIMS_NUM_INPUT, true, true, true));

				$_SESSION['share']['object'] = serialize($share);
				die();
				break;
			case 'remove_contact':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$share->removeContact(dims_load_securvalue('id_contact',dims_const::_DIMS_NUM_INPUT, true, true, true));

				$_SESSION['share']['object'] = serialize($share);
				die();
				break;
			case 'get_participants':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$participants = array('users' => array(), 'contacts' => array());

				$participants['users'] = $share->getUsers();
				$participants['contacts'] = $share->getContacts();

				ob_end_clean();
				echo json_encode($participants);
				ob_end_flush();
				die();
				break;
			case 'save_add_contact':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				$contact = new contact();

				$contact->init_description();
				$contact->setugm();

				$contact->fields['private'] = 1;

				$contact->setvalues($_GET, 'ct_');

				if(!empty($contact->fields['firstname']) && !empty($contact->fields['lastname']) && !empty($contact->fields['email'])) {
					$contact->save();

					$share->addContact($contact->getId());
					$_SESSION['share']['object'] = serialize($share);
				}
				die();
				break;
			case 'third_step':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$share = unserialize($_SESSION['share']['object']);

				$view->assign('share', $share->fields);
				$view->assign('files', $share->getFiles());
				$view->assign('share_param', $sharefile_param->fields);
				$view->assign('step', 3);

				$view->render('share/third_step.tpl.php');
				$view->render('share/header.tpl.php', 'header');
				break;
			case 'delete_file':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$share = unserialize($_SESSION['share']['object']);

				$share->deleteFile(dims_load_securvalue('id_doc', dims_const::_DIMS_NUM_INPUT, true, true));

				$_SESSION['share']['object'] = serialize($share);
				dims_redirect($url->addParams(array('op' => 'share', 'action' => 'third_step')));
				break;
			case 'save_files':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = unserialize($_SESSION['share']['object']);

				// enregistrement des docs
				// creation du fichier zip unique
				ini_set('max_execution_time',0);
				ini_set('memory_limit',"2048M");

				// on va regarder ce qu'il y a dans le repertoire temporaire du user courant
				$sid = session_id();
				$upload_dir = realpath('./data/uploads/'.$sid).'/';
				if (is_dir( realpath('./data/uploads/'.$sid)) && is_dir($upload_dir)) {

					if ($dh = opendir($upload_dir)) {
						while (($filename = readdir($dh)) !== false) {
							if ($filename!="." && $filename!="..") {
								$docfile = new docfile();
								$docfile->setvalues($_POST,'docfile_');
								$docfile->fields['id_module'] = $moduleid;
								$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$docfile->fields['id_folder'] = -1;
								$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
								$docfile->tmpuploadedfile = $upload_dir.$filename;
								$docfile->fields['name'] = $filename;
								$docfile->fields['size'] = filesize($upload_dir.$filename);
								$error = $docfile->save();

								$id_doc=$docfile->fields['id'];

								$share->addFile($id_doc);
							}
						}
						closedir($dh);
					}

					rmdir($upload_dir);
				}

				$_SESSION['share']['object'] = serialize($share);

				dims_redirect($url->addParams(array('op' => 'share', 'action' => 'save_share')));
				break;
			case 'save_share':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				if(!isset($_SESSION['share']['object'])) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
				$share = unserialize($_SESSION['share']['object']);

				$share->save();

				// envoi de l'email
				include_once "./include/functions/mail.php";

				// Construction du contenu de l'email
				$usercreate = new user();
				$usercreate->open($_SESSION['dims']['userid']);

				$properties = $share->getFilesProperties();

				$datecreate= dims_timestamp2local($share->fields['timestp_create']);

				$workspace = new workspace();
				$workspace->open($_SESSION['dims']['workspaceid']);
				if ($workspace->fields['email']!='') {
					$from[0] = array(
						'address' => $workspace->fields['email'],
						'name'  => $workspace->fields['email']
					);
				}
				else {
					$from[0] = array(
						'address'=> $usercreate->fields['email'],
						'name'  => $usercreate->fields['email']
					);
				}

				if (substr($_SERVER['SERVER_PROTOCOL'],0,5)!="HTTP/") $url->setPort('443')->setProtocol('https://');

				$nbfiles=$properties['nbfiles'];
				//{{FIRSTNAME} {LASTNAME} {EMAIL} {URL} {NBFILES} {DATE_END} {SHARE_NAME} {DATE_CREATE} {CODE} {WEIGHT}

				$elem = array('{FIRSTNAME}','{LASTNAME}','{NBFILES}','{EMAIL}','{URL}','{DATE_END}','{SHARE_NAME}','{DATE_CREATE}','{CODE}','{WEIGHT}');

				$poids=sprintf("%.02f",$properties['size']/1048576);
				$datecreate=$datecreate['date'];

				if ($share->fields['timestp_finished']>0) $datefin= dims_timestamp2local($share->fields['timestp_finished']);
				else $datefin['date']="";

				if ($share->fields['code']!="") $code=$share->fields['code'];
				else $code="";

				foreach($share->getUsers() as $u) {
					$emailto=$u['email'];

					if ($u['code']!="") $code=$u['code'];

					// construction du lien
					$link='<a href="'.dims_urlencode($url->addParams(array('op' => 'share', 'action' => 'view', 'id_share' => $share->fields['id'], 'id_user' => $u['id']))).'">Acc&eacute;der au partage</a>';

					//$newcontent.="<tr><td colspan=\"2\">".$link."</td></tr></table></body></html>";
					$elemby = array(
						$usercreate->fields['firstname'],
						$usercreate->fields['lastname'],
						$nbfiles,
						$usercreate->fields['email'],
						$link,
						$datefin['date'],
						$share->fields['label'],
						$datecreate,
						$code,
						$poids
					);

					$title=str_replace($elem,$elemby,$sharefile_param->fields['email_title']);
					$message=str_replace($elem,$elemby,$sharefile_param->fields['send_message']);
					// envoi de l'email
					dims_send_mail($from,$emailto,$title,$message);
				}

				foreach($share->getContacts() as $u) {
					$emailto=$u['email'];

					if ($u['code']!="") $code=$u['code'];

					// construction du lien
					$link='<a href="'.dims_urlencode($url->addParams(array('op' => 'share', 'action' => 'view', 'id_share' => $share->fields['id'], 'id_contact' => $u['id']))).'">Acc&eacute;der au partage</a>';

					$elemby = array(
						$usercreate->fields['firstname'],
						$usercreate->fields['lastname'],
						$nbfiles,
						$usercreate->fields['email'],
						$link,
						$datefin['date'],
						$share->fields['label'],
						$datecreate,
						$code,
						$poids);
					$title=str_replace($elem,$elemby,$sharefile_param->fields['email_title']);
					$message=str_replace($elem,$elemby,$sharefile_param->fields['send_message']);

					// envoi de l'email
					dims_send_mail($from,$emailto,$title,$message);
				}

				$view->assign('message', $sharefile_param->fields['message']);

				$view->render('share/recap_share.tpl.php');

				unset($_SESSION['share']['object']);
				break;
			case 'delete':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = new sharefile_share();
				$share->open(dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true));

				$share->delete();

				dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
				break;
			case 'duplicate':
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = new sharefile_share();
				$share->open(dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true));

				// controle de securite pour la propriete du partage
				if ($share->isOwner($_SESSION['dims']['userid'])) {
					$newshare = clone $share;
					$newshare->fields['id_module'] = $moduleid;

					$_SESSION['share']['object'] = serialize($newshare);
					$_SESSION['share']['currentsearch'] = '';
					dims_redirect($url->addParams(array('op' => 'share', 'action' => 'first_step')));
				}
				else {
					dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
				}
				break;

			case "stats":
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));
				$share = new sharefile_share();
				$share->open(dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true));

				if ($share->isOwner($_SESSION['dims']['userid']) || dims_isadmin()) {
					$tab_histo=array();

					// compte des users
					$sql_r = '	SELECT 	h.id_user,h.timestp_create
								FROM 	dims_mod_sharefile_history as h
								WHERE 	h.id_share = :idshare
								AND 	h.id_user>0';

					$res_r = $db->query($sql_r, array(':idshare' => $share->getId() ));

					while($value = $db->fetchrow($res_r)) {
						if (isset($tab_histo['users'][$value['id_user']])) {
							if ($tab_histo['users'][$value['id_user']]['timestp_create']<$value['timestp_create'])
								$tab_histo['users'][$value['id_user']]['timestp_create']=$value['timestp_create'];

							$tab_histo['users'][$value['id_user']]['cpte']++;
						}
						else {
							$value['cpte']=1;
							$tab_histo['users'][$value['id_user']] = $value;
						}
					}

					// compte des contacts
					$sql_r = '	SELECT 	h.id_contact,h.timestp_create
								FROM 	dims_mod_sharefile_history as h
								WHERE 	h.id_share = :idshare
								AND 	h.id_contact>0';

					$res_r = $db->query($sql_r, array(':idshare' => $share->getId() ));

					while($value = $db->fetchrow($res_r)) {
						if (isset($tab_histo['contacts'][$value['id_contact']])) {
							if ($tab_histo['contacts'][$value['id_contact']]['timestp_create']<$value['timestp_create'])
								$tab_histo['contacts'][$value['id_contact']]['timestp_create']=$value['timestp_create'];

							$tab_histo['contacts'][$value['id_contact']]['cpte']++;
						}
						else {
							$value['cpte']=1;
							$tab_histo['contacts'][$value['id_contact']] = $value;
						}
					}

					$view->assign('share', $share->fields);
					$view->assign('sharefile_param', $sharefile_param->fields);
					$view->assign('users', $share->getUsers());
					$view->assign('contacts', $share->getContacts());
					$view->assign('tab_histo', $tab_histo);

					$view->render('share/stats.tpl.php');
				}
				else {
					dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'main_menu')));
				}
				break;
			case "unlock_account":
				if(!$_SESSION['dims']['connected']) dims_redirect($url->addParams(array('op' => 'sharefile', 'action' => 'connection')));

				$shareuser = new sharefile_user();
				$shareuser->open(dims_load_securvalue("id_shareuser",dims_const::_DIMS_NUM_INPUT,true,true));

				$share = new sharefile_share();
				$share->open($shareuser->fields['id_share']);

				// verif
				if (dims_isadmin() || $share->isOwner($_SESSION['dims']['userid'])) {
					$shareuser->fields['view']=0;
					$shareuser->save();
					// avec envoi d'email

					include_once "./include/functions/mail.php";

					// Construction du contenu de l'email
					$usercreate = new user();
					$usercreate->open($_SESSION['dims']['userid']);

					$properties = $share->getFilesProperties();
					$datecreate= dims_timestamp2local($share->fields['timestp_create']);

					$from=array();

					$from[0] = array(
						'address' 	=> $usercreate->fields['email'],
						'name' 	=> $usercreate->fields['email']
					);

					if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") {
						$urlpath = "http://".$_SERVER['HTTP_HOST']."/";
					}
					else $urlpath = "https://".$_SERVER['HTTP_HOST']."/";

					$nbfiles=$properties['nbfiles'];
					//{{FIRSTNAME} {LASTNAME} {EMAIL} {URL} {NBFILES} {DATE_END} {SHARE_NAME} {DATE_CREATE} {CODE} {WEIGHT}

					$elem = array('{FIRSTNAME}','{LASTNAME}','{NBFILES}','{EMAIL}','{URL}','{DATE_END}','{SHARE_NAME}','{DATE_CREATE}','{CODE}','{WEIGHT}');

					$poids=sprintf("%.02f",$properties['size']/1048576);
					$datecreate=$datecreate['date'];

					if ($share->fields['timestp_finished']>0) $datefin= dims_timestamp2local($share->fields['timestp_finished']);
					else $datefin['date']="";

					if ($share->fields['code']!="") $code=$share->fields['code'];
					else $code="";

					if ($shareuser->fields['id_user']>0) {
						//recherche des noms prenom et email,
						$res=$db->query("SELECT DISTINCT u.id, u.email, u.firstname,u. lastname,fu.code
										FROM dims_user AS u
										LEFT JOIN dims_mod_sharefile_user AS fu
										ON fu.id_user=u.id
										AND fu.id_share= :idshare
										WHERE u.id = :userid",
										array(':idshare' => $share->fields['id'], ':userid' =>$shareuser->fields['id_user'] )
										);
						if ($db->numrows($res)>0) {
							while ($u=$db->fetchrow($res)) {
								$emailto=$u['email'];

								if ($u['code']!="") $code=$u['code'];

								// construction du lien
								$link='<a href='.dims_urlencode($url->addParams(array('op' => 'share', 'action' => 'view', 'id_share' => $share->fields['id'], 'id_user' => $u['id']))).'">Acc&eacute;der au partage</a>';

								//$newcontent.="<tr><td colspan=\"2\">".$link."</td></tr></table></body></html>";
								$elemby = array(
									$usercreate->fields['firstname'],
									$usercreate->fields['lastname'],
									$nbfiles,
									$usercreate->fields['email'],
									$link,
									$datefin['date'],
									$share->fields['label'],
									$datecreate,
									$code,
									$poids
								);

								$title=str_replace($elem,$elemby,$sharefile_param->fields['email_title']);
								$content=str_replace($elem,$elemby,$sharefile_param->fields['send_message']);
								// envoi de l'email
								dims_send_mail($from,$emailto,$title,$content);
							}
						}
					}
					else {
						// on a un contact
						$res=$db->query("SELECT DISTINCT c.id, c.email, c.firstname,c.lastname,fu.code
										FROM dims_mod_business_contact AS c
										LEFT JOIN dims_mod_sharefile_user AS fu
										ON fu.id_contact=c.id
										WHERE fu.id_share= :idshare
										AND c.id_user= :userid
										AND c.id = :idcontact ",
										array(':idshare' => $share->fields['id'], ':userid' => $_SESSION['dims']['userid'], ':idcontact' => $shareuser->fields['id_contact'])
										);
						if ($db->numrows($res)>0) {
							while ($u=$db->fetchrow($res)) {
								$emailto=$u['email'];

								if ($u['code']!="") $code=$u['code'];

								// construction du lien
								$link='<a href="'.dims_urlencode($url->addParams(array('op' => 'share', 'action' => 'view', 'id_share' => $share->fields['id'], 'id_contact' => $u['id']))).'">Acc&eacute;der au partage</a>';

								$elemby = array(
									$usercreate->fields['firstname'],
									$usercreate->fields['lastname'],
									$nbfiles,
									$usercreate->fields['email'],
									$link,
									$datefin['date'],
									$share->fields['label'],
									$datecreate,
									$code,
									$poids
								);

								$title=str_replace($elem,$elemby,$sharefile_param->fields['email_title']);
								$content=str_replace($elem,$elemby,$sharefile_param->fields['send_message']);

								// envoi de l'email
								dims_send_mail($from,$emailto,$title,$content);
							}
						}
					}
				}

				dims_redirect($url->addParams(array('op' => 'share', 'action' => 'stats', 'id_share' => $share->fields['id'])));

				break;

			case 'file_download':
				include_once './include/class_dims_data_object.php';
				include_once './include/functions/date.php';
				include_once './include/functions/filesystem.php';
				include_once './common/modules/doc/include/global.php';
				include_once './common/modules/doc/class_docfile.php';

				if (!isset($_SESSION['currentshare']['id_share'])) $_SESSION['currentshare']['id_share']=0;
				if (!isset($_SESSION['currentshare']['id_user'])) $_SESSION['currentshare']['id_user']=0;
				if (!isset($_SESSION['currentshare']['id_contact'])) $_SESSION['currentshare']['id_contact']=0;

				$id_share=dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_share']);
				$id_user=dims_load_securvalue("id_user",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_user']);
				$id_contact=dims_load_securvalue("id_contact",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_contact']);

				if (!isset($usercode)) $usercode="";

				if ($_SESSION['currentshare']['id_share']==0) {
					die();
				}
				else {
					$share = new sharefile_share();
					$share->open($id_share);

					// code active
					// controle si date deja d�pass�e ou non
					$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
					$dateday=date('d/m/Y',$maxtoday);
					$maxtoday=dims_local2timestamp($dateday);

					// verification de l'existance du partage
					if ($share->fields['deleted']) {
						$view->assign('message', dims_constant::getVal('ERROR_SHARE_DELETED'));
						$view->render('share/message.tpl.php');
					}
					// verification du code
					elseif (($share->fields['code']!="" && $share->fields['code']!=$_SESSION['sharecodes'][$id_share]) || ($usercode!="" && $usercode!=$_SESSION['sharecodes'][$id_share]) ) {
						die('Security exception');
					}
					elseif ($share->fields['timestp_finished']>$maxtoday && !isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid'])) {
						$view->assign('message', dims_constant::getVal('ERROR_SHARE_MAXDATE'));
						$view->render('share/message.tpl.php');
					}
					else {
						// active par défaut le code vierge
						if (!isset($_SESSION['sharecodes'][$id_share])) $_SESSION['sharecodes'][$id_share]="";

						// on peut etre ici
						if (!empty($_GET['docfile_md5id'])) {
							$resultat=$share->verifAccessFile($_GET['docfile_md5id']);
							if ($resultat) {
								// on peut downloader le fichier
								$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5 ",array(':md5'=>addslashes($_GET['docfile_md5id']) ));
								if ($fields = $db->fetchrow($res)) {

									$docfile = new docfile();
									$docfile->open($fields['id']);

									// vérification du partage
									if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
									elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);

								}
							}
						}
					}
				}

				die();
				break;
			case 'view':

				if (!isset($_SESSION['currentshare']['id_share'])) $_SESSION['currentshare']['id_share']=0;
				if (!isset($_SESSION['currentshare']['id_user'])) $_SESSION['currentshare']['id_user']=0;
				if (!isset($_SESSION['currentshare']['id_contact'])) $_SESSION['currentshare']['id_contact']=0;

				$id_share = dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_share']);
				$id_user = dims_load_securvalue("id_user",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_user']);
				$id_contact = dims_load_securvalue("id_contact",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_contact']);
				$usercode = '';

				if (empty($id_share)) {
					die();
				}
				else {
					$share = new sharefile_share();
					$share->open($id_share);

					$enabled = $share->isEnabled($id_user,$id_contact,$usercode) || $share->isOwner($_SESSION['dims']['userid'] || dims_isadmin());

					// active par défaut le code vierge
					if (!isset($_SESSION['sharecodes'][$id_share])) $_SESSION['sharecodes'][$id_share]="";

					// verification de l'existance du partage
					if ($share->fields['deleted']) {
						$view->assign('message', dims_constant::getVal('ERROR_SHARE_DELETED'));
						$view->render('share/message.tpl.php');
					}
					elseif ($enabled) {
						// verification du code
						if (isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid'])) {
							if (($share->fields['code']!="" && $share->fields['code']!=$_SESSION['sharecodes'][$id_share]) || ($usercode!="" && $usercode!=$_SESSION['sharecodes'][$id_share]) ) {
								dims_redirect($url->addParams(array('op' => 'share', 'action' => 'sharefile_codecheck', 'id_share' => $share->getId())));
							}
						}

						// code active
						// controle si date deja dépassée ou non
						$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
						$dateday=date('d/m/Y',$maxtoday);
						$maxtoday=dims_local2timestamp($dateday);

						if ($share->fields['timestp_finished']>$maxtoday && !isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid'])) {
							$view->assign('message', dims_constant::getVal('ERROR_SHARE_MAXDATE'));
							$view->render('share/message.tpl.php');
						}
						// controle du nombre de consultation
						//elseif (!isset($_SESSION['dims']['userid']) || (isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid']))) {
						else {
							if (!isset($_SESSION['sharecount'][$id_share]) && !$share->isOwner($_SESSION['dims']['userid'])) {
								// on le crée en comptant au préalable le nbre de download
								if ($_SESSION['currentshare']['id_user']>0)
									$res=$db->query("SELECT *
													FROM dims_mod_sharefile_user
													WHERE id_share= :idshare
													AND id_user= :userid",array(':idshare' => $share->fields['id'], ':userid' => $_SESSION['currentshare']['id_user']));
								else
									$res=$db->query("SELECT *
													FROM dims_mod_sharefile_user
													WHERE id_share= :idshare
													AND id_contact= :idcontact",array(':idshare' => $share->fields['id'], ':idcontact' => $_SESSION['currentshare']['id_contact']));

								$cpte=0;
								$idfileuser=0;

								if ($db->numrows($res)>0) {
									$f=$db->fetchrow($res);
									$cpte=$f['view'];
									$idfileuser=$f['id'];
								}

								if ($cpte<$sharefile_param->fields['nbdownload'] || $sharefile_param->fields['nbdownload']==0) {
									$file_user = new sharefile_user();

									if ($idfileuser>0) {
										$file_user->open($idfileuser);
										$file_user->fields['view']++;
										$file_user->save();
									}
									$cpte++;
									// creation du log de consultation
									$share->createHistory($_SESSION['currentshare']['id_user'],$_SESSION['currentshare']['id_contact']);
								}
								$_SESSION['sharecount'][$id_share]=$cpte;
							}

							if((isset($_SESSION['sharecount'][$id_share]) && $_SESSION['sharecount'][$id_share] < $sharefile_param->fields['nbdownload']) || $sharefile_param->fields['nbdownload'] == 0 || $share->isOwner($_SESSION['dims']['userid'])) {
								// date de création
								$datecreate = dims_timestamp2local($share->fields['timestp_create']);

								$user = new user();
								$user->open($share->fields['id_user']);

								$view->assign('sharefile_param', $sharefile_param->fields);

								$view->assign('files', $share->getFiles());
								$view->assign('share', $share->fields);
								$view->assign('user', $user->fields);
								$view->assign('properties', $share->getFilesProperties());

								$view->render('share/view.tpl.php');
							}
							else {
								$view->assign('message', dims_constant::getVal('ERROR_SHARE_MAXDOWNLOAD'));
								$view->render('share/message.tpl.php');
							}
						}
					}
					else dims_redirect($url->addParams(array('op' => 'share', 'action' => 'sharefile_codecheck', 'id_share' => $share->getId())));
				}
				break;
			case "sharefile_maxdate":
				include_once("./common/modules/sharefile/sharefile_maxdate.php");
				break;
			case "sharefile_maxdownload":
				include_once("./common/modules/sharefile/sharefile_maxdownload.php");
				break;
			case "sharefile_deleted":
				include_once("./common/modules/sharefile/sharefile_deleted.php");
				break;
			case "sharefile_codecheck":
				$id_share=dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true);

				if (!empty($id_share)) {
					// verification du code
					$share = new sharefile_share();
					$share->open($id_share);

					$usercode = '';

					$enabled=$share->isEnabled($_SESSION['currentshare']['id_user'],$_SESSION['currentshare']['id_contact'],$usercode);

					$error_check=false;
					if ($enabled) {
						$view->assign('id_share', $id_share);
						$view->render('share/codecheck.tpl.php');
					}
					else {
						$view->render('share/message.tpl.php');
					}
				}
				else {
					$view->render('share/message.tpl.php');
				}
				break;
			case "sharefile_codecheck_form":
				$sharefile_codecheck = dims_load_securvalue("sharefile_codecheck",dims_const::_DIMS_CHAR_INPUT,true,true);
				$id_share = dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true);

				if(!empty($id_share) && !empty($sharefile_codecheck)) {
					$share = new sharefile_share();

					$share->open($id_share);

					$usercode = '';

					$enabled = $share->isEnabled($_SESSION['currentshare']['id_user'], $_SESSION['currentshare']['id_contact'], $usercode);

					if ($enabled && ((!empty($share->fields['code']) && $share->fields['code'] == $sharefile_codecheck) || (!empty($usercode) && $usercode == $sharefile_codecheck))) {
						$_SESSION['sharecodes'][$id_share] = $sharefile_codecheck;
						dims_redirect($url->addParams(array('op' => 'share', 'action' => 'view')));
					}
					else {
						$view->assign('error', dims_constant::getVal('CODE_CHECK_ERROR'));
						$view->assign('id_share', $id_share);
						$view->render('share/codecheck.tpl.php');
					}
				}
				else {
					$view->assign('error', dims_constant::getVal('CODE_CHECK_ERROR'));
					$view->assign('id_share', $id_share);
					$view->render('share/codecheck.tpl.php');
				}
				break;
		}
		break;
}

$view->compute();

?>

