<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$sqlshare= "SELECT	*
			FROM 		dims_mod_sharefile_contact
			WHERE 		id_user= :iduser
			ORDER BY lastname,firstname";

$res = $db->query($sqlshare, array(':iduser' => $_SESSION['dims']['userid']));

echo "<table width=\"100%\">";
echo "<tr><td colspan=\"4\" align=\"right\"><img title=\"".$_DIMS['cste']['_DIRECTORY_ADDNEWCONTACT']."\" src=\"./common/img/add.gif\">&nbsp;<a href=\"".dims_urlencode($dims->getScriptEnv()."?op=sharefile_new_contact")."\">".$_DIMS['cste']['_DIRECTORY_ADDNEWCONTACT']."</a></td></tr>";
echo "<tr style=\"background-color:#EDEDED;\"><td>Nom</td><td>Pr&eacute;nom</td><td>Email</td><td>Actions</td></tr>";
$c=0;
if ($db->numrows($res)>0) {

	while($value = $db->fetchrow($res)) {
		$c++;
	    if ($c%2==0) $class="class=\"trl1\"";
	    else $class="class=\"trl2\"";

		echo "<tr ".$class."><td>".$value['lastname']."</td><td>".$value['firstname']."</td><td>".$value['email']."</td>";

		// modif / sup
		echo "<td><a alt=\"Modifier\" href=\"".$dims->getScriptEnv()."?op=sharefile_modify_contact&id_ct=".$value['id']."\"><img title=\"Modifier\" src=\"./common/img/edit.gif\" style=\"border:0px;\"></a>&nbsp;/&nbsp;";
        echo "<a alt=\"Supprimer\" href=\"".$dims->getScriptEnv()."?op=sharefile_delete_contact&id_ct=".$value['id']."\"><img title=\"Supprimer\" src=\"./common/img/del.png\" style=\"border:0px;\"></a></td></tr>";
	}
}
echo "</table>";

//echo "<p style=\"margin-top:20px;width:100%;text-align:center;\"><a href=\"".dims_urlencode($dims->getScriptEnv()."?op=lst_share")."\">".$_DIMS['cste']['_DIMS_RETURN']."</a></p>";
?>
