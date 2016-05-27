<?
$array_users=array();
$array_contacts=array();
$tab_histo=array();

$sqlshare= "SELECT  distinct u.id,u.lastname,u.firstname,u.email, fu.view, fu.id as fuid
		FROM 		dims_mod_sharefile_user as fu
		LEFT JOIN 	dims_user as u
		ON 		u.id = fu.id_user
		WHERE		fu.id_user>0 and fu.id_share= :idshare ";

$res_s = $db->query($sqlshare, array(':idshare' => $_SESSION['currentshare']['id_share'] ));
while($value = $db->fetchrow($res_s)) {
    $arrayusers[]=$value;
}

$sqlshare= "SELECT  distinct c.id,c.lastname,c.firstname,c.email, fu.view, fu.id as fuid
		FROM 		dims_mod_sharefile_user as fu
		INNER JOIN 	dims_mod_sharefile_contact as c
		ON 		c.id = fu.id_contact
		AND		fu.id_share= :idshare ";

$res_s = $db->query($sqlshare, array(':idshare' => $_SESSION['currentshare']['id_share'] ));
while($value = $db->fetchrow($res_s)) {
    $arraycontacts[]=$value;
}

// compte des users
$sql_r = "SELECT    h.id_user,h.timestp_create
            FROM 	dims_mod_sharefile_history as h
            WHERE 	h.id_share = :idshare
            AND     h.id_user>0";

$res_r = $db->query($sql_r, array(':idshare' => $_SESSION['currentshare']['id_share'] ));

while($value = $db->fetchrow($res_r)) {
    if (isset($tab_histo['users'][$value['id_user']])) {
        if ($tab_histo['users'][$value['id_user']]['timestp_create']<$value['timestp_create'])
            $tab_histo['users'][$value['id_user']]['timestp_create']=$value['timestp_create'];

		$tab_histo['users'][$value['id_user']]['cpte']++;
    }
    else {
        $value['cpte']=1;
        $tab_histo['users'][$value['id_user']] = $value;
    }
}

// compte des contacts
$sql_r = "SELECT        h.id_contact,h.timestp_create
            FROM 	dims_mod_sharefile_history as h
            WHERE 	h.id_share = :idshare
            AND         h.id_contact>0";

$res_r = $db->query($sql_r, array(':idshare' => $_SESSION['currentshare']['id_share'] ));

while($value = $db->fetchrow($res_r)) {
    if (isset($tab_histo['contacts'][$value['id_contact']])) {
        if ($tab_histo['contacts'][$value['id_contact']]['timestp_create']<$value['timestp_create'])
            $tab_histo['contacts'][$value['id_contact']]['timestp_create']=$value['timestp_create'];

		$tab_histo['contacts'][$value['id_contact']]['cpte']++;
    }
    else {
        $value['cpte']=1;
        $tab_histo['contacts'][$value['id_contact']] = $value;
    }
}

$share = new sharefile_share();
$share->open($_SESSION['currentshare']['id_share']);

?>
<form name="form_etape1" method="post" action="<? echo dims_urlencode($dims->getScriptEnv()."?op=share&action=stats"); ?>">
<br>
<table width="100%" cellpadding="0" cellspacing="2" border="0">
	<tr>
		<td colspan="6" style="font-size:14px;font-weight:bold;text-align:center;height:30px;">
			Nom du partage : <? echo $share->fields['label']; ?>
		</td>
	</tr>
    <tr style="background-color:#EDEDED;font-size:14px;font-weight:bold;"><td width="25%">Nom</td><td width="30%">Email</td><td width="10%">Derni&egrave;re consult.</td><td width="10%">Nb consult.</td>
	<td width="20%">Téléchargements possibles avant limite (<? echo $sharefile_param->fields['nbdownload']; ?>)</td><td width="5%">Rappel</td></tr>
    <?
    if (!empty($arrayusers)) {
        foreach($arrayusers as $k => $user) {
            if (isset($tab_histo['users'][$user['id']])) {
                $nb_histo = $tab_histo['users'][$user['id']]['cpte'];
                $datlect=dims_timestamp2local($tab_histo['users'][$user['id']]['timestp_create']);
            }
            else {
                $nb_histo = 0;
                $datlect['date']="";
            }
            echo "<tr><td>".$user['firstname']." ".$user['lastname']."</td><td>".$user['email']."</td><td>".$datlect['date']."</td><td>".$nb_histo."</td>";
			// calcul limite atteinte + deblocage
			if ($user['view']>=$sharefile_param->fields['nbdownload']) {
				// atteint !
				echo "<td><img src=\"./common/modules/system/img/ico_point_red.gif\">&nbsp;".($sharefile_param->fields['nbdownload']-$user['view'])."</td>";
			}
			else echo "<td><img src=\"./common/modules/system/img/ico_point_green.gif\">&nbsp;".($sharefile_param->fields['nbdownload']-$user['view'])."</td>";

			echo "<td><a title=\"Relancer\" href=".$dims->getScriptEnv()."?op=share&action=unlock_account&id=".$user['fuid']."><img border=\"0\" src=\"./common/modules/system/img/unclose_16.png\"></a></td>";

			echo "</tr>";
        }
    }

    if (!empty($arraycontacts)) {
        foreach($arraycontacts as $k => $user) {
            if (isset($tab_histo['contacts'][$user['id']])) {
                $nb_histo = $tab_histo['contacts'][$user['id']]['cpte'];
                $datlect=dims_timestamp2local($tab_histo['contacts'][$user['id']]['timestp_create']);
            }
            else {
                $nb_histo = 0;
                $datlect['date']="";
            }
            echo "<tr><td>".$user['firstname']." ".$user['lastname']."</td><td>".$user['email']."</td><td>".$datlect['date']."</td><td>".$nb_histo."</td>";

			// calcul limite atteinte + deblocage
			if ($user['view']>=$sharefile_param->fields['nbdownload']) {
				// atteint !
				echo "<td><img src=\"./common/modules/system/img/ico_point_red.gif\">&nbsp;".($sharefile_param->fields['nbdownload']-$user['view'])."</td>";
			}
			else echo "<td><img src=\"./common/modules/system/img/ico_point_green.gif\">&nbsp;".($sharefile_param->fields['nbdownload']-$user['view'])."</td>";

			echo "<td><a title=\"Relancer\" href=".$dims->getScriptEnv()."?op=share&action=unlock_account&id=".$user['fuid']."><img border=\"0\" src=\"./common/modules/system/img/unclose_16.png\"></a></td>";

			echo "</tr>";
        }
    }
    ?>
</table>
</form>
