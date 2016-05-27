<?
require_once DIMS_APP_PATH.'modules/system/class_workspace_user_role.php';

class group_user extends dims_data_object {
    const TABLE_NAME = "dims_group_user";
    /**
    * Class constructor
    *
    * @param int $idconnexion
    * @access public
    **/
    function __construct() {
	    parent::dims_data_object(self::TABLE_NAME,'id_group','id_user');
    }

    public static function getGroupeOfUser($id_user){
	$list_id_user = array();

	$db = dims::getInstance()->getDb();

	$sql = "SELECT id_group WHERE id_user = :iduser";

	$res = $db->query($sql, array(
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
	));

	while($row = $db->fetchrow($res)) {
	    $list_id_user[] = $row['id_group'];
	}

	return $list_id_user;

    }

    public static function getIdUsersForListGroups(array $list_groups) {
	$list_id_user = array();
	if(!empty ($list_groups)){
	    $db = dims::getInstance()->getDb();
	    $sql = "SELECT id_user FROM ".self::TABLE_NAME."
		WHERE id_group IN (".  $db->getParamsFromArray($list_groups, 'idgroup', $params).")";

	    $res = $db->query($sql, $params);
	    while ($row = $db->fetchrow($res)) {
		$list_id_user[] = $row['id_user'];
	    }
	}
	return $list_id_user;
    }

    public static function getIdGroupsForUserId($id_user) {
	$list_id_group = array();

	if($id_user > 0){
	    $db = dims::getInstance()->getDb();
	    $sql = "SELECT id_group FROM ".self::TABLE_NAME."
		WHERE id_user = :iduser";

	    $res = $db->query($sql, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));
	    while ($row = $db->fetchrow($res)) {
		$list_id_group[] = $row['id_group'];
	    }
	}

	return $list_id_group ;
    }
}

?>
