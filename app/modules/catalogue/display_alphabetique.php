<?php
$caractere = '';
$caractere = dims_load_securvalue('param', dims_const::_DIMS_CHAR_INPUT, true, true, true, $caractere,'A');
//$currpage = 0;
//$currpage = dims_load_securvalue('p', dims_const::_DIMS_NUM_INPUT, true, true, true,$page,0);

$alphabetique = array('classement' => array(), 'articles' => array());

$sql = 'SELECT LEFT(TRIM(LEADING \' \' FROM TRIM(LEADING \'"\' FROM TRIM(LEADING \'&\' FROM a.designation))), 1) as lettre, COUNT(PREF) as count  FROM `dims_mod_vpc_article` GROUP BY LEFT(TRIM(LEADING \' \' FROM TRIM(LEADING \'"\' FROM TRIM(LEADING \'&\' FROM designation))), 1)';

if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
	$sql = "
		SELECT	    LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) as lettre,
                    COUNT(a.reference) as count
		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		INNER JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		INNER JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		INNER JOIN	dims_mod_cata_famille f
		ON			f.id_famille = af.id_famille

		WHERE	a.published = 1
		GROUP BY LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1)" ;
}
else {
	$sql = "
		SELECT	    LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) as lettre,
                    COUNT(a.reference) as count
		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		INNER JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		INNER JOIN	dims_mod_cata_famille f
		ON			f.id_famille = af.id_famille

		LEFT JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		WHERE	a.published = 1
		GROUP BY LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1)" ;
}

$res = $db->query($sql);

$elem = array();
while($info = $db->fetchrow($res)) {
	if(is_numeric($info['lettre']))
	{
		if(!isset($alphabetique['classement']['0-9']))
		{
			$alphabetique['classement']['0-9'] = array();
			$alphabetique['classement']['0-9']['lettre'] = '0-9';
			$alphabetique['classement']['0-9']['count'] = 0;
			$alphabetique['classement']['0-9']['link'] = '/index.php?op=alphabetique&param=0-9';
		}
		$alphabetique['classement']['0-9']['count'] += $info['count'];
		if($caractere=='0-9')
		{
			$alphabetique['classement']['0-9']['SEL'] = 'selected';
		}
		else $alphabetique['classement']['0-9']['SEL'] = '';
	}
    else
	{
		$elem['lettre']     = strtoupper($info['lettre']);
		$elem['count']      = $info['count'];
		$elem['link']       = '/index.php?op=alphabetique&param='.$info['lettre'];
		if($caractere==$elem['lettre'])
		{
			$elem['SEL'] = 'selected';
			$total = $elem['count'];
		}
		else $elem['SEL'] = '';
		$alphabetique['classement'][] = $elem;
	}
}

//transformation de la page en LIMIT ... ... mysql (on est sur du 50 articles max par page)
//$begin = ($pag-1) * 50;
//$limit = 50;
//$sql_limit = 'LIMIT '.$begin.', '.$limit;



$search_caractere = " LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '".$caractere."'";
if($caractere=='0-9')
{
	$search_caractere = " LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '0'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '1'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '2'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '3'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '4'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '5'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '6'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '7'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '8'
						  OR LEFT(TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label))), 1) LIKE '9'";

	$total = $alphabetique['classement']['0-9']['count'];
}

if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
	$sql = "
		SELECT	a.id,
				a.reference,
				al.label,
				a.page AS numpage,
				a.image,
				a.qte,
				a.putarif_1,
				a.degressif,
				s.selection,
				af.id_famille
		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		INNER JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		INNER JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		INNER JOIN	dims_mod_cata_famille f
		ON			f.id_famille = af.id_famille

		WHERE ".$search_caractere."
		AND a.published = 1
		GROUP BY a.reference
		ORDER BY TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label)))";
}
else {
	$sql = "
		SELECT	a.id,
				a.reference,
				al.label,
				a.page AS numpage,
				a.image,
				a.qte,
				a.putarif_1,
				a.degressif,
				s.selection,
				af.id_famille
		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		INNER JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		INNER JOIN	dims_mod_cata_famille f
		ON			f.id_famille = af.id_famille

		LEFT JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		WHERE	".$search_caractere."
		AND a.published = 1
		GROUP BY a.reference
		ORDER BY TRIM(LEADING ' ' FROM TRIM(LEADING '\"' FROM TRIM(LEADING '&' FROM al.label)))" ;
}
$res = $db->query($sql);
while($info = $db->fetchrow($res)) {
    $alphabetique['articles'][$info['id']] = $info;
}

if (!isset($_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere])) {
	$_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['page'] = 0;
	$_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['nbElems'] = 50;
}

if (isset($_GET['p'])) {
	$pag = dims_load_securvalue('p', dims_const::_DIMS_NUM_INPUT, true, false) - 1;
	$_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['page'] = $pag;
}

$alphabetique['articles'] = cata_paginate('articles', 'alphabetique'.$caractere, $alphabetique['articles']);
$nbPages = ceil($total/50);
$smarty->assign('current_page', $_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['page']);
$a_paginationLiens = cata_getPaginationLinks('articles', 'alphabetique'.$caractere, $nbPages);
$smarty->assign('pagination_liens', $a_paginationLiens);

// numero des premier et dernier articles
$smarty->assign('pagination_deb', $_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['page'] * $_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['nbElems'] + 1);
if ($_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['page'] == $nbPages - 1) {
	$smarty->assign('pagination_fin', $total);
}
else {
	$smarty->assign('pagination_fin', ($_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['page'] + 1) * $_SESSION['catalogue']['pagination']['articles']['alphabetique'.$caractere]['nbElems']);
}

$_SESSION['dims']['tpl_page']['TITLE'] = 'Catalogue / Produits A-Z';
$_SESSION['dims']['tpl_page']['META_DESCRIPTION'] = 'Tous nos produits par ordre alphabétique.';
$_SESSION['dims']['tpl_page']['META_KEYWORDS'] = 'Catalogue, produits, A-Z, articles, ';
$_SESSION['dims']['tpl_page']['CONTENT'] = '';

$smarty->assign('alphabetique', $alphabetique);
