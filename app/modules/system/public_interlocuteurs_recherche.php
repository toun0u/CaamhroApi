<?
if (!isset($interlocuteur_nom)) $interlocuteur_nom = '';
if (!isset($interlocuteur_prenom)) $interlocuteur_prenom = '';
if (!isset($interlocuteur_ville)) $interlocuteur_ville = '';
if (!isset($interlocuteur_tiers)) $interlocuteur_tiers = '';
if (!isset($interlocuteur_datecreation1)) $interlocuteur_datecreation1 = '';
if (!isset($interlocuteur_datecreation2)) $interlocuteur_datecreation2 = '';
if (!isset($interlocuteur_datemaj1)) $interlocuteur_datemaj1 = '';
if (!isset($interlocuteur_datemaj2)) $interlocuteur_datemaj2 = '';
if (!isset($categorie_interlocuteur)) $categorie_interlocuteur = '';
?>

<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("cat",	_BUSINESS_CAT_INTERLOCUTEUR);
	$token->field("op",		"interlocuteurs_recherche");
	$token->field("interlocuteur_nom");
	$token->field("interlocuteur_prenom");
	$token->field("interlocuteur_ville");
	$token->field("interlocuteur_tiers");
	$token->field("categorie_interlocuteur");
	$token->field("interlocuteur_datecreation1");
	$token->field("interlocuteur_datemaj1");
	$token->field("interlocuteur_datecreation2");
	$token->field("interlocuteur_datemaj2");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="cat" VALUE="<? echo _BUSINESS_CAT_INTERLOCUTEUR; ?>">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="interlocuteurs_recherche">
<TABLE CELLPADDING="0" CELLSPACING="0">
<tr>
	<td width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<tr>
			<td align="RIGHT"><? echo _BUSINESS_LABEL_NOM; ?>:&nbsp;</td>
			<td colspan="3"><input type="text" name="interlocuteur_nom" class="text" value="<? echo $interlocuteur_nom; ?>" size="25"></td>
		</tr>
		<tr>
			<td align="RIGHT"><? echo _BUSINESS_LABEL_PRENOM; ?>:&nbsp;</td>
			<td colspan="3"><input type="text" name="interlocuteur_prenom" class="text" value="<? echo $interlocuteur_prenom; ?>" size="25"></td>
		</tr>
		<tr>
			<td align="RIGHT"><? echo _BUSINESS_LABEL_VILLE; ?>:&nbsp;</td>
			<td colspan="3"><input type="text" name="interlocuteur_ville" class="text" value="<? echo $interlocuteur_ville; ?>" size="25"></td>
		</tr>
		<tr>
			<td align="RIGHT">Client:&nbsp;</td>
			<td colspan="3"><input type="text" name="interlocuteur_tiers" class="text" value="<? echo $interlocuteur_tiers; ?>" size="25"></td>
		</tr>
		</table>
	</td>
	<td>
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP">Catégorie (Emailing):&nbsp;</TD>
			<TD ALIGN="LEFT">
				<SELECT CLASS="Select" NAME="categorie_interlocuteur">
				<?
				$listenum = business_getlistenum('categorie_interlocuteur');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == $categorie_interlocuteur) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</tr>
		<tr>
			<td align="RIGHT">Créé entre le:&nbsp;</td>
			<td colspan="3"><input type="text" name="interlocuteur_datecreation1" class="text" value="<? echo $interlocuteur_datecreation1; ?>" size="15">&nbsp;Et le:&nbsp;<input type="text" name="interlocuteur_datecreation2" class="text" value="<? echo $interlocuteur_datecreation2; ?>" size="15"></td>
		</tr>
		<tr>
			<td align="RIGHT">Modifié entre le:&nbsp;</td>
			<td colspan="3"><input type="text" name="interlocuteur_datemaj1" class="text" value="<? echo $interlocuteur_datemaj1; ?>" size="15">&nbsp;Et le:&nbsp;<input type="text" name="interlocuteur_datemaj2" class="text" value="<? echo $interlocuteur_datemaj2; ?>" size="15"></td>
		</tr>
		<tr>
			<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SEARCH; ?>"></TD>
		</tr>
		</TABLE>
	</td>
	</tr>
</table>
</FORM>

<?

// recherche éventuelle des dernieres fiches lues
$select =  "SELECT 		max(al.timestp) as timestp, al.id_action, al.id_record
			FROM		dims_user_action_log as al
			inner join 	dims_mod_business_interlocuteur as t
			on 			t.id=id_record and t.id_module=al.id_module
			AND			al.id_user = :userid
			AND			al.id_module = :moduleid
			AND			al.id_action = :actionid
			GROUP BY 	id_record
			ORDER BY 	timestp DESC
			LIMIT 0,10
			";

$res=$db->query($select, array(
	':userid'	=> $_SESSION['dims']['userid'],
	':moduleid'	=> $_SESSION['dims']['moduleid'],
	':actionid' => _BUSINESS_ACTION_OUVRIRINTERLOCUTEUR
));

while ($fields = $db->fetchrow($res)) {
	$lst_interloc[]=$fields['id_record'];
}


if (!isset($_SESSION['business']['interlocuteur_search'])) $_SESSION['business']['interlocuteur_search'] = '';

//Selection et affichage du tiers sélectionné
if ($op == "interlocuteurs_recherche" || $_SESSION['business']['interlocuteur_search'] || !empty($lst_interloc)) {
	echo "<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\"><tr><td>";

	$export_outlook = "<a href=\"$scriptenv?op=interlocuteurs_export&format=email\">Export Outlook</a>";
	$title2 = "<div style=\"float:right\">$export_outlook - <a href=\"$scriptenv?op=interlocuteurs_export&format=csv\">Export CSV</a> - <a href=\"$scriptenv?op=interlocuteurs_export&format=xls\">Export XLS</a> - <a href=\"$scriptenv?op=interlocuteurs_export&format=xml\">Export XML</a></div>";

	if ($op!="interlocuteurs_recherche")
		echo $skin->open_simplebloc(_BUSINESS_LABEL_LASTSCONSULT, '', '', $title2);
	else
		echo $skin->open_simplebloc(_BUSINESS_LABEL_RESULTAT, '', '', $title2);

	if ($op == 'interlocuteurs_recherche') // construction de la requete
	{
		$where = " WHERE 1 ";
		$params = array();
		if ($interlocuteur_nom != '') {
			$where .= " AND (i.nom LIKE :name OR i.nom_search LIKE :name)";
			$params[':name'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_nom.'%');
		}
		if ($interlocuteur_prenom != '') {
			$where .= " AND (i.prenom LIKE :firstname OR i.prenom_search LIKE :firstname)";
			$params[':frstname'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_prenom.'%');
		}
		if ($interlocuteur_ville != '') {
			$where .= " AND i.ville LIKE :city ";
			$params[':city'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_ville.'%');
		}
		if ($interlocuteur_tiers != '') {
			$where .= " AND (t.intitule LIKE :tiersname OR t.intitule_search LIKE :tiersname) ";
			$params[':tiersname'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_ville.'%');
		}
		if ($interlocuteur_datecreation1 != '') {
			$where .= " AND i.date_creation >= :datecreation1 ";
			$params[':datecreation1'] = array('type' => PDO::PARAM_INT, 'value' => business_datefr2us($interlocuteur_datecreation1));
		}
		if ($interlocuteur_datecreation2 != '') {
			$where .= " AND i.date_creation <= :datecreation2 ";
			$params[':datecreation2'] = array('type' => PDO::PARAM_INT, 'value' => business_datefr2us($interlocuteur_datecreation2));
		}
		if ($interlocuteur_datemaj1 != '') {
			$where .= " AND i.date_maj >= '".business_datefr2us($interlocuteur_datemaj1)."' ";
			$params[':city'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_ville.'%');
		}
		if ($interlocuteur_datemaj2 != '') {
			$where .= " AND i.date_maj <= '".business_datefr2us($interlocuteur_datemaj2)."' ";
			$params[':city'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_ville.'%');
		}
		if ($categorie_interlocuteur != '') {
			$where .= " AND ic.categorie = '".addslashes($categorie_interlocuteur)."'";
			$params[':city'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$interlocuteur_ville.'%');
		}

		$select =  "SELECT 		i.*
					FROM 		dims_mod_business_interlocuteur i
					LEFT JOIN	dims_mod_business_tiers_interlocuteur ti ON i.id = ti.interlocuteur_id
					LEFT JOIN	dims_mod_business_tiers t ON t.id = ti.tiers_id
					LEFT JOIN	dims_mod_business_interlocuteur_categorie ic ON ic.id_interlocuteur = i.id
					$where
					GROUP BY 	i.id
					ORDER BY 	i.nom, i.prenom, i.ville
					";
		$result = $db->query($select, $params);
	} else {
		$select = $_SESSION['business']['interlocuteur_search'];
		$result = $db->query($select);
	}

	$_SESSION['business']['interlocuteur_search'] = $select;

	$opened=false;

	$color = $skin->values['bgline1'];

	?>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_GENRE; ?></TD>
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_NOM; ?></TD>
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_PRENOM; ?></TD>
		<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_TIERS; ?></TD>
		<TD></TD>
	</TR>
	<?
	$array_email = array();
	if ($db->numrows($result)>0 && $db->numrows($result) < _BUSINESS_MAX_RESULT)
	{
		while ($fields = $db->fetchrow($result))
		{
			$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
			$ouvrir = "$scriptenv?op=interlocuteur_ouvrir&interlocuteur_id={$fields['id']}&dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSINFORMATIONS;

			if ($fields['mel'] != '' && !is_null($fields['mel'])) $array_email[$fields['mel']] = $fields['mel'];

			$tiers_list = '';
			$select = 	"
						SELECT 		t.*, ti.telephone as i_telephone, ti.telmobile as i_telmobile, ti.mel as i_mel
						FROM 		dims_mod_business_tiers t,
									dims_mod_business_tiers_interlocuteur ti
						WHERE		ti.tiers_id = t.id
						AND			ti.interlocuteur_id = :interlocuteurid
						ORDER BY 	intitule
						";

			$result_tiers = $db->query($select, array(
				':interlocuteurid' => $fields['id']
			));

			$tiers_list = "<table>";
			while ($fields_tiers = $db->fetchrow($result_tiers))
			{
				if ($fields_tiers['i_mel'] != '' && !is_null($fields_tiers['i_mel'])) $array_email[$fields_tiers['i_mel']] = $fields_tiers['mel'];

				$ouvrir_client = "$scriptenv?cat="._BUSINESS_CAT_TIERS."&op=interlocuteur_ouvrir&tiers_id={$fields_tiers['id']}&interlocuteur_id={$fields['id']}&dims_moduletabid="._BUSINESS_TAB_TIERSINTERLOCUTEURS;

				$tiers_list .= 	"
								<tr bgcolor=\"$color\">
									<td><a href=\"$ouvrir_client\">{$fields_tiers['intitule']}</a></td>
								";
				if ($fields_tiers['telephone']) $tiers_list .= 	"<td>tel:&nbsp;<font color=\""._BUSINESS_COLOR_TIERS."\">".business_display_tel($fields_tiers['telephone'])."</font></td>";
				else $tiers_list .= "<td></td>";
				if ($fields_tiers['telmobile']) $tiers_list .= 	"<td>mob:&nbsp;<font color=\""._BUSINESS_COLOR_TIERS."\">".business_display_tel($fields_tiers['telmobile'])."</font></td>";
				else $tiers_list .= "<td></td>";
				if ($fields_tiers['i_telephone']) $tiers_list .= 	"<td>tel:&nbsp;<font color=\""._BUSINESS_COLOR_INTERLOC."\">".business_display_tel($fields_tiers['i_telephone'])."</font></td>";
				else $tiers_list .= "<td></td>";
				if ($fields_tiers['i_telmobile']) $tiers_list .= 	"<td>mob:&nbsp;<font color=\""._BUSINESS_COLOR_INTERLOC."\">".business_display_tel($fields_tiers['i_telmobile'])."</font></td>";
				else $tiers_list .= "<td></td>";

				$tiers_list .= "</tr>";
			}

			$tiers_list .= "</table>";
			?>
			<TR bgcolor="<? echo $color; ?>">
				<TD><? echo $fields['genre'];?></TD>
				<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['nom'];?></A></TD>
				<TD><A HREF="<? echo $ouvrir; ?>"><? echo $fields['prenom'];?></A></TD>
				<TD><? echo $tiers_list;?></TD>
				<TD ALIGN="CENTER" nowrap>
					<A HREF="<? echo $ouvrir; ?>"><img src="./common/modules/system/img/crayon.gif" border="0" ALT="<? echo _DIMS_MODIFY; ?>"></A>
					&nbsp;&nbsp;&nbsp;<A HREF="javascript:dims_confirmlink('<? echo "$scriptenv?op=interlocuteur_effacer&delete_id={$fields['id']}"; ?>','<? echo str_replace('<VALUE>',addslashes("{$fields['nom']} {$fields['prenom']}"),_BUSINESS_MSG_CONFIRMDELETE); ?>')"><img src="./common/modules/system/img/ico_delete.gif" ALT="<? echo _DIMS_DELETE; ?>" border="0"></A>
				</TD>
			</TR>
			<?
		}
	}
	else
	{
		if ($db->numrows($result)>0) echo '<tr><td colspan="8" align="center">'.$db->numrows($result).' résultats trouvés. Vous devez préciser votre recherche.</td></tr>';
		else echo '<tr><td colspan="8" align="center">Aucun résultat</td></tr>';
	}
	echo "</TABLE>";
	echo $skin->close_simplebloc();
	echo "</td></tr></TABLE>";

}

?>
