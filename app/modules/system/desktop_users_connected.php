<?php
// parcours de l'ensemble des personnes connectés visibles sur l'espace

$res=$db->query("SELECT distinct u.*
				FROM dims_user as u
				INNER JOIN dims_connecteduser as cu
				ON u.id=cu.user_id
				AND cu.workspace_id= :workspaceid ", array(
		':workspaceid' => $_SESSION['dims']['workspaceid']
));

if ($db->numrows($res)>0) {
	while ($fuser=$db->fetchrow($res)) {
		echo "<img src=\"./common/img/user.gif\" alt=\"\"/>".$fuser['firstname']." ".$fuser['lastname']."&nbsp;&nbsp;";
	}
}

?>
