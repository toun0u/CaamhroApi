<?
if (_DIMS_SESSIONTIME <= 86400) {
	$timestplimit=date("YmdHis",mktime(0, 0, date("G")*3600+date("i")*60-1*_DIMS_SESSIONTIME, date("m"), date("d"), date("Y")));
}
else $timestplimit = date("YmdHis",mktime(0, 0, date("G")*3600+date("i")*60-1*86400, date("m"), date("d"), date("Y")));;


$sql_pmod = "	SELECT			distinct c.firstname, c.lastname, c.id as id_pers, c.timestp_modify
				FROM			dims_mod_business_contact as c
				INNER JOIN		dims_user as u
				ON				u.id_contact = c.id
				INNER JOIN		dims_connecteduser as cu
				ON				cu.user_id =u.id
				and				cu.timestp > $timestplimit
				and				cu.workspace_id= :workspaceid ";


$res_p = $db->query($sql_pmod, array(
	':workspaceid' => $_SESSION['dims']['workspaceid']
));
$nb_resp = $db->numrows($res_p);
$class_col="";
?>
<table cellspacing="0" cellpadding="0" style="width:100%;margin-top:5px;margin-bottom:10px;">
	<tbody>
	<? if($nb_resp > 0) { ?>
		<tr style="font-size:11px;background-color:#ffffff;color:#777777;">
			<td style="width: 70%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?></td>
			<td style="width: 30%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
		</tr>
	<?
		$old_id = '';
		while($tab_p = $db->fetchrow($res_p)) {
			if($old_id != $tab_p['id_pers']) {
			$date_c = dims_timestamp2local($tab_p['timestp_modify']);

			if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
			echo '	<tr class="'.$class_col.'">
						<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
							<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_p['firstname'].'&nbsp;'.$tab_p['lastname'].'</a>
						</td>
						<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
							'.$date_c['date'].'
						</td>
					</tr>';
			$old_id = $tab_p['id_pers'];
			}
		}
	}
	else {
		echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
	}
	?>
   </tbody>
</table>
