<?
if (!isset($tiers_typeclient)) $tiers_typeclient = '';
if (!isset($tiers_intitule)) $tiers_intitule = '';
if (!isset($tiers_ville)) $tiers_ville = '';
if (!isset($tiers_departement)) $tiers_departement = '';
if (!isset($tiers_interlocuteur)) $tiers_interlocuteur = '';
if (!isset($tiers_dossier)) $tiers_dossier = '';
if (!isset($tiers_motscles)) $tiers_motscles = '';
if (!isset($tiers_id_user)) $tiers_id_user = '';
if (!isset($tiers_datecreation1)) $tiers_datecreation1 = '';
if (!isset($tiers_datecreation2)) $tiers_datecreation2 = '';
if (!isset($tiers_datemaj1)) $tiers_datemaj1 = '';
if (!isset($tiers_datemaj2)) $tiers_datemaj2 = '';
if (!isset($tiers_actif)) $tiers_actif = 'Oui';

if (!isset($tiers_datemaj2)) $tiers_datemaj2 = '';

if (!isset($tiers_origine_contact)) $tiers_origine_contact = '';
if (!isset($tiers_origine_contact_detail)) $tiers_origine_contact_detail = '';
?>

<script language="javascript">
<?
$select = 	"
			SELECT 	enum1.*, enum2.libelle as libelle2
			FROM 	dims_mod_business_enum enum1
			LEFT JOIN dims_mod_business_enum enum2 ON enum2.id = enum1.id_enum
			WHERE 	enum1.type = 'origine_contact_detail'
			ORDER BY enum1.libelle";
$db->query($select);
echo "var enums = new Array(". $db->numrows() .");";

$c=0;
while ($fields = $db->fetchrow())
{
	echo "enums[{$c}] = new Array(2);\n";
	echo "enums[{$c}]['libelle'] = '".addslashes($fields['libelle'])."';\n";
	echo "enums[{$c}]['parent'] = '".addslashes($fields['libelle2'])."';\n";
	$c++;
}
?>

function refresh_originecontact(parent, detail, sel)
{
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

<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("cat",	_BUSINESS_CAT_TIERS);
	$token->field("op",		"tiers_recherche");
	$token->field("tiers_typeclient");
	$token->field("tiers_intitule");
	$token->field("tiers_ville");
	$token->field("tiers_departement");
	$token->field("tiers_interlocuteur");
	$token->field("tiers_dossier");
	$token->field("tiers_motscles");
	$token->field("tiers_datecreation1");
	$token->field("tiers_datecreation2");
	$token->field("tiers_datemaj1");
	$token->field("tiers_datemaj2");
	$token->field("tiers_origine_contact");
	$token->field("tiers_origine_contact_detail");
	$token->field("tiers_actif");
	$token->field("tiers_id_user");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="cat" VALUE="<? echo _BUSINESS_CAT_TIERS; ?>">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_recherche">
<div style="float:left;display:block;width:40%;padding-left:50px;">
	<TABLE CELLPADDING="2" CELLSPACING="1">
	<TR>
		<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_TYPECLIENT ?>:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<SELECT CLASS="Select" NAME="tiers_typeclient">
			<option value="">(tous)</option>
			<?

				$select =	"
							SELECT 		dims_mod_business_enum.*
							FROM 		dims_mod_business_enum
							WHERE type='typeclient' ORDER BY LIBELLE
							";

				$result = $db->query($select);
				while ($fields = $db->fetchrow($result))
				{
					$sel = ($fields['libelle'] == $tiers_typeclient) ? 'selected' : '';
					echo "<OPTION $sel VALUE=\"{$fields['libelle']}\">{$fields['libelle']}</OPTION>";
				}
			?>
		</TD>
	</TR>

	<tr>
		<td align="RIGHT"><? echo _BUSINESS_LABEL_INTITULE; ?>:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_intitule" class="text" value="<? echo $tiers_intitule; ?>" size="25"></td>
	</tr>
	<tr>
		<td align="RIGHT"><? echo _BUSINESS_LABEL_VILLE; ?>:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_ville" class="text" value="<? echo $tiers_ville; ?>" size="25"></td>
	</tr>
	<tr>
		<td align="RIGHT"><? echo _BUSINESS_LABEL_DEPARTEMENT; ?>:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_departement" class="text" value="<? echo $tiers_departement; ?>" size="5"></td>
	</tr>
	<tr>
		<td align="RIGHT"><? echo _BUSINESS_LABEL_INTERLOCUTEUR; ?>:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_interlocuteur" class="text" value="<? echo $tiers_interlocuteur; ?>" size="25"></td>
	</tr>
	<tr>
		<td align="RIGHT"><? echo _BUSINESS_LABEL_DOSSIER; ?>:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_dossier" class="text" value="<? echo $tiers_dossier; ?>" size="25"></td>
	</tr>
	<tr>
		<td align="RIGHT"><? echo _BUSINESS_LABEL_KEYWORDS; ?>:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_motscles" class="text" value="<? echo $tiers_motscles; ?>" size="25"></td>
	</tr>
	</table>
</div>
<div style="float:left;display:block;width:40%;">
	<TABLE CELLPADDING="2" CELLSPACING="1">
	<tr>
		<td align="RIGHT">Créé entre le:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_datecreation1" class="text" value="<? echo $tiers_datecreation1; ?>" size="15">&nbsp;Et le:&nbsp;<input type="text" name="tiers_datecreation2" class="text" value="<? echo $tiers_datecreation2; ?>" size="15"></td>
	</tr>
	<tr>
		<td align="RIGHT">Modifié entre le:&nbsp;</td>
		<td colspan="3"><input type="text" name="tiers_datemaj1" class="text" value="<? echo $tiers_datemaj1; ?>" size="15">&nbsp;Et le:&nbsp;<input type="text" name="tiers_datemaj2" class="text" value="<? echo $tiers_datemaj2; ?>" size="15"></td>
	</tr>

	<TR>
		<TD ALIGN="right"><? echo _BUSINESS_LABEL_ORIGINE; ?>:&nbsp;</TD>
		<TD ALIGN="left" colspan="3">
			<SELECT CLASS="Select" NAME="tiers_origine_contact" onchange="javascript:refresh_originecontact(this.value, document.form_tiers.tiers_origine_contact_detail,'')">
			<?
			$listenum = business_getlistenum('origine_contact');
			foreach($listenum as $enum)
			{
				$sel = ($enum['libelle'] == $tiers_origine_contact) ? 'selected' : '';
				echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
			}
			?>
			</SELECT>
			<SELECT CLASS="Select" NAME="tiers_origine_contact_detail">
			</SELECT>
		</TD>
	</TR>

	<TR>
		<TD ALIGN=RIGHT>Client Actif:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<SELECT CLASS="Select" NAME="tiers_actif">
			<option value="">(tous)</option>
			<option value="Oui" <? if ($tiers_actif == 'Oui') echo 'selected'; ?>>Oui</option>
			<option value="Non" <? if ($tiers_actif == 'Non') echo 'selected'; ?>>Non</option>
			</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT>Responsable:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<SELECT CLASS="Select" NAME="tiers_id_user">
			<option value="">(tous)</option>
			<?

				$select =	"
							SELECT 		*
							FROM 		dims_user
							";

				$result = $db->query($select);
				while ($fields = $db->fetchrow($result))
				{
					$sel = ($fields['id'] == $tiers_id_user) ? 'selected' : '';
					echo "<OPTION $sel VALUE=\"{$fields['id']}\">{$fields['firstname']} {$fields['lastname']} ({$fields['login']})</OPTION>";
				}
			?>
		</TD>
	</TR>
	<tr>
		<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SEARCH; ?>"></TD>
	</tr>
	</TABLE>
</div>
</FORM>

<script language="javascript">
	refresh_originecontact('<? echo addslashes($tiers_origine_contact); ?>', document.form_tiers.tiers_origine_contact_detail, '<? echo addslashes($tiers_origine_contact_detail); ?>');
</script>

<?
// recherche éventuelle des dernieres fiches lues
$select = 	"
			SELECT 		max(al.timestp) as timestp, al.id_action, al.id_record
			FROM		dims_user_action_log as al
			inner join 	dims_mod_business_tiers as t
			on 			t.id=id_record and t.id_module=al.id_module
			AND			al.id_user = :iduser
			AND			al.id_module = :idmodule
			AND			al.id_action = :idaction
			GROUP BY 	id_record
			ORDER BY 	timestp DESC
			LIMIT 0,10
			";

$res=$db->query($select, array(
	':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
	':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']),
	':idaction' => array('type' => PDO::PARAM_INT, 'value' => _BUSINESS_ACTION_OUVRIRTIERS),
));

while ($fields = $db->fetchrow($res)) {
	$lst_tiers[]=$fields['id_record'];
}


if (!isset($_SESSION['business']['tiers_search'])) $_SESSION['business']['tiers_search'] = '';

//Selection et affichage du tiers sélectionné
if ($op == "tiers_recherche" || $_SESSION['business']['tiers_search'] || !empty($lst_tiers))
{

	echo 	"<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\"><tr><td>";

	$export_outlook = "<a href=\"$scriptenv?op=tiers_export&format=email\">Export Outlook</a>";
	$title2 = "<div style=\"float:right\">$export_outlook - <a href=\"$scriptenv?op=tiers_export&format=csv\">Export CSV</a> - <a href=\"$scriptenv?op=tiers_export&format=xls\">Export XLS</a> - <a href=\"$scriptenv?op=tiers_export&format=xml\">Export XML</a></div>";

	if ($op!="tiers_recherche")
		echo $skin->open_simplebloc(_BUSINESS_LABEL_LASTSCONSULT, '', '', $title2);
	else
		echo $skin->open_simplebloc(_BUSINESS_LABEL_RESULTAT, '', '', $title2);

	if ($op == 'tiers_recherche') {// construction de la requete
		$where = " WHERE 1 ";
		$from = "";
		$params = array();

		if ($tiers_intitule != '') {
			$where .= " AND (t.intitule LIKE :tiersintitule OR t.intitule_search LIKE :tiersintitule OR t.abrege LIKE :tiersintitule)";
			$params[':tiersintitule'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$tiers_intitule.'%');
		}
		if ($tiers_typeclient != '') {
			$where .= " AND t.typeclient = :typeclient ";
			$params[':typeclient'] = array('type' => PDO::PARAM_INT, 'value' => $tiers_typeclient);
		}
		if ($tiers_ville != '') {
			$where .= " AND t.ville LIKE :ville ";
			$params[':ville'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$tiers_ville.'%');
		}
		if ($tiers_departement != '') {
			$where .= " AND LEFT(t.codepostal,2) = :department ";
			$params[':department'] = array('type' => PDO::PARAM_INT, 'value' => $tiers_departement);
		}
		if ($tiers_id_user != '') {
			$where .= " AND t.id_user = :iduser ";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $tiers_id_user);
		}
		if ($tiers_datecreation1 != '') {
			$where .= " AND t.date_creation >= :datecreate1 ";
			$params[':datecreate1'] = array('type' => PDO::PARAM_INT, 'value' => business_datefr2us($tiers_datecreation1));
		}
		if ($tiers_datecreation2 != '') {
			$where .= " AND t.date_creation >= :datecreate2 ";
			$params[':datecreate2'] = array('type' => PDO::PARAM_INT, 'value' => business_datefr2us($tiers_datecreation2));
		}
		if ($tiers_datemaj1 != '') {
			$where .= " AND t.date_maj >= :dateupdate1 ";
			$params[':dateupdate1'] = array('type' => PDO::PARAM_INT, 'value' => business_datefr2us($tiers_datemaj1));
		}
		if ($tiers_datemaj2 != '') {
			$where .= " AND t.date_maj >= :dateupdate2 ";
			$params[':dateupdate2'] = array('type' => PDO::PARAM_INT, 'value' => business_datefr2us($tiers_datemaj2));
		}
		if ($tiers_origine_contact != '') {
			$where .= " AND t.origine_contact = :origincontact ";
			$params[':origincontact'] = array('type' => PDO::PARAM_INT, 'value' => $tiers_origine_contact);
		}
		if ($tiers_origine_contact_detail != '') {
			$where .= " AND t.origine_contact_detail = :origincontactdetail ";
			$params[':origincontactdetail'] = array('type' => PDO::PARAM_INT, 'value' => $tiers_origine_contact_detail);
		}
		if ($tiers_actif != '') {
			if ($tiers_actif == 'Oui') $where .= " AND t.actif = 'Oui' ";
			else $where .= " AND t.actif <> 'Oui' ";
		}

		if ($tiers_motscles != '') {
			$where .= " AND (t.motscles LIKE '%".addslashes($tiers_motscles)."%' OR t.motscles_search LIKE '%".addslashes(business_format_search($tiers_motscles))."%')";
			$params[':keywords'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$tiers_motscles.'%');
			$params[':keywordssearc'] = array('type' => PDO::PARAM_STR, 'value' => '%'.business_format_search($tiers_motscles).'%');
		}

		if ($tiers_interlocuteur != '') {
			$from .= ", dims_mod_business_tiers_interlocuteur ti, dims_mod_business_interlocuteur i";
			$where .= " AND ti.tiers_id = t.id AND i.id = ti.interlocuteur_id AND (i.nom LIKE :interlocutor OR i.nom_search LIKE :interlocutor OR i.prenom LIKE :interlocutor OR i.prenom_search LIKE :interlocutor) ";
			$params[':interlocutor'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$tiers_interlocuteur.'%');
		}
		if ($tiers_dossier != '') {
			$from .= ", dims_mod_business_tiers_dossier td, dims_mod_business_dossier d";
			$where .= " AND td.tiers_id = t.id AND d.id = td.dossier_id AND (d.objet_dossier LIKE :tiersdossier OR d.objet_dossier_search LIKE :tiersdossier) ";
			$params[':tiersdossier'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$tiers_dossier.'%');
		}


		$select = 	"
					SELECT 		t.*, u.login
					FROM 		dims_mod_business_tiers t $from
					LEFT JOIN	dims_user u ON t.id_user = u.id
					$where
					GROUP BY 	intitule
					ORDER BY 	intitule, typeclient, ville
					";

	}
	elseif(!empty($lst_tiers)) {
		$params = array();
		$select = 	"
					SELECT 		t.*, u.login
					FROM 		dims_mod_business_tiers t $from
					LEFT JOIN	dims_user u ON t.id_user = u.id
					WHERE		t.id in (".$db->getParamsFromArray($lst_tiers, 'idtier', $params).")
					GROUP BY 	intitule
					";
	}
	else $select = $_SESSION['business']['tiers_search'];

	$result = $db->query($select, $params);
	$_SESSION['business']['tiers_search'] = $select;
	$_SESSION['business']['tiers_searchparams'] = $params;

	$opened=false;

	$color = $skin->values['bgline1'];

	?>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_INTITULE; ?></TD>
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_VILLE; ?></TD>
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TYPECLIENT; ?></TD>
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TEL; ?></TD>
		<TD ALIGN="LEFT">Resp.</TD>
		<TD></TD>
	</TR>
	<?
	$array_email = array();
	if ($db->numrows($result)>0 && $db->numrows($result) < _BUSINESS_MAX_RESULT)
	{
		while ($fields = $db->fetchrow($result))
		{
			if ($fields['mel'] != '' && !is_null($fields['mel'])) $array_email[$fields['mel']] = $fields['mel'];

			$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
			$ouvrir = "$scriptenv?op=tiers_ouvrir&tiers_id={$fields['id']}&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS;
			?>
			<TR bgcolor="<? echo $color; ?>">
				<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['intitule'];?></A></TD>
				<TD><? echo $fields['ville'];?></TD>
				<TD><? echo $fields['typeclient'];?></TD>
				<TD><? echo business_display_tel($fields['telephone']);?></TD>
				<TD><? echo $fields['login'];?></TD>
				<TD ALIGN="CENTER" nowrap>
					<A HREF="<? echo $ouvrir; ?>"><img src="./common/modules/system/img/crayon.gif" border="0" ALT="<? echo _DIMS_MODIFY; ?>"></A>
					&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo "$scriptenv?op=tiers_effacer&delete_id={$fields['id']}"; ?>','<? echo str_replace('<VALUE>',addslashes($fields['intitule']),_BUSINESS_MSG_CONFIRMDELETE); ?>')"><img src="./common/modules/system/img/ico_delete.gif" ALT="<? echo _DIMS_DELETE; ?>" border="0"></A>
				</TD>
			</TR>
			<?
		}
	}
	else
	{
		if ($db->numrows($result)>0) echo '<tr><td colspan="5" align="center">'.$db->numrows($result).' résultats trouvés. Vous devez préciser votre recherche.</td></tr>';
		else echo '<tr><td colspan="5" align="center">Aucun résultat</td></tr>';
	}
	echo "</TABLE>";
	echo $skin->close_simplebloc();
	echo "</td></tr></TABLE>";

}


?>
