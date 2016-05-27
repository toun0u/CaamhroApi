<?
class PDF_ETIQUETTES extends FPDF
{
	var $hauteur = 44; //25
	var $largCells = 95; //60
	var $spc = 0;
	var $lspc = 0;
	var $constdeb = 40;
	var $hspc = 0; //0
	var $maxNumber = 16; // 11
	var $maxLargeur = 105;
	var $pas = 5;
        var $elements;

        function setElements($elems) {
            if (sizeof($elems)==1) {
                for ($i=0;$i<=$maxNumber;$i++) {
                    $this->elements[]=$elems[0];
                }
            }
            else {
                $this->elements=$elems;
            }

        }

	function draw_table() {
		//$this->SetFont('Arial','',10);
		$this->SetLineWidth(0.1);

		while($this->hspc <= 297) {
			$this->line(0,$this->hspc,210,$this->hspc);
			$this->hspc += $this->hauteur;
		}

		while($this->lspc <= $this->maxLargeur)
		{
			$this->line($this->lspc,0,$this->lspc,297);
			$this->lspc += ($this->largCells + $this->pas * 2);
		}

		$this->lspc = 0;
		$this->hspc = 36;
	}

	function fill_table() {
		$count = 0;
		//$this->SetFont('Arial','',8);
		$this->hspc=$this->constdeb;

		foreach($this->elements as $k=>$value) {
			$ref=$value['reference'];

			if($ref != '') {
				$this->SetXY($this->lspc+32,$this->hspc);

				$this->Cell($this->largCells,4,$value['line1'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+4);
				$this->Cell($this->largCells,4,$value['line2'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+8);
				$this->Cell($this->largCells,4,$value['line3'],0,2);
				$this->SetXY($this->lspc+30,$this->hspc+12);

				$this->Image('./data/barcode/'. $ref .'.png',$this->lspc + 25,$this->hspc + $this->hauteur - 32,45,14);

				$this->lspc += ($this->largCells + $this->pas);

				if($this->lspc >= $this->maxLargeur) {
					$this->lspc = 0;

					if($count < $this->maxNumber ) {
							 $this->hspc += $this->hauteur;
							 $count++;
					}
					else {
							$this->AddPage();
							$this->SetFont('times','',8);
							//$this->draw_table();
							$this->hspc = $this->constdeb;
							$count=0;
					}
				}
			}
		}
	}

	function fillTableUSer() {

		$this->maxNumber=5;
		$count = 0;
		//$this->SetFont('Arial','',8);
		$this->hspc=$this->constdeb;

		foreach($this->elements as $k=>$value) {
			if($k != '') {

				$this->SetXY($this->lspc+32,$this->hspc);

				$this->Cell($this->largCells,4,  utf8_decode($value['firstname'] ." ". $value['lastname']),0,2);
				$this->SetXY($this->lspc+32,$this->hspc+4);
				$this->Cell($this->largCells,4,utf8_decode($value['address']),0,2);
				$this->SetXY($this->lspc+32,$this->hspc+8);
				$this->Cell($this->largCells,4,utf8_decode($value['postalcode'] ." ". $value['city']),0,2);
	    		$this->SetXY($this->lspc+32,$this->hspc+12);
				//$this->Cell($this->largCells,4,utf8_decode($value['country']),0,2);

				//$this->Image('./data/codes_barre/'. $numUser .'.png',$this->lspc + 32,$this->hspc + $this->hauteur - 20,38,9);

				$this->lspc += ($this->largCells + $this->pas);

				if($this->lspc >= $this->maxLargeur) {
					$this->lspc = 0;

					if($count < $this->maxNumber ) {
						 $this->hspc += $this->hauteur;
						 $count++;
					}
					else {
						$this->AddPage();
						$this->SetFont('times','',8);
						//$this->draw_table();
						$this->hspc = $this->constdeb;
						$count=0;
					}
				}

			}
		}
	}

}
?>
