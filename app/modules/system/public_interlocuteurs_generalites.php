<script language="javascript">
function business_validate(form)
{
	if (dims_validatefield("<? echo _BUSINESS_LABEL_NOM; ?>",form.interlocuteur_nom,"string"))
		return true;

	return false;
}
</script>

<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST" OnSubmit="javascript:return business_validate(this)">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", 				"interlocuteur_enregistrer");
	$token->field("interlocuteur_id",	$interlocuteur->fields['id']);
	$token->field("interlocuteur_genre");
	$token->field("interlocuteur_titre");
	$token->field("interlocuteur_nom");
	$token->field("interlocuteur_prenom");
	$token->field("interlocuteur_adresse");
	$token->field("interlocuteur_codepostal");
	$token->field("interlocuteur_ville");
	$token->field("interlocuteur_pays");
	$token->field("interlocuteur_mel");
	$token->field("interlocuteur_telephone");
	$token->field("interlocuteur_telecopie");
	$token->field("interlocuteur_telmobile");
	$token->field("interlocuteur_commentaire");
	$token->field("categorie_interlocuteur");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="interlocuteur_enregistrer">
<INPUT TYPE="HIDDEN" NAME="interlocuteur_id" VALUE="<? echo $interlocuteur->fields['id']; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1">
<?
$sql = "SELECT 	dims_mod_business_tiers.intitule, dims_mod_business_tiers.ville, dims_mod_business_tiers_interlocuteur.*
		FROM 	dims_mod_business_tiers_interlocuteur,
				dims_mod_business_tiers
		WHERE	dims_mod_business_tiers_interlocuteur.interlocuteur_id = :interlocuteurid
		AND		dims_mod_business_tiers.id = dims_mod_business_tiers_interlocuteur.tiers_id
		";

$res = $db->query($sql, array(
	':interlocuteurid' => $interlocuteur->fields['id']
));

if ($db->numrows($res))
{
	$color = $skin->values['bgline1'];
	?>
	<TR>
		<TD COLSPAN="2" CLASS="Title">Fonctions</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<table cellpadding="2" cellspacing="1">
		<tr bgcolor="<? echo $color; ?>" class="title">
			<td>Client</td>
			<td>Ville</td>
			<td>Fonction</td>
			<td>Email</td>
			<td>Telephone</td>
			<td>Télécopie</td>
			<td>Mobile</td>
		</tr>
		<?
		while ($fields = $db->fetchrow($res))
		{
			$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
			?>
			<tr bgcolor="<? echo $color; ?>">
				<td><b><? echo $fields['intitule']; ?><b></td>
				<td><? echo $fields['ville']; ?></td>
				<td><? echo $fields['fonction']; ?></td>
				<td><? echo $fields['mel']; ?></td>
				<td nowrap><? echo business_display_tel($fields['telephone']); ?></td>
				<td nowrap><? echo business_display_tel($fields['telecopie']); ?></td>
				<td nowrap><? echo business_display_tel($fields['telmobile']); ?></td>
			</tr>
			<?
		}
		?>
		</table>
		</td>
	</TR>
<?
}
?>

<TR>
	<TD COLSPAN="2" CLASS="Title">Informations personnelles</TD>
</TR>
<TR>
	<!-- colonne 1 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_GENRE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<SELECT CLASS="Select" NAME="interlocuteur_genre">
				<?
				$listenum = business_getlistenum('genre');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $interlocuteur->fields['genre']) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<!--TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_CIVILITE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_civilite" VALUE="<? echo $interlocuteur->fields['civilite']; ?>"></TD>
		</TR-->
		<TR>
			<TD ALIGN="right">Titre:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_titre" VALUE="<? echo $interlocuteur->fields['titre']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_NOM; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_nom" VALUE="<? echo $interlocuteur->fields['nom']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_PRENOM; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_prenom" VALUE="<? echo $interlocuteur->fields['prenom']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right" VALIGN="top"><? echo _BUSINESS_LABEL_ADRESSE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><TEXTAREA CLASS="Text" ROWS="3" COLS="30" NAME="interlocuteur_adresse"><? echo $interlocuteur->fields['adresse']; ?></TEXTAREA></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_CODEPOSTAL; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="5" NAME="interlocuteur_codepostal" VALUE="<? echo $interlocuteur->fields['codepostal']; ?>"></TD>
		</TR>

		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_VILLE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_ville" VALUE="<? echo $interlocuteur->fields['ville']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_PAYS; ?>:&nbsp;</TD>
			<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="interlocuteur_pays">
				<?
				$listenum = business_getlistenum('pays');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $interlocuteur->fields['pays']) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</TR>
		</TABLE>
	</TD>

	<!-- colonne 2 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_EMAIL; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_mel" VALUE="<? echo $interlocuteur->fields['mel']; ?>"><? if ($interlocuteur->fields['mel']) echo "&nbsp;&nbsp;<A HREF=\"mailto:{$interlocuteur->fields['mel']}\" TARGET=\"_blank\"><IMG BORDER=\"0\" ALT=\"Envoyer un Mel\" SRC=\"./common/modules/business/img/ico_email.gif\"></A>"?></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_TEL; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="interlocuteur_telephone" VALUE="<? echo business_display_tel($interlocuteur->fields['telephone']); ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_FAX; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="interlocuteur_telecopie" VALUE="<? echo business_display_tel($interlocuteur->fields['telecopie']); ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><? echo _BUSINESS_LABEL_MOBILE; ?>:&nbsp;</TD>
			<TD ALIGN="LEFT"><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="interlocuteur_telmobile" VALUE="<? echo business_display_tel($interlocuteur->fields['telmobile']); ?>"></TD>
		</TR>

		<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP"><? echo _BUSINESS_LABEL_COMMENTAIRE; ?>:&nbsp;</TD>
			<TD ALIGN="LEFT">
			<TEXTAREA CLASS="Text" TYPE="Text" COLS="40" ROWS="4" NAME="interlocuteur_commentaire"><? echo $interlocuteur->fields['commentaire']; ?></TEXTAREA>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP">Catégorie (Emailing):&nbsp;</TD>
			<TD ALIGN="LEFT">
				<TABLE CELLPADDING="0" CELLSPACING="0">
					<?
					$listenum = business_getlistenum('categorie_interlocuteur', false);
					foreach($listenum as $enum)
					{
						$sel = (in_array($enum['libelle'],$interlocuteur->categories)) ? 'checked' : '';
						echo "<tr><td><input type=\"checkbox\" $sel name=\"categorie_interlocuteur[]\" value=\"{$enum['libelle']}\"></td><td>{$enum['libelle']}</td></tr>";
					}
					?>
				</TABLE>
			</TD>
		</tr>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<TD ALIGN="left" VALIGN="middle">
		Fiche créée le <b><? echo business_dateus2fr($interlocuteur->fields['date_creation']); ?></b> - Dernière modification le <b><? echo business_dateus2fr($interlocuteur->fields['date_maj']); ?></b>
	</TD>
	<TD ALIGN="right" VALIGN="middle">
		<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>">
	</TD>
</TR>
</TABLE>
</form>