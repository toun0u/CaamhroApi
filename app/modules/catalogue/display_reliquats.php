<?php
if ($_SESSION['dims']['connected']) {
	ob_start();

	// Reliquat
	$sql = 'SELECT
				b . id AS id_bl,
				b . date AS date_bl,
				bl_l . *
			FROM
				dims_mod_cata_bl b
			INNER JOIN
				dims_mod_cata_bl_lignes bl_l
				ON
					bl_l.numbl = b.numbl
			WHERE
				b.code_client = '.$_SESSION['catalogue']['code_client'].'
			AND
				bl_l.qte_rel > 0
			ORDER BY
				b.date DESC,
				b.numbl DESC';
	$rs = $db->query($sql);

	if ($db->numrows($rs)) {
		$i = 0;
		$catalogue['reliquats']['total'] = array( 'PU_HT' => 0, 'QTE_CDE' => 0, 'QTE_LIV' => 0, 'QTE_REL' => 0, 'TOTAL_HT' => 0 );

		while ($row = $db->fetchrow($rs)) {
			$i++;
			$date = dims_timestamp2local($row['date_bl']);

			$catalogue['reliquats']['lignes'][] = array(
				'NUMBL'		=> $row['numbl'],
				'DATEBL'	=> $date['date'],
				'REF' 		=> $row['ref_article'],
				'LABEL' 	=> $row['designation'],
				'PU_HT' 	=> catalogue_formateprix($row['puht']),
				'QTE_CDE' 	=> $row['qte_cde'],
				'QTE_LIV' 	=> $row['qte_liv'],
				'QTE_REL' 	=> ($row['qte_cde']) ? $row['qte_rel'] : 0,
				'TOTAL_HT'	=> catalogue_formateprix($row['puht'] * $row['qte_rel']),
				'VIEWLINK'	=> 'op=bonslivraison&action=view&numbl='.$row['numbl'],
				'CLASS'		=> 'ligne'.($i % 2)
			);

			$catalogue['reliquats']['total']['PU_HT'] 	+= $row['puht'];
			$catalogue['reliquats']['total']['QTE_CDE']	+= $row['qte_cde'];
			$catalogue['reliquats']['total']['QTE_LIV']	+= $row['qte_liv'];
			$catalogue['reliquats']['total']['QTE_REL']	+= ($row['qte_cde']) ? $row['qte_rel'] : 0;
			$catalogue['reliquats']['total']['TOT_HT']	+= $row['puht'] * $row['qte_rel'];
		}

		$catalogue['reliquats']['total']['PU_HT']		= catalogue_formateprix($catalogue['reliquats']['total']['PU_HT']);
		$catalogue['reliquats']['total']['TOTAL_HT']	= catalogue_formateprix($catalogue['reliquats']['total']['TOTAL_HT']);

		$smarty->assign('catalogue', $catalogue);
	}
	$smarty->assign('tpl_name', 'reliquats');

	$page['TITLE'] = 'Reliquats';
	$page['META_DESCRIPTION'] = 'Reliquats';
	$page['META_KEYWORDS'] = 'reliquats';
	$page['CONTENT'] = '';

	ob_end_clean();
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
