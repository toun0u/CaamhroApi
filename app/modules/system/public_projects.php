<?php

/**********************************************************
***********************************************************
*** @author	Arnaud KNOBLOCH [NETLOR CONCEPT]		***
*** @author	Patrick NOURRISSIER [NETLOR CONCEPT]	***
*** @author	Florian DAVOINE		[NETLOR CONCEPT]	***
*** @version	1.2						***
*** @package	projects					***
*** @access	public						***
*** @licence	GPL						***
***********************************************************
***********************************************************/

/* On 'charge' les fonctions/scripts et les objets dont on aura besoin */
define ("_DIMS_CSTE_CURRENTPROJECT",		0);
define ("_DIMS_CSTE_MILESTONE",				1);
define ("_DIMS_CSTE_TASK",					2);
define ("_DIMS_CSTE_ACTION",				3);
define ("_DIMS_CSTE_ADDPROJECT",			4);
define ("_DIMS_CSTE_ANNOT",					5);
define ("_DIMS_CSTE_GANTT",					6);
define ("_DIMS_CSTE_ADDTASK",				7);
define ("_DIMS_CSTE_USERAFFECT",			8);
define ("_DIMS_CSTE_PROPERTIES",			9);
define ("_DIMS_CSTE_PHASE",					10);
define ("_DIMS_CSTE_DOC",					11);
define ("_DIMS_CSTE_PERS_CONC",				12);

require_once DIMS_APP_PATH . '/modules/system/class_project.php';
require_once DIMS_APP_PATH . '/modules/system/class_task.php';
require_once DIMS_APP_PATH . '/modules/system/class_action.php';
require_once DIMS_APP_PATH . '/modules/system/class_action_user.php';

require_once DIMS_APP_PATH . '/modules/system/include/projects_functions.php';

require_once DIMS_APP_PATH . '/modules/system/class_milestone.php';

require_once DIMS_APP_PATH . '/modules/system/class_task_task.php';
require_once DIMS_APP_PATH . '/modules/system/class_task_user.php';
require_once(DIMS_APP_PATH . "/modules/system/class_project_user.php");
require_once (DIMS_APP_PATH . '/include/functions/tickets.php');
require_once (DIMS_APP_PATH . '/include/functions/mail.php');

echo '<script language="Javascript" src="./common/modules/system/include/projects_functions.js"> </script>';

/* On regarde la parametre du filtre */
if (isset($_GET['filtertype'])) $_SESSION['projects']['filtertype'] = dims_load_securvalue('filtertype', dims_const::_DIMS_CHAR_INPUT, true, true, true);
if (!isset($_SESSION['projects']['filtertype'])) $_SESSION['projects']['filtertype'] = 'all';
if (!isset($_SESSION['dims']['currentproject'])) $_SESSION['dims']['currentproject']=0;
if (!isset($_SESSION['dims']['currenttask'])) $_SESSION['dims']['currenttask']=0;
if (!isset($_SESSION['dims']['currentphase'])) $_SESSION['dims']['currentphase']=0;
if (!isset($_SESSION['project']['zoom'])) $_SESSION['project']['zoom']="m";

$filtertype = $_SESSION['projects']['filtertype'];

/* project */
if (isset($_GET['idproject']) && $_GET['idproject']==-1) unset($_SESSION['dims']['currentproject']);
$idprojet=dims_load_securvalue('idproject',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['currentproject']);
$project = new project();
if ($_SESSION['dims']['currentproject']>0 && $op!="add_project") $project->open($_SESSION['dims']['currentproject']);
else $project->init_description();

/* task */
if (isset($_GET['idtask']) && $_GET['idtask']==-1) unset($_SESSION['dims']['currenttask']);
$idtask = dims_load_securvalue('idtask',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['currenttask']);

$task = new task();
if ($_SESSION['dims']['currenttask']>0 && $op!="task_add") $task->open($_SESSION['dims']['currenttask']);
else $task->init_description();

/* phase */
if (isset($_GET['idphase']) && $_GET['idphase']==-1) unset($_SESSION['dims']['currentphase']);
$idphase = dims_load_securvalue('idphase',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['currentphase']);


$task = new task();
if ($_SESSION['dims']['currentphase']>0 && $op!="phase_add") $task->open($_SESSION['dims']['currentphase']);
else $task->init_description();

/* On regarde le parametre du trie */
if (isset($_GET['sort']))
	$_SESSION['projects']['sort'] = dims_load_securvalue('sort', dims_const::_DIMS_CHAR_INPUT, true, true, true);

if (!isset($_SESSION['projects']['sort']))
	$_SESSION['projects']['sort'] = 'name';

$sort = $_SESSION['projects']['sort'];

/* Permet de changer l'orde d'affichage du trie si on click une seconde fois sur le meme trie */
if (!isset($order)) $order = 'desc';
if (!isset($op)) $op = '';

if ($order=="asc") {
	$order="desc";
} else {
	$order="asc";
}

//fonction javascript pour suppression de doc
?>
<script language="javascript" type="text/javascript">
	function delete_doc(id_doc) {
		if(confirm("<?php echo $_DIMS['cste']['_PROJET_CONFIRM_DELDOC']; ?>")) {
			dims_xmlhttprequest('admin.php','op=delete_file&id_doc='+id_doc);
			document.location.reload();
		}
	}
</script>
<?php

/* On regarde quelle action doit etre faite */
switch ($op) {
	case 'task_del_tiers':

		$id_task = dims_load_securvalue('id_task', dims_const::_DIMS_NUM_INPUT, true, true);

		$task = new task();
		$task->open($id_task);

		$task->fields['id_tiers'] = '';

		$task->save();

		dims_redirect($scriptenv);

		break;
	case 'search_linktoadd':
		ob_end_clean();

		$cont_search = dims_load_securvalue('search_name', dims_const::_DIMS_CHAR_INPUT, true, true);
		$type = dims_load_securvalue('type_search', dims_const::_DIMS_CHAR_INPUT, true, true);

		$sql_tiers = "SELECT id, intitule FROM dims_mod_business_tiers WHERE intitule LIKE :intitule AND inactif != 1 ORDER BY intitule";
		//echo $sql_tiers;

		$cont = $db->query($sql_tiers, array(
			':intitule' => array('type' => PDO::PARAM_STR, 'value' => '%'.$cont_search.'%'),
		));
	$nb_rep = $db->numrows($cont);

		$retour = '<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>';

		if($nb_rep>0) {
			$retour .= '<table width="100%">
							<tr>
								<td colspan="3" align="center">
									<select id="task_id_tiers" name="task_id_tiers">
										<option>--</option>';
				while($list_cont = $db->fetchrow($cont)) {

						$option = '<option value="'.$list_cont['id'].'">'.$list_cont['intitule'].'</option>';

					$retour .= $option;
				}
			$retour .= '			</select>
								</td>
							</tr>
						</table>';
		}
		else {
			$retour .= '<p style="font-size:14px;">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</p>';
		}
		$retour .= '</td></tr></table>';

		echo $retour;


		die();
		break;
	case 'ticket_dmd_participation':
		$id_proj = dims_load_securvalue('id_proj',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_user = dims_load_securvalue('id_dmdeur',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_task = dims_load_securvalue('id_task',dims_const::_DIMS_NUM_INPUT,true,true,false);

		$proj = new project();
		$proj->open($id_proj);

		$task = new task();
		$task->open($id_task);

		$dmdeur = new user();
		$dmdeur->open($id_user);


		$_SESSION['dims']['tickets']['users_selected'][0] = $proj->fields['id_create'];
		$_SESSION['dims']['tickets']['users_selected'][1] = $proj->fields['id_resp'];

		$title = $_DIMS['cste']['_DIMS_TITLE_PROJ_TICKET_DMD_INSC'];

		$message = "Bonjour,<br/>";
		$message .= $dmdeur->fields['firstname']." ".$dmdeur->fields['lastname']." souhaite participer &agrave la t&acirc;che \"".$task->fields['label']."\"
					issue du projet \"".$proj->fields['label']."\".<br/>
					Vous pouvez valider son inscription en le s&eacute;lectionnant dans la liste des personnes concern&eacute;es de la t&acirc;che \"".$task->fields['label']."\".";

		dims_tickets_send($title, $message, 1, '', '', dims_const::_SYSTEM_OBJECT_PROJECT, $id_task, '','');

	break;
	//Apel AJAX : Suppresion de documents
	case 'delete_file':
		require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
		$id_file = dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true,false);

		if($id_file != '') {
			$doc = new docfile();
			$doc->open($id_file);
			$doc->delete();
		}

		break;



	case "project_initsearch":
			ob_end_clean();
			if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$_SESSION['project']['currentsearch']="";
			}
			die();
			break;
	case "deleteActionUser":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$id_user= dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_user>0) {
				if (isset($_SESSION['project']['users'][$id_user])) unset($_SESSION['project']['users'][$id_user]);
			}
		}
		die();
		break;
	case "addActionUser":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$id_user= dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_user>0) {
				if (!isset($_SESSION['project']['users'][$id_user])) $_SESSION['project']['users'][$id_user]=$id_user;
			}
		}
		die();
		break;
	case "deleteActionGroup":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$id_grp= dims_load_securvalue('id_grp',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_grp>0) {
				if (isset($_SESSION['project']['groups'][$id_grp])) unset($_SESSION['project']['groups'][$id_grp]);
			}
		}
		die();
		break;
	case "addActionGroup":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$id_grp= dims_load_securvalue('id_grp',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_grp>0) {
				if (!isset($_SESSION['project']['groups'][$id_grp])) $_SESSION['project']['groups'][$id_grp]=$id_grp;
			}
		}
		die();
		break;
	case "deleteActionContact":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$id_contact= dims_load_securvalue('id_contact',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_contact>0) {
				if (isset($_SESSION['project']['contacts'][$id_contact])) unset($_SESSION['project']['contacts'][$id_contact]);
			}
		}
		die();
		break;

	case "addActionContact":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$id_contact= dims_load_securvalue('id_contact',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_contact>0) {
				if (!isset($_SESSION['project']['contacts'][$id_contact])) $_SESSION['project']['contacts'][$id_contact]=$id_contact;
			}
		}
		die();
		break;
	case "project_search_user_resp":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			if (!isset($_SESSION['project']['currentsearch'])) $_SESSION['project']['currentsearch']="";
			$nomsearch	= dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['project']['currentsearch']);
			$_SESSION['project']['currentsearch']=$nomsearch;

			require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
			$dims_user= new user();
			$dims_user->open($_SESSION['dims']['userid']);
			$groupslst=array();
			$lstusers=$dims_user->getusersgroup($nomsearch,0,0,$groupslst);

			$lstuserssel=array();
			if (!empty($_SESSION['project']['users'])) $lstuserssel+=$_SESSION['project']['users'];
			//echo "<div style=\"width:100%;height:120px;overflow:auto;\">";
			// affichage de la liste de resultat
			if (!empty($lstusers) && strlen($nomsearch)>=2) {
				$tabcorresp = array();
				$params = array();
				$res=$db->query("SELECT id_user,code
								FROM dims_group_user
								INNER JOIN dims_group on dims_group.id=dims_group_user.id_group
								AND id_user in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")", $params);
				if ($db->numrows($res)) {
					while ($gu=$db->fetchrow($res)) {
						if (!isset($tabcorresp[$gu['id_user']])) $tabcorresp[$gu['id_user']]=$gu['code'];
						else $tabcorresp[$gu['id_user']].=", ".$gu['code'];
					}
				}

				$params = array();
				// requete pour les noms
				$res=$db->query("SELECT DISTINCT id,firstname,lastname,color
								FROM dims_user
								WHERE id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")
								ORDER BY lastname,firstname", $params);
				if ($db->numrows($res)>0) {
					echo '<p style="clear:both;margin:0 0 0.2em;overflow:auto;padding:0.2em 0;">
							<label>'.$_DIMS['cste']['_DIMS_PRJT_SELECT_USER'].'</label>
								<select id="project_id_resp" name="project_id_resp" style="width:350px;">';
					while ($f=$db->fetchrow($res)) {
						echo "<option value=\"".$f['id']."\">".$f['lastname'].". ".$f['firstname']."</option>";
					}
					echo "</select></p>";
				}
			}
		}
		die();
		break;
	case "project_search_user":
		ob_end_clean();
		if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			if (!isset($_SESSION['project']['currentsearch'])) $_SESSION['project']['currentsearch']="";
			$nomsearch	= dims_load_securvalue('nomsearch',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['project']['currentsearch']);
			$_SESSION['project']['currentsearch']=$nomsearch;

			require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
			$dims_user= new user();
			$dims_user->open($_SESSION['dims']['userid']);
			$groupslst=array();
			$lstusers=$dims_user->getusersgroup($nomsearch,0,0,$groupslst);

			$lstuserssel=array();
			if (!empty($_SESSION['project']['users'])) $lstuserssel+=$_SESSION['project']['users'];
			echo "<div style=\"width:100%;height:120px;overflow:auto;\">";
			// affichage de la liste de resultat
			if (!empty($lstusers) && strlen($nomsearch)>=2) {
				$params = array();
				$tabcorresp = array();
				$res=$db->query("SELECT id_user,code
								FROM dims_group_user
								INNER JOIN dims_group on dims_group.id=dims_group_user.id_group
								AND id_user in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")", $params);
				if ($db->numrows($res)) {
					while ($gu=$db->fetchrow($res)) {
						if (!isset($tabcorresp[$gu['id_user']])) $tabcorresp[$gu['id_user']]=$gu['code'];
						else $tabcorresp[$gu['id_user']].=", ".$gu['code'];
					}
				}

				// requete pour les noms
				$params = array();
				$res=$db->query("SELECT distinct id,firstname,lastname,color
								FROM dims_user
								WHERE id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")
								ORDER BY lastname,firstname", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
							if (!in_array($f['id'],$lstuserssel)) {
									echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname'];
									if (isset($tabcorresp[$f['id']])) echo "&nbsp;(".$tabcorresp[$f['id']].")";
									echo "</td><td>";
									echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelected('addActionUser',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
							}
							else {
									echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname'];
									if (isset($tabcorresp[$f['id']])) echo "&nbsp;(".$tabcorresp[$f['id']].")";
									echo "</td><td>";
									echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelected('deleteActionUser',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
							}
					}
					echo "</table>";
				}
			}

			// affichage des groupes
			if (!empty($groupslst) && strlen($nomsearch)>=2) {
				$params = array();
				echo "<table style=\"width:100%;\">";
				$res=$db->query("SELECT g.*
								FROM dims_group as g
								WHERE id in (".$db->getParamsFromArray($groupslst, 'idgroup', $params).")
								ORDER BY label", $params);
				while ($f=$db->fetchrow($res)) {
					echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_group.gif\" border=\"0\">&nbsp;".$f['label']."</td><td>";
					echo "<td><a href=\"javascript:void(0);\" onclick=\"updateGroupActionFromSelected('addActionGroup',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
				}
				echo "</table>";
			}

			elseif(strlen($nomsearch)>=2) echo "<p style=\"width:100%;text-align:center\">".$_DIMS['cste']['_DIMS_LABEL_NO_RESP']."</p>";
			echo "</div>";

			echo "<span style=\"margin-top:0px;width:20%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;\">";
			echo "<img src=\"/modules/system/img/contacts.png\"></span>";
			echo "<span style=\"margin-top:25px;width:40%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;\">".$_DIMS['cste']['_DIRECTORY_MYCONTACTS']."</span>";
			echo "<span style=\"margin-top:25px;width:38%;display:block;float:left;font-size:12px;color:#BABABA;font-weight:none;\"></span>";

			// on calcul pour les contacts
			$params = array();
			if ($nomsearch>=2) {
				$sql="select * from dims_mod_project_contact where id_user=:iduser and firstname like :name or lastname like :name or email like :name order by firstname, lastname";
				$params[':name'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$nomsearch.'%');
			} else {
				$sql="select * from dims_mod_project_contact where id_user=:iduser order by firstname, lastname";
			}
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

			$res=$db->query($sql, $params);
			if ($db->numrows($res)>0) {
				echo "<br><table style=\"width:100%;\">";
				while ($f=$db->fetchrow($res)) {
					if (!isset($_SESSION['project']['contacts'][$f['id']])) {
							echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname']."</td><td>";
							echo "<td><a href=\"javascript:void(0);\" onclick=\"updateContactActionFromSelected('addActionContact',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
					}
					else {
							echo "<tr><td width=\"80%\"<img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname']."</td><td>";
							echo "<td><a href=\"javascript:void(0);\" onclick=\"updateContactActionFromSelected('deleteActionContact',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
					}
				}
				echo "</table>";
			}
			else echo "<p style=\"width:100%;text-align:center\">".$_DIMS['cste']['_DIMS_LABEL_NO_RESP']."</p>";

			echo "||";
			?>
			<span style="width:10%;display:block;float:left;height:30px;">
				<img src="/common/modules/system/img/users.png">
			</span>
			<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;margin-top:15px;height:30px;">
				<? echo $_DIMS['cste']['_DIMS_LABEL_USER_AFFECT']; ?>
			</span>
			<?
			echo "<span style=\"width:60%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;\">";
			echo $_DIMS['cste']['_DIMS_LABEL_INTERNAL_SOURCES']."</span>";
			echo "<div style=\"float:left;width:100%;height:150px;display:block;\">";
			// on affiche par defaut les personnes selectionnees en interne
			if (!empty($_SESSION['project']['users'])) {
				$params = array();
				$res=$db->query("SELECT u.*
								FROM dims_user as u
								WHERE id in (".$db->getParamsFromArray($_SESSION['project']['users'], 'iduser', $params).")
								ORDER BY lastname,firstname", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
						echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">".$f['lastname'].". ".$f['firstname']."</td><td>";
						echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelected('deleteActionUser',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
					}
					echo "</table>";
				}
			}

			// on affiche par defaut les personnes selectionnees en interne
			if (!empty($_SESSION['project']['groups'])) {
				$params = array();
				$res=$db->query("SELECT g.*
								FROM dims_group as g
								WHERE id in (".$db->getParamsFromArray($_SESSION['project']['groups'], 'idgroup', $params).")
								ORDER BY label", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
						echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_group.gif\" border=\"0\">".$f['label']."</td><td>";
						echo "<td><a href=\"javascript:void(0);\" onclick=\"updateGroupActionFromSelected('deleteActionGroup',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
					}
					echo "</table>";
				}
			}
			echo "</div>";

			echo "<span style=\"margin-top:15px;width:60%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;\">";
			echo $_DIMS['cste']['_DIMS_LABEL_CONTACTS']."</span>";
			// ajout des personnes selectionnes en contact

			if (!empty($_SESSION['project']['contacts'])) {
				$params = array();
				$res=$db->query("SELECT c.*
								FROM dims_mod_project_contact as c
								WHERE id in (".$db->getParamsFromArray($_SESSION['project']['contacts'], 'idcontact', $params).")
								ORDER BY lastname,firstname", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
							echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname']."</td><td>";
							echo "<td><a href=\"javascript:void(0);\" onclick=\"updateContactActionFromSelected('deleteActionContact',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
					}
					echo "</table>";
				}
			}

			echo "<script language=\"JavaScript\" type=\"text/JavaScript\">";
			if (!empty($_SESSION['project']['contacts']) ||
			!empty($_SESSION['project']['users']) ||
			!empty($_SESSION['project']['groups']))
				echo "activeProjectButton(1);";
			else
				echo "activeProjectButton(0);";
			echo "</script>";
		}
		die();
		break;
	case "project_valid_etape1":
			if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {

				$new_proj = new project();
				$new_proj->init_description();
				$new_proj->setvalues($_POST,'project_');

				$new_proj->fields['id_user'] = $_SESSION['dims']['userid'];
				$new_proj->fields['id_create'] = $_SESSION['dims']['userid'];
				$new_proj->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$new_proj->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$new_proj->fields['progress'] = 0;

				$new_proj->fields['date_start'] = dims_local2timestamp($new_proj->fields['date_start']);
				$new_proj->fields['date_end'] = dims_local2timestamp($new_proj->fields['date_end']);

				$new_proj->save();
				//dims_print_r($new_proj);die();
				$_SESSION['dims']['currentproject']=$new_proj->fields['id'];
				//dims_print_r($new_proj); die();
			}
			//dims_redirect($dims->getScriptEnv()."?op=add_project&etape=2");
			dims_redirect($dims->getScriptEnv()."?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&projectmenu=".dims_const::_DIMS_PROJECTMENU_PROJECT."");
		break;
	case "add_project":
			$reset=dims_load_securvalue("reset",dims_const::_DIMS_NUM_INPUT,true,true);
			if ($reset) unset($_SESSION['project']);
			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PROJECT_CREATE'],'100%','','',false);
			require_once(DIMS_APP_PATH . '/modules/system/desktop_project_create.php');
			echo $skin->close_simplebloc();
		break;
	case 'init_project':
		unset($_SESSION['dims']['currentproject']);
		unset($_SESSION['dims']['currenttask']);
		dims_redirect("/admin.php");
		break;
	case 'affectation_save':
		// on boucle sur tableau de useraffect
		if (isset($_POST['useraffect']) || isset($_POST['groupaffect'])) {
			$project = new project();
			$project->open($_SESSION['dims']['currentproject']);

			if (!isset($_POST['groupaffect'])) $tabgp = array();
			else $tabgp = dims_load_securvalue('groupaffect', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if (!isset($_POST['useraffect'])) $tabpost = array();
			else $tabpost = dims_load_securvalue('useraffect', dims_const::_DIMS_NUM_INPUT, true, true, true);

			$project->updateUsers($tabpost,$tabgp);
		}
		dims_redirect($scriptenv);
		break;
	case 'phase_affect_save':
		$id_phase = dims_load_securvalue("id_phase",dims_const::_DIMS_NUM_INPUT,true,true);
		if(!isset($id_phase) || $id_phase == 0) $id_phase = $_SESSION['dims']['currentphase'];

		// on boucle sur tableau de useraffect
		if (isset($_POST['useraffect'])) {
			$phase = new task();
			$phase->open($id_phase);
			if (!isset($_POST['useraffect'])) $tabpost=array();
			else $tabpost=dims_load_securvalue('useraffect', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$phase->updateUsers($tabpost, '', 1);
		}
		dims_redirect($scriptenv);
	break;
	case 'task_affect_save':
		$id_task = dims_load_securvalue("id_task",dims_const::_DIMS_NUM_INPUT,true,true);
		if(!isset($id_task) || $id_task == 0) $id_task = $_SESSION['dims']['currenttask'];

		// on boucle sur tableau de useraffect
		if (isset($_POST['useraffect'])) {
			$tsk = new task();
			$tsk->open($id_task);
			$tabselusers=$tsk->getUsers(0);
			if (empty($_POST['useraffect'])) $tabpost=array();
			else $tabpost=dims_load_securvalue('useraffect', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$tsk->updateUsers($tabpost, '', 0);

			//envoi de mail aux nouveaux inscrits
			$ufrom = new user();
			$ufrom->open($_SESSION['dims']['userid']);

			$from = array();
			$from[0]['name'] = $ufrom->fields['firstname']." ".$ufrom->fields['lastname'];
			$from[0]['address'] = $ufrom->fields['email'];

			$to = array();
			$cpt = 0;
			foreach($tabpost as $key => $iduserto) {
				if(!isset($tabselusers[$iduserto])) {

					$userto = new user();
					$userto->open($iduserto);
					$to[$cpt]['name'] = $userto->fields['firstname']." ".$userto->fields['lastname'];;
					$to[$cpt]['address'] = $userto->fields['email'];

					$cpt++;
				}
			}

			$subject = $_DIMS['cste']['_DIMS_LABEL_VALID_TASK_INSC'];

			$message = "Bonjour, <br/><br/>
						Votre inscription &agrave; la t&acirc;che ".$tsk->fields['label']." vient d'&ecirc;tre valid&eacute;e.<br/>
						Vous pouvez d&eacute;sormais acc&eacute;der aux informations concernant cette t&acirc;che.<br/><br/>
						Bien cordialement<br/><br/>
						";

			if($cpt > 0) dims_send_mail($from, $to, $subject, $message, '', '');

		}
		dims_redirect($scriptenv);
	break;
	case 'refuse_all_insc':
		$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_task = dims_load_securvalue('id_task', dims_const::_DIMS_NUM_INPUT, true, false, true, $_SESSION['dims']['currenttask']);

		//on va chercher toutes les actions du workspace pour le user donnees
		$sql_a = "	SELECT			a.*,
									t.label as task_name,
									u.email,
									u.lastname,
									u.firstname
					FROM			dims_mod_business_action a

					INNER JOIN		dims_mod_business_action_utilisateur au
					ON				au.action_id = a.id
					AND			au.user_id = :iduser

					INNER JOIN		dims_user u
					ON				u.id = :iduser

					INNER JOIN		dims_task t
					ON				t.id = :idtask

					WHERE			a.id_workspace = :idworkspace
					AND			a.id_task = :idtask";

		//echo $sql_a; die();
		$res_a = $db->query($sql_a, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $id_task),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		if($db->numrows($res_a) > 0) {
			while($tab_act = $db->fetchrow($res_a)) {
				$au = new action_user();
				$au->open($tab_act['id'],$id_user);
				$au->fields['participate'] = 2;
				$au->save();

				$uname = $tab_act['firstname']." ".$tab_act['lastname'];
				$email = $tab_act['email'];
				$tname = $tab_act['task_name'];
			}

			//on envoie un mail au user pour le prevenir de la validation de son inscription
			$from = array();
			$to = array();

			$from[0]['name'] = 'I-net Portal';
			$from[0]['address'] = '';

			$to[0]['name'] = $uname;
			$to[0]['address'] = $email;

			$subject = "I-net Portal : Registration to $tname";

			$message = "Dear $uname, <br /><br />
						Your participation to all actions from \"$tname\" is refused.<br />
						For more details you can go on your personnal page : <a href=\"http://projets_lfb/index.php\">Projects LFB</a><br /><br />
						Thank you for your participation.";

			dims_send_mail($from, $to, $subject, $message);

			dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_SUBMENU_SEARCH."&dims_desktop=block&dims_action=public&idtask=$id_task");
		}

	break;
	case 'refuse_act_insc':
		$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_act = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_task = dims_load_securvalue('id_task', dims_const::_DIMS_NUM_INPUT, true, false, true, $_SESSION['dims']['currenttask']);

		$au = new action_user();
		$au->open($id_act,$id_user);
		$au->fields['participate'] = 2;
		$au->save();

		$user = new user();
		$user->open($id_user);

		$act = new action();
		$act->open($id_act);

		$uname = $user->fields['firstname']." ".$user->fields['lastname'];
		$email = $user->fields['email'];
		$tname = $act->fields['libelle'];
		$date = explode("-", $act->fields['datejour']);
		$date_act = $date[1]."/".$date[2]."/".$date[0];
		$hdeb = explode(":", $act->fields['heuredeb']);
		$hdeb_act = $hdeb[0]." h ".$hdeb[1];
		$hfin = explode(":", $act->fields['heurefin']);
		$hfin_act = $hfin[0]." h ".$hfin[1];

		//on envoie un mail au user pour le prevenir de la validation de son inscription
		$from = array();
		$to = array();

		$from[0]['name'] = 'I-net Portal';
		$from[0]['address'] = '';

		$to[0]['name'] = $uname;
		$to[0]['address'] = $email;

		$subject = "I-net Portal : Registration to $tname";

		$message = "Dear $uname, <br /><br />
					Your participation to \"$tname\" is not validated for the date below : <br />
					<b>$date_act </b>, from <b>$hdeb_act </b>to <b>$hfin_act</b>. <br />
					For more details you can go on your personnal page : <a href=\"http://projets_lfb/index.php\">Projects LFB</a><br /><br />
					Thank you for your participation.";

		dims_send_mail($from, $to, $subject, $message);

		dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_SUBMENU_SEARCH."&dims_desktop=block&dims_action=public&idtask=$id_task");
	case 'valid_act_insc':
		$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_act = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_task = dims_load_securvalue('id_task', dims_const::_DIMS_NUM_INPUT, true, false, true, $_SESSION['dims']['currenttask']);

		$au = new action_user();
		$au->open($id_act,$id_user);
		$au->fields['participate'] = 1;
		$au->save();

		$user = new user();
		$user->open($id_user);

		$act = new action();
		$act->open($id_act);

		$uname = $user->fields['firstname']." ".$user->fields['lastname'];
		$email = $user->fields['email'];
		$tname = $act->fields['libelle'];
		$date = explode("-", $act->fields['datejour']);
		$date_act = $date[1]."/".$date[2]."/".$date[0];
		$hdeb = explode(":", $act->fields['heuredeb']);
		$hdeb_act = $hdeb[0]." h ".$hdeb[1];
		$hfin = explode(":", $act->fields['heurefin']);
		$hfin_act = $hfin[0]." h ".$hfin[1];

		//on envoie un mail au user pour le prevenir de la validation de son inscription
		$from = array();
		$to = array();

		$from[0]['name'] = 'I-net Portal';
		$from[0]['address'] = '';

		$to[0]['name'] = $uname;
		$to[0]['address'] = $email;

		$subject = "I-net Portal : Registration to $tname";

		$message = "Dear $uname, <br /><br />
					Your participation to \"$tname\" is now agreed for the date bellow :<br />
					<b>$date_act </b>, from <b>$hdeb_act </b>to <b>$hfin_act</b>. <br />
					For more details you can go on your personnal page : <a href=\"http://projets_lfb/index.php\">Projects LFB</a><br /><br />
					Thank you for your participation.";

		dims_send_mail($from, $to, $subject, $message);

		dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_SUBMENU_SEARCH."&dims_desktop=block&dims_action=public&idtask=$id_task");

		break;
	case 'valid_all_insc':
		$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_task = dims_load_securvalue('id_task', dims_const::_DIMS_NUM_INPUT, true, false, true, $_SESSION['dims']['currenttask']);

		//on va chercher toutes les actions du workspace pour le user donnees
		$sql_a = "	SELECT			a.*,
									t.label as task_name,
									u.email,
									u.lastname,
									u.firstname
					FROM			dims_mod_business_action a

					INNER JOIN		dims_mod_business_action_utilisateur au
					ON				au.action_id = a.id
					AND			au.user_id = :iduser

					INNER JOIN		dims_user u
					ON				u.id = :iduser

					INNER JOIN		dims_task t
					ON				t.id = :idtask

					WHERE			a.id_workspace = :idworkspace";

		$res_a = $db->query($sql_a, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $id_task),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		if($db->numrows($res_a) > 0) {
			while($tab_act = $db->fetchrow($res_a)) {
				$au = new action_user();
				$au->open($tab_act['id'],$id_user );
				$au->fields['participate'] = 1;
				$au->save();

				$uname = $tab_act['firstname']." ".$tab_act['lastname'];
				$email = $tab_act['email'];
				$tname = $tab_act['task_name'];
			}

			//on envoie un mail au user pour le prevenir de la validation de son inscription
			$from = array();
			$to = array();

			$from[0]['name'] = 'I-net Portal';
			$from[0]['address'] = '';

			$to[0]['name'] = $uname;
			$to[0]['address'] = $email;

			$subject = "I-net Portal : Registration to $tname";

			$message = "Dear $uname, <br /><br />
						Your participation to all actions from \"$tname\" is now agreed.<br />
						For more details you can go on your personnal page : <a href=\"http://projets_lfb/index.php\">Projects LFB</a><br /><br />
						Thank you for your participation.";

			dims_send_mail($from, $to, $subject, $message);

			dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_SUBMENU_SEARCH."&dims_desktop=block&dims_action=public&idtask=$id_task");
		}

	break;
	case 'delete_project':
		if (isset($_SESSION['dims']['currentproject'])) {
			/* On cree le nouveau projet */
			$project = new project();
			$project->open($_SESSION['dims']['currentproject']);

			// test des droits de suppression
			if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0 && ($_SESSION['dims']['userid']==$user_crea || dims_isadmin())) {
				$project->delete();
				dims_redirect($scriptenv);
			}
		}
	break;
	case 'project_save': /* Sauvegarde d'un projet */
		/* Requete SQL : on cherche le nom-prenom du createur grece e l'id*/
		$sql =	"select concat(lastname, ' ', firstname) as name from dims_user where id=:iduser";

		/* On execute la requete */
		$rs = $db->query($sql, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('id_crea', dims_const::_DIMS_NUM_INPUT, true, true, true)),
		));

		while ($fields = $db->fetchrow($rs)) {
			/* On affecte le nom-prenom au champ grace e l'id */
			$_POST['id_crea']=$fields['name'];
		}

		/* On cree le nouveau projet */
		$project = new project();

		if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0) $project->open($_SESSION['dims']['currentproject']);

		$project->setvalues($_POST,'project_');

		$project->fields['date_start']=dims_local2timestamp($project->fields['date_start']);
		$project->fields['date_end']=dims_local2timestamp($project->fields['date_end']);
		$project->setugm();

		$project->save();

		dims_redirect("$scriptenv?idproject=".$project->fields['id']);
	break;

	case 'project_task_add': /* Ajout d'une nouvelle teche e un projet */
		require_once(DIMS_APP_PATH . "/modules/system/desktop_project_task.php");
		die();
	break;

	case 'delete_task':
		$task = new task();

		$type = dims_load_securvalue('task_type',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($type == 1 && $idtask>0) {
			$task->open($idtask);
			unset($_SESSION['dims']['currenttask']);
		}
		elseif($type == 0 && $idphase>0)  {
			$task->open($idphase);
			unset($_SESSION['dims']['currentphase']);
		}

		$task->delete();

		$project = new project();
		$project->open($_SESSION['dims']['currentproject']);
		$project->refreshState();

		dims_redirect("$scriptenv");
	break;

	case 'task_save': /* Sauvegarde d'une tache */
		/* On cree la tache */
		$task = new task();
		/* on prepare l'action relative*/
		$act = new action();
		/* Si la tache doit etre simplement modifiee */
		$type = dims_load_securvalue('task_type',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($type == 1 && $idtask>0) {
			$task->open($idtask);
		}
		elseif($type == 0 && $idphase>0)  {
			$task->open($idphase);
		}

		$task->setvalues($_POST,'task_');

		$task->fields['time']=str_replace(",",".",$task->fields['time']);

		// verification de la duree

		$task->fields['time']=doubleval($task->fields['time']);

		//gestion des horaires
		if($task->fields['hdeb_h'] == '') $task->fields['hdeb_h'] = "8";
		if($task->fields['hdeb_m'] == '') $task->fields['hdeb_m'] = "00";
		$task->fields['heuredeb'] = $task->fields['hdeb_h'].':'.$task->fields['hdeb_m'].':00';
		unset($task->fields['hdeb_h']);
		unset($task->fields['hdeb_m']);

		if($task->fields['hfin_h'] == '') $task->fields['hfin_h'] = "18";
		if($task->fields['hfin_m'] == '') $task->fields['hfin_m'] = "00";
		$task->fields['heurefin'] = $task->fields['hfin_h'].':'.$task->fields['hfin_m'].':00';
		unset($task->fields['hfin_h']);
		unset($task->fields['hfin_m']);

		// test si apres virgule et bien un multiple de 0.25
		if ($task->fields['time']-intval($task->fields['time'])>0) {
			$diff = $task->fields['time'] - floor($task->fields['time']);
			 if ($diff<=0.25) {
			  $task->fields['time'] = floor($task->fields['time']) + 0.25;
			 };
			 if ($diff>0.25 && $diff<=0.5) {
			  $task->fields['time'] = floor($task->fields['time']) + 0.5;
			 };
			 if ($diff>0.5 && $diff<=0.75) {
			  $task->fields['time'] = floor($task->fields['time']) + 0.75;
			 };
			 if ($diff>0.75) {
			  $task->fields['time'] = floor($task->fields['time']) + 1;
			 };
		}
		$task->fields['date_start'] = dims_local2timestamp($task->fields['date_start']);
		if(empty($task->fields['date_end']))
			$task->fields['date_end'] = $task->fields['date_start'];
		else
			$task->fields['date_end'] = dims_local2timestamp($task->fields['date_end']);

		$task->setugm();
		$task->fields['id_project'] = $_SESSION['dims']['currentproject'];

		$task->save();

		if (!isset($_POST['useraffect'])) $tabpost=array();
		else $tabpost= dims_load_securvalue('useraffect', dims_const::_DIMS_NUM_INPUT, true, true, true);
		// affectation
		$task->updateUsers($tabpost);

		$project = new project();
		$project->open($_SESSION['dims']['currentproject']);
		$project->refreshState();

		if($type == 1) unset($_SESSION['dims']['currenttask']);
		elseif($type == 0 ) unset($_SESSION['dims']['currentphase']);
		dims_redirect("$scriptenv");
	break;

	case 'project_task_delete': /* Suppression d'une teche */
		/* Il faut connaitre l'id du projet et de la teche */
		if (isset($idproject) && isset($idtask)) {
			/* Si on trouve la teche, on la supprime */
			$task = new task();
			if ($task->open($idtask)) {
				$task->delete();
			}
		}
	break;

	case 'project_task_task_add': /* Ajout d'une nouvelle dependance entre 2 teches */

		 /* Si la dependance e ete effectue dans le mauvaise ordre
		 Note : On fait ce test en premier car on modifie l'ordre */
		if (datefr2us($datee1)<datefr2us($datee2)) {
			ob_start();
			echo 'Attention : Vous avez selectionne les teches dans le mauvaise ordre. Nous avons corrige ce probleme.';
			ob_end_flush();
			$tmp = $idtask;
			$idtask=$idtaskneeded;
			$idtaskneeded=$tmp;
		}

		/* Requete SQL : on cherche si la dependance existe deje */
		$sql =	"select * from dims_mod_prjt_task_task where id_task=:idtask and id_task_needed=:idtaskneeded";

		/* On execute la requete */
		$rs = $db->query($sql, array(
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $idtask),
			':idtaskneeded' => array('type' => PDO::PARAM_INT, 'value' => $idtaskneeded),
		));

		/* Si la dependance existe deje */
		if ($db->numrows()>0) {
			ob_start();
			echo 'Erreur : La dependance existe deje.';
			ob_end_flush();
			die();
			break;
		}

		/* Si la dependance lie la teche avec elle meme */
		if ($idtask==$idtaskneeded) {
			ob_start();
			echo 'Erreur : Vous ne pouvez pas lier la teche avec elle meme';
			ob_end_flush();
			die();
			break;
		}

		/* On cree le nouveau lien */
		$task_task = new task_task();
		$task_task->fields['id_task']=$idtask;
		$task_task->fields['id_task_needed']=$idtaskneeded;
		$task_task->save();

		die();
	break;

	case 'project_objective_add': /* Ajout d'un nouvel objectif */

		/* Le contenu de la page (essentielement le formulaire) */
		echo $contents.'<div class="projects_form">
			<form name="form_objective" action="'.$scriptenv.'"  method="POST">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op", 		"project_objective_save");
		$token->field("idproject",	$idproject);
		$token->field("objective_label");
		$token->field("objective_date");
		$token->field("objective_description");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo '<input type="hidden" name="op" value="project_objective_save">
			<input type="hidden" name="idproject" value="'.$idproject.'">
			<div class="projects_form">
			<div style="float:left;width:100px;">'.$_DIMS['cste']['_DIMS_LABEL'].'</div><div style="float:left;width:200px;"><input type="text" name="objective_label"></div>
			</div><div class="projects_form">
			<div style="float:left;width:100px;">'.$_DIMS['cste']['_FORM_OBJECTIVE_DATE'].'</div><div style="float:left;width:250px;"><input type="text" class="text" size="30" name="objective_date" id="objective_date" value="'.dims_getdate().'">&nbsp;<a href="#" onclick="javascript:dims_calendar_open(\'objective_date\', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a></div></div><div class="projects_form">
			</div><div class="projects_form">
			<div style="float:left;width:100px;">'.$_DIMS['cste']['_FORM_OBJECTIVE_COMMENT'].'</div><div style="float:left;width:300px;"><textarea class="text" type="text" cols="52" rows="3" name="objective_description"></textarea></div>
			</div><div class="projects_form">
			<div style="float:left;"><input class="button" type="submit" value="'.$_DIMS['cste']['_FORM_OBJECTIVE_OK'].'"></div>
			<div style="float:left;"><input type="button" class="button" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="dims_getelem(\'dims_popup2\').style.visibility=\'hidden\';"></div>
			</div></form>';

		die();
	break;

	case 'project_objective_save': /* Sauvegarde d'un objectif */

		/* On cree la teche */
		$objective = new objective();

		$objective->setvalues($_POST,'objective_');
		$objective->save();

		/* On sauvegarde la relation */
		$project_objectif = new project_objective();
		$project_objectif->fields['id_project']=$idproject;
		$project_objectif->fields['id_objective']=$objective->fields['id'];
		$project_objectif->save();

		/* On recharge la page */
		dims_redirect($scriptenv);

		die();

	break;

	case 'project_delete': /* Suppression d'un projet */

		/* Il faut connaitre l'id du projet */
		if (isset($idproject)) {

			/* Si on trouve le projet, on le supprime */
			$project = new project();
			if ($project->open($idproject)) {
				$project->delete();
			}

			/* Requete SQL : on cherche les relations avec les teches */
			$sql =	"select * from dims_mod_prjt_project_task where id_project = :idproject";
			$rs = $db->query($sql, array(
				':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
			));

			while ($fields = $db->fetchrow($rs)) {
				$project_task = new project_task();
				if ($project_task->open($fields['id'])) {
					$project_task->delete();
				}
				$task = new task();
				if ($task->open($fields['id_task'])) {
					$task->delete();
				}
			}

			/* Idem pour la relation avec les objective */
			$sql =	"select * from dims_mod_prjt_project_objective where id_project = :idproject";
			$rs = $db->query($sql, array(
				':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
			));

			while ($fields = $db->fetchrow($rs)) {
				$project_objective = new project_objective();
				if ($project_objective->open($fields['id'])) {
					$project_objective->delete();
				}
				$objective = new objective();
				if ($objective->open($fields['id_objective'])) {
					$objective->delete();
				}
			}

		}

		/* On recharge la page */
		dims_redirect($scriptenv);
	break;

	case 'project_task_task_delete': /* Suppression d'un lien entre 2 teches */

		/* Il faut connaitre l'id du lien entre les 2 teches */
		if (isset($idtasktask)) {

			/* Si on trouve la dependance, on la supprime */
			$task_task = new task_task();
			if ($task_task->open($idtasktask)) {
				$task_task->delete();
			}
		}
	break;

	case 'project_objective_delete': /* Suppression d'un objectif */

		/* Il faut connaitre l'id de l'objectif */
		if (isset($idobjective)) {

			/* Si on trouve la dependance, on la supprime */
			$objective = new objective();
			if ($objective->open($idobjective)) {
				$objective->delete();
			}

			/* Idem pour la relation avec le projet */

			/* Requete SQL : on cherche la relation (id) */
			$sql =	"select id from dims_mod_prjt_project_objective where id_project=:idproject and id_objective=:idobjective";
			$rs = $db->query($sql, array(
				':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
				':idobjective' => array('type' => PDO::PARAM_INT, 'value' => $idobjective),
			));

			while ($fields = $db->fetchrow($rs)) {
				$project_objective = new project_objective();
				if ($project_objective->open($fields['id'])) {
					$project_objective->delete();
				}
			}
		}
	break;

	case 'project_change_state': /* Changement de l'etat du projet : En Cours -> Cles -> En Cours -> ... */

		/* Il faut connaitre l'id du projet */
		if (isset($idproject)) {

			/* On change le status du projet */
			$project = new project();
			$project->change_state($idproject);
		}

		/* On recharge la page */
		dims_redirect($scriptenv);
	break;

	case 'project_task_change_state': /* Changement de l'etat de la teche : En Cours -> Cles -> En Cours -> ... */

		/* Il faut connaitre l'id de la teche */
		if (isset($idtask)) {

			/* On change le status de la teche */
			$task = new task();
			$task->change_state($idtask);
		}

		die();
	break;

	case 'project_objective_change_state': /* Changement de l'etat de l'objectif : En Cours -> Cles -> En Cours -> ... */

		/* Il faut connaitre l'id de l'objectif */
		if (isset($idobjective)) {

			/* On change le status de l'objectif */
			$objective = new objective();
			$objective->change_state($idobjective);
		}

		die();
	break;

	case 'project_task_modify': /* Changement de l'etat de la teche : En Cours -> Cles -> En Cours -> ... */

		/* Il faut connaitre l'id de la teche */
		if (isset($idtask)) {

			/* Si on trouve la teche, on ouvre un formulaire avec les valeurs de l'objets */
			$task = new task();
			if ($task->open($idtask)) {

				switch($task->fields['priority']) {

					case 0:
						$priority= '<option value="0" selected>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_0'].'</option><option value="1">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_1'].'</option><option value="2">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_2'].'</option>';
						break;
					case 1:
						$priority= '<option value="0">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_0'].'</option><option value="1" selected>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_1'].'</option><option value="2">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_2'].'</option>';
						break;
					case 2:
						$priority= '<option value="0">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_0'].'</option><option value="1">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_1'].'</option><option value="2" selected>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_2'].'</option>';
						break;
					default:
						$priority= '<option value="0" selected>'.$_DIMS['cste']['_FORM_TASK_PRIORITY_0'].'</option><option value="1">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_1'].'</option><option value="2">'.$_DIMS['cste']['_FORM_TASK_PRIORITY_2'].'</option>';
						break;
				}

				echo '<div class="projects_form">
				<div style="float:left;width:300px;"><input type="text" style="border:none;color:#ff0000;background-color:#e0e0e0;" value="" id="task_error" READONLY></div>
				</div>
				<form name="form_task" method="POST">';
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("task_label");
				$token->field("task_date_start");
				$token->field("task_date_end");
				$token->field("task_priority");
				$token->field("task_description");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
				echo '<div class="projects_form">
				<div style="float:left;width:100px;">'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</div><div style="float:left;width:200px;"><input type="text" name="task_label" value='.$task->fields['label'].'></div>
				<div style="float:left;width:100px;">'.$_DIMS['cste']['_FORM_TASK_START_DATE'].'</div><div style="float:left;width:250px;"><input type="text" class="text" size="30" name="task_date_start" id="project_date_start" value="'.$task->fields['date_start'].'">&nbsp;<a href="#" onclick="javascript:dims_calendar_open(\'project_date_start\', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a></div>
				</div><div class="projects_form">
				<div style="float:left;width:100px;">'.$_DIMS['cste']['_FORM_TASK_PRIORITY'].'</div><div style="float:left;width:200px;"><select name="task_priority">'.$priority.'</select></div>
				<div style="float:left;width:100px;">'.$_DIMS['cste']['_END'].'</div><div style="float:left;width:250px;"><input type="text" class="text" size="30" name="task_date_end" id="project_date_end" value="'.$task->fields['date_end'].'">&nbsp;<a href="#" onclick="javascript:dims_calendar_open(\'project_date_end\', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a></div>
				</div><div class="projects_form">
				<div style="float:left;width:100px;">'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</div><div style="float:left;width:500px;"><textarea class="text" type="text" cols="82" rows="3" name="task_description">'.$task->fields['description'].'</textarea></div>
				</div><div class="projects_form">
				<div style="float:left"><input class="button" type="submit" value="'.$_DIMS['cste']['_FORM_TASK_MODIFY'].'" onclick="javascript:task_save(\''.$idproject.'\',\''.$idtask.'\',\''.$zoom.'\', event, form_task);"></div>
				<div style="float:left;"><input type="button" class="button" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:dims_hidepopup2();"></div>
				</div></form>';

			}
		}

		die();
	break;

	case 'project_task_attach_user': /* Attachement, e la teche, d'un utilisateur (Affichage) */

		echo '<div class="projects_form">
		<form name="form_attach_user" method="POST">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo attachUsers(false,null,"90%").'
		<div style="float:left"><input class="button" type="submit" value="'.$_DIMS['cste']['_FORM_USER_OK'].'" onclick="javascript:attach_user_save(\''.$idtask.'\',\''.$idproject.'\',\''.$zoom.'\');"></div>
		<div style="float:left;"><input type="button" class="button" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:dims_hidepopup2();"></div>
		</form></div>';

		die();
	break;

	case 'project_task_attach_user_save': /* Attachement, e la teche, d'un utilisateur (Sauvegarde) */

		foreach($_SESSION['dims']['tickets']['users_selected'] as $user_id) {

			$task_user = new task_user();
			$task_user->fields['id_task'] = $idtask;
			$task_user->fields['id_user'] = $user_id;
			$task_user->save();

		}

		unset($_SESSION['dims']['tickets']['users_selected']);

		die();
	break;

	case 'project_view_gantt': /* Visualisation du diagramme de gant du projet */
		$crea="";
		$res="";
		$ddeb="";
		$dfin="";
		$etat="";
		$comm="";

		$zoom=dims_load_securvalue("zoom",dims_const::_DIMS_CHAR_INPUT,true,true);
		$idproject=$_SESSION['dims']['currentproject'];

		if ($zoom!="") $_SESSION['project']['zoom']=$zoom;
		else $zoom=$_SESSION['project']['zoom'];

		echo '<div id="dims_popup2" style="position:absolute;visibility:hidden;top:0px;left:0px;z-index:1000;width:200px;float:left;padding:0px;border:1px solid #a0a0a0;background: #dcdcdc url(../common/img/field_bg.png) top left repeat-y;color: #000000;text-align:left;opacity:0.9;overflow:auto;"></div>';
		echo '<div id="div_loading" style="position:absolute;visibility:hidden;top:200px;left:600px;z-index:1001;width:300px;height:150px;float:left;padding:0px;border:1px solid #a0a0a0;text-align:center;font-weight:bold;font-size:16px;background: #ffffff;color: #000000;text-align:left;opacity:0.9;overflow:auto;"></div>';

		/* On cree la page */
		//echo $skin->create_pagetitle($_DIMS['cste']['_LABEL_PROJECTS'],'100%');
		//echo $skin->open_simplebloc('','float:left;width:100%;overflow:none');

		/* Div visualisation_infos */
		echo '<div id="visualisation_infos" style="position:absolute;visibility:hidden;"></div>';

		/* Il faut connaitre l'id du projet */
		if (isset($idproject)) {

			/* Le div principale de la page. possede un largeur variable en fonction de l'etat du visualisateur */
			echo '<div id="main_div_project" style="width:75%;height:100%;">';

			/* Le zoom est sur le mois par defaut */
			if (!isset($zoom)) $zoom="m";

			/* Le visualisateur est visible par defaut */
			if (!isset($hide)) $hide="n";

			/* Les tailles des elements par defaut */

			$visu_header_w = "23.28%";
			$visu_header_h = "16px"; // (on fixe la valeur)

			$visu_w = "23.43%";
			$visu_h = "25.58%";

			$visu_zoom_w = "15.43%";
			$visu_zoom_h = "100%";

			$visu_cadre_w = "3.000%";
			$visu_cadre_h = "3.800%";

			$visu_area_w = "7.815%";
			$visu_area_h = "25.20%";

			$visu_header_hide_w = "1.955%";
			$visu_header_hide_h = "25px";

			$visu_hide_w = "1.715%";
			$visu_hide_h = "25.58%";

			$visu_image_w =  "100%";
			$visu_image_h = "25.58%";

			$_SESSION['project']['visu_w']=$visu_w;
			$_SESSION['project']['visu_h']=$visu_h;
			$_SESSION['project']['visu_zoom_w']=$visu_zoom_w;
			$_SESSION['project']['visu_zoom_h']=$visu_zoom_h;
			/* Requete SQL de selection */
			$sql =	"SELECT p.label, concat(u1.lastname, ' ', u1.firstname) as crea, concat(u2.lastname, ' ', u2.firstname) as resp, p.date_start, p.date_end, p.state, p.description
					FROM dims_project as p, dims_user u1, dims_user u2
					WHERE p.id_create=u1.id
					AND p.id_resp=u2.id
					AND p.id= :idproject";

			/* On execute la requete */
			$rs = $db->query($sql, array(
				':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
			));

			while ($fields = $db->fetchrow($rs)) {

				$name = "'".$fields['label']."'";
				$crea = "<br><b>".$_DIMS['cste']['_INFOS_CREATOR']."</b>".$fields['crea'];
				$res = "<br><b>".$_DIMS['cste']['_DIMS_LABEL_RESPONSIBLE']."</b>".$fields['resp'];
				$ddeb = "<br><b>".$_DIMS['cste']['_INFOS_START_DATE']."</b>".$fields['date_start'];
				$dfin = "<br><b>".$_DIMS['cste']['_INFOS_END_DATE']."</b>".$fields['date_end'];
				$etat = "<br><b>".$_DIMS['cste']['_INFOS_STATE']."</b>".$fields['state'];
				$comm = "<br><b>".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</b>".$fields['description'];
			}

			/* Les informations generales */
			$infos = '<b>'.$_DIMS['cste']['_INFOS_LABEL'].'</a></b><br>'.$crea.$res.$ddeb.$dfin.$etat.$comm.displayInfos($idproject);


			/* On affiche le div permettant la visualisation du diagramme suivant le zoom voulu */
			echo '<div id="cadre"></div>';

			/* Fenetre de visualisation (Zoom sur le mois et visible par defaut) */
			echo '<div id="visualisation_header" class="visualisation_header" style="display:none;width:'.$visu_header_w.';height:'.$visu_header_h.';"><div style="float:left;width:190px;height:16px;"><b>'.$_DIMS['cste']['_PROJECT_LABEL_VISU'].'</b></div><div style="float:left;width:20px;height:35px;">
			<img src="./common/modules/system/img/v_close_16.png" onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="change_display(1,\'m\',\'o\');"></div></div>
			<div id="visualisation" class="visualisation" style="display:none;width:'.$visu_w.';height:'.$visu_h.';">';

			/* On affiche la miniature du diagramme de gantt du projet dans ce div */
			echo '<div id="visu_div" style="display:none;float:left;overflow:none;border:none;height:100%;width:'.(95-substr($visu_zoom_w,0,2)).'%"></div>';

			echo '<div id="visualisation_zoom" class="visualisation_zoom" style="display:none;float:right;width:'.$visu_zoom_w.';height:'.$visu_zoom_h.';">
			<img id="zoom_img" style="border:none;" src="./common/modules/system/img/visualisation_mois.png"></div>';
			echo '</div>';

			/* Div visualisation_area */
			echo '<div id="visualisation_area"	style=" display:none;width:'.$visu_area_w.';height:'.$visu_area_h.';" onmousedown="beginDragX(document.getElementById(\'visualisation_area\'),event);" onmousemove="dragX(event);" onmouseup="endDragX();"></div>';

			/* Affichage des divs visu_cadre */
			echo '
			<div id="visualisation_cadre_j" class="visualisation_cadre_j" style="display:none;width:'.$visu_cadre_w.';height:'.$visu_cadre_h.';"
			onmouseover="javascript:this.style.cursor=\'pointer\';"
			onclick="javascript:change_view(\''.$idproject.'\',\'j\');">
			<img src="./common/modules/system/img/visualisation_cadre.png">
			</div>
			<div id="visualisation_cadre_s" class="visualisation_cadre_s" style="display:none;width:'.$visu_cadre_w.';height:'.$visu_cadre_h.';"
			onmouseover="javascript:this.style.cursor=\'pointer\';"
			onclick="javascript:change_view(\''.$idproject.'\',\'s\');">
			<img src="./common/modules/system/img/visualisation_cadre.png">
			</div>
			<div id="visualisation_cadre_m" class="visualisation_cadre_m" style="display:none;width:'.$visu_cadre_w.';height:'.$visu_cadre_h.';"
			onmouseover="javascript:this.style.cursor=\'pointer\';"
			onclick="javascript:change_view(\''.$idproject.'\',\'m\');">
			<img src="./common/modules/system/img/visualisation_cadre.png">
			</div>
			<div id="visualisation_cadre_a" class="visualisation_cadre_a" style="display:none;width:'.$visu_cadre_w.';height:'.$visu_cadre_h.';"
			onmouseover="javascript:this.style.cursor=\'pointer\';"
			onclick="javascript:change_view(\''.$idproject.'\',\'a\');">
			<img src="./common/modules/system/img/visualisation_cadre.png">
			</div>';

			/* On affiche le diagramme de gantt du projet dans ce div */
			echo '<div id="diag_div" class="projects_div_gantt" style="overflow:hidden"></div>';

			/* Fermeture du div principale */
			echo "<div>";

			/* On initialise l'affichage */
			echo '<script type="text/javascript">change_display(\''.$idproject.'\',\''.$zoom.'\',\''.$hide.'\');</script>';


		} else {
			echo "Erreur : Le chargement du projet n'a pu etre etablie correctement.";
		}

		/* On ferme le bloc (=la page) */
		//echo $skin->close_simplebloc();

		/* Gestion du redimensionnement de la fenetre */
		echo '
		<script>
			window.onresize = resize;

			function resize() {
				position_all(\'n\');
			}

		</script>';
	break;

	case 'project_diag_gantt': /* Visualisation du diagramme de gantt du projet */
		ob_end_clean();
		$width=dims_load_securvalue("width",dims_const::_DIMS_NUM_INPUT,true,true);
		$height=dims_load_securvalue("height",dims_const::_DIMS_NUM_INPUT,true,true);
		if ($width!="") $_SESSION['project']['width']=$width;
		if ($height!="") $_SESSION['project']['height']=$height;

		$idproject=$_SESSION['dims']['currentproject'];
		/* Il faut connaitre l'id du projet */
		if (isset($idproject)) {
			if (!isset($hide) || $hide=="n") {
				$gantt_w = "75%";
			} else {
				$gantt_w = "96.6%";
			}
			$_SESSION['project']['gantt_w']=$gantt_w;

			/* On affiche le diagramme de gantt du projet */
			require_once(DIMS_APP_PATH . '/modules/system/task_gantt.php');

		}else {
			echo "Erreur : Le chargement du projet n'a pu etre etablie correctement.";
		}
		die();
	break;

	case 'project_diag_small_gantt': /* Visualisation de la miniature du diagramme de gantt du projet */
	ob_end_clean();
		/* Il faut connaitre l'id du projet */
		if (isset($idproject)) {

			if (!isset($hide) || $hide=="n") {
				$gantt_w = "75%";
			} else {
				$gantt_w = "96.6%";
			}
			/* On affiche le diagramme de gantt du projet */

			require_once(DIMS_APP_PATH . '/modules/system/task_gantt_small.php');


		}else {
			echo "Erreur : Le chargement du projet n'a pu etre etablie correctement.";
		}
		die();
	break;

	case 'project_task_infos': /* Visualisation des informations de la teche selectionnee */
		ob_end_clean();
		/* Il faut connaitre l'id de la teche */
		if (isset($idtask)) {

			/* On ouvre la teche selectionnee */
			$task = new task();
			if ($task->open($idtask)) {
				$datestartfr=dims_timestamp2local($task->fields['date_start']);
				$task->fields['date_start']=$datestartfr['date'];
				$dateendfr=dims_timestamp2local($task->fields['date_end']);
				$task->fields['date_end']=$dateendfr['date'];

				/* On recupere les durees */
				$view_date = get_view($task->fields['date_start'],$task->fields['date_end']);


				/* Requete SQL : selection des utilisateurs de la teche */
				$sql =	"SELECT distinct concat(lastname, ' ', firstname) as name
						FROM dims_user u, dims_mod_prjt_task_user tu
						WHERE u.id=tu.id_user
						AND tu.id_task = :idtask";

				/* On execute la requete */
				$rs = $db->query($sql, array(
					':idtask' => array('type' => PDO::PARAM_INT, 'value' => $idtask),
				));

				/* On recupere les infos */
				$task_infos = "<b>Teche : ".$task->fields['label']." (".$task->fields['id'].")	[".$task->fields['state']."]</b>";
				$task_infos .= "<br><br><b>Description : </b>".$task->fields['description'];
				$task_infos .= "<br><b>Priorite : </b>".$task->fields['priority'];
				$task_infos .=	"<br><b>Debut : </b>".$task->fields['date_start'];
				$task_infos .=	"<br><b>Fin : </b>".$task->fields['date_end'];
				$task_infos .= "<hr>";
				$task_infos .=	"<b>Nb jour : </b>".$view_date['nb_day'];
				$task_infos .=	"<br><b>Nb semaine : </b>".$view_date['nb_week'];
				$task_infos .=	"<br><b>Nb de mois : </b>".$view_date['nb_month'];
				$task_infos .=	"<br><b>Nb d'annee : </b>".$view_date['nb_year'];
				$task_infos .=	"<hr>";
				$task_infos .= "<b>Liste des utilisateurs attaches : </b>";

				while ($fields = $db->fetchrow($rs)) {

					$task_infos .= "<br>- ".$fields['name'];

				}

				echo $task_infos;

			}

		}

		die();
	break;

	case 'project_view_multi_project': /* Visualisation multi projet */

		echo '<div id="div_loading" style="position:absolute;visibility:hidden;top:200px;left:600px;z-index:1001;width:300px;height:150px;float:left;padding:0px;border:1px solid #a0a0a0;text-align:center;font-weight:bold;font-size:16px;background: #ffffff;color: #000000;text-align:left;opacity:0.9;overflow:auto;"></div>';

		echo $skin->create_pagetitle($_DIMS['cste']['_LABEL_PROJECTS'],'100%');

		echo $skin->open_simplebloc('','100%');

		echo '<div><b>'.$_DIMS['cste']['_PROJECT_LABEL_VIEW_MULTI_PROJECT'].' <i>'.$name.'</i></b> -
		<a href="#" onclick="javascript:change_view_multi_project(\'s\');">'.$_DIMS['cste']['_PROJECT_LABEL_ZOOM_WEEK'].'</a> -
		<a href="#" onclick="javascript:change_view_multi_project(\'m\');">'.$_DIMS['cste']['_PROJECT_LABEL_ZOOM_MONTH'].'</a> -
		<a href="'.$scriptenv.'?dims_mainmenu='.dims_const::_DIMS_MENU_PROJECTS.'&filtertype=all"">'.$_DIMS['cste']['_PROJECT_LABEL_BACK'].'</a></div>';

		echo '<div id="diag_div" class="projects_div_gantt" style="overflow-x:hidden"></div>';

		echo $skin->close_simplebloc();

		/* On initialise sur la visualisation par le mois */
		echo '<script>change_view_multi_project(\'m\');</script>';

		/* Gestion du redimensionnement de la fenetre */
		echo '
		<script>
			window.onresize = resize;

			function resize() {
				change_view_multi_project(\'m\');
			}

		</script>';
	break;

	case 'project_multi_project': /* Les diagrammes de gantt des projets */

		require_once(DIMS_APP_PATH . '/modules/system/task_gantt_all_project.php');

		die();
	break;

	case 'project_view_multi_task': /* Visualisation multi teche */

		echo '<div id="div_loading" style="position:absolute;visibility:hidden;top:200px;left:600px;z-index:1001;width:300px;height:150px;float:left;padding:0px;border:1px solid #a0a0a0;text-align:center;font-weight:bold;font-size:16px;background: #ffffff;color: #000000;text-align:left;opacity:0.9;overflow:auto;"></div>';

		//echo $skin->create_pagetitle($_DIMS['cste']['_LABEL_PROJECTS'],'100%');

		echo $skin->open_simplebloc('','100%');

		echo '<div><b>'.$_DIMS['cste']['_PROJECT_LABEL_VIEW_MULTI_TASK'].' <i>'.$name.'</i></b> -
		<a href="#" onclick="javascript:change_view_multi_task(\'s\');">'.$_DIMS['cste']['_PROJECT_LABEL_ZOOM_WEEK'].'</a> -
		<a href="#" onclick="javascript:change_view_multi_task(\'m\');">'.$_DIMS['cste']['_PROJECT_LABEL_ZOOM_MONTH'].'</a> -
		<a href="'.$scriptenv.'?dims_mainmenu='.dims_const::_DIMS_MENU_PROJECTS.'&filtertype=all"">'.$_DIMS['cste']['_PROJECT_LABEL_BACK'].'</a></div>';

		echo '<div id="diag_div" class="projects_div_gantt" style="overflow-x:hidden"></div>';

		echo $skin->close_simplebloc();

		/* On initialise sur la visualisation par le mois */
		echo '<script>change_view_multi_task(\'m\');</script>';

		/* Gestion du redimensionnement de la fenetre */
		echo '
		<script>
			window.onresize = resize;

			function resize() {
				change_view_multi_task(\'m\');
			}

		</script>';
	break;

	case 'project_multi_task': /* Les diagrammes de gantt des teches de l'utilisateur */
		require_once(DIMS_APP_PATH . '/modules/system/task_gantt_all_task.php');

		die();
	break;

	case "search_ct" :
		ob_start();

		//recuperation du parametre
		$search = dims_load_securvalue('search', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$type_elem = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		//requete de recherche : chaine%
		$sql = 'SELECT
					ct.id AS id_ct,
					ct.lastname,
					ct.firstname,
					ctl.email AS email_ct,
					u.id,
					u.email AS email_user
				FROM
					dims_mod_business_contact ct

				LEFT JOIN	dims_mod_business_contact_layer ctl
				ON			ctl.id = ct.id
				AND		ctl.type_layer = 1
				AND		ctl.id_layer = :idworkspace

				LEFT JOIN
					dims_user u
					ON
						u.id_contact = ct.id
				WHERE
						ct.lastname LIKE :search
					OR
						ct.firstname LIKE :search';

		$ress = $db->query($sql, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			':search' => array('type' => PDO::PARAM_STR, 'value' => $search.'%'),
		));

		if($db->numrows($ress) > 0) { //On a des contact

			//Récupération de la liste des user du workspace
			$ws= new workspace();
			$ws->open($_SESSION['dims']['workspaceid']);

			$lstusers=$ws->getusers();

			echo '<table style="width: 100%; border-collapse: collapse;">';
			$class = 'trl1';
			while($info = $db->fetchrow($ress)) {
				//Si le contact(Avec user) n'est pas déjà dans la liste des WSusers
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
							$js = "document.location.href='admin.php?dims_mainemenu=".dims_const::_DIMS_MENU_PROJECTS."&op=add_ct&type=".$type_elem."&id_ct=".$info['id_ct']."';";
					   }
					else
						$js = 'askmail('.$info['id_ct'].', \''.$type_elem.'\');';

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

	case "add_ct" :
			$idCt = 0;
			$mail = '';
			$type = '';

			//Param
			$idCt = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$mail = dims_load_securvalue('mail', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$type_elem = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true, true);

			//Si on atteint bien la page avec un id de contact
			if($idCt != 0) {
				require_once DIMS_APP_PATH . '/include/functions/mail.php';

				$idUser = 0;
				$user = new user();
				$ct = new contact();

				$ct->open($idCt);

				//recherche de l'email
				if($mail == '') {
					$sqle = "SELECT email FROM dims_mod_business_contact_layer WHERE id = :idcontact AND type_layer = 1 AND id_layer = :idlayer";
					$rese = $db->query($sqle, array(
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $idCt),
						':idlayer' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					));
					if($db->numrows($rese) > 0) {
						$tab_mail = $db->fetchrow($rese);
						$mail = $tab_mail['email'];
					}
					elseif(isset($ct->fields['email']) && !empty($ct->fields['email'])) {
						$mail = $ct->fields['email'];
					}
				}

				//recherche d'un user déjà lié au Ct
				$sqlUser = 'SELECT id FROM dims_user WHERE id_contact = :idcontact LIMIT 1';

				$ressUser = $db->query($sqlUser, array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $idCt),
				));

				//On a déjà un compte, il suffit de le rattacher au WS et au projet
				if($db->numrows($ressUser) > 0) {
					$info = $db->fetchrow($ressUser);
					$idUser = $info['id'];

					$user->open($idUser);

					if(isset($user->fields['email']) && !empty($user->fields['email']))
						$mail = $user->fields['email'];
					else
						$user->fields['email'] = $mail;
				}
				//Création d'un user
				else {
					$search_login	= true;
					$i_login	= 0;

					// Créer un login non existant encore (En fonction du nom/prenom[+nombre aleatoire])
					// /!\ Risque de trop de requete :(
					// Autre solution ?
					$login = $ct->fields['lastname'].$ct->fields['firstname'];

					while($search_login)
					{
						$i_login++;

						$sql = 'SELECT id FROM dims_user WHERE login =:login';
						$ress = $db->query($sql, array(
							':login' => array('type' => PDO::PARAM_STR, 'value' => $login),
						));

						if($db->numrows($ress) == 0)
							$search_login = false;
						else
							$login = $result['lastname'].$result['firstname'].$i_login;
					}

					$user->fields['login']		= $login;
					$user->fields['lastname']	= $ct->fields['lastname'];
					$user->fields['firstname']	= $ct->fields['firstname'];
					$user->fields['email']		= $mail;
					$user->fields['id_contact'] = $idCt;
				}
				// Création d'un mot de passe [a-zA-Z0-9]
				$password = '';
				$hash_pwd = '';

				$char_list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				$size_list	= strlen($char_list)-1;

				for($i = 0; $i < 8; $i++)
				{
					$rand_nb   = mt_rand(0, $size_list);
					$password .= $char_list[$rand_nb];
				}

				$hash_pwd = dims_getPasswordHash($password);

				$user->fields['password'] = $hash_pwd;

				//On attache l'user au WS courant
				$user->save();
				$user->attachtoworkspace($_SESSION['dims']['workspaceid']);

				$idUser = $user->fields['id'];

				switch($type_elem) {
					case 'phase':
						//On attache l'user a la phase
						$userList = array();
						$tempList = array();
						$ph = new task();
						$ph->open($_SESSION['dims']['currentphase']);

						//récupération de la liste courante
						$tempList = $ph->getUsers(1);

						//on recrée la liste courante []=>id
						foreach($tempList as $id => $usr) {
							$userList[] = $id;
						}

						//Ajout a la liste des user
						$userList[] = $idUser;

						$ph->updateUsers($userList, '', 1);

						$ph->save();

					break;
					case 'task' :
						//On attache l'user a la tache
						$userList = array();
						$tempList = array();
						$ph = new task();
						$ph->open($_SESSION['dims']['currenttask']);

						//récupération de la liste courante
						$tempList = $ph->getUsers(0);

						//on recrée la liste courante []=>id
						foreach($tempList as $id => $usr) {
							$userList[] = $id;
						}

						//Ajout a la liste des user
						$userList[] = $idUser;

						$ph->updateUsers($userList, '', 0);

						$ph->save();
					break;
					default:
						//On attache l'user au projet
						$userList = array();
						$tempList = array();
						$project = new project();
						$project->open($_SESSION['dims']['currentproject']);

						//récupération de la liste courante
						$tempList = $project->getUsers();

						//on recrée la liste courante []=>id
						foreach($tempList as $id => $usr) {
							$userList[] = $id;
						}

						//Ajout a la liste des user
						$userList[] = $idUser;

						$project->updateUsers($userList);

						$project->save();
					break;
				}
				//mail
				$from	= array();
				$to	= array();
				$subject= '';
				$message= '';

				//TODO : Pouvoir detecter l'host frontoffice
				$host = 'http://events/index.php?headingid=184&articleid=140';

				$to[0]['name']	   = $user->fields['lastname'].' '.$user->fields['firstname'];
				$to[0]['address']  = $user->fields['email'];

				$from[0]['name']   = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
				$from[0]['address']= $_SESSION['dims']['user']['email'];

				$subject = $project->fields['label'].': You have been added to a project.';

				$content = 'Dear '.$user->fields['lastname'].' '.$user->fields['firstname'].'<br /><br />
					You have been added to the project : '.$project->fields['label'].'.<br />
					Now you can view and edit your tasks by connecting with your login.<br />
					please follow the link to your personal microsite : <a href="'.dims_urlencode($host).'">link</a>'."
					<br />Login: ".$user->fields['login']."
					<br />Password:".$password."<br />";

				unset($password);

				dims_send_mail($from,$to, $subject, $content);
			}

			dims_redirect($scriptenv);

		break;

	case 'affich_list_perstoadd':
		ob_end_clean();
		$id_action = dims_load_securvalue('idaction', dims_const::_DIMS_NUM_INPUT, true, false);
		echo $skin->open_simplebloc($_DIMS['cste']['_ADD_CT'],'100%','','',false);

			$ws= new workspace();
			$ws->open($_SESSION['dims']['workspaceid']);

			// construction des personnes affectées à ce projet
			$tabselusers=array();

			if (isset($_SESSION['dims']['currentproject'])) {
				$project = new project();
				$project->open($_SESSION['dims']['currentproject']);
				$tabselusers=$project->getUsers();
			}

			//on recherche les personnes inscrites pour l'action courante
			$sql_u = "SELECT user_id FROM dims_mod_business_action_utilisateur WHERE action_id = :idaction";
			$res_u = $db->query($sql_u, array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
			));
			if($db->numrows($res_u) > 0) {
				while($tab_resu = $db->fetchrow($res_u)) {
					$tab_u[$tab_resu['user_id']] = $tab_resu['user_id'];
				}
			}

			//dims_print_r($tabselusers);
			$possible_groups = $ws->getGroups();
			//dims_print_r($possible_groups);
			foreach($possible_groups as $id_group => $tab_g) {
				$gp = new group();
				$gp->open($id_group);
				$gpuser[$id_group]['name'] = $tab_g['label'];
				$gpuser[$id_group]['users'] = $gp->getusers();
				$gpuser[$id_group]['nb_users_sel'] = 0;
				//on indique si la personne est affectée ou non
				foreach($gpuser[$id_group]['users'] as $id_u => $inf_u) {
					//if(isset($tabselusers[$id_u])) {
					//	$gpuser[$id_group]['nb_users_sel']++;
					//	$gpuser[$id_group]['users'][$id_u]['selected'] = 1;
					//}
					//else
					if(isset($tab_u[$id_u])) {
						$gpuser[$id_group]['nb_users_sel']++;
						$gpuser[$id_group]['users'][$id_u]['selected'] = 1;
					}
					else $gpuser[$id_group]['users'][$id_u]['selected'] = 0;
				}
			}
			//dims_print_r($gpuser);

			echo '<div style="width: 100%; float: left;overflow:auto;">';
			if (sizeof($gpuser)>0) {//

				echo "<form id=\"form_add_participant\" name=\"form_add_participant\" method=\"POST\">";
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("op");
				$token->field("idaction");
				$token->field("groupaffect");
				$token->field("useraffect");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
				echo "		<input type=\"hidden\" name=\"op\" value=\"add_participant\">
							<input type=\"hidden\" name=\"idaction\" value=\"$id_action\">
							<div style=\"width: 100%; float: left;overflow:hidden;\">";

							foreach($gpuser as $id_group => $tab_g) {
								$gpsel = '';
								$nb_gusers = count($tab_g['users']);
								$nb_concerned = $tab_g['nb_users_sel'];
								if($nb_gusers == $nb_concerned) $gpsel = 'checked';

								echo	'<div style="width:100%; float:left;overflow:hidden;">
											<input name="groupaffect[]" id="gp_'.$id_group.'" value="'.$id_group.'" type="checkbox" '.$gpsel.' onclick="javascript:selgroup(\''.$id_group.'\', '.$nb_gusers.');">
											<span style="padding-left:10px;">
												<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'group_'.$id_group.'\');">'.$tab_g['name'].' ('.$nb_concerned.'/'.$nb_gusers.')</a>
											</span>
										</div>
										<div id="group_'.$id_group.'" style="display:none;width:100%;float:left;overflow:hidden;">';
								$cpt = 0;
								foreach($tab_g['users'] as $id_user => $tab_u) {
									$cpt++;
									$select=(isset($tabselusers[$id_user])) ? "checked" : "";
									echo	'<div style="width:100%; float:left;overflow:hidden;padding-left:30px;">
												<input name="useraffect[]" id="'.$id_group.'_'.$cpt.'" value="'.$id_user.'" type="checkbox" '.$select.' onclick="javascript:if(!this.checked) verif_gp(\''.$id_group.'\');">
												<span style="padding-left:10px;">'.$tab_u['firstname'].' '.$tab_u['lastname'].'</span>
											</div>';
								}
								echo	'</div>';
							}
							echo "</div>";
					echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_add_participant.submit();","","");
					echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"./common/img/close.png","javascript:document.getElementById('add_contact_project').style.display='none';","","float:left;");
				echo "</form>";
			}
			echo '</div>';

		echo $skin->close_simplebloc();
		die();
	break;

	case 'add_participant':
		$id_action = dims_load_securvalue('idaction', dims_const::_DIMS_NUM_INPUT, true, true);
		$date = date("YmdHis");

		// on boucle sur tableau de useraffect
		if (isset($_POST['useraffect']) || isset($_POST['groupaffect'])) {
			$action = new action();
			$action->open($id_action);
			$date_act = explode("-", $action->fields['datejour']);
			$hdeb = explode(":", $action->fields['heuredeb']);
			$hfin = explode(":", $action->fields['heurefin']);

			$tab_users = array();

			//on enregistre tous les users rattaches avant modif
			$tab_old_u = array();
			$sql_v = "SELECT user_id FROM dims_mod_business_action_utilisateur WHERE action_id = :idaction";
			$res_v = $db->query($sql_v, array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
			));
			if($db->numrows($res_v) > 0) {
				while($tab_res = $db->fetchrow($res_v)) {
					$tab_old_u[$tab_res['user_id']] = $tab_res['user_id'];
				}
			}

			if (isset($_POST['groupaffect'])) {
				//on recupere les users des groups
				$groupaffect = dims_load_securvalue('groupaffect', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($groupaffect as $key => $id_group) {
					$gp = new group();
					$gp->open($id_group);
					$tab_users[$id_group] = $gp->getusers();
				}
				//on inscrit les users
				$tab_verif = array();
				foreach($tab_users as $id_group => $tab_u) {
					foreach($tab_u as $id_user => $inf_u) {
						$tab_gpuser[$id_user] = $id_user;
						//on verifie que le user n'est pas deja associe
						if(!in_array($id_user, $tab_old_u)) {
							$sql = "INSERT INTO `dims_mod_business_action_utilisateur` (
												`user_id`,
												`action_id`,
												`resp`,
												`participate`,
												`date_demande`)
									VALUES (
											:iduser,
											:idaction,
											NULL,
											'0',
											'$date')";
							$db->query($sql, array(
								':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
								':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
							));
							$tab_verif[$id_user] = $id_user;

							//Envoi d'un email pour demande a l'utilisateur
							$user = new user();
							$user->open($id_user);

							$from = array();
							$from[0]['name'] = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
							$from[0]['address'] = $_SESSION['dims']['user']['email'];

							$to = array();
							$to[0]['name'] = $user->fields['firstname']." ".$user-> fields['lastname'];
							$to[0]['address'] = $user->fields['email'];

							$subject = "I-net Portal : Demande de participation à la tâche ".$action->fields['libelle'];

							$message = "Bonjour, <br/><br/>
										Votre participation &agrave; la r&eacute;alisation de la t&acirc;che {$action->fields['libelle']} est requise par votre groupe de travail.<br/>
										Cette t&acirc;che aura lieu le {$date_act[2]}/{$date_act[1]}/{$date_act[0]} de {$hdeb[0]} h {$hdeb[1]} &agrave; {$hfin[0]} h {$hfin[1]}.<br/><br/>
										Merci de bien vouloir indiquer votre &eacute;ventuelle disponibilit&eacute; par retour de cet email.<br/></br>
										Bien cordialement, <br/><br/>
										I-net Portal pour ".$from[0]['name'];

							dims_send_mail($from,$to,$subject,$message);
						}
					}
				}
			}
			//cas ou on n'effecte pas tout un groupe
			$useraffect = dims_load_securvalue('useraffect', dims_const::_DIMS_NUM_INPUT, true, true, true);
			foreach($useraffect as $key => $id_user) {
				if(!in_array($id_user,$tab_verif)) {
					//on verifie que le user n'est pas deja associe
					if(!in_array($id_user,$tab_old_u)) {
						$sql = "INSERT INTO `dims_mod_business_action_utilisateur` (
											`user_id`,
											`action_id`,
											`resp`,
											`participate`,
											`date_demande`)
								VALUES (
										:iduser,
										:idaction,
										NULL,
										'0',
										'$date')";
						$db->query($sql, array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
							':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
						));

						//Envoi d'un email pour demande a l'utilisateur
						$user = new user();
						$user->open($id_user);

						$from = array();
						$from[0]['name'] = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
						$from[0]['address'] = $_SESSION['dims']['user']['email'];

						$to = array();
						$to[0]['name'] = $user->fields['firstname']." ".$user-> fields['lastname'];
						$to[0]['address'] = $user->fields['email'];

						$subject = "I-net Portal : Demande de participation à la tâche ".$action->fields['libelle'];

						$message = "Bonjour, <br/><br/>
									Votre participation &agrave; la r&eacute;alisation de la t&acirc;che {$action->fields['libelle']} est requise par votre groupe de travail.<br/>
									Cette t&acirc;che aura lieu le {$date_act[2]}/{$date_act[1]}/{$date_act[0]} de {$hdeb[0]} h {$hdeb[1]} &agrave; {$hfin[0]} h {$hfin[1]}.<br/><br/>
									Merci de bien vouloir indiquer votre &eacute;ventuelle disponibilit&eacute; par retour de cet email.<br/></br>
									Bien cordialement, <br/><br/>
									I-net Portal pour ".$from[0]['name'];

						dims_send_mail($from,$to,$subject,$message);
					}
				}
			}
			//suppression des users dont l'inscription est annulee
			foreach($tab_old_u as $id_user) {
				$useraffect = dims_load_securvalue('useraffect', dims_const::_DIMS_NUM_INPUT, true, true, true);
				if(!in_array($id_user, $useraffect) && !in_array($id_user,$tab_gpuser)) {
					$sqld = "DELETE FROM dims_mod_business_action_utilisateur WHERE user_id = :iduser AND action_id = :idaction";
					$db->query($sqld, array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
						':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
					));

				}
			}
		}
		dims_redirect($scriptenv);
		break;

	case 'project_main':
	default:
		require_once(DIMS_APP_PATH . '/modules/system/desktop_project_menu.php');
		switch($_SESSION['dims']['projectmenu']) {
			default:
			case dims_const::_DIMS_PROJECTMENU_PROJECT:
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_main.php');
			break;
			case dims_const::_DIMS_PROJECTMENU_TASK :
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_todolist.php');
			break;
			case dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT :
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_suite.php');
			break;
			case dims_const::_DIMS_PROJECTMENU_ADD_PROJECT :
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_create_etape1.php');
			break;
		}
		break;
}
?>
