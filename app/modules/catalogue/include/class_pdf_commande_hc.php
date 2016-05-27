<?php
define ('FPDF_FONTPATH','./lib/fpdf/font/');

include_once DIMS_APP_PATH.'lib/fpdf/fpdf.php';

class pdf_commande_hc extends FPDF {

	function GetDateLocal() {return date(_DATEFORMAT);}
	function GetTimeLocal() {return date(_TIMEFORMAT);}

	function SetFont_Title()
	{
		$this->SetFont('Arial', 'B', 12);
	}

	function SetFont_Standard()
	{
		$this->SetFont('Arial', '', 12);
	}

	function SetFont_LigneCommande()
	{
		$this->SetFont('Arial', '', 10);
	}

        function SetFont_LigneCommandeDetail()
	{
		$this->SetFont('Arial', '', 7);
	}

	function SetFont_LigneCommandeBold()
	{
		$this->SetFont('Arial', 'B', 10);
	}

        function SetFont_Small()
	{
		$this->SetFont('Arial', '', 8);
	}

	function SetFont_StandardBold()
	{
		$this->SetFont('Arial', 'B', 12);
	}

	function SetFont_Copyright()
	{
		$this->SetFont('Arial', '', 8);
	}

	function SetColor_Standard()
	{
			$this->SetTextColor(25,9,134);
	}

	function SetColor_Black()
	{
			$this->SetTextColor(0,0,0);
	}

	function SetColor_Highlighted()
	{
			$this->SetTextColor(200,0,200);
	}

	function WriteTitle($w, $text)
	{
		$this->DrawHLine($w);
		$this->DrawHLine($w+9);
		$this->SetFont_Title();
		$this->Text((210 - $this->GetStringWidth($text))/2, $w + 7, $text);
		$w+=15;
	}

	function WriteLine($w, $text, $x = -1)
	{
		if ($text!='')
		{
			if ($x == -1) $this->Text((210 - $this->GetStringWidth($text))/2, $w, $text);
			else $this->Text($x, $w, $text);
			$w+=5;
		}
	}

	function WriteCentered($text, $x, $y, $w)
	{
		if ($text!='')
		{
			$this->Text( $x + ($w - $this->GetStringWidth($text))/2, $y, $text);
		}
	}

	function WriteLeft($text, $x, $y, $w)
	{
		if ($text!='')
		{
			$this->Text( $x, $y, $text);
		}
	}

	function WriteRight($text, $x, $y, $w)
	{
		if ($text!='')
		{
			$this->Text( $x + $w - $this->GetStringWidth($text), $y, $text);
		}
	}

	function DrawHLine($w)
	{
		$this->Line(0, $w, 210, $w);
	}


	function DrawArray($wstart,$w)
	{
		if ($this->afficher_prix)
		{
			$this->SetFillColor(220,220,220);
			$this->Rect(10,$wstart,192,7,'F');
			$this->Line(10, $wstart, 202, $wstart);
			$this->Line(10, $wstart+7, 202, $wstart+7);
			$this->Line(10, $w, 202, $w);
			$this->Line(10, $wstart, 10, $w);
			$this->Line(30, $wstart, 30, $w);
			$this->Line(154, $wstart, 154, $w);
			$this->Line(172, $wstart, 172, $w);
			$this->Line(182, $wstart, 182, $w);
			$this->Line(202, $wstart, 202, $w);
		}
		else
		{
			$this->SetFillColor(220,220,220);
			$this->Rect(10,$wstart,192,7,'F');
			$this->Line(10, $wstart, 202, $wstart);
			$this->Line(10, $wstart+7, 202, $wstart+7);
			$this->Line(10, $w, 202, $w);
			$this->Line(10, $wstart, 10, $w);
			$this->Line(30, $wstart, 30, $w);
			$this->Line(192, $wstart, 192, $w);
			$this->Line(202, $wstart, 202, $w);
		}
	}

	function Header()
	{
    $w = 10;

    // ADRESSES BAS DE PAGE
    $this->SetFont_Title();
    $this->SetColor_Black();

    $this->Image('./templates/frontoffice/unifob/gfx/logo1_nb.png', 30, 5, 30);
    $this->Image('./templates/frontoffice/unifob/gfx/logo2_nb.png', 70, 10, 70);
    $this->WriteLine($w+65,"Commande n°{$this->commande->fields['id']} : '{$this->commande->fields['libelle']}'",6);

    $this->SetFont_Standard();
    $w = 40;


	// Date
	$date_validation = dims_timestamp2local($this->commande->fields['date_validation']);
	$this->WriteLeft($date_validation['date'], 130, 40, 40);

	// Adresse de facturation
    $this->WriteLine($w+10,html_entity_decode($this->client->fields['CNOM']),120);
    $this->WriteLine($w+15,html_entity_decode($this->client->fields['CRUE']),120);
    ($this->client->fields['CAUX'] != '') ? $this->WriteLine($w+20,html_entity_decode($this->client->fields['CAUX'],120)) : $w -= 5;
    $this->WriteLine($w+25,$this->client->fields['CCPTL'] ." ". html_entity_decode($this->client->fields['CVIL']),120);

    if($this->commande->fields['CRUEL'] != '')
    {
      $w = 40;
      // Adresse de livraison
      $this->WriteLine($w,'A livrer à :',10);
      $this->WriteLine($w+10,html_entity_decode($this->commande->fields['CNOML']),20);
      $this->WriteLine($w+15,html_entity_decode($this->commande->fields['CRUEL']),20);
      ($this->commande->fields['CAUXL'] != '') ? $this->WriteLine($w+20,html_entity_decode($this->commande->fields['CAUXL']),20) : $w -= 5;
      $this->WriteLine($w+25,$this->commande->fields['CPPTLL'] ." ". html_entity_decode($this->commande->fields['CVILL']),20);
	  }
   }

	function Content()
	{
      $w = 95;

      $this->SetFont_LigneCommande();
      foreach($this->commande->getarticles() as $cmd_detail)
      {
        if ($w>240)
        {
          $w = 95;
          $this->AddPage();
        }

        if ($this->afficher_prix)
        {
			$this->WriteLine($w,$cmd_detail['reference'],12);
			$this->SetFont_LigneCommandeBold();
			$this->WriteLine($w,$cmd_detail['designation'],32);
			$this->SetFont_LigneCommande();
			$this->WriteRight(catalogue_formateprix($cmd_detail['pu']),150,$w,20);
			$this->WriteRight($cmd_detail['qte'],170,$w,10);
			$this->WriteRight(catalogue_formateprix($cmd_detail['pu']*$cmd_detail['qte']),180,$w,20);

			$this->SetFont_LigneCommandeDetail();
			$this->SetXY(42,$w+2);
			$w = $this->GetY()+5;
        }
        else
        {
			$this->WriteLine($w,$cmd_detail['reference'],12);
			$this->SetFont_LigneCommandeBold();
			$this->WriteLine($w,$cmd_detail['designation'],32);
			$this->SetFont_LigneCommande();
			$this->WriteRight($cmd_detail['qte'],190,$w,10);

			$this->SetFont_LigneCommandeDetail();
			$this->SetXY(42,$w+2);
			$w = $this->GetY()+5;
        }

        $this->SetFont_LigneCommande();
      }


	$this->WriteLine(250,'Commentaire :',11);
	$this->SetXY(10,252);
	$this->MultiCell(100,3,html_entity_decode($this->commande->fields['commentaire'],ENT_QUOTES));

	// Cadre du commentaire
	$this->SetDrawColor(0,0,0);
	$xmin = 10;
	$xmax = 110;
	$ymin = 251;
	$ymax = 270;
	$this->Line($xmin, $ymin, $xmax, $ymin);
	$this->Line($xmax, $ymin, $xmax, $ymax);
	$this->Line($xmin, $ymin, $xmin, $ymax);
	$this->Line($xmin, $ymax, $xmax, $ymax);

	$this->SetFont_LigneCommandeBold();
	if ($this->afficher_prix) $this->WriteRight("Montant HT: ".catalogue_formateprix($this->commande->fields['total_ht'])." ".chr(128)."     Montant TTC: ".catalogue_formateprix($this->commande->fields['total_ttc'])." ".chr(128),10,265,190);
	}

	function Footer()
	{
		$w = 275;

		$this->SetFont_Copyright();
		$this->SetColor_Black();

		$this->WriteCentered(_PDF_ADRESSE1,0,$w+=4,210);
		$this->WriteCentered(_PDF_ADRESSE2,0,$w+=4,210);

		$this->Image('./templates/frontoffice/unifob/gfx/logo_unifob_nb.png', 158, 276, 5);

		$this->SetFont_LigneCommandeBold();
		$w = 85;
		$this->SetDrawColor(0,0,0);
		$this->DrawArray($w-5,245);

		$this->WriteLine($w,'Réf',12);                 // 30-50
		$this->WriteLine($w,'Description',32);         // 50-140

		if ($this->afficher_prix)
		{
			$this->WriteRight('Prix Net',150,$w,20);       // 150-170
			$this->WriteRight('Qté',170,$w,10);            // 170-180
			$this->WriteRight('Total',180,$w,20);          // 180-200
		}
		else
		{
			$this->WriteRight('Qté',190,$w,10);            // 170-180
		}
	}
}
