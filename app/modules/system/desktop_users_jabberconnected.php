<?php
// parcours de l'ensemble des personnes connectés visibles sur l'espace
$sql = "select distinct u.*
		from 			dims_user as u
		inner join 		dims_jabber_connecteduser as ju
		on 				u.jabberId=ju.jabberId";

$res=$db->query($sql);

if ($db->numrows($res)>0) {
	while ($fuser=$db->fetchrow($res)) {
		echo "<img src=\"./common/img/user.gif\" alt=\"\"/>".$fuser['firstname']." ".$fuser['lastname']."&nbsp;&nbsp;";
	}
}
else {
	echo "<p style=\"text-align:center;font-weight:bold;\">".$_DIMS['cste']['_DIMS_LABEL_NO_INTERCOM']."</p>";
}
?>
