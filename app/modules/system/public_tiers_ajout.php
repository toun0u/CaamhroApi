<?
switch($op)
{
	default:
		$title = "Etape 1 (recherche)";
	break;

	case 'tiers_ajout1':
		$title = "Etape 2 (choix)";
	break;

	case 'tiers_ajout2':
		$title = "Etape 3 (validation)";
	break;
}

echo $skin->open_simplebloc("Ajout d'un Client - $title",'100%','');
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR><TD HEIGHT="5"></TD></TR></TABLE>
<?
switch($op) {
	default:
		?>
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",	"tiers_ajout1");
			$token->field("tiers_intitule");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_ajout1">
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<tr>
			<td align="RIGHT"><? echo _BUSINESS_LABEL_INTITULE; ?>:&nbsp;</td>
			<td><input type="text" name="tiers_intitule" class="text" value="" size="25"></td>
		</tr>
		</table>
		<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<tr>
			<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SEARCH; ?>"></TD>
		</tr>
		</table>
		</FORM>
		<?
	break;

	case 'tiers_ajout1':
		?>
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op", 			"tiers_enregistrer");
			$token->field("tiers_intitule", htmlentities($tiers_intitule));
			$token->field("tiers_id");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_enregistrer">
		<INPUT TYPE="HIDDEN" NAME="tiers_intitule" VALUE="<? echo htmlentities($tiers_intitule); ?>">
<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<?
		$params = array();
		$select = 	"
					SELECT 	*
					FROM 	dims_mod_business_tiers
					WHERE 1
					";
		if (isset($tiers_intitule)) {
			$select .= " AND (intitule LIKE :tiersintitule OR intitule_search LIKE :tiersintitule)";
			$params[':tiersintitule'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$tiers_intitule.'%');
		}

		$db->query("$select ORDER BY intitule", $params);
		while ($fields = $db->fetchrow())
		{
			$checked = ($db->numrows()>1) ? '' : 'checked';
			?>
			<TR>
				<TD>
				<INPUT TYPE="radio" NAME="tiers_id" <? echo $checked; ?> VALUE="<? echo $fields['id']; ?>">&nbsp;[ <? echo $fields['typeclient']; ?> ]&nbsp;<? echo $fields['intitule']; ?>, <? echo $fields['adresse']; ?>, <? echo $fields['codepostal']; ?> <? echo $fields['ville']; ?>
				</TD>
			</TR>
			<?
		}
		$checked = ($db->numrows()) ? '' : 'checked';
		?>
		<TR>
			<TD>
			<INPUT TYPE="radio" NAME="tiers_id" <? echo $checked; ?> VALUE="0">&nbsp;<b>Nouveau Client [ <? echo "$tiers_intitule"; ?> ]</b>
			</TD>
		</TR>
		</table>

		<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<tr>
			<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Button" OnClick="document.location.href='<? echo "$scriptenv?op=tiers_ajouter"; ?>'" CLASS="FlatButton" VALUE="« Retour Etape 1">&nbsp;<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="Sélectionner ce Client"></TD>
		</tr>
		</table>

		</form>
		<?
	break;

}
?>


<? echo $skin->close_simplebloc(); ?>
