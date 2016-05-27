<script language="javascript">
function business_validate(form)
{
	if (dims_validatefield("<? echo _BUSINESS_LABEL_INTITULE_project; ?>",form.project_intitule,"string"))
		return true;

	return false;
}
</script>

<?
echo $skin->open_simplebloc(_PROJECT_LABEL_ADD_PROJECT,'border:2px solid '._BUSINESS_COLOR_project.';','color:'._BUSINESS_COLOR_SEL.';background-color:'._BUSINESS_COLOR_project.';');

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
	<TD ALIGN="right">
	<INPUT TYPE="submit" CLASS="FlatButton" VALUE="<? echo _PROJECT_LABEL_RETURN_LISTPROJECTS; ?>">
	</TD>
</TR>
</TABLE>
</FORM>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR><TD HEIGHT="5"></TD></TR></TABLE>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" NAME="form_project">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op","project_enregistrer");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="project_enregistrer">
<TABLE CELLPADDING="2" CELLSPACING="1" width="100%">
<TR>
	<!-- colonne 1 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1" width="100%">
		<TR>
			<TD ALIGN="LEFT" width="10%"><? echo _FORM_SELECTION; ?></TD>
			<TD ALIGN="LEFT" width="40%"><? echo _FORM_PROJECT_LABEL; ?></TD>
			<TD ALIGN="LEFT" width="20%"><? echo _FORM_PROJECT_STATE; ?></TD>
			<TD ALIGN="LEFT" width="30%"><? echo _PROJECT_MENU_PERSON_IN_CHARGE; ?></TD>
		</TR>
		<TR>
		<?
			$list_project_sel=array();

			$select = 	"
				SELECT 		p.id
				FROM		dims_project p
				INNER JOIN	dims_mod_business_tiers_project tp
				ON		tp.id_project = p.id
				AND		tp.id_tiers = :idtier
				ORDER BY 	d.date_debut DESC
				";

			$res=$db->query($select, array(
				':idtier' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['business']['tiers_id']),
			));
			if ($db->numrows($res)>0) {
				while ($f=$db->fetchrow($res)) {
					$list_project_sel[]=$f['id'];
				}
			}
			else $list_project_sel[]=0;

			// on construit la liste des projects disponibles
			$params = array();
			$sql = 	"select 		p.id, p.label,p.progress, u1.lastname, u1.firstname,
								concat(u2.lastname, ' ', u2.firstname) as resp,
								p.date_start, p.date_end, p.state
				from 			dims_project p
				left join		dims_user as u1
				on				p.id_create=u1.id
				left join		dims_user as u2
				on 				p.id_resp=u2.id
				where			p.id not in (".$db->getParamsFromArray($list_project_sel, 'idproject', $params).")";

			/* On execute la requete */
			$rs = $db->query($sql, $params);

			/* On affiche les resultats de la requete */
			$cpt=0;
			echo "<form id=\"form_affect\" name=\"form_affect\" method=\"POST\">";
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op","affectation_save");
			$token->field("id_project");
			$token->field("tiersproject_interlocuteur_id");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
			echo "<input type=\"hidden\" name=\"op\" value=\"affectation_save\">";

			echo "<table style=\"width:100%;\" cellpadding=\"0\" cellspacing=\"0\">";

			while ($fields = $db->fetchrow($rs)) {
				if ($cpt % 2 == 1)
					echo '	<tr class="trl1">';
				else
					echo '	<tr class="trl2">';

				echo "<tr><td><input name=\"id_project\" value=\"".$fields['id']."\" type=\"radio\" ></td>
				<td>".$fields['label']."</td><td>";
				echo display_avancement($fields['progress']);

				echo "</td><td>".strtoupper(substr($fields['firstname'],0,1))." ".$fields['lastname']."</td></tr>";

			}
			echo "</td></tr></table>";
		?>
	</TD>
	<!-- colonne 2 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1" width="100%">
		<TR>
			<TD ALIGN=RIGHT><? echo _BUSINESS_LABEL_INTERLOCUTEUR; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<SELECT CLASS="Select" NAME="tiersproject_interlocuteur_id">
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
					$sel = '';
					echo "<option $sel value=\"{$fields['id']}\">{$fields['nom']} {$fields['prenom']}</option>";
				}
				?>
				</SELECT>
			</TD>
		</tr>
		</table>
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
