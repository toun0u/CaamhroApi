<?php
    require_once DIMS_APP_PATH."include/class_todo.php";
	$lst_tasks = todo::getLastTasks($_SESSION['dims']['userid']);
?>
<script type="text/javascript" src="./assets/javascripts/common/views/todos/functions.js"></script>
<div class="companies_recently">
	<h2 class="h1_zone_companies_recently"><?php echo $_SESSION['cste']['_TODOS'].' ('.count($lst_tasks).')'; ?></h2>
	<?php
	if(count($lst_tasks)){
		?>
		<div id="todos" class="zone_todos" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_todos']) && $_SESSION['desktopV2']['content_droite']['zone_todos'] == 0) echo 'style="display:none;"'; ?>>
			<?php
			foreach($lst_tasks as $todo){
				$todo->setLightAttribute('from', 'desktop');
				$go = $todo->getLightAttribute('gobject');

				$link_to = dims::getInstance()->getScriptEnv().'?dims_mainmenu=0&submenu=2&id='.$go->fields['id_record'].'&type='.$go->fields['id_object'].'&init_filters=1&from=desktop';
				$ig_param = '&concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_INFOS_GENERALES;
				$todo_param = '&concepts_op='.dims_const_desktopv2::DESKTOP_V2_CONCEPTS_TODOS.'#todo_'.$todo->getId();

				switch($go->fields['id_object']){
					case dims_const::_SYSTEM_OBJECT_EVENT :
						$obj = new action();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['libelle'];
						$on_the_record = $_SESSION['cste']['ON_THE_EVENT_RECORD'];
						break;
					case dims_const::_SYSTEM_OBJECT_ACTIVITY :
						require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
						$obj = new dims_activity();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->getLibelle();
						$on_the_record = $_SESSION['cste']['ON_THE_ACTIVITY_RECORD'];
						$link_to = dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/admin.php?submenu=1&mode=activity&action=view&activity_id='.$obj->getid();
						$ig_param = '&tab=general';
						$todo_param = '&tab=todos#todo_'.$todo->getId();
						$ig_param = '&tab=general';
						$todo_param = '&tab=todos#todo_'.$todo->getId();
						break;
					case dims_const::_SYSTEM_OBJECT_OPPORTUNITY :
						require_once DIMS_APP_PATH.'modules/system/opportunity/class_opportunity.php';
						$obj = new dims_opportunity();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['libelle'];
						$on_the_record = $_SESSION['cste']['ON_THE_OPPORTUNITY_RECORD'];
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$obj = new contact();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['firstname'].' '.$obj->fields['lastname'];
						$on_the_record = $_SESSION['cste']['ON_THE_CONTACT_RECORD'];
						$todo_param = $ig_param = '';
						$link_to = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$obj->get('id');
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$obj = new tiers();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['intitule'];
						$on_the_record = $_SESSION['cste']['ON_THE_COMPANY_RECORD'];
						$todo_param = $ig_param = '';
						$link_to = dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$obj->get('id');
						break;
					case dims_const::_SYSTEM_OBJECT_DOCFILE :
						$obj = new docfile();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['name'];
						$on_the_record = $_SESSION['cste']['ON_THE_DOCUMENT_RECORD'];
						break;
					case dims_const::_SYSTEM_OBJECT_CASE :
						$obj = new dims_case();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['label'];
						$on_the_record = $_SESSION['cste']['ON_THE_CASE_RECORD'];
						break;
					case dims_const::_SYSTEM_OBJECT_SUIVI :
						$obj = new suivi();
						$obj->open($go->fields['id_record']);
						$title_object = $obj->fields['libelle'];
						$on_the_record = $_SESSION['cste']['ON_THE_COMMERCIAL_DOCUMENT_RECORD'];
						break;
				}

				if (!isset($title_object)) $title_object='';
				if (!isset($on_the_record)) $on_the_record='';
				$go->setLightAttribute('title_object', $title_object);
				$go->setLightAttribute('on_the_record', $on_the_record);

				$go->setLightAttribute('todo_param', $todo_param);
				$go->setLightAttribute('home_param', $ig_param);//IG pour Infos générales

				//$go->setLightAttribute('additional_object_classes', 'lien_bleu');
				$go->setLightAttribute('link_to', $link_to);

				$todo->setLightAttribute('gobject', $go);//on écrase avec le go mis à jour

				$todo->setLightAttribute('keep_context', '&dims_mainmenu=0&submenu=2&id='.$go->fields['id_record'].'&type='.$go->fields['id_object'].'&init_filters=1&from=desktop');
				$todo->setLightAttribute('redirect_on', dims::getInstance()->getScriptEnv().'?submenu=1&mode=default&force_desktop=1');
				$todo->display(DIMS_APP_PATH.'/include/views/todos/todo.tpl.php');
			}
			?>
		</div>
		<?php
	}
	else{
		?>
		<div class="no_todo"><?= $_SESSION['cste']['NO_TASK_TO_DO']; ?></div>
		<?php
	}
?>
</div>
