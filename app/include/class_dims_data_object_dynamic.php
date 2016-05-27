<?php
/**
* Generic data object class. Allow object-oriented database records manipulation with dynamics fields
*
* @version 1.0
*
* @access public
* @abstract
*
* @package includes
* @subpackage data object
*
* @author Netlor Concept
* @copyright  2011 Netlor SAS
* @license http://www.netlor.fr
*/

require_once DIMS_APP_PATH.'modules/system/dynfield/class_meta_field.php';

class dims_data_object_dynamic extends dims_data_object {
	/**
	* constructor
	*
	* @param int one or more database record ID
	*
	* @access public
	*
	* @uses init_description()
	**/
	private $id_object;
	private $id_module_type;
	private $metierEnvList;
	private $metierEnvForm;

	 function dims_data_object_dynamic($id_object,$id_module_type) {
		$this->id_object = $id_object;
		$this->id_module_type = $id_module_type;

		if(!isset($this->db)) {
			$this->setdb(dims::getInstance()->getDB());
		}
	 }

	 /*
	 * Get id_object value
	 */
	 public function getIdObject() {
		  return $this->id_object;
	 }

	 /*
	 * Get id_module_type
	 */
	 public function getIdModuleType() {
		  return $this->id_module_type;
	 }

	public function setMetierEnvList($env) {
		$this->metierEnvList = $env;
	}

	public function setMetierEnvForm($env) {
		$this->metierEnvForm = $env;
	}

	public function getMetierEnvList() {
		return $this->metierEnvList;
	}

	public function getMetierEnvForm() {
		return $this->metierEnvForm;
	}

	 /*
	 * Fonction permettant d'afficher la liste des champs
	 */
	 public function displayListFields() {
		global $_DIMS;
		global $skin;
		require_once(DIMS_APP_PATH . '/modules/system/dynfield/global.php');
		require_once(DIMS_APP_PATH . '/modules/system/dynfield/view/dynfield_field_list.php');
	 }

	 /*
	 * Fonction permettant d'afficher la liste des champs
	 */
	 public function displayAddFields($tablename, $metafield_id = 0) {
		global $_DIMS;

		$metafield = $this->getDynField($metafield_id);

		require_once(DIMS_APP_PATH . '/modules/system/dynfield/global.php');
		require_once(DIMS_APP_PATH . '/modules/system/dynfield/view/dynfield_field.php');
	 }

	 /*
	 * Fonction permettant de recuperer les champs utiliser
	 */

	 public function getMbFieldsUsed($clefMbfield = true) {
		$mbfields_used = array();
		$sql = "SELECT		distinct id_mbfield, dims_meta_field.*,
					dims_mb_field.label,dims_mb_field.protected,dims_mb_field.indexed,
					dims_mb_field.label AS titlefield, dims_mb_field.name as namefield
			FROM		dims_meta_field
			INNER JOIN	dims_mb_field
			ON		dims_mb_field.id=dims_meta_field.id_mbfield
			AND		dims_meta_field.id_object = :idobject
			AND		dims_meta_field.id_module_type = :idmoduletype";

		$res=$this->db->query($sql, array(
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $this->id_object),
			':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->id_module_type),
		));
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				if ($clefMbfield)
					$mbfields_used[$f['id_mbfield']]=$f;
				else
					$mbfields_used[$f['id']]=$f;
			}
		  }
		  return $mbfields_used;
	 }

	public function getDynField($id_metafield = 0) {
		$meta_field = new meta_field();
		$meta_field->init_description();

		if(!empty($id_metafield)) {
			$meta_field->open($id_metafield);
		}

		return $meta_field;
	}

	public function addDynFields(meta_field $mfield) {
		if($this->db->tableexist($this->tablename)) {
			// on ajout cette nouvelle référence dans le champ
			$ind = $mfield->fields['fieldname'];

			// on ajoute le nouveau champ
			$this->db->query("ALTER TABLE `".$this->tablename."` ADD `field".$ind."` VARCHAR( 255 ) NULL ");
		}
	}

	public function deleteDynField(meta_field $meta_field) {
		if($this->db->tableexist($this->tablename)) {
			$sql = 'SELECT COUNT(*) AS total FROM '.$this->tablename.'
				WHERE field'.$meta_field->fields['fieldname'].' != ""
				AND field'.$meta_field->fields['fieldname'].' IS NOT NULL';

			$res = $this->db->query($sql);
			$info = $this->db->fetchrow($res);

			if($info['total'] == 0) {
				$sql = "SELECT	column_name
					FROM	information_schema.columns
					WHERE	TABLE_SCHEMA = :database
					AND	table_name = :tablename
					AND	column_name LIKE :columnname";

				$res = $this->db->query($sql, array(
					':database' => array('type' => PDO::PARAM_STR, 'value' => _DIMS_DB_DATABASE),
					':tablename' => array('type' => PDO::PARAM_STR, 'value' => $tablename),
					':columnname' => array('type' => PDO::PARAM_STR, 'value' => 'field'.$meta_field->fields['fieldname']),
				));

				if($this->db->numrows($res)) {
					// on supprime la colonne
					$this->db->query("ALTER TABLE `".$this->tablename."` DROP `field".$meta_field->fields['fieldname']."`");
					$this->db->query("optimize table :tablename", array(
						':tablename' => array('type' => PDO::PARAM_STR, 'value' => $this->tablename),
					));
				}

				$meta_field->delete();
			}
			else {
				$meta_field->fields['used']=0;
				$meta_field->save();
			}
		}
	}

	public static function getBestFieldName($id_object,$tablename) {
		$db = dims::getInstance()->getDB();
		// Recherche d'un slot libre pour construire le fieldname
		$ind=1;
		$slots=array();
		$trouve=false;

		// Recherche des champs dynamique existant pour l'id_object
		$res=$db->query('SELECT fieldname FROM dims_meta_field WHERE id_object = :idobject ORDER BY fieldname ASC', array(
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		));

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				if($f['fieldname'] != $ind) {
					/* Vérification au niveau de la structure de la table
					 * de la non-existance de la column
					 */
					$sql = "SELECT	column_name
						FROM	information_schema.columns
						WHERE	TABLE_SCHEMA = :database
						AND		table_name = :tablename
						AND		column_name LIKE :columnname";
					$res_schema = $db->query($sql, array(
						':database' => array('type' => PDO::PARAM_STR, 'value' => _DIMS_DB_DATABASE),
						':tablename' => array('type' => PDO::PARAM_STR, 'value' => $tablename),
						':columnname' => array('type' => PDO::PARAM_STR, 'value' => 'field'.$ind),
					));
					if(!$db->numrows($res_schema))
						break;
					else {
						/* Si la column existe malgré l'absence de l'enregistrement meta_field
						 * On re-test avec un nouvel indice et en ré-initialisant le pointeur mysql sur $res
						 */
						$ind++;
						mysql_data_seek($res, 0);
					}
				}
				else $ind ++;
			}
		}
		else {
			$sql = "SELECT	column_name
				FROM	information_schema.columns
				WHERE	TABLE_SCHEMA = :database
				AND	table_name = :tablename
				AND	column_name LIKE 'field%'";

			$res_schema = $db->query($sql, array(
				':database' => array('type' => PDO::PARAM_STR, 'value' => _DIMS_DB_DATABASE),
				':tablename' => array('type' => PDO::PARAM_STR, 'value' => $tablename),
			));

			while($info_schema = $db->fetchrow($res_schema)) {
				if($info_schema['column_name'] != 'field'.$ind)
					break;
				else
					$ind++;
			}
		}

		return $ind;
	}

	public static function isUniqName($name, $id_object) {
		$db = dims::getInstance()->getDB();

		$sql = 'SELECT id FROM dims_meta_field WHERE name LIKE :name AND id_object = :idobject';

		$res = $db->query($sql, array(
			':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		));

		return !(bool)$db->numrows($res);
	}
}
