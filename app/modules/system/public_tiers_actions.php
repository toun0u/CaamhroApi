<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD COLSPAN="6" ALIGN="left">
	<FORM ACTION="<? echo $scriptenv; ?>" NAME="liste_dossiers">
		<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op", "action_filtrer");
		$token->field("dossier_id");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		$select = 	"
					SELECT 		d.*, i.genre, i.nom, i.prenom
					FROM		dims_mod_business_dossier d,
								dims_mod_business_tiers_dossier td
					LEFT JOIN	dims_mod_business_interlocuteur i
					ON			td.interlocuteur_id = i.id
					WHERE		td.dossier_id = d.id
					AND			td.tiers_id = :tiersid
					AND			d.termine = 'Non'
					ORDER BY 	d.date_debut DESC
					";

		$db->query($select, array(
			':tiersid' => $_SESSION['business']['tiers_id']
		));
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="action_filtrer">
		Dossiers&nbsp;:&nbsp;<SELECT CLASS="SELECT" NAME="dossier_id" OnChange="javascript:document.liste_dossiers.submit();">
		<OPTION VALUE="">(tous)</OPTION>
		<?
		while ($fields = $db->fetchrow())
		{
			$sel = (isset($dossier_id) && $dossier_id == $fields['id']) ? 'selected' : '';
			echo "<OPTION $sel VALUE=\"{$fields['id']}\">{$fields['objet_dossier']} - {$fields['procedure']}</OPTION>";
		}
		?>
		</SELECT>
		<INPUT TYPE="submit" CLASS="FlatButton" VALUE="Filtrer">
		</FORM>
	</TD>
	<TD COLSPAN="2" ALIGN="right">
		<FORM ACTION="<? echo $scriptenv; ?>">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op", "action_ajouter");
			$token->field("FlatButton");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="action_ajouter">
		<INPUT TYPE="submit" CLASS="FlatButton" VALUE="Ajouter une Action">
		</FORM>
	</TD>
</TR>

<? $color = $skin->values['bgline1']; ?>
<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
	<TD></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_DATEJOUR; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_HEUREDEB; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_HEUREFIN; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TEMPS_PASSE; ?></TD>
	<TD ALIGN="LEFT">Libellé</TD>
	<TD ALIGN="LEFT">Dossiers</TD>
	<TD></TD>
</TR>
<?
$params = array();
if (isset($dossier_id) && $dossier_id != '') {
	$where = "AND (ad.dossier_id = :dossierid OR d.id = :dossierid )";
	$params[':dossierid'] = $dossier_id;
}
else $where = '';

$select =  "SELECT 		a.*,
						d.id as dossier_id,
						d.procedure,
						d.objet_dossier as dossier_intitule,
						i.nom,
						i.prenom
			FROM 		dims_mod_business_action a
			LEFT JOIN	dims_mod_business_action_detail ad
			ON 			a.id = ad.action_id AND ad.tiers_id = :tiersid
			LEFT JOIN	dims_mod_business_dossier d
			ON			ad.dossier_id = d.id
			LEFT JOIN	dims_mod_business_interlocuteur i
			ON			ad.interlocuteur_id = i.id
			WHERE 		(a.tiers_id = :tiersid OR ad.tiers_id = :tiersid )
			$where
			ORDER BY	datejour DESC, heuredeb DESC, heurefin DESC
			";

$params[':tiersid'] = $_SESSION['business']['tiers_id'];

$_SESSION['business']['tiers']['actions'] = array();

$db->query($select, $params);
if (!$db->numrows()) echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="8" ALIGN="CENTER">Aucune réponse</TD></TR>';
else
{
	$actions = array();
	while ($fields = $db->fetchrow())
	{
		$actions[$fields['id']][] = $fields;
	}

	//dims_print_r($actions);

	$temps_total = 0;
	foreach($actions as $action)
	// while ($fields = $db->fetchrow())
	{
		$fields = $action[0];

		$dossiers = '';
		foreach($action as $actiondetails)
		{
			if ($dossiers!='') $dossiers.= ', ';
			$dossiers .= "{$actiondetails['dossier_intitule']} - {$actiondetails['procedure']}";
		}

		$readonly = ($fields['tiers_id'] != $_SESSION['business']['tiers_id']);
		if (!$readonly) $_SESSION['business']['dossier']['actions'][$fields['id']] = $fields['id'];

		$temps_duplique = ($fields['temps_duplique'] == 'oui') ? '*' : '';

		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		$temps_total += $fields['temps_passe'];
		?>
		<TR bgcolor="<? echo $color; ?>">
			<TD>
			<?
			if (!$readonly)
			{
				?>
				<A HREF="<? echo "$scriptenv?op=action_dupliquer&action_id={$fields['id']}"; ?>"><img src="./common/modules/business/img/ico_renew.gif" border="0" ALT="dupliquer"></A>
				<?
			}
			?>
			</TD>
			<TD nowrap><? echo business_dateus2fr_ext($fields['datejour']);?></TD>
			<TD><? echo $fields['heuredeb']; ?></TD>
			<TD><? echo $fields['heurefin']; ?></TD>
			<TD><? echo "{$fields['temps_passe']} min $temps_duplique"; ?></TD>
			<TD><? echo $fields['libelle']; ?></TD>
			<TD><? echo $dossiers ?></TD>
			<TD WIDTH="1%" NOWRAP>
			<?
			if (!$readonly)
			{
				?>
				&nbsp;&nbsp;<A HREF="<? echo "$scriptenv?op=action_modifier&action_id={$fields['id']}"; ?>"><img src="./common/modules/business/img/ico_modify.gif" border="0" ALT="<? echo _DIMS_MODIFY; ?>"></A>
				&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo "$scriptenv?op=action_supprimer&action_id={$fields['id']}"; ?>','<? echo str_replace('<VALUE>',addslashes("{$fields['typeaction']} - {$fields['libelle']} - ".business_dateus2fr($fields['datejour'])),_BUSINESS_MSG_CONFIRMDELETE); ?>')"><img src="./common/modules/business/img/ico_delete.gif" border="0" ALT="<? echo _DIMS_DELETE; ?>"></A>
				&nbsp;&nbsp;
				<?
			}
			else
			{
				?>
				&nbsp;&nbsp;<A HREF="<? echo "$scriptenv?cat="._BUSINESS_CAT_DOSSIER."&dims_moduletabid="._BUSINESS_TAB_DOSSIERSACTIONS."&dossier_id={$fields['dossier_id']}&op=action_modifier&action_id={$fields['id']}"; ?>"><img src="./common/modules/business/img/ico_goto.gif" border="0" ALT="<? echo _BUSINESS_LABEL_GOTO; ?>"></A>
				&nbsp;&nbsp;&nbsp;<img src="./common/modules/business/img/ico_noway.gif">
				&nbsp;&nbsp;
				<?
			}
			?>
			</TD>
		</TR>
		<?
	}
	$temps_total_h = ($temps_total - ($temps_total % 60)) / 60;
	$temps_total_m = $temps_total - ($temps_total_h * 60);
	$temps_total = '';
	if ($temps_total_h > 0) $temps_total = "$temps_total_h h";
	if ($temps_total_m > 0) $temps_total .= "$temps_total_m min";
	echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="8" ALIGN="Left"><b>Temps total : '.$temps_total.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* : Temps Dupliqué</TD></TR>';
}
?>
</TABLE>
