<?php

require_once DIMS_APP_PATH.'modules/catalogue/include/class_reseller.php';

// recherche du template
$lstwcemods = $dims->getWceModules();
$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

$headings = wce_getheadings($wce_module_id);
$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

$site_template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
$smartyobject->assign('site_template_name', $site_template_name);
$template_web_path = '/templates/frontoffice/'.$site_template_name.'/';
// FIN - recherche du template


$template_dir = DIMS_APP_PATH . "templates/frontoffice/".$site_template_name;
$template_web_dir = "/assets/images/frontoffice/".$site_template_name;

$smartyobject->assign('template_dir', $template_web_dir);
$smartyobject->template_dir = $template_dir;


// Affichage de la carte interactive de la france
$template_name = 'resellers_map';


// Chargement des adresses des revendeurs
$a_resellers = cata_reseller::all();
$resellers_adresses = array();
foreach ($a_resellers as $reseller) {
	$googleAddress = $reseller->getAddress1();
	$visualAddress = $reseller->getAddress1();

	if ($reseller->getAddress2()) {
		$googleAddress .= ' '.$reseller->getAddress2();
		$visualAddress .= '<br>'.$reseller->getAddress2();
	}
	if ($reseller->getAddress3()) {
		$googleAddress .= ' '.$reseller->getAddress3();
		$visualAddress .= '<br>'.$reseller->getAddress3();
	}

	$googleAddress .= ' '.$reseller->getPostalCode().' '.$reseller->getCity().' '.$reseller->getCountryLabel();
	$visualAddress .= '<br>'.$reseller->getPostalCode().' '.$reseller->getCity().'<br>'.$reseller->getCountryLabel();

	$resellers_adresses[] = array(
		'title' 			=> addslashes($reseller->getName()),
		'googleAddress' 	=> addslashes(trim($googleAddress)),
		'visualAddress' 	=> addslashes(trim($visualAddress)),
		'website' 			=> addslashes($reseller->getWebSite()),
		'email' 			=> addslashes($reseller->getEmail()),
		'telephone' 		=> addslashes($reseller->getPhone()),
		'fax'		 		=> addslashes($reseller->getFax()),
		'logo' 				=> addslashes($reseller->getLogoWebPath())
		);
}
$smartyobject->assign('resellers', $resellers_adresses);



if (file_exists($smartyobject->template_dir[0].$template_name.'.tpl')) {
	$smartypath = DIMS_APP_PATH.'smarty';

	if (!file_exists($smartypath.'/templates_c/'.$template_name)) {
		mkdir ($smartypath."/templates_c/".$template_name."/");
	}
	$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";
	$smartyobject->display($template_name.'.tpl');
}
else {
	echo 'ERREUR : '.$template_name.'.tpl manquant !';
}
