<?
ini_set('max_execution_time',-1);

dims_init_module('sharefile');
include_once("./common/modules/sharefile/include/classes/class_sharefile_file.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_history.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_share.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_user.php");
include_once("./common/modules/sharefile/include/classes/class_sharefile_contact.php");
include_once('./common/modules/sharefile/include/classes/class_sharefile_param.php');
include_once("./common/modules/doc/include/global.php");
include_once("./common/modules/doc/class_docfile.php");

$select = 	"
			SELECT 		*
			FROM 		dims_mod_sharefile_share where timestp_finished < ".date("Ymd")."000000";

$result = $db->query($select);
if ($db->numrows($result)>0) {

	while ($fields = $db->fetchrow($result)) {
		$share = new sharefile_share();
        $share->open($fields['id']);
		$share->delete();
	}
}
?>
