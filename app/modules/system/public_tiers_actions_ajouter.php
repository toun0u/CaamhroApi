<?
echo $skin->open_simplebloc("Ajout d'une Action",'100%');

if ($op == 'action_dupliquer')
{
	$action = new action();
	$action->open($action_id);
}
else
{
	$action = new action();
	$action->init_description();
}

?>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" NAME="form">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="">
<TABLE WIDTH="100%" CELLPADDING="4" CELLSPACING="0" BORDER="0" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<TD ALIGN="right">
	<INPUT TYPE="submit" CLASS="FlatButton" VALUE="Retour à la liste des Actions">
	</TD>
</TR>
</TABLE>
</FORM>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR><TD HEIGHT="5"></TD></TR></TABLE>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" NAME="form_action">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "action_enregistrer");
	$token->field("action_datejour");
	$token->field("actionx_heuredeb_h");
	$token->field("actionx_heuredeb_m");
	$token->field("actionx_heurefin_h");
	$token->field("actionx_heurefin_m");
	$token->field("actionx_duree");
	$token->field("action_typeaction");
	$token->field("action_libelle");
	$token->field("actiondetail_dossier_id");
	$token->field("action_description");
	$token->field("action_temps_duplique");
	$token->field("actionutilisateur_id");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="action_enregistrer">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<!-- colonne 1 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_DATEJOUR; ?>:&nbsp;</TD>
			<td align="left">
			<input maxlength="10" name="action_datejour" id="action_datejour" size="20" class="text" value="<? echo date('d/m/Y'); ?>">&nbsp;<a href="#" onclick="javascript:dims_calendar_open('action_datejour', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
			</td>
		</TR>
			<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_HEUREDEB; ?>:&nbsp;</TD>
			<td align="left">
				<select class="select" name="actionx_heuredeb_h">
					<?
					$heure = date('H');
					$minute = date('i');
					$minute = $minute - ($minute%5);
					for ($h=_BUSINESS_HEUREMIN;$h<=_BUSINESS_HEUREMAX;$h++)
					{
						$sel = ($heure==$h) ? 'selected' : '';
						printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
					}
					?>
				</select> h
				<select class="select" name="actionx_heuredeb_m">
					<?
					for ($m=0;$m<12;$m++)
					{
						$sel = ($minute==$m*5) ? 'selected' : '';
						printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*5, $m*5);
					}
					?>
				</select>
			</td>
		</TR>
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_HEUREFIN; ?>:&nbsp;</TD>
			<td align="left">
				<select class="select" name="actionx_heurefin_h">
					<?
					$heure = date('H');
					$minute = date('i');
					$minute = $minute - ($minute%5);
					for ($h=_BUSINESS_HEUREMIN;$h<=_BUSINESS_HEUREMAX;$h++)
					{
						$sel = ($heure==$h) ? 'selected' : '';
						printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
					}
					?>
				</select> h
				<select class="select" name="actionx_heurefin_m">
					<?
					for ($m=0;$m<12;$m++)
					{
						$sel = ($minute==$m*5) ? 'selected' : '';
						printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*5, $m*5);
					}
					?>
				</select>
			</td>
		</TR>

		<TR>
			<TD ALIGN="right">ou Durée:&nbsp;</TD>
			<td align="left">
					<select class="select" name="actionx_duree">
						<option value="">au choix avec l'heure de fin</option>
						<option value="5">5 min</option>
						<option value="10">10 min</option>
						<option value="15">15 min</option>
						<option value="30">30 min</option>
						<option value="45">45 min</option>
						<option value="60">1 h 00</option>
						<option value="90">1 h 30</option>
						<option value="120">2 h 00</option>
						<option value="150">2 h 30</option>
						<option value="180">3 h 00</option>
						<option value="210">3 h 30</option>
						<option value="240">4 h 00</option>
						<option value="300">5 h 00</option>
						<option value="360">6 h 00</option>
						<option value="420">7 h 00</option>
						<option value="480">8 h 00</option>
						<option value="540">9 h 00</option>
						<option value="600">10 h 00</option>
						<option value="660">11 h 00</option>
						<option value="720">12 h 00</option>
					</select>
			</td>
		</TR>
		<tr>
			<td align="right">Type:&nbsp;</td>
			<td align="left">
			<select class="select" name="action_typeaction">
			<?
			$listenum = business_getlistenum('typeaction');
			foreach($listenum as $id_enum => $enum)
			{
				$sel = ($enum['libelle'] == $action->fields['typeaction']) ? 'selected' : '';
				echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
			}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td align="right">Libellé:&nbsp;</td>
			<td align="left">
				<input type="text" class="text" size="30" name="action_libelle" value="<? echo htmlentities($action->fields['libelle']); ?>">
			</td>
		</tr>
		<tr>
			<td align="right" valign="top">Dossiers:&nbsp;<br>Plusieurs choix&nbsp;<br>possibles&nbsp;<br>(CTRL+Click)&nbsp;</td>
			<td align="left">
			<select class="select" name="actiondetail_dossier_id[]" size="5" style="width:250px" multiple id="actiondetail_dossier_id">
			<!--option value="">(aucun)</option>
			<option value="-1">(tous)</option-->
			<?
			$select = 	"
						SELECT 		d.*, i.genre, i.nom, i.prenom
						FROM 		dims_mod_business_dossier d,
									dims_mod_business_tiers_dossier td
						LEFT JOIN	dims_mod_business_interlocuteur i ON td.interlocuteur_id = i.id
						WHERE		td.tiers_id = :idtiers
						AND			td.dossier_id = d.id
						AND			d.termine = 'Non'
						ORDER BY 	date_debut DESC
						";
			$db->query($select, array(
				':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
			));


			while ($fields = $db->fetchrow())
			{
				$sel = (in_array($fields['id'], $action->dossiers)) ? 'selected' : '';
				echo "<option $sel value=\"{$fields['id']}\">{$fields['objet_dossier']} - {$fields['procedure']} ({$fields['genre']} {$fields['nom']} {$fields['prenom']})</option>";
			}
			?>
			</select>
			</td>
		</tr>
		</TABLE>
	</TD>

	<!-- colonne 2 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<tr>
			<td align="right" valign="top">Description:&nbsp;</td>
			<td align="left">
				<textarea name="action_description" rows="14" cols="40" class="text"><? echo htmlentities($action->fields['description']); ?></textarea>
			</td>
		</tr>
		<tr>
			<TD></TD>
			<TD ALIGN="LEFT"><INPUT TYPE="checkbox" NAME="action_temps_duplique" <? if ($action->fields['temps_duplique'] == 'oui') echo 'checked'; ?> VALUE="oui">&nbsp;Dupliquer pour chaque dossier</TD>
		</tr>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<td>Réalisateurs:&nbsp;&nbsp;&nbsp;</td>
	<?
	$select = 	"
				SELECT 	distinct (u.id), u.login
				FROM 	dims_user u,
						dims_group_user gu
				WHERE	u.id = gu.id_user
				";
	$db->query($select);
	while ($fields = $db->fetchrow())
	{
		if ($op == 'action_dupliquer') $sel = (in_array($fields['id'], $action->utilisateurs)) ? 'checked' : '';
		else $sel = ($fields['id'] == $_SESSION['dims']['userid']) ? 'checked' : '';
		echo "<td><input $sel type=\"checkbox\" value=\"{$fields['id']}\" name=\"actionutilisateur_id[]\"></td><td>{$fields['login']}&nbsp;&nbsp;&nbsp;</td>";
	}
	?>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<tr>
	<td ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>" onclick="javascript:if (document.getElementById('actiondetail_dossier_id').selectedIndex < 0) {alert('vous devez choisir au moins un dossier');return(false);}"></TD>
</tr>
</TABLE>
</FORM>

<? echo $skin->close_simplebloc(); ?>
