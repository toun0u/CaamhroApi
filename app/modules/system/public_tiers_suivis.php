<?
if (!isset($type_suivi)) $type_suivi = '';
if (!isset($exercice_suivi)) $exercice_suivi = '';
if (!isset($dossier_id)) $dossier_id = '';
?>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD COLSPAN="9" ALIGN="left">
		<?
		?>
		<table cellpadding="0" cellspacing="0">

		<tr>
			<td>
			<form ACTION="<? echo $scriptenv; ?>" name="suivi_filtre">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("type_suivi");
				$token->field("op", "suivi_filtrer");
				$token->field("exercice_suivi");
				$token->field("dossier_id");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="suivi_filtrer">
			Type&nbsp;:&nbsp;<select class="select" name="type_suivi" onchange="javascript:document.suivi_filtre.submit()">
			<option value="">(tous)</option>
			<?
			$listenum = business_getlistenum('typesuivi', false);
			foreach($listenum as $id_enum => $enum)
			{
				$sel = ($enum['libelle'] == $type_suivi) ? 'selected' : '';
				echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
			}
			?>
			</select>

			&nbsp;&nbsp;Exercice&nbsp;:&nbsp;<select class="select" name="exercice_suivi" onchange="javascript:document.suivi_filtre.submit()">
			<option value="">(tous)</option>
			<?
			$select = 	"
						SELECT 		distinct(exercice)
						FROM 		dims_mod_business_suivi
						WHERE		tiers_id = :idtier
						AND 		id_workspace = :idworkspace
						ORDER BY 	exercice DESC
						";

			$db->query($select, array(
				':idtier' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));
			while ($fields = $db->fetchrow())
			{
				$sel = ($fields['exercice'] == $exercice_suivi) ? 'selected' : '';
				echo "<option $sel value=\"{$fields['exercice']}\">{$fields['exercice']}</option>";
			}
			?>
			</select>

			&nbsp;&nbsp;Dossiers&nbsp;:&nbsp;<select class="select" name="dossier_id" onchange="javascript:document.suivi_filtre.submit()">
			<OPTION VALUE="">(tous)</OPTION>
			<?
			$select = 	"
						SELECT 		d.*
						FROM 		dims_mod_business_dossier d,
									dims_mod_business_tiers_dossier td
						WHERE		td.dossier_id = d.id
						AND			td.tiers_id = :idtier
						ORDER BY 	objet_dossier, `procedure`
						";

			$db->query($select, array(
				':idtier' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
			));
			while ($fields = $db->fetchrow())
			{
				$sel = (isset($dossier_id) && $dossier_id == $fields['id']) ? 'selected' : '';
				echo "<OPTION $sel VALUE=\"{$fields['id']}\">{$fields['objet_dossier']} - {$fields['procedure']}</OPTION>";
			}
			?>
			</select>
			<INPUT TYPE="submit" CLASS="FlatButton" VALUE="Filtrer">
			</form>
			</td>
		</tr>
		</table>
	</TD>
	<TD COLSPAN="2" ALIGN="right">
		<?
		if ($datejour >= $params['datedeb'] && $datejour <= $params['datefin'])
		{
			?>
			<FORM ACTION="<? echo $scriptenv; ?>">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("op", "suivi_ajouter");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="suivi_ajouter">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<INPUT TYPE="HIDDEN" NAME="op" VALUE="suivi_ajouter">
				<INPUT TYPE="submit" CLASS="FlatButton" VALUE="Ajouter un Suivi">
				</td>
			</tr>
			</table>
			</FORM>
			<?
		}
		else echo "Il n'y a pas d'exercice en cours, vous devez en définir un.";
		?>
	</TD>
</TR>


<? $color = $skin->values['bgline1']; ?>
<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
	<TD width="170"></TD>
	<TD ALIGN="LEFT">Type</TD>
	<TD ALIGN="LEFT">Exercice</TD>
	<TD ALIGN="LEFT">Numéro</TD>
	<TD ALIGN="LEFT">Date</TD>
	<TD ALIGN="LEFT">Libellé</TD>
	<TD ALIGN="LEFT">Dossier</TD>
	<TD align="LEFT" nowrap><? echo _BUSINESS_LABEL_DATEJOUR; ?></TD>
	<TD align="right" nowrap>Montant HT</TD>
	<TD align="right" nowrap>Solde TTC</TD>
	<TD width="75"></TD>
</TR>
<?
$where  = '';
if (isset($dossier_id) && $dossier_id != '') {
	$where .= " AND s.dossier_id = $dossier_id ";
	$params[':iddossier'] = array('type' => PDO::PARAM_INT, 'value' => $dossier_id);
}
if (isset($type_suivi) && $type_suivi != '') {
	$where .= " AND s.type = :type ";
	$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type_suivi);
}
if (isset($exercice_suivi) && $exercice_suivi != '') {
	$where .= " AND s.exercice = :exercice";
	$params[':exercice'] = array('type' => PDO::PARAM_INT, 'value' => $exercice_suivi);
}

$select = 	"
			SELECT 		s.*, d.objet_dossier
			FROM 		dims_mod_business_suivi s
			LEFT JOIN	dims_mod_business_dossier d
			ON			s.dossier_id = d.id
			WHERE		s.tiers_id = :idtier
			$where
			AND 		s.id_workspace = :idworkspace
			ORDER BY	s.datejour DESC, s.id DESC
			";
$params[':idtier'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']);
$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);

$_SESSION['business']['dossier']['suivis'] = array();

$db->query($select, $params);
if (!$db->numrows()) echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="11" ALIGN="CENTER">Aucune réponse</TD></TR>';
else
{
	while ($fields = $db->fetchrow())
	{
		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		$suivi = new suivi();
		$suivi->fields = $fields;

		$ouvrir = dims_urlencode("$scriptenv?op=suivi_modifier&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}");
		?>
		<TR bgcolor="<? echo $color; ?>">
			<TD width="170" align="center">
				<a href="<? echo dims_urlencode("$scriptenv?op=suivi_imprimer&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}"); ?>"><img src="./common/modules/business/img/download_odt.gif" border="0" alt="imprimer (ODT)"></a>
				<a href="<? echo dims_urlencode("$scriptenv?op=suivi_imprimer&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}&format=PDF"); ?>"><img src="./common/modules/business/img/download_pdf.gif" border="0" alt="imprimer (PDF)"></a>
				<a href="<? echo dims_urlencode("$scriptenv?op=suivi_imprimer&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}&format=DOC"); ?>"><img src="./common/modules/business/img/download_doc.gif" border="0" alt="imprimer (DOC)"></a>
			</TD>
			<td>
			<?
			$filename = './common/modules/business/img/ico_'.strtolower($fields['type']).'.gif';
			if (file_exists($filename)) echo "<img src=\"$filename\">";
			else echo "<strong>{$fields['type']}</strong>";
			?>
			</td>
			<TD><? echo $fields['exercice']; ?></TD>
			<TD><A HREF="<? echo $ouvrir; ?>"><? echo $suivi->getnum(); ?></A></TD>
			<TD><? echo business_dateus2fr($fields['datejour']);?></TD>
			<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['libelle']; ?></A></TD>
			<TD><? echo $fields['objet_dossier']; ?></TD>
			<TD nowrap><? echo business_dateus2fr($fields['datejour']);?></TD>
			<TD nowrap align="right"><? echo business_format_price($fields['montantht']); ?></TD>
			<?
			if ($suivi->fields['type'] == 'Facture') //  && $suivi->fields['solde'] > 0
			{
				if ($suivi->fields['solde'] > 0)
				{
					?>
					<TD nowrap align="right"><? echo business_format_price($fields['solde']); ?><br />
					<a href="javascript:dims_confirmlink('<? echo dims_urlencode("$scriptenv?op=suivi_solder&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}"); ?>','<? echo _DIMS_CONFIRM; ?>');">Solder</a>
					<?
				}
				else
				{
					?>
					<TD nowrap align="right"><? echo business_format_price($fields['solde']); ?></TD>
					<?
				}
			}
			else echo '<td align="right"> - - - - </td>';
			?>
			<TD width="120" align="center">
				<A HREF="<? echo $ouvrir; ?>"><img src="./common/modules/business/img/ico_modify.gif" border="0" ALT="<? echo _DIMS_MODIFY; ?>"></A>
				<A HREF="javascript:dims_confirmlink('<? echo dims_urlencode("$scriptenv?op=suivi_supprimer&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}"); ?>','<? echo str_replace('<VALUE>',addslashes("{$fields['type']} - ".$suivi->getnum()." - {$fields['libelle']}"),_BUSINESS_MSG_CONFIRMDELETE); ?>')"><img src="./common/modules/business/img/ico_delete.gif" border="0" ALT="<? echo _DIMS_DELETE; ?>"></A>
				<A HREF="javascript:dims_confirmlink('<? echo dims_urlencode("$scriptenv?op=suivi_dupliquer&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}"); ?>','<? echo str_replace('<VALUE>',addslashes("{$fields['type']} - ".$suivi->getnum()." - {$fields['libelle']}"),_BUSINESS_MSG_CONFIRMRENEW); ?>')"><img src="./common/modules/business/img/ico_renew.gif" border="0"></A>
			</TD>
		</TR>
		<?
	}
}
?>
</TABLE>
