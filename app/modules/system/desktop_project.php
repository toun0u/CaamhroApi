<!--<div class="subheader">-->
<div id="content_onglet">
	<div id="menu_content_onglet">
	<?php
	// verification de l'onglet selectionne
	// on initialise la variable de desktop user
	if(!isset($_SESSION['dims']['desktop_project'])) $_SESSION['dims']['desktop_project']=dims_const::_DIMS_CSTE_TASK;
		//echo dims_const::_DIMS_CSTE_TASK."  ".dims_const::_DIMS_CSTE_CURRENTPROJECT;
	$desktop_project=array();

	$desktop_project[]=array('title' => $_DIMS['cste']['_DIMS_TASKS'],'url' => "refreshDesktop('project',".dims_const::_DIMS_CSTE_TASK.",'','content_onglet')",'SELECTED' => ($_SESSION['dims']['desktop_project'] == dims_const::_DIMS_CSTE_TASK) ? '1' : '', 'position' => 'left', 'width' => '', 'function' => '1');
	$desktop_project[]=array('title' => $_DIMS['cste']['_LABEL_PROJECTS'],'url' => "refreshDesktop('project',".dims_const::_DIMS_CSTE_CURRENTPROJECT.",'','content_onglet')",'SELECTED' => ($_SESSION['dims']['desktop_project'] == dims_const::_DIMS_CSTE_CURRENTPROJECT) ? '1' : '', 'position' => 'left', 'width' => '', 'function' => '1');
	$desktop_project[]=array('title' => $_DIMS['cste']['_DIMS_ADDPROJECT'],'url' => "$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&op=project_add",'SELECTED' => '0', 'position' => 'left', 'width' => '150');
	//$desktop_project[]=array('TITLE' => $_DIMS['cste']['_DIMS_MILESTONE'],'URL' => "refreshDesktop('project',".dims_const::_DIMS_CSTE_MILESTONE.",'','desktopproject')",'SELECTED' => ($_SESSION['dims']['desktop_project'] == dims_const::_DIMS_CSTE_MILESTONE) ? 'selected' : '');
	//echo $skin->create_onglet($desktop_project,$desktop_project[$_SESSION['dims']['desktop_project']],1,'0',"onglet");
	?>
	<!--<ul>-->
	<?php

	/*foreach ($desktop_project as $elemproject) {
		echo "<li class=\"module".$elemproject['SELECTED']."\"><a href=\"#\" onclick=\"javascript:".$elemproject['URL']."\">".$elemproject['TITLE']."</a></li>";
	}
	echo "<li  class=\"module\"><a href=\"$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&op=project_add\">".$_DIMS['cste']['_DIMS_ADDPROJECT']."</a></li>";
	*/?>
	<!--</ul>-->
<!--</div>-->
	</div>
	<table cellpadding="10" cellspacing="0" style="width:100%;background:#FCFCFC">
		<tr>
			<td>
				<div class="contentdesktop" id="contentdesktopproject" style="height:160px;overflow:auto;color:#cccccc;">
				<?php
					switch($_SESSION['dims']['desktop_project']) {
						case dims_const::_DIMS_CSTE_CURRENTPROJECT:
							unset($_SESSION['dims']['currentproject']);
							require_once(DIMS_APP_PATH . '/modules/system/desktop_project_list.php');
							break;
						case dims_const::_DIMS_CSTE_TASK:
							require_once(DIMS_APP_PATH . '/modules/system/desktop_task_list.php');
							break;
					}
				?>
				</div>
			</td>
		</tr>
	</table>
</div>
