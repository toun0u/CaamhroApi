<?php

/* On 'charge' les fonctions/scripts et les objets dont on aura besoin */
require_once DIMS_APP_PATH.'modules/system/class_project.php';
require_once DIMS_APP_PATH.'modules/system/class_task.php';
require_once DIMS_APP_PATH.'modules/system/class_action_user.php';
require_once DIMS_APP_PATH.'modules/system/include/projects_functions.php';

require_once DIMS_APP_PATH.'modules/system/class_milestone.php';

require_once DIMS_APP_PATH.'modules/system/class_task_task.php';
require_once DIMS_APP_PATH.'modules/system/class_task_user.php';
require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
require_once DIMS_APP_PATH.'modules/system/class_project_user.php';

require_once DIMS_APP_PATH.'include/class_skin_common.php';

require_once DIMS_APP_PATH.'include/functions/tickets.php';

echo '<script language="Javascript" src="./common/modules/system/include/projects_functions.js"> </script>';

$scriptenv = $dims->getScriptEnv();

$skin = new skin_common('dims');

echo '<div id="project">';
/**** Bloc login ****/
echo '<div id="login_block">';
if(!$_SESSION['dims']['connected']) {
	?>
	<form class="login-form" id="user-login" method="post" accept-charset="UTF-8" action="">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("dims_login");
			$token->field("dims_password");
			$token->field("op");
			$token->field("form_id",	"form_id");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<div class="formLine">
			<label for="dims_login">
				<?php echo $_DIMS['cste']['_LOGIN']; ?> :
			</label>
			<input type="text" class="form-text required" value="" size="15" id="dims_login" name="dims_login" maxlength="60"/>
		</div>
		<div class="formLine">
			<label for="dims_password">
				<?php echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD']; ?> :
			</label>
			<input type="password" class="form-text required" size="15" id="dims_password" name="dims_password" maxlength=""/>
		</div>
		<input type="submit" class="submit" value="<?php echo $_DIMS['cste']['_DIMS_LABEL_GO']; ?> >" id="edit-submit" name="op"/>
		<a href="index.php?action=getPwd">
			<?php echo $_DIMS['cste']['_DIMS_LABEL_FORGOTTEN_PASSWORD']; ?>
		</a>
		<input type="hidden" value="user_login" id="edit-user-login" name="form_id"/>
	</form>
	<?php
}
else {
	?>
	<div style="float:right;">
		<?php echo $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname']; ?>
	&nbsp;
		<a class="logoutlk" href="?dims_logout=1">
			<?php echo $_DIMS['cste']['_DIMS_LABEL_DISCONNECT']; ?>
			<img style="border:0px" src="./common/templates/frontofficelfb_front/gfx/logout.png" alt="">
		</a>
	</div>
	<?php
}

echo '</div>'; //id = login_block

/********************/

if(!isset($_SESSION['dims']['cmscurrentaction']))
	$_SESSION['dims']['cmscurrentaction']  = 0;
if (!isset($_SESSION['dims']['cmscurrentproject']))
	$_SESSION['dims']['cmscurrentproject'] = 0;
if (!isset($_SESSION['dims']['cmscurrenttask']))
	$_SESSION['dims']['cmscurrenttask']    = 0;

$action = 0;
$actioncms = dims_load_securvalue('actioncms', dims_const::_DIMS_CHAR_INPUT, true, true, false);
$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true, false, $_SESSION['dims']['cmscurrentaction']);

if ($actioncms!='') {
	$action=$actioncms;
}

$idproject = 0;
$idproject = dims_load_securvalue('idproject',dims_const::_DIMS_NUM_INPUT,true,true,false, $_SESSION['dims']['cmscurrentproject']);

if($idproject == 0)
	$_SESSION['dims']['cmscurrenttask'] = 0;

$idtask = 0;
$idtask = dims_load_securvalue('idtask',dims_const::_DIMS_NUM_INPUT,true,true,false, $_SESSION['dims']['cmscurrenttask']);

/* project */
$project = new project();

if ($_SESSION['dims']['cmscurrentproject']>0 && $op!="add_project")
	$project->open($_SESSION['dims']['cmscurrentproject']);
else
	$project->init_description();

//Set de qqs variables pour permettre l'envoie de ticket et l'upload de files
//$_SESSION['dims']['moduleid'] = $project->fields['id_module'];
//$_SESSION['dims']['currentmodule'] = $project->fields['id_module'];
//$_SESSION['dims']['desktop'] = 'block';

if($_SESSION['dims']['connected']) {
	//construction des onglets
	$class1 = '';
	$class2 = '';
	$class3 = '';

	switch($action) {
		default:
		case 'add_dmd_insc':
		case 'index':
			$class1 = '_sel';
			break;
		case 'todo':
			$class2 = '_sel';
			break;
		case 'histo':
			$class3 = '_sel';
			break;
	}
	echo '<div>
			<ul id="menu_horizontal">
				<li class="bouton_gauche'.$class1.'"><a href="index.php?action=index"><img src="./common/img/planning.png" alt="'.$_DIMS['cste']['_PLANNING'].'" />'.$_DIMS['cste']['_PLANNING'].'</a></li>
				<li class="bouton_gauche'.$class2.'"><a href="index.php?action=todo"><img src="./common/img/checkdo.png" alt="'.$_DIMS['cste']['_FORM_TASK_TIME_TODO'].'" />'.$_DIMS['cste']['_FORM_TASK_TIME_TODO'].'</a></li>
				<li class="bouton_gauche'.$class3.'"><a href="index.php?action=histo"><img src="./common/img/icon_finance.gif" alt="'.$_DIMS['cste']['_DIMS_HISTORY'].'" />'.$_DIMS['cste']['_DIMS_HISTORY'].'</a></li>
			</ul>
		</div>';
	echo '<div id="cms_projet">';
	switch($action) {
		case 'histo':

			$sql = "SELECT			t.label,
									a.id as id_action,
									a.datejour,
									a.heuredeb,
									a.heurefin,
									p.label as project

					FROM			dims_mod_business_action a

					INNER JOIN		dims_mod_business_action_utilisateur au
					ON				au.action_id = a.id
					AND				au.user_id = :userid
					AND				au.participate = 1

					INNER JOIN		dims_task t
					ON				t.id = a.id_task

					INNER JOIN		dims_project p
					ON				p.id = t.id_project

					WHERE			a.datejour < CURDATE()

					ORDER BY		a.datejour DESC";

			//echo $sql;
			$res = $db->query($sql, array(
				':userid' => $_SESSION['dims']['userid']
			));

			//on construit le tableau de resultats
			if($db->numrows($res) > 0) {
				$tab_hist = array();
				while($tab_r = $db->fetchrow($res)) {
					//on recherche l'annee de l'action
					$date_act = explode("-",$tab_r['datejour']);
					if(!isset($tab_hist[$date_act[0]])) $tab_hist[$date_act[0]] = array();
					$tab_hist[$date_act[0]][$tab_r['id_action']] = $tab_r;
					$tab_hist[$date_act[0]][$tab_r['id_action']]['date'] = $date_act[2]."/".$date_act[1]."/".$date_act[0];
				}

				//dims_print_r($tab_hist);

				//on affiche
				echo "<table width=\"100%\" cellspacing=\"0\">";
				$first = 0;
				foreach($tab_hist as $annee => $tab_action) {
					//gestion de la visibilite auto
					if($first == 0) $disp = "block";
					else $disp = "none";

					//colori des lignes
					$color = "background-color:#FFFFFF;";
					echo	'<tr>
								<td style="padding-left:15px;padding-top:15px;">
									<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'hist_'.$annee.'\');" style="text-decoration:none;">'.$annee.'</a>
								</td>
							</tr>
							<tr>
								<td>
									<div id="hist_'.$annee.'" style="width:100%;display:'.$disp.';padding-top:15px;padding-left:30px;">
										<table width="90%">
											<tr style="'.$color.'">
												<th align="left">'.$_DIMS['cste']['_AT'].'</th>
												<th align="left">'.$_DIMS['cste']['_LABEL_PROJECTS'].'</th>
												<th align="left">'.$_DIMS['cste']['_DIMS_TASKS'].'</th>
												<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_HEUREDEB'].'</th>
												<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_HEUREFIN'].'</th>
											</tr>';

					foreach($tab_action as $id_action => $info) {
					   if($color == "") $color = "background-color:#FFFFFF;";
					   else $color = "";
						echo				'<tr style="'.$color.'">
												<td>'.$info['date'].'</td>
												<td>'.$info['project'].'</td>
												<td>'.$info['label'].'</td>
												<td>'.substr($info['heuredeb'],0,5).'</td>
												<td>'.substr($info['heurefin'],0,5).'</td>
											</tr>';
					}
					echo			'</div>
								</td>';

					$first = 1; //on met a 1 pour indiquer qu'on a fait le premier
				}
				echo "</table>";
			}
			else {
				echo "<div style=\"width:100%;height:200px;padding:25px 0 0 25px;\">Aucun historique.</div>";
			}

			break;
		case 'ask_attach_task':

			$id_task = dims_load_securvalue('task_id',dims_const::_DIMS_NUM_INPUT,true,true,false);

			$taskAsk = new task();
			$taskAsk->open($id_task);

			$dmdeur = new user();
			$dmdeur->open($_SESSION['dims']['userid']);

			$_SESSION['dims']['tickets']['users_selected'][0] = $project->fields['id_create'];
			$_SESSION['dims']['tickets']['users_selected'][1] = $project->fields['id_resp'];

			$title = $_DIMS['cste']['_DIMS_TITLE_PROJ_TICKET_DMD_INSC'];

			$message = "Bonjour,<br/>";
			$message .= $dmdeur->fields['firstname']." ".$dmdeur->fields['lastname']." souhaite participer &agrave la t&acirc;che \"".$taskAsk->fields['label']."\"
						issue du projet \"".$project->fields['label']."\".<br/>
						Vous pouvez valider son inscription en le s&eacute;lectionnant dans la liste des personnes concern&eacute;es de la t&acirc;che \"".$taskAsk->fields['label']."\".";

			dims_tickets_send($title, $message, 1, '', '', dims_const::_SYSTEM_OBJECT_PROJECT, $id_task, '','');

			dims_redirect($scriptenv.'?desktop_project_suite=2&action=index', true);
			break;
		case 'task_save': /* Sauvegarde d'une tache */
			/* On crée la tache */
			$task = new task();

			/* Si la tache doit etre simplement modifiée */
			if ($idtask>0) {
				$task->open($idtask);
			}

			$task->setvalues($_POST,'task_');

			$task->fields['time']=str_replace(",",".",$task->fields['time']);

			// verification de la duree
			$task->fields['time']=doubleval($task->fields['time']);

			// test si apres virgule et bien un multiple de 0.25
			if ($task->fields['time']-intval($task->fields['time'])>0) {
				$diff = $task->fields['time'] - floor($task->fields['time']);
				if ($diff<=0.25) {
					$task->fields['time'] = floor($task->fields['time']) + 0.25;
				}

				if ($diff>0.25 && $diff<=0.5) {
					$task->fields['time'] = floor($task->fields['time']) + 0.5;
				}

				if ($diff>0.5 && $diff<=0.75) {
					$task->fields['time'] = floor($task->fields['time']) + 0.75;
				}

				if ($diff>0.75) {
					$task->fields['time'] = floor($task->fields['time']) + 1;
				}
			}
			$task->setugm();
			$task->fields['id_project']=$_SESSION['dims']['currentproject'];
			$task->save();

			$project = new project();
			$project->open($_SESSION['dims']['currentproject']);
			$project->refreshState();

			unset($_SESSION['dims']['currenttask'], $_SESSION['dims']['currentaction']);
			dims_redirect("$scriptenv");
			break;

		case 'xml_planning':
			ob_end_clean();
				include(DIMS_APP_PATH . '/modules/system/cms_planning.php');
				die();
			break;
		case 'detail_action_planning':
			ob_end_clean();
				if(isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
					$id_action=dims_load_securvalue('id_action',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if ($id_action>0) {
						require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
						echo "<div id=\"planning_view_details\">";

						$action = new action();
						$action->open($id_action);
						$tabcorrespmulti=array();

						//on recherche le nombre de personnes dont l'inscription est validée (on pourrait mettre les noms mais ???)
						$nb_participant = 0;
						$sql_nb = "SELECT user_id FROM dims_mod_business_action_utilisateur WHERE action_id = :idaction AND participate = 1";
						$res_nb = $db->query($sql_nb, array(
							':idaction' => $id_action
						));
						$nb_participant = $db->numrows($res_nb);

						//on recherche le nombre de places disponibles
						$task = new task();
						$task->open($action->fields['id_task']);

						if($task->fields['id_tiers'] > 0) {
							$tiers = new tiers();
							$tiers->open($task->fields['id_tiers']);
						}

						//les responsables du projet
						$proj = new project();
						$proj->open($task->fields['id_project']);

						$sql_u = 	"SELECT DISTINCT lastname, firstname
									FROM dims_user
									WHERE id IN ( :idresp1 , :idresp2 , :idresp3 , :idcreate )";
						$res_u = $db->query($sql_u, array(
							':idresp1' 	=> $proj->fields['id_resp'],
							':idresp2' 	=> $proj->fields['id_resp2'],
							':idresp3' 	=> $proj->fields['id_resp3'],
							':idcreate' => $proj->fields['id_create']
						));
						$list_u = '';
						if($db->numrows($res_u) > 0) {
							while($tab_u = $db->fetchrow($res_u)) {
								$list_u .= $tab_u['firstname']." ".$tab_u['lastname']." ; ";
							}
						}

						if ($action->fields['type'] == 4) {

								$da=array_reverse(explode("-",$action->fields['datejour']));

								$detail = implode($da,"/")." de ".substr($action->fields['heuredeb'],0,5)." &agrave; ".substr($action->fields['heurefin'],0,5);
								if ($action->fields['libelle'] != '') {
									$detail .= '<div><b>'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</b> : '.$action->fields['libelle'].'</div>';
								}

								if ($action->fields['typeaction'] != '') {
									$detail .= '<div><b>'.$_DIMS['cste']['_TYPE'].'</b> : '.$_DIMS['cste'][$action->fields['typeaction']].'</div>';
								}
								if ($action->fields['description'] != '') {
									$detail .= '<div><b>'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</b> : '.$action->fields['description'].'</div>';
								}

								$detail .= '<div><b>'.$_DIMS['cste']['_DIMS_LABEL_NBATTACH'].'</b> : '.$nb_participant.'/'.$task->fields['nb_place'].'</div>';

								if($list_u != '') {
									$detail .= "<div><b>Responsable(s)</b> : $list_u</div>";
								}
								if($task->fields['id_tiers'] > 0) {
									$detail .= "<div><b>Entreprise concern&eacute;e</b> : ".$tiers->fields['intitule']."</div>";
								}

								/*$pers = array();
								$persinfo = array();
								if (isset($tabcorrespmulti[$action->fields['id']])) {
										foreach($tabcorrespmulti[$action->fields['id']] as $iduser=>$nom) {
												// test si participe ou pour info
												if (isset($tabparticipate[$action->fields['id']][$iduser]) && $tabparticipate[$action->fields['id']][$iduser]==0) $persinfo[]=$nom;
												else $pers[]=$nom;

												if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_PARTICIP'].'</b> : '.implode(', ',$pers).'</div>';
												// calcul du pour info
												if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_TOINFO'].'</b> : '.implode(', ',$persinfo).'</div>';
										}
								}*/
						}
						else {
							if (isset($action->fields['firstname']) && isset($action->fields['lastname'])) {
								$detail=strtoupper(substr($action->fields['firstname'],0,1)).". ".$action->fields['lastname']."<br>Non disponible";
							}
							else {
								$detail="<br>Non disponible";
							}
						}
						echo $detail;
						/*
						if ($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action['acteur']==$_SESSION['dims']['userid']) {
								$detailpopup=$action['detail'];
								$onclick="onclick=\"javascript:location.href='admin.php?op=xml_planning_modifier_action&id=".$action['id']."'\"";
								$cursor="pointer";
						}
						else {
								$detailpopup=strtoupper(substr($action['firstname'],0,1)).". ".$action['lastname']."<br>Non disponible";
								$onclick="";
								$cursor="";
						}*/
						echo "</div>";

						//echo $skin->close_simplebloc();

					}
				}
				die();

			break;
		case 'add_dmd_insc':
			$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false, true);
			$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, false, true);
			//echo 'id_user : '.$id_user."<br/> id_action".$id_action;
			// on ouvre l'action pour v�rifier si l'action est sur plusieurs jours
			$act= new action();

			if($id_action > 0) {
				$act->open($id_action);
				if ($act->fields['id_parent']>0) {
					$id_action=$act->fields['id_parent']; // on a un parent, on regarde la liste des events
				}

				$act_usr = new action_user();
				//il faudrait tester si le user n'a pas deja� une action a faire au meme moment...
				if($act_usr->open($id_action,$id_user)) {
					echo "<p style=\"padding:30px;\">".$_DIMS['cste']['_DIMS_LABEL_DMD_WAITING']."</p>
							<p style=\"padding-left:30px;\"><a href=\"".dims_urlencode('index.php?action=index')."\">Retour au planning</a></p>";
				}
				else {

					$res=$db->query("select id from dims_mod_business_action where id_parent= :idaction or id= :idaction ", array(
						':idaction' => $id_action
					));
					$array_act=array();

					while ($f=$db->fetchrow($res)) {
						$array_act[]=$f['id'];
					}

					foreach($array_act as $k=>$f) {
						$act_usr = new action_user();
						$act_usr->init_description();
						$act_usr->fields['user_id'] = $id_user;
						$act_usr->fields['action_id'] = $f;
						$act_usr->fields['participate'] = 0;
						$act_usr->fields['date_demande'] = date("YmdHis");
						$act_usr->save();
					}
					echo "<p style=\"padding:30px;\">".$_DIMS['cste']['_DIMS_LABEL_DMD_REGISTERED']."<p>
					<p style=\"padding-left:30px;\"><a href=\"".dims_urlencode('index.php?action=index')."\">Retour au planning</a></p>";
				}

			}
			else {
				echo "<p style=\"padding:30px;\">ERREUR : Veuillez r&eacute;it&eacute;rer votre demande.</p>
						<p style=\"padding-left:30px;\"><a href=\"".dims_urlencode('index.php?action=index')."\">Retour au planning</a></p>";
			}

			break;

		case 'annul_demande':

			$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, false);
			$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false);

			//echo $id_action." ".$id_user;
			$act_usr = new action_user();
			$act_usr->open($id_action,$id_user);
			$act_usr->delete();

			dims_redirect('index.php?action=todo&annuldmd=1');

			break;
		case 'annul_action' :

			$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, false);
			$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, false);

			//on change l'etat
			$act_usr = new action_user();
			$act_usr->open($id_action,$id_user);
			$act_usr->fields['participate'] = 3;
			$act_usr->save();

			//on averti les responsables par email


			dims_redirect('index.php?action=todo');

			break;
		case 'todo':

			$annuldmd = dims_load_securvalue('annuldmd', dims_const::_DIMS_NUM_INPUT, true,false);
			$msgdmd = '&nbsp;';
			$tab_todo = '';
			$tab_do = '';
			$tab_toval = '';
			$tab_val = '';
			$tab_dont = '';
			$tab_nt = '';
			$nb_todo = 0;
			$nb_toval = 0;
			$nb_dont = 0;
			$date_d = date("Y")."-".date("m")."-".date("d");

			//Affichage des messages eventuels
			if($annuldmd != 0) $msgdmd = 'Votre demande a bien &eacute;t&eacute; annul&eacute;e.';

			//on recherche l'ensemble des actions pour l'utilisateur
			$sql_a = "	SELECT			a.*,
										au.*

						FROM			dims_mod_business_action a

						INNER JOIN		dims_mod_business_action_utilisateur au
						ON				au.action_id = a.id
						AND				au.user_id = :userid

						WHERE			a.datejour >= CURDATE()

						ORDER BY		a.id_task ASC, a.datejour DESC";

	//echo $sql_a;
			$res_a = $db->query($sql_a, array(
				':userid' => $_SESSION['dims']['userid']
			));
			if($db->numrows($res_a) > 0) {
				$style1 = "";
				$style2 = "";
				$style3 = "";
				while($taba = $db->fetchrow($res_a)) {
					$date = explode("-", $taba['datejour']);
					$date_j = $date[2]."/".$date[1]."/".$date[0];
					$date_d = dims_timestamp2local($taba['date_demande']);
					$hdeb = substr($taba['heuredeb'], 0, -3);
					$hfin = substr($taba['heurefin'], 0, -3);
					switch($taba['participate']) {
						case 0 : //en cours de validation

							$tab_val .= '<tr style="background-color:'.$style1.'">
											<td align="left">'.$taba['libelle'].'</td>
											<td align="left">'.$date_j.'</td>
											<td align="left"> de '.$hdeb.' &agrave; '.$hfin.'</td>
											<td align="left">'.$date_d['date'].'</td>
											<td align="left"><a href="index.php?action=annul_demande&id_action='.$taba['action_id'].'&id_user='.$taba['user_id'].'">Annuler la demande</a></td>
										</tr>';
							if($style1 == "") $style1 = "#FFFFFF";
							else $style1 = "";
							$nb_toval++;

							break;
						case 3 : //Annule par l'utilisateur
						case 1 : //accepte

							$tab_do .= '<tr style="background-color:'.$style2.'">
											<td align="left">'.$taba['libelle'].'</td>
											<td align="left">'.$date_j.'</td>
											<td align="left"> de '.$hdeb.' &agrave; '.$hfin.'</td>
											<td align="left">';
							if($taba['participate'] == 1)
								$tab_do .=		'<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\'index.php?action=annul_action&id_action='.$taba['action_id'].'&id_user='.$taba['user_id'].'\', \'Etes-vous certain de vouloir annuler votre participation ?\');">Annulation</a>
														<font style="font-size:10px;font-style:italic;">(Uniquement en cas d\'urgence)</font>';
							else
								$tab_do .=		'<img src="./common/img/warning.png">Participation annul&eacute;e';
							$tab_do .= '	</td>
										</tr>';

							 if($style2 == "") $style2 = "#FFFFFF";
							else $style2 = "";

							$nb_todo++;

							break;
						case 2 : //refuse

							$tab_nt .= '<tr style="background-color:'.$style3.'">
											<td align="left">'.$taba['libelle'].'</td>
											<td align="left">'.$date_j.'</td>
											<td align="left"> de '.$hdeb.' &agrave; '.$hfin.'</td>
											<td align="left"></td>
										</tr>';

							$nb_dont++;

							if($style3 == "") $style3 = "#FFFFFF";
							else $style3 = "";

							break;
					}
				}
				//Affichage
				if($nb_todo > 0) {
					$tab_todo = '<table width="96%" cellpadding="2" cellspacing="2">
									<tr style="background-color:#FFFFFF">
										<th align="left">T&acirc;ches concern&eacute;es</th>
										<th align="left">Date </th>
										<th align="left">Horaires</th>
										<th></th>
									</tr>';
					$tab_todo .= $tab_do;
					$tab_todo .= '</table>';
					if($nb_todo > 10) $disp_todo = "none";
					else $disp_todo = "block";
				}
				else {
					$tab_todo = 'Aucune action &agrave; venir.';
				}
				if($nb_toval > 0) {
					$tab_toval = '<table width="96%" cellpadding="2" cellspacing="2">
									<tr style="background-color:#FFFFFF">
										<th align="left">T&acirc;ches concern&eacute;es</th>
										<th align="left">Date </th>
										<th align="left">Horaires</th>
										<th align="left">Date de demande</th>
										<th></th>
									</tr>';
					$tab_toval .= $tab_val;
					$tab_toval .= '</table>';
					if($nb_toval < 6) $disp_toval = "block";
					else $disp_toval = "none";
				}
				else {
					$tab_toval = 'Aucune demande en cours.';
				}
				if($nb_dont > 0) {
					$tab_dont = '<table width="96%" cellpadding="2" cellspacing="2">
									<tr style="background-color:#FFFFFF">
										<th align="left">T&acirc;ches concern&eacute;es</th>
										<th align="left">Date </th>
										<th align="left">Horaires</th>
										<th></th>
									</tr>';
					$tab_dont .= $tab_nt;
					$tab_dont .= '</table>';
					if($nb_dont < 6) $disp_dont = "block";
					else $disp_dont = "none";
				}
				else {
					$tab_dont = 'Aucune demande refus&eacute;e.';
				}
			}
			else {
				$tab_todo = 'Aucune action &agrave; venir.';
				$tab_toval = 'Aucune demande en cours.';
				$tab_dont = 'Aucune demande refus&eacute;e.';
			}

			echo '	<table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="font-weight:bold;font-size:13px;">
								<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'div_todo\');" style="text-decoration:none;">Liste des actions &agrave; effectuer ('.$nb_todo.')</a>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div id="div_todo" style="display:'.$disp_todo.';width:100%;padding-left:30px;">'.$tab_todo.'</div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="font-weight:bold;font-size:13px;">
								<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'div_toval\');" style="text-decoration:none;">Liste des demande en cours de validation ('.$nb_toval.')</a>
							</td>
						</tr>
						<tr>
							<td style="color:#FF0000;">'.$msgdmd.'</td>
						</tr>
						<tr>
							<td><div id="div_toval" style="display:'.$disp_toval.';width:100%;padding-left:30px;">'.$tab_toval.'</div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="font-weight:bold;font-size:13px;">
								<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'div_dont\');" style="text-decoration:none;">Liste des demandes refus&eacute;es ('.$nb_dont.')</a>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div id="div_dont" style="display:'.$disp_dont.';width:100%;padding-left:30px;">'.$tab_dont.'</div></td>
						</tr>
					</table>';

			break;
		case 'index':
		default:

			/*if (!isset($_SESSION['dims']['currentproject']) || $_SESSION['dims']['currentproject']==0)
				require_once DIMS_APP_PATH.'modules/system/cms_projects_list.php';
			else
				require_once DIMS_APP_PATH.'modules/system/cms_projects_suite.php';*/

			if (!isset($_SESSION['business']['planning_weekadd'])) $_SESSION['business']['planning_weekadd'] = 0; // semaine courante
			?>
			<div id="dims_popup"></div>
			<div id="planning_xmlplanning" style="margin:0px auto;width:1000px;min-height:600px;clear:both;"></div>

			<script language="javascript">
			function affiche_planning(params) {
				dims_xmlhttprequest_todiv("index.php","actioncms=xml_planning"+params,'','planning_xmlplanning');
			}

			function refresh_planning() {
				affiche_planning('&cat=-1');
				return(true);
			}

			function affiche_planning_delayed(params) {

				setTimeout("affiche_planning('"+params+"')",50);
				return(true);
			}

			var affiche_planning_closure;
			function createClosures() {
				affiche_planning_closure = affiche_planning_delayed;
			}

			window.onload = createClosures;

			dims_xmlhttprequest_todiv("index.php","actioncms=xml_planning",'','planning_xmlplanning');
			//setInterval("refresh_planning()",30000);

			</script>
			<?
			require_once DIMS_APP_PATH.'modules/system/cms_planning_display.php';
			break;
	}
	echo '</div>'; //id = cms_projet
}
else
	echo $_DIMS['cste']['_DIMS_FRONT_PROJECT_LOGIN'];
echo '</div>'; //id = project
?>
