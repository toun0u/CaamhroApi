<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">

<? $color = $skin->values['bgline1']; ?>
<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_INTITULE; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_VILLE; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TYPECLIENT; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TEL; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_MOBILE; ?></TD>
	<TD ALIGN="LEFT"></TD>
</TR>
<?
$select =  "SELECT 		t.*,ti.fonction,ti.telephone as titel,ti.telmobile as timobile
			FROM 		dims_mod_business_tiers t,
						dims_mod_business_tiers_interlocuteur ti
			WHERE		ti.tiers_id = t.id
			AND			ti.interlocuteur_id = :idinterlocutor
			ORDER BY 	intitule, typeclient, ville
			";

$res=$db->query($select, array(
	':idinterlocutor' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['interlocuteur_id'])
));

if (!$db->numrows($res)) echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="6" ALIGN="CENTER">Aucune réponse</TD></TR>';
else
{
	while ($fields = $db->fetchrow($res)) {
		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		$allervers = "$scriptenv?cat="._BUSINESS_CAT_TIERS."&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS."&tiers_id={$fields['id']}";
		$couper = "$scriptenv?op=tiers_couper&tiers_id={$fields['id']}";

		?>
		<TR bgcolor="<? echo $color; ?>">
			<TD><A HREF="<? echo $allervers; ?>"><? echo $fields['intitule'];?></A></TD>
			<TD><? echo $fields['ville'];?></TD>
			<TD><? echo $fields['typeclient'];?></TD>
			<TD><?
			if ($fields['titel']!="") echo business_display_tel($fields['titel'])." / ";
			echo business_display_tel($fields['telephone']);
			?></TD>
			<TD><?
			if ($fields['timobile']!="") echo business_display_tel($fields['timobile'])." / ";
			echo business_display_tel($fields['telmobile']);
			?></TD>
			<TD ALIGN="CENTER">
				<A HREF="<? echo $allervers; ?>"><img src="./common/modules/system/img/ico_goto.png" ALT="<? echo _BUSINESS_LABEL_GOTO; ?>" border="0"></A>
				&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo $couper; ?>','<? echo str_replace('<VALUE>',addslashes($fields['intitule']),_BUSINESS_MSG_CONFIRMCUT); ?>')"><img src="./common/modules/business/img/ico_cut.gif" ALT="<? echo _BUSINESS_LABEL_CUT; ?>" border="0"></A>
			</TD>
		</TR>
		<?
	}
}
?>
</TABLE>
