<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
		require_once module_wiki::getTemplatePath('/collaborateurs/collaborateurs/list_collaborateurs.tpl.php');
		break;
	case module_wiki::_ACTION_EDIT_COLLAB:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$user = new user();
		if ($id != '' && $id > 0)
			$user->open($id);
		else
			$user->init_description();
		$user->display(module_wiki::getTemplatePath('/collaborateurs/collaborateurs/edit_collaborateur.tpl.php'));
		break;
	case module_wiki::_ACTION_SAVE_COLLAB:
		if(dims_isactionallowed(module_wiki::_ACTION_ADMIN_EDIT_COLLAB)){
			$id = dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$pwd = dims_load_securvalue('password',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$pwd2 = dims_load_securvalue('password2',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$user = new user();
			$user->init_description();
			$tmstp = dims_createtimestamp();
			if ($id != '' && $id > 0){
				$user->open($id); // gestion du mdp
				if ($pwd != '' && $pwd == $pwd2) {
                                    $dims->getPasswordHash($pwd,$user->fields['password'],$user->fields['salt']);
                                    //$user->fields['password'] = dims_getPasswordHash($pwd);
                                }
				$user->fields['login'] = dims_load_securvalue('login',dims_const::_DIMS_CHAR_INPUT,true,true,true);

			}else{
				$user->fields['date_creation'] = $tmstp;
				$user->fields['defaultworkspace'] = $_SESSION['dims']['workspaceid'];
                $user->fields['login'] = dims_load_securvalue('login',dims_const::_DIMS_CHAR_INPUT,true,true,true);
				//$user->fields['password'] = dims_getPasswordHash($pwd);
                                $dims->getPasswordHash($pwd,$user->fields['password'],$user->fields['salt']);
				$user->fields['id_skin'] = module_wiki::DEFAULT_USER_SKIN;
				$user->fields['status'] = 1;
			}
			$user->setvalues($_POST,'ct_');
			$user->fields['function'] = dims_load_securvalue('user_function',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$user->save();

			require_once(DIMS_APP_PATH.'modules/system/class_workspace_user.php');
			$wu = new workspace_user();
			if (!$wu->open($_SESSION['dims']['workspaceid'],$user->fields['id'])){
				$user->attachtoworkspace($_SESSION['dims']['workspaceid']);
			}

			$lstGr = module_wiki::getGrDispo();
			foreach($lstGr as $gr){
				$user->detachfromgroup($gr->fields['id']);
			}

			$grId = dims_load_securvalue('services',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($grId != "" && $grId > 0)
				$user->attachtogroup($grId);

			$id_contact = $user->fields['id_contact'];
			require_once(DIMS_APP_PATH.'modules/system/crm_contact_add_photo.php');
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C));
		break;
	case module_wiki::_ACTION_SWITCH_COLLAB:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0 && dims_isactionallowed(module_wiki::_ACTION_ADMIN_VALID_COLLAB)){
			$user = new user();
			$user->open($id);
			if ($user->fields['date_expire'] == '' || $user->fields['date_expire'] == '00000000000000' || $user->fields['date_expire'] >= dims_createtimestamp()){
				$user->fields['date_expire'] = date('Ymd000000');
				$user->fields['status'] = 0;
			}else{
				$user->fields['date_expire'] = '';
				$user->fields['status'] = 1;
			}
			$user->save();
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C));
		break;
}
?>