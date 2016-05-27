<?php
require_once DIMS_APP_PATH .'include/class_todo.php';
/* --------- CONTROLLEUR PERMETTANT DE GERER LES ACTIONS SUR LES TODOS d'UN GLOBALOBJECT QUEL QU'IL SOIT ---- */

$title = $this->getLightAttribute('title_list_todos');
if(!empty($title_list_todos)){
	echo $title_list_todos; //permet d'envoyer par exemple un <H3> avec genre Collaboration around ... title de l'objet
}

$keep_context = $this->getLightAttribute('keep_context');
if( ! isset($keep_context)) $keep_context = '';
?>

<?php
$todo_op =dims_load_securvalue('todo_op',dims_const::_DIMS_CHAR_INPUT,true,true,true);
if( empty($todo_op)) $todo_op = dims_const::_SHOW_COLLABORATION;

switch($todo_op){
	default:
	case dims_const::_SHOW_COLLABORATION:
		$todos = todo::getTodosForObject($this->getId());
		$this->setLightAttribute('todo_list', $todos);
		$this->display(DIMS_APP_PATH.'/include/views/todos/list.tpl.php');
		break;

	case dims_const::_EDIT_INTERVENTION:
		$todo_id = dims_load_securvalue('todo_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$todo = new todo();
		if( empty($todo_id) ){
			$todo->init_description();
			$todo->setugm();
			$new = true;
		}
		else{
			$todo->open($todo_id);
			$new = false;
		}

		//récupération de la liste des utilisateurs
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$lstUsers = $work->getUsersOpen('', '', false, ' ORDER BY dims_user.firstname, dims_user.lastname');

		$todo->setLightAttribute('users', $lstUsers);
		$todo->setLightAttribute('action_path', dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_SAVE_INTERVENTION.$keep_context);
		$todo->setLightAttribute('back_path', dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_SHOW_COLLABORATION.$keep_context);
		$todo->setLightAttribute('todo_id_globalobject_ref', $this->getId());
		$todo->setLightAttribute('todo_user_from', $_SESSION['dims']['userid']);
		$todo->display(DIMS_APP_PATH.'/include/views/todos/form.tpl.php');
		break;
	case dims_const::_SAVE_INTERVENTION:
		$go_todo = dims_load_securvalue('id_globalobject',dims_const::_DIMS_NUM_INPUT,true,true);
		$todo = new todo();
		if ( ! empty($go_todo) ){
			$todo->openWithGB($go_todo);
			$new = false;
		}
		else{
			$todo->init_description();
			$todo->setugm();
			$new = true;
		}

		$todo->setvalues($_POST, 'todo_');
		$todo->setConsiderationAs(todo::TODO_SIMPLE_MESSAGE);//on remet à 0 parce que le $_POST ne le renvoie pas s'il a été décoché

		if(isset($_POST['mode_todo']))
			$mode = dims_load_securvalue('mode_todo', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		else $mode = '';

		if( ! empty($mode) && $mode == 'validation'){
			$todo->fields['is_validator'] = 1;

			$parent = new todo();
			$parent->open($todo->fields['id_parent']);

			//il faut également mettre à jour le champ validated sur le destinataire qui valide
			$dest_parent = $parent->getUserDest($_SESSION['dims']['userid']);
			if( ! is_null($dest_parent) && ! $dest_parent->isNew()  ){
				$dest_parent->fields['validated'] = todo_dest::TODO_VALIDATED_BY_USER;
				$dest_parent->fields['date_validation'] = date('Y-m-d H:i:s');
				$dest_parent->save();
			}

			if($parent->fields['type'] == todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION){//on doit compter le nombre de fils qu'il a
				$lst_dests = $parent->getListDestinataires();
				$all_validated = true;
				foreach($lst_dests as $d){
					if( ! $d->fields['validated'] ){
						$all_validated = false;
						break;
					}
				}
			}

			if( $parent->fields['type'] != todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION || $all_validated ){
				$parent->fields['date_validation'] = date('Y-m-d H:i:s');
				$parent->fields['state'] = todo::TODO_STATE_VALIDATED;
				$parent->save();
			}
		}
		$is_todo = dims_load_securvalue('is_todo',dims_const::_DIMS_CHAR_INPUT,true,true);
		$todo->setConsiderationAs(todo::TODO_SIMPLE_MESSAGE);//permet de gérer l'édition
		if(!empty($is_todo) && $is_todo){
			$todo->setConsiderationAs(todo::TODO_RAW_TASK);
		}
		$is_all_validation_required = dims_load_securvalue('is_all_validation_required',dims_const::_DIMS_CHAR_INPUT,true,true);
		$todo->fields['type'] = todo::TODO_TYPE_CLASSICAL;//permet de gérer l'édition
		if(!empty($is_all_validation_required) && $is_all_validation_required){
			$todo->fields['type'] = todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION;
		}

		if($id_todo = $todo->save()){
			//gestion des destinataires
			$destinataires = dims_load_securvalue('dests_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
			if( ! $new ){//on supprime les destinataires précédents pour le cas où y'ai eu un changement
				$todo->purgeDestinataires();
			}
			if(!empty($destinataires)){

				$from = new user();
				$from->open($_SESSION['dims']['userid']);
				$creator = new contact();
				$creator->open($from->fields['id_contact']);
				$file = $creator->getPhotoPath(20);//real_path
				if(file_exists($file)){
					$from->setLightAttribute('picture', '<img src="'.dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/'.$creator->getPhotoWebPath(20).'">');
				}
				else{
					$from->setLightAttribute('picture', '<img src="'.dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/common/modules/system/desktopV2/templates/gfx/common/human40.png" width="20px" height="20px">');
				}
				$link_to = $this->getLightAttribute('mail_link').'#'.$todo->getId();//permet de mettre l'ancre sur le todo_id
				foreach($destinataires as $dest_id){
					if($dest_id != 'dims_nan')
						$todo->addDestinataire($dest_id, $_SESSION['dims']['userid']);
						$dest = new user();
						$dest->open($dest_id);
						if($dest->fields['ticketsbyemail'])
							$todo->sendNotification(DIMS_APP_PATH.'/include/views/todos/todo_mail.tpl.php', $from, $dest, $link_to, $this->getLightAttribute('title_object'), $this->getLightAttribute('on_the_record'));
				}
			}

			$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true);

			if( isset($from) && $from == 'desktop'){
				$redirect_on = dims_load_securvalue('redirect_on',dims_const::_DIMS_CHAR_INPUT,true,true);
				dims_redirect($redirect_on);
			}
			else {
				dims_redirect(dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_SHOW_COLLABORATION.$keep_context);
			}
		}
		else{
			//récupération de la liste des utilisateurs
			$work = new workspace();
			$work->open($_SESSION['dims']['workspaceid']);
			$lstUsers = $work->getUsersOpen($_SESSION['wiki']['collab']['name'], '', false, ' ORDER BY dims_user.firstname, dims_user.lastname');

			$todo->setLightAttribute('users', $lstUsers);
			$todo->setLightAttribute("global_error", $_SESSION['cste']['ERROR_THROWN']);
			$todo->setLightAttribute('action_path', dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_SAVE_INTERVENTION.$keep_context);
			$todo->setLightAttribute('back_path', dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_SHOW_COLLABORATION.$keep_context);
			$todo->setLightAttribute('id_globalobject_ref', $this->getId());
			$todo->display(DIMS_APP_PATH.'/include/views/todos/form.tpl.php');
		}
		break;
}
?>
