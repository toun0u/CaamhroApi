<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_annotation_tag.php';
require_once DIMS_APP_PATH.'modules/system/class_tag.php';

class annotation extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_annotation','id');
	}

	function save($id_object="",$execute_sql=true) {
		$db = dims::getInstance()->getDb();

		$id_tag = 0;

	$tags = array();
	if (isset($this->tags))
		$tags = preg_split('/(,)|( )/',$this->tags,-1,PREG_SPLIT_NO_EMPTY);

		$id_annotation = parent::save();

		foreach($tags as $tag) {
			$tag = trim($tag);

			$select = "SELECT id FROM dims_tag WHERE tag = :tag";
			$rs = $db->query($select, array(
				':tag' => array('type' => PDO::PARAM_STR, 'value' => $tag),
			));
			if (!($row = $db->fetchrow($rs)))
			{
				$objtag = new tag();
				$objtag->fields['tag'] = $tag;
				//$objtag->fields['id_user'] = $this->fields['id_user'];
				$id_tag = $objtag->save();
			}
			else $id_tag = $row['id'];

			$annotation_tag = new annotation_tag();
			$annotation_tag->fields['id_tag'] = $id_tag;
			$annotation_tag->fields['id_annotation'] = $id_annotation;
			$annotation_tag->save();
		}

		return($id_annotation);
	}


	function delete()
	{
		$db = dims::getInstance()->getDb();

		$select =	"
					SELECT	*
					FROM	dims_annotation_tag
					WHERE	id_annotation = :idannotation
					";

		$rs = $db->query($select, array(
			':idannotation' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $db->fetchrow($rs))
		{
			$annotation_tag = new annotation_tag();
			$annotation_tag->open($this->fields['id'], $row['id_tag']);
			$annotation_tag->delete();
		}

		parent::delete();
	}

	public function getChildrens(){
	$lstAnno = array();
	if ($this->fields['id_parent'] == 0){
		$db = dims::getInstance()->getDb();
		if (!isset($this->id_globalobject)) $this->setid_object();
		$sql = "SELECT	a.*, u.*, ct.*
			FROM	dims_annotation a
			INNER JOIN	dims_user u
			ON		u.id = a.id_user
			INNER JOIN	dims_mod_business_contact ct
			ON		u.id_contact = ct.id
			WHERE	a.id_parent = :idannotation
			ORDER BY	a.date_annotation DESC";
		$res = $db->query($sql, array(
			':idannotation' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		require_once DIMS_APP_PATH.'modules/system/class_user.php';
		require_once DIMS_APP_PATH.'modules/system/class_contact.php';
		foreach($db->split_resultset($res) as $r){
		$anno = new annotation();
		$anno->openWithFields($r['a']);
		$us = new user();
		$us->openWithFields($r['u']);
		$anno->setLightAttribute('user', $us);
		$ct = new contact();
		$ct->openWithFields($r['ct']);
		$anno->setLightAttribute('contact', $ct);
		$lstAnno[] = $anno;
		}
	}
	return $lstAnno;
	}
}
