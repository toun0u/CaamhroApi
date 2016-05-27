<?php
require_once DIMS_APP_PATH."modules/system/class_selection.php";
class selection_categ extends DIMS_DATA_OBJECT{
	const TABLE_NAME = "dims_selection_categ";
	private $lstSelection = null;

	function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function getElements(){
		if (is_null($this->lstSelection)){
			$sel = "SELECT		*
				FROM		".selection::TABLE_NAME."
				WHERE		id_categ = :idcateg
				ORDER BY	timestp ASC";
			$db = dims::getInstance()->db;
			$res = $db->query($sel, array(
				':idcateg' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$this->lstSelection = array();
			while($r = $db->fetchrow($res)){
				$s = new selection();
				$s->openFromResultSet($r);
				$this->lstSelection[] = $s;
			}
		}
		return $this->lstSelection;
	}

	public function addElement($id_go){
		$selection = selection::openByLink($this->getId(), $id_go);

		if($selection->isNew()) {
			$selection->save();

			$this->lstSelection[] = $selection;
		}
	}

	public function deleteElem($id_go) {
		$sql = "DELETE FROM ".selection::TABLE_NAME."
				WHERE		id_categ = :idcateg
				AND		id_globalobject = :idglobalobject";

		$this->db->query($sql, array(
			':idcateg' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
		));
		$this->lstSelection = null;
	}

	public function delete() {
		$sql = "DELETE FROM ".selection::TABLE_NAME."
				WHERE		id_categ = :idcateg";

		$this->db->query($sql, array(
			':idcateg' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		return parent::delete();
	}

	public static function getDefault(){
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	is_default = 1
				AND	id_user = :iduser";
		$db = dims::getInstance()->db;
		$res = $db->query($sel, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
		));
		$cat = new selection_categ();
		if ($r = $db->fetchrow($res)){
			$cat->openFromResultSet($r);
		}else{
			$cat->setugm();
			$cat->fields['timestp'] = dims_createtimestamp();
			$cat->fields['is_default'] = 1;
			$cat->fields['label'] = '_DIMS_DEFAULT';
			$cat->save();
		}
		return $cat;
	}

	public static function getCategories(){
		$lst = array();
		$lst[] = self::getDefault();
		$sel = "SELECT	*
			FROM	".self::TABLE_NAME."
			WHERE	is_default = 0
			AND	id_user = :iduser";
		$db = dims::getInstance()->db;
		$res = $db->query($sel, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
		));
		while ($r = $db->fetchrow($res)){
			$cat = new selection_categ();
			$cat->openFromResultSet($r);
			$lst[] = $cat;
		}
		return $lst;
	}
}
?>
