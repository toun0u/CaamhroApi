<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

require_once DIMS_APP_PATH.'modules/system/class_tag_category.php';

require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
require_once DIMS_APP_PATH.'modules/system/class_contact.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

class tag_category_object extends dims_data_object {
	const TABLE_NAME = 'dims_tag_category_object';

	private static $listObject = Array(
		tiers::MY_GLOBALOBJECT_CODE => tiers::MY_GLOBALOBJECT_CODE,
		contact::MY_GLOBALOBJECT_CODE => contact::MY_GLOBALOBJECT_CODE,
		docfile::MY_GLOBALOBJECT_CODE => docfile::MY_GLOBALOBJECT_CODE,
	);

	public static function getListObjectLabel(){
		$lst = array();
		$lst2 = self::$listObject;
		foreach($lst2 as $id){
			switch ($id) {
				case contact::MY_GLOBALOBJECT_CODE:
					$lst[$id] = $_SESSION['cste']['_DIMS_LABEL_CONTACT'];
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$lst[$id] = ucfirst($_SESSION['cste']['COMPANY_MINUSCULE']);
					break;
				case docfile::MY_GLOBALOBJECT_CODE:
					$lst[$id] = ucfirst($_SESSION['cste']['DOCUMENT']);
					break;
			}
		}
		return $lst;
	}

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_tag','id_object');
	}

	public static function getListObject(){
		return self::$listObject;
	}

	public static function getCategByObj(){
		$lst = $lstCat = array();
		$db = dims::getInstance()->getDb();
		$params = array();
		$sel = "SELECT 		c.*, lk.*
				FROM 		".tag_category::TABLE_NAME." c
				INNER JOIN 	".self::TABLE_NAME." lk
				ON 			lk.id_tag = c.id
				WHERE 		lk.id_object IN (".implode(',',self::$listObject).")
				ORDER BY 	c.label";
		$res = $db->query($sel,$params);
		$res2 = $db->split_resultset($res);
		foreach($res2 as $r){
			$cat = new tag_category();
			$cat->openFromResultSet($r['c']);
			$lst[$r['lk']['id_object']][$cat->get('id')] = $cat;
			$lstCat[$cat->get('id')] = $cat->get('id');
		}
	}

	/*
	* if $obj == false
	*	return array(id_tag => array(id_object => id_object))
	* else
	*	return array(id_tag => array('obj'=>cat, 'list'=>array(id_object => id_object)))
	*/
	public static function getObjByCateg($obj = false){
		$lst = $lstCat = array();
		$db = dims::getInstance()->getDb();
		$params = array();
		$sel = "SELECT 		c.*, lk.*
				FROM 		".tag_category::TABLE_NAME." c
				INNER JOIN 	".self::TABLE_NAME." lk
				ON 			lk.id_tag = c.id
				WHERE 		lk.id_object IN (".implode(',',self::$listObject).")
				ORDER BY 	c.label";
		$res = $db->query($sel,$params);
		$res2 = $db->split_resultset($res);
		if($obj){
			foreach($res2 as $r){
				if(!isset($lst[$r['c']['id']])){
					$cat = new tag_category();
					$cat->openFromResultSet($r['c']);
					$lst[$r['c']['id']]['obj'] = $cat;
					$lstCat[$r['c']['id']] = $r['c']['id'];
				}
				$lst[$r['c']['id']]['list'][$r['lk']['id_object']] = $r['lk']['id_object'];
			}
		}else{
			foreach($res2 as $r){
				$lst[$r['c']['id']][$r['lk']['id_object']] = $r['lk']['id_object'];
				$lstCat[$r['c']['id']] = $r['c']['id'];
			}
		}
		return $lst;
	}
}