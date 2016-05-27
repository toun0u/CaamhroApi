<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($_SESSION['dims']['projectmenu']) || $_SESSION['dims']['projectmenu']=="")  {
	$_SESSION['dims']['projectmenu']=dims_const::_DIMS_PROJECTMENU_RESUME;
}

if (!empty($_SESSION['dims']['currentproject'])) $_SESSION['dims']['projectmenu']=dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT;

$op=dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);
if (isset($_GET['projectmenu']) && $_GET['projectmenu']==dims_const::_DIMS_PROJECTMENU_RESUME) unset($_SESSION['dims']['currentproject']);
$projectmenu=dims_load_securvalue('projectmenu',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['projectmenu'],$_SESSION['dims']['projectmenu']);

$tabs=array();

$tabs[dims_const::_DIMS_PROJECTMENU_PROJECT]['title'] = $_DIMS['cste']['_LABEL_PROJECTS'];
$tabs[dims_const::_DIMS_PROJECTMENU_PROJECT]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&projectmenu=".dims_const::_DIMS_PROJECTMENU_PROJECT;
$tabs[dims_const::_DIMS_PROJECTMENU_PROJECT]['icon'] = "./common/img/projects.png";
$tabs[dims_const::_DIMS_PROJECTMENU_PROJECT]['width'] = 150;
$tabs[dims_const::_DIMS_PROJECTMENU_PROJECT]['position'] = 'left';

//$tabs[dims_const::_DIMS_PROJECTMENU_RESUME]['title'] = $_DIMS['cste']['_DIMS_LABEL_ACTIVITY'];
//$tabs[dims_const::_DIMS_PROJECTMENU_RESUME]['url'] = "admin.php?projectmenu=".dims_const::_DIMS_PROJECTMENU_RESUME;
//$tabs[dims_const::_DIMS_PROJECTMENU_RESUME]['icon'] = "./common/img/activity.png";
//$tabs[dims_const::_DIMS_PROJECTMENU_RESUME]['width'] = 110;
//$tabs[dims_const::_DIMS_PROJECTMENU_RESUME]['position'] = 'left';

$tabs[dims_const::_DIMS_PROJECTMENU_TASK]['title'] = $_DIMS['cste']['_FORM_TASK_TIME_TODO'];
$tabs[dims_const::_DIMS_PROJECTMENU_TASK]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&projectmenu=".dims_const::_DIMS_PROJECTMENU_TASK;
$tabs[dims_const::_DIMS_PROJECTMENU_TASK]['icon'] = "./common/img/publish.png";
$tabs[dims_const::_DIMS_PROJECTMENU_TASK]['width'] = 170;
$tabs[dims_const::_DIMS_PROJECTMENU_TASK]['position'] = 'left';

if (!empty($_SESSION['dims']['currentproject'])) {
	// construction de l'onglet du projet courant
	$tabs[dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT]['title'] = $_DIMS['cste']['_PROPERTIES_PROJECT'];
	$tabs[dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&projectmenu=".dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT;
	$tabs[dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT]['icon'] = "./common/img/project.png";
	$tabs[dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT]['width'] = 170;
	$tabs[dims_const::_DIMS_PROJECTMENU_CURRENTPROJECT]['position'] = 'left';
}

$tabs[dims_const::_DIMS_PROJECTMENU_ADD_PROJECT]['title'] = $_DIMS['cste']['_DIMS_ADDPROJECT'];
$tabs[dims_const::_DIMS_PROJECTMENU_ADD_PROJECT]['url'] = "admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&projectmenu=".dims_const::_DIMS_PROJECTMENU_ADD_PROJECT;
$tabs[dims_const::_DIMS_PROJECTMENU_ADD_PROJECT]['icon'] = "./common/img/add.gif";
$tabs[dims_const::_DIMS_PROJECTMENU_ADD_PROJECT]['width'] = 170;
$tabs[dims_const::_DIMS_PROJECTMENU_ADD_PROJECT]['position'] = 'left';

echo "<div id=\"content_onglet\" style=\"border-bottom:0px;\"><div id=\"menu_content_onglet\">";
//echo $skin->create_menu($tabs,$projectmenu,true,'0',"onglet");
//echo $skin->create_toolbar($tabs,$projectmenu);
echo $skin->create_toolbar($tabs,$projectmenu);
echo $skin->close_toolbar();
//echo $skin->create_onglet($tabs, $projectmenu, true,0, "","","-1");
//echo $skin->close_toolbar();
echo "</div>";
?>
