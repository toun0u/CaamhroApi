<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class tag_category extends pagination {
	const TABLE_NAME = 'dims_tag_category';
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_TAG_CATEGORY;

	const _TYPE_DEFAULT = 0;
	const _TYPE_GEO = 1;
	const _TYPE_DURATION = 2;

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
		$this->title = $this->fields['label'];
	}

	function save(){
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function delete(){
		require_once DIMS_APP_PATH.'modules/system/class_tag_category_object.php';
		$lks = tag_category_object::find_by(array('id_tag'=>$this->get('id')));
		foreach($lk as $lk){
			$lk->delete();
		}
		require_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$tags = tag::find_by(array('id_category'=>$this->get('id')));
		foreach($tags as $tag){
			$tag->set('id_category',0);
			$tag->save();
		}
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function linkToObject($idObj){
		require_once DIMS_APP_PATH.'modules/system/class_tag_category_object.php';
		$lk = tag_category_object::find_by(array('id_tag'=>$this->get('id'), 'id_object'=>$idObj),null,1);
		if(empty($lk)){
			$lk = new tag_category_object();
			$lk->init_description();
			$lk->set('id_tag',$this->get('id'));
			$lk->set('id_object',$idObj);
			$lk->save();
		}
		return $lk;
	}

	public static function getForObject($idObj){
		require_once DIMS_APP_PATH.'modules/system/class_tag_category_object.php';
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		t.*
				FROM 		".self::TABLE_NAME." t
				INNER JOIN 	".tag_category_object::TABLE_NAME." lk
				ON 			t.id = lk.id_tag
				WHERE 		t.id_workspace = :id_work
				AND 		lk.id_object = :ido
				ORDER BY 	t.label";
		$params = array(
			':id_work' => array('value'=>$_SESSION['dims']['workspaceid'], 'type'=>PDO::PARAM_INT),
			':ido' => array('value'=>$idObj, 'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$cat = new tag_category();
			$cat->openFromResultSet($r);
			$lst[] = $cat;
		}
		return $lst;
	}

	public function getTagLink(){
		require_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		*
				FROM 		".tag::TABLE_NAME."
				WHERE 		id_category = :id_cat
				ORDER BY 	tag";
		$params = array(
			':id_cat' => array('value'=>$this->get('id'), 'type'=>PDO::PARAM_INT),
		);
		$res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$tag = new tag();
			$tag->openFromResultSet($r);
			$lst[] = $tag;
		}
		return $lst;
	}

}
?>
