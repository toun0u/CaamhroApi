<?php
switch($a) {
	case 'load':
		$start = dims_load_securvalue('start', dims_const::_DIMS_NUM_INPUT, true, true);
		$nb = dims_load_securvalue('nb', dims_const::_DIMS_NUM_INPUT, true, true);

		if($nb <= 0){
			$nb = _DASHBOARD_NB_ELEMS_DISPLAY;
		}

		$db = dims::getInstance()->getDb();
		$sql = "SELECT		*
				FROM		".docfile::TABLE_NAME." t
				INNER JOIN	".user::TABLE_NAME." u
				ON			t.id_user = u.id
				INNER JOIN	".contact::TABLE_NAME." c
				ON			c.id = u.id_contact
				WHERE		t.id_module = :idm
				GROUP BY 	t.id
				ORDER BY	t.timestp_create DESC
				LIMIT 		:start, :end";
		$params = array(
			':idm' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['moduleid']),
			':start' => array('type' => PDO::PARAM_INT, 'value' => $start),
			':end' => array('type' => PDO::PARAM_INT, 'value' => $nb),
		);
		$res = $db->query($sql,$params);
		$separation = $db->split_resultset($res);
		$return = array();
		foreach ($separation as $tab) {
			$dd = dims_timestp2local($tab['t']['timestp_create']);
			$docfile = new docfile();
			$docfile->openFromResultSet($tab['t']);
			$path = $docfile->getThumbnail(48);
			if(empty($path)){
				$path = $docfile->getFileIcon(48);
			}
			$return[] = array(
				'id' => $tab['t']['id'],
				'thumbnail' => $path,
				'name' => $tab['t']['name'],
				'lkObject' => "#",
				'lkObjectLk' => 0,
				'user' => ($tab['u']['id']==$_SESSION['dims']['userid'])?'vous-même':$tab['c']['firstname']." ".$tab['c']['lastname'],
				'userLk' => $tab['c']['id'],
				'date' => $dd['date'],
			);
		}
		die(json_encode($return));
		break;
}
