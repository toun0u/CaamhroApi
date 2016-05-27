<?
$arrayshare=array();
$tab_share=array();
$tab_histo=array();

$sqlshare= "SELECT s.*, u.lastname,u.firstname,count(f.id_doc) as cptefile
		FROM 		dims_mod_sharefile_share as s
		LEFT JOIN 	dims_user as u
		ON 			u.id = s.id_user
		LEFT JOIN	dims_mod_sharefile_file as f
		ON			f.id_share=s.id
		WHERE		s.deleted=0
        AND s.id_user= :userid
        AND s.id_module= :moduleid
		GROUP BY	s.id
        ORDER BY timestp_create DESC";
$res_s = $db->query($sqlshare,array(':userid' => $_SESSION['dims']['userid'], ':moduleid' => $moduleid));
while($value = $db->fetchrow($res_s)) {
    $tab_share[$value['id']] = $value;
    $arrayshare[]=$value['id'];
}

// comptage des users
if (sizeof($arrayshare)>0) {
    $params = array();
    $sql_r = "SELECT 	h.id_share,count( distinct h.id) AS cpte
    		FROM 		dims_mod_sharefile_history AS h
			inner join	dims_mod_sharefile_user AS u
			ON			h.id_share IN (".$db->getParamsFromArray($arrayshare, 'share', $params).")
			AND			h.id_share=u.id_share
			AND			h.id_user>0
			INNER JOIN	dims_user as du
			ON			du.id=u.id_user
			GROUP BY	h.id_share";

    $res_r = $db->query($sql_r, $params);
    $tab_histo =array();
    while($value = $db->fetchrow($res_r)) {
        $tab_histo[$value['id_share']] = $value['cpte'];
    }
}
// contact
if (sizeof($arrayshare)>0) {
    $params = array();
    $sql_r = "SELECT 	h.id_share,count( distinct h.id) AS cpte
    		FROM 		dims_mod_sharefile_history AS h
			INNER JOIN	dims_mod_sharefile_user AS u
			ON			h.id_share IN (".$db->getParamsFromArray($arrayshare, 'share', $params).")
			AND			h.id_share=u.id_share
			AND			h.id_contact>0
			INNER JOIN	dims_mod_business_contact as c
			ON			c.id=u.id_contact
			GROUP BY	h.id_share";


    $res_r = $db->query($sql_r, $params);

    while($value = $db->fetchrow($res_r)) {
		if (isset($tab_histo[$value['id_share']])) $tab_histo[$value['id_share']]+=$value['cpte'];
        else $tab_histo[$value['id_share']] = $value['cpte'];
    }
}

?>
<form>

<table style="font-size:12px;" width="100%" cellpadding="0" cellspacing="2" border="0">
	<tr style="background-color:#EDEDED;font-size:12px;font-weight:bold;"><td>Nom de partage</td><td>Date d'envoi</td><td>Date de fin</td><td>Nb fichiers</td><td>Nb consult.</td><td align="center">Stats</td><td align="center">Voir</td><td align="center">Dupliquer</td><td align="center">Sup.</td></tr>
    <?
    if (!empty($tab_share)) {
		$ind=1;
        foreach($tab_share as $id_share => $share) {
            $nb_histo = 0;
            if (isset($tab_histo[$id_share]['cpte'])) $nb_histo = count($tab_histo[$id_share]['cpte']);

            // construction de la liste
            $datenvoi=dims_timestamp2local($share['timestp_create']);
            $datenvoi=$datenvoi['date'];

            if ($share['timestp_finished']>0) {
                    $datefin=dims_timestamp2local($share['timestp_finished']);
                    $datefin=$datefin['date'];
            }
            else $datefin="-";
            $nbconsult=0;
            if (isset($tab_histo[$share['id']])) $nbconsult=$tab_histo[$share['id']];
			$ind=($ind==1) ? $ind=2 : $ind=1;
            echo "<tr class=\"trl$ind\"><td><a title=\"".$share['description']."\" href=\"".$dims->getScriptEnv()."?op=view_share&id_share=".$id_share."\">".$share['label']."</a></td><td>".$datenvoi."</td><td>".$datefin."</td><td>".$share['cptefile']."</td><td>".$nbconsult."</td>";

            echo "<td align=\"center\">
            <a href=\"".$dims->getScriptEnv()."?op=sharefile_stats&id_share=".$id_share."\" alt=\"Statistiques\"><img title=\"Statistiques\" src=\"./common/img/icon_finance.gif\" style=\"border:0px;\"></a>
            </td><td align=\"center\"><a href=\"".$dims->getScriptEnv()."?op=view_share&id_share=".$id_share."\" alt=\"Voir les fichiers\"><img title=\"Voir les fichiers\" src=\"./common/img/view.png\" style=\"border:0px;\"></a>
			</td><td align=\"center\"><a href=\"".$dims->getScriptEnv()."?op=duplicate_share&id_share=".$id_share."\" alt=\"Dupliquer l'envoi\"><img title=\"Dupliquer l'envoi\" src=\"./common/img/public.png\" style=\"border:0px;\"></a>";
            //echo "</td><td align=\"center\"><a href=\"".$dims->getScriptEnv()."?op=delete_share&id_share=".$id_share."\" alt=\"Supprimer le partage\"><img title=\"Supprimer le partage\" src=\"./common/img/del.png\" style=\"border:0px;\"></a></td></tr>";
			$link_del=$dims->getScriptEnv()."?op=delete_sharefile&id_share=".$id_share;
			?>
			</td><td align="center"><a href="javascript:dims_confirmlink('<? echo $link_del; ?>','Souhaitez vous supprimer le partage \'<? echo addslashes($share['label']); ?>\' ?');"><img title="Supprimer" style="border:0;" src="./common/img/del.png"></a></td></tr>
			<?
        }
    }
    else {
            echo "<tr><td colspan=\"6\" style=\"text-align:center\"><br>Pas d'envoi en cours</td></tr>";
    }
    ?>
</table>
</form>
