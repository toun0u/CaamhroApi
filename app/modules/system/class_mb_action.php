<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_role_action.php';

class mb_action extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mb_action','id_module_type','id_action');
	}

	function save($id_object="",$execute_sql=true) {
		$db = dims::getInstance()->getDb();

		if ($this->new && ($this->fields['id_action'] == "" || $this->fields['id_action'] <= 0)) {
			$answer = $db->query("SELECT max(id_action) as maxi from dims_mb_action where id_module_type= :idmoduletype", array(
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
			));
			$resfields=$db->fetchrow($answer);
			$this->fields['id_action']=$resfields['maxi']+1;
		}
		return(parent::save());
	}

	function delete($preserve_data = false) {
		$db = dims::getInstance()->getDb();

		if ($this->fields['id_action']!=-1 && !$preserve_data) {
			$select =	"
						SELECT	*
						FROM	dims_role_action
						WHERE	id_action = :idaction
						AND		id_module_type = :idmoduletype
						";

			$answer = $db->query($select, array(
				':idaction' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_action']),
				':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module_type']),
			));
			while ($deletefields = $db->fetchrow($answer))
			{
				$role_action = new role_action();
				$role_action->open($deletefields['id_role'],$this->fields['id_action'],$this->fields['id_module_type']);
				$role_action->delete();
			}
		}
		parent::delete();
	}
}
?>
