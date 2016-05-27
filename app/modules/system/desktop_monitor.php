<?
if (!isset( $_SESSION['dims']['submenumonitor']) || $_SESSION['dims']['submenumonitor']=="" || (!$currentworkspace['contact'] && $_SESSION['dims']['submenumonitor']=="dims_const::_DIMS_SUBMENU_CONTACT"))  {
   if ($currentworkspace['contact']) $_SESSION['dims']['submenumonitor']=dims_const::_DIMS_SUBMENU_TODO;
   else $_SESSION['dims']['submenumonitor']=dims_const::_DIMS_SUBMENU_TODO;
}

$submenumonitor=dims_load_securvalue('submenumonitor',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['submenumonitor'],$_SESSION['dims']['submenumonitor']);
// on switche sur l'element couramment s