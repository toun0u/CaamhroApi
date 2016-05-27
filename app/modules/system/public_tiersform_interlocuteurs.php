<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD COLSPAN="8" ALIGN="right">
	<FORM ACTION="<? echo $scriptenv; ?>">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op", "interlocuteur_ajouter");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<INPUT TYPE="HIDDEN" NAME="op" VALUE="interlocuteur_ajouter">
	<INPUT TYPE="submit" CLASS="FlatButton" VALUE="<? echo _BUSINESS_LABELTAB_INTERLOCUTEURADD; ?>">
	</FORM>
	</TD>
</TR>

<? $color = $skin->values['bgline1']; ?>
<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_GENRE; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_NOM; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_PRENOM; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_FONCTION; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TEL; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_MOBILE; ?></TD>
	<TD ALIGN="LEFT">Liste de Diffusion</TD>
	<TD></TD>
</TR>
<?
$select =	"
			SELECT	i.id,
					i.genre,
					i.nom,
					i.prenom,
					ti.fonction,
					ti.service,
					ti.telephone,
					ti.telmobile,
					ic.categorie
			FROM	dims_mod_business_tiers_interlocuteur ti,
					dims_mod_business_interlocuteur i
			LEFT JOIN	dims_mod_business_interlocuteur_categorie ic ON ic.id_interlocuteur = i.id
			WHERE	ti.interlocuteur_id = i.id
			AND		ti.tiers_id = :idtier
			ORDER BY nom, prenom
			";

$res=$db->query($select, array(
	':idtier' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
));
if (!$db->numrows($res)) echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="8" ALIGN="CENTER">Aucune réponse</TD></TR>';
else
{
	while ($fields = $db->fetchrow($res)) {
		if (!isset($interlocuteurs[$fields['id']])) $interlocuteurs[$fields['id']] = $fields;
		if ($fields['categorie']) $interlocuteurs[$fields['id']]['categories'][] = $fields['categorie'];
	}


	foreach($interlocuteurs as $fields) {
		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		$ouvrir = "$scriptenv?op=interlocuteur_ouvrir&interlocuteur_id={$fields['id']}";
		$allervers = "$scriptenv?cat="._BUSINESS_CAT_INTERLOCUTEUR."&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS."&op=interlocuteur_ouvrir&interlocuteur_id={$fields['id']}";
		$couper = "$scriptenv?op=interlocuteur_couper&interlocuteur_id={$fields['id']}";
		?>
			<TR BGCOLOR="<? echo $color; ?>">
				<TD><? echo $fields['genre'];?></TD>
				<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['nom'];?></A></TD>
				<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['prenom'];?></A></TD>
				<TD><? echo $fields['fonction'];?></TD>
				<TD><? echo business_display_tel($fields['telephone']);?></TD>
				<TD><? echo business_display_tel($fields['telmobile']);?></TD>
				<TD>
				<?
				if (isset($fields['categories']))
				{
					$i=1;
					foreach($fields['categories'] as $cat)
					{
						echo $cat;
						if ($i++<sizeof($fields['categories'])) echo ', &nbsp;';
					}
				}
				?>
				</TD>
				<TD ALIGN="CENTER">
					<A HREF="<? echo $ouvrir; ?>"><img src="./common/modules/system/img/ico_modify.gif" border="0" ALT="<? echo _DIMS_MODIFY; ?>"></A>
					&nbsp;&nbsp;&nbsp;<A HREF="<? echo $allervers; ?>"><img src="./common/modules/system/img/ico_goto.png" ALT="<? echo _BUSINESS_LABEL_GOTO; ?>" border="0"></A>
					&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo $couper; ?>','<? echo str_replace('<VALUE>',"{$fields['nom']} {$fields['prenom']}",_BUSINESS_MSG_CONFIRMCUT); ?>')"><img src="./common/modules/system/img/ico_cut.gif" ALT="<? echo _DIMS_DELETE; ?>" border="0"></A>
				</TD>
			</TR>
		<?
	}
}
?>
</TABLE>
