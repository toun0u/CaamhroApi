<?php
require_once DIMS_APP_PATH.'include/class_object_change.php';

class dims_object_historic extends dims_data_object {
	private $changes;//liste des modifications opérées sur l'action

	private $user;

	public function __construct() {
		parent::dims_data_object('dims_object_historic_action', 'id');
		$this->user = array();
	}

	public function open() {
		$id = func_get_arg(0);
		parent::open($id);
		$this->changes = $this->initChanges();
	}

	/*
	 * Fonction permettant d'initialiser les tables de changement pour cette action
	 */
	private function initChanges() {
		$sql = 'SELECT c.*
			FROM dims_object_historic_changes c
			INNER JOIN dims_object_historic_action a ON a.id = c.action_id
			WHERE c.action_id=:idaction';

		$res = $this->db->query($sql, array(
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$this->changes = array();

		while($tab = $this->db->fetchrow($res)) {
			$changement = new dims_object_change();
			$changement->initValues($tab['id'], $tab['action_id'], $tab['field_name'], $tab['field_before'], $tab['field_after'] );
			$this->changes[] = $changement;
		}
		return $this->changes;
	}

	public function getComment(){
		return $this->fields['comment'];
	}

	public function setComment($comment){
		$this->fields['comment'] = $comment;
	}

	public function getDateAction(){
		return $this->fields['date'];
	}

	public function setDateAction($date){
		$this->fields['date'] = $date;
	}

	public function getChanges(){
		return $this->changes;
	}

	public function getUserID(){
		return $this->fields['id_user'];
	}

	/*
	 * Fonction qui crée un action avec les ids et retourne l'action elle-même
	 */
	public function historiseAction($go, $comment, $type, $code = '', $ref = 0)
	{
		$this->init_description(true);
		$this->setugm();
		$this->fields['ref_globalobject'] = $go;
		$this->fields['comment'] = $comment;
		$this->fields['type'] = $type;
		$this->fields['code'] = $code;
		$this->fields['go_reference'] = $ref;
		$this->changes = array();
		$this->fields['date'] = date('YmdHis');
		$action_id = $this->save();
		return $this;
	}


	public function addChange($field, $previous, $next)
	{
		$changement = new dims_object_change();
		$changement->init_description();
		$changement->setActionID($this->fields['id']);
		$changement->setFieldName($field);
		$changement->setPreviousValue($previous);
		$changement->setNextValue($next);
		$changement->save();
		$this->changes[] = $changement;
	}

	public function setUserName($lastname, $firstname){
		$this->user['lastname'] = $lastname;
		$this->user['firstname'] = $firstname;

	}

	public function getUserLastName(){
		return $this->user['lastname'];
	}

	public function getUserFirstName(){
		return $this->user['firstname'];
	}

}
