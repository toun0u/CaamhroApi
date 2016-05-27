<?php
ob_clean();
ob_start();

header('Content-type: text/html; charset='._DIMS_ENCODING);
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé

// unité de vente
$uventeField = 'uvente';
if ($_SESSION['dims']['connected'] && defined('_CATA_VARIANTE')) {
	if (file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client.php')) {
		require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client.php';
		$oClient = new cata_client();
		$oClient->openByCode($_SESSION['catalogue']['code_client']);

		if (file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
			require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
			$oClientCplmt = new cata_client_cplmt();
			if ($oClientCplmt->open($oClient->fields['id_client'])) {
				if ($oClientCplmt->fields['soldeur'] == 'Oui') {
					$uventeField = 'uventesolde';
				}
			}
		}
	}
}

// si un marché est en cours, on regarde si il est restrictif
$restrictions = false;
$a_restrictions = array();
if (isset($_SESSION['catalogue']['market'])) {
	$market = cata_market::getByCode($_SESSION['catalogue']['market']['code']);
	if ($market->hasRestrictions()) {
		$restrictions = true;
		$a_restrictions = $market->getRestrictions();
	}
}


require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';

if (!isset($_SESSION['catalogue']['panier']['articles'])) {
	$_SESSION['catalogue']['panier']['articles'] = array();
}

// Ajout de la ref au panier
$article_id = dims_load_securvalue('article_id', dims_const::_DIMS_NUM_INPUT, true, false);
$quantity 	= dims_load_securvalue('quantity', dims_const::_DIMS_NUM_INPUT, true, false);

// reference à ajouter au panier
if ($article_id > 0 && $quantity > 0 && (!$restrictions || isset($a_restrictions[$article_id]))) {
	$article = new article();
	$article->open($article_id);
	$ref_article = $article->getReference();

	$recolisage = false;
	$moduloQte = $quantity % $article->fields[$uventeField];
	if($moduloQte > 0) {
		$recolisage = true;
		$quantity = $quantity + ($article->fields[$uventeField] - $moduloQte);
	}

	if (!isset($_SESSION['catalogue']['panier']['articles'][$article->getReference()]['qte'])) {
		$_SESSION['catalogue']['panier']['articles'][$article->getReference()]['qte'] = 0;
	}
	$_SESSION['catalogue']['panier']['articles'][$article->getReference()]['qte'] += $quantity;

	// Recalcul du montant
	$_SESSION['catalogue']['panier']['montant'] = 0;
	foreach ($_SESSION['catalogue']['panier']['articles'] as $reference => $detail) {
		$article = new article();
		$article->findByRef($reference);
		$prix = catalogue_getprixarticle($article, $detail['qte']);

		// Si on a un prix, on ajoute au montant
		if ($prix > 0) {
			$_SESSION['catalogue']['panier']['montant'] += $prix * $detail['qte'];
		}
		// Sinon, on enlève la référence du panier
		else {
			unset($_SESSION['catalogue']['panier']['articles'][$reference]);
		}
	}

	// Mise à jour du panier en cookie ou bdd
	if ($oCatalogue->getParams('cart_management') == 'cookie') {
		panier2cookie();
	}
	elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
		panier2bdd();
	}
}

// Initialisation du op en session
$_SESSION['catalogue']['op'] = '';

// Renvoi de la réponse
$data = array(
	'articles' 	=> array(),
	'montant' 	=> $_SESSION['catalogue']['panier']['montant']
	);
foreach ($_SESSION['catalogue']['panier']['articles'] as $reference => $detail) {
	$data['articles'][] = array(
		'reference' => $reference,
		'quantity' 	=> $detail['qte']
		);
}
echo json_encode($data);
ob_flush();
exit(0);
