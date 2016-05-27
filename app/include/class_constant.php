<?php
require_once(DIMS_APP_PATH.'include/class_dims_data_object.php');

class dims_constant extends dims_data_object {
	const TABLE_NAME = 'dims_constant';
	/**
	* Class constructor
	*
	* @access public
	**/
	function dims_constant() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public static function openPhpValue($label, $idLang = -1) {
		if($idLang == -1) {
			$idLang = $_SESSION['dims']['currentlang'];
		}

		global $dims;

		$sql = 'SELECT id FROM dims_constant WHERE phpvalue LIKE :label AND id_lang = :idlang';

		$res = $dims->db->query($sql, array(
			':label' => array('type' => PDO::PARAM_STR, 'value' => $label),
			':idlang' => array('type' => PDO::PARAM_INT, 'value' => $idlang),
		));

		$constant = new dims_constant();
		$constant->init_description();

		if($dims->db->numrows($res) >= 1) {
			$info = $dims->db->fetchrow($res);

			$constant->open($info['id']);
		}
		else {
			$constant->fields['id_lang'] = $idLang;
			$constant->fields['phpvalue'] = $label;
		}

		return $constant;
	}

	public function getAllValues($php_value) {
		if(empty($_SESSION['dims']['cste_values'][$php_value])) {
			$res = $this->db->query('SELECT DISTINCT value FROM '.self::TABLE_NAME.' WHERE phpvalue = :phpvalue', array(
				':phpvalue' => array('type' => PDO::PARAM_STR, 'value' => $php_value),
			));

			$values = array();

			while($tab = $this->db->fetchrow($res)) {
				$values[] = $tab['value'];
			}
			return $_SESSION['dims']['cste_values'][$php_value] = $values;
		}
		else return $_SESSION['dims']['cste_values'][$php_value];
	}

	public function openWithContext($php_value, $id_lang) {
		$res = $this->db->query('SELECT * FROM dims_constant WHERE phpvalue=:phpvalue AND id_lang=:idlang LIMIT 0,1', array(
			':phpvalue' => array('type' => PDO::PARAM_STR, 'value' => $php_value),
			':idlang' => array('type' => PDO::PARAM_INT, 'value' => $id_lang),
		));

		if($this->db->numrows($res) == 1) {
			$fields = $this->db->fetchrow($res);
			$this->openFromResultSet($fields);
			return true;
		}
		return false;
	}

	public function setModuleType($moduletype, $save = false) {
		$this->setAttribut("moduletype", self::TYPE_ATTRIBUT_STRING, $moduletype, $save);
	}

	public function setIdLang($id_lang, $save = false) {
		$this->setAttribut("id_lang", self::TYPE_ATTRIBUT_KEY, $id_lang, $save);
	}

	public function setValue($value, $save = false) {
		$this->setAttribut("value", self::TYPE_ATTRIBUT_STRING, $value, $save);
	}

	public function setPHPValue($phpvalue, $save = false) {
		$this->setAttribut("phpvalue", self::TYPE_ATTRIBUT_STRING, $phpvalue, $save);
	}

	public static function getVal($constant) {
		if(isset($_SESSION['cste'][$constant])) {
			return $_SESSION['cste'][$constant];
		} else {
			if(defined('_DIMS_DEBUG_CSTE') && _DIMS_DEBUG_CSTE) {
				$_SESSION['cste'] = array();
				throw new Error_class(array('message' => 'Undefined constant : "'.$constant.'"'));
			}
			return '';
		}
	}
}
