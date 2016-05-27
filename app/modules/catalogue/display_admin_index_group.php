<?php
$bloctitle = str_replace('<GROUP>','<U>'.$group->fields['label'].'</U>',_CATALOGUE_LABEL_GROUP_MANAGEMENT);
$bloctitle = str_replace('<LABEL>',$currentgroup,$bloctitle);

echo $skin->open_simplebloc($bloctitle ,'100%');

if ($father = $group->getfather()) {
	$parentlabel = $father->fields['label'];
	$parentid = $father->fields['id'];
}
else {
	$parentlabel = 'Racine';
	$parentid = '';
}

$users = $group->getusers();
$nbusers = sizeof($users);

$groups = $group->getgroupchildren(1);

$grouplist = '';
foreach ($groups as $childid => $fields) {
	if ($grouplist!='') $grouplist .= ' &#149; ';
	$grouplist .= $fields['label'];
}

$skinlist = dims_getavailableskins();
$groups_parents = system_getallgroups($groupid);

if (isset($_SESSION['catalogue']['code_client'])) {
	$sql = "
		SELECT *
		FROM dims_mod_vpc_budget
		WHERE id_group = $groupid
		AND id_client = '{$_SESSION['catalogue']['code_client']}'
		AND en_cours = 1";
	$db->query($sql);
	$budget_fields = $db->fetchrow();
}
catalogue_getbudget();
?>

<script language="JavaScript">
	function switch_selection(field) {
		if (field.checked) document.getElementById('div_selection').style.display = 'block';
		else document.getElementById('div_selection').style.display = 'none';
	}
</script>

<FORM NAME="form" ACTION="<? echo $dims->getScriptEnv(); ?>" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="administration">
<INPUT TYPE="HIDDEN" NAME="action" VALUE="save_group">
<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<? echo $group->fields['id']; ?>">
<INPUT TYPE='Hidden' NAME='groupinterid' VALUE='<? echo $_SESSION['catalogue']['root_group']; ?>'>
<INPUT TYPE="HIDDEN" NAME="client_cata_restreint" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_afficher_prix" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_budget_non_bloquant" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_change_livraison" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_hors_catalogue" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_imprimer_selection" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_utiliser_selection" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_statistiques" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_export_catalogue" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_ttc" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_retours" VALUE="0">
<INPUT TYPE="HIDDEN" NAME="client_ref_cde_oblig" VALUE="0">

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0">
	<TR CLASS="Title" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<TD COLSPAN=2>
		&#149; <? echo _CATALOGUE_LABEL_GROUP_INFORMATION; ?>
		</TD>
	</TR>
	<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
		<TD COLSPAN=2>
			Ce service appartient au service <B><A HREF="<? echo $dims->getScriptEnv()."?groupid=$parentid"; ?>"><? echo $parentlabel; ?></A></B>
			<BR>Ce service contient <B><? echo $nbusers; ?> utilisateur(s)</B>
			<BR>Ce service a pour sous-services directs : <? echo $grouplist; ?>
			<BR>
			<?php
			if ($_SESSION['session_adminlevel'] == _DIMS_ID_LEVEL_GROUPMANAGER) {
				if ($_SESSION['catalogue']['limite_budget'] === '0') {
					echo "<BR><B>Pas de limitation de budget</B><BR><BR>";
				}
				else {
					$realbudget = getrealbudget($groupid);
					echo "
						<BR>Budget Total : <B>". number_format(round($budget_fields['valeur'],2),2,',',' ') ." &euro;</B>
						<BR>Budget Réel : <B>". number_format(round($realbudget,2),2,',',' ') ." &euro;</B>
						<BR>Crédit : <B>".number_format(round($budget_fields['valeur'] - getminimummoney($groupid),2),2,',',' ') ." &euro;</B>
						<BR><BR>";
				}
			}
			?>
		</TD>
	</TR>
	<TR CLASS="Title" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<TD COLSPAN=2>
		&#149; <? echo _CATALOGUE_LABEL_GROUP_MODIFY; ?>
		</TD>
	</TR>
	<?php
	if ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_GROUPMANAGER && isset($_SESSION['catalogue']['code_client'])) {
		if ($group->fields['id'] != $_SESSION['catalogue']['root_group']) {
			?>
			<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
				<TD ALIGN="CENTER"><B><? echo _CATALOGUE_LABEL_GROUP_NAME; ?></B>:&nbsp;<INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="group_label" VALUE="<? echo $group->fields['label']; ?>"></TD>
				<TD></TD>
			</TR>
			<?
		}
		?>
		<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
			<TD ALIGN="CENTER" VALIGN=TOP COLSPAN="2">
				<TABLE CELLPADDING=2 CELLSPACING=1>
					<TR CLASS='Title'>
						<TD>Adresse de livraison</TD>
					</TR>
					<TR>
						<TD>
							<SELECT CLASS='Select' NAME='gl_id_livraison' Style='width:500px'>
								<OPTION VALUE='-1'></OPTION>
								<?php
								$group_livraison = new group_livraison();
								$group_livraison->open($groupid);

								$sql = "
									SELECT *
									FROM dims_mod_vpc_livraison
									WHERE CLREF = '{$_SESSION['catalogue']['code_client']}'";
								$db->query($sql);
								while ($fields = $db->fetchrow()) {
									(isset($group_livraison->fields['id_livraison']) && $fields['CLNO'] === $group_livraison->fields['id_livraison']) ? $selected = " SELECTED" : $selected = "";
									echo "<OPTION VALUE='{$fields['CLNO']}'$selected>{$fields['CNOML']}&nbsp;{$fields['CRUEL']}&nbsp;{$fields['CAUXL']}&nbsp;{$fields['CPPTLL']}&nbsp;{$fields['CVILL']}</OPTION>";
								}
								?>
							</SELECT>
						</TD>
					</TR>
				</TABLE>
			</TD>
		</TR>
		<?
	}
	?>
	<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
		<TD ALIGN="CENTER" VALIGN="TOP" WIDTH="50%">
			<TABLE CELLPADDING=2 CELLSPACING=1>
				<?php
				if ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_SYSTEMADMIN) {
					if (sizeof($groups_parents)) {
						?>
						<TR>
							<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_GROUP_FATHER; ?>:&nbsp;</TD>
							<TD ALIGN=LEFT>
								<SELECT CLASS="Select" NAME="group_id_group">
									<OPTION VALUE="<? echo _DIMS_SYSTEMGROUP; ?>"></OPTION>
									<?php
									foreach ($groups_parents as $index => $fields) {
										if ($fields['id'] == $group->fields['id_group']) {$sel = 'selected';}
										else {$sel = '';}
										echo "<option $sel VALUE=\"$fields[id]\">$fields[fullpath]</option>";
									}
									?>
								</SELECT>
							</TD>
						</TR>
						<?
					}
				}

				// Ici, on est obligé de vérifier la valeur du CLIENT car pour le responsable des achats,
				// $_SESSION['catalogue']['afficher_prix'] est toujours vrai
				if (!isset($client)) {
					$client = new client();
					$client->open($_SESSION['catalogue']['code_client']);
				}
				($client->fields['afficher_prix'] == 1) ? $afficher_prix = " checked" : $afficher_prix = "";

				($_SESSION['catalogue']['cata_restreint'] == 1) ? $cata_checked = " checked" : $cata_checked = "";
				($_SESSION['catalogue']['change_livraison'] == 1) ? $change_livraison = " checked" : $change_livraison = "";
				($_SESSION['catalogue']['hors_catalogue'] == 1) ? $hors_catalogue = " checked" : $hors_catalogue = "";
				($_SESSION['catalogue']['imprimer_selection'] == 1) ? $imprimer_selection = " checked" : $imprimer_selection = "";
				($_SESSION['catalogue']['utiliser_selection'] == 1) ? $utiliser_selection = " checked" : $utiliser_selection = "";
				($_SESSION['catalogue']['statistiques'] == 1) ? $statistiques = " checked" : $statistiques = "";
				($_SESSION['catalogue']['export_catalogue'] == 1) ? $export_catalogue = " checked" : $export_catalogue = "";
				($_SESSION['catalogue']['ttc'] == 1) ? $ttc = " checked" : $ttc = "";
				($_SESSION['catalogue']['retours'] == 1) ? $retours = " checked" : $retours = "";
				($_SESSION['catalogue']['ref_cde_oblig'] == 1) ? $ref_cde_oblig = " checked" : $ref_cde_oblig = "";

				if ($groupid == $_SESSION['catalogue']['root_group']) {
					if ($_SESSION['catalogue']['limite_budget'] === '1') {
						($_SESSION['catalogue']['budget_non_bloquant'] == 1) ? $budget_checked = " checked" : $budget_checked = "";
						?>
						<TR>
							<TD COLSPAN="2">
								<BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_budget_non_bloquant' VALUE='1'<? echo $budget_checked; ?>> <B>Budget non bloquant</B>
							</TD>
						</TR>
						<TR>
							<TD COLSPAN="2">
								<SELECT CLASS="Select" NAME="client_budget_reconduction">
									<?php
									$recond = array(
										0 => "Pas de reconduction",
										1 => "Reconduction mensuelle",
										2 => "Reconduction annuelle"
									);
									foreach ($recond as $key => $value) {
										($key == $_SESSION['catalogue']['budget_reconduction']) ? $selected = " selected" : $selected = "";
										echo "<OPTION VALUE=\"$key\"$selected>$value</OPTION>";
									}
									?>
								</SELECT>
							</TD>
						</TR>
						<?
					}
					?>
					<TR>
						<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_afficher_prix' VALUE='1'<? echo $afficher_prix; ?>> <B>Afficher les prix</B></TD>
					</TR>
					<TR>
						<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_change_livraison' VALUE='1'<? echo $change_livraison; ?>> <B>Permettre aux utilisateurs<br>de changer l'adresse de livraison</B></TD>
					</TR>
					<?php
					if (isset($_SESSION['catalogue']['iwasadmin']) && $_SESSION['catalogue']['iwasadmin']) {
						?>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_hors_catalogue' VALUE='1'<? echo $hors_catalogue; ?>> <B>Permettre les commandes hors catalogue</B></TD>
						</TR>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_export_catalogue' VALUE='1'<? echo $export_catalogue; ?>> <B>Permettre les exports de catalogue</B></TD>
						</TR>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_utiliser_selection' VALUE='1'<? echo $utiliser_selection; ?> onClick="javascript:switch_selection(this);"> <B>Utiliser la sélection</B></TD>
						</TR>
						<TR>
							<TD COLSPAN="2">
								<? ($client->fields['utiliser_selection']) ? $display = 'block' : $display = 'none'; ?>
								<div id="div_selection" style="display:<? echo $display; ?>">
									<TABLE CELLPADDING="2" CELLSPACING="1" STYLE="border: #2A5B90 1px solid">
									<TR>
										<TD COLSPAN="2"><INPUT disabled="disabled" CLASS='Checkbox' TYPE='Checkbox' NAME='client_imprimer_selection' VALUE='1'<? echo $imprimer_selection; ?>> <B>Permettre d'imprimer la sélection</B></TD>
									</TR>
									<TR>
										<TD COLSPAN="2"><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_cata_restreint' VALUE='1'<? echo $cata_checked; ?>> <B>Catalogue restreint</B></TD>
									</TR>
									</TABLE>
								</div>
							</TD>
						</TR>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_statistiques' VALUE='1'<? echo $statistiques; ?>> <B>Activer les statistiques</B></TD>
						</TR>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_ttc' VALUE='1'<? echo $ttc; ?>> <B>Gestion des prix TTC</B></TD>
						</TR>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_retours' VALUE='1'<? echo $retours; ?>> <B>Permettre les demandes des retours</B></TD>
						</TR>
						<TR>
							<TD COLSPAN="2"><BR><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='client_ref_cde_oblig' VALUE='1'<? echo $ref_cde_oblig; ?>> <B>Ref de cde obligatoire</B></TD>
						</TR>
						<?
					}
				}
				?>
				<TR>
					<TD ALIGN=CENTER COLSPAN=2>
						<BR><INPUT TYPE="Submit" CLASS="Button" VALUE="Enregistrer">
					</TD>
				</TR>
			</FORM>
			</TABLE>
		<BR>
		</TD>
		<TD VALIGN="TOP" WIDTH="50%">
			<?php
			// Si on est un client
			if (isset($_SESSION['catalogue']['code_client'])) {
				// Si on est à la racine des groupes
				if ($groupid == $_SESSION['catalogue']['root_group']) {
					?>
					<FORM ACTION='<? echo $dims->getScriptEnv(); ?>' METHOD='Post'>
					<INPUT TYPE='Hidden' NAME='action' VALUE='save_budget'>
					<INPUT TYPE='Hidden' NAME='op' VALUE='administration'>
					<INPUT TYPE='Hidden' NAME='groupinterid' VALUE='<? echo $_SESSION['catalogue']['root_group']; ?>'>
					<INPUT TYPE='Hidden' NAME='budget_id_group' VALUE='<? echo $groupid; ?>'>
					<INPUT TYPE='Hidden' NAME='budget_en_cours' VALUE='1'>

					<TABLE CELLPADDING=2 CELLSPACING=1>
						<?php
						if ($_SESSION['catalogue']['limite_budget'] === '1') {
							echo "
								<INPUT TYPE='Hidden' NAME='id_budget' VALUE='{$budget_fields['id']}'>
								<INPUT TYPE='Hidden' NAME='limite_budget' VALUE='1'>
								<TR CLASS='Title'>
									<TD COLSPAN=2>Modifier le budget</TD>
								</TR>";

							if (isset($err)) {
								echo "<TR><TD COLSPAN=2><FONT STYLE='color:red'>{$err_msg[$err]}</FONT><BR><BR></TD></TR>";
							}

							echo "
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Code :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_code' VALUE='{$budget_fields['code']}'></TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Montant :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_valeur' VALUE='{$budget_fields['valeur']}'></TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=RIGHT NOWRAP><INPUT CLASS='Button' TYPE='Button' VALUE='Clôturer le budget' onClick=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?op=administration&action=close_budget&id_budget={$budget_fields['id']}&groupid=$groupid','Etes-vous sûr(e) de vouloir clôturer le budget ?');\"></TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=CENTER NOWRAP><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Enregistrer'></TD>
								</TR>
								</FORM>

								<FORM ACTION='$scriptenv' METHOD='Post'>
								<INPUT TYPE='Hidden' NAME='op' VALUE='administration'>
								<INPUT TYPE='Hidden' NAME='action' VALUE='rectif_budget'>
								<INPUT TYPE='Hidden' NAME='id_budget' VALUE='{$budget_fields['id']}'>
								<INPUT TYPE='Hidden' NAME='budget_id_group' VALUE='$groupid'>
								<INPUT TYPE='Hidden' NAME='budget_orig' VALUE='{$_SESSION['catalogue']['budget']['credit']}'>
								<TR><TD COLSPAN=\"2\">&nbsp;</TD></TR>
								<TR CLASS='Title'>
									<TD COLSPAN=2>Budget courant</TD>
								</TR>";
							if (isset($err2)) {
								echo "<TR><TD COLSPAN=2><FONT STYLE='color:red'>{$err_msg[$err2]}</FONT><BR><BR></TD></TR>";
							}
							echo "
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Actuel :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_nv' VALUE='{$_SESSION['catalogue']['budget']['credit']}'></TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Commentaire :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='commentaire' MAXLENGTH='255'></TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=CENTER NOWRAP><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Rectifier le budget'></TD>
								</TR>
								</FORM>";
						}
						elseif ($_SESSION['catalogue']['limite_budget'] === '0') {
							echo "
								<TR CLASS='Title'>
									<TD>Pas de limitation de budget</TD>
								</TR>
								<TR>
									<TD><INPUT CLASS='Button' TYPE='Submit' VALUE='Limiter le budget'></TD>
								</TR>
								</FORM>";
						}
						else {
							echo "
								<INPUT TYPE='Hidden' NAME='limite_budget' VALUE='1'>
								<TR CLASS='Title'>
									<TD COLSPAN=2>Créer un nouveau budget</TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Code :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_code'></TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Montant :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_valeur'></TD>
								</TR>
								<TR>
									<TD COLSPAN=2><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='limite_budget' VALUE='0'>&nbsp;Pas de limitation de budget</TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=CENTER><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Enregistrer'></TD>
								</TR>
								</FORM>";
						}
						?>
					</TABLE>
				<?php
				}
				// Si on n'est pas dans le groupe racine
				else {
					// Si pas de limitation de budget
					if ($_SESSION['catalogue']['limite_budget'] === '0') {
						?>
						<TABLE CELLPADDING=2 CELLSPACING=1>
							<TR CLASS='Title'>
								<TD COLSPAN=2>Pas de limitation de budget</TD>
							</TR>
						</TABLE>
						<?
					}
					// Si on a une limite de budget
					else {
						// Si on est rattaché au groupe principal ou si on n'est pas le groupe auquel on est pas rattaché
						$sql = "SELECT id_group FROM dims_group_user WHERE id_user = {$_SESSION['dims']['userid']}";
						$db->query($sql);
						$row = $db->fetchrow();

						if ($groupid != $row['id_group'] || $row['id_group'] == $_SESSION['catalogue']['root_group']) {
							$sql = "
								SELECT *
								FROM dims_mod_vpc_budget
								WHERE id_group = $groupid
								AND id_client = '{$_SESSION['catalogue']['code_client']}'
								AND en_cours = 1";
							$db->query($sql);
							$fields = $db->fetchrow();
							?>
							<FORM ACTION='<? echo $dims->getScriptEnv(); ?>' METHOD='Post'>
							<INPUT TYPE='Hidden' NAME='op' VALUE='administration'>
							<INPUT TYPE='Hidden' NAME='action' VALUE='affecter_budget'>
							<INPUT TYPE='Hidden' NAME='groupid' VALUE='<? echo $groupid; ?>'>
							<INPUT TYPE='Hidden' NAME='groupinterid' VALUE='<? echo $_SESSION['catalogue']['root_group']; ?>'>
							<INPUT TYPE='Hidden' NAME='id_budget' VALUE='<? echo $fields['id']; ?>'>
							<INPUT TYPE='Hidden' NAME='budget_id_group' VALUE='<? echo $groupid; ?>'>
							<INPUT TYPE='Hidden' NAME='budget_en_cours' VALUE='1'>

							<TABLE CELLPADDING=2 CELLSPACING=1>
								<TR CLASS='Title'>
									<TD COLSPAN=2>Affecter un budget</TD>
								</TR>
								<?php
								if (isset($err)) {
									echo "<TR><TD COLSPAN=2><FONT STYLE='color:red'>{$err_msg[$err]}</FONT><BR><BR></TD></TR>";
								}
								?>
								<TR>
									<TD NOWRAP>Budget à répartir :</TD>
									<TD><? echo number_format(round(getAvailableMoney($groupid,$_SESSION['dims']['userid']),2),2,',',' '); ?> &euro;</TD>
								</TR>
								<TR>
									<TD NOWRAP>Code :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_code' VALUE='<? echo $fields['code']; ?>'></TD>
								</TR>
								<TR>
									<TD NOWRAP>Montant :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_valeur' VALUE='<? echo $fields['valeur']; ?>'></TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=CENTER><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Affecter'></TD>
								</TR>
							</FORM>
							</TABLE>

							<FORM ACTION='<? echo $dims->getScriptEnv(); ?>' METHOD='Post'>
							<INPUT TYPE='Hidden' NAME='op' VALUE='administration	'>
							<INPUT TYPE='Hidden' NAME='action' VALUE='rectif_budget'>
							<INPUT TYPE='Hidden' NAME='id_budget' VALUE='<? echo $budget_fields['id']; ?>'>
							<INPUT TYPE='Hidden' NAME='budget_id_group' VALUE='<? echo $groupid; ?>'>
							<INPUT TYPE='Hidden' NAME='budget_orig' VALUE='<? echo round($budget_fields['valeur'] - getminimummoney($groupid),2); ?>'>

							<TABLE CELLPADDING="2" CELLSPACING="1">
							<TR><TD COLSPAN="2">&nbsp;</TD></TR>
							<TR CLASS='Title'>
								<TD COLSPAN=2>Budget courant</TD>
							</TR>
							<TR>
								<TD ALIGN=RIGHT NOWRAP>Actuel :</TD>
								<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_nv' VALUE='<? echo round($budget_fields['valeur'] - getminimummoney($groupid),2); ?>'></TD>
							</TR>
							<TR>
								<TD ALIGN=RIGHT NOWRAP>Commentaire :</TD>
								<TD><INPUT CLASS='Text' TYPE='Text' NAME='commentaire' MAXLENGTH='255'></TD>
							</TR>
							<TR>
								<TD COLSPAN=2 ALIGN=CENTER NOWRAP><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Rectifier le budget'></TD>
							</TR>
							</FORM>
							</TABLE>
							<?php
						}
						else {
							?>
							<TABLE CELLPADDING=2 CELLSPACING=1>
								<TR>
									<TD NOWRAP><B>Budget affecté au groupe :&nbsp;</B></TD>
									<TD><? echo number_format(round($budget_fields['valeur'],2),2,',',' '); ?> &euro;</TD>
								</TR>
								<?php
								if ($budget_fields['valeur'] == 0) {
								  echo "
								    <tr>
								      <td colspan=\"2\">Demandez à votre administrateur de vous affecter un budget pour que vous puissiez passer des commandes.</td>
								    </tr>";
								}
								?>
							</TABLE>
							<?php
						}
					}
				}
			}
			?>
		</TD>
	</TR>
	<TR CLASS="Title" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<TD COLSPAN=2>
		&#149; <? echo _CATALOGUE_LABEL_MANAGEMENT; ?>
		</TD>
	</TR>
	<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
		<TD COLSPAN=2>
			<?php
			$sql = "SELECT id_group FROM dims_group_user WHERE id_user = {$_SESSION['dims']['userid']}";
			$db->query($sql);
			$row = $db->fetchrow();

			$toolbar_group[0]['title'] = str_replace('<LABEL>','<BR><B>'.$childgroup.'</B>', _CATALOGUE_LABEL_CREATE_CHILD);
			$toolbar_group[0]['url'] = $dims->getScriptEnv()."?op=administration&action=create_child&groupid=$groupid";
			$toolbar_group[0]['icon'] = './common/modules/catalogue/img/btn_group_createchild.png';
			$toolbar_group[0]['width'] = '200';

			// delete button if group not protected and no children
			if (!$group->fields['protected'] && $groupid != $_SESSION['catalogue']['root_group'] && !sizeof($group->getgroupchildrenlite()) && $row['id_group'] != $groupid) {
				$toolbar_group[1]['title'] = str_replace('<LABEL>','<BR><B>'.$currentgroup.'</B>', _CATALOGUE_LABEL_DELETE_GROUP);
				$toolbar_group[1]['url'] = $dims->getScriptEnv()."?op=administration&action=delete_group&groupid=$groupid";
				$toolbar_group[1]['icon'] = './common/modules/catalogue/img/btn_group_delete.png';
				$toolbar_group[1]['width'] = '200';
				$toolbar_group[1]['confirm'] = "Etes-vous sûr(e) de vouloir supprimer ce service ?";
			}

			echo $skin->create_toolbar($toolbar_group, $x, false);
			?>

		</TD>
	</TR>

</TABLE>
<?php echo $skin->close_simplebloc(); ?>
