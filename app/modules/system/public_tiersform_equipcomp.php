<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<!-- colonne 1 -->
	<TD valign="top" WIDTH="280">
		<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",			"tiers_ajoutercompetence");
			$token->field("tiers_id",	$tiers->fields['id']);
			$token->field("competence_code");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_ajoutercompetence">
		<INPUT TYPE="HIDDEN" NAME="tiers_id" VALUE="<? echo $tiers->fields['id']; ?>">

		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD COLSPAN="3" ALIGN="left"><b>Compétences:&nbsp;</b></TD>
		</TR>
		<?
		$select =	"
					SELECT	c.code, c.intitule
					FROM	dims_mod_business_tiers_competence tc,
							dims_mod_business_competence c
					WHERE	tc.tiers_id = :idtier
					AND		tc.competence_code = c.code
					ORDER BY c.code ASC
					";

		$db->query($select, array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $tiers->getId()),
		));

		if ($db->numrows())
		{
			while ($row = $db->fetchrow())
			{
				echo "<TR><TD>{$row['code']}</TD><TD>{$row['intitule']}</TD><TD><A HREF=\"$scriptenv?op=tiers_supprimercompetence&tiers_id={$tiers->fields['id']}&competence_code={$row['code']}\"><IMG BORDER=\"0\" SRC=\"./common/modules/business/img/ico_delete.gif\"></A></TD></TR>";
			}
		}
		else echo "<TD COLSPAN=\"3\">Aucune compétence définie</TD>";
		?>
		<TR>
			<TD COLSPAN="3" ALIGN="left"><b>Ajouter une compétence:&nbsp;</b></TD>
		</TR>
		<TR>
			<TD COLSPAN="3" ALIGN="left">
			<SELECT NAME="competence_code" CLASS="SELECT" Style="width:250px">
			<?
			$select =	"
						SELECT	*
						FROM	dims_mod_business_competence
						ORDER BY code
						";

			$db->query($select);

			while ($row = $db->fetchrow())
			{
				echo "<OPTION VALUE=\"{$row['code']}\">{$row['code']} - {$row['intitule']}</OPTION>";
			}
			?>
			</SELECT>
			</TD>
		</TR>
		<TR>
			<TD COLSPAN="3" ALIGN="right">
				<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_ADD; ?>">
			</TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>

	<!-- colonne 2 -->
	<TD valign="top" WIDTH="280">
		<FORM NAME="form_tiers" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",			"tiers_ajouterequipement");
			$token->field("tiers_id",	$tiers->fields['id']);
			$token->field("equipement_code");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="tiers_ajouterequipement">
		<INPUT TYPE="HIDDEN" NAME="tiers_id" VALUE="<? echo $tiers->fields['id']; ?>">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD COLSPAN="3" ALIGN="left"><b>Equipements:&nbsp;</b></TD>
		</TR>
		<?
		$select =	"
					SELECT	e.code, e.intitule
					FROM	dims_mod_business_tiers_equipement te,
							dims_mod_business_equipement e
					WHERE	te.tiers_id = :idtier
					AND		te.equipement_code = e.code
					ORDER BY e.code ASC
					";

		$db->query($select, array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $tiers->getId()),
		));

		if ($db->numrows())
		{
			while ($row = $db->fetchrow())
			{
				echo "<TR><TD>{$row['code']}</TD><TD>{$row['intitule']}</TD><TD><A HREF=\"$scriptenv?op=tiers_supprimerequipement&tiers_id={$tiers->fields['id']}&equipement_code={$row['code']}\"><IMG BORDER=\"0\" SRC=\"./common/modules/business/img/ico_delete.gif\"></A></TD></TR>";
			}
		}
		else echo "<TD COLSPAN=\"3\">Aucun équipement défini</TD>";
		?>
		<TR>
			<TD COLSPAN="3" ALIGN="left"><b>Ajouter un équipement:&nbsp;</b></TD>
		</TR>
		<TR>
			<TD COLSPAN="3" ALIGN="left">
			<SELECT NAME="equipement_code" CLASS="SELECT" Style="width:250px">
			<?
			$select =	"
						SELECT	*
						FROM	dims_mod_business_equipement
						ORDER BY code
						";

			$db->query($select);

			while ($row = $db->fetchrow())
			{
				echo "<OPTION VALUE=\"{$row['code']}\">{$row['code']} - {$row['intitule']}</OPTION>";
			}
			?>
			</SELECT>
			</TD>
		</TR>
		<TR>
			<TD COLSPAN="3" ALIGN="right">
				<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_ADD; ?>">
			</TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>

</TR>
</TABLE>
