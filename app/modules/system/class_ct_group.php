<?php
require_once DIMS_APP_PATH."modules/system/class_ct_group_link.php";
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class ct_group extends dims_data_object {
	const TABLE_NAME = "dims_mod_business_contact_group";
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function save() {
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		$del = "DELETE FROM  dims_mod_business_contact_group_link
				WHERE		 id_group_ct = :idgroupct";
		$db->query($del, array(
			':idgroupct' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		parent::delete();
	}

	public function getContactsGroup($type = 0){
		$res = array();
		switch($type){
			case dims_const::_SYSTEM_OBJECT_CONTACT :
				$notIn = array();
				$sel = "SELECT		DISTINCT c.*
						FROM		dims_mod_business_contact c
						INNER JOIN	dims_mod_business_contact_group_link dl
						ON			c.id_globalobject = dl.id_globalobject
						WHERE		dl.id_group_ct = :idgroupct
						AND			dl.type_contact = ".dims_const::_SYSTEM_OBJECT_CONTACT."
						ORDER BY	c.lastname, c.firstname";
				$rSel = $this->db->query($sel, array(
					':idgroupct' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				));
				while($r = $this->db->fetchrow($rSel)){
					$ct = new contact();
					$ct->openWithFields($r);
					$res[] = $ct;
					$notIn[] = $r['id'];
				}
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS :
				require_once DIMS_APP_PATH."/modules/system/class_tiers.php";
				$sel = "SELECT		e.*
						FROM		dims_mod_business_tiers e
						INNER JOIN	dims_mod_business_contact_group_link dl
						ON			e.id_globalobject = dl.id_globalobject
						WHERE		dl.id_group_ct = :idgroupct
						AND			dl.type_contact = ".dims_const::_SYSTEM_OBJECT_TIERS."
						ORDER BY	e.intitule";
				$rSel = $this->db->query($sel, array(
					':idgroupct' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				));
				while($r = $this->db->fetchrow($rSel)){
					$ct = new tiers();
					$ct->openWithFields($r);
					$res[] = $ct;
				}
				break;
		}
		return $res;
	}

	public static function getNbGroupsForContact($id_gb, $type, $id_user = 0){
		$nb = 0;
		$db = dims::getInstance()->getDb();
		if ($id_user == 0) $id_user = $_SESSION['dims']['userid'];
		if ($id_gb > 0 && ($type == dims_const::_SYSTEM_OBJECT_CONTACT || $type == dims_const::_SYSTEM_OBJECT_TIERS)){
			$sel = "SELECT	COUNT(gl.id) as nbgr
				FROM	dims_mod_business_contact_group_link gl
				INNER JOIN	dims_mod_business_contact_group cg
				ON		cg.id = gl.id_group_ct
				AND		cg.id_user_create = :iduser
				WHERE	gl.id_globalobject = :idglobalobject
				AND		gl.type_contact = :type";
			$res = $db->query($sel, array(
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
				':type' => array('type' => PDO::PARAM_INT, 'value' => $type),
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_gb),
			));
			if ($r = $db->fetchrow($res))
			$nb = $r['nbgr'];
		}
		return $nb;
	}

	public function isInGroup($id_gb){
	$val = false;
	if ($id_gb > 0){
		$sel = "SELECT	*
			FROM	dims_mod_business_contact_group_link
			WHERE	id_group_ct = :idgroupct
			AND		id_globalobject = :idglobalobject";
		$res = $this->db->query($sel, array(
			':idgroupct' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_gb),
		));
		$val = $this->db->numrows($res) > 0;
	}
	return $val;
	}

	public function deOrAttachContact($id_gb,$type){
		if ($id_gb > 0){
			$sel = "SELECT	*
				FROM	dims_mod_business_contact_group_link
				WHERE	id_group_ct = :idgroupct
				AND		id_globalobject = :idglobalobject";
			$res = $this->db->query($sel, array(
				':idgroupct' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_gb),
			));
			if($r = $this->db->fetchrow($res)){
			$this->db->query("DELETE FROM	dims_mod_business_contact_group_link
					  WHERE		id_group_ct = :idgroupct
					  AND		id_globalobject = :idglobalobject", array(
				':idgroupct' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_gb),
			));
			}elseif($type == dims_const::_SYSTEM_OBJECT_CONTACT || $type == dims_const::_SYSTEM_OBJECT_TIERS){
			require_once DIMS_APP_PATH."/modules/system/class_ct_group_link.php";
			$lk = new ct_group_link();
			$lk->fields['id_globalobject'] = $id_gb;
			$lk->fields['type_contact'] = $type;
			$lk->fields['id_group_ct'] = $this->fields['id'];
			$lk->fields['date_create'] = dims_createtimestamp();
			$lk->save();
			}
		}
	}

	public static function getGroupsLinked($go, $type){
		$sel = "SELECT 		DISTINCT g.*
				FROM 		".self::TABLE_NAME." g
				INNER JOIN 	".ct_group_link::TABLE_NAME." lk
				ON 			lk.id_group_ct = g.id
				WHERE 		lk.id_globalobject = :go
				AND 		lk.type_contact = :type
				ORDER BY 	g.label";
		$params = array(
			':go'=>array('value'=>$go,'type'=>PDO::PARAM_INT),
			':type'=>array('value'=>$type,'type'=>PDO::PARAM_INT),
		);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$g = new ct_group();
			$g->openFromResultSet($r);
			$lst[] = $g;
		}
		return $lst;
	}

	public function getNbObjLinked($type){
		return count(ct_group_link::find_by(array('type_contact'=>$type,'id_group_ct'=>$this->get('id'))));
	}
}
