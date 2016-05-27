<?php
if ($_SESSION['dims']['connected']) {
	ob_start();

	$rs = $db->query("
		SELECT	*
		FROM	dims_mod_cata_ecrits
		WHERE	codcpt = '".$_SESSION['catalogue']['code_client']."'
		ORDER BY datper DESC");
	if ($db->numrows($rs)) {
		$i = 0;

		while ($row = $db->fetchrow($rs)) {
			$i++;
			$catalogue['extraits'][] = array(
				'date'		=> substr($row['datper'], 6, 2) .'/'. substr($row['datper'], 3, 2) .'/'. substr($row['datper'], 0, 2),
				'libelle'	=> $row['libelle'],
				'codrgt'	=> $row['codrgt'],
				'debit'		=> ($row['type'] == 'DEBIT') ? '&nbsp;' : $row['montant'],
				'credit'	=> ($row['type'] == 'CREDIT') ? '&nbsp;' : $row['montant'],
				'class'		=> 'ligne'.($i % 2)
				);
		}
		$smarty->assign('catalogue', $catalogue);
	}
	$smarty->assign('tpl_name', 'extraits_compte');
//	$smarty->debugging = true;

    $page['TITLE'] = 'Extraits de compte';
    $page['META_DESCRIPTION'] = 'Extraits de compte';
    $page['META_KEYWORDS'] = 'extraits, compte';
    $page['CONTENT'] = '';

	ob_end_clean();
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
