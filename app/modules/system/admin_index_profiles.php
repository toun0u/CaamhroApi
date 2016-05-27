<?
require_once(DIMS_APP_PATH . '/modules/system/class_profile.php');

$system_profiletabid = dims_load_securvalue('system_profiletabid', dims_const::_DIMS_CHAR_INPUT, true, true);

if (isset($system_profiletabid)) $_SESSION['system_profiletabid'] = $system_profiletabid;

if (!isset($_SESSION['system_profiletabid'])) $_SESSION['system_profiletabid'] = '';

$tabs[_SYSTEM_TAB_PROFILEMANAGEMENT]['title'] = $_DIMS['cste']['_SYSTEM_LABELTAB_PROFILEMANAGEMENT'];
$tabs[_SYSTEM_TAB_PROFILEMANAGEMENT]['url'] = "$scriptenv?system_profiletabid="._SYSTEM_TAB_PROFILEMANAGEMENT;
$tabs[_SYSTEM_TAB_PROFILEMANAGEMENT]['width'] = 120;

$tabs[_SYSTEM_TAB_PROFILEADD]['title'] = $_DIMS['cste']['_SYSTEM_LABELTAB_PROFILEADD'];
$tabs[_SYSTEM_TAB_PROFILEADD]['url'] = "$scriptenv?system_profiletabid="._SYSTEM_TAB_PROFILEADD;
$tabs[_SYSTEM_TAB_PROFILEADD]['width'] = 120;

echo $skin->create_tabs('',$tabs,$_SESSION['system_profiletabid']);

switch($_SESSION['system_profiletabid'])
{
	case _SYSTEM_TAB_PROFILEADD:
	switch($op)
	{
		case 'save_profile':
			$profile = new profile();
			$profile_id=dims_load_securvalue('profile_id',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$selectroles=dims_load_securvalue('select_roles',dims_const::_DIMS_NUM_INPUT,false,true,true);
			if ($profile_id>0) $profile->open($profile_id);

			$profile->setvalues($_POST,'profile_');
			$select_roles=$selectroles;

			$profile->save($select_roles);

			dims_redirect("$scriptenv?system_profiletabid="._SYSTEM_TAB_PROFILEMANAGEMENT);
		break;

		default:
			$profile = new profile();
			$profile->init_description();
			include(DIMS_APP_PATH . '/modules/system/admin_index_profiles_add.php');
		break;
	}
	break;

	case _SYSTEM_TAB_PROFILEMANAGEMENT:
	default:
	switch($op) {
		case 'switch_defaultprofile':
			$profile = new profile();
			$profile_id=dims_load_securvalue('profile_id',dims_const::_DIMS_NUM_INPUT,true,false,true);
			if ($profile_id>0) {
				$profile->open($profile_id);
				$profile->fields['def'] = ($profile->fields['def']+1)%2;
				$profile->updateDefault();
				$profile->save($profile->getroles());
			}
			dims_redirect("$scriptenv?system_profiletabid="._SYSTEM_TAB_PROFILEMANAGEMENT);
			break;
		case 'switch_sharedprofile':
			$profile = new profile();
			$profile_id=dims_load_securvalue('profile_id',dims_const::_DIMS_NUM_INPUT,true,false,true);
			if ($profile_id>0) {
				$profile->open($profile_id);
				$profile->fields['shared'] = ($profile->fields['shared']+1)%2;
				$profile->save($profile->getroles());
			}
			dims_redirect("$scriptenv?system_profiletabid="._SYSTEM_TAB_PROFILEMANAGEMENT);
			break;

		case 'save_profile':
			$profile = new profile();
			$profile_id=dims_load_securvalue('profile_id',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$selectroles=dims_load_securvalue('select_roles',dims_const::_DIMS_NUM_INPUT,false,true,true);
			if ($profile_id>0) $profile->open($profile_id);

			$profile->setvalues($_POST,'profile_');

			$select_roles= $selectroles;

			$profile->save($select_roles);

			dims_redirect("$scriptenv?system_profiletabid="._SYSTEM_TAB_PROFILEMANAGEMENT);
		break;

		case 'modify_profile':
			$profile = new profile();
			$profileid=dims_load_securvalue('profileid',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($profileid>0) {
				$profile->open($profileid);
				include(DIMS_APP_PATH . '/modules/system/admin_index_profiles_add.php');
			}
		break;
		case 'delete_profile':
			$profileid=dims_load_securvalue('profileid',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($profileid>0) {
				$profile = new profile();
				$profile->open($profileid);
				$profile->delete();
			}
			dims_redirect("$scriptenv");
		break;

		default:
			include(DIMS_APP_PATH . '/modules/system/admin_index_profiles_management.php');
		break;

	}
	break;
}
?>
