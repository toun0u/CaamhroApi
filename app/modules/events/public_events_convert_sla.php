<?php
require_once(DIMS_APP_PATH . '/modules/system/class_xmlmodel.php');
require_once(DIMS_APP_PATH . '/modules/system/xmlparser_content.php');
require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
require_once(DIMS_APP_PATH . '/modules/system/class_action_etap.php');
require_once(DIMS_APP_PATH . '/include/functions/string.php');

$id_doc=dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_event=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_etap=dims_load_securvalue('id_etap',dims_const::_DIMS_NUM_INPUT,true,true,false);

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

// on copie vers le dossier DIMS_TMP_PATH
$encoded=md5($suivi_modele);
$tmp_path = DIMS_TMP_PATH . $encoded;

if (!file_exists($tmp_path)) {
	dims_makedir($tmp_path);
}

$output_path = DIMS_TMP_PATH;

if ($format != 'ODT') {
	switch($format) {
				case 'SLA':
					$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.sla" ;
					break;
		case 'PDF':
			$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.pdf" ;
		break;

		case 'DOC':
			$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.doc" ;
		break;

		case 'SXW':
			$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.sxw" ;
		break;

		case 'RTF':
			$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.rtf" ;
		break;

		case 'XML':
			$output_file = "{$type_doc}_{$_SESSION['dims']['currentaction']}.xml" ;
		break;
	}
}

$output_odt = "{$type_doc}_{$_SESSION['dims']['currentaction']}.odt" ;
$path=substr($suivi_modele,0,strrpos($suivi_modele,"/"))."/";
$fichier=$doc->fields['id']."_".$doc->fields['version'].".".$doc->fields['extension'];

$content=file_get_contents($path.$fichier);

$content=str_replace('(TITLE)', $action->fields['libelle'],$content);
$content=str_replace('(DATE_START)', business_dateus2fr($action->fields['datejour']),$content);
$content=str_replace('(YEAR_EVENT)', substr($action->fields['datejour'],0,4),$content);
$content=str_replace('(DATE_END)', business_dateus2fr($action->fields['datefin']),$content);

$date_fin_etap = dims_timestamp2local($etap->fields['date_fin']);
$content=str_replace('(DATEFIN_ETAP)', $date_fin_etap['date'],$content);
$content=str_replace('(PRICE)', $action->fields['prix'],$content);
$content=str_replace('(LANGUAGE)', $action->fields['language'],$content);

/*$xmlmodel->addtag('(LOCATION)', $action->fields['lieu']);
$xmlmodel->addtag('(DATE_START)', business_dateus2fr($action->fields['datejour']));
$xmlmodel->addtag('(YEAR_EVENT)', substr($action->fields['datejour'],0,4));
$xmlmodel->addtag('(DATE_END)', business_dateus2fr($action->fields['datefin']));

$date_fin_etap = dims_timestamp2local($etap->fields['date_fin']);
$xmlmodel->addtag('(DATEFIN_ETAP)', $date_fin_etap['date']);

$xmlmodel->addtag('(PRICE)', $action->fields['prix']);
$xmlmodel->addtag('(LANGUAGE)', $action->fields['language']);
*/

// Assurons nous que le fichier est accessible en écriture
if (is_writable($output_path)) {
	file_put_contents(realpath($output_path)."/".$output_file,$content);
	dims_downloadfile(realpath($output_path)."/".$output_file,$output_file,true, true);
}
else {
	echo "Le dossier $output_path n'est pas accessible en écriture.";
}

?>
