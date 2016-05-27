<?php
define ('FPDF_FONTPATH', './lib/fpdf/font/');

include_once './lib/fpdf/fpdf.php';

class pdf_promotion extends FPDF {
	var $descMaxLenght = 62;

	function GetDateLocal() {return date(_DATEFORMAT);}
	function GetTimeLocal() {return date(_TIMEFORMAT);}

	function SetFont_Title() {
		$this->SetFont('Arial', 'B', 20);
	}
	function SetFont_Rub1() {
		$this->SetFont('Arial', 'B', 14);
	}
	function SetFont_Rub2() {
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

	function SetFont_Copyright() {
		$this->SetFont('Arial', '', 8);
	}


	function SetFont_Promo() {
		$this->SetFont('Arial', 'B', 10);
		$this->SetTextColor(0, 193, 92);
	}

	function SetColor_DarkBlue() {
		$this->SetTextColor(0, 33, 90);
	}
	function SetColor_DarkGreen() {
		$this->SetTextColor(0, 90, 33);
	}



	function SetColor_Standard() {
		$this->SetTextColor(25,9,134);
	}

	function SetColor_Black() {
		$this->SetTextColor(0,0,0);
	}

	function SetColor_Highlighted() {
		$this->SetTextColor(200,0,200);
	}

	function WriteTitle($w, $text) {
		$this->DrawHLine($w);
		$this->DrawHLine($w+9);
		$this->SetFont_Title();
		$this->Text((210 - $this->GetStringWidth($text))/2, $w + 7, $text);
		$w+=15;
	}

	function WriteLine($w, $text, $x = -1) {
		if ($text!='') {
			if ($x == -1) $this->Text((210 - $this->GetStringWidth($text))/2, $w, $text);
			else $this->Text($x, $w, $text);
			$w+=5;
		}
	}

	function WriteCentered($text, $x, $y, $w) {
		if ($text!='') {
			$this->Text( $x + ($w - $this->GetStringWidth($text))/2, $y, $text);
		}
	}

	function WriteRight($text, $x, $y, $w) {
		if ($text!='') {
			$this->Text( $x + $w - $this->GetStringWidth($text), $y, $text);
		}
	}

	function DrawHLine($w) {
		$this->Line(0, $w, 210, $w);
	}

	function Header() {
		$w = 10;

		// numero de page
		$this->SetFont_Small();
		$this->WriteCentered('Page '.$this->PageNo().'/{nb}', 0, 285, 210);

		$this->WriteLine(285, $this->tiers->getIntitule(), 10);
	}

	function Content() {
		$w = 10;
		$ts = dims_createtimestamp();

		$this->SetFont_Title();
		$this->SetColor_Black();
		if (sizeof($this->promo)) {
			$this->WriteLine($w + 30, utf8_decode($this->promo['libelle']), 10);

			$this->SetFont_LigneCommande();
			$date_debut = dims_timestamp2local($this->promo['date_debut']);
			$date_fin = dims_timestamp2local($this->promo['date_fin']);
			$this->WriteLine($w + 35, 'du '.$date_debut['date'].' au '.$date_fin['date'], 10);
		} else {
			$this->WriteLine($w + 30, "Promotions", 10);
		}

		// recherche du logo
		if ($this->tiers->getPhotoPath(400)) {
			$this->Image($this->tiers->getPhotoWebPath(400), 55, 10, 100);
		}


		$w = 60;

		foreach ($this->articles as $rub0 => $a_rub1) {
			if ($w > 240) {
				$w = 20;
				$this->AddPage();
			}

			$rub0 = str_replace('<br/>', '', $rub0);

			$this->SetColor_DarkBlue();
			$this->SetFont_Rub1();
			$this->WriteLine($w, utf8_decode($rub0), 30);
			$w+=5;

			foreach ($a_rub1 as $rub1 => $a_articles) {
				if ($w > 240) {
					$w = 20;
					$this->AddPage();
				}

				$this->SetColor_DarkGreen();
				$this->SetFont_Rub2();
				$this->WriteLine($w, utf8_decode($rub1), 10);
				$w+=8;

				foreach ($a_articles as $ref_article) {
					$this->SetColor_Black();
					$this->SetFont_LigneCommande();

					if ($w > 270) {
						$w = 20;
						$this->AddPage();
					}

					$article = new article();
					$article->findByRef($ref_article);

					// Recherche de la photo
					$vignette = $article->getVignette(100);
					if ($vignette != null) {
						$dmax = 12;
						$dim = catalogue_getImageDimensions($vignette);
						if ($dim['w'] > $dim['h']) {
							$this->Image('.'.$vignette, 10, $w - 3, $dmax);
						}
						else {
							$this->Image('.'.$vignette, 10, $w - 3, 0, $dmax);
						}
					}

					$this->WriteLine($w, $article->fields['reference'], 25);

					$this->SetFont_LigneCommande();

					// prix barre
					$prix_orig = catalogue_formateprix(catalogue_getprixarticle($article, 1, 1));
					$barre_orig = '';
					for ($c = 0; $c < strlen($prix_orig) + 6; $c++) {
						$barre_orig .= '_';
					}
					$this->WriteRight($barre_orig, 190, $w - 2, 11);
					$this->WriteRight($prix_orig.' E TTC', 190, $w, 10);

					// prix promo
					$this->SetFont_Promo();
					$this->WriteRight(catalogue_formateprix(catalogue_getprixarticle($article)).' E TTC', 190, $w + 5, 10);
					$this->SetColor_Black();

					// Affichage de la designation
					$this->SetFont_LigneCommandeBold();


					// Suppression de caractères non supportés
					$article->fields['label'] = str_replace('œ', 'oe', $article->fields['label']);
					$article->fields['label'] = utf8_decode($article->fields['label']);

					if (strlen($article->fields['label']) > $this->descMaxLenght) {
						// decoupage de la designation en plusieurs lignes
						// qui font maxi la taille definie sans couper les mots
						$a_desc = explode(' ', $article->fields['label']);
						$desc = '';
						foreach ($a_desc as $mot) {
							if (strlen($desc) + strlen($mot) + 1 > $this->descMaxLenght) {
								$this->WriteLine($w, $desc, 45);
								$desc = '';
								$w += 4;
							}
							if ($desc != '') $desc .= ' ';
							$desc .= $mot;
						}
						if ($desc != '') {
							$this->WriteLine($w, $desc, 45);
						}
					} else {
						$this->WriteLine($w, $article->fields['label'], 45);
					}

					if ($article->fields['page']) {
						$this->SetFont_LigneCommandeDetail();
						$array_detail = array('Page '.$article->fields['page'].' du catalogue');

						$added = 0;
						foreach($array_detail as $detail) {
							if (sizeof($detail) > 50) $detail = substr($detail, 0, 50);
							if ($detail!='') { $this->WriteLine($w + 4, $detail, 45); $w += 3; $added++; }
						}
					}
					$this->SetFont_LigneCommande();
					$w += 13 - $added;
				}
			}
		}
	}

}
