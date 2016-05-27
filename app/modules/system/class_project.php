<?php

/****************************************************
*****************************************************
*** @authors	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
***	Patrick Nourrissier							  ***
*** @version	1.0				  ***
*** @package	projects			  ***
*** @access	public				  ***
*****************************************************
*****************************************************/

/* Class d'un projet */
require_once(DIMS_APP_PATH."modules/system/class_task.php");
require_once(DIMS_APP_PATH."modules/system/class_group.php");

class project extends pagination {
	const TABLE_NAME = 'dims_project';

    const _TYPE_MICRO_EVAL = 1;
    const _TYPE_FONDAMENTAL = 2;
    const _TYPE_MAITRISE = 3;

    public $tiers;
	/* Constructeur	*/

	function __construct() {

		parent::dims_data_object(self::TABLE_NAME);
		$this->fields['state'] = 'En cours';
		$this->isPageLimited = true;
	}

	/* Fonction pour changer l'�tat de la t�che */

	function change_state($idproject) {

		$db = dims::getInstance()->getDb();

		$etat = "";
		$select = "select state as state from dims_project where id = :idproject";
		$res=$db->query($select, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
		));

		/* On recupere l'etat */
		if ($row = $db->fetchrow($res)) $etat = $row['state'];

		/* On change d'�tat suivant l'�tat courant */
		if ($etat == 1) {
			$select = "update `dims_project` set `state` = 0 where id = :idproject";
		} else {
			$select = "update `dims_project` set `state` = 1 where id = :idproject";
		}

		$res=$db->query($select, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $idproject),
		));
	}

	/* Fonction de suppression d'un projet */
	function delete() {
		$db = dims::getInstance()->getDb();

		$sql =	"
					SELECT		*
					FROM		dims_task as t
					WHERE		t.id_project = :idproject";

		$rs = $db->query($sql, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		// boucle sur les taches a supprimer
		while ($fields = $db->fetchrow($rs)) {
			$task=new task();
			$task->open($fields['id']);
			$task->delete();
		}
		parent::delete();
	}

	/* Fonction de sauvegarde d'un projet */
	function save()	{
		return parent::save(dims_const::_SYSTEM_OBJECT_PROJECT);
	}

	function refreshState() {
		$db = dims::getInstance()->getDb();
		$sql =	"
					SELECT	sum(progress) as progress,
							count(id) as cpte

					FROM	dims_task
					WHERE	dims_task.id_project= :idproject";

		$rs_status = $db->query($sql, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($fields_status = $db->fetchrow($rs_status)) {
			$state=$fields_status['progress'];
			$cpte=$fields_status['cpte'];
			$this->fields['progress'] = ($state/$cpte)-(($state/$cpte)%10);
			$this->save();
		}
	}

	function getUsers() {
		$db = dims::getInstance()->getDb();
		$tabusers=array();

		$sql =	"
					SELECT	u.id,u.firstname,u.lastname
					FROM		dims_user as u
					inner join	dims_project_user as pu
					ON			u.id=pu.id_ref
					AND			pu.type=0
					AND			pu.id_project = :idproject";

		$rs = $db->query($sql, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($fields = $db->fetchrow($rs)) {
			$tabusers[$fields['id']]=$fields;
		}
		return $tabusers;
	}

	function getUsersByTask() {
		$db = dims::getInstance()->getDb();
		$tabusers=array();

		$sql =	"
					SELECT		t.id as idtask,
								u.id,
								u.firstname,
								u.lastname,
								tu.type
					FROM		dims_user as u
					inner join	dims_task_user as tu
					ON			u.id=tu.id_ref
					inner join	dims_task as t
					ON			t.id_project= :idproject
					AND			t.id = tu.id_task";

		$rs = $db->query($sql, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($fields = $db->fetchrow($rs)) {
			$tabusers[$fields['idtask']][$fields['type']][$fields['id']]=$fields;
		}
		return $tabusers;
	}

	function getStatusByTask($made=1) {
		$db = dims::getInstance()->getDb();
		$tabtasks=array();

		$sql =	"
					SELECT		id_task,
								sum(time) as cpte
					FROM		dims_task_action
					WHERE		id_project= :idproject
					AND			state= :state
					GROUP BY	id_task";

		$rs = $db->query($sql, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':state' => array('type' => PDO::PARAM_INT, 'value' => $made),
		));
		while ($fields = $db->fetchrow($rs)) {
			$tabtasks[$fields['idtask']]=$fields['cpte'];
		}
		return $tabtasks;
	}

	public function getNextDate() {
		$sql =	"
					SELECT		max(date_start) as maxi
					FROM		dims_task
					WHERE		id_project= :idproject";

		$rs = $this->db->query($sql, array(
			':idproject' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($this->db->numrows($rs)>0) {
			while ($fields = $this->db->fetchrow($rs)) {
				if ($fields['maxi']!=null)
					$maxdate=$fields['maxi'];
				else
					$maxdate=$this->fields['date_start'];
			}
		}
		else {
			$maxdate=$this->fields['date_start'];
		}

		return $maxdate;
	}


	function updateUsers($tabusers,$tabgroups = array()) {
		$db = dims::getInstance()->getDb();

		$params = array();
		$tabusers[]=0;
		// on collecte toutes les personnes qui vont partir
		$sql =	"
					SELECT		*
					FROM		dims_project_user as pu
					WHERE		pu.id_project = :idproject
					AND			type=0
					AND			pu.id_ref not in (".$db->getParamsFromArray($tabusers, 'idref', $params).")";
		$params[':idproject'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$rs = $db->query($sql);

		// boucle sur les personnes retir�es
		while ($fields = $db->fetchrow($rs)) {
			// on supprime la personne du projet et des taches
			$pu = new project_user();
			$pu->open($this->fields['id'],$fields['id_ref'],0);
			$pu->delete();
		}

		//on traite tout d'abord les groupes
		if(count($tabgroups) > 0) {
			foreach ($tabgroups as $id => $group) {
				$gp = new group();
				$gp->open($group);
				$tab_guser = $gp->getusers();

				foreach($tab_guser as $id_user => $inf_u) {
					$pg = new project_user();
					if (!$pg->open($this->fields['id'],$id_user,0)) { //verif si pas deja present
						$pg->init_description();
						$pg->fields['id_project']=$this->fields['id'];
						$pg->fields['id_ref']=$id_user;
						$pg->fields['type']=0;

						$pg->save();
					}
				}
			}
		}


		if(count($tabusers) > 0) {
			foreach ($tabusers as $id=>$user) {
				$pu = new project_user();

				if (!$pu->open($this->fields['id'],$user,0)) { //verif si pas deja present
					$pu->init_description();
					$pu->fields['id_project']=$this->fields['id'];
					$pu->fields['id_ref']=$user;
					$pu->fields['type']=0;

					$pu->save();
				}
			}
		}
	}

	public function settitle() {
		$this->title = $this->fields['label'];
	}

	public function setid_object() {
		$this->id_globalobject = dims_const::_SYSTEM_OBJECT_PROJECT;
	}

	public function getContent($pagination=false) {
		$params = array();
		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getContent(true));
			$limit = "LIMIT ".$this->sql_debut.", ".$this->limite_key;
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}
		else $limit="";

		$sql = 'SELECT * FROM '.self::TABLE_NAME.' '.$limit;

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

	public static function getListClientForResp($idResp){
		require_once DIMS_APP_PATH."modules/system/class_tiers.php";
		$sel = "SELECT	    DISTINCT t.*
			FROM	    ".tiers::TABLE_NAME." t
			INNER JOIN  ".self::TABLE_NAME." p
			ON	    p.id_tiers = t.id
			WHERE	    p.id_resp = :idresp
			GROUP BY    t.id";
		$db = dims::getInstance()->db;
		$lst = array();
		$res = $db->query($sel, array(
			':idresp' => array('type' => PDO::PARAM_INT, 'value' => $idResp),
		));
		while ($r = $db->fetchrow($res)){
			$t = new tiers();
			$t->openFromResultSet($r);
			$lst[$r['id']] = $t;
		}
		return $lst;
	}

	public function getAll($search_text = '', $id_resp = 0, $type = 0, $id_tiers = 0, $pagination = false){
		$params = array();
		require_once DIMS_APP_PATH."modules/system/class_tiers.php";
		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getAll($search_text, $id_resp, $type, $id_tiers, true));
			$limit = " LIMIT ".$this->sql_debut.", ".$this->limite_key;
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		} else
			$limit="";
		$where = "WHERE 1=1 ";
		if(!empty($search_text)){
			$tag = "%".implode('%', explode(' ', $search_text))."%";
			$where .= " AND p.label LIKE :tag";
			$params[':tag'] = array('type' => PDO::PARAM_INT, 'value' => '%'.str_replace(' ', '%', $search_text).'%');
		}
		if ($id_resp > 0){
			$where .= " AND p.id_resp = :idresp ";
			$params[':idresp'] = array('type' => PDO::PARAM_INT, 'value' => $id_resp);
		}
		if ($id_tiers > 0){
			$where .= " AND p.id_tiers = :idtier ";
			$params[':idtier'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
		}
		if ($type > 0){
			$where .= " AND p.type = $type ";
			$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
		}

		$sql = "SELECT p.*, t.*
				FROM ".self::TABLE_NAME." p
				INNER JOIN ".tiers::TABLE_NAME." t
				ON t.id = p.id_tiers
				$where
				ORDER BY t.intitule ASC, p.label ASC " . $limit;

		$res_sql = $this->db->query($sql, $params);
		$all = array();
		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($res_sql);
		}
		else {
			foreach($this->db->split_resultset($res_sql) as $tab){
				$p = new project();
				$p->openFromResultSet($tab['p']);
				$t = new tiers();
				$t->openFromResultSet($tab['t']);
				$p->tiers = $t;
				$all[] = $p;
			}
			return $all;
		}
	}

	public function getLabel(){
		return $this->fields['label'];
	}

	public function getTypeObject(){
		return $_SESSION['cste']['PROJET'];
	}
}
