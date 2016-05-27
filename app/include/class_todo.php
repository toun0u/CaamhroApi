<?php
require_once(DIMS_APP_PATH.'include/class_todo_dest.php');

/*Remise à zéro de la classe todo par Cyril le 05/07/2011 */

class todo extends dims_data_object {
	private $dest;
	private $grdest;
	private $dev_attributes;

	const TABLE_NAME = "dims_todo";

	const TODO_TYPE_UNDEFINED = 0;
	const TODO_TYPE_CLASSICAL = 1;
	const TODO_TYPE_WITH_ALL_DEST_VALIDATION = 2;

	const TODO_STATE_RELEASED = 0;
	const TODO_STATE_VALIDATED = 1;

	const TODO_SIMPLE_MESSAGE = 0;
	const TODO_RAW_TASK = 1;

	const MY_GLOBALOBJECT_CODE = 333;

	function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
		$this->dest = array();
		$this->dev_attributes = array();
	}

	function setid_object() {
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	function save(){
		if($this->isNew()){
			$this->set('state',self::TODO_STATE_RELEASED);
		}
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function delete(){
		$lk = todo_dest::find_by(array('id_todo'=>$this->get('id')));
		foreach($lk as $l)
			$l->delete();
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	function settitle(){
		$this->title = $this->fields['content'];
	}

	public function open(){
		$id = func_get_arg(0);
		parent::open($id);
		$this->initDestinataires();
	}
	public function openWithFields($fields, $unset_db = false) {
		parent::openWithFields($fields,$unset_db);
		$this->initDestinataires();
	}
	public function openFromResultSet($fields, $unset_db = false, $go_object_value = null) {
		parent::openWithFields($fields,$unset_db,$go_object_value);
		$this->initDestinataires();
	}
	public function openWithGB($id) {
		$return = parent::openWithGB($id);
		$this->initDestinataires();
		return $return;
	}
	public static function find_by($search, $order_by = null, $limit_start = null, $limit_qte = null, $force_database = null){
		$res = parent::find_by($search, $order_by, $limit_start, $limit_qte, $force_database);
		if(!empty($res) && !is_array($res)){
			$res->initDestinataires();
		}
		return $res;
	}

	public function initDestinataires(){
		$res = $this->db->query("SELECT * FROM dims_todo_dest WHERE id_todo=:idtodo AND type = :type", array(
			':idtodo' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
			':type' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_USER),
		));
		while($d = $this->db->fetchrow($res)){
			$de = new todo_dest();
			$de->openFromResultSet($d);
			$this->dest[$d['id_user']] = $de;//on met en clef l'id de l'utilisateur pour fetcher rapidement le tableau sur cette clef
		}
		$res = $this->db->query("SELECT * FROM dims_todo_dest WHERE id_todo=:idtodo AND type = :type", array(
			':idtodo' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
			':type' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_GROUP),
		));
		while($d = $this->db->fetchrow($res)){
			$de = new todo_dest();
			$de->openFromResultSet($d);
			$this->grdest[] = $de;
		}
	}

	public function setUserFrom($val){
		$this->fields['user_from'] = $val;
	}

	public function	getUserFrom(){
		return $this->fields['user_from'];
	}

	public function setPriority($val){
		$this->fields['priority'] = $val;
	}

	public function	getPriority(){
		return $this->fields['priority'];
	}

	public function setContent($val){
		$this->fields['content'] = $val;
	}

	public function	getContent(){
		return $this->fields['content'];
	}

	public function setState($val){
		$this->fields['state'] = $val;
	}

	public function	getState(){
		return $this->fields['state'];
	}

	public function isValidated(){
		return $this->getState() == self::TODO_STATE_VALIDATED;
	}

	public function setConsiderationAs($type = self::TODO_SIMPLE_MESSAGE){
		$this->fields['considered_as'] = $type;
	}

	public function isSimpleMessage(){
		return $this->fields['considered_as'] == self::TODO_SIMPLE_MESSAGE;
	}

	public function setObjectLinked($id_globalobject=0){
		$this->fields['id_globalobject_ref'] = $id_globalobject;
	}

	public function hasObjectLinked(){
		return isset($this->fields['id_globalobject_ref']) && $this->fields['id_globalobject_ref']>0;
	}

	public function getObjectLinked(){
		if($this->hasObjectLinked()) {
			$globalObject = dims_globalobject();
			$globalObject->open($this->fields['id_globalobject_ref']);

			return $globalObject;
		}
	}

	//teste si l'id passé en paramètre est celui d'un des destinataires du todo
	public function hasDestinataire($id_dest){
		return isset($this->dest[$id_dest]);
	}

	public function addDestinataire($dest_id, $from){
		$dest = todo_dest::find_by(array('id_todo'=>$this->get('id'),'id_user'=>$dest_id,'type'=>dims_const::_SYSTEM_OBJECT_USER),null,1);
		if(empty($dest)){
			$dest = new todo_dest();
			$flag = todo_dest::TODO_UNFLAGGED;
			if($from==$dest_id) $flag = todo_dest::TODO_FLAGGED_BY_USER;
			$dest->addTodoUser($this->fields['id'], $dest_id, $flag);
			$dest->fields['type'] = dims_const::_SYSTEM_OBJECT_USER;
			$dest->save();
		}
		$this->dest[] = $dest;
	}
	public function addGrDestinataire($dest_id, $from){
		$dest = todo_dest::find_by(array('id_todo'=>$this->get('id'),'id_user'=>$dest_id,'type'=>dims_const::_SYSTEM_OBJECT_GROUP),null,1);
		if(empty($dest)){
			$dest = new todo_dest();
			$flag = todo_dest::TODO_UNFLAGGED;
			if($from==$dest_id) $flag = todo_dest::TODO_FLAGGED_BY_USER;
			$dest->addTodoUser($this->fields['id'], $dest_id, $flag);
			$dest->fields['type'] = dims_const::_SYSTEM_OBJECT_GROUP;
			$dest->save();
		}
		$this->grdest[] = $dest;
	}

	public function getListDestinataires(){
		return $this->dest;
	}

	public function getListGrDestinataires(){
		return $this->grdest;
	}

	//format attendu Y-m-d H:i:s
	public function setDate($val){
		$this->fields['date'] = $val;
	}

	public function getDate(){
		return $this->fields['date'];
	}

	//format attendu Y-m-d H:i:s
	public function setDateValidation($val){
		$this->fields['date_validation'] = $val;
	}

	public function getDateValidation(){
		return $this->fields['date_validation'];
	}


	public function display($tpl_file){
		$todo = $this;
		include $tpl_file;
	}

	public function isFlagged($user){
		$flag = false;
		$dest = $this->getUserDest($user);
		if(isset($dest)) $flag = $dest->getFlag();
		return $flag;
	}

	public function getUserDest($user){
		foreach($this->getListDestinataires() as $dest){
			if($dest->fields['id_user'] == $user){
				return $dest;
			}
		}
		return null;
	}

	/*
	 * fonction laxiste permettant d'ajouter autant d'attribut que recquiert le module métier qui utilise la classe todo
	 */
	public function setDevAttribute($key, $value){
		$this->dev_attributes[$key] = $value;
	}

	/*
	 * fonction qui retourne la valeur associé à la clef métier envoyée
	 */
	public function getDevAttribute($key){
		return (isset($this->dev_attributes[$key])) ? $this->dev_attributes[$key] : null;
	}


	private $list_todo_dest = null ;
	/**
	 * No query !
	 * @param todo_dest $todo_dest
	 */
	public function addTodoDest(todo_dest $todo_dest){
		if($this->list_todo_dest == null){
			$this->list_todo_dest = array();
		}
		$this->list_todo_dest[$todo_dest->getId()] = $todo_dest;
	}

	/**
	 * No query !
	 * @return todo_dest
	 */
	public function getListTodoDest(){
		if($this->list_todo_dest != null){
			return $this->list_todo_dest;
		}else{
			return array();
		}
	}

	public function getIdUser() {
		return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
	}

	public function getUserTo() {
		return $this->getAttribut("user_to", self::TYPE_ATTRIBUT_KEY);
	}

	public function getUserBy() {
		return $this->getAttribut("user_by", self::TYPE_ATTRIBUT_KEY);
	}

	public function setUserBy($user_by, $save = false){
		$this->setAttribut("user_by", self::TYPE_ATTRIBUT_KEY, $user_by, $save);
	}

	public function getCommentBy() {
		return $this->getAttribut("comment_by", self::TYPE_ATTRIBUT_STRING);
	}

	public function valideTodo($comment = ""){
		$this->fields['user_by'] = $_SESSION['dims']['userid'];
		$this->fields['date_validation'] = date('Y-m-d 00:00:00');
		$this->fields['state'] = self::TODO_STATE_VALIDATED;
		//$this->fields['comment_by'] = $comment; // Champ non présent en BDD
		$this->save();
	}
	public function unvalideTodo(){
		$this->fields['user_by'] = 0;
		$this->fields['date_validation'] = '0000-00-00 00:00:00';
		$this->fields['state'] = self::TODO_STATE_RELEASED;
		$this->save();
	}

	public function removeDests(){
		$del = "DELETE FROM dims_todo_dest
				WHERE		id_todo = :idtodo";
		dims::getInstance()->db->query($del, array(':idtodo' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id'])));
	}

	public static function getTodosForObject($id_go){
		$db = dims::getInstance()->getDb();
		$res = $db->query("	SELECT * FROM ".self::TABLE_NAME." t
					LEFT JOIN dims_user u ON t.id_user = u.id
					LEFT JOIN dims_mod_business_contact c ON c.id = u.id_contact
					WHERE t.id_globalobject_ref = :idglobalobjectref
					ORDER BY id_parent DESC, timestp_create DESC", array(
			':idglobalobjectref' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
		));
		$todos = array();

		$separation = $db->split_resultset($res);

			foreach ($separation as $tab) {
				$todo = new todo();
			$todo->openFromResultSet($tab['t']);
				$contact = new contact();
				$contact->init_description();
				$contact->setugm();
				if( !empty($tab['c']['id']) ){
				$contact->openFromResultSet($tab['c']);
			}
			$todo->setLightAttribute('creator', $contact);
			$todos[$tab['t']['id_parent']][] = $todo;
		}
		//dims_print_r($todos);
		$ground0 = array();
		if(!empty($todos) && isset($todos[0])){
				$ground0 = $todos[0];
				foreach( $ground0 as $todo ){
					$temp = array();
					$temp[] = $todo;
					while(!empty($temp)){
						$current = array_shift($temp);
						$current->children = array();
						if( !empty($todos[$current->getId()]) ){
							foreach($todos[$current->getId()] as $sub){
								$current->children[] = $sub;
								$temp[] = $sub;
							}
						}
					}
				}
			}
		return $ground0;
	}

	public static function countNbTasks($id_user, $mb_id_object = null, $mb_id_module_type = null,$id_go = null){//par défaut aucune limitation
		$db = dims::getInstance()->getDb();

		$params = array();

		$object_type = '';
		if( ! is_null($mb_id_object) && ! is_null($mb_id_module_type)){//permet de rechercher uniquement les objets d'un certain type
			$object_type = ' AND o.id_object = :idobject AND o.id_module_type = :idmoduletype ';
			$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $mb_id_object);
			$params[':idmoduletype'] = array('type' => PDO::PARAM_INT, 'value' => $mb_id_module_type);
		}elseif(!is_null($id_go)){
			$object_type = ' AND o.id = :go ';
			$params[':go'] = array('type' => PDO::PARAM_INT, 'value' => $id_go);
		}

		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $id_user);
		$params[':todostate'] = array('type' => PDO::PARAM_STR, 'value' => self::TODO_STATE_RELEASED);
		$params[':idw'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['workspaceid']);

		$sql = "	SELECT		COUNT(t.id) AS nb
					FROM		".self::TABLE_NAME." t
					INNER JOIN	".todo_dest::TABLE_NAME." d
					ON			d.id_todo = t.id
					LEFT JOIN	dims_globalobject o
					ON			o.id = t.id_globalobject_ref
					WHERE		t.state = :todostate
					AND			d.id_user=:iduser
					AND 		t.id_workspace = :idw
					$object_type
					ORDER BY	timestp_create DESC ";

		$res = $db->query($sql, $params);
		$r = $db->fetchrow($res);
		return empty($r['nb'])?0:$r['nb'];
	}

	public static function getLastTasks($id_user, $limit=null, $mb_id_object = null, $mb_id_module_type = null){//par défaut aucune limitation
		$db = dims::getInstance()->getDb();

		$limitation = '';
		$params = array();
		if(!is_null($limit) ){
			$limitation = ' LIMIT 0, :limit';
			$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limit);
		}

		$object_type = '';
		if( ! is_null($mb_id_object) && ! is_null($mb_id_module_type)){//permet de rechercher uniquement les objets d'un certain type
			$object_type = ' AND o.id_object = :idobject AND o.id_module_type = :idmoduletype ';
			$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $mb_id_object);
			$params[':idmoduletype'] = array('type' => PDO::PARAM_INT, 'value' => $mb_id_module_type);
		}

		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $id_user);
		$params[':todostate'] = array('type' => PDO::PARAM_STR, 'value' => self::TODO_STATE_RELEASED);
		$params[':idw'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['workspaceid']);

		$res = $db->query("	SELECT		*
					FROM		".self::TABLE_NAME." t
					INNER JOIN	".todo_dest::TABLE_NAME." d
					ON			d.id_todo = t.id
					INNER JOIN	dims_globalobject o
					ON			o.id = t.id_globalobject_ref
					LEFT JOIN	".user::TABLE_NAME." u
					ON			t.id_user = u.id
					LEFT JOIN	".contact::TABLE_NAME." c
					ON			c.id = u.id_contact
					WHERE		t.state = :todostate
					AND			d.id_user=:iduser
					AND 		t.id_workspace = :idw
					$object_type
					ORDER BY	timestp_create DESC ". $limitation, $params);
		$todos = array();
		$separation = $db->split_resultset($res);

		foreach ($separation as $tab) {
			$todo = new todo();
			$todo->openFromResultSet($tab['t']);
			$todo->initDestinataires();
			$contact = new contact();
			$contact->init_description();
			$contact->setugm();
			if( !empty($tab['c']['id'])) {
				$contact->openFromResultSet($tab['c']);
			}

			$gobject = new dims_globalobject();
			$gobject->openFromResultSet($tab['o']);

			$todo->setLightAttribute('creator', $contact);
			$todo->setLightAttribute('gobject', $gobject);

			$todos[] = $todo;
		}
		return $todos;
	}

	public function purgeDestinataires(){
		$this->db->query("DELETE FROM dims_todo_dest WHERE id_todo = :idtodo", array(':idtodo' => array('type' => PDO::PARAM_INT, 'value' => $this->getId())));
	}

	public function sendNotification($tpl, $from, $dest, $link_to, $title_object, $on_the_record){
		$this->setLightAttribute('from', $from);
		$this->setLightAttribute('dest', $dest);
		$this->setLightAttribute('link_to', $link_to);
		$this->setLightAttribute('title_object', $title_object);
		$this->setLightAttribute('on_the_record', $on_the_record);
		include $tpl;
		dims_send_mail_with_pear($from->fields['email'], $dest->fields['email'], $subject, $message);
	}

	public function isValidatedByUser($user_id){
		$dest = $this->getUserDest($user_id);
		if( ! is_null($dest) && !$dest->isNew()){
			return $dest->fields['validated'];
		}
		else return false;
	}

	public static function getTodosObjUser($gobject, $idUser = 0){
		if($idUser == 0) $idUser = $_SESSION['dims']['userid'];
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		DISTINCT t.*
				FROM 		".self::TABLE_NAME." t
				INNER JOIN 	".todo_dest::TABLE_NAME." td
				ON 			td.id_todo = t.id
				WHERE 		td.id_user = :idUser
				AND 		t.id_globalobject_ref = :go
				ORDER BY 	timestp_modify DESC";
		$params = array(
			':idUser' => array('type'=>PDO::PARAM_INT,'value'=>$idUser),
			':go' => array('type'=>PDO::PARAM_INT,'value'=>$gobject),
		);
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$todo = new todo();
			$todo->openFromResultSet($r);
			$lst[$todo->get('id')] = $todo;
		}
		return $lst;
	}

	public static function getTodosObj($gobject){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		DISTINCT t.*
				FROM 		".self::TABLE_NAME." t
				WHERE 		t.id_globalobject_ref = :go
				ORDER BY 	timestp_modify DESC";
		$params = array(
			':go' => array('type'=>PDO::PARAM_INT,'value'=>$gobject),
		);
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$todo = new todo();
			$todo->openFromResultSet($r);
			$lst[$todo->get('id')] = $todo;
		}
		return $lst;
	}
}
