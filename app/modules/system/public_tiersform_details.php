<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",			"tiers_enregistrer");
	$token->field("tiers_id",	$tiers->fields['id']);
	$token->field("tiers_typeclient");
	$token->field("tiers_ent_statut");
	$token->field("tiers_ent_siren");
	$token->field("tiers_ent_codenace");
	$token->field("tiers_ent_activiteprincipale");
	$token->field("tiers_ent_datecreation");
	$token->field("tiers_ent_capital");
	$token->field("tiers_ent_ca");
	$token->field("tiers_ent_ca_annee");
	$token->field("tiers_ent_effectif");
	$token->field("tiers_ent_effectif_annee");
	$token->field("tiers_ent_hebergee");
	$token->field("tiers_typeclient");
	$token->field("tiers_cre_datenaissance");
	$token->field("tiers_cre_activite");
	$token->field("tiers_cre_issu_recherche");
	$token->field("tiers_cre_statut_createur");
	$token->field("tiers_cre_heb_libreservice");
	$token->field("tiers_cre_heb_bureau");
	$token->field("tiers_cre_heb_pepiniere");
	$token->field("tiers_cre_autresservices");
	$token->field("tiers_typeclient");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_enregistrer">
<INPUT TYPE="HIDDEN" NAME="tiers_id" VALUE="<? echo $tiers->fields['id']; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<?

	switch($tiers->fields['typeclient']) {
		default:
		case _BUSINESS_TYPE_ENTREPRISE:
		?>
		<!-- colonne 1 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<TR>
				<TD ALIGN="right"><? echo _BUSINESS_LABEL_TYPECLIENT; ?>:&nbsp;</TD>
				<TD ALIGN="left">
					<SELECT CLASS="Select" NAME="tiers_typeclient" OnChange="javascript:document.form_tiers.submit()">
					<?

					$listenum = business_getlistenum('typeclient');

					foreach($listenum as $enum) {
						$sel = ($enum['libelle'] == $tiers->fields['typeclient']) ? 'selected' : '';
						echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
					}
					?>
					</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Statut Juridique:&nbsp;</TD>
				<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="tiers_ent_statut">
				<?
				$listenum = business_getlistenum('statutjuridique');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $tiers->fields['ent_statut']) ? 'selected' : '';
					echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP><? echo _BUSINESS_LABEL_ENT_SIREN; ?>:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=20 MAXLENGTH=100 NAME="tiers_ent_siren" VALUE="<? echo $tiers->fields['ent_siren']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP><? echo _BUSINESS_LABEL_CODENACE; ?>:&nbsp;</TD>
				<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="tiers_ent_codenace" STYLE="width:200px">
				<?
				$listenum = business_getlistenum('codenace');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $tiers->fields['ent_codenace']) ? 'selected' : '';
					echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Activité Principale:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_ent_activiteprincipale" VALUE="<? echo $tiers->fields['ent_activiteprincipale']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Date Création:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=15 MAXLENGTH=100 NAME="tiers_ent_datecreation" VALUE="<? echo business_dateus2fr($tiers->fields['ent_datecreation']); ?>"></TD>
			</TR>
			</TABLE>
		</TD>

		<!-- colonne 2 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<TR>
				<TD ALIGN="right" VALIGN=TOP><? echo _BUSINESS_LABEL_ENT_CAPITAL; ?>:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=15 MAXLENGTH=100 NAME="tiers_ent_capital" VALUE="<? echo $tiers->fields['ent_capital']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Chiffre d'Affaire:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=15 MAXLENGTH=100 NAME="tiers_ent_ca" VALUE="<? echo $tiers->fields['ent_ca']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Année Chiffre d'Affaire:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=15 MAXLENGTH=100 NAME="tiers_ent_ca_annee" VALUE="<? echo $tiers->fields['ent_ca_annee']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Effectif:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=15 MAXLENGTH=100 NAME="tiers_ent_effectif" VALUE="<? echo $tiers->fields['ent_effectif']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Année Effectif:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=15 MAXLENGTH=100 NAME="tiers_ent_effectif_annee" VALUE="<? echo $tiers->fields['ent_effectif_annee']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Hébergée:&nbsp;</TD>
				<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="tiers_ent_hebergee" <? if ($tiers->fields['ent_hebergee'] == 'oui') echo 'checked'; ?> VALUE="oui"></TD>
			</TR>
			</TABLE>
		</TD>
		<?
		break;

		case _BUSINESS_TYPE_CREATEUR:
		?>
		<!-- colonne 1 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<TR>
				<TD ALIGN="right"><? echo _BUSINESS_LABEL_TYPECLIENT; ?>:&nbsp;</TD>
				<TD ALIGN="left">
					<SELECT CLASS="Select" NAME="tiers_typeclient" OnChange="javascript:document.form_tiers.submit()">
					<?
					$listenum = business_getlistenum('typeclient');
					foreach($listenum as $enum)
					{
						$sel = ($enum['libelle'] == $tiers->fields['typeclient']) ? 'selected' : '';
						echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
					}
					?>
					</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Date de naissance du créateur:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_cre_datenaissance" VALUE="<? echo business_dateus2fr($tiers->fields['cre_datenaissance']); ?>"></TD>
			</TR>
			<!--TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Université d'Origine:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_cre_origine_univ" VALUE="<? echo $tiers->fields['cre_origine_univ']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Diplôme:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_cre_diplome" VALUE="<? echo $tiers->fields['cre_diplome']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Exp. Professionnelle:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_cre_exper_prof" VALUE="<? echo $tiers->fields['cre_exper_prof']; ?>"></TD>
			</TR-->
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Activité:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_cre_activite" VALUE="<? echo $tiers->fields['cre_activite']; ?>"></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Issu de la Recherche:&nbsp;</TD>
				<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="tiers_cre_issu_recherche" <? if ($tiers->fields['cre_issu_recherche'] == 'oui') echo 'checked'; ?> VALUE="oui"></TD>
			</TR>
			</TABLE>
		</TD>

		<!-- colonne 2 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Statut Créateur:&nbsp;</TD>
				<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="tiers_cre_statut_createur">
				<?
				$listenum = business_getlistenum('statut_createur');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $tiers->fields['cre_statut_createur']) ? 'selected' : '';
					echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Heb. Libre Service:&nbsp;</TD>
				<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="tiers_cre_heb_libreservice" <? if ($tiers->fields['cre_heb_libreservice'] == 'oui') echo 'checked'; ?> VALUE="oui"></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Heb. Bureau:&nbsp;</TD>
				<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="tiers_cre_heb_bureau" <? if ($tiers->fields['cre_heb_bureau'] == 'oui') echo 'checked'; ?> VALUE="oui"></TD>
			</TR>
			<TR>
				<TD ALIGN=RIGHT VALIGN=TOP>Heb. Pépinière:&nbsp;</TD>
				<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="tiers_cre_heb_pepiniere" <? if ($tiers->fields['cre_heb_pepiniere'] == 'oui') echo 'checked'; ?> VALUE="oui"></TD>
			</TR>
			<TR>
				<TD ALIGN="right" VALIGN=TOP>Autres Services:&nbsp;</TD>
				<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_cre_autresservices" VALUE="<? echo $tiers->fields['cre_autresservices']; ?>"></TD>
			</TR>
			</TABLE>
		</TD>
		<?
		break;

		case _BUSINESS_TYPE_PROCEDURE:
		?>
		<!-- colonne 1 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<TR>
				<TD ALIGN="right"><? echo _BUSINESS_LABEL_TYPECLIENT; ?>:&nbsp;</TD>
				<TD ALIGN="left">
					<SELECT CLASS="Select" NAME="tiers_typeclient" OnChange="javascript:document.form_tiers.submit()">
					<?
					$listenum = business_getlistenum('typeclient');
					foreach($listenum as $enum)
					{
						$sel = ($enum['libelle'] == $tiers->fields['typeclient']) ? 'selected' : '';
						echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
					}
					?>
					</SELECT>
				</TD>
			</TR>
			</TABLE>
		</TD>

		<!-- colonne 2 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			</TABLE>
		</TD>
		<?
		break;

		case _BUSINESS_TYPE_LABORATOIRE:
		?>
		<!-- colonne 1 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<TR>
				<TD ALIGN="right"><? echo _BUSINESS_LABEL_TYPECLIENT; ?>:&nbsp;</TD>
				<TD ALIGN="left">
					<SELECT CLASS="Select" NAME="tiers_typeclient" OnChange="javascript:document.form_tiers.submit()">
					<?
					$listenum = business_getlistenum('typeclient');
					foreach($listenum as $enum)
					{
						$sel = ($enum['libelle'] == $tiers->fields['typeclient']) ? 'selected' : '';
						echo "<option $sel alue=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
					}
					?>
					</SELECT>
				</TD>
			</TR>
			</TABLE>
		</TD>

		<!-- colonne 2 -->
		<TD valign="top">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			</TABLE>
		</TD>
		<?
		break;
	}
	?>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<TD COLSPAN="2" ALIGN="right">
		<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>">
	</TD>
</TR>
</TABLE>
</form>