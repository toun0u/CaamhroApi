<?
require_once(DIMS_APP_PATH . '/modules/system/class_module_type.php');

$uninstallidmoduletype = dims_load_securvalue('uninstallidmoduletype', dims_const::_DIMS_NUM_INPUT, true, true);

$module_type = new module_type();
$module_type->open($uninstallidmoduletype);

dims_create_user_action_log(_SYSTEM_ACTION_UNINSTALLMODULE, $module_type->fields['label']);

echo $skin->open_simplebloc(str_replace('<LABEL>',$module_type->fields['label'],$_DIMS['cste']['_DIMS_LABEL_MODULEUNINSTALL']),'100%');
echo "<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\"><TR><TD>";

if (file_exists(DIMS_APP_PATH . "/modules/{$module_type->fields['label']}/include/admin_uninstall.php")) include(DIMS_APP_PATH . "/modules/{$module_type->fields['label']}/include/admin_uninstall.php");

// DELETE FILES
$filestodelete = DIMS_APP_PATH . "/modules/".$module_type->fields['label'];
if (file_exists($filestodelete)) dims_deletedir($filestodelete);

// DELETE TABLES
$select = "SELECT * FROM dims_mb_object WHERE id_module_type = :idmoduletype";
$rs = $db->query($select, array(':idmoduletype' => $uninstallidmoduletype) );
while ($fields = $db->fetchrow($rs))
{
	echo $fields['name'];
	if ($fields['name']!='')
		$res=$db->query("DROP TABLE IF EXISTS `{$fields['name']}`");
}

// DELETE MODULE TYPE, MODULES, ACTIONS, etc...
$module_type->delete();

?>
