<?php

class dims_excel_import extends DIMS_DATA_OBJECT {
	private $table_name;			  // name of table
	private $file_name;			  // input file
	private $table_exists;			  // test if table exists
	private $nb_rows;
	private $start_row;
	private $db;
	private $Column_max;
	private $Column_max2;

	function __construct($file_name) {
		$this->file_name = $file_name;
		$this->headers = array();
		$this->table_exists = false;
		$this->db = dims::getInstance()->getDb();
		$this->nb_rows = 0;
		$this->start_row = 1;
		$this->Column_max = "A";
		$this->Column_max2 = 1;
	}

	public function getTableTemp() {
		return $this->table_name;
	}

	public function setFile($file) {
		if (file_exists($file))
			$this->file_name = $file;
	}

	public function setStartRow($row) {
		if ($row > 0)
			$this->start_row = $row;
		else
			$this->start_row = 1;
	}

	private function createTableImport() {
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->table_name." (";
		if($this->Column_max2 > 0) {
			$arr = array();
			for($i=1; $i<=$this->Column_max2; $i++) {
				$arr[] = "`row_$i` TEXT";
			}
			$sql .= implode(",", $arr);
			$sql .= ") character set utf8 COLLATE utf8_general_ci";
			$res = $this->db->query($sql);
			$this->table_exists=true;
		}
	}

	public function import(){
		if (file_exists($this->file_name)){
			$extension	= explode(".", $this->file_name);
			$extension	= $extension[count($extension)-1];
			$extension	= strtolower($extension);
			$this->table_name = "temp_".date("d_m_Y_H_i_s");
			require_once DIMS_APP_PATH.'include/PHPExcel/IOFactory.php';
			$liste_version["csv"]	= "CSV";
			$liste_version["xlsx"]	= "Excel2007";
			$liste_version["xls"]	= "Excel5";
			$excel = PHPExcel_IOFactory::createReader($liste_version[$extension]);
			$objPHPExcel = PHPExcel_IOFactory::load($this->file_name);
			$obj_all_sheets	= $objPHPExcel->getAllSheets();

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

			$this->nb_rows = $obj_all_sheets[0]->getHighestRow();
			$this->Column_max = $obj_all_sheets[0]->getHighestColumn();//Nombre de cellule
			if(strlen($this->Column_max) > 2)
				$this->Column_max = 'AZ';
			$split = array_reverse(str_split($this->Column_max));
			foreach ($split as $sp)
				$this->Column_max2 = $this->Column_max2 * array_search($sp,$alphabet);

			$this->createTableImport();
			$maxline=120;

			for ($i = $this->start_row; $i <= $this->nb_rows; $i++) {
				$ins = "INSERT INTO ".$this->table_name." VALUES(";
				$params = array();
				$dat = array();
				$x = 0;
				$y = 0;
				$lettre1 = "";
				$lettre2 = "";
				while($lettre1.$lettre2 != $this->Column_max){
					$x ++;
					$lettre2 = $alphabet[$x];
					$dat[] = "'".str_replace("'","''",$objPHPExcel->getActiveSheet()->getCell($lettre1.$lettre2.$i)->getValue())."'";
					if ($alphabet[$x] == "Z") {
						$y++;
						$x = 0;
						$lettre1 = $alphabet[$d];
					}
				}
				$ins .= $this->db->getParamsFromArray($dat, 'dat', $params).')';
				$this->db->query($ins);
			}
		}
	}
}
