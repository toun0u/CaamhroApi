<?php
define ('FPDF_FONTPATH', DIMS_APP_PATH.'include/fpdf/font/');

include_once DIMS_APP_PATH.'include/fpdf/fpdf.php';

class pdf_commande extends FPDF {
	public $commande = null;
	public $client = null;
	public $user = null;
	public $service = null;

	function GetDateLocal() {return date(_DATEFORMAT);}
	function GetTimeLocal() {return date(_TIMEFORMAT);}

	function SetFont_Title() {
		$this->SetFont('Arial', 'B', 12);
	}

	function SetFont_Standard() {
		$this->SetFont('Arial', '', 12);
	}

	function SetFont_LigneCommande() {
		$this->SetFont('Arial', '', 10);
	}

    function SetFont_LigneCommandeDetail() {
		$this->SetFont('Arial', '', 7);
	}

	function SetFont_LigneCommandeBold() {
		$this->SetFont('Arial', 'B', 10);
	}

    function SetFont_Small() {
		$this->SetFont('Arial', '', 8);
	}

	function SetFont_StandardBold() {
		$this->SetFont('Arial', 'B', 12);
	}

	function SetColor_Standard() {
		$this->SetTextColor(25,9,134);
	}

	function SetFont_CopyrightBold() {
		$this->SetFont('Arial', 'B', 8);
	}

	function SetFont_Copyright() {
		$this->SetFont('Arial', '', 8);
	}

	function SetColor_Black() {
		$this->SetTextColor(0,0,0);
	}

	function SetColor_Highlighted() {
		$this->SetTextColor(200,0,200);
	}

	function WriteTitle($w, $text) {
		$this->DrawHLine($w);
		$this->DrawHLine($w + 9);
		$this->SetFont_Title();
		$this->Text((210 - $this->GetStringWidth($text)) / 2, $w + 7, $text);
		$w += 15;
	}

	function WriteLine($w, $text, $x = -1) {
		if ($text != '') {
			if ($x == -1) $this->Text((210 - $this->GetStringWidth($text)) / 2, $w, $text);
			else $this->Text($x, $w, $text);
			$w += 5;
		}
	}

	function WriteCentered($text, $x, $y, $w) {
		if ($text != '') {
			$this->Text( $x + ($w - $this->GetStringWidth($text)) / 2, $y, $text);
		}
	}

	function WriteLeft($text, $x, $y, $w) {
		if ($text != '') {
			$this->Text( $x, $y, $text);
		}
	}

	function WriteRight($text, $x, $y, $w) {
		if ($text != '') {
			$this->Text( $x + $w - $this->GetStringWidth($text), $y, $text);
		}
	}

	function DrawHLine($w) {
		$this->Line(0, $w, 210, $w);
	}

	function RotatedText($x, $y, $txt, $angle) {
		//Rotation du texte autour de son origine
		$this->Rotate($angle, $x, $y);
		$this->Text($x, $y, $txt);
		$this->Rotate(0);
	}

	function DrawArray($wstart, $w) {
		if ($this->afficher_prix) {
			$this->SetFillColor(220, 220, 220);
			$this->Rect(10, $wstart, 192, 7, 'F');
			$this->Line(10, $wstart, 202, $wstart);
			$this->Line(10, $wstart + 7, 202, $wstart + 7);
			$this->Line(10, $w, 202, $w);
			$this->Line(10, $wstart, 10, $w);
			$this->Line(30, $wstart, 30, $w);
			$this->Line(154, $wstart, 154, $w);
			$this->Line(172, $wstart, 172, $w);
			$this->Line(182, $wstart, 182, $w);
			$this->Line(202, $wstart, 202, $w);
		} else {
			$this->SetFillColor(220, 220, 220);
			$this->Rect(10, $wstart, 192, 7, 'F');
			$this->Line(10, $wstart, 202, $wstart);
			$this->Line(10, $wstart + 7, 202, $wstart + 7);
			$this->Line(10, $w, 202, $w);
			$this->Line(10, $wstart, 10, $w);
			$this->Line(30, $wstart, 30, $w);
			$this->Line(192, $wstart, 192, $w);
			$this->Line(202, $wstart, 202, $w);
		}
	}

	function Header() {
		$w = 10;
		global $template_path;

		if (isset($this->commande->fields['exceptionnelle']) && $this->commande->fields['exceptionnelle']) {
			//Affiche le filigrane
			$this->SetFont('Arial','B',50);
			$this->SetTextColor(200, 200, 200);
			$this->RotatedText(30, 230, 'Commande exceptionnelle', 45);
		}

		// ADRESSES BAS DE PAGE
		$this->SetFont_Title();
		$this->SetColor_Black();

		/*$this->Image($http_host.$template_path.'/gfx/logo1_nb.png', 30, 5, 30);
		$this->Image($http_host.$template_path.'/gfx/logo2_nb.png', 10, 10, 70);
		if (file_exists($http_host.$template_path.'/gfx/logo_3_nb.png')) {
			$this->Image($http_host.$template_path.'/gfx/logo_3_nb.png', 150, 12, 50);
		}*/


		if (isset($this->commande->fields['exceptionnelle']) && $this->commande->fields['exceptionnelle']) {
			$libelle_commande = 'Commande exceptionnelle n°';
		} else {
			$libelle_commande = utf8_decode(dims_constant::getVal('_ORDER_NO'));
		}
		$libelle_commande .= " ".$this->commande->fields['id_cde'];
		if ($this->commande->fields['libelle'] != '') {
			$libelle_commande .= ' : \''.$this->commande->fields['libelle'].'\'';
		}

		$this->WriteLine($w + 65, $libelle_commande, 6);

		$this->SetFont_Standard();
		$w = 40;


		// Date
		$date_validation = dims_timestamp2local($this->commande->fields['date_validation']);
		$this->WriteLeft(dims_constant::getVal('_DIMS_DATE')." : ".$date_validation['date'], 160, 40, 40);

		// Adresse de facturation
		$this->WriteLine($w + 10,html_entity_decode($this->commande->fields['cli_nom']), 120);
		$this->WriteLine($w + 15,html_entity_decode($this->commande->fields['cli_adr1']), 120);
		($this->commande->fields['cli_adr2'] != '') ? $this->WriteLine($w + 20, html_entity_decode($this->commande->fields['cli_adr2']), 120) : $w -= 5;
		$this->WriteLine($w + 25, $this->commande->fields['cli_cp'] .' '. html_entity_decode($this->commande->fields['cli_ville']), 120);

		if (isset($this->commande->fields['CRUEL']) && $this->commande->fields['CRUEL'] != '') {
			$w = 40;
			// Adresse de livraison
			$this->WriteLine($w,'A livrer à :',10);
			$this->WriteLine($w+10,html_entity_decode($this->commande->fields['CNOML']),20);
			$this->WriteLine($w+15,html_entity_decode($this->commande->fields['CRUEL']),20);
			($this->commande->fields['CAUXL'] != '') ? $this->WriteLine($w+20,html_entity_decode($this->commande->fields['CAUXL']),20) : $w -= 5;
			$this->WriteLine($w+25,$this->commande->fields['CPPTLL'] ." ". html_entity_decode($this->commande->fields['CVILL']),20);
		}
	}

	function Content() {
		$w = 91;

		$this->SetFont_LigneCommande();
		$lignes = $this->commande->getlignes('id_article');
		foreach ($this->commande->getlignes() as $article) {
			if ($w > 240) {
				$w = 91;
				$this->AddPage();
			}

			/*include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/include/class_selection.php';
			$selection = new selection();
			($selection->open($_SESSION['catalogue']['CREF'],$article->fields['PREF']) && $selection->fields['selection'] == 1) ? $sel_img = "./templates/frontoffice/unifob/gfx/selection_nb.png" : $sel_img = "";

			$refimage = substr($article->fields['image'],0,strlen($article->fields['image'])-4);
			$imagefile = "./photos/$refimage.jpg";
			*/


			$detail_produit = '';
			$detail1 = '';
			$detail2 = '';
			$detail3 = '';
			$detail = $article->fields;
			$description = '';

			if (($desc = $article->get('label'))!='') {
				$description = str_replace('', "\n", $desc);
				if (substr($description,0,1) == '#') $description = '';
			}

			if (isset($detail['detail1']) && $detail['detail1'] != '') $detail1 = "{$detail['detail1']} : ";
			if (isset($detail['libelle1']) && $detail['libelle1']!='') $detail1 .= "{$detail['libelle1']}";

			if (isset($detail['detail2']) && $detail['detail2'] != '') $detail2 = "{$detail['detail2']} : ";
			if (isset($detail['libelle2']) && $detail['libelle2']!='') $detail2 .= "{$detail['libelle2']}";

			if (isset($detail['detail3']) && $detail['detail3'] != '') $detail3 = "{$detail['detail3']} : ";
			if (isset($detail['libelle3']) && $detail['libelle3']!='') $detail3 .= "{$detail['libelle3']}";

			if ($description != '') $detail_produit = "$description\n";
			$detail_produit .= $detail1;
			if ($detail_produit != '') $detail_produit .= "\n";
			if ($detail2 != '') $detail_produit .= $detail2;
			if ($detail_produit != '' && $detail2 != '' && $detail3 != '') $detail_produit .= "\n";
			if ($detail3 != '') $detail_produit .= $detail3;

			if (isset($detail['marque']) && $detail['marque'] != '') $detail_produit .= "\nMarque : {$detail['marque']}";

			$cmd_detail = $lignes[$article->fields['id_article']]->fields;
			$this->WriteRight(cata_arrayDecode($cmd_detail['ref']),10,$w,18);
			$this->WriteLeft(cata_arrayDecode(utf8_decode($detail_produit)), 32, $w, 120);

			if ($this->afficher_prix) {
				$this->SetFont_LigneCommande();
				$this->WriteRight(catalogue_formateprix($cmd_detail['pu_remise']),150,$w,20);
				$this->WriteRight($cmd_detail['qte'],170,$w,10);
				$this->WriteRight(catalogue_formateprix($cmd_detail['pu_remise']*$cmd_detail['qte']),180,$w,20);
				$this->SetXY(32,$w);
				$this->SetFont_LigneCommandeBold();
				//$this->MultiCell(120,4,$article->fields['PDES']);
				$w = $this->GetY();
			} else {
				$this->SetFont_LigneCommande();
				$this->WriteRight($cmd_detail['qte'],190,$w,10);
				$this->SetXY(32,$w);
				$this->SetFont_LigneCommandeBold();
				//$this->MultiCell(160,4,$article->fields['PDES']);
				$w = $this->GetY();
			}

			$this->SetDrawColor(220,220,220);
			$this->Line(10, $w + 1, 202, $w + 1);

			$this->SetXY(42,$w+2);
			$w = $this->GetY()+5;


			$this->SetFont_LigneCommande();
		}


		// Commentaire
		//$com = $this->commande->fields['commentaire2'];
		$com = '';
		if ($this->commande->fields['commentaire'] != '') {
			if ($com != '') {
				$com .= "\n".stripslashes(str_replace('\r\n', "\r\n", $this->commande->fields['commentaire']));
			} else {
				$com = stripslashes(str_replace('\r\n', "\r\n", $this->commande->fields['commentaire']));
			}
		}

		$this->WriteLine(250, dims_constant::getVal('_DIMS_COMMENTS').' :', 11);
		$this->SetXY(10, 252);
		$this->MultiCell(100, 3, html_entity_decode($com, ENT_QUOTES));

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
		if ($this->afficher_prix) {
			$this->WriteLeft(dims_constant::getVal('_DUTY_FREE_AMOUNT')." : ".catalogue_formateprix($this->commande->fields['total_ht'])." ".chr(128)."     ".dims_constant::getVal('_TOTAL_AMOUNT_WITH_DUTY')." : ".catalogue_formateprix($this->commande->fields['total_ttc'])." ".chr(128),115,255,190);
			if (_PLUGIN_AUTOCONNECT) $this->WriteLeft(dims_constant::getVal('_PORT_HT')." : ".catalogue_formateprix($this->commande->fields['port'])." ".chr(128),115,260,190);

			$bureau = ($this->user->fields['comments'] != '') ? ' ('.$this->user->fields['comments'].')' : '';
            $this->WriteLeft(dims_constant::getVal('_DIMS_LABEL_USER')." : ". $this->commande->fields['user_name'].$bureau, 115, 265, 190);
			$this->WriteLeft(dims_constant::getVal('_SERVICE')." : ". $this->service, 115, 270, 190);
		}
	}

	function Footer() {
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']); // FIXME: Avoid globals/session

		$tiers = $work->getTiers();

		$w = 275;

		$this->SetFont_Copyright();
		$this->SetColor_Black();

		$this->WriteCentered($tiers->get('intitule'), 0, $w += 4, 210);
		$this->WriteCentered($tiers->get('adresse'), 0, $w += 4, 210);
		$this->WriteCentered($tiers->get('codepostal'), 0, $w += 4, 210);

		//$this->Image('./templates/frontoffice/unifob/gfx/logo_unifob_nb.png', 158, 276, 5);

		$this->SetFont_LigneCommandeBold();
		$w = 85;
		$this->SetDrawColor(0,0,0);
		$this->DrawArray($w-5,245);

		$this->WriteLine($w,dims_constant::getVal('REF'),12);                 // 30-50
		$this->WriteLine($w,dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),32);         // 50-140

		if ($this->afficher_prix) {
			$this->WriteRight(dims_constant::getVal('PU_HT'),150,$w,20);       // 150-170
			$this->WriteRight(utf8_decode(dims_constant::getVal('SHORT_QUANTITY')),170,$w,10);            // 170-180
			$this->WriteRight(ucfirst(dims_constant::getVal('_DIMS_LABEL_TOTAL')),180,$w,20);          // 180-200
		} else {
			$this->WriteRight(dims_constant::getVal('SHORT_QUANTITY'),190,$w,10);            // 190-200
		}
	}
}
