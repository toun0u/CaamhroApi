<?php

$view = view::getInstance();
$view->setLayout('layouts/default_layout.tpl.php');

switch ($a) {
	default:
		$view->render('exports/index.tpl.php');
		break;
	case 'export_families':
		ob_start();

		header("Cache-control: private");
		header("Content-type: application/csv");
		header("Content-Disposition: inline; filename=familles.csv");
		header("Pragma: public");

		$fams = cata_getfamilys_adm();

		function getFamilyCSVLine($fams, $id_family) {
			echo $id_family, ';';
			$parents = str_replace('0;1', '', $fams['list'][$id_family]['parents']);
			if ($parents != '') {
				foreach (explode(';', $parents) as $p) {
					if ($p > 0) {
						echo '"', strip_tags($fams['list'][$p]['label']), '"', ';';
					}
				}
			}
			echo '"', strip_tags($fams['list'][$id_family]['label']), '"', "\r\n";
		}

		function exportFamiliesRec($fams, $id_parent) {
			if (isset($fams['tree'][$id_parent])) {
				foreach ($fams['tree'][$id_parent] as $f) {
					getFamilyCSVLine($fams, $f);
					exportFamiliesRec($fams, $f);
				}
			}
		}

		$id_parent = 1; // Catalogue
		exportFamiliesRec($fams, $id_parent);

		ob_end_flush();
		die();
		break;
	case 'export_articles':
		ob_start();

		header("Cache-control: private");
		header("Content-type: application/csv");
		header("Content-Disposition: inline; filename=articles.csv");
		header("Pragma: public");

		$sep = ';';

		echo
			'"Référence"', $sep,
			'"Désignation"', $sep,
			'"Prix TTC"', $sep,
			'"Taux TVA"', $sep,
			'"Page"', $sep,
			'"Marque"', $sep,
			'"Conditionnement"', $sep,
			'"Description"', $sep,
			'"Position"', $sep,
			'"Nouveauté"', $sep,
			'"Prix TTC 1"', $sep,
			'"Taux TVA 1"', $sep,
			'"Prix TTC 2"', $sep,
			'"Taux TVA 2"', $sep,
			'"Type"', $sep,
			'"Code fournisseur"', $sep,
			'"Date de parution"', $sep,
			'"Date de fin de commercialisation"', $sep,
			'"Référence de remplacement"', $sep,
			'"Numéro de famille"', "\r\n";

		$rs = $db->query('SELECT
				a.`reference`,
				a.`label`,
				a.`putarif_0`,
				tva.`tx_tva`,
				a.`page`,
				m.`libelle` AS marque,
				a.`cond`,
				a.`description`,
				1 AS `position`,
				a.`nouveaute`,
				a.`puttc_1`,
				a.`tx_tva_1`,
				a.`puttc_2`,
				a.`tx_tva_2`,
				a.`type_livre`,
				a.`ref_fournisseur`,
				a.`date_parution`,
				a.`date_fin_com`,
				a.`ref_remplacement`,
				af.`id_famille`
			FROM `dims_mod_cata_article` a

			LEFT JOIN `dims_mod_cata_article_famille` af
			ON af.`id_article` = a.`id`

			LEFT JOIN `dims_mod_cata_marque` m
			ON m.`id` = a.`marque`

			LEFT JOIN `dims_mod_cata_tva` tva
			ON tva.`id_tva` = a.`ctva`');

			// LIMIT 0, 10');

			// LEFT JOIN `dims_mod_cata_article_famille` af

		while ($row = $db->fetchrow($rs)) {
			foreach ($row as $k => $v) {
				$row[$k] = str_replace('"', '""', $v);
			}

			echo
				'"', $row['reference'], '"', $sep,
				'"', $row['label'], '"', $sep,
				'"', $row['putarif_0'], '"', $sep,
				'"', $row['tx_tva'], '"', $sep,
				'"', $row['page'], '"', $sep,
				'"', $row['marque'], '"', $sep,
				'"', $row['cond'], '"', $sep,
				'"', $row['description'], '"', $sep,
				'"', $row['position'], '"', $sep,
				'"', $row['nouveaute'], '"', $sep,
				'"', $row['puttc_1'], '"', $sep,
				'"', $row['tx_tva_1'], '"', $sep,
				'"', $row['puttc_2'], '"', $sep,
				'"', $row['tx_tva_2'], '"', $sep,
				'"', $row['type_livre'], '"', $sep,
				'"', $row['ref_fournisseur'], '"', $sep,
				'"', $row['date_parution'], '"', $sep,
				'"', $row['date_fin_com'], '"', $sep,
				'"', $row['ref_remplacement'], '"', $sep,
				'"', $row['id_famille'], '"', "\r\n";
		}
		ob_end_flush();
		die();
		break;

}
