<div id="contentprojectsuite" style="float: left; width: 100%;">
<script type="text/javascript" language="javascript" src="./include/portal.js"></script>
<div id='dims_popup' style="display: none; position: fixed;"></div>
		<?php
		// verification de l'onglet selectionne
		// on initialise la variable de desktop user
		if (!isset($_SESSION['dims']['desktop_project_suite'])) $_SESSION['dims']['desktop_project_suite']=dims_const::_DIMS_CSTE_PROPERTIES;
		$desktop_project_suite=dims_load_securvalue('desktop_project_suite',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_project_suite'],$_SESSION['dims']['desktop_project_suite']);

		$tabs=array();
		$tabs[dims_const::_DIMS_CSTE_PROPERTIES]['title'] = $_DIMS['cste']['_DIMS_PROPERTIES'];
		$tabs[dims_const::_DIMS_CSTE_PROPERTIES]['url'] = $scriptenv.'?desktop_project_suite='.dims_const::_DIMS_CSTE_PROPERTIES;
		$tabs[dims_const::_DIMS_CSTE_PROPERTIES]['icon'] = "./common/img/project.png";
		$tabs[dims_const::_DIMS_CSTE_PROPERTIES]['width'] = 120;
		$tabs[dims_const::_DIMS_CSTE_PROPERTIES]['position'] = 'left';

		$tabs[dims_const::_DIMS_CSTE_PHASE]['title'] = $_DIMS['cste']['_DIMS_LABEL_PHASE'];
		$tabs[dims_const::_DIMS_CSTE_PHASE]['url'] = $scriptenv.'?desktop_project_suite='.dims_const::_DIMS_CSTE_PHASE.'&idtask=-1';
		$tabs[dims_const::_DIMS_CSTE_PHASE]['icon'] = "./common/modules/system/img/projects.png";
		$tabs[dims_const::_DIMS_CSTE_PHASE]['width'] = 120;
		$tabs[dims_const::_DIMS_CSTE_PHASE]['position'] = 'left';

		$tabs[dims_const::_DIMS_CSTE_TASK]['title'] = $_DIMS['cste']['_DIMS_TASKS'];
		$tabs[dims_const::_DIMS_CSTE_TASK]['url'] = $scriptenv.'?desktop_project_suite='.dims_const::_DIMS_CSTE_TASK.'&idtask=-1';
		$tabs[dims_const::_DIMS_CSTE_TASK]['icon'] = "./common/modules/system/img/tasks.png";
		$tabs[dims_const::_DIMS_CSTE_TASK]['width'] = 120;
		$tabs[dims_const::_DIMS_CSTE_TASK]['position'] = 'left';

		//$tabs[dims_const::_DIMS_CSTE_GANTT]['title'] = $_DIMS['cste']['_DIMS_GANTT'];
		//$tabs[dims_const::_DIMS_CSTE_GANTT]['url'] = "admin.php?desktop_project_suite=".dims_const::_DIMS_CSTE_GANTT;
		//$tabs[dims_const::_DIMS_CSTE_GANTT]['icon'] = "./common/img/gantt.png";
		//$tabs[dims_const::_DIMS_CSTE_GANTT]['width'] = 120;
		//$tabs[dims_const::_DIMS_CSTE_GANTT]['position'] = 'left';

		$tabs[dims_const::_DIMS_CSTE_DOC]['title'] = $_DIMS['cste']['_DOCS'];
		$tabs[dims_const::_DIMS_CSTE_DOC]['url'] = $scriptenv.'?desktop_project_suite='.dims_const::_DIMS_CSTE_DOC;
		$tabs[dims_const::_DIMS_CSTE_DOC]['icon'] = "./common/modules/doc/img/mod16.png";
		$tabs[dims_const::_DIMS_CSTE_DOC]['width'] = 190;
		$tabs[dims_const::_DIMS_CSTE_DOC]['position'] = 'left';

		$tabs[dims_const::_DIMS_CSTE_ANNOT]['title'] = $_DIMS['cste']['_DIMS_COMMENTS'];
		$tabs[dims_const::_DIMS_CSTE_ANNOT]['url'] = $scriptenv.'?desktop_project_suite='.dims_const::_DIMS_CSTE_ANNOT;
		$tabs[dims_const::_DIMS_CSTE_ANNOT]['icon'] = "./common/img/annot.gif";
		$tabs[dims_const::_DIMS_CSTE_ANNOT]['width'] = 140;
		$tabs[dims_const::_DIMS_CSTE_ANNOT]['position'] = 'left';

		echo $skin->create_onglet($tabs,$desktop_project_suite,true,'0',"onglet");

		//echo "<li class=\"module\"><a href=\"#\" onclick=\"javascript:refreshDesktop('projectsuite',".dims_const::_DIMS_CSTE_ADDTASK.",'project_task_add','contentdesktopprojectsuite')\"><img src=\"./common/img/add.gif\" alt=\"".$_DIMS['cste']['_DIMS_ADD']."\">&nbsp;".$_DIMS['cste']['_DIMS_ADDTASK']."</a></li>";
		?>
	<div class="contentdesktop" id="contentdesktopprojectsuite">
	<?php
		switch($_SESSION['dims']['desktop_project_suite']) {
			case dims_const::_DIMS_CSTE_PROPERTIES:
				require_once(DIMS_APP_PATH . '/modules/system/cms_projects_detail.php');
				break;
			case dims_const::_DIMS_CSTE_TASK:
				if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0) {
					require_once DIMS_APP_PATH . '/modules/system/class_task.php';
					$task = new task();
					$task->open($_SESSION['dims']['currenttask']);
					require_once(DIMS_APP_PATH . '/modules/system/cms_projects_task.php');
				}
				else
					require_once(DIMS_APP_PATH . '/modules/system/cms_projects_list_task.php');
				break;
			case dims_const::_DIMS_CSTE_DOC:
				require_once(DIMS_APP_PATH . '/modules/system/cms_projects_doc.php');
				break;
			//case dims_const::_DIMS_CSTE_USERAFFECT:
			//	require_once(DIMS_APP_PATH . '/modules/system/desktop_project_affectation.php');
			//	break;

			case dims_const::_DIMS_CSTE_PHASE:
				require_once(DIMS_APP_PATH . '/modules/system/cms_projects_list_phase.php');
				break;
			case dims_const::_DIMS_CSTE_ANNOT:
				require_once(DIMS_APP_PATH . '/modules/system/cms_projects_annot.php');
				break;
			//case dims_const::_DIMS_CSTE_MILESTONE:
			//	require_once(DIMS_APP_PATH . '/modules/system/desktop_project_milestone.php');
			//	break;
			//case dims_const::_DIMS_CSTE_GANTT:
			//	$op="project_view_gantt";
			//	include DIMS_APP_PATH . '/modules/system/public_projects.php';
			//	break;
			//case dims_const::_DIMS_CSTE_ADDTASK:
			//	ob_end_clean();
			//	require_once DIMS_APP_PATH . '/modules/system/public_projects.php';
			//	die();
			//	break;
		}
	?>
	</div>
	<div class="back" style="clear: both; margin-left: 5px; font-size: 14px;">
		<a href="<?php echo $scriptenv.'?idproject=0'; ?>">< <?php echo $_DIMS['cste']['_PROJECT_LABEL_BACK']; ?></a>
	</div>
</div>
