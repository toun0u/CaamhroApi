<?php
require_once DIMS_APP_PATH.'include/class_dims_globalobject_link.php';
require_once DIMS_APP_PATH.'modules/system/class_mb_object.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class dims_globalobject extends DIMS_DATA_OBJECT {
	const TABLE_NAME = "dims_globalobject";

	function __construct() {
		parent::dims_data_object('dims_globalobject');
	}

	function save(){
		$need_update_go_origin = false;
		if(dims::getInstance()->isDimsSync() && empty($this->fields['id_dims_origin'])){
			$this->fields['id_dims_origin'] = (is_null(dims::getInstance()->getCurrentDimsID()))?-1:dims::getInstance()->getCurrentDimsID();
			$need_update_go_origin = true;
		}
		parent::save();
		if($need_update_go_origin){
			$this->fields['id_object_origin'] = $this->getId();
			$this->save();
		}
	}

	public function getObject($id_module,$id_object,$id_record,$title) {
		if(!is_null($id_record))
		{
			global $dims;

			$sql = "SELECT id,title
				FROM dims_globalobject
				WHERE id_module = :idmodule
				AND id_object = :idobject
				AND id_record = :idrecord";
			$res = $this->db->query($sql, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
				':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			));
			if ($this->db->numrows($res)>0) {
				if ($elem=$this->db->fetchrow($res)) {
					// on met a jour le title si le titre change
					if ($title!='' && $title!=$elem['title']) {
						$this->db->query("update dims_globalobject set title=:title where id_module = :idmodule AND id_object = :idobject AND id_record = :idrecord", array(
							':title' => array('type' => PDO::PARAM_STR, 'value' => $title),
							':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
							':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
						));
					}
					return $elem['id'];
				}
			}
			else {
				// on cree l'objet
				$this->new=true;
				$this->fields['id_module']=$id_module;
				if ($id_module==1) $mod['id']=1;
				else $mod=$dims->getModule($id_module);
				$this->fields['id_module_type']=$mod['id'];
				$this->fields['id_object']=$id_object;
				$this->fields['id_record']=$id_record;
				$this->fields['title']=$title;
				//$this->fields['link_title']='javascript:viewPropertiesObject('.$this->fields['id_object'].','.$this->fields['id_record'].','.$this->fields['id_module'].',1);';
				$this->save();

				return $this->fields['id'];
			}
			return 0;
		}
	}

	public function searchLinkFromListObjects($listGo,$id_module_type) {
		$res = array();

		if (empty($listGo)) $listGo='0';

		$params = array();
		$params[':idmoduletypeto'] = array('type' => PDO::PARAM_INT, 'value' => $id_module_type);
		$select_from = "SELECT	gll.id_globalobject_from, gl.id_module_type, gl.id_object, gll.id_globalobject_to
					FROM		dims_globalobject gl
					INNER JOIN	dims_globalobject_link gll
					ON		gl.id = gll.id_globalobject_from
					WHERE		gll.id_module_type_to = :idmoduletypeto
					AND		gll.id_globalobject_to in (".$db->getParamsFromArray($listGo, 'idglobalobjectto', $params).")";
		$res_from = $this->db->query($select_from, $params);

		while ($from = $this->db->fetchrow($res_from)) {
			$res[$from['id_globalobject_to']][$from['id_module_type']][$from['id_object']][$from['id_globalobject_from']] = $from['id_globalobject_from'];
		}

		$params = array();
		$params[':idmoduletypefrom'] = array('type' => PDO::PARAM_INT, 'value' => $id_module_type);
		$select_to = "	SELECT	gll.id_globalobject_to, gl.id_module_type, gl.id_object,gll.id_globalobject_from
					FROM		dims_globalobject gl
					INNER JOIN	dims_globalobject_link gll
					ON		gl.id = gll.id_globalobject_to
					WHERE		gll.id_module_type_from = :idmoduletypefrom
					AND		gll.id_globalobject_from in (".$db->getParamsFromArray($listGo, 'idglobalobjectto', $params).")";
		$res_to = $this->db->query($select_to, $params);

		while ($to = $this->db->fetchrow($res_to)) {
			$res[$to['id_globalobject_from']][$to['id_module_type']][$to['id_object']][$to['id_globalobject_to']] = $to['id_globalobject_to'];
		}

		return $res;
	}

	// recherche tous les liens avec ce bien : retourne une liste => id_object => liste id_globalobject
	public function searchLink($pref = 0) {
		$res = array();
		if ($pref > 0)

		$res[$pref] = array();
		$select_from = "SELECT		gll.id_globalobject_from, gl.id_object
				FROM		dims_globalobject gl
				INNER JOIN	dims_globalobject_link gll
				ON		gl.id = gll.id_globalobject_from
				WHERE		gll.id_module_type_to = :idmoduletypeto
				AND		gll.id_globalobject_to = :idglobalobjectto";

		$res_from = $this->db->query($select_from, array(
			':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
			':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
		));
		while ($from = $this->db->fetchrow($res_from)) {
			$res[$from['id_object']][$from['id_globalobject_from']] = $from['id_globalobject_from'];
		}

		$select_to = "	SELECT		gll.id_globalobject_to, gl.id_object
				FROM		dims_globalobject gl
				INNER JOIN	dims_globalobject_link gll
				ON		gl.id = gll.id_globalobject_to
				WHERE		gll.id_module_type_from = :idmoduletypefrom
				AND		gll.id_globalobject_from = :idglobalobjectfrom";
		$res_to = $this->db->query($select_to, array(
			':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
			':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
		));
		while ($to = $this->db->fetchrow($res_to)) {
			$res[$to['id_object']][$to['id_globalobject_to']] = $to['id_globalobject_to'];
		}

		if ($pref > 0)
			return $res[$pref];
		else
			return $res ;
	}

	/*
	 * name: addLink
	 * @description Crée un lien entre deux globalobject
	 * @param int|array|dims_globalobject $obj int id_object to link, array of int id_object to link, dims_globalobject to link
	 */
	public function addLink($obj) {
		if (is_numeric($obj)) {
			$go = new dims_globalobject();
			if ($go->open($obj)) {
				$insert = "	INSERT INTO	dims_globalobject_link (`id_module_type_from`,`id_globalobject_from`,`id_module_type_to`,`id_globalobject_to`)
							VALUES		(:idmoduletypefrom, :idglobalobjectfrom, :idmoduletypeto, :idglobalobjectto)";
				$this->db->query($insert, array(
					':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
					':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
					':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $go->fields['id_module_type']),
					':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $go->getId()),
				));
			}
		} elseif(is_array($obj)) {
			foreach ($obj as $id_obj) {
				if (is_numeric($id_obj)) {
					$go = new dims_globalobject();
					if ($go->open($id_obj)) {
						$insert = "	INSERT INTO	dims_globalobject_link (`id_module_type_from`,`id_globalobject_from`,`id_module_type_to`,`id_globalobject_to`)
									VALUES		(:idmoduletypefrom, :idglobalobjectfrom, :idmoduletypeto, :idglobalobjectto)";
						$this->db->query($insert, array(
							':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
							':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
							':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $obj->fields['id_module_type']),
							':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $obj->getId()),
						));
					}
				}
			}
		} else {
			$insert = "	INSERT INTO	dims_globalobject_link (`id_module_type_from`,`id_globalobject_from`,`id_module_type_to`,`id_globalobject_to`)
						VALUES		(:idmoduletypefrom, :idglobalobjectfrom, :idmoduletypeto, :idglobalobjectto)";
			$this->db->query($insert, array(
				':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
				':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $obj->fields['id_module_type']),
				':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $obj->getId()),
			));
		}
	}

	// prend en paramètre un id_globalobject, une liste d'id_globalobject ou un globalobject
	public function deleteLink($obj){
		if (is_numeric($obj)){
			$go = new dims_globalobject();
			if ($go->open($obj)){
				$insert = "	DELETE FROM	dims_globalobject_link
							WHERE		(			id_module_type_from = :idmoduletypefrom
										AND			id_globalobject_from = :idglobalobjectfrom
										AND			id_module_type_to = :idmoduletypeto
										AND			id_globalobject_to = :idglobalobjectto)

							OR			(			id_module_type_to = :idmoduletypefrom
										AND			id_globalobject_to = :idglobalobjectfrom
										AND			id_module_type_from = :idmoduletypeto
										AND			id_globalobject_from = :idglobalobjectto)";
				$this->db->query($insert, array(
					':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
					':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
					':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $go->fields['id_module_type']),
					':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $go->getId()),
				));
			}
		}elseif(is_array($obj)){
			foreach ($obj as $id_obj){
				if (is_numeric($id_obj)){
					$go = new dims_globalobject();
					if ($go->open($id_obj)){
						$insert = "	DELETE FROM	dims_globalobject_link
									WHERE		(			id_module_type_from = :idmoduletypefrom
												AND			id_globalobject_from = :idglobalobjectfrom
												AND			id_module_type_to = :idmoduletypeto
												AND			id_globalobject_to = :idglobalobjectto)

									OR			(			id_module_type_to = :idmoduletypefrom
												AND			id_globalobject_to = :idglobalobjectfrom
												AND			id_module_type_from = :idmoduletypeto
												AND			id_globalobject_from = :idglobalobjectto)";
						$this->db->query($insert, array(
				':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
				':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $go->fields['id_module_type']),
				':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $go->getId()),
			));
					}
				}
			}
		}else{
			$insert = "	DELETE FROM	dims_globalobject_link
						WHERE		(			id_module_type_from = :idmoduletypefrom
									AND			id_globalobject_from = :idglobalobjectfrom
									AND			id_module_type_to = :idmoduletypeto
									AND			id_globalobject_to = :idglobalobjectto)

						OR			(			id_module_type_to = :idmoduletypefrom
									AND			id_globalobject_to = :idglobalobjectfrom
									AND			id_module_type_from = :idmoduletypeto
									AND			id_globalobject_from = :idglobalobjectto)";
			$this->db->query($insert, array(
				':idmoduletypefrom' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
				':idglobalobjectfrom' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idmoduletypeto' => array('type' => PDO::PARAM_INT, 'value' => $obj->fields['id_module_type']),
				':idglobalobjectto' => array('type' => PDO::PARAM_INT, 'value' => $obj->getId()),
			));
		}
	}

	public function updateLightTimestamp($date=null){
		self::setLightTimestamp($this->getId(), $date);
	}

	public static function setLightTimestamp($id, $date=null){
		$db = dims::getInstance()->getDb();
		if(is_null($date)) $date = date('YmdHis');
		$db->query('UPDATE '.self::TABLE_NAME.' SET timestamp = :timestamp WHERE id = :id', array(
			':timestamp' => array('type' => PDO::PARAM_INT, 'value' => $date),
			':id' => array('type' => PDO::PARAM_INT, 'value' => $id),
		));
	}

	//Cyril - 16/03/2012 - gestion automatique de la registration du mb_object relatif à ce globalobject
	public static function checkRegistration($idgo, $id_module_type, $object) {
		if(!empty($id_module_type) && !empty($idgo)) {
			//récupération de l'instance de dims
			$dims = dims::getInstance();
			$mbo_registered = $dims->getMBObjects();

						//die($id_module_type." ".$idgo);
			if(!isset($mbo_registered[$id_module_type][$idgo])) {
				//dans ce cas on le crée
				$mbo = mb_object::create($id_module_type, $idgo, $object);
				$dims->addNewMBObject($id_module_type, $idgo, $mbo->fields);
			} else {
				//throw error
			}
		}
	}

	public function getTimestamp(){
		return $this->fields['timestamp'];
	}

	public function getLastImportTimestamp(){
		return $this->fields['last_import'];
	}

	public function openFromOrigin($id_dims_origin, $id_object_origin) {
		$res = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE id_dims_origin=:iddimsorigin AND id_object_origin=:idobjectorigin LIMIT 0,1", array(
			':iddimsorigin' => array('type' => PDO::PARAM_INT, 'value' => $id_dims_origin),
			':idobjectorigin' => array('type' => PDO::PARAM_INT, 'value' => $id_object_origin),
		));
		if($this->db->numrows($res)){
			$this->openFromResultSet($this->db->fetchrow($res));
		}
	}

	/*
	* Fonction permettant de retourner le nom de la classe associée à un globalobject
	*/
	public function getType(){
		$mb_object_fields = dims::getInstance()->getMBObjectFields($this->fields['id_module_type'], $this->fields['id_object']);
		if( ! is_null($mb_object_fields) ){
			$mb_class = dims::getInstance()->getMBClassDataFromID($mb_object_fields['id_class']);
			if( ! is_null($mb_class) ){
				return $mb_class['classname'];
			}
		}
		return null;
	}
}
?>
