<?php
ob_start();

// CHARGEMENT DE L'ENVIRONNEMENT DE L'UTILISATEUR
$user = new user();
$user->open($_SESSION['dims']['userid']);
$groups = $user->getgroups(false);

//recherche des groupes enfants
$group = new group();
$group->open(key($groups));
$lst_group = implode(',', array_merge($groups, $group->getgroupchildrenlite()));


if (isset($_POST['format'])) {
	require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';

	// liste des champs numeriques qui vont etre alignes a droite
	$a_numeriques = array(
		'montant_ht',
		'montant_ttc',
		'nb_article',
		'budget_montant'
		);

	// liste des champs monaitaires qui doivent avoir 2 decimales apres la virgule
	$a_monaitaires = array(
		'montant_ht',
		'montant_ttc',
		'budget_montant'
		);

	if ($_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_SYSTEMADMIN && isset($_SESSION['catalogue']['code_client'])) $client = $_SESSION['catalogue']['code_client'];


	$service 				= dims_load_securvalue('service', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$utilisateur 			= dims_load_securvalue('utilisateur', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$datedeb 				= dims_load_securvalue('datedeb', dims_const::_DIMS_CHAR_INPUT, false, true, true);
	$datefin 				= dims_load_securvalue('datefin', dims_const::_DIMS_CHAR_INPUT, false, true, true);
	$articles 				= dims_load_securvalue('articles', dims_const::_DIMS_CHAR_INPUT, false, true, true);
	$groupby_client 		= dims_load_securvalue('groupby_client', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$groupby_service 		= dims_load_securvalue('groupby_service', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$groupby_utilisateur 	= dims_load_securvalue('groupby_utilisateur', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$groupby_budget 		= dims_load_securvalue('groupby_budget', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$groupby_jour 			= dims_load_securvalue('groupby_jour', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$groupby_commande 		= dims_load_securvalue('groupby_commande', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$groupby_article 		= dims_load_securvalue('groupby_article', dims_const::_DIMS_NUM_INPUT, false, true, true);
	$format 				= dims_load_securvalue('format', dims_const::_DIMS_CHAR_INPUT, false, true, true);



	$fields = $where = $groupby = $orderby = '';

	$datedeb = dims_local2timestamp($datedeb, '00:00:00');
	$datefin = dims_local2timestamp($datefin, '23:59:59');

	if ($datedeb) $where .= " AND cde.date_validation >= '$datedeb'";
	if ($datefin) $where .= " AND cde.date_validation <= '$datefin'";


	if ($_SESSION['session_adminlevel'] == _DIMS_ID_LEVEL_STATISTICS) {
		$lstgroups = $lst_group;
		$where .= " AND cde.id_group IN ($lstgroups)";
	}
	// TODO A FILTRER
	// if ($_POST['client']) $where .= " AND cde.ref_client = '".$_POST['client']."'";

	if ($articles) 			$where .= " AND lignes.ref IN ('".str_replace(';', "','", $articles)."') ";

	// TODO stats par service fonctionnent pas encore
	if ($service) 			$where .= " AND cde.id_service = '$service'";
	if ($utilisateur) 		$where .= " AND cde.id_user = '$utilisateur'";

	// TODO A vérifier, mais a premiere vue, c'est faux
	// if (isset($budget) && $budget) $where .= " AND cde.id_user = '$utilisateur'";

	if ($groupby_client) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'cde.code_client';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'cde.code_client';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= 'cde.code_client, cli.nom AS client_nom';
	}

	if ($groupby_service) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'cde.id_service';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'cde.id_service';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= 'grp.label AS service';
	}

	if ($groupby_utilisateur) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'cde.id_user';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'cde.id_user';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= "cde.user_name AS utilisateur";
	}

	if ($groupby_budget) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'cde.id_budget';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'cde.id_budget';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= "budget.code AS budget_code, budget.valeur AS budget_montant";
	}

	if ($groupby_commande) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'cde.id_cde';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'cde.id_cde';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= "
			cde.id_cde AS commande_id,
			cde.libelle AS commande_libelle,
			CONCAT(SUBSTRING(cde.date_validation, 7, 2), '/', SUBSTRING(cde.date_validation, 5, 2), '/', LEFT(cde.date_validation, 4)) AS commande_date_validation,
			CONCAT(SUBSTRING(cde.date_validation, 9, 2), ':', SUBSTRING(cde.date_validation, 11, 2)) AS commande_heure_validation";
	}

	if ($groupby_article) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'art.reference';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'art.reference';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= 'art.reference AS article_reference, art.label AS article_designation';
	}

	if ($groupby_jour) {
		$groupby .= ($groupby!='') ? ', ' : '';
		$groupby .= 'cde.date_validation';
		$orderby .= ($orderby!='') ? ', ' : '';
		$orderby .= 'LEFT(cde.date_validation, 8)';
		$fields .= ($fields!='') ? ', ' : '';
		$fields .= "CONCAT(SUBSTRING(cde.date_validation, 7, 2), '/', SUBSTRING(cde.date_validation, 5, 2), '/', LEFT(cde.date_validation, 4)) AS commande_date_validation";
	}

	if ($fields == '') {
		$fields = '
			cde.code_client,
			cli.nom AS nom_client,
			grp.label AS service,
			user.lastname as utilisateur_nom,
			user.firstname AS utilisateur_prenom,
			user.login AS utilisateur_login,
			cde.id_cde AS commande_id,
			cde.libelle AS commande_libelle,
			CONCAT(SUBSTRING(cde.date_validation, 7, 2), \'/\', SUBSTRING(cde.date_validation, 5, 2), \'/\', LEFT(cde.date_validation, 4)) AS commande_date_validation,
			CONCAT(SUBSTRING(cde.date_validation, 9, 2), \':\', SUBSTRING(cde.date_validation, 11, 2)) AS commande_heure_validation,
			art.reference AS article_reference,
			art.label AS article_designation,
			budget.code AS budget_code,
			budget.valeur AS budget_montant';
		$groupby = 'cde.code_client, grp.id, user.id, cde.id_cde, art.reference';
		$orderby = 'cde.code_client, grp.id, user.id, cde.id_cde, art.reference';
	}

	$fields .= ",
		SUM(lignes.pu_ht * lignes.qte) AS montant_ht,
		SUM(lignes.qte) AS nb_article";

	if ($groupby) {
		$orderby = " ORDER BY $orderby ";
		$groupby = " GROUP BY $groupby ";
	}


	$select = "
		SELECT 	$fields

		FROM 	dims_mod_cata_cde cde

		INNER JOIN 	dims_mod_cata_cde_lignes lignes
		ON 			lignes.id_cde = cde.id_cde

		INNER JOIN 	dims_mod_cata_client cli
		ON 			cli.id_client = cde.id_client

		INNER JOIN 	dims_group grp
		ON 			grp.id = cde.id_service

		INNER JOIN 	dims_user user
		ON 			user.id = cde.id_user

		INNER JOIN 	dims_mod_cata_article art
		ON 			art.id = lignes.id_article

		LEFT JOIN 	dims_mod_vpc_budget budget
		ON 			budget.id = cde.id_budget

		WHERE 	cde.code_client = '{$_SESSION['catalogue']['code_client']}'
		AND 	cde.etat = ".commande::_STATUS_VALIDATED."

		$where
		$groupby
		$orderby";

	$db->query($select);

	ob_end_clean();
	switch ($_POST['format']) {
		case 'xls';
			require_once 'Spreadsheet/Excel/Writer.php';

			// Creating a workbook
			$workbook = new Spreadsheet_Excel_Writer();

			// sending HTTP headers
			$workbook->send("stats.xls");

			$format_title 		=& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
			$format_rubtitle 	=& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 14));
			$format 			=& $workbook->addFormat( array( 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));
			$format_numeric 	=& $workbook->addFormat( array( 'Align' => 'right', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));
			$format_prix 		=& $workbook->addFormat( array( 'Align' => 'right', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10, 'NumFormat' => '0.00'));

			$l=0;

			$worksheet =& $workbook->addWorksheet("stats");

			while ($row = $db->fetchrow()) {
				$c = 0;
				foreach ($row as $var => $value) {
					if ($l == 0) {
						$worksheet->write($l, $c, $var, $format_title);
					}
					if (in_array($var, $a_numeriques)) {
						if (in_array($var, $a_monaitaires)) {
							$worksheet->write($l + 1, $c++, $value, $format_prix);
						} else {
							$worksheet->write($l + 1, $c++, $value, $format_numeric);
						}
					} else {
						$worksheet->write($l + 1, $c++, $value, $format);
					}
				}
				$l++;
			}

			$workbook->close();
			break;
		case 'html';
			header("Cache-control: private");
			header("Content-type: text/html");
			header("Content-Disposition: attachment; filename=stats.html");
			header("Pragma: public");

			$content = '';
			$title = '';
			while ($row = $db->fetchrow()) {
				$line = '';
				foreach ($row as $var => $value) {
					if ($content == '') $title .= "<th><strong>$var</strong></th>";
					if (in_array($var, $a_numeriques)) {
						if (in_array($var, $a_monaitaires)) {
							$line .= "<td align=\"right\">".catalogue_formateprix($value)."</td>";
						} else {
							$line .= "<td align=\"right\">$value</td>";
						}
					} else {
						$line .= "<td>$value</td>";
					}
				}
				$content .= "<tr>$line</tr>";
			}

			$styles = 'body,td,th,h1,h2 {font-family: sans-serif;}table{border-collapse:collapse;padding:4px;}td,th{border:1px solid #000;padding:4px;}';

			echo "<html><head><style type=\"text/css\">$styles</style></head><body><table><tr>$title</tr>$content</table></body></html>";
			break;
		case 'csv';
			header("Cache-control: private");
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=stats.csv");
			header("Pragma: public");

			$content = '';
			$title = '';
			while ($row = $db->fetchrow()) {
				$line = '';
				foreach ($row as $var => $value) {
					if ($content == '') {
						$title .= ($title!='') ? ';' : '';
						$title .= $var;
					}
					$line .= ($line!='') ? ';' : '';
					$line .= $value;
				}

				if ($content=='') $title .= "\r\n";
				$content .= "$line\r\n";
			}

			echo "$title$content";
			break;
	}

	while (@ob_end_flush());
	die();
}
?>


<table width="100%" height="330" cellpadding="0" cellspacing="0">
<tr bgcolor="#8BCE44">
	<td height="25" colspan="2">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="WebNavTitle">&nbsp;Statistiques</td>
		</tr>
		<tr bgcolor="#dddddd" height="1"><td></td></tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" width="400">
		<form action="<?=$dims->getScriptEnv();?>" method="POST">
		<input type="hidden" name="op" value="statistiques" />

		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div id="div_commandes" style="display:block">
					<table cellpadding="2" cellspacing="2">
					<tr>
						<td colspan="2"><b>Exporter les données</b></td>
					</tr>
					<?
					if ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_SYSTEMADMIN) {
						?>
						<tr>
							<td>Client&nbsp;:&nbsp;<select class="select" name="client"><option value="">(tous)</option>
							<?
								$select = 	"
											SELECT 	c.*
											FROM 	dims_mod_vpc_client c,
													dims_mod_vpc_client_detail cd,
													dims_group g
											WHERE	c.CREF = cd.CREF
											AND		cd.id_group = g.id
											ORDER BY c.CNOM
											";
								$db->query($select);

								while ($fields = $db->fetchrow()) echo "<option value=\"{$fields['CREF']}\">{$fields['CREF']} - {$fields['CNOM']}</option>";
							?>
							</select></td>
						</tr>
						<?
					} elseif ($_SESSION['session_adminlevel'] == _DIMS_ID_LEVEL_STATISTICS) {
						?>
						<tr>
							<td>Client&nbsp;:&nbsp;<select class="select" name="client"><option value="">(tous)</option>
							<?
								$select = 	"
											SELECT 	c.*
											FROM 	dims_mod_vpc_client c,
													dims_mod_vpc_client_detail cd,
													dims_group g
											WHERE	c.CREF = cd.CREF
											AND		cd.id_group = g.id
											AND		g.id IN ($lst_group)
											ORDER BY c.CNOM
											";
								$db->query($select);

								while ($fields = $db->fetchrow()) echo "<option value=\"{$fields['CREF']}\">{$fields['CREF']} - {$fields['CNOM']}</option>";
							?>
							</select></td>
						</tr>
						<tr>
							<td>Utilisateur&nbsp;:&nbsp;<select class="select" name="utilisateur"><option value="">(tous)</option>
							<?
								$select = 	"
											SELECT 	u.*
											FROM 	dims_group_user gu, dims_user u
											WHERE	gu.id_group in ($lst_group)
											AND		gu.id_user = u.id
											ORDER BY u.lastname, u.firstname
											";
								$db->query($select);

								while ($fields = $db->fetchrow()) echo "<option value=\"{$fields['id']}\">{$fields['lastname']} {$fields['firstname']} {$fields['login']}</option>";
							?>
							</select></td>
						</tr>
						<tr>
							<td>Budget&nbsp;:&nbsp;<select class="select" name="budget"><option value="">(tous)</option>
							<?
								$select = 	"
											SELECT 	*
											FROM 	dims_mod_vpc_budget
											WHERE	id_group in ($lst_group)
											ORDER BY en_cours DESC, code
											";
								$db->query($select);

								$encours = ($fields['en_cours']) ? 'en cours' : 'terminé';

								while ($fields = $db->fetchrow()) echo "<option value=\"{$fields['id']}\">{$fields['code']} - {$encours} - {$fields['valeur']} </option>";
							?>
							</select></td>
						</tr>
						<?
					} else {
						?>
						<tr>
							<td>Service&nbsp;:&nbsp;<select class="select" name="service"><option value="">(tous)</option>
							<?
								$select = 	"
											SELECT 	*
											FROM 	dims_group
											WHERE	id in ($lst_group)
											ORDER BY label
											";
								$db->query($select);

								while ($fields = $db->fetchrow()) echo "<option value=\"{$fields['id']}\">{$fields['label']}</option>";
							?>
							</select></td>
						</tr>
						<tr>
							<td>Utilisateur&nbsp;:&nbsp;<select class="select" name="utilisateur"><option value="">(tous)</option>
							<?
								$select = 	"
											SELECT 	u.*
											FROM 	dims_group_user gu, dims_user u
											WHERE	gu.id_group in ($lst_group)
											AND		gu.id_user = u.id
											ORDER BY u.lastname, u.firstname
											";
								$db->query($select);

								while ($fields = $db->fetchrow()) echo "<option value=\"{$fields['id']}\">{$fields['lastname']} {$fields['firstname']} {$fields['login']}</option>";
							?>
							</select></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td>Entre le <input type="text" name="datedeb" class="text" size="10">&nbsp;et le <input type="text" name="datefin" class="text" size="10">&nbsp;&nbsp;(format jj/mm/aaaa)</td>
					</tr>
					<tr>
						<td>Références Article&nbsp;:&nbsp;<input type="text" name="articles" class="text" size="30">&nbsp;&nbsp;(séparateur ";")</td>
					</tr>
					<tr>
						<td colspan="2">
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td nowrap>Regrouper par :&nbsp;&nbsp;</td>
							<?
							if ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_STATISTICS) {
								?>
								<td><input type="checkbox" id="groupby_client" name="groupby_client" value="1"></td><td><label for="groupby_client">Client</label>&nbsp;&nbsp;</td>
								<?
							}
							?>
							<td><input type="checkbox" id="groupby_service" name="groupby_service" value="1"></td><td><label for="groupby_service">Service</label>&nbsp;&nbsp;</td>
							<td><input type="checkbox" id="groupby_utilisateur" name="groupby_utilisateur" value="1"></td><td><label for="groupby_utilisateur">Utilisateur</label>&nbsp;&nbsp;</td>
							<td><input type="checkbox" id="groupby_budget" name="groupby_budget" value="1"></td><td><label for="groupby_budget">Budget</label>&nbsp;&nbsp;</td>
							<td></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="checkbox" id="groupby_jour" name="groupby_jour" value="1"></td><td><label for="groupby_jour">Jour</label>&nbsp;&nbsp;</td>
							<td><input type="checkbox" id="groupby_commande" name="groupby_commande" value="1"></td><td><label for="groupby_commande">Commande</label>&nbsp;&nbsp;</td>
							<td><input type="checkbox" id="groupby_article" name="groupby_article" value="1"></td><td><label for="groupby_article">Article</label></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td>Format d'export:&nbsp;&nbsp;</td>
							<td><input type="radio" id="format_html" name="format" value="html" checked></td><td><label for="format_html">HTML</label>&nbsp;&nbsp;&nbsp;</td>
							<td><input type="radio" id="format_csv" name="format" value="csv"></td><td><label for="format_csv">CSV</label>&nbsp;&nbsp;&nbsp;</td>
							<td><input type="radio" id="format_xls" name="format" value="xls"></td><td><label for="format_xls">XLS</label>&nbsp;&nbsp;&nbsp;</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input type="submit" class="button" value="Exporter"></td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
		</form>
		</table>
	</td>
	<td valign="top" align="left">
		<table width="100%" cellpadding="10" cellspacing="0">
		<tr>
			<td>
			L'outil "Statistiques" vous permet d'exporter facilement l'ensemble des données relatives aux commandes que vous avez passées sur notre site.
			<br><br>Regrouper par "Service", "Utilisateur", "Commande" ou "Référence Article" vous permet d'obtenir un total de commande pour le(s) critère(s) de regroupement choisi(s)
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<?php
$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));
ob_end_clean();
