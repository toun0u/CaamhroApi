<?
ini_set('max_execution_time',-1);

dims_init_module('sharefile');
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_file.php");
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_history.php");
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_share.php");
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_user.php");
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_contact.php");
require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_param.php');
require_once(DIMS_APP_PATH . "/modules/doc/include/global.php");
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");

$select =	"
			SELECT		*
			FROM		dims_mod_sharefile_share where timestp_finished < ".date("Ymd")."000000";

$result = $db->query($select);
if ($db->numrows($result)>0) {

	while ($fields = $db->fetchrow($result)) {
		$share = new sharefile_share();
		$share->open($fields['id']);
		$share->delete();
	}
}
?>
