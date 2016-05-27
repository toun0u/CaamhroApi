<?php

/****************************************************
*****************************************************
*** @author	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
*** @author	Florian DAVOINE [NETLOR CONCEPT]  ***
*** @version	2.0				  ***
*** @package	projects			  ***
*** @access	public				  ***
*** @licence	GPL				  ***
*****************************************************
*****************************************************/


require_once DIMS_APP_PATH.'modules/system/class_action.php';
require_once DIMS_APP_PATH.'modules/system/class_task_user.php';
require_once DIMS_APP_PATH.'include/functions/mail.php';

/* Class d'une t�che */

class task extends pagination {

	/* Constructeur */

	function __construct() {
	$this->id_globalobject = dims_const::_SYSTEM_OBJECT_TASK;
		parent::dims_data_object('dims_task');
		$this->fields['state'] = 1;
	}

	/* Fonction pour changer l'�tat de la t�che */
	function change_state($idtask) {

		$db = dims::getInstance()->getDb();

		$etat = "";
		$select = "select state as state from dims_task where id = :idtask";
		$res=$db->query($select, array(
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $idtask),
		));

		/* On r�cup�re l'�tat */
		if ($row = $db->fetchrow($res))
			$etat = $row['state'];

		/* On change d'�tat suivant l'�tat courant */
		if ($etat == 1) {
			$select = "update `dims_task` set `state` = 0 where id = :idtask";
		} else {
			$select = "update `dims_task` set `state` = 1 where id = :idtask";
		}

		$res=$db->query($select, array(
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $idtask),
		));
	}

	/* Fonction de suppression d'une t�che */

	function delete() {
		$db = dims::getInstance()->getDb();

		// suppression des ratachements de personnes
		$res=$db->query("delete from dims_task_user where id_task= :idtask", array(
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		// suppression des task_task
		$res=$db->query("delete from dims_task_task where id_task= :idtask", array(
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		//suppression des actions
		$res=$db->query("delete from dims_mod_business_action where id_task= :idtask", array(
			':idtask' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		parent::delete();
	}

	/* Fonction de sauvegarde d'une tâche */
	function save()	{

		$db = dims::getInstance()->getDb();

		parent::save(dims_const::_SYSTEM_OBJECT_TASK);

		if($this->fields['type'] == 1) {
			//changement de format des dates
			$old_act = array();
			$date_deb = array();
			$date_deb[0] = substr($this->fields['date_start'], 0, 4); //Year
			$date_deb[1] = substr($this->fields['date_start'], 4, 2); //month
			$date_deb[2] = substr($this->fields['date_start'], 6, 2); //day
			$date_fin = array();
			$date_fin[0] = substr($this->fields['date_end'], 0, 4); //Year
			$date_fin[1] = substr($this->fields['date_end'], 4, 2); //month
			$date_fin[2] = substr($this->fields['date_end'], 6, 2); //day

			$date = dims_timestamp2local($this->fields['date_start']);

			$parent_deb = explode("/",$date['date']);
			$parent_start = intval($parent_deb[0].$parent_deb[1].$parent_deb[2]."000000");

			//gestion des actions associees
			$action = new action();
			//on verifie si la tache a deja une action associee sauf pour l'action parente car son id ne changera pas
			$sql_v = "SELECT id, datejour, id_parent FROM dims_mod_business_action WHERE id_task = :idtask ORDER BY datejour";
			$res_v = $db->query($sql_v, array(
				':idtask' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			if($db->numrows($res_v) > 0) {

				while($tab_act = $db->fetchrow($res_v)) {
					//on calcul le timestamp de cette date
					$tab_date = explode("-",$tab_act['datejour']);
					$cur_date = intval($tab_date[0].$tab_date[1].$tab_date[2]."000000");

					//on verifie s'il y a des inscriptions pour cette action
					$sql_vu = "SELECT * FROM dims_mod_business_action_utilisateur WHERE action_id = :actionid ";
					$res_vu = $db->query($sql_vu, array(
						':actionid' => array('type' => PDO::PARAM_INT, 'value' => $tab_act['id']),
					));
					if($db->numrows($res_vu) > 0) {
						while($tab_o = $db->fetchrow($res_vu)) {
							if($cur_date > $parent_start) { //toutes les action_user � recreer
								//on enregistre les infos dans une table temporaire
								if(!isset($new_act_user[$tab_act['datejour']])) {
									$new_act_user[$tab_act['datejour']] = array();
								}
								$new_act_user[$tab_act['datejour']][] = $tab_o;
							}
							elseif($cur_date == $parent_start) { //cas du parent
								if(!isset($old_act[$tab_act['datejour']])) {
									$old_act[$tab_act['datejour']] = array();
								}
								$old_act[$tab_act['datejour']][] = $tab_o;
							}
							elseif($cur_date < $parent_start) { //tous les mails � envoyer pour avertir suppression
								if(!isset($mail_act[$tab_act['datejour']])) {
									$mail_act[$tab_act['datejour']] = array();
								}
								$mail_act[$tab_act['datejour']][] = $tab_o;
							}

						}
						//on supprime le rattachement qui sera recr�� par la suite
						$db->query("DELETE FROM dims_mod_business_action_utilisateur WHERE action_id = :actionid ", array(
							':actionid' => array('type' => PDO::PARAM_INT, 'value' => $tab_act['id']),
						));
					}
					if($tab_act['id_parent'] != 0) {
						//on supprime l'action sauf l'action parente
						$db->query("DELETE FROM dims_mod_business_action WHERE id = :actionid AND id_parent != 0", array(
							':actionid' => array('type' => PDO::PARAM_INT, 'value' => $tab_act['id']),
						));
					}
					else {
						//on ouvre l'id parent pour modification
						$action->open($tab_act['id']);

						//attention : l'action parente est l'action correspondant au premier jour de la  tache
						//or si on modifie la date de ce premier jour, il faut :
						// 1 : supprimer le rattachement entre cette action et les personnes eventuellement inscrites (qui devront etre prevenues par email)
						// 2 : trouver s'il y avait des inscrits au nouveau premier jour et si oui modifier le rattachement
					}
				}
			}
			else {
				$action->init_description();
			}


			//on calcul la duree de la tache pour connaitre ne nombre d'action a creer
			$datedeb_timestp = mktime(0,0,0,$date_deb[1],$date_deb[2],$date_deb[0]);

			$datefin_timestp = mktime(0,0,0,$date_fin[1],$date_fin[2],$date_fin[0]);

			$nb_jour=($datefin_timestp-$datedeb_timestp)/86400;

			$i = 0;
			$id_parent = 0;

			for($i=0; $i<=$nb_jour; $i++) {
				//on traite le cas du parent
				if($i == 0 ) {
					$action->fields['id_parent'] = 0;
					$action->fields['typeaction'] = 'Formation';
					$action->fields['libelle'] = $this->fields['label'];
					$action->fields['description'] = $this->fields['description'];
					$action->fields['datejour'] = $this->fields['date_start'];
					$action->fields['datefin'] = $this->fields['date_end'];
					$action->fields['heuredeb'] = $this->fields['heuredeb'];
					$action->fields['heurefin'] = $this->fields['heurefin'];
					$action->fields['priorite'] = $this->fields['priority'];
					$action->fields['type'] = dims_const::_PLANNING_ACTION_TSK;
					$action->fields['allow_fo'] = 0;
					$action->fields['id_organizer'] = $_SESSION['dims']['user']['id_contact'];
					$action->fields['id_responsible'] = $_SESSION['dims']['user']['id_contact'];
					$action->fields['id_task'] = $this->fields['id'];
					$action->setugm();

					$action->save();
					$id_parent = $action->fields['id'];

					if(!empty($old_act[$action->fields['datefin']])) {
						foreach($old_act[$action->fields['datefin']] as $key => $tab_action_user) {
							$new_au = new action_user();
							$new_au->init_description();
							$new_au->fields['user_id'] = $tab_action_user['user_id'];
							$new_au->fields['action_id'] = $action->fields['id'];
							$new_au->fields['participate'] = $tab_action_user['participate'];
							$new_au->fields['date_demande'] = $tab_action_user['date_demande'];
							$new_au->save();
						}
					}
				}
				elseif($id_parent > 0) {
					$act = new action();

					//calcul du jour
					$datedeb_timestp=mktime(0,0,0,$date_deb[1],$date_deb[2]+$i,$date_deb[0]);
					$djour=date("d/m/Y",$datedeb_timestp);
					$act->fields['datejour'] = business_datefr2us($djour);
					$act->fields['datefin'] = business_datefr2us($djour);

					$act->fields['id_parent'] = $id_parent;
					$act->fields['typeaction'] = 'Formation';
					$act->fields['libelle'] = $this->fields['label'];
					$act->fields['description'] = $this->fields['description'];
					$act->fields['heuredeb'] = $this->fields['heuredeb'];
					$act->fields['heurefin'] = $this->fields['heurefin'];
					$act->fields['priorite'] = $this->fields['priority'];
					$act->fields['type'] = dims_const::_PLANNING_ACTION_TSK;
					$act->fields['allow_fo'] = 0;
					$act->fields['id_organizer'] = $_SESSION['dims']['user']['id_contact'];
					$act->fields['id_responsible'] = $_SESSION['dims']['user']['id_contact'];
					$act->fields['id_task'] = $this->fields['id'];
					$act->setugm();

					$act->save();
				}
			}
			//on met a jour les inscriptions si necessaire

			//enfin, il faut pr�venir le user que l'action a ete supprim�e
			if(!empty($old_act)) {
				foreach($old_act as $day => $tab_action) {
					foreach($tab_action as $id_action => $tab_user) { //il n'y aura toujours qu'une seule action ici
						foreach($tab_user as $userid => $participate) {
							$sql_u = "SELECT lastname, firstname, email FROM dims_user WHERE id = :iduser";
							$res_u = $db->query($sql_u, array(
								':iduser' => array('type' => PDO::PARAM_INT, 'value' => $userid),
							));
							$tab_u = $db->fetchrow($res_u);

							$from[0]['name'] = "I-net Portal";
							$from[0]['address'] = "";

							$to[0]['name'] = $tab_u['firstname']." ".$tab_u['lastname'];
							$to[0]['address'] = $tab_u['email'];

							//suivant l'etat de l'inscription, on va modifier le contenu du mail et le sujet
							switch($participate) {
								case 0 : //dmd insc
									$day = date("d/m/Y", $datedeb_timestp);
									$title = "I-net Portal : Annulation d'une action";
									$message = "Bonjour, <br />
												L'action en date du $day vient d'&ecirc;tre annul&eacute;e,
												votre demande d'inscription n'est maintenant plus effective.<br /><br />
												Merci de votre attention.<br /><br />
												Bien cordialement, <br /><br />
												I-net Portal";

									dims_send_mail($from, $to, $title, $message);

									break;
								case 1 : //inscrit
									$day = date("d/m/Y", $datedeb_timestp);
									$title = "I-net Portal : Annulation d'une action";
									$message = "Bonjour, <br />
												Nous tenons &agrave; vous informer que l'action en date du $day vient d'&ecirc;tre annul&eacute;e,
												votre participation n'est donc plus requise.<br /><br />
												Merci de votre attention.<br /><br />
												Bien cordialement, <br /><br />
												I-net Portal";

									dims_send_mail($from, $to, $title, $message);

									break;
								default:
									break;
							}
						}
					}
				}
			}
		}
		return $this->fields['id'];
	}

	function getUsers($type=0) {
		//ATTENTION: type = 0 pour les taches et type = 1 pour les phases !! (c'est l'inverse dans dims_task)

		$db = dims::getInstance()->getDb();
		$tabusers=array();

		$sql =	"
					SELECT		u.id,u.firstname,u.lastname
					FROM		dims_user as u
					inner join	dims_task_user as tu
					ON		u.id=tu.id_ref
					AND		tu.type=  :type
					AND		tu.id_task = :idtask";

		$rs = $db->query($sql, array(
			':idtask' 	=> array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':type' 	=> array('type' => PDO::PARAM_INT, 'value' => $type),
		));
		while ($fields = $db->fetchrow($rs)) {
			$tabusers[$fields['id']]=$fields;
		}
		return $tabusers;
	}

	function updateUsers($tabusers,$tabgroups,$type=0) {
		$db = dims::getInstance()->getDb();
		$tabusers[]=0;
		$params = array();
		// on collecte toutes les personnes qui vont partir
		$sql =	"
					SELECT		*
					FROM		dims_task_user as tu
					WHERE		tu.id_task = :idtask
					AND		type= :type
					AND		tu.id_ref not in (".$db->getParamsFromArray($tabusers, 'iduser', $params).")";
		$params[':idtask'] 	= array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$params[':type'] 	= array('type' => PDO::PARAM_INT, 'value' => $type);

		$rs = $db->query($sql, $params);

		// boucle sur les personnes retir�es
		while ($fields = $db->fetchrow($rs)) {
			// on supprime la personne du projet et des taches
			$tu = new task_user();
			$tu->open($this->fields['id'],$fields['id_ref'],0);
			$tu->delete();
		}

		// on attache
		//foreach ($_POST['useraffect'] as $id=>$user) {
		//dims_print_r($tabusers); die();
		foreach ($tabusers as $id=>$user) {
			$tu = new task_user();

			if (!$tu->open($this->fields['id'],$user,$type)) {
				$tu->init_description();
				$tu->fields['id_task']=$this->fields['id'];
				$tu->fields['id_ref']=$user;
				$tu->fields['type']=$type;
			}
			$tu->save();
		}
		// a rajouter le traitement des groupes
	}

	public function settitle() {
		$this->title = $this->fields['label'];
	}

	public function setid_object() {
		$this->id_globalobject = dims_const::_SYSTEM_OBJECT_TASK;
	}

	public function getContent($pagination=false) {
		$params = array();
		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getContent(true));
			$limit = "LIMIT ".$this->sql_debut.", ".$this->limite_key;
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limit_key);
		}
		else $limit="";


		$sql =	"select		t.id,
					t.label,
					t.date_start,
					t.date_end,
					t.priority,
					t.state,
					t.progress,
					t.time,
					p.label as label_parent,
					p.id as phase_id_parent,
					u.lastname,
					u.firstname
			from		dims_task as t
			inner join	dims_user as u
			on		u.id=t.id_user
			left join	dims_task as p
			on		p.id = t.id_parent
			".$this->where."
			".$this->orderby."
			".$limit;

		$result_object = $this->db->query($sql, $params);

		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($result_object);
		}
		else {
			return $result_object;
		}
	}

	public function isActive() {
		return (bool) $this->fields['state'];
	}

	public function setWherepagination($where) {
		$this->where = $where;
	}

	public function setOrderPagination($orderby) {
		$this->orderby = $orderby;
	}
}
?>
