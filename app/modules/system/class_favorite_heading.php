<?php
class favorite_heading extends dims_data_object {

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_favorite_heading');
	}


	function createchild($label = '')
	{
		$child = new favorite_heading();
		$child->fields['id_heading'] = $this->fields['id'];
		$child->fields['label'] = ($label == '') ? 'fils de '.$this->fields['label'] : $label;
		$child->fields['parents'] = $this->fields['parents'].';'.$this->fields['id'];
		$child->fields['depth'] = $this->fields['depth']+1;
		$child->fields['id_user'] = $this->fields['id_user'];
		return($child);
	}

	function getfavorites()
	{
		$db = dims::getInstance()->getDb();

		$favs = array();

		$select =	"
					SELECT	fav.*,
						mod.label as module_label,
						obj.label as object_label,
						obj.script as object_script
					FROM	dims_favorite fav,
						dims_module mod,
						dims_mb_object obj

					WHERE	fav.id_heading = :idheading
					AND	fav.id_module = mod.id
					AND	mod.id_module_type = obj.id_module_type
					AND	fav.id_object = obj.id
					";
		$rs = $db->query($select, array(
			':idheading' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($row = $db->fetchrow($rs))
		{
			$favs[$row['id']] = $row;
			$favs[$row['id']]['object_script'] = str_replace('<IDRECORD>',$row['id_record'],$favs[$row['id']]['object_script']);
			$favs[$row['id']]['object_script'] = str_replace('<IDMODULE>',$row['id_module'],$favs[$row['id']]['object_script']);
		}

		return($favs);
	}
}
