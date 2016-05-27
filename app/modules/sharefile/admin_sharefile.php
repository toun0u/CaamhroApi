<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$sqlshare= "SELECT  u.lastname,u.firstname,s.id_user,
					count( distinct s.id) as nbshare,
					sum(s.nbdoc) as nbfile,
					sum(s.docsize) as poids,
					sum(s.deleted) as deleted
		FROM 		dims_mod_sharefile_share as s
		INNER JOIN 	dims_user as u
		ON 			u.id = s.id_user
		WHERE		s.id_module= :idmodule
		GROUP BY	s.id_user";


$res_s = $db->query($sqlshare, array(':idmodule' => $moduleid) );
while($value = $db->fetchrow($res_s)) {
    $tab_share[] = $value;
    $arrayshare[]=$value['id_user'];
}

$total=0;
?>

<form>

<table style="font-size:12px;" width="100%" cellpadding="0" cellspacing="2" border="0">
	<tr style="background-color:#EDEDED;font-size:14px;font-weight:bold;"><td>Personne</td><td>Nombre de partage</td><td>Nombre de fichiers</td><td>Espace disque</td><td align="center">Nb supprim&eacute;</td><td align="center">Voir</td></tr>
    <?
    if (!empty($tab_share)) {
		$ind=1;
        foreach($tab_share as $id_share => $share) {

			$ind=($ind==1) ? $ind=2 : $ind=1;
			$link=$dims->getScriptEnv()."?op=view_adminshare&id_user=".$share['id_user'];
            echo "<tr class=\"trl$ind\"><td><a title=\"".$share['firstname']." ".$share['lastname']."\" href=\"".$link."\">".$share['firstname']." ".$share['lastname']."</a></td><td>".$share['nbshare']."</td><td>".$share['nbfile']."</td><td>".sprintf("%.02f",$share['poids']/1048576)." Mo</td>";
		echo "<td>".$share['deleted']."</td>";
            echo "<td align=\"center\"><a href=\"".$link."\" alt=\"Voir\"><img title=\"Voir\" src=\"./common/img/view.png\" style=\"border:0px;\"></a>";
			$total+=$share['poids'];
        }
    }
    else {
            echo "<tr><td colspan=\"6\" style=\"text-align:center\"><br>Pas d'envoi en cours</td></tr>";
    }
    ?>
</table>
</form>


<span style="text-align:center;width:100%;font-weight:bold;display:block;float:left;margin-top:30px;margin-bottom:30px;font-size:14px;">
<?
echo "Espace total utilis&eacute; pour les partages : ".sprintf("%.02f",$total/1048576)." Mo";
?>
</span>
