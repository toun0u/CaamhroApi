<?

###############################################################################
#
# user actions management
#
###############################################################################

/**
* ! description !
*
* @param int $id_action
* @param int $id_record
* @param int $id_module_type
* @param int $id_module
* @return void
*
* @version 2.09
* @since 0.1
*
* @category user action management
*/
function dims_create_user_action_log($id_action, $label, $id_module_type = -1, $id_module = -1,$id_record=0,$id_object=0) {
	$db = dims::getInstance()->getDb();

	if ($id_module_type <=0) $id_module_type = $_SESSION['dims']['moduletypeid'];
	if ($id_module <=0) $id_module = $_SESSION['dims']['moduleid'];


	$user_action_log = new user_action_log();
	$user_action_log->fields['id_user'] = $_SESSION["dims"]["userid"];
	$user_action_log->fields['id_action'] = $id_action;
	$user_action_log->fields['id_module_type'] = $id_module_type;
	$user_action_log->fields['id_module'] = $id_module;
	$user_action_log->fields['id_record'] = $id_record;
	//$user_action_log->fields['label'] = $label;
	//$user_action_log->fields['id_object'] = $id_object;
	$user_action_log->fields['ip'] = $_SERVER['REMOTE_ADDR'];
	$user_action_log->fields['timestp'] = dims_createtimestamp();
	$user_action_log->save();
}

/**
* ! description !
*
* @param int $id_record
* @param int $id_action
* @param int $id_module_type
* @param int $id_module
* @return int $user_action
*
* @version 2.09
* @since 0.1
*
* @category user action management
*/
function dims_get_user_action_log($id_record=0, $id_object = 0, $id_module = 0, $id_action = 0, $limit_offset = 0, $limit_count = 25) {
	$db = dims::getInstance()->getDb();
	global $_DIMS;
	require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
	$module = new module();

	if ($id_module == 0) $id_module = $_SESSION['dims']['moduleid'];
	$module->open($id_module);

	$where = '';
	$params = array();
	if ($id_action != 0) {
		$where .= " AND dims_user_action_log.id_action = idaction";
		$params[':idaction'] = array('type' => PDO::PARAM_STR, 'value' => $id_action);
	}

	if ($id_object != 0) {
		$where .= " AND dims_mb_action.id_object = :idobject";
		$params[':idobject'] = array('type' => PDO::PARAM_STR, 'value' => $id_object);
	}

	if ($id_record != 0) {
		$where .= " AND dims_user_action_log.id_record = :idrecord";
		$params[':idrecord'] = array('type' => PDO::PARAM_STR, 'value' => $id_record);
	}

	$params[':idmodule'] = array('type' => PDO::PARAM_STR, 'value' => $id_module_type);
	$sql = "SELECT		dims_user_action_log.*,
				dims_user.id,
				dims_user.lastname,
				dims_user.firstname,
				dims_mb_action.label as action_label

		FROM		dims_user_action_log

		INNER JOIN	dims_mb_action
		ON			dims_mb_action.id_action = dims_user_action_log.id_action
		AND			dims_mb_action.id_module_type = dims_user_action_log.id_module_type

		LEFT JOIN	dims_user ON dims_user.id = dims_user_action_log.id_user
		WHERE		dims_user_action_log.id_module = :idmoduletype
		{$where}
		ORDER BY	timestp DESC ";

	if ($limit_count>0 || ($limit_offset>0 && $limit_offset>$limit_count)) {
		$sql.="LIMIT :limitoffset, :limitcount";
		$params[':limitoffset'] = array('type' => PDO::PARAM_INT, 'value' => $limit_offset);
		$params[':limitcount'] = array('type' => PDO::PARAM_INT, 'value' => $limit_count);
	}

	$result = $db->query($sql, $params);
	$user_action = array();
	while ($fields = $db->fetchrow($result)) {
		$user_action[] = array(
			'timestp'	=> $fields['timestp'],
			'id_action'	=> $fields['id_action'],
			'label'		=> $fields['label'],
			'action_label'	=> $fields['action_label'],
			'id_user'	=> $fields['id_user'],
			'user_name'	=> ($fields['id'] == null) ? $_DIMS['cste']['_DIMS_UNKNOWNUSER'] : "{$fields['lastname']} {$fields['firstname']}"
		);
	}

	return($user_action);
}

function dims_actions_getusers($id_action, $id_module_type = -1) {
	$db = dims::getInstance()->getDb();

	if ($id_module_type == -1) $id_module_type = $_SESSION['dims']['moduletypeid'];

	$sql =	"SELECT		wur.id_user
		FROM		dims_workspace_user_role wur

		INNER JOIN	dims_role_action ra ON ra.id_role = wur.id_role

		WHERE		ra.id_action = :idaction
		AND		ra.id_module_type = :idmoduletype ";

	$result = $db->query($sql, array(
		':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_action),
		':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $id_module_type),
	));
	$users = array();
	while ($fields = $db->fetchrow($result))
	{
		$users[] = $fields['id_user'];
	}

	return($users);

}

?>
