<script language="javascript">
function business_validate(form)
{
	if (dims_validatefield("<? echo _BUSINESS_LABEL_INTITULE_DOSSIER; ?>",form.dossier_intitule,"string"))
	if (dims_validatefield("<? echo _BUSINESS_LABEL_DATE_DEBUT_DOSSIER; ?>",form.dossier_date_debut,"emptydate"))
	if (dims_validatefield("<? echo _BUSINESS_LABEL_DATE_FIN_DOSSIER; ?>",form.dossier_date_fin,"emptydate"))
		return true;

	return false;
}
</script>

<?
$dossier = new dossier();
$dossier->open($dossier_id);

$tiers_dossier = new tiers_dossier();
$tiers_dossier->open($_SESSION['business']['tiers_id'],$dossier_id);

/*
dims_print_r($dossier);
dims_print_r($tiers_dossier);
*/

$title = "Dossier : {$dossier->fields['objet_dossier']}";
echo $skin->open_simplebloc($title,'border:2px solid '._BUSINESS_COLOR_DOSSIER.';','color:'._BUSINESS_COLOR_SEL.';background-color:'._BUSINESS_COLOR_DOSSIER.';');

if ($dossier->fields['termine'] == 'Oui')
{
	$avancement =	"
					<table cellpadding=\"0\" cellspacing=\"1\" width=\"100\" height=\"14\" bgcolor=\"#000000\">
					<tr>
						<td bgcolor=\"#FF0000\"><font style=\"font-size:9px;color:#000000\">&nbsp;terminé&nbsp;</font></td>
					</tr>
					</table>
					";
}
else
{
	$avancement = business_display_avancement($dossier->fields['date_debut'],$dossier->fields['date_fin']);
}
$temps_ecoule = business_display_temps_ecoule($dossier->fields['duree'],$dossier->get_totaltime());

?>

<TABLE WIDTH="100%" CELLPADDING="4" CELLSPACING="0" BORDER="0" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<TD ALIGN="right">
	<INPUT TYPE="button" onclick="javascript:document.location.href='<? echo "$scriptenv?dims_moduletabid="._BUSINESS_TAB_TIERSDOSSIERS; ?>'" CLASS="FlatButton" VALUE="Retour à la liste des Dossiers">&nbsp;
	<INPUT TYPE="button" onclick="javascript:document.location.href='<? echo "$scriptenv?op=dossier_ajouter"; ?>'"CLASS="FlatButton" VALUE="Ajouter un Dossier">
	</TD>
</TR>
</TABLE>
<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR><TD HEIGHT="5"></TD></TR></TABLE>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" OnSubmit="javascript:return business_validate(this)" name="form_dossier">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op"			,"dossier_enregistrer");
	$token->field("dossier_id"	,$dossier_id);
	$token->field("dossier_domaine_intervention");
	$token->field("dossier_objet_dossier");
	$token->field("dossier_procedure");
	$token->field("dossier_termine");
	$token->field("tiersdossier_interlocuteur_id");
	$token->field("dossier_date_debut");
	$token->field("dossier_date_fin");
	$token->field("dossier_duree");
	$token->field("dossier_commentaire");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="dossier_enregistrer">
<INPUT TYPE="HIDDEN" NAME="dossier_id" VALUE="<? echo $dossier_id; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<!-- colonne 1 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_DOMAINE_INTERVENTION; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<SELECT CLASS="Select" NAME="dossier_domaine_intervention">
				<?
				$listenum = business_getlistenum('domaine_intervention');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $dossier->fields['domaine_intervention']) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
			</TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_NATURE_ACTIVITE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="30" NAME="dossier_objet_dossier" VALUE="<? echo $dossier->fields['objet_dossier']; ?>"></TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_PROCEDURE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<SELECT CLASS="Select" NAME="dossier_procedure">
				<?
				$listenum = business_getlistenum('procedure');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $dossier->fields['procedure']) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT VALIGN=TOP><? echo _BUSINESS_LABEL_TERMINE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT TYPE="checkbox" NAME="dossier_termine" <? if ($dossier->fields['termine'] == 'Oui' || $dossier->fields['termine'] == 'oui') echo 'checked'; ?> VALUE="Oui"></TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_INTERLOCUTEUR; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<SELECT CLASS="Select" NAME="tiersdossier_interlocuteur_id">
				<OPTION></OPTION>
				<?
				// recherche des interlocuteurs du tiers
				$select = 	"
							SELECT 	i.id, i.genre, i.nom, i.prenom, ti.fonction, ti.service, ti.telephone, ti.telmobile
							FROM	dims_mod_business_tiers_interlocuteur ti,
									dims_mod_business_interlocuteur i
							WHERE	ti.interlocuteur_id = i.id
							AND		ti.tiers_id = :idtier
							";

				$db->query($select, array(
					':idtier' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
				));

				while($fields = $db->fetchrow())
				{
					$sel = ($tiers_dossier->fields['interlocuteur_id']==$fields['id']) ? 'selected' : '';
					echo "<option $sel value=\"{$fields['id']}\">{$fields['nom']} {$fields['prenom']}</option>";
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
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_DATE_DEBUT_DOSSIER; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="dossier_date_debut" id="dossier_date_debut" VALUE="<? echo business_dateus2fr($dossier->fields['date_debut']); ?>">&nbsp;<a href="#" onclick="javascript:dims_calendar_open('dossier_date_debut', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_DATE_FIN_DOSSIER; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="15" NAME="dossier_date_fin" id="dossier_date_fin" VALUE="<? echo business_dateus2fr($dossier->fields['date_fin']); ?>">&nbsp;<a href="#" onclick="javascript:dims_calendar_open('dossier_date_fin', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_AVANCEMENT_DOSSIER; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><? echo $avancement; ?></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_DUREE_IMPARTIE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE="10" NAME="dossier_duree" VALUE="<? echo $dossier->fields['duree']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_TEMPS_ECOULE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><? echo $temps_ecoule; ?></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN=RIGHT VALIGN=TOP><? echo _BUSINESS_LABEL_COMMENTAIRE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<TEXTAREA CLASS="Text" COLS="89" ROWS="2" NAME="dossier_commentaire"><? echo $dossier->fields['commentaire']; ?></TEXTAREA>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<tr>
	<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>"></TD>
</tr>
</TABLE>
</FORM>

<? echo $skin->close_simplebloc(); ?>
