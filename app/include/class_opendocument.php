<?php
require_once(DIMS_APP_PATH . '/modules/system/class_xmlmodel.php');
require_once(DIMS_APP_PATH . '/modules/system/xmlparser_content.php');
require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
require_once(DIMS_APP_PATH . '/include/functions/string.php');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class dims_opendocument extends DIMS_DATA_OBJECT {

	private $model;		// model of open document
	private $data;		// data containing
	private $format;
    private $images;
    private $tables;
    private $styles;
    public $arrayXmlns = array(
					"xmlns:office",
					"xmlns:style",
					"xmlns:text",
					"xmlns:table",
					"xmlns:draw",
					"xmlns:fo",
					"xmlns:xlink",
					"xmlns:dc",
					"xmlns:meta",
					"xmlns:number",
					"xmlns:svg",
					"xmlns:chart",
					"xmlns:dr3d",
					"xmlns:math",
					"xmlns:form",
					"xmlns:script",
					"xmlns:ooo",
					"xmlns:ooow",
					"xmlns:oooc",
					"xmlns:dom",
					"xmlns:xforms",
					"xmlns:xsd",
					"xmlns:xsi",
					"xmlns:rpt",
					"xmlns:of",
					"xmlns:xhtml",
					"xmlns:grddl",
					"xmlns:officeooo",
					"xmlns:tableooo",
					"xmlns:drawooo",
					"xmlns:calcext",
					"xmlns:field",
					"xmlns:formx",
					"xmlns:css3t"
				);

	function __construct($model=''){
		if ($model!='' && file_exists($model)) {
			$this->model=$model;
		}
	}

	function setModel($model) {
		if ($model!='' && file_exists($model)) {
			$this->model=$model;
		}
	}

	function getModel() {
		return ($this->model);
	}

	function setData($data) {
		$this->data=$data;
	}

	function setFormat($format) {
		$this->format=$format;
	}

	static function getXMLNS(){
		$o = new dims_opendocument();
		return $o->arrayXmlns;
	}

	/*
	$t = array(
		<name table> => array(
							array( // tr
								<tag> => <value>, // td
								<tag> => <value>, // td
								etc ...
							),
							array( // tr
								<tag> => <value>, // td
								<tag> => <value>, // td
								etc ...
							),
							etc ...
						),
		<name table2> => array(
							array( // tr
								<tag> => <value>, // td
								<tag> => <value>, // td
								etc ...
							),
							ect ...
						),
	)
	*/
	function setTables($t) {
		$this->tables=$t;
	}

	function setStyles($s){
		$this->styles = $s;
	}

    /*
     * Fonctions permettant de définir les images à remplacer
     */
    function setImages($images) {
        $this->images=$images;
    }

    function xml_to_array( $file ) {
        $parser = xml_parser_create();
        xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
        xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
        xml_parse_into_struct( $parser, file_get_contents($file), $tags );
        xml_parser_free( $parser );

        $elements = array();
        $stack = array();
        foreach ( $tags as $tag )
        {
            $index = count( $elements );
            if ( $tag['type'] == "complete" || $tag['type'] == "open" )
            {
                $elements[$index] = array();
                if (isset($tag['tag']))
                    $elements[$index]['name'] = $tag['tag'];
                if (isset($tag['attributes']))
                    $elements[$index]['attributes'] = $tag['attributes'];

                if (isset($tag['value']))
                    $elements[$index]['content'] = $tag['value'];

                if ( $tag['type'] == "open" )
                {    # push
                    $elements[$index]['children'] = array();
                    $stack[count($stack)] = &$elements;
                    $elements = &$elements[$index]['children'];
                }
            }

            if ( $tag['type'] == "close" )
            {    # pop
                $elements = &$stack[count($stack) - 1];
                unset($stack[count($stack) - 1]);
            }
        }
        return $elements[0];
    }

	function createOpenDocument($filedest='',$pathdest='',$images,$downloadfile=true) {
		if ($filedest=='') {
			$filedest='result.pdf';
		}

		$encoded=md5($this->model.session_id());
		$tmp_path=DIMS_TMP_PATH.$encoded;

		if(file_exists(realpath(DIMS_TMP_PATH.$filedest))) {
			unlink(realpath(DIMS_TMP_PATH).$filedest);
		}

		if (file_exists($tmp_path)) {
			dims_deletedir($tmp_path);
		}

		dims_makedir($tmp_path);

		global $xmlmodel;
		global $output;
		global $modeleligne;

		$modele_content = $tmp_path."/content.xml" ;
		$modele_styles = $tmp_path."/styles.xml" ;
		$modele_styles_exp = $tmp_path."/styles2.xml" ;

		$output_path = DIMS_TMP_PATH ;

		if ($pathdest=='') {
			$pathdest=DIMS_TMP_PATH;
		}

		$path=substr($this->model,0,strrpos($this->model,"/"));
		$fichier = substr($this->model,strrpos($this->model,"/")+1);

		dims_unzip($fichier,$path, $tmp_path) ;

        unlink($tmp_path."/".$fichier);

		$xml_content = '';
		$xml_styles = '';

		if (file_exists($modele_content)) {
            $xml_content=file_get_contents($modele_content);
		}
		else die("erreur avec le fichier $modele_content");
        if (file_exists($modele_styles)) {
            $xml_styles=file_get_contents($modele_styles);
		}
		else die("erreur avec le fichier $modele_styles");

        $output="";

        // construction des tags à remplacer
        $xmlmodel = new xmlmodel('');

        // init tmp path variable
        $xmlmodel->setTmpPath($tmp_path);
        //dims_print_r($this->data);die();
        foreach($this->data as $key => $value) {
            $xmlmodel->addtag($key, $value);
        }

        $xml_parser = xmlparser_content();
        if (!xml_parse($xml_parser, $xml_content, TRUE)) {
            printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
        }
        $content = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

        if (isset($this->images) && !empty($this->images)) {
            // conversion en XML du tableau
            $results=$this->xml_to_array($modele_content);
            foreach ($this->images as $elem) {
                if (isset($elem['tag']) && isset($elem['title']) && isset($elem['image'])) {
                    $xmlmodel->replaceImageByTitle($results,$elem['tag'],$elem['title'],$elem['image']);
                }
            }
        }

                // on boucle sur les lignes maintenant
		$xml_modeleligne = $modeleligne;

		$output = '';
		$etape = '';
		$modeleligne = '';

		$xml_parser = xmlparser_content();
		if (!xml_parse($xml_parser, $xml_styles, TRUE)) {
			printf("Erreur XML Style: %s  (ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
		}

        // update des styles
        if(!empty($this->styles)){
        	$this->insertNewStyles($content);
        }

		// update des tableaux
        if(!empty($this->tables) && count($this->tables)){
        	$this->insertTableValues($content);
        }

		$styles = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

		//echo $modele_styles;die();
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

			$output_odt = "temp.odt";
			shell_exec(escapeshellcmd(_DIMS_BINPATH."zip -r ".escapeshellarg("../$output_odt")." ."));

			chdir($cwd);
			if ($this->format != 'ODT') {
				if (!file_exists(realpath($output_path)."/".$filedest)) {

					switch(_DIMS_OOCONVERTER) {
						case 'unoconv':
							$cmd = 'unoconv --stdout ' . realpath($output_path).'/'.$output_odt.' > '.realpath($output_path).'/'.$filedest;
							break;
						case 'jooconverter':
							$cmd = _DIMS_JAVAPATH . ' -jar ' . _DIMS_JOOCONVERTER_PATH .' '. escapeshellarg(realpath($output_path.'/'.$output_odt)) . ' ' . escapeshellarg(realpath($output_path) . '/' . $filedest);
							break;
					}
					shell_exec($cmd);
				}

				unlink(realpath("{$output_path}/{$output_odt}"));

				if ($downloadfile) {
					dims_downloadfile(realpath($output_path)."/".$filedest,$filedest,true, true);
				}
			}
			else {
				if ($downloadfile) {
					dims_downloadfile(realpath($output_path)."/".$output_odt,$filedest, true, true);
				}
				else {
					rename(DIMS_TMP_PATH . '/temp.odt', realpath($output_path).'/'.$filedest);
				}
			}
		}
		else {
			echo "Le dossier $output_path n'est pas accessible en écriture.";
		}
	}

	private function insertTableValues(&$model){
		$xml = new DomDocument();
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->loadXML($model);
		$xtable = new DOMXpath($xml);
		foreach($this->tables as $name => $values){
			$nodelist = $xtable->query("//table:table[@table:name='$name']");
			$table = $nodelist->item(0);
			$lstTr = array();
			$trToDel = null;
			foreach($values as $value2){
				$tag = current(array_keys($value2));
				$res2 = $xtable->query("//table:table[@table:name='$name']/table:table-row/table:table-cell[.='$tag']/parent::table:table-row");
				if($res2->length){

					$trToDel[$res2->item(0)->nodeValue] = $res2->item(0);
					$tr = $res2->item(0)->cloneNode(true);
					// on remplace les valeurs
					$lstTd = $tr->getElementsByTagNameNS("*","*");
					$nbTd = $lstTd->length;
					// echo " --------------------------- $nbTd -- ".$res2->item(0)->nodeValue."<br>";

					for($i=0;$i<$nbTd;$i++){
						if(isset($lstTd->item($i)->nodeName) && $lstTd->item($i)->nodeName == "table:table-cell" && isset($value2[$lstTd->item($i)->nodeValue])){
							$add_styles = "";
							if(isset($lstTd->item($i)->firstChild) && $lstTd->item($i)->firstChild->nodeName == 'text:p') {
								$add_styles = $lstTd->item($i)->firstChild->getAttribute('text:style-name');
							}
							$nodeValue = $lstTd->item($i)->nodeValue;
							if(is_array($value2[$nodeValue])){ // Gestion des listes
								$lct = '<text:list text:style-name="Liste"><text:list-header>';
								$ll = $value2[$nodeValue];
								foreach($ll as $v){
									$lct .= '<text:p '.(!empty($add_styles) ? 'text:style-name="'.$add_styles.'"' : '' ).'>'.$v.'</text:p>';
								}
								$lct .= '</text:list-header></text:list>';
								$ent = "";
								foreach(self::getXMLNS() as $xmlns){
									if($xml->documentElement->hasAttribute($xmlns))
										$ent .= $xmlns.'="'.$xml->documentElement->getAttribute($xmlns).'" ';
								}

								$orgdoc = new DOMDocument;
								$orgdoc->loadXML('<root '.$ent.'>'.$lct."</root>");
								$styles = $orgdoc->getElementsByTagName("*");
								for($z=0;$z<$styles->length;$z++){
									if($styles->item($z)->nodeName == "text:list"){
										$nodeList = $lstTd->item($i)->ownerDocument->importNode($styles->item($z),true);
										self::deleteChildren($lstTd->item($i));
										$lstTd->item($i)->appendChild($nodeList);
									}
								}
							}else{ // Gestion du texte simple ou du style sur cellule
								if(strpos($value2[$nodeValue], "table:style-name=") !== false){
									$styleAttr = explode('"',$value2[$lstTd->item($i)->nodeValue]);
									if(count($styleAttr) > 2 && isset($styleAttr[1])){
										$lstTd->item($i)->setAttribute('table:style-name',$styleAttr[1]);
									}
									$value2[$nodeValue] = "";
								}

								$ent = "";
								foreach(self::getXMLNS() as $xmlns){
									if($xml->documentElement->hasAttribute($xmlns))
										$ent .= $xmlns.'="'.$xml->documentElement->getAttribute($xmlns).'" ';
								}

								$orgdoc = new DOMDocument;
								$lines = explode("\n",$value2[$nodeValue]);
								$orgdoc->loadXML('<root '.$ent.'><text:list text:style-name="Liste"><text:list-header><text:p '.(!empty($add_styles) ? 'text:style-name="'.$add_styles.'"' : '' ).'>'.implode('</text:p><text:p '.(!empty($add_styles) ? 'text:style-name="'.$add_styles.'"' : '' ).'>',$lines).'</text:p></text:list-header></text:list></root>');
								$styles = $orgdoc->getElementsByTagName("*");
								for($z=0;$z<$styles->length;$z++){
									if($styles->item($z)->nodeName == "text:list"){
										$nodeList = $lstTd->item($i)->ownerDocument->importNode($styles->item($z),true);
										self::deleteChildren($lstTd->item($i));
										$lstTd->item($i)->appendChild($nodeList);
									}
								}
							}
							unset($value2[$nodeValue]);
							$nbTd = $lstTd->length;
							$i=0;
						}
					}
					$lstTr[] = $tr;
				}
			}
			foreach($trToDel as $d)
				$table->removeChild($d);
			foreach($lstTr as $t){
				$table->appendChild($t);
			}
		}
		return $model = $xml->saveXML();
	}

	static function deleteChildren($node) {
		while (isset($node->firstChild)) {
			self::deleteChildren($node->firstChild);
			$node->removeChild($node->firstChild);
		}
	}

	private function insertNewStyles(&$model){
		$xml = new DomDocument();
		$xml->loadXML($model);
		$xtable = new DOMXpath($xml);
		$styleNode = $xtable->query("//office:automatic-styles");
		$ent = "";
		foreach(self::getXMLNS() as $xmlns){
			if($xml->documentElement->hasAttribute($xmlns))
				$ent .= $xmlns.'="'.$xml->documentElement->getAttribute($xmlns).'" ';
		}

		$orgdoc = new DOMDocument;
		$orgdoc->loadXML('<root '.$ent.'>'.$this->styles."</root>");
		$styles = $orgdoc->getElementsByTagName("*");
		for($i=0;$i<$styles->length;$i++){
			if($styles->item($i)->nodeName == "style:style"){
				$styleNode->item(0)->appendChild($styleNode->item(0)->ownerDocument->importNode($styles->item($i), true));
			}
		}
		return $model = $xml->saveXML();
	}
}
