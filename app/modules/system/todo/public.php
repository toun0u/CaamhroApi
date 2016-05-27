<?php
require_once DIMS_APP_PATH.'include/class_todo.php';
$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch($action) {
	case 'edit':
		unset($_SESSION['desktopv2']['todo']);
		$todo_id = dims_load_securvalue('todo_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$todo = new todo();

		if ($todo_id > 0) {
			$todo->open($todo_id);

			$sel = "SELECT		DISTINCT dims_mod_business_action.id
					FROM		dims_mod_business_action
					INNER JOIN	dims_matrix
					ON			dims_matrix.id_activity = :idglobalobject
					AND			dims_matrix.id_action > 0
					AND			dims_matrix.id_action = dims_mod_business_action.id_globalobject
					GROUP BY	dims_matrix.id_action";

			$res = $db->query($sel, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
			));
		} else {
			$todo->init_description();
		}

		$date = $todo->fields['date'];
		$date_validation = $todo->fields['date_validation'];
		$todo->setLightAttribute('date',$date);
		$todo->setLightAttribute('date_validation',$date_validation);
		break;
	case 'save':
		/*
		dims_print_r($_SESSION['dims']);
		die();
		 */
		require_once DIMS_APP_PATH."modules/system/class_matrix.php";

		$todo_id			= dims_load_securvalue('todo_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$type				= dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, false, true);
		$priority			= dims_load_securvalue('priority', dims_const::_DIMS_CHAR_INPUT, false, true);
		$date				= dims_load_securvalue('date', dims_const::_DIMS_CHAR_INPUT, false, true);
		$date_validation	= dims_load_securvalue('date_validation', dims_const::_DIMS_CHAR_INPUT, false, true);
		$content			= dims_load_securvalue('content', dims_const::_DIMS_CHAR_INPUT, false, true);
		$user_to			= dims_load_securvalue('user_to', dims_const::_DIMS_CHAR_INPUT, false, true);
		$state				= dims_load_securvalue('state', dims_const::_DIMS_NUM_INPUT, false, true);
		$id_record			= dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, false, true);
		$id_object			= dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, false, true);
		$id_parent			= dims_load_securvalue('id_parent', dims_const::_DIMS_NUM_INPUT, false, true);
		$id_module			= dims_load_securvalue('id_module', dims_const::_DIMS_NUM_INPUT, false, true);
		$id_module_type		= dims_load_securvalue('id_module_type', dims_const::_DIMS_NUM_INPUT, false, true);
		$id_country			= !empty($_SESSION['dims']['user']['country']) ? $_SESSION['dims']['user']['country'] : 0;

		$todo = new todo();
		$todo->init_description();

		if ($todo_id > 0) {
			$todo->open($todo_id);
		}

		$todo->setugm();
		$todo->fields['type'] = $type;
		$todo->fields['priority'] = $priority;
		$todo->fields['date'] = dims_getdatetime();
		$date = dims_getdatetimedetail($todo->fields['date']);
		$todo->fields['content'] = $content;
		$todo->fields['user_to'] = $user_to;
		$todo->fields['state'] = $state;
		$todo->fields['id_record'] = $id_record;
		$todo->fields['id_object'] = $id_object;
		$todo->fields['id_parent'] = $id_parent;
		$todo->fields['id_module_type'] = $_SESSION['dims']['moduletypeid'];
		$todo->save();

		$bouton_enregistrement = dims_load_securvalue('enregistrement',dims_const::_DIMS_CHAR_INPUT,true,true,true);

		switch($bouton_enregistrement) {
			default:
			case $_SESSION['cste']['_SAVE_TODO'] :
				dims_redirect($dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&force_desktop=1');
				break;
			case $_SESSION['cste']['_SAVE_TODO_AND_ADD_NEW_ONE'] :
				dims_redirect($dims->getScriptEnv().'?mode=todo&action=edit');
				break;
		}
		break;
}
