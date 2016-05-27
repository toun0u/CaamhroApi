<?php
if (!isset($_SESSION['dims']['desktop_ticket'])) $_SESSION['dims']['desktop_ticket']=dims_const::_DIMS_CSTE_TOVIEW;

$desktop_ticket=dims_load_securvalue('action',dims_const::_DIMS_NUM_INPUT,true,true,false);
if ($desktop_ticket>0) {
	if ($desktop_ticket!=$_SESSION['dims']['desktop_ticket']) unset($_SESSION['dims']['current_ticket']);
	$_SESSION['dims']['desktop_ticket']=$desktop_ticket;
}

// on initialise la variable de desktop collab
//unset($_SESSION['dims']['desktop_ticket']);

// calcul des infos a actualiser
$recentact=(isset($_SESSION['dims']['activities'][$_SESSION['dims']['workspaceid']])) ? $_SESSION['dims']['activities'][$_SESSION['dims']['workspaceid']] : 0;

$nbtoview=0;
$nbtovalidate=0;
$nbwait=0;
$nbfavorite=0;

// on compte le nombre de tickets a voir
require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
$usr=new user();
$usr->open($_SESSION['dims']['userid']);
// liste des users visibles par le user courant
//$lstusers=$usr->getusersgroup();
// liste des espaces de travail rattach?s
$lstworkspace=array_keys($usr->getworkspaces());

for($i=0;$i<4;$i++){
	$params = array();
	$where = '';
	$where1 = '';

	if($i==0){ //Tickets a voir
		$inner = "";
		$where = " AND td.id_user = :userid";
		$params[':userid'] = $_SESSION['dims']['userid'];
	}

	if($i==1){ //Tickets a valider
		$inner = "";
		$where = " AND td.id_user = :userid AND t.id_user <> :userid AND t.needed_validation = 1";
		$params[':userid'] = $_SESSION['dims']['userid'];
	}

	if($i==2){ //Tickets en cours de validation
		$inner = " AND td.id_user <> t.id_user
			   INNER JOIN dims_user du ON du.id = td.id_user";
		$where = " AND t.id_user = :userid AND t.needed_validation = 1
			   AND (t.status < :status )";
		$params[':userid'] = $_SESSION['dims']['userid'];
		$params[':status'] = dims_const::_DIMS_TICKETS_DONE;
	}

	if($i==3){ //Tickets envoy?s
		$inner = " INNER JOIN dims_user du ON du.id = td.id_user AND td.id_user <> t.id_user ";
		$where = " AND t.id_user = :userid
			   AND (t.status = 0)";
		$params[':userid'] = $_SESSION['dims']['userid'];
	}

	$sql = "SELECT			t.id

			FROM		dims_ticket t

			INNER JOIN	dims_ticket_dest td
			ON			td.id_ticket = t.id

			$inner

			LEFT JOIN	dims_ticket_watch tw
			ON			tw.id_ticket = t.id
			AND				tw.id_user = td.id_user

			LEFT JOIN	dims_user u
			ON			u.id = t.id_user

			LEFT JOIN	dims_ticket_status ts
			ON			ts.id_ticket = t.id
			AND			ts.id_user = td.id_user

			WHERE		td.deleted = 0

			$where

			GROUP BY t.id";

	//echo $sql."<br/>";
	$res = $db->query($sql, $params);

	unset($data);
	unset($tickets_nb);
	$tickets_nb=array();
	while ($data = $db->fetchrow($res)) {
		if (!isset($tickets_nb[$data['id']])) $tickets_nb[$data['id']] = $data;
	}

	switch($i) {
			case 1:
				$sql = "SELECT		t.id
					FROM		dims_ticket t
					INNER JOIN	dims_ticket_dest td
						ON		td.id_ticket = t.id
						AND		td.id_user = :userid
					INNER JOIN	dims_ticket_status ts
						ON		ts.id_ticket = t.id
					WHERE			ts.status = :status
						AND		ts.id_user = :userid ";
				//echo $sql;
				$res_diff = $db->query($sql, array(
					':userid'	=> $_SESSION['dims']['userid'],
					':status'	=> dims_const::_DIMS_TICKETS_DONE
				));
				$tickets_diff = array();
				while ($data = $db->fetchrow($res_diff)) {
					if (!isset($tickets_diff[$data['id']])) $tickets_diff[$data['id']] = $data;
				}

				$tickets_nb = array_diff_assoc($tickets_nb,$tickets_diff);
			break;

			/*case 2:
				$sql = "SELECT		t.id
					FROM		dims_ticket t
					INNER JOIN	dims_ticket_status ts
						ON		ts.id_ticket = t.id
					WHERE			ts.status = ".dims_const::_DIMS_TICKETS_DONE."
						AND		t.id_user = {$_SESSION['dims']['userid']}";
				//echo $sql;
				$res_diff = $db->query($sql);
				$tickets_diff = array();
				while ($data = $db->fetchrow($res_diff)) {
					if (!isset($tickets_diff[$data['id']])) $tickets_diff[$data['id']] = $data;
				}

				$tickets_nb = array_diff_assoc($tickets_nb,$tickets_diff);
			break;*/
		};
	//echo "apr?s : ".dims_print_r($tickets_nb)."<br/>";

	$nbtickets = count($tickets_nb);

	if($i==0) //Tickets a voir
		$nbtoview = $nbtickets;
	if($i==1) //Tickets a valider
		$nbtovalidate = $nbtickets;
	if($i==2) //Tickets en cours de validation
		$nbtoconfirm = $nbtickets;
	if($i==3) //Tickets envoy?s
		$nbtosent = $nbtickets;
}

?>
