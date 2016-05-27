<?php

ini_set('max_execution_time',-1);
ini_set('memory_limit','512M');

if ($_SESSION['dims']['importform']['object_id']==dims_const::_SYSTEM_OBJECT_CONTACT) {
	$file = $_FILES['file_import_contact']['tmp_name'];
	$extension = explode(".", $_FILES['file_import_contact']['name']);
}else {
	$file = $_FILES['file_import_tiers']['tmp_name'];
	$extension = explode(".", $_FILES['file_import_tiers']['name']);
}

$extension = $extension[count($extension)-1];
$extension = strtolower($extension);

$liste_version["csv"] = "CSV";
$liste_version["xlsx"] = "Excel2007";
$liste_version["xls"] = "Excel5";

require_once(DIMS_APP_PATH . '/include/PHPExcel/IOFactory.php');

$objReader = PHPExcel_IOFactory::createReader($liste_version[$extension]);

//dims_print_r($objReader); die();
$objPHPExcel = PHPExcel_IOFactory::load($file);

$obj_all_sheets = $objPHPExcel->getAllSheets();
$nb_row = $obj_all_sheets[0]->getHighestRow();
$_SESSION['dims']['importform']['nbrow'] = $nb_row ;
$Column_max = $obj_all_sheets[0]->getHighestColumn();
$nb_Column = 1 ;

$alphabet[1]	= "A";
$alphabet[]	= "B";
$alphabet[]	= "C";
$alphabet[]	= "D";
$alphabet[]	= "E";
$alphabet[]	= "F";
$alphabet[]	= "G";
$alphabet[]	= "H";
$alphabet[]	= "I";
$alphabet[]	= "J";
$alphabet[]	= "K";
$alphabet[]	= "L";
$alphabet[]	= "M";
$alphabet[]	= "N";
$alphabet[]	= "O";
$alphabet[]	= "P";
$alphabet[]	= "Q";
$alphabet[]	= "R";
$alphabet[]	= "S";
$alphabet[]	= "T";
$alphabet[]	= "U";
$alphabet[]	= "V";
$alphabet[]	= "W";
$alphabet[]	= "X";
$alphabet[]	= "Y";
$alphabet[]	= "Z";

/*foreach ($alphabet as $key => $value){
	if ($value == $Column_max)
	$nb_Column = $key ;
}*/

if (strlen($Column_max)> 2)
	$Column_max = "AZ";

for ($i=1; $i <= $nb_row; $i++){
	$c=0;
	$d=0;
	$first_lettre = "";
	$lettre = "";
	while ($first_lettre.$lettre != $Column_max){
	$c ++ ;
	$lettre = $alphabet[$c];
	if ($objPHPExcel->getActiveSheet()->cellExists($first_lettre.$lettre.$i)){
		$value = $objPHPExcel->getActiveSheet()->getCell($first_lettre.$lettre.$i)->getValue();
		if ($value != ""){
		if ($objPHPExcel->getActiveSheet()->getCell($first_lettre.$lettre.$i)->getDataType() == 'f'){
			$value = $objPHPExcel->getActiveSheet()->getCell($first_lettre.$lettre.$i)->getCalculatedValue();
		}

		$_SESSION['dims']['importform']['data'][$i][$first_lettre.$lettre] = $value ;
		}
	}

	if ($alphabet[$c] == "Z"){
		$d++;
		$c=0;
		$first_lettre = $alphabet[$d];
	}
	}
}

$_SESSION['dims']['importform']['firstdataline'] = 1;

unset($obj_all_sheets);
unset($objPHPExcel);

dims_redirect("$scriptenv?op=import_contact3");

?>
