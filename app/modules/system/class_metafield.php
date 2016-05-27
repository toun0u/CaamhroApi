<?
require_once(DIMS_APP_PATH . "/modules/system/class_mb_field.php");

/**
* @author	NETLOR - Pat
* @version	1.0
* @package	business
* @access	public
*/
class metafield extends dims_data_object {
	const TABLE_NAME = "dims_mod_business_meta_field";
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	function save() {
		if ($this->fields['used']==0) {
			$mbf = new mb_field();
			$mbf->open($this->fields['id_mbfield']);
			$mbf->fields['indexed']=0; // on desindex
			$mbf->save();
		}
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		//update position
		$res=$db->query("UPDATE dims_mod_business_meta_field set position=position-1 where position > :position and id_object = :idobject and id_metacateg= :idmetacateg", array(
			':position' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_object']),
			':idmetacateg' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_metacateg']),
		));

		// on regarde maintenant le mbfield attache
		$mbf = new mb_field();
		$mbf->open($this->fields['id_mbfield']);
		if ($mbf->fields['protected']==0) {
			// on a une valeur métier, on peut supprimer le mbfield
			$mbf->delete();

			// on supprime la colonne
			if ($this->fields['id_object']==_SYSTEM_OBJECT_CONTACT) {
				$db->query("ALTER TABLE `dims_mod_business_contact` DROP `field".$this->fields['fieldname']."`");
				$db->query("optimize table dims_mod_business_contact");
			}
			else {
				$db->query("ALTER TABLE `dims_mod_business_tiers` DROP `field".$this->fields['fieldname']."`");
				$db->query("optimize table dims_mod_business_tiers");
			}
		}
		parent::delete();
	}
}
?>
