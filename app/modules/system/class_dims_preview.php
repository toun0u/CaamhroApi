<?php

/*
 * !!!!!!!! Attention !!!!!!!!
 * L'utilisation des previsualisations nécessite l'installation de html2ps, pdfimages & convert
 */

class dims_preview{
	/**
	 * @name dims_preview
	 * @author Netlor
	 * @link http://netlor.fr
	 */

	/**
	 *@var string
	 *@desc name of preview
	 */
	private $namepreview;

	/**
	 *@var string
	 *@desc type de preview : doc,html, etc.
	 */
	private $type;

	/**
	 *@var string
	 *@desc chemin précisant ou sont les fichiers générés
	 */
	private $pathData;

	/**
	 *@var string
	 *@desc chemin précisant ou sont les fichiers générés
	 */
	private $webPathData;

	/**
	 *@var string
	 *@desc masque utilisee pour filtrer les images du dossier en cours
	 */
	private $mask;

	/**
	 *@var string
	 *@desc Root acces from http connexion
	 */
	private $httpPath;

	/**
	 *@var string
	 *@desc nbImages;
	 */
	private $nbImages;

	/**
	 *@var array
	 *@desc Définit la liste des images qui vont servir à faire des miniatures
	 */
	private $listImages;

	/**
	 *@var array
	 *@desc Définit l'image sélectionnee
	 */
	private $selectedImage;


	/**
	 *@var string
	 *@desc definition de la largeur de travail
	 */
	private $widthform;

	/**
	 *@var string
	 *@desc definition de la hauteur de travail
	 */
	private $heightform;

	/**
	 *@var string
	 *@desc definition de la largeur de colonne
	 */
	private $largeurcolumn;

	/**
	 *@var string
	 *@desc definition de l'id md5 du doc
	 */
	private $md5id;


	private $ratio = array(array('val' => 0.10, 'label' => '1:10', 'selected' => false),
						   array('val' => 0.20, 'label' => '1:5', 'selected' => false),
						   array('val' => 0.25, 'label' => '1:4', 'selected' => false),
						   array('val' => 0.3333, 'label' => '1:3', 'selected' => false),
						   array('val' => 0.5, 'label' => '1:2', 'selected' => false),
						   array('val' => 1, 'label' => '1:1', 'selected' => false),
						   array('val' => 2, 'label' => '2:1', 'selected' => false),
						   array('val' => 3, 'label' => '3:1', 'selected' => false),
						   array('val' => 4, 'label' => '4:1', 'selected' => false),
						   array('val' => 5, 'label' => '5:1', 'selected' => false),
						   array('val' => 6, 'label' => '6:1', 'selected' => false));
	/**
	 * Constructeur
	 * @name dims_preview::__construct()
	 * @param Nombre de colonne
	 * @param Liste des données
	 * @return void
	*/
	public function __construct($namepreview='', $pathData = '', $type='doc',$mask = '',$init=true,$md5id=0){

	    if (isset($_SESSION['dims']['preview'][$namepreview]) && !$init) {
		$this->namepreview=$namepreview;

		$this->type = $_SESSION['dims']['preview'][$this->namepreview]['type'];
		$this->pathData = $_SESSION['dims']['preview'][$this->namepreview]['pathData'];
		$this->webPathData = $_SESSION['dims']['preview'][$this->namepreview]['webPathData'];
		$this->listImages = $_SESSION['dims']['preview'][$this->namepreview]['listImages'];
		$this->selectedImage=$_SESSION['dims']['preview'][$this->namepreview]['selectedImage'];
		$this->nbImages=$_SESSION['dims']['preview'][$this->namepreview]['nbImages'];
		$this->mask=$_SESSION['dims']['preview'][$this->namepreview]['mask'];
		$this->widthform=$_SESSION['dims']['preview'][$this->namepreview]['widthform'];
		$this->heightform=$_SESSION['dims']['preview'][$this->namepreview]['heightform'];
		$this->largeurcolumn=$_SESSION['dims']['preview'][$this->namepreview]['largeurcolumn'];
		$this->md5id=$_SESSION['dims']['preview'][$this->namepreview]['md5id'];
	    }
	    else {
			// on annule la preview
			unset($_SESSION['dims']['preview']);

			if ($namepreview=='') {
			    $this->namepreview='dims_preview'.mt_rand(0, 100);
			}
			else {
			    $this->namepreview=$namepreview;
			}

			$this->type = $type;
			$this->pathData = $pathData;
			$this->webPathData = str_replace("//","/",_DIMS_WEBPATHDATA.$pathData);
			$this->listImages = array();
			$this->selectedImage='';
			$this->nbImages=0;
			$this->largeurcolumn="120";
			$this->mask=$mask;
			$this->md5id=$namepreview;

			// collecte du path pour la construction des urls
			if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $this->httpPath="http://";
			else $this->httpPath="https://";
			$this->httpPath.=$_SERVER['HTTP_HOST'];

			// on parcourt les images qui se trouvent dans le dossier en cours
			// list image
			$listimages=array();

			if (is_dir(DIMS_ROOT_PATH."www/data/".$this->pathData) && is_dir(DIMS_ROOT_PATH."www/data/".$this->pathData)) {
			   	if ($dh = opendir(DIMS_ROOT_PATH."www/data/".$this->pathData)) {
					while (($filename = readdir($dh)) !== false) {
					    if ($filename!="." && $filename!="..") {
							$continue=true;

							if ($this->mask!='') {
							    $continue=false;

							    if (strpos($filename,strtolower($this->mask))!==false && strpos($filename,strtolower('png'))) {
									// on a une image
									//$this->listImages[]=$filename;
									$listimages[]=$filename;
							    }
							}
					    }
					}
					//sort($this->listImages);
					natsort($listimages);
					foreach ($listimages as $imgsrc) {
						$this->listImages[]=$imgsrc;
					}
			    }
			}

			// on calcule le nombre d'images qui se trouvent dans le tableau
			$this->nbImages=sizeof($this->listImages);

			$_SESSION['dims']['preview'][$this->namepreview]['type']=$this->type;
			$_SESSION['dims']['preview'][$this->namepreview]['pathData']=$this->pathData;
			$_SESSION['dims']['preview'][$this->namepreview]['webPathData']=$this->webPathData;
			$_SESSION['dims']['preview'][$this->namepreview]['listImages']=$this->listImages;
			$_SESSION['dims']['preview'][$this->namepreview]['nbImages']=$this->nbImages;
			$_SESSION['dims']['preview'][$this->namepreview]['selectedImage']=$this->selectedImage;
			$_SESSION['dims']['preview'][$this->namepreview]['mask']=$this->mask;
			$_SESSION['dims']['preview'][$this->namepreview]['widthform']=$this->widthform;
			$_SESSION['dims']['preview'][$this->namepreview]['heightform']=$this->heightform;
			$_SESSION['dims']['preview'][$this->namepreview]['largeurcolumn']=$this->largeurcolumn;
			$_SESSION['dims']['preview'][$this->namepreview]['md5id']=$this->md5id;
	    }
	}

	public function getLargeurColumn(){
		return $this->largeurcolumn;
	}
	public function getWidthForm(){
		return $this->widthform;
	}

    public function getHeightForm(){
		return $this->heightform;
	}

	/**
	 * Attribution de valeur
	 * @name dims_preview::setValues()
	 * @param attribut
	 * @param valeur
	 * @return void
	*/
	public function setValues($attribute,$value) {
		$this->$attribute=$value;
		$_SESSION['dims']['preview'][$this->namepreview][$attribute]=$value;
	}

	public function displayAjax($file,$selected = ""){
		$this->selectedImage = $selected;

		global $dims;

		echo '<div class="previewDocument" id="'.$this->namepreview.'" style="height: auto !important;height: 100%;max-height: 100%;">&nbsp;';

		$_SESSION['dims']['preview'][$this->namepreview]['template']=$file;
		echo '</div>';

		?>
		<script type="text/javascript">
			var ias='';

			window['contentImage']=function contentImage(result) {
			    $("#contentselectedtext").html('<img src="./common/img/loading.gif">');

			    // on fait la suite pour activer les boutons
			    //$("a.pastetext").css("display","block");
			}
			window['suiteContentImage']=function suiteContentImage(result) {
			    $("#contentselectedtext").html(result);

			    // on fait la suite pour activer les boutons
			    //$("a.pastetext").css("display","block");
			}

			window['getPasteText']=function getPasteText(stype,idelement) {
			    if (stype=='textarea') {
				document.getElementById(idelement).value=$("#contentselectedtext").html();
				//("#"+idelement).val($("#contentselectedtext").html());
			    }
			}

			window['updateScrollViewThumb']=function updateScrollViewThumb(i) {

			    var p =$("#li-pagepreview"+i);
			    if (p!=null) {
				var position = p.position();
				if (position.top!=null)
				    $("#thumbnail").scrollTop(position.top);
			    }

			}

			window['refreshPreviewDocument']= function refreshPreviewDocument(page) {
			    if (ias!=null && ias !='') {
				    //ias.setOptions({ hide: true });
				    //ias.update();
			    }
				$(".imgareaselect-handle").remove();
				$.ajax({
					type: "GET",
					url: 'admin.php',
					async: false,
					data: {
							'dims_op' : 'refreshPreviewDocumentPage',
							'namepreview': '<? echo $this->namepreview; ?>',
							'page': page
					},
					dataType: "text",
					success: function(data){
							$("#<? echo $this->namepreview;?>").html(data);
					},

					error: function(data){
							//rien à faire
					}
				});
			}

			$(document).ready(function() {
				$("#<? echo $this->namepreview;?>").css("height","100%");
				var heightdiv=$("#<? echo $this->namepreview;?>").outerHeight();
				var widthdiv=$("#<? echo $this->namepreview;?>").outerWidth();

			    $.ajax({
							type: "GET",
							url: 'admin.php',
							async: false,
							data: {
									'dims_op' : 'refreshPreviewDocument',
									'namepreview': '<? echo $this->namepreview; ?>',
									'widthform': widthdiv,
									'heightform': heightdiv
							},
							dataType: "text",
							success: function(data){
									$("#<? echo $this->namepreview;?>").html(data);
							},

							error: function(data){
									//rien à faire
							}
						});
		$(window).resize(function(){
		    heightdiv=$("#<? echo $this->namepreview;?>").outerHeight();
		    widthdiv=$("#<? echo $this->namepreview;?>").outerWidth();
		    $.ajax({
							type: "GET",
							url: 'admin.php',
							async: false,
							data: {
									'dims_op' : 'refreshPreviewDocument',
									'namepreview': '<? echo $this->namepreview; ?>',
									'widthform': widthdiv,
									'heightform': heightdiv
							},
							dataType: "text",
							success: function(data){
									$("#<? echo $this->namepreview;?>").html(data);
							},

							error: function(data){
									//rien à faire
							}
						});
		});
			});
		</script>
		<?
	}

	public function display($file,$selected = ""){
		$this->selectedImage = $selected;

		include(str_replace("..", "", $file));
	}

	public function getSelectChoosePage($imgPrevious = '', $imgNext = '', $selectRatio = false, $sliderRatio = false, $imgPlus = '', $imgMoins = ''){
		$scriptAlt = '';

		$res = '<span class="img_previous">';
		if ($imgPrevious != '' && $this->nbImages > 1 && $this->selectedImage > 0)
			$res .= '<img onclick="javascript:refreshPreviewDocument('.($this->selectedImage-1).');" id="img_prev_'.$this->namepreview.'" style="cursor:pointer;" alt="'.$_SESSION['cste']['_PREVIOUS'].'" title="'.$_SESSION['cste']['_PREVIOUS'].'" src="'.$imgPrevious.'" />';
		$res .= '</span>';

		$res .= "<span class=\"select_page\"><input style=\"width:30px; text-align:right;\" type=\"text\" id=\"choose_page_".$this->namepreview."\" value=\"".($this->selectedImage+1)."\" /><label>&nbsp;/&nbsp;".$this->nbImages."</label></span>";
		$scriptAlt .= "$('span.select_page input#choose_page_".$this->namepreview."').keydown(function(event){
			// Allow: backspace, delete, tab and escape
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
	     // Allow: Ctrl+A
				(event.keyCode == 65 && event.ctrlKey === true) ||
				 // Allow: home, end, left, right
				event.keyCode == 35 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 37) {
					 // let it happen, don't do anything
					 return;
			}
			else if(event.keyCode == 40){ //UP
				if ($(this).val() < ".$this->nbImages.")
					$(this).val(parseInt($(this).val())+1);

			}else if(event.keyCode == 38){ // DOWN
				if ($(this).val() > 1)
					$(this).val($(this).val()-1);
			}else{
				// Ensure that it is a number and stop the keypress
				if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
					event.preventDefault();
				}
			}
		}).keyup(function(event){
			if(event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 13){
				if ($(this).val() != ".($this->selectedImage+1)."){
					if ($(this).val() > ".$this->nbImages.")
						refreshPreviewDocument(".($this->nbImages-1).");
					else if($(this).val() <= 0)
						refreshPreviewDocument(0);
					else
						refreshPreviewDocument($(this).val()-1);
				}
			}
		}).focus();";
		/*$res .= "<span class=\"select_page\"><select onchange=\"javascript:refreshPreviewDocument(this.options[this.selectedIndex].value);\" id=\"choose_page_".$this->namepreview."\">";
		for($i = 0; $i < $this->nbImages;$i++)
			if ($this->selectedImage == $i)
				$res .= '<option selected=true value="'.$i.'">'.($i+1).'</option>';
			else
				$res .= '<option value="'.$i.'">'.($i+1).'</option>';
		$res .= "</select>"."<label>&nbsp;/&nbsp;".$this->nbImages."</label></span>";*/

		$res .= '<span class="img_next">';
		if ($imgNext != '' && $this->nbImages > 1 && $this->selectedImage+1 < $this->nbImages)
			$res .= '<img onclick="javascript:refreshPreviewDocument('.($this->selectedImage+1).');" id="img_next_'.$this->namepreview.'" style="cursor:pointer;" alt="'.$_SESSION['cste']['_NEXT'].'" title="'.$_SESSION['cste']['_NEXT'].'" src="'.$imgNext.'" />';
		$res .= '</span>';

		if ($selectRatio || $sliderRatio || ($imgPlus != '' && $imgMoins != '')){
			$valration = $curRatio = $zoneheight = $zonewidth = $src_w = $src_h = 0;
			$this->updateRatios($valration,$curRatio,$zonewidth,$zoneheight,$src_w, $src_h);
		}

		$scriptRatio = '';
		if ($selectRatio){
			$res.= '<label for="zoom">Zoom</label>
						<select id="ratioimage2_'.$this->namepreview.'">';
			foreach($this->ratio as $id => $rat){
				$scriptRatio .= "ratio[$id]=".$rat['val']."; ";
				if ($rat['selected'])
					$res .= '<option selected=true value="'.$id.'">'.$rat['label'].'</option>';
				else
					$res .= '<option value="'.$id.'">'.$rat['label'].'</option>';
			}
			$res .='</select>';
			$scriptAlt.='
				$( "#ratioimage2_'.$this->namepreview.'" ).change(function() {';
			if ($sliderRatio)
				$scriptAlt.='
						slider2.slider( "value", this.selectedIndex  +1);';

			$scriptAlt.='updateImage(this.selectedIndex);
				});';
		}else
			foreach($this->ratio as $id => $rat)
				$scriptRatio .= "ratio[$id]=".$rat['val']."; ";

		if ($sliderRatio){
			$tmp = '';
			$tmp2 = '';
			if ($selectRatio){
				$scriptAlt .= 'var select2 = $( "#ratioimage2_'.$this->namepreview.'" );';
				$tmp = 'select2[ 0 ].selectedIndex = ui.value - 1;';
				$tmp2 = '"select#ratioimage2_'.$this->namepreview.'"';
			}elseif($imgPlus != '' && $imgMoins != '')
				$tmp2 = '"img#img_plus_'.$this->namepreview.'"';
			$scriptAlt .= '
				var slider2 = $( "<div id=\'slider\'></div>" ).insertAfter( '.$tmp2.' ).slider({
						min: 1,
						max: '.(count($this->ratio)).',
						range: "min",
						value: '.($curRatio+1).',
						slide: function( event, ui ) {
								'.$tmp.'
								updateImage(ui.value - 1);

								//ias.setOptions({ hide: true });
								//ias.update();
						}
				});';
		}

		if (($imgPlus != '' && $imgMoins != '')){
			$scriptAlt .= '
				$("img#img_plus_'.$this->namepreview.'").click(function(){';
			if ($selectRatio)
				$scriptAlt .= 'select2[ 0 ].selectedIndex = select2[ 0 ].selectedIndex+1; $( "#ratioimage2_'.$this->namepreview.'" ).change();';
			elseif ($sliderRatio)
				$scriptAlt.='slider2.slider( "value", slider2.slider( "value")+1); updateImage(slider2.slider( "value")-1);';
			else
				$scriptAlt.='updateImage(courRatio+1);';
			$scriptAlt .= '
					if (courRatio == '.(count($this->ratio)-1).')
						$(this).css("display", "none");
					$("img#img_moins_'.$this->namepreview.'").css("display", "");
				});
				$("img#img_moins_'.$this->namepreview.'").click(function(){';
			if ($selectRatio)
				$scriptAlt .= 'select2[ 0 ].selectedIndex = select2[ 0 ].selectedIndex-1; $( "#ratioimage2_'.$this->namepreview.'" ).change();';
			elseif ($sliderRatio)
				$scriptAlt.='slider2.slider( "value", slider2.slider( "value")-1); updateImage(slider2.slider( "value")-1);';
			else
				$scriptAlt.='updateImage(courRatio-1);';
			$scriptAlt .= '
					if (courRatio == 0)
						$(this).css("display", "none");
					$("img#img_plus_'.$this->namepreview.'").css("display", "");
				});
			';
			$res .= '<span class="img_moins">';
			//if (isset($this->ratio[$curRatio-1]))
				$res .= '<img id="img_moins_'.$this->namepreview.'" src="'.$imgMoins.'" style="cursor:pointer;'.(isset($this->ratio[$curRatio-1])?"":"display:none;").'" />';
			$res .= '</span><span class="img_plus">';
			//if (isset($this->ratio[$curRatio+1]))
				$res .= '<img id="img_plus_'.$this->namepreview.'" src="'.$imgPlus.'" style="cursor:pointer;'.(isset($this->ratio[$curRatio+1])?"":"display:none;").'" />';
			$res .= '</span>';
		}

		$res .= '<script type="text/javascript" language="javascript">
					'.$this->loadDefault($src_w,$src_h,$scriptRatio,$curRatio).'
					$(document).ready(function(){
						'.$scriptAlt.'
					});
				</script>';
		return $res;
	}

	private function updateRatios(&$valratio, &$curRatio, &$zonewidth, &$zoneheight, &$src_w, &$src_h){
		$image = $this->listImages[$this->selectedImage];
		$curfolder=$this->pathData;
		$tmpdir=DIMS_ROOT_PATH.'www/data/'.$this->pathData;
		//$image='image-000.jpg';
		$zonewidth=$this->widthform*2/3;
		$zoneheight=$this->heightform;

		if (file_exists($tmpdir.$image)) {
			$size = getimagesize($tmpdir.$image);

			$src_w = ($size[0] > 0) ? $size[0] : 1;
			$src_h = ($size[1] > 0) ? $size[1] : 1;
		}
		else {
			$src_w = 1;
			$src_h = 1;
		}

		// calcul du ratio courant necessaire
		$valratio=$zonewidth/$src_w;

		for ($j=0;$j<=10;$j++) {
			if ($this->ratio[$j]['val']<$valratio) {
				if (isset($this->ratio[$j-1]))
					$this->ratio[$j-1]['selected'] = false;
				$curRatio= $j;
				$this->ratio[$j]['selected'] = true;
			}
			else {
				$this->ratio[$j]['selected'] = false;
			}
		}
	}

	private function loadDefault($src_w,$src_h,$scriptRatio,$curRatio){
		$res = '';
		if (!isset($this->jsLoad) || (isset($this->jsLoad) && !$this->jsLoad)){
			$res = 'var ratio = new Array('.count($this->ratio).');
			var wimg='.$src_w.';
			var himg='.$src_h.';

			var courRatio='.$curRatio.';
			'.$scriptRatio.'
			window[\'updateImage\'] = function updateImage(cour) {
				// on redimensionne image
				var img=document.getElementById("imgrender_'.$this->namepreview.'");
				var newx=wimg*ratio[cour];
				var newy=himg*ratio[cour];
				courRatio=cour;
				img.width=newx;
				img.height=newy;
			}';
			$this->jsLoad = true;
		}
		return $res;
	}

	/*
	 Attention !!! $selectArea == true nécessite le plugin imgAreaSelect (http://odyniec.net/projects/imgareaselect/)
	*/
	public function zoomImage($selectRatio = true, $selectArea = false, $sliderRatio = true) {
		$image = $this->listImages[$this->selectedImage];
		$valration = $curRatio = $zoneheight = $zonewidth = $src_w = $src_h = 0;
		$this->updateRatios($valration,$curRatio,$zonewidth,$zoneheight,$src_w, $src_h);

		$resultat = '';
		$scriptRatio = '';

		if ($selectRatio ){
			$resultat.= '<div style="width:90%height:40px:">
							<div style="width:300px;margin:0 auto;text-align:center;"><label for="zoom">Zoom</label>
								<select id="ratioimage_'.$this->namepreview.'">';
			foreach($this->ratio as $id => $rat){
				$scriptRatio .= "ratio[$id]=".$rat['val']."; ";
				if ($rat['selected'])
					$resultat .= '<option selected=true value="'.$id.'">'.$rat['label'].'</option>';
				else
					$resultat .= '<option value="'.$id.'">'.$rat['label'].'</option>';
			}

			$resultat .=		'</select>
							</div>
						</div>';
		}else
			foreach($this->ratio as $id => $rat)
				$scriptRatio .= "ratio[$id]=".$rat['val']."; ";
		$resultat .= '
					<div id="page1-div" style="overflow:auto;width:100%;text-align: center;height:'.$zoneheight.'px;">
						<img id="imgrender_'.$this->namepreview.'"  src="'.$this->webPathData.$image.'" alt="background image"/>
					</div>';

		$resultat.='
		<script type="text/javascript">
			'.$this->loadDefault($src_w,$src_h,$scriptRatio,$curRatio).'
			$(document).ready(function(){';

		if ($selectArea) {
			$resultat.='

				ias=$("#imgrender_'.$this->namepreview.'").imgAreaSelect({
					handles: true,
					 instance: true,
					 onSelectStart:function (img,selection) {
						//ias.setOptions({ show: true });
					},
					onSelectChange: function (img, selection) {
						$(".imgareaselect-outer").css("display", "block");

					},
					onSelectEnd: function (img, selection) {
						contentImage();
						dims_xmlhttprequest_tofunction("admin.php", "dims_op=cropcontentimage&curfolder='.$this->pathData.'&image='.$image.'&x="+(selection.x1/ratio[courRatio])+"&y="+(selection.y1/ratio[courRatio])+"&w="+(selection.width/ratio[courRatio])+"&h="+(selection.height/ratio[courRatio]),suiteContentImage);

						$(".imgareaselect-outer").css("display", "none");
						$(".imgareaselect-handle").css("display", "block");
						$(".gareaselect-selection").css("display", "block");
						$(".gareaselect-selection").css("visibility", "visible");

					}
				});

			//$("#imgrender_'.$this->namepreview.'").overscroll();

		';

		}
		if ($sliderRatio){
			$tmp = '';
			if ($selectRatio){
				$resultat .= 'var select = $( "#ratioimage_'.$this->namepreview.'" );';
				$tmp = 'select[ 0 ].selectedIndex = ui.value - 1;';
			}
			$resultat.='

				var slider = $( "<div id=\'slider\'></div>" ).insertAfter( "select#ratioimage_'.$this->namepreview.'" ).slider({
						min: 1,
						max: '.(count($this->ratio)).',
						range: "min",
						value: '.($curRatio+1).',
						slide: function( event, ui ) {
								'.$tmp.'
								updateImage(ui.value - 1);

								//ias.setOptions({ hide: true });
								//ias.update();
						}
				});';
		}

		if ($selectRatio){
			$resultat.='
				$( "#ratioimage_'.$this->namepreview.'" ).change(function() {';
			if ($sliderRatio)
				$resultat.='
						slider.slider( "value", this.selectedIndex  +1);';

			$resultat.='updateImage(this.selectedIndex);
				});';
		}

		$resultat.='
				updateImage('.$curRatio.');
		if ($("div#'.$this->namepreview.' ul").outerHeight() > $("div#'.$this->namepreview.' div.thumbnail").outerHeight()){
		    $("div#'.$this->namepreview.'refresh").width($("div#'.$this->namepreview.'refresh").outerWidth()-33);
		    $("div#'.$this->namepreview.' div.thumbnail").width($("div#'.$this->namepreview.' div.thumbnail").outerWidth()+30);
		}
			});
		</script>';

		return $resultat;

	}
}

?>
