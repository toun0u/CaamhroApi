<?php
//------------------------ PERIODE D'HISTORIQUE ---------
// liste des IPS ayant un accès complet aux historiques
global $authorized_ips;
$authorized_ips = array(
	// '127.0.0.1',		// localhost
	// '82.247.249.174'	// netlor (free)
	);

// nombre de mois d'historique pour toutes les IPS qui n'ont pas l'acces complet (en mois)
global $history_months;
$history_months = 200;

// periode de validité des prix (en heures)
// au delà, le prix est recalculé
define ('_PRICE_VALIDITY', 24);

//------------------------ PERIODE DE NOUVEAUTES ---------
// les articles en nouveauté sont ceux qui ont été créés il y a X jours ou moins
$nouveautes_days = 90;

//------------------------ SLIDESHOW ---------------------
// taille du slideshow tel que défini par la feuille de style
define('_CATA_SLIDESHOW_DIMENTIONS_IMAGES',		'755x253');
define('_CATA_SLIDESHOW_DIMENTIONS_MINIATURES',	'48x48');
// position de la description dans le sideshow
$a_descr_positions = array(
	'left'		=> 'Gauche',
	'right'		=> 'Droite',
	'top'		=> 'Haut',
	'bottom'	=> 'Bas');

define('_CATA_PANIER_TYPE_LIST_CLASSIQUE',	1);
define('_CATA_PANIER_TYPE_LIST_SCOLAIRE',	2);
