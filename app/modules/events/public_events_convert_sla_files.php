<?php
require_once(DIMS_APP_PATH . '/modules/system/class_xmlmodel.php');
require_once(DIMS_APP_PATH . '/modules/system/xmlparser_content.php');
require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
require_once(DIMS_APP_PATH . '/modules/system/class_action_etap.php');
require_once(DIMS_APP_PATH . '/include/functions/string.php');

$_SESSION['dims']['currentaction']=$id_event;

$action = new action();
$action->open($id_event);

$etap = new action_etap();
$etap->open($id_etap);

$doc = new docfile();
$doc->open($id_doc);

$suivi_modele=$doc->getfilepath();

$doc->fields['name']=dims_convertaccents($doc->fields['name']);
$type_doc=substr($doc->fields['name'],0,strlen($doc->fields['name'])-4);

if (!isset($format)) $format = 'SLA';
//if (!isset($suivi_modele)) $suivi_modele = $type_doc.'.odt';

$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.sla" ;

// on recupere les info du path courant
$path=$zip_path;
$fichier=$file['name'];

//echo $path.$fichier."<br>";
$content=file_get_contents($path.$fichier);
//echo $content;die();
$content=str_replace('(TITLE)', $action->fields['libelle'],$content);
$content=str_replace('(LOCATION)', $action->fields['lieu'],$content);
$content=str_replace('(DATE_START)', business_dateus2fr($action->fields['datejour']),$content);
$content=str_replace('(YEAR_EVENT)', substr($action->fields['datejour'],0,4),$content);
$content=str_replace('(DATE_END)', business_dateus2fr($action->fields['datefin']),$content);

$date_fin_etap = dims_timestamp2local($etap->fields['date_fin']);
$content=str_replace('(DATEFIN_ETAP)', $date_fin_etap['date'],$content);
$content=str_replace('(DATEFIN_INSC)', business_dateus2fr($action->fields['datefin_insc']),$content);
$content=str_replace('(PRICE)', $action->fields['prix'],$content);
$content=str_replace('(LANGUAGE)', $action->fields['language'],$content);

// Assurons nous que le fichier est accessible en écriture
if (is_writable($zip_path.$fichier)) {
	file_put_contents($zip_path.$fichier,$content);
	//echo $content;
	//die();
//	  dims_downloadfile(realpath($output_path)."/".$output_file,$output_file,true, true);
}
else {
	echo "Le dossier $output_path n'est pas accessible en écriture.";
}

?>
