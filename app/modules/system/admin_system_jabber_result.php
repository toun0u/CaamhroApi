<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . "/include/jabber/class_dims_xml2array.php");
require_once(DIMS_APP_PATH . "/include/jabber/functions.php");
require_once(DIMS_APP_PATH . "/include/jabber/requetes.php");
require_once(DIMS_APP_PATH . "/include/jabber/paramsJabber.php");
require_once(DIMS_APP_PATH . "/include/jabber/ListeStructures.php");
require_once(DIMS_APP_PATH . "/intercom/evenement.php");


// on traite l'XML
$analyseXML = new DomDocument();

$_SESSION['ejabber']['error']='';
$_SESSION['ejabber']['success']='';

$analyseXML->loadXML($_SESSION['ejabber']['message']);
$structureMessages = new ListeStructures;
$resultat = $structureMessages->analyseStructure($analyseXML);

if (isset($resultat['designation'])) {
	switch($resultat['designation']) {
		case 'initDimsFailed':
		case 'autrecaserreur':
			$nodes=$analyseXML->getElementsByTagName('codeErreur');
			if (!is_null($nodes->item(0))) {
				$codeerror=$nodes->item(0)->textContent;
				$_SESSION['ejabber']['error']=$codeerror;
			}
			$intercom->delete();
			break;
		case 'initDimsSucceed':

			$_SESSION['ejabber']['success']=1;
			$nodes=$analyseXML->getElementsByTagName('clefSecurite');
			if (!is_null($nodes->item(0))) {
				$_SESSION['ejabber']['clefSecurite']=$nodes->item(0)->textContent;

			}
			break;
	}
}

// permet de ne pas creer de nouveau le profil de connexion
unset($_SESSION['ejabber']['dims_name']);
die();
?>
