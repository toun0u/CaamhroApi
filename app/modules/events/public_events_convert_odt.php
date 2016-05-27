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

if (!isset($format)) $format = 'PDF';
//if (!isset($suivi_modele)) $suivi_modele = $type_doc.'.odt';

// on copie vers le dossier DIMS_TMP_PATH
$encoded=md5($suivi_modele);
$tmp_path = DIMS_TMP_PATH.$encoded;

if (!file_exists($tmp_path)) {
	dims_makedir($tmp_path);
}

$modele_content = $tmp_path."/content.xml" ;
$modele_styles = $tmp_path."/styles.xml" ;

$output_path = DIMS_TMP_PATH;

if ($format != 'ODT') {
	switch($format) {
		case 'PDF':
			$output_file = "{$type_doc}.pdf" ;
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
$path=substr($suivi_modele,0,strrpos($suivi_modele,"/"));
$fichier=$doc->fields['id']."_".$doc->fields['version'].".".$doc->fields['extension'];
//echo $path.$fichier."<br>";
dims_unzip($fichier,$path, $tmp_path) ;

$xml_content = '';
$xml_styles = '';
echo "<br>".$tmp_path." ".$modele_content;
if ($f = fopen( $modele_content, "r" )) {
	while (!feof($f)) $xml_content .= fgets($f, 4096);
	fclose($f);
}
else die("erreur avec le fichier $modele_content");

if ($f = fopen( $modele_styles, "r" )) {
	while (!feof($f)) $xml_styles .= fgets($f, 4096);
	fclose($f);
}
else die("erreur avec le fichier $modele_styles");

global $xmlmodel;
global $output;
global $modeleligne;
$output = '';

// construction des tags à remplacer
$xmlmodel = new xmlmodel('');

// init tmp path variable
$xmlmodel->setTmpPath($tmp_path);

$xmlmodel->addtag('(TITLE)', $action->fields['libelle']);
$xmlmodel->addtag('(LOCATION)', $action->fields['lieu']);
$xmlmodel->addtag('(DATE_START)', business_dateus2fr($action->fields['datejour']));
$xmlmodel->addtag('(YEAR_EVENT)', substr($action->fields['datejour'],0,4));
$xmlmodel->addtag('(DATE_END)', business_dateus2fr($action->fields['datefin']));

$date_fin_etap = dims_timestamp2local($etap->fields['date_fin']);
$xmlmodel->addtag('(DATEFIN_ETAP)', $date_fin_etap['date']);

$xmlmodel->addtag('(PRICE)', $action->fields['prix']);
$xmlmodel->addtag('(LANGUAGE)', $action->fields['language']);

// ajout des images
//$xmlmodel->addImage('(LOGO)','./common/img/configure.png');

$xml_parser = xmlparser_content();
if (!xml_parse($xml_parser, $xml_content, TRUE)) {
	printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
}

$content = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

$xml_modeleligne = $modeleligne;

$output = '';
$etape = '';
$modeleligne = '';

$xml_parser = xmlparser_content();
if (!xml_parse($xml_parser, $xml_styles, TRUE)) {
	printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
}

$styles = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

// Assurons nous que le fichier est accessible en écriture
if (is_writable($output_path)) {
	if (!$handle = fopen($modele_styles, 'w')) {
		 echo "Impossible d'ouvrir le fichier $modele_styles";
		 exit;
	}

	if (fwrite($handle, $styles) === FALSE) {
		echo "Impossible d'écrire dans le fichier $modele_styles";
		exit;
	}

	if (!$handle = fopen($modele_content, 'w')) {
		 echo "Impossible d'ouvrir le fichier $modele_content";
		 exit;
	}

	if (fwrite($handle, $content) === FALSE) {
		echo "Impossible d'écrire dans le fichier $modele_content";
		exit;
	}

	fclose($handle);

	$res = array();
	$cwd = getcwd();
	chdir($tmp_path);

	shell_exec(escapeshellcmd("zip -r ../$output_odt *"));
	//shell_exec("rm -rf *");
	chdir($cwd);
	if ($format != 'ODT') {
		$converter_path = realpath(DIMS_APP_PATH . '/modules/system/jooconverter/').'/jooconverter-2.0.0.jar ';

		if (_DIMS_JAVAPATH != '') $javapath=_DIMS_JAVAPATH;
		else $javapath="/usr/local/java/bin/java";

		//$cmd = _DIMS_JAVAPATH." -jar $converter_path".realpath("{$output_path}/{$output_odt}").''.realpath("{$output_path}")."/{$output_file}";

		if (!file_exists(realpath($output_path)."/".$output_file))
		{
				echo realpath($output_path);
				$cmd = " unoconv -f ".strtolower($format)." ".realpath($output_path)."/".$output_odt;

				// OVH
				//$cmd = $javapath." -jar $converter_path".realpath("{$output_path}/{$output_odt}").''.realpath("{$output_path}")."/{$output_file}";
				//$cmd = "/usr/bin/java -jar $converter_path".realpath("{$output_path}/{$output_odt}").''.realpath("{$output_path}")."/{$output_file}";
				//echo $cmd;die();
				shell_exec(escapeshellcmd($cmd));
		}
		//echo realpath($output_path)."/".$output_file;

		dims_downloadfile(realpath($output_path)."/".$output_file,$output_file,true, true);
	}
	else dims_downloadfile(realpath($output_path)."/".$output_odt,$output_odt, true, true);
}
else {
	echo "Le dossier $output_path n'est pas accessible en écriture.";
}

?>
