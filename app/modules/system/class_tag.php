<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_tag_category.php';

class tag extends pagination {
	const TABLE_NAME = 'dims_tag';
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_TAG;
	const TYPE_DEFAULT = 0;
	const TYPE_GEO = 1;
	const TYPE_DATE = 4;
	const TYPE_DURATION = 5;
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	function settitle(){
		$this->title = $this->fields['tag'];
	}

	function save(){
		if($this->isNew()){
			$this->setugm();
			$this->set('timestp_create',dims_createtimestamp());
		}
		$this->set('timestp_modify',dims_createtimestamp());
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function delete(){
		require_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';
		$lks = tag_globalobject::find_by(array('id_tag'=>$this->get('id')));
		foreach($lks as $lk){
			$lk->delete();
		}
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}
	/*
	 *
	 * name: linkToGlobalObject
	 * @param mixed $globalObject, can be int, object, array
	 * @return bool true if success, false if errors
	 */
	public function linkToGlobalObject($globalObject) {
		$success = false;

		if(!$this->isNew()) {
			$saveLink = function($id_tag, $id_globalobject) {
				require_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';
				$lk = tag_globalobject::find_by(array('id_tag'=>$id_tag,'id_globalobject'=>$id_globalobject),null,1);
				if(empty($lk)){
					$lk->set('timestp_modify',dims_createtimestamp());
					$lk->save();
				}else{
					$lk = new tag_globalobject();
					$lk->init_description();
					$lk->set('id_tag',$id_tag);
					$lk->set('id_globalobject',$id_globalobject);
					$lk->set('timestp_modify',dims_createtimestamp());
					$lk->save();
				}
			};

			switch(gettype($globalObject)) {
				case 'integer':
					$saveLink($this->getId(), $globalObject);
					$success = true;
					break;
				case 'object':
					if(get_class($globalObject) == 'dims_globalobject') {
						$saveLink($this->getId(), $globalObject->getId());
						$success = true;
					}
					elseif(isset($globalObject->fields['id_globalobject'])) {
						$saveLink($this->getId(), $globalObject->fields['id_globalobject']);
						$success = true;
					}
					break;
				case 'array':
					foreach($globalObject as $subGlobalObject) {
						$success &= $this->linkToGlobalObject($subGlobalObject);
					}
					break;
			}
		}

		return $success;
	}

	/*
	 * Fonction de collecte des contenus des objets liés
	 */
	public function getContent($labelFilter = '', $pagination=false) {
		$params = array();
		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getContent($labelFilter, true));
			$limit = "LIMIT :limitstart, :limitkey";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}
		else $limit="";

		$sql = 'SELECT * FROM '.self::TABLE_NAME;

		if(!empty($labelFilter)) {
			$sql .= " WHERE tag LIKE :tablabel ";
			$params[':tablabel'] = array('type' => PDO::PARAM_STR, 'value' => '%'.str_replace(' ', '%', $labelFilter).'%');
		}

		$result_object = $this->db->query($sql, $params);

		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($result_object);
		}
		else {
			$object_list = array();
			while($data_object = $this->db->fetchrow($result_object)) {
				$object = new static();
				$object->openFromResultSet($data_object);

				$object_list[] = $object;
			}
			return $object_list;
		}
	}

	//fonction qui retourne tous les tags
	// FIXME : Should not accept sql parameters without a check
	public static function getAllTags($all_data = true, $inner = '', $where = '', $order = '', $limit = ''){
		$db = dims::getInstance()->getDb();
		$select = ' * ';
		if(!$all_data) $select = ' DISTINCT(tag) ';
		$res = $db->query("SELECT ".$select." FROM ".self::TABLE_NAME." t ".$inner." ".$where." ".$order." ".$limit);
		$tags = array();
		while($fields = $db->fetchrow($res)){
			if($all_data){
				$tag = new tag();
				$tag->openFromResultSet($fields);
			}
			else $tag = $fields['tag'];
			$tags[] = $tag;
		}
		return $tags;
	}

	//suppression des tags de l'objet passé en param (id_globalobject)
	public static function removeAllTagsOn($id_go){
		require_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';
		$lks = tag_globalobject::find_by(array('id_globalobject'=>$id_go),null,1);
		foreach($lks as $lk){
			$lk->delete();
		}
	}


	public function openWithTagName($name, $workspace_id){
		$res = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE tag= :tagname AND id_workspace= :idworkspace", array(
			':tagname' => array('type' => PDO::PARAM_STR, 'value' => $name),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspace_id),
		));//on tape sur un index unique nom + workspace_id donc une seule ligne possible
		if($this->db->numrows($res)){
			$fields = $this->db->fetchrow($res);
			$this->openFromResultSet($fields);
		}
	}

	public static function getNamesFor($ids){
		$db = dims::getInstance()->getDb();
		$params = array();
		$res = $db->query("SELECT id, tag FROM dims_tag WHERE id IN (".$db->getParamsFromArray($ids, 'idtag', $params).")", $params);
		$data = array();
		while($fields = $db->fetchrow($res)){
			$data[$fields['id']] = $fields['tag'];
		}
		return $data;
	}

	public static function addTradLang($ref){
		$elem = new tag();
		$elem->init_description();
		if (!isset($elem->fields['tag_'.$ref])){
			$sql = "ALTER TABLE ".self::TABLE_NAME." ADD `tag_$ref` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
			dims::getInstance()->db->query($sql);
			$_SESSION['dims']['permanent_data']['table_descriptions'] = array();
			dims::getInstance()->initTableDescriptions($_SESSION['dims']['permanent_data']['table_descriptions']);
		}
	}

	public function getYearsContact($goCt,&$months = array()){
		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		$years = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		DISTINCT year, month
				FROM 		".matrix::TABLE_NAME."
				WHERE 		id_contact = :go
				AND 		id_tag = :id
				ORDER BY 	year";
		$params = array(
			':go'=>array('value'=>$goCt,'type'=>PDO::PARAM_INT),
			':id'=>array('value'=>$this->get('id'),'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$years[$r['year']] = $r['year'];
			$months[$r['year']] = $r['month'];
		}
		return $years;
	}

	public function getYearsTiers($goTiers,&$months = array()){
		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		$years = array();
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		DISTINCT year, month
				FROM 		".matrix::TABLE_NAME."
				WHERE 		id_tiers = :go
				AND 		id_tag = :id
				ORDER BY 	year";
		$params = array(
			':go'=>array('value'=>$goTiers,'type'=>PDO::PARAM_INT),
			':id'=>array('value'=>$this->get('id'),'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$years[$r['year']] = $r['year'];
			$months[$r['year']] = $r['month'];
		}
		return $years;
	}
}
