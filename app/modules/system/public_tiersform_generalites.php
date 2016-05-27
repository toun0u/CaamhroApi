<script language="javascript">
function business_validate(form)
{
	if (dims_validatefield("<? echo _BUSINESS_LABEL_INTITULE; ?>",form.tiers_intitule,"string"))
		return true;

	return false;
}

<?
$select = 	"
			SELECT 	enum1.*, enum2.libelle as libelle2
			FROM 	dims_mod_business_enum enum1
			LEFT JOIN dims_mod_business_enum enum2 ON enum2.id = enum1.id_enum
			WHERE 	enum1.type = 'origine_contact_detail'
			ORDER BY enum1.libelle";
$res=$db->query($select);
echo "var enums = new Array(". $db->numrows() .");";

$c=0;
while ($fields = $db->fetchrow($res)) {
	echo "enums[{$c}] = new Array(2);\n";
	echo "enums[{$c}]['libelle'] = '".addslashes($fields['libelle'])."';\n";
	echo "enums[{$c}]['parent'] = '".addslashes($fields['libelle2'])."';\n";
	$c++;
}
?>

function refresh_originecontact(parent, detail, sel) {
	detail.length = 0; // vide la liste
	detail.options[0] = new Option('', '');
	for (i=0;i<enums.length;i++)
	{
		if (enums[i]['parent'] == parent)
		{
			detail.options[detail.length] = new Option(enums[i]['libelle'], enums[i]['libelle']);
			if (sel == enums[i]['libelle']) detail.selectedIndex = detail.length-1;
		}
	}
}
</script>

<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST" OnSubmit="javascript:return business_validate(this)">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",			"tiers_enregistrer");
	$token->field("tiers_id",	$tiers->fields['id']);
	$token->field("tiers_intitule");
	$token->field("tiers_abrege");
	$token->field("tiers_typeclient");
	$token->field("tiers_telephone");
	$token->field("tiers_telecopie");
	$token->field("tiers_telmobile");
	$token->field("tiers_actif");
	$token->field("tiers_adresse");
	$token->field("tiers_codepostal");
	$token->field("tiers_ville");
	$token->field("tiers_pays");
	$token->field("tiers_mel");
	$token->field("tiers_internet");
	$token->field("tiers_origine_contact");
	$token->field("tiers_origine_contact_detail");
	$token->field("tiers_commentaire");
	$token->field("tiers_motscles");
	$token->field("tiers_id_user");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_enregistrer">
<INPUT TYPE="HIDDEN" NAME="tiers_id" VALUE="<? echo $tiers->fields['id']; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<!-- colonne 1 -->
	<TD valign="top">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_REF; ?>:&nbsp;</TD>
			<TD ALIGN="left"><? echo business_format_ref($tiers->fields['id']); ?></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_INTITULE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="tiers_intitule" VALUE="<? echo $tiers->fields['intitule']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right">&nbsp;&nbsp;<? echo _BUSINESS_LABEL_ABREGE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="tiers_abrege" VALUE="<? echo $tiers->fields['abrege']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_TYPECLIENT; ?>:&nbsp;</TD>
			<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="tiers_typeclient">
				<?
				$listenum = business_getlistenum('typeclient');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $tiers->fields['typeclient']) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_TEL; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="tiers_telephone" VALUE="<? echo business_display_tel($tiers->fields['telephone']); ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_FAX; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="tiers_telecopie" VALUE="<? echo business_display_tel($tiers->fields['telecopie']); ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_MOBILE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="tiers_telmobile" VALUE="<? echo business_display_tel($tiers->fields['telmobile']); ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT VALIGN=TOP><? echo _BUSINESS_LABEL_ACTIF; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="tiers_actif" <? if ($tiers->fields['actif'] == 'Oui' || $tiers->fields['actif'] == 'oui') echo 'checked'; ?> VALUE="Oui"></TD>
		</TR>


	</TABLE>
	</TD>

	<!-- colonne 2 -->
	<TD valign="top">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right" VALIGN="top"><? echo _BUSINESS_LABEL_ADRESSE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><TEXTAREA CLASS="Text" ROWS="2" COLS="30" NAME="tiers_adresse"><? echo $tiers->fields['adresse']; ?></TEXTAREA></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_CODEPOSTAL; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="5" NAME="tiers_codepostal" VALUE="<? echo $tiers->fields['codepostal']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_VILLE; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="tiers_ville" VALUE="<? echo $tiers->fields['ville']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_PAYS; ?>:&nbsp;</TD>
			<TD ALIGN="left">
			<SELECT CLASS="Select" NAME="tiers_pays">
			<?
			$listenum = business_getlistenum('pays');
			foreach($listenum as $enum)
			{
				$sel = ($enum['libelle'] == $tiers->fields['pays']) ? 'selected' : '';
				echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
			}
			?>
			</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_EMAIL; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_mel" VALUE="<? echo $tiers->fields['mel']; ?>"><? if ($tiers->fields['mel']) echo "&nbsp;&nbsp;<A HREF=\"mailto:{$tiers->fields['mel']}\" TARGET=\"_blank\"><IMG BORDER=\"0\" ALT=\"Envoyer un Mel\" SRC=\"./common/modules/business/img/ico_email.gif\"></A>"?></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_INTERNET; ?>:&nbsp;</TD>
			<TD ALIGN="left"><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="tiers_internet" VALUE="<? echo $tiers->fields['internet']; ?>"><? if ($tiers->fields['internet']) echo "&nbsp;&nbsp;<A HREF=\"{$tiers->fields['internet']}\" TARGET=\"_blank\"><IMG BORDER=\"0\" ALT=\"Visiter le site Internet\" SRC=\"./common/modules/business/img/ico_web.gif\"></A>"?></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_ORIGINE; ?>:&nbsp;</TD>
			<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="tiers_origine_contact" onchange="javascript:refresh_originecontact(this.value, document.form_tiers.tiers_origine_contact_detail,'')">
				<?
				$listenum = business_getlistenum('origine_contact');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $tiers->fields['origine_contact']) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="right"></TD>
			<TD ALIGN="left">
				<SELECT CLASS="Select" NAME="tiers_origine_contact_detail">
				</SELECT>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" ALIGN="left">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN=RIGHT VALIGN=TOP><? echo _BUSINESS_LABEL_COMMENTAIRE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
			<TEXTAREA CLASS="Text" TYPE="Text" COLS="80" ROWS="2" NAME="tiers_commentaire"><? echo $tiers->fields['commentaire']; ?></TEXTAREA>
			</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT VALIGN=TOP><? echo _BUSINESS_LABEL_MOTSCLES; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
			<TEXTAREA CLASS="Text" TYPE="Text" COLS="80" ROWS="2" NAME="tiers_motscles"><? echo $tiers->fields['motscles']; ?></TEXTAREA>
			</TD>
		</TR>
		</TABLE>
	</TD>
	<TD COLSPAN="2" ALIGN="left">
	</TD>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<TD ALIGN="left" VALIGN="middle">
		<?
		$resp = '</b>&nbsp;-&nbsp;Responsable&nbsp;:&nbsp;<select class="select" name="tiers_id_user"><option value="0"></option>';

		$select = 	"
					SELECT 	distinct (u.id), u.login, u.firstname, u.lastname
					FROM 	dims_user u,
							dims_group_user gu
					WHERE	u.id = gu.id_user
					";
		$res=$db->query($select);
		while ($fields = $db->fetchrow($res)) {
			$sel = ($tiers->fields['id_user'] == $fields['id']) ? 'selected' : '';
			$resp .= "<option $sel value=\"{$fields['id']}\">{$fields['firstname']} {$fields['lastname']} ({$fields['login']})</option>";
		}
		$resp .= '</select>&nbsp;';

		?>
		Fiche créée le <b><? echo business_dateus2fr($tiers->fields['date_creation']); ?></b> - Dernière modification le <b><? echo business_dateus2fr($tiers->fields['date_maj']); ?><? echo $resp ?></b><? if ($tiers->fields['resp'] != '') echo " - Resp. ORION : {$tiers->fields['resp']}"; ?>
	</TD>
	<TD ALIGN="right" VALIGN="middle">
		<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>">
	</TD>
</TR>
</TABLE>
</form>

<script language="javascript">
	refresh_originecontact('<? echo addslashes($tiers->fields['origine_contact']); ?>', document.form_tiers.tiers_origine_contact_detail, '<? echo addslashes($tiers->fields['origine_contact_detail']); ?>');
</script>