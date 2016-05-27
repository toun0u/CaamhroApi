<?
class PDF extends FPDF
{
	var $hauteur = 44; //25
	var $largCells = 95; //60
	var $spc = 0;
	var $lspc = 0;
	var $constdeb = 40;
	var $hspc = 0; //0
	var $maxNumber = 5; // 11
	var $maxLargeur = 105;
	var $pas = 5;

	function draw_table()
	{
		//$this->SetFont('Arial','',10);
		$this->SetLineWidth(0.1);

		while($this->hspc <= 297)
		{
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

	function fill_table()
	{
		$count = 0;
		//$this->SetFont('Arial','',8);
		$this->hspc=$this->constdeb;

		foreach($_SESSION['cram_rcmd'] as $numUser => $value)
		{
			if($numUser != '')
			{
				$this->SetXY($this->lspc+32,$this->hspc);

				$this->Cell($this->largCells,4,$value['prenom'] ." ". $value['nom'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+4);
				$this->Cell($this->largCells,4,$value['adresse1'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+8);
				$this->Cell($this->largCells,4,$value['adresse2'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+12);
				$this->Cell($this->largCells,4,$value['codepostal'] ." ". $value['ville'],0,2);
	    		$this->SetXY($this->lspc+32,$this->hspc+16);
				$this->Cell($this->largCells,4,"(num�ro d'agent : ".$numUser.")",0,2);

				//$this->Image('./data/codes_barre/'. $numUser .'.png',$this->lspc + 32,$this->hspc + $this->hauteur - 20,38,9);

				$this->lspc += ($this->largCells + $this->pas);

				if($this->lspc >= $this->maxLargeur)
				{
					$this->lspc = 0;

					if($count < $this->maxNumber )
					{
						 $this->hspc += $this->hauteur;
						 $count++;
					}
					else
					{
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

		$this->maxNumber=11;
		$count = 0;
		//$this->SetFont('Arial','',8);
		$this->hspc=$this->constdeb;

		foreach($_SESSION['cram_rcmd'] as $numUser => $value) {
			if($numUser != '')
			{
				$this->SetXY($this->lspc+32,$this->hspc);

				$this->Cell($this->largCells,4,$value['prenom'] ." ". $value['nom'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+4);
				$this->Cell($this->largCells,4,$value['adresse1'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+8);
				$this->Cell($this->largCells,4,$value['adresse2'],0,2);
				$this->SetXY($this->lspc+32,$this->hspc+12);
				$this->Cell($this->largCells,4,$value['codepostal'] ." ". $value['ville'],0,2);
	    		$this->SetXY($this->lspc+32,$this->hspc+16);
				$this->Cell($this->largCells,4,"(num�ro d'agent : ".$numUser.")",0,2);

				//$this->Image('./data/codes_barre/'. $numUser .'.png',$this->lspc + 32,$this->hspc + $this->hauteur - 20,38,9);

				$this->lspc += ($this->largCells + $this->pas);

				if($this->lspc >= $this->maxLargeur)
				{
					$this->lspc = 0;

					if($count < $this->maxNumber )
					{
						 $this->hspc += $this->hauteur;
						 $count++;
					}
					else
					{
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
