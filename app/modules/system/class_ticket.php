<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class ticket_deprecated extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_ticket','id');
	}

	function save() {

		if (!$this->new && $this->fields['needed_validation'] > dims_const::_DIMS_TICKETS_NONE && $this->fields['status'] < dims_const::_DIMS_TICKETS_DONE) {
			// update ticket status
			$db = dims::getInstance()->getDb();

			$sql =	"
					SELECT	td.id_user,
							MAX( IF( ISNULL(ts.status), 0, ts.status)) as max_status

					FROM	dims_ticket_dest td

					LEFT JOIN	dims_ticket_status ts
					ON		ts.id_ticket = td.id_ticket
					AND		ts.id_user = td.id_user

					WHERE	td.id_ticket = :idticket

					GROUP BY td.id_user
					";

			$rs_status = $db->query($sql, array(
				':idticket' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			$global_status = dims_const::_DIMS_TICKETS_DONE;
			while ($fields_status = $db->fetchrow($rs_status))
			{
				if ($fields_status['max_status'] < $global_status) $global_status = $fields_status['max_status'];
			}

			$this->fields['status'] = $global_status;

		}

		if ($this->new) {
			$ret = parent::save();
			// update root_id
			if (!isset($this->fields['root_id']) || $this->fields['root_id'] == '' || $this->fields['root_id'] == 0) $this->fields['root_id'] = $this->fields['id'];
			if (!isset($this->fields['parent_id']) || $this->fields['parent_id'] == '' || $this->fields['parent_id'] == 0) $this->fields['parent_id'] = $this->fields['id'];
			if ($this->fields['parent_id'] == $this->fields['id']) $this->fields['parent_id'] = 0;
			parent::save();
		}
		else $ret = parent::save();

		return($ret);
	}
}
?>
