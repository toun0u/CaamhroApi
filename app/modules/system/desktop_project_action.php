<div class="contentdesktop" id="contentprojectsuite">
	<div class="subheader">
		<?
		// verification de l'onglet selectionne
		// on initialise la variable de desktop user
		if (!isset($_SESSION['dims']['desktop_project_suite'])) $_SESSION['dims']['desktop_project_suite']=dims_const::_DIMS_CSTE_TASK;

		$desktop_project_suite=array();
		$desktop_project_suite[]=array('TITLE' => $_DIMS['cste']['_DIMS_TASK'],'ICON' => 'checkdo0.png','URL' => "refreshDesktop('projectsuite',".dims_const::_DIMS_CSTE_TASK.",'','contentprojectsuite')",'SELECTED' => ($_SESSION['dims']['desktop_project_suite'] == dims_const::_DIMS_CSTE_TASK) ? 'selected' : '');
		$desktop_project_suite[]=array('TITLE' => $_DIMS['cste']['_DIMS_MILESTONE'],'ICON' => '','URL' => "refreshDesktop('projectsuite',".dims_const::_DIMS_CSTE_MILESTONE.",'','contentprojectsuite')",'SELECTED' => ($_SESSION['dims']['desktop_project_suite'] == dims_const::_DIMS_CSTE_MILESTONE) ? 'selected' : '');
		$desktop_project_suite[]=array('TITLE' => $_DIMS['cste']['_DIMS_GANTT'],'ICON' => 'gantt.gif','URL' => "refreshDesktop('projectsuite',".dims_const::_DIMS_CSTE_GANTT.",'','contentprojectsuite')",'SELECTED' => ($_SESSION['dims']['desktop_project_suite'] == dims_const::_DIMS_CSTE_GANTT) ? 'selected' : '');
		$desktop_project_suite[]=array('TITLE' => $_DIMS['cste']['_DIMS_COMMENTS'],'ICON' => 'annot.gif','URL' => "refreshDesktop('projectsuite',".dims_const::_DIMS_CSTE_ANNOT.",'','contentprojectsuite')",'SELECTED' => ($_SESSION['dims']['desktop_project_suite'] == dims_const::_DIMS_CSTE_GANTT) ? 'selected' : '');
		?>
		<ul>
		<?
		foreach ($desktop_project_suite as $elemproject) {
			echo "<li class=\"module".$elemproject['SELECTED']."\"><a href=\"#\" onclick=\"javascript:".$elemproject['URL']."\"><img src=\"./common/img/".$elemproject['ICON']."\" alt=\"\">&nbsp;".$elemproject['TITLE']."</a></li>";
		}

		echo "<li class=\"module\"><a href=\"#\" onclick=\"javascript:refreshDesktop('projectsuite',".dims_const::_DIMS_CSTE_ADDTASK.",'project_task_add','contentdesktopprojectsuite')\"><img src=\"./common/img/add.gif\" alt=\"".$_DIMS['cste']['_DIMS_ADD']."\">&nbsp;".$_DIMS['cste']['_DIMS_ADDTASK']."</a></li>";
		?>
		</ul>
	</div>
	<div class="contentdesktop" id="contentdesktopprojectsuite">
	<?
		switch($_SESSION['dims']['desktop_project_suite']) {
			case dims_const::_DIMS_CSTE_TASK:
				if (isset($_SESSION['dims']['currenttask']) && $_SESSION['dims']['currenttask']>0)
					require_once(DIMS_APP_PATH . '/modules/system/desktop_project_task.php');
				else
					require_once(DIMS_APP_PATH . '/modules/system/desktop_project_list_task.php');
				break;
			case dims_const::_DIMS_CSTE_ACTION:
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_action.php');
				break;
			case dims_const::_DIMS_CSTE_MILESTONE:
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_milestone.php');
				break;
			case dims_const::_DIMS_CSTE_GANTT:
				require_once(DIMS_APP_PATH . '/modules/system/desktop_project_gantt.php');
				break;
			case dims_const::_DIMS_CSTE_ADDTASK:
				ob_end_clean();
				require_once(DIMS_APP_PATH . '/modules/system/public_projects.php');
				die();
				break;
		}
	?>
	</div>
	<div class="contentdesktop"
</div>
