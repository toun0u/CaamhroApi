<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD COLSPAN="8" ALIGN="right">
	<FORM ACTION="<? echo $scriptenv; ?>">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op","project_ajouter");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<INPUT TYPE="HIDDEN" NAME="op" VALUE="project_ajouter">
	<INPUT TYPE="submit" CLASS="FlatButton" VALUE="<? echo _PROJECT_LABEL_ADD_PROJECT; ?>">
	</FORM>
	</TD>
</TR>
<? $color = $skin->values['bgline1']; ?>
<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
	<TD ALIGN="LEFT"><? echo _FORM_PROJECT_LABEL; ?></TD>
	<TD ALIGN="LEFT"><? echo _FORM_PROJECT_STATE; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_INTERLOCUTEUR; ?></TD>
	<TD ALIGN="LEFT"><? echo _FORM_PROJECT_START_DATE; ?></TD>
	<TD ALIGN="LEFT"><? echo _FORM_PROJECT_END_DATE; ?></TD>
	<TD></TD>
</TR>
<?
$select =	"
			SELECT		p.*, i.genre, i.nom, i.prenom
			FROM		dims_project p
			INNER JOIN	dims_mod_business_tiers_project tp
			ON			tp.id_project = p.id
			AND			tp.id_tiers = :idtier
			LEFT JOIN	dims_mod_business_interlocuteur i
			ON			tp.interlocuteur_id = i.id
			ORDER BY	d.date_debut DESC
			";

$res=$db->query($select, array(
	':idtier' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
));
if (!$db->numrows($res)) echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="6" ALIGN="CENTER">Aucune réponse</TD></TR>';
else {
	while ($fields = $db->fetchrow($res)) {
		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		$ouvrir = "$scriptenv?op=project_ouvrir&project_id={$fields['id']}";
		$allervers = "$scriptenv?cat="._BUSINESS_CAT_DOSSIER."&dims_moduletabid="._BUSINESS_TAB_DOSSIERSINFORMATIONS."&op=project_ouvrir&project_id={$fields['id']}";
		$effacer = "$scriptenv?op=project_effacer&project_id={$fields['id']}";
		$couper = "$scriptenv?op=project_couper&project_id={$fields['id']}";

		if ($fields['progress'] == 100) {
			$avancement =	"
							<table cellpadding=\"0\" cellspacing=\"1\" width=\"100\" height=\"14\" bgcolor=\"#000000\">
							<tr>
								<td bgcolor=\"#FF0000\"><font style=\"font-size:9px;color:#000000\">&nbsp;terminé&nbsp;</font></td>
							</tr>
							</table>
							";
		}
		else {
			$avancement=display_avancement($fields['progress']);
		}
		?>
		<TR BGCOLOR="<? echo $color; ?>">
			<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['label'];?></A></TD>
			<TD><? echo $fields['domaine_intervention'];?></TD>
			<TD><? echo $avancement; ?></TD>
			<TD><? echo "{$fields['genre']} {$fields['nom']} {$fields['prenom']}";?></TD>
			<TD><? echo business_dateus2fr($fields['date_start']);?></TD>
			<TD><? echo business_dateus2fr($fields['date_end']);?></TD>
			<TD ALIGN="CENTER" nowrap>
				<A HREF="<? echo $ouvrir; ?>"><img src="./common/modules/system/img/ico_modify.gif" border="0" ALT="<? echo _BUSINESS_LABEL_MODIFY; ?>"></A>
				&nbsp;&nbsp;&nbsp;<A HREF="<? echo $allervers; ?>"><img src="./common/modules/system/img/ico_goto.gif" ALT="<? echo _BUSINESS_LABEL_GOTO; ?>" border="0"></A>
				&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo $couper; ?>','<? echo str_replace('<VALUE>',"{$fields['label']}",_BUSINESS_MSG_CONFIRMCUT); ?>')"><img src="./common/modules/system/img/ico_cut.gif" ALT="<? echo _BUSINESS_LABEL_CUT; ?>" border="0"></A>
				&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo $effacer; ?>','<? echo str_replace('<VALUE>',"{$fields['label']}",_BUSINESS_MSG_CONFIRMDELETE); ?>')"><img src="./common/modules/system/img/ico_delete.gif" ALT="<? echo _BUSINESS_LABEL_DELETE; ?>" border="0"></A>
			</TD>
		</TR>
		<?
	}
}
?>
</TABLE>
