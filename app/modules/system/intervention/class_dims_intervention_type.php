<?php

/**
 * Description of dims_intervention
 *
 * @author Netlor
 * @copyright Wave Software / Netlor 2011
 */
class dims_intervention_type extends dims_data_object{
    const TABLE_NAME = "dims_intervention_type";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

	public function getText() {
		return $_SESSION['cste'][$this->fields['php_value']];
	}

	public static function getListType(){
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	id_module_type = :idmoduletype ";
		$db = dims::getInstance()->db;
		$res = $db->query($sel, array(
			':idmoduletype' => $_SESSION['dims']['moduletypeid']
		));
		$lst = array();
		while ($r = $db->fetchrow($res)){
			$t = new dims_intervention_type();
			$t->openFromResultSet($r);
			$lst[] = $t;
		}
		return $lst;
	}
}

?>
