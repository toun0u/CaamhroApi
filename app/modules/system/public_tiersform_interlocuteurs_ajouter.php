<?
switch($op)
{
	default:
		$title = "Etape 1 (recherche)";
	break;

	case 'interlocuteur_ajout1':
		$title = "Etape 2 (choix)";
	break;

	case 'interlocuteur_ajout2':
		$title = "Etape 3 (validation)";
	break;
}

echo $skin->open_simplebloc(_BUSINESS_LABELTAB_CONTACTADD." - $title",'border:2px solid '._BUSINESS_COLOR_INTERLOC.';','color:'._BUSINESS_COLOR_SEL.';background-color:'._BUSINESS_COLOR_INTERLOC.';');
?>

<TABLE WIDTH="100%" CELLPADDING="4" CELLSPACING="0" BORDER="0" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<TD ALIGN="right">
	<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op","");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<INPUT TYPE="HIDDEN" NAME="op" VALUE="">
	<INPUT TYPE="submit" CLASS="FlatButton" VALUE="<? echo _BUSINESS_LABEL_BACK_CONTACT; ?>">
	</FORM>
	</TD>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
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
			$token->field("op", "interlocuteur_ajout1");
			$token->field("interlocuteur_nom");
			$token->field("interlocuteur_prenom");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="interlocuteur_ajout1">
			<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
			<tr>
				<td align="RIGHT"><? echo _BUSINESS_LABEL_NOM; ?>:&nbsp;</td>
				<td><input type="text" name="interlocuteur_nom" class="text" value="" size="25"></td>
			</tr>
			<tr>
				<td align="RIGHT"><? echo _BUSINESS_LABEL_PRENOM; ?>:&nbsp;</td>
				<td><input type="text" name="interlocuteur_prenom" class="text" value="" size="25"></td>
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

	case 'interlocuteur_ajout1':
		?>
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",						"interlocuteur_ajout2");
			$token->field("interlocuteur_nom",		htmlentities($interlocuteur_nom));
			$token->field("interlocuteur_prenom",	htmlentities($interlocuteur_prenom));
			$token->field("interlocuteur_id");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="interlocuteur_ajout2">
		<INPUT TYPE="HIDDEN" NAME="interlocuteur_nom" VALUE="<? echo htmlentities($interlocuteur_nom); ?>">
		<INPUT TYPE="HIDDEN" NAME="interlocuteur_prenom" VALUE="<? echo htmlentities($interlocuteur_prenom); ?>">
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<?
		$select = 	"
					SELECT 	*
					FROM 	dims_mod_business_interlocuteur
					WHERE 1
					";
		if (isset($interlocuteur_nom)) {
			$select .= " AND (nom LIKE :name OR nom_search LIKE :name)";
			$params[':name'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_prenom.'%');
		}
		if (isset($interlocuteur_prenom)) {
			$select .= " AND (prenom LIKE :name OR prenom_search LIKE :name)";
			$params[':name'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_prenom.'%');
		}

		$res=$db->query("$select ORDER BY nom, prenom", $params);
		while ($fields = $db->fetchrow($res)) {
			$checked = ($db->numrows($res)>1) ? '' : 'checked';
			?>
			<TR>
				<TD>
				<INPUT TYPE="radio" NAME="interlocuteur_id" VALUE="<? echo $fields['id']; ?>">&nbsp;<? echo $fields['genre']; ?> <? echo $fields['nom']; ?> <? echo $fields['prenom']; ?>
				</TD>
			</TR>
			<?
		}
		$checked = ($db->numrows($res)) ? '' : 'checked';
		?>
		<TR>
			<TD>
			<INPUT TYPE="radio" NAME="interlocuteur_id" <? echo $checked; ?> VALUE="0">&nbsp;<b><? echo _BUSINESS_LABELTAB_CONTACTADD." [ ".$interlocuteur_nom." ".$interlocuteur_prenom; ?> ]</b>
			</TD>
		</TR>
		</table>

		<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<tr>
			<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Button" OnClick="document.location.href=''" CLASS="FlatButton" VALUE="« Retour Etape 1">&nbsp;<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _BUSINESS_LABELTAB_CONTACTSELECT; ?>"></TD>
		</tr>
		</table>
		</FORM>
		<?
	break;

	case 'interlocuteur_ajout2':
		?>
		<script language="javascript">
		function interlocuteur_valider(form)
		{
			if (dims_validatefield("<? echo _BUSINESS_LABEL_NOM; ?>",form.interlocuteur_nom,"string"))
				return true;

			return false;
		}
		</script>

		<?
		$interlocuteur = new interlocuteur();
		if (isset($interlocuteur_id) && $interlocuteur_id) $interlocuteur->open($interlocuteur_id);
		else {
			$interlocuteur->init_description();
			$interlocuteur->fields['nom'] = $interlocuteur_nom;
			$interlocuteur->fields['prenom'] = $interlocuteur_prenom;
		}

		$tiers_interlocuteur = new tiers_interlocuteur();
		if (!$tiers_interlocuteur->open($_SESSION['business']['tiers_id'],$interlocuteur_id)) {
			$tiers_interlocuteur->init_description();
			$tiers_interlocuteur->fields['telephone'] = $tiers->fields['telephone'];
			$tiers_interlocuteur->fields['telecopie'] = $tiers->fields['telecopie'];
			$tiers_interlocuteur->fields['telmobile'] = $tiers->fields['telmobile'];
			$tiers_interlocuteur->fields['email'] = $tiers->fields['mel'];
			$tiers_interlocuteur->fields['adresse'] = $tiers->fields['adresse'];
			$tiers_interlocuteur->fields['codepostal'] = $tiers->fields['codepostal'];
			$tiers_interlocuteur->fields['ville'] = $tiers->fields['ville'];
			$tiers_interlocuteur->fields['pays'] = $tiers->fields['pays'];
			if ($tiers_interlocuteur->fields['pays'] == '') $tiers_interlocuteur->fields['pays'] = 'France';
		}
		?>
		<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" OnSubmit="javascript:return interlocuteur_valider(this)">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",					"interlocuteur_ajout3");
			$token->field("interlocuteur_id",	$interlocuteur_id);
			$token->field("tiersinterlocuteur_fonction");
			$token->field("tiersinterlocuteur_mel");
			$token->field("tiersinterlocuteur_telephone");
			$token->field("tiersinterlocuteur_telecopie");
			$token->field("tiersinterlocuteur_telmobile");
			$token->field("tiersinterlocuteur_adresse");
			$token->field("tiersinterlocuteur_codepostal");
			$token->field("tiersinterlocuteur_ville");
			$token->field("tiersinterlocuteur_pays");
			$token->field("interlocuteur_genre");
			$token->field("interlocuteur_nom");
			$token->field("interlocuteur_prenom");
			$token->field("interlocuteur_adresse");
			$token->field("interlocuteur_codepostal");
			$token->field("interlocuteur_ville");
			$token->field("interlocuteur_pays");
			$token->field("interlocuteur_mel");
			$token->field("interlocuteur_telmobile");
			$token->field("interlocuteur_telephone");
			$token->field("interlocuteur_telecopie");
			$token->field("interlocuteur_commentaire");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="interlocuteur_ajout3">
		<INPUT TYPE="HIDDEN" NAME="interlocuteur_id" VALUE="<? echo $interlocuteur_id; ?>">
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD COLSPAN="2" CLASS="Title">Fonction chez ' <? echo $tiers->fields['intitule']; ?> '</TD>
		</TR>

		<TR>
			<!-- colonne 1 -->
			<TD valign="top" width="50%">
				<TABLE CELLPADDING="2" CELLSPACING="1">
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_FONCTION; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT>
						<SELECT CLASS="Select" NAME="tiersinterlocuteur_fonction">
						<?
						$listenum = business_getlistenum('fonction');
						foreach($listenum as $enum) {
							$sel = ($enum['libelle'] == $tiers_interlocuteur->fields['fonction']) ? 'selected' : '';
							echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
						}
						?>
						</SELECT>
					</TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_EMAIL; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="tiersinterlocuteur_mel" VALUE="<? echo $tiers_interlocuteur->fields['mel']; ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_TEL; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="tiersinterlocuteur_telephone" VALUE="<? echo business_display_tel($tiers_interlocuteur->fields['telephone']); ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_FAX; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="tiersinterlocuteur_telecopie" VALUE="<? echo business_display_tel($tiers_interlocuteur->fields['telecopie']); ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_MOBILE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="tiersinterlocuteur_telmobile" VALUE="<? echo business_display_tel($tiers_interlocuteur->fields['telmobile']); ?>"></TD>
				</TR>

				</TABLE>
			</TD>

			<!-- colonne 2 -->
			<TD valign="top" width="50%">
				<TABLE CELLPADDING="2" CELLSPACING="1">
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_ADRESSE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><TEXTAREA CLASS="Text" ROWS="3" COLS="30" NAME="tiersinterlocuteur_adresse"><? echo $tiers_interlocuteur->fields['adresse']; ?></TEXTAREA></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_CODEPOSTAL; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="5" NAME="tiersinterlocuteur_codepostal" VALUE="<? echo $tiers_interlocuteur->fields['codepostal']; ?>"></TD>
				</TR>

				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_VILLE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="tiersinterlocuteur_ville" VALUE="<? echo $tiers_interlocuteur->fields['ville']; ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_PAYS; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT>
						<SELECT CLASS="Select" NAME="tiersinterlocuteur_pays">
						<?
						$listenum = business_getlistenum('pays');
						foreach($listenum as $enum) {
							$sel = ($enum['libelle'] == $tiers_interlocuteur->fields['pays']) ? 'selected' : '';
							echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
						}
						?>
						</SELECT>
					</TD>
				</TR>
				</TABLE>
			</TD>
		</TR>

		<TR>
			<TD COLSPAN="2" CLASS="Title">Informations personnelles</TD>
		</TR>
		<TR>
			<!-- colonne 1 -->
			<TD valign="top" width="50%">
				<TABLE CELLPADDING="2" CELLSPACING="1">
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_GENRE; ?>:&nbsp;</TD>
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
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_NOM; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_nom" VALUE="<? echo $interlocuteur->fields['nom']; ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_PRENOM; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_prenom" VALUE="<? echo $interlocuteur->fields['prenom']; ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_ADRESSE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><TEXTAREA CLASS="Text" ROWS="3" COLS="30" NAME="interlocuteur_adresse"><? echo $interlocuteur->fields['adresse']; ?></TEXTAREA></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_CODEPOSTAL; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="5" NAME="interlocuteur_codepostal" VALUE="<? echo $interlocuteur->fields['codepostal']; ?>"></TD>
				</TR>

				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_VILLE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_ville" VALUE="<? echo $interlocuteur->fields['ville']; ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_PAYS; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT>
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
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_EMAIL; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="interlocuteur_mel" VALUE="<? echo $interlocuteur->fields['mel']; ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_TEL; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="interlocuteur_telephone" VALUE="<? echo business_display_tel($interlocuteur->fields['telephone']); ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_FAX; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="interlocuteur_telecopie" VALUE="<? echo business_display_tel($interlocuteur->fields['telecopie']); ?>"></TD>
				</TR>
				<TR>
					<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_MOBILE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="interlocuteur_telmobile" VALUE="<? echo business_display_tel($interlocuteur->fields['telmobile']); ?>"></TD>
				</TR>

				<TR>
					<TD ALIGN=RIGHT VALIGN=TOP><? echo _BUSINESS_LABEL_COMMENTAIRE; ?>:&nbsp;</TD>
					<TD ALIGN=LEFT>
					<TEXTAREA CLASS="Text" TYPE="Text" COLS="40" ROWS="4" NAME="interlocuteur_commentaire"><? echo $interlocuteur->fields['commentaire']; ?></TEXTAREA>
					</TD>
				</TR>
				</TABLE>
			</TD>
		</TR>
		</table>

		<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<tr>
			<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>"></TD>
		</tr>
		</TABLE>
		</FORM>
		<?
	break;

}
?>


<? echo $skin->close_simplebloc(); ?>
