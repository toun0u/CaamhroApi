<table cellpadding="6" cellspacing="0" width="100%">
<tr>
  <td>
  <?php
	include_once './common/modules/catalogue/'._CATA_VARIANTE.'/include/class_client.php';

	$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
	$code_client = dims_load_securvalue('code_client', dims_const::_DIMS_CHAR_INPUT, true, true);
	$filtre = dims_load_securvalue('filtre', dims_const::_DIMS_CHAR_INPUT, true, true);
	$client_code_client = dims_load_securvalue('client_code_client', dims_const::_DIMS_CHAR_INPUT, false, true);
	$new_CREF = dims_load_securvalue('new_CREF', dims_const::_DIMS_CHAR_INPUT, false, true);

	if (!isset($_SESSION['catalogue']['filtreClients'])) $_SESSION['catalogue']['filtreClients'] = '';

	switch ($action) {
		case 'delete':
			$code_client = dims_load_securvalue('code_client', dims_const::_DIMS_CHAR_INPUT, true, false);
			$client = new cata_client();
			$client->openByCode($code_client);
			$client->delete();
            dims_redirect($dims->getScriptEnv().'?part=clients&filtre='.$_SESSION['catalogue']['filtreClients']);
			break;
		case 'save':
			$client = new cata_client();
			if (!empty($client_code_client)) $client->openByCode($client_code_client);
			if (!empty($new_CREF) && trim($new_CREF) != "") $client->change_cref($new_CREF);
			$client->setvalues($_POST, 'client_');
			$client->save();

            if (!empty($_POST['parents'])) {
                include_once './common/modules/catalogue/include/class_client_ratt.php';
                foreach ($_POST['parents'] as $code_client_parent) {
                    $cratt = new client_ratt();
                    $cratt->open($client->fields['code_client'], $code_client_parent);
                    $cratt->setvalues($_POST, "ratt{$code_client_parent}_");
                    $cratt->save();
                }
            }

            dims_redirect($dims->getScriptEnv().'?part=clients&filtre='.$_SESSION['catalogue']['filtreClients']);
			break;
 		case 'add':
            echo $skin->open_simplebloc('Ajout d\'un client');
			?>
			<table cellpadding="4" cellspacing="0">
			<form action="<?php echo $dims->getScriptEnv(); ?>" method="post">
			<input type="hidden" name="part" value="clients" />
			<input type="hidden" name="action" value="save" />
			<tr>
				<td align="right">Code Client:&nbsp;</td>
				<td><input type="text" class="text" name="client_CREF" size="10" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Nom:&nbsp;</td>
				<td><input type="text" class="text" name="client_CNOM" size="32" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Adresse 1:&nbsp;</td>
				<td><input type="text" class="text" name="client_CRUE" size="32" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Adresse 2:&nbsp;</td>
				<td><input type="text" class="text" name="client_CAUX" size="32" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Code Postal:&nbsp;</td>
				<td><input type="text" class="text" name="client_CCPTL" size="10" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Ville:&nbsp;</td>
				<td><input type="text" class="text" name="client_CVIL" size="32" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Code Tarif 1:&nbsp;</td>
				<td><input type="text" class="text" name="client_CCHAN" size="10" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Code Tarif 2:&nbsp;</td>
				<td><input type="text" class="text" name="client_CCODTAR" size="10" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right">Compte Bloqué:&nbsp;</td>
				<td><select name="client_CBLOQUE"><option value="B">oui</option><option selected value="">non</option></select></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><input type="submit" class="button" value="Ajouter"</td>
			</tr>
			</form>
			</table>
			<?php
			echo $skin->close_simplebloc();
			break;
		case 'modify':
            echo $skin->open_simplebloc('Modifier le client');

			$client = new cata_client();
			$client->openByCode($code_client);
			?>
			<form name="cli_form" action="<? echo $scriptenv; ?>" method="post">
			<input type="hidden" name="part" value="clients" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="client_code_client" value="<?php echo $client->fields['code_client']; ?>" />

			<table cellpadding="2" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<table cellpadding="4" cellspacing="0">
						<tr>
							<td align="right">Code Client:&nbsp;</td>
							<td><?php echo $client->fields['code_client']; ?></td>
						</tr>
						<tr>
							<td align="right">Nouveau Code Client:&nbsp;</td>
							<td><input type="text" class="text" name="new_CREF" size="32" maxlength="255" value=""></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td align="right">Nom:&nbsp;</td>
							<td><input type="text" class="text" name="client_nom" size="32" maxlength="255" value="<?php echo $client->fields['nom']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Adresse 1:&nbsp;</td>
							<td><input type="text" class="text" name="client_adr1" size="32" maxlength="255" value="<?php echo $client->fields['adr1']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Adresse 2:&nbsp;</td>
							<td><input type="text" class="text" name="client_adr2" size="32" maxlength="255" value="<?php echo $client->fields['adr2']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Code Postal:&nbsp;</td>
							<td><input type="text" class="text" name="client_cp" size="10" maxlength="255" value="<?php echo $client->fields['cp']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Ville:&nbsp;</td>
							<td><input type="text" class="text" name="client_ville" size="32" maxlength="255" value="<?php echo $client->fields['ville']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Code Tarif 1:&nbsp;</td>
							<td><input type="text" class="text" name="client_code_tarif_1" size="10" maxlength="255" value="<?php echo $client->fields['code_tarif_1']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Code Tarif 2:&nbsp;</td>
							<td><input type="text" class="text" name="client_code_tarif_2" size="10" maxlength="255" value="<?php echo $client->fields['code_tarif_2']; ?>"></td>
						</tr>
						<tr>
							<td align="right">Compte Bloqué:&nbsp;</td>
							<td><select name="client_bloque"><option value="B">oui</option><option <?php if ($client->fields['bloque']!='1') echo 'selected'; ?> value="">non</option></select></td>
						</tr>
						<tr>
							<td align="right">Mode de paiement:&nbsp;</td>
							<td><select name="client_observation"><option value="comptant">Comptant</option><option <?php if ($client->fields['observation']=='differe') echo 'selected'; ?> value="differe">Diff&eacute;r&eacute;</option></select></td>
						</tr>
			<!-- 			<tr>
							<td align="right">Relance automatique:&nbsp;</td>
							<td><select name="client_relance_auto"><option value="1">oui</option><option <?php if ($client->fields['relance_auto']==0) echo 'selected'; ?> value="0">non</option></select></td>
						</tr>
			 -->
						</table>
					</td>
				</tr>
				<tr>
	                <td valign="top">
	                    <?
	                    // Liste des comptes auxquels il est rattaché
	                    $a_ratt = array();
	                    $db->query("
	                        SELECT  rat.*,
	                                IF (cli.nom <> '', cli.nom, fact.nom) AS nom,
	                                IF (cli.adr1 <> '', cli.adr1, fact.adr1) AS adr1,
	                                IF (cli.adr2 <> '', cli.adr2, fact.adr2) AS adr2,
	                                IF (cli.cp <> '', cli.cp, fact.cp) AS cp,
	                                IF (cli.ville <> '', cli.ville, fact.ville) AS ville,
	                                IF (cli.nom <> '', 'C', 'F') AS type
	                        FROM    dims_mod_cata_client_ratt rat
	                        LEFT JOIN   dims_mod_cata_client cli
	                        ON          cli.code_client = rat.code_client_parent
	                        LEFT JOIN   dims_mod_cata_facturation fact
	                        ON          CONCAT('F',fact.id) = rat.code_client_parent
	                        WHERE code_client_enfant = '$code_client'");
	                    while ($row = $db->fetchrow()) {
	                        $a_ratt[$row['code_client_parent']] = array(
	                            'nom' => $row['nom'],
	                            'adr1' => $row['adr1'],
	                            'adr2' => $row['adr2'],
	                            'cp' => $row['cp'],
	                            'ville' => $row['ville'],
	                            'force' => $row['force'],
	                            'valid_oblig' => $row['valid_oblig'],
	                            'type' => $row['type']
	                            );
	                    }

	                    if (sizeof($a_ratt)) {
	                        echo $skin->open_simplebloc('Rattaché aux comptes','100%');
	                            ?>
	                            <table cellpadding="2" cellspacing="0" width="100%">
	                            <tr bgcolor="<? echo $skin->values['bgline2']; ?>">
	                                <th>Ref client</th>
	                                <th>Nom</th>
	                                <th>Fact. auto.</th>
	                                <th>Valid. oblig.</th>
	                                <th>&nbsp;</th>
	                            </tr>
	                            <?
	                            $color = $skin->values['bgline2'];
	                            foreach ($a_ratt as $code_client_parent => $values) {
	                                echo "<input type=\"hidden\" name=\"parents[]\" value=\"$code_client_parent\">";
	                                echo "<input type=\"hidden\" name=\"ratt{$code_client_parent}_force\" value=\"0\">";
	                                echo "<input type=\"hidden\" name=\"ratt{$code_client_parent}_valid_oblig\" value=\"0\">";

	                                $color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
	                                $force = ($values['force']) ? ' checked' : '';
	                                $valid_oblig = ($values['valid_oblig']) ? ' checked' : '';

	                                $adrfact = "{$values['nom']}<br>{$values['adr1']}<br>";
	                                if ($values['adr2'] != '') $adrfact .= "{$values['adr2']}<br>";
	                                $adrfact .= "{$values['cp']} {$values['ville']}";

	                                echo "
	                                    <tr bgcolor=\"$color\">
	                                        <td>$code_client_parent</td>
	                                        <td onmouseover=\"javascript:dims_showpopup('$adrfact','',event);this.style.cursor='pointer';\" onmouseout=\"javascript:dims_hidepopup();this.style.cursor='default';\">
	                                            {$values['nom']}
	                                        </td>
	                                        <td align=\"center\" onmouseover=\"javascript:dims_showpopup('Si ce compte de rattachement est en facturation automatique, toutes les commandes du client lui seront facturées.','',event);\" onmouseout=\"javascript:dims_hidepopup();\">
	                                            <input class=\"checkbox\" type=\"checkbox\" name=\"ratt{$code_client_parent}_force\" value=\"1\"{$force}>
	                                        </td>";
	                                // CLIENT
									if ($values['type'] == 'C') {
										echo "
												<td align=\"center\" onmouseover=\"javascript:dims_showpopup('Si la validation est obligatoire, le compte de rattachement devra valider toutes les commandes passées par celui-ci.','',event);\" onmouseout=\"javascript:dims_hidepopup();\">
													<input class=\"checkbox\" type=\"checkbox\" name=\"ratt{$code_client_parent}_valid_oblig\" value=\"1\"{$valid_oblig}>
												</td>";
									}
									// ADRESSE DE FACTURATION
									else {
										echo "
												<td align=\"center\">
													<input class=\"checkbox\" type=\"checkbox\" name=\"ratt{$code_client_parent}_valid_oblig\" value=\"1\"{$valid_oblig} disabled>
												</td>";
									}
									echo "
	                                        <td align=\"center\"><a href=\"javascript:dims_confirmlink('$scriptenv?part=clients&action=detach_client&code_client_enfant=$code_client&code_client_parent=$code_client_parent','Etes-vous sûr(e) de vouloir détacher ce client ?');\"><img src=\"./common/modules/catalogue/img/detach.gif\" alt=\"Détacher\" border=\"0\"></a></td>
	                                    </tr>";
	                            }
	                            ?>
	                            </table>
	                            <?
	                        echo $skin->close_simplebloc();
	                    }

						// Liste des comptes rattachés à ce compte
	                    $a_ratt_child = array();
	                    $db->query("
	                        SELECT  rat.*,
	                                cli.nom AS nom
	                        FROM    dims_mod_cata_client_ratt rat
	                        INNER JOIN  dims_mod_cata_client cli
	                        ON          cli.code_client = rat.code_client_enfant
	                        WHERE code_client_parent = '$code_client'");
	                    while ($row = $db->fetchrow()) {
	                        $a_ratt_child[$row['code_client_enfant']] = array(
	                            'nom' => $row['nom'],
	                            'force' => $row['force']
	                            );
	                    }
	                    if (sizeof($a_ratt_child)) {
	                        echo $skin->open_simplebloc('Liste des comptes rattachés à ce compte','100%');
	                            ?>
	                            <table cellpadding="2" cellspacing="0" width="100%">
	                            <tr bgcolor="<? echo $skin->values['bgline2']; ?>">
	                                <th>Ref client</th>
	                                <th>Nom</th>
	                            </tr>
	                            <?
	                            $color = $skin->values['bgline2'];
	                            foreach ($a_ratt_child as $code_client_enfant => $values) {
	                                $color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
	                                $force = ($values['force']) ? ' checked' : '';

	                                echo "
	                                    <tr bgcolor=\"$color\">
	                                        <td>$code_client_enfant</td>
	                                        <td>{$values['nom']}</td>
	                                    </tr>";
	                            }
	                            ?>
	                            </table>
	                            <?
	                        echo $skin->close_simplebloc();
	                    }

	                    ?>
						</form>
						<?

						// Rattacher à un compte
	                    if (!empty($filtre)) {
	                        $filtre = str_replace('%', '', trim($filtre));
	                    }
	                    echo $skin->open_simplebloc('Rattacher à un compte','100%');
	                        ?>
	                        <form name="rech_form" action="<? echo $scriptenv; ?>" method="Post">
	                        <input type="hidden" name="part" value="clients">
	                        <input type="hidden" name="action" value="modify">
	                        <input type="hidden" name="code_client" value="<? echo $code_client; ?>">

	                        <table cellpadding="2" cellspacing="0" width="100%">
	                        <tr bgcolor="<? echo $skin->values['bgline2']; ?>">
	                            <th>Rechercher :</th>
	                            <th><input class="text" type="text" name="filtre" value="<? echo $filtre; ?>"></th>
	                            <th><input class="button" type="submit" value="Rechercher"></th>
	                        </tr>
	                        <?
	                        if ($filtre != '') {
	                            $color = $skin->values['bgline2'];

	                            $i = -1;
	                            $a_res = array();

	                            // comptes client
	                            $rs = $db->query("
	                                SELECT  cli.code_client,
	                                        cli.nom AS nom,
	                                        cli.adr1 AS adr1,
	                                        cli.adr2 AS adr2,
	                                        cli.cp AS cp,
	                                        cli.ville AS ville,
	                                        g.id,
	                                        g.label
	                                FROM    dims_mod_cata_client cli
	                                LEFT JOIN	dims_group g
	                                ON			cli.dims_group = g.id_group
	                                WHERE   (
	                                            cli.code_client = '$filtre' OR
	                                            cli.nom LIKE '%$filtre%'
	                                        )
	                                AND     cli.code_client <> 'INTERNET'");
	                            while ($row = $db->fetchrow($rs)) {
									if ($a_res[$i]['code_client'] != $row['code_client']) $i++;

									if (!isset($a_res[$i]['groups'])) {
										$a_res[$i] = array(
											'code_client' => $row['code_client'],
											'nom' => $row['nom'],
											'adr1' => $row['adr1'],
											'adr2' => $row['adr2'],
											'cp' => $row['cp'],
											'ville' => $row['ville'],
											'groups' => array()
											);
									}
									if ($row['id'] != '') $a_res[$i]['groups'][$row['id']] = $row['label'];
	                            }

	                            // adresses de facturation
	                            $rs = $db->query("
	                                SELECT  *
	                                FROM    dims_mod_cata_facturation
	                                WHERE   nom LIKE '%$filtre%'");
	                            while ($row = $db->fetchrow($rs)) {
	                                $a_res[] = $row;
	                            }

	                            if (sizeof($a_res)) {
	                                foreach ($a_res as $row) {
	                                    $color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
	                                    $ref = (empty($row['code_client'])) ? 'F'.$row['id'] : $row['code_client'];

	                                    $adrfact = addslashes($row['nom']).'<br>'.addslashes($row['adr1']).'<br>';
	                                    if ($row['adr2'] != '') $adrfact .= addslashes($row['adr2']).'<br>';
	                                    $adrfact .= addslashes($row['cp']).' '.addslashes($row['ville']);

	                                    $attach_btn = (empty($row['groups'])) ? "<a href=\"$scriptenv?part=clients&action=attach_client&code_client_enfant=$code_client&code_client_parent=$ref\"><img src=\"./common/modules/catalogue/img/link.png\" alt=\"Attacher à ce client\" border=\"0\"></a>" : '';

	                                    echo "
	                                        <tr bgcolor=\"$color\">
	                                            <td colspan=\"3\">
	                                                <table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">
	                                                <tr>
	                                                    <td width=\"1%\"><b>$ref</b></td>
	                                                    <td width=\"98%\" onmouseover=\"javascript:dims_showpopup('$adrfact','',event);this.style.cursor='pointer';\" onmouseout=\"javascript:dims_hidepopup();this.style.cursor='default';\">
	                                                        {$row['nom']}
	                                                    </td>
	                                                    <td width=\"1%\">$attach_btn</td>
	                                                </tr>";
										if (!empty($row['groups'])) {
											foreach ($row['groups'] as $id => $label) {
												echo "
													<tr bgcolor=\"$color\">
														<td>&nbsp;</td>
														<td>$label</td>
														<td><a href=\"$scriptenv?part=clients&action=attach_client&code_client_enfant=$code_client&code_client_parent=$ref&id_group=$id\"><img src=\"./common/modules/catalogue/img/link.png\" alt=\"Attacher à ce client\" border=\"0\"></a></td>
													</tr>";
											}
										}
										echo "
	                                                </table>
	                                            </td>
	                                        </tr>";
	                                }
	                            }
	                            else {
	                                echo "
	                                    <tr bgcolor=\"{$skin->values['bgline1']}\">
	                                        <td colspan=\"3\">Aucun client trouvé</td>
	                                    </tr>";
	                            }
	                        }
	                        ?>
	                        </table>
	                        </form>
	                        <?
	                    echo $skin->close_simplebloc();
	                    ?>
	                </td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" class="button" value="Annuler" onclick="document.location.href='<?php echo $dims->getScriptEnv();?>';" />
						<input type="button" class="button" value="Enregistrer" onclick="javascript:document.cli_form.submit();">
					</td>
				</tr>
			</table>
			<?php
			echo $skin->close_simplebloc();
			break;
		case 'create_account':
			if (isset($_REQUEST['CREF'])) {
				$client = new cata_client();
				$client->openByCode($_REQUEST['CREF']);
				$login = $_REQUEST['CREF'];

				echo $skin->open_simplebloc('Valider le login');
					?>
					<script language="JavaScript">
						function test_email(field) {
							var ok = false;
							if(field.value != '') ok = dims_validatefield('Email',document.login_form.email,'email');
							else ok = confirm("Attention !\nVous n'avez pas spécifié l'email du gestionnaire de compte.\nIl ne recevra donc pas d'email l'informant de la création du compte.\nEtes-vous sûr(e) de vouloir faire cela ?");
							return ok;
						}
					</script>

					<form name='login_form' action='<?php echo $dims->getScriptEnv(); ?>' method='post'>
					<input type='Hidden' name='op' value='clients' />
					<input type='Hidden' name='action' value='save_account' />
					<input type='Hidden' name='CREF' value='<?php echo $_REQUEST['CREF']; ?>' />
					<table cellpadding="2" cellspacing="0">
						<?php
						if (isset($msg)) {
							echo "<tr><td align=\"center\" colspan=\"2\"><font style='color:red'>{$err_msg[$msg]}</font></td></tr>";
						}
						?>
						<tr>
							<td>&nbsp;Login du gestionnaire de compte :</td>
							<td><input class='WebText' type='Text' name='login' value='<? echo $login; ?>'></td>
						</tr>
						<tr>
							<td>&nbsp;Mot de passe du gestionnaire de compte :</td>
							<td>
								<input class='WebText' type='Text' name='password'>
								Si le mot de passe n'est pas spécifié, le système en génèrera un.
							</td>
						</tr>
						<tr>
							<td>&nbsp;Nom du gestionnaire de compte :</td>
							<td><input class='WebText' type='Text' name='lastname'></td>
						</tr>
						<tr>
							<td>&nbsp;Prénom du gestionnaire de compte :</td>
							<td><input class='WebText' type='Text' name='firstname'></td>
						</tr>
						<tr>
							<td>&nbsp;Email du gestionnaire de compte :</td>
							<td><input class='WebText' type='Text' name='email'></td>
						</tr>
						<tr>
							<td>&nbsp;Copie Jointe :</td>
							<td><input class='WebText' type='Text' name='copie_jointe'></td>
						</tr>
						<tr>
							<td colspan="2">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<input CLASS='Button' type='Button' value='Valider' onClick="javascript:if(test_email(document.login_form.email)) document.login_form.submit();">
											<input CLASS='Button' type='Button' value='Annuler' onClick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>';">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</form>
					</table>
					<?php
				echo $skin->close_simplebloc();
			}
			break;
        case 'save_account':
			// recherche du groupe courant
			$rs = $db->query('SELECT wg.id_group FROM dims_workspace_group wg INNER JOIN dims_group g ON g.id = wg.id_group WHERE wg.id_workspace = '.$_SESSION['dims']['workspaceid'].' LIMIT 0, 1');
			if ($db->numrows($rs)) {
				$row = $db->fetchrow($rs);
				$clients_group = $row['id_group'];
			}
			else {
				$clients_group = _CLIENTS_GROUP;
			}

			$login			= dims_load_securvalue('login', dims_const::_DIMS_CHAR_INPUT, false, true);
			$CREF			= dims_load_securvalue('CREF', dims_const::_DIMS_CHAR_INPUT, false, true);
			$password		= dims_load_securvalue('password', dims_const::_DIMS_CHAR_INPUT, false, true);
			$firstname		= dims_load_securvalue('firstname', dims_const::_DIMS_CHAR_INPUT, false, true);
			$lastname		= dims_load_securvalue('lastname', dims_const::_DIMS_CHAR_INPUT, false, true);
			$email			= dims_load_securvalue('email', dims_const::_DIMS_CHAR_INPUT, false, true);
			$copie_jointe	= dims_load_securvalue('copie_jointe', dims_const::_DIMS_CHAR_INPUT, false, true);

            if (!empty($login)) {
				// On vérifie que le login n'existe pas
				$sql = "SELECT id FROM dims_user WHERE login = '$login'";
				$db->query($sql);
				if ($db->numrows()) {
					  dims_redirect($dims->getScriptEnv()."?part=create_account&CREF={$CREF}&msg=3");
				}
				else {
					include_once './common/modules/system/class_group.php';
					include_once './common/modules/system/class_user.php';
					include_once './common/modules/system/class_group_user.php';
					include_once './common/modules/system/class_module_group.php';

					// Génération du mot de passe du compte si non fourni
					if (trim($password) == '') $password = passgen();

					// Création du groupe
					$client = new cata_client();
					// $client->open($CREF);

					$group = new group();
					$group->open($clients_group);
					$child = $group->createchild();

					$child->fields['label'] = $client->fields['CNOM'];
					$child->fields['code'] = $client->fields['CREF'];
					$groupid = $child->save();

					$client->fields['id_group'] = $child->fields['id'];
					$client->fields['adminlogin'] = $login;
					$client->fields['adminpwd'] = $password;
					$client->fields['limite_budget'] = 0;
					$client->fields['date_creation'] = dims_createtimestamp();
					$client->save();

					// Création du gestionnaire de groupe
					$user = new user();
					$user->fields['id_type'] = 0;
					$user->fields['login'] = $login;
					$user->fields['password'] = md5($password);
					$user->fields['email'] = $_REQUEST['email'];
					$user->fields['firstname'] = $_REQUEST['firstname'];
					$user->fields['lastname'] = $_REQUEST['lastname'];
					$user->save();

					$group_user = new group_user();
					$group_user->fields['id_user'] = $user->fields['id'];
					$group_user->fields['id_group'] = $child->fields['id'];
					$group_user->fields['adminlevel'] = _DIMS_ID_LEVEL_PURCHASERESP;
					$group_user->save();

					include_once './include/functions/mail.php';

					// Envoi d'un mail si une adresse a été donnée
					if (isset($email) && $email != '') {
						$from[0]['name'] = _MAIL_FROM;
						$from[0]['address'] = _MAIL_ADDRESS;

						$to[0]['name'] = '';
						$to[0]['address'] = $email;

						if (trim($copie_jointe) != '') {
							$to[1]['name'] = '';
							$to[1]['address'] = $copie_jointe;
						}

						$subject = (_MAIL_ACCOUNT_SUBJECT) ? _MAIL_ACCOUNT_SUBJECT : 'Création de votre compte Internet';

						$message = str_replace("<SOCIETE>", $client->fields['CNOM'], _MAIL_ACCOUNT_CONTENT);
						$message = str_replace("<NOM>", "{$user->fields['firstname']} {$user->fields['lastname']}", $message);
						$message = str_replace("<PASSWD>", $password, $message);
						$message = str_replace("<LOGIN>", $login, $message);
						dims_send_mail($from, $to, $subject, $message);
					}
				}
            }

			dims_redirect($dims->getScriptEnv()."?part=clients&filtre=".$_SESSION['catalogue']['filtreClients']);
	        break;
        case 'attach_client':
        	$code_client_enfant = dims_load_securvalue('code_client_enfant', dims_const::_DIMS_CHAR_INPUT, true, true);
        	$code_client_parent = dims_load_securvalue('code_client_parent', dims_const::_DIMS_CHAR_INPUT, true, true);

            if (!empty($code_client_enfant) && !empty($code_client_parent)) {
                include_once './common/modules/catalogue/include/class_client_ratt.php';
                $cratt = new client_ratt();
                $cratt->open($code_client_enfant, $code_client_parent);
                if (isset($id_group)) $cratt->fields['id_group'] = $id_group;
                $cratt->fields['valid_oblig'] = 1;
                $cratt->save();
            }
            dims_redirect("$scriptenv?part=clients&action=modify&code_client=$code_client_enfant");
	        break;
        case 'detach_client':
        	$code_client_enfant = dims_load_securvalue('code_client_enfant', dims_const::_DIMS_CHAR_INPUT, true, true);
        	$code_client_parent = dims_load_securvalue('code_client_parent', dims_const::_DIMS_CHAR_INPUT, true, true);

            if (!empty($code_client_enfant) && !empty($code_client_parent)) {
                include_once './common/modules/catalogue/include/class_client_ratt.php';
                $cratt = new client_ratt();
                $cratt->open($code_client_enfant, $code_client_parent);
                $cratt->delete();
            }
            dims_redirect("$scriptenv?part=clients&action=modify&code_client=$code_client_enfant");
	        break;
		// case 'view':
		// 	include_once './common/modules/catalogue/public_clients_view.php';
		// 	break;
		case 'use':
			if ($dims->isAdmin()) {
				$user_id = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, false);
				$group_id = dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, false);
				$code_client = dims_load_securvalue('code_client', dims_const::_DIMS_NUM_INPUT, true, false);
				$_SESSION['dims']['userid'] = $user_id;
				$_SESSION['catalogue']['iwasadmin'] = true;
				$_SESSION['catalogue']['selected_cref'] = $code_client;
				$_SESSION['catalogue']['selected_groupid'] = $group_id;
                                $_SESSION['catalogue']['panier']['articles'] = array();
                                $_SESSION['catalogue']['panier']['montant'] = 0;
				if (_CATA_PANIER == 'cookie') {
					panier2cookie();
				}
				elseif (_CATA_PANIER == 'bdd') {
					panier2bdd();
				}
				dims_redirect('/index.php?reloadsession=1');
			}
			break;
        case 'import_file':
            if ($dims->isAdmin()) {
                ini_set('max_execution_time',0);
                ini_set('memory_limit',"1024M");
                $code_client = dims_load_securvalue('code_client', dims_const::_DIMS_NUM_INPUT, true, false);

                if (!empty($_FILES['importFile']) && !$_FILES['importFile']['error']) {
                        $articles=array();

                        // on charge l'ensemble des articles
                        // ,fl.label as libellefamille
                         $res=$db->query("
	                        SELECT  distinct a.reference,al.label,a.cond,'' as prix,a.gencode

	                        FROM    dims_mod_cata_article as a
                                INNER JOIN dims_mod_cata_article_lang as al on al.reference=a.reference
                                LEFT JOIN  dims_mod_cata_article_famille as af
                                ON  af.id_article=a.id_article
                                LEFT JOIN  dims_mod_cata_famille_lang as fl
                                ON  fl.id_famille_1=af.id_famille
                                "); // ORDER BY libellefamille

	                    while ($row = $db->fetchrow($res)) {
	                        $articles[$row['reference']]=$row;
	                    }

                            require_once 'Spreadsheet/Excel/Writer.php';

                            ob_end_clean();

                            // Creating a workbook
                            $workbook = new Spreadsheet_Excel_Writer();

                            // sending HTTP headers
                            $workbook->send('articles'.$code_client.'.xls');

                            // Creating a worksheet
                            $worksheet =& $workbook->addWorksheet('Articles');

                            $headers=array();
                            $headers[]='Réf';
                            $headers[]='Désignation';
                            $headers[]='Colisage';
                            $headers[]='Prix';
                            $headers[]='Gencode';

                            include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
                            $article = new article();

                            $format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
                            $format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

                            foreach ($headers as $col => $field) {
                                if ($col==1)
                                    $worksheet->setColumn($col, $col, 70); //voir peut etre pour agrandir en fonction des champs
                                else
                                    $worksheet->setColumn($col, $col, 20); //voir peut etre pour agrandir en fonction des champs
                                    $worksheet->writeString($line, $col, utf8_decode($field), $format_title);
                            }

                            include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client.php';
                            include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';

                            $obj_cli = new cata_client();
                            $obj_cli->openByCode($code_client);

                            $obj_cli_cplmt = new cata_client_cplmt();
                            $obj_cli_cplmt->open($obj_cli->fields['id_client']);

                            $handle = @fopen($_FILES['importFile']['tmp_name'], 'r');

                            if ($handle) {
                                $line=1;
                                while (!feof($handle)) {
                                    $buffer = fgets($handle, 4096);
                                    $chp = explode(';', $buffer);
                                    $chp[0]=trim($chp[0]);

                                    if (isset($articles[$chp[0]])) {
                                        $i=0;
                                        foreach ($articles[$chp[0]] as $col => $field) {
                                            if ($col=='prix') {
                                                // ajout du prix
                                                $article->open($chp[0]);
                                                $prix=variante_getprixarticle($article, 1, $false, $obj_cli , $obj_cli_cplmt );
                                                $worksheet->writeString($line, $i, utf8_decode($prix), $format);
                                             //   echo $prix;
                                            }
                                            else {
                                                $worksheet->writeString($line, $i, utf8_decode($field), $format);
                                               // echo $field;
                                            }
                                           // echo " ";

                                            $i++;
                                        }
                                        //echo "<br>";
                                        $line++;
                                    }
                                }

                                // on a lu l'ensemble
                            }
                            // Let's send the file
                            $workbook->close();
                            die('');
                }

            }
            break;
        case 'exportprix':

            if ($dims->isAdmin()) {
                $user_id = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$group_id = dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, false);
                $code_client = dims_load_securvalue('code_client', dims_const::_DIMS_NUM_INPUT, true, false);
                echo $skin->open_simplebloc('Export d\'articles');
            ?>
              <table cellpadding="5" cellspacing="0">
			<tr>
				<td valign="top">
					<form action="<?=$scriptenv;?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="op" value="admin_imports" />
					<input type="hidden" name="action" value="import_file" />
					<input type="hidden" name="keepCurrent" value="0" />
                                        <input type="hidden" name="code_client" value="<? echo $code_client; ?>" />

					<strong><em>S&eacute;l&eacute;ction du fichier d'articles :</strong></em>
					<input class="text" type="file" name="importFile" />
					<input class="button" type="submit" value="Importer les articles !" /><br/>

					<br/>


					</form>
				</td>
			</tr>
			</table>
            <?
            echo $skin->close_simplebloc();
            }
            break;
        default:
            echo $skin->open_simplebloc('Liste des clients');
				if (!empty($filtre)) {
				  $_SESSION['catalogue']['filtreClients'] = $filtre;
				}
                ?>
                <form name="clients_form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
                <input type="hidden" name="op" value="clients">

                <table cellpadding="2" cellspacing="1" width="100%" border="0">
                    <!--<tr>
                      <td colspan="9" align="center">[ <a href="<?php echo $dims->getScriptEnv()."?part=clients&action=add" ?>">Ajouter un Client</a> ]</td>
					</tr-->
                    <tr>
                    	<td colspan="9" align="center">
                    		<input class="text" type="text" name="filtre" value="<?php echo $_SESSION['catalogue']['filtreClients'] ?>">
                    		<input class="button" type="submit" value="Rechercher">
                    	</td>
                    </tr>
                    <tr bgcolor="<? echo $skin->values['bgline1']; ?>">
						<td nowrap><b>&nbsp;</b></td>
						<td nowrap><b>&nbsp;Code client&nbsp;</b></td>
                        <td nowrap><b>&nbsp;Nom&nbsp;</b></td>
                        <td nowrap><b>&nbsp;Adresse&nbsp;</b></td>
                        <td nowrap><b>&nbsp;CP&nbsp;</b></td>
                        <td nowrap><b>&nbsp;Ville&nbsp;</b></td>
                        <!-- <td nowrap><b>&nbsp;Compte Web&nbsp;</b></td> -->
                        <td nowrap><b>&nbsp;Login&nbsp;</b></td>
                        <td nowrap><b>&nbsp;Pwd&nbsp;</b></td>
                    </tr>
                    <?php
                    if (!empty($_SESSION['catalogue']['filtreClients'])) {
                      $color = '';

      //                 $sql = "
						// SELECT c.*, g.id AS id_group, d.adminlogin, d.adminpwd, d.limite_budget
						// FROM dims_mod_vpc_client c
						// LEFT JOIN dims_mod_vpc_client_detail d
						// ON c.CREF = d.CREF
						// LEFT JOIN dims_group g
						// ON d.id_group = g.id
						// WHERE ( c.CNOM LIKE '%{$_SESSION['catalogue']['filtreClients']}%' OR c.CREF = '{$_SESSION['catalogue']['filtreClients']}' )
						// AND c.CREF <> 'INTERNET'
						// ORDER BY c.CNOM";
					$sql = '
						SELECT 	*
						FROM 	dims_mod_cata_client
						WHERE ( nom LIKE \'%'.$_SESSION['catalogue']['filtreClients'].'%\' OR code_client = \''.$_SESSION['catalogue']['filtreClients'].'\' )
						ORDER BY nom';
                      $res = $db->query($sql);
                      while ($fields = $db->fetchrow($res)) {
                      	foreach ($fields as $k => $v) { $fields[$k] = stripslashes($v); }

                          ($color == $skin->values['bgline1']) ? $color = $skin->values['bgline2'] : $color = $skin->values['bgline1'];
                          // if ($fields['id_group'] != 0) {
                          // $uselink="<a HREF=\"".$dims->getScriptEnv()."?part=clients&action=use&user_id=".$fields['dims_user']."&code_client=".$fields['code_client']."&group_id=".$fields['dims_group']."\"><img src=\"./common/modules/catalogue/img/ico_user_orange.gif\" border=\"0\" ALT=\"Utiliser\"></A>";
                          $uselink="<a HREF=\"".$dims->getScriptEnv()."?part=clients&action=exportprix&user_id=".$fields['dims_user']."&code_client=".$fields['code_client']."&group_id=".$fields['dims_group']."\"><img src=\"./common/img/export.png\" border=\"0\" ALT=\"Export\"></A>&nbsp;";
                          $uselink.="<a HREF=\"".$dims->getScriptEnv()."?part=clients&action=use&user_id=".$fields['dims_user']."&code_client=".$fields['code_client']."&group_id=".$fields['dims_group']."\"><img src=\"./common/img/go-next.png\" border=\"0\" ALT=\"Utiliser\"></A>";
                          //dims_print_r($fields);die();
                          // &nbsp;<a href=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?part=clients&action=delete&code_client=".addslashes($fields['code_client'])."','Êtes vous certain de vouloir supprimer le client ".addslashes($fields['code_client'])."');\"><img alt=\"Supprimer\" border=\"0\" src=\"./common/modules/catalogue/img/ico_delete.gif\"></a>
                                  echo "
                                      <TR BGCOLOR='$color'>
                                        <TD><a href=\"".$dims->getScriptEnv()."?part=clients&action=modify&code_client={$fields['code_client']}\"><img alt=\"Modifier\" border=\"0\" src=\"./common/modules/catalogue/img/ico_modify.gif\"></a> ".$uselink."</TD>
                                        <TD><B>{$fields['code_client']}</B></TD>
                                          <TD><B>{$fields['nom']}</B></TD>
                                          <TD><B>{$fields['adr1']} {$fields['adr2']}</B></TD>
                                          <TD><B>{$fields['cp']}</B></TD>
                                          <TD><B>{$fields['ville']}</B></TD>
                                          <!--TD ALIGN=CENTER>
											<b><a href='".$dims->getScriptEnv()."?part=clients&action=view&code_client={$fields['code_client']}'>Voir</a></b>
										  </TD-->
                                          <TD><B>{$fields['librcha1']}</B></TD>
                                          <TD><B>{$fields['librcha2']}</B></TD>
                                      </TR>";
            //               }
            //               else {
            //                       echo "
            //                           <TR BGCOLOR='$color'>
										  // <TD><a href=\"".$dims->getScriptEnv()."?part=clients&action=modify&code_client={$fields['CREF']}\"><img alt=\"Modifier\" border=\"0\" src=\"./common/modules/catalogue/img/ico_modify.gif\"></a>&nbsp;<a href=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?part=clients&action=delete&code_client=".addslashes($fields['CREF'])."','Êtes vous certain de vouloir supprimer le client ".addslashes($fields['CREF'])."');\"><img alt=\"Supprimer\" border=\"0\" src=\"./common/modules/catalogue/img/ico_delete.gif\"></a></TD>
										  // <TD><B>{$fields['CREF']}</B></TD>
            //                               <TD>{$fields['CNOM']}</TD>
            //                               <TD>{$fields['CRUE']} {$fields['CAUX']}</TD>
            //                               <TD>{$fields['CCPTL']}</TD>
            //                               <TD>{$fields['CVIL']}</TD>
            //                               <TD ALIGN=CENTER><A HREF='".$dims->getScriptEnv()."?part=clients&action=create_account&CREF={$fields['CREF']}'>Créer</A></TD>
            //                               <TD></TD>
            //                               <TD></TD>
            //                           </TR>";
            //               }

                      }
                    }
                    ?>
                </form>
                </table>

                <script language="JavaScript">
                	document.clients_form.filtre.focus();
	                </script>
                <?php
            echo $skin->close_simplebloc();
        break;
    }
  ?>
  </td>
</tr>
</table>
