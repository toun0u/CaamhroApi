<?
echo $skin->open_simplebloc("Modification d'une Action",'100%');

$action = new action();
$action->open($action_id);

$next = $prev = '';
if (isset($_SESSION['business']['tiers']['actions']) && sizeof($_SESSION['business']['tiers']['actions']))
{
	array_set_current($_SESSION['business']['tiers']['actions'],$action_id);
	$prev = next($_SESSION['business']['tiers']['actions']);
	array_set_current($_SESSION['business']['tiers']['actions'],$action_id);
	$next = prev($_SESSION['business']['tiers']['actions']);
}
?>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
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
	<td>&nbsp;</td>
	<?
	if ($prev != '')
	{
		?>
		<td width="60" width="center">
			<input type="button" class="FlatButton" VALUE="«  Préc." onclick="document.location.href='index.php?op=action_modifier&action_id=<? echo $prev; ?>'">
		</td>
		<?
	}
	else echo '<td width="60">&nbsp;</td>';

	if ($next != '')
	{
		?>
		<td width="60" width="center">
			<input type="button" class="FlatButton" VALUE="Suiv.  »" onclick="document.location.href='index.php?op=action_modifier&action_id=<? echo $next; ?>'">
		</td>
		<?
	}
	else echo '<td width="60">&nbsp;</td>';
	?>
	<TD ALIGN="right" width="100">
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
	$token->field("op",			"action_enregistrer");
	$token->field("action_id",	$action->fields['id']);
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
<INPUT TYPE="HIDDEN" NAME="action_id" VALUE="<? echo $action->fields['id']; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<!-- colonne 1 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_DATEJOUR; ?>:&nbsp;</TD>
			<td align="left">
				<input maxlength="10" name="action_datejour" id="action_datejour" size="20" class="text" value="<? echo business_dateus2fr($action->fields['datejour']); ?>">&nbsp;<a href="#" onclick="javascript:dims_calendar_open('action_datejour', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
			</td>
		</TR>
			<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_HEUREDEB; ?>:&nbsp;</TD>
			<td align="left">
				<select class="select" name="actionx_heuredeb_h">
					<?
					$heure_split = split(':',$action->fields['heuredeb']);
					$heure = $heure_split[0];
					$minute = $heure_split[1];
					$minute = $minute - ($minute%5);
					for ($h=7;$h<=20;$h++)
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
					$heure_split = split(':',$action->fields['heurefin']);
					$heure = $heure_split[0];
					$minute = $heure_split[1];
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
				<?
				$array_heure = array(
									'5' => '5 min',
									'10' => '10 min',
									'15' => '15 min',
									'30' => '30 min',
									'45' => '45 min',
									'60' => '1 h 00',
									'90' => '1 h 30',
									'120' => '2 h 00',
									'150' => '2 h 30',
									'180' => '3 h 00',
									'210' => '3 h 30',
									'240' => '4 h 00',
									'300' => '5 h 00',
									'360' => '6 h 00',
									'420' => '7 h 00',
									'480' => '8 h 00',
									'540' => '9 h 00',
									'600' => '10 h 00',
									'660' => '11 h 00',
									'720' => '12 h 00',
									);
				?>
				<select class="select" name="actionx_duree">
					<option value="">au choix avec l'heure de fin</option>
					<?
					foreach($array_heure as $value => $text)
					{
						$sel = ($action->fields['temps_prevu'] == $value) ? 'selected' : '';
						echo "<option $sel value=\"$value\">$text</option>";
					}
					?>
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
			$params = array();
			$select = 	"
						SELECT 		d.*, i.genre, i.nom, i.prenom
						FROM 		dims_mod_business_dossier d,
									dims_mod_business_tiers_dossier td
						LEFT JOIN	dims_mod_business_interlocuteur i ON td.interlocuteur_id = i.id
						WHERE		td.tiers_id = :idtiers
						AND			td.dossier_id = d.id
						AND			(d.termine = 'Non' OR d.id IN ('".$db->getParamsFromArray($action->dossiers, 'iddossier', $params)."'))
						ORDER BY 	d.date_debut DESC
						";
			$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']);
			$db->query($select, $params);

			while ($fields = $db->fetchrow())
			{
				$sel = (in_array($fields['id'], $action->dossiers)) ? 'selected' : '';
				echo "<option $sel value=\"{$fields['id']}\">{$fields['objet_dossier']} ({$fields['genre']} {$fields['nom']} {$fields['prenom']})</option>";
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
		$sel = (in_array($fields['id'], $action->utilisateurs)) ? 'checked' : '';
		echo "<td><input $sel type=\"checkbox\" value=\"{$fields['id']}\" name=\"actionutilisateur_id[]\"></td><td>{$fields['login']}&nbsp;&nbsp;&nbsp;</td>";
	}
	?>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<tr>
	<td ALIGN="LEFT"><INPUT TYPE="Button" CLASS="FlatButton" VALUE="Changer l'Affectation" onclick="document.location.href='<? echo "$scriptenv?op=action_affecter&action_id=$action_id"; ?>'" style="color:#CC6666"></TD>
	<td ALIGN="RIGHT"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>" onclick="javascript:if (document.getElementById('actiondetail_dossier_id').selectedIndex < 0) {alert('vous devez choisir au moins un dossier');return(false);}"></TD>
</tr>
</TABLE>
</FORM>

<? echo $skin->close_simplebloc(); ?>
