<?
class xmlmodel
{
	var $array_tags;
	var $content;
	var $tmppath;

	function xmlmodel($content)
	{
		$this->array_tags = array();
		$this->content = $content;
	}

	function setTmpPath($path) {
		$this->tmppath=$path;
	}

	function addtag($tag, $value, $align = 'left')
	{
		//$this->array_tags[$tag]['value'] = mb_convert_encoding(str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $value),'UTF-8', 'LATIN1');
		//$value = mb_convert_encoding($value,'UTF-8', 'ISO-8859-15');
		//$value = iconv("ISO-8859-1", "UTF-8", $value);
		$this->array_tags[$tag]['value'] = str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $value);
		$this->array_tags[$tag]['align'] = $align;
        $tag=str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $tag);
		$this->array_tags[$tag]['value'] = str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $value);
		$this->array_tags[$tag]['align'] = $align;
		//echo '<br>'.$value.' => '.mb_detect_encoding($value, 'ISO-8859-15, ISO-8859-1, ASCII').' => '.mb_convert_encoding($value,'UTF-8', 'LATIN1');
	}

    public function replaceImageByTitle($struct,$name,$title,$imgsrc) {
    	if(file_exists($imgsrc)){
            $result=$this->searchInArray($struct,$name,$title,$imgsrc,null);
            if ($result!==false) {
                // on a trouve l'objet les infos sont dans le sous tableau des children
                if (isset($result['children'])) {
                    // on parcourt le tableau de children
                    foreach ($result['children'] as $child) {
                        if ($child['name']=="draw:image" && isset($child['attributes']['xlink:href'])) {
                            // on lit le href
                            if (file_exists($this->tmppath."/".$child['attributes']['xlink:href']) && file_exists($imgsrc)){
                                // on remplace l'image par celle fournie
                                if (!copy($imgsrc,$this->tmppath."/".$child['attributes']['xlink:href'])) {
                                    echo 'error from copy image';die();
                                }
                            }
                        }
                    }
                }   
            }
        }
    }
    
    private function searchInArray($elem,$name,$title,$parent) {
        if (isset($elem['name']) && isset($elem['content']) && $elem['content']==$title) {
            return $parent;
        }
        else {
            if (!empty($elem['children'])) {
                foreach ($elem['children'] as $child) {
                    $result=$this->searchInArray($child,$name,$title,$elem);
                    if ($result!==false) {
                        return $result;
                    }
                }
            }
        }
        return false;
    }
        
	public function addImage($key, $value) {
        $filename = strtok(strrchr($value,'/'),'/.');
        $file = substr(strrchr($value,'/'),1);

        //$xml = "
        //<draw:frame draw:style-name=\"fr1\" draw:name=\"".$filename."\" text:anchor-type=\"paragraph\" svg:width=\"5cm\" svg:height=\"8cm\" draw:z-index=\"0\"><draw:image xlink:href=\"Pictures/".$file."\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/></draw:frame>;";
        $xml = "
        <draw:frame draw:style-name=\"fr1\" draw:name=\"".$filename."\" text:anchor-type=\"page\" text:anchor-page-number=\"1\" svg:x=\"6.209cm\" svg:y=\"3.806cm\" svg:width=\"0.564cm\" svg:height=\"0.564cm\" draw:z-index=\"5\"><draw:image xlink:href=\"Pictures/".$file."\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/></draw:frame>";
        $this->addtag($key,$xml);

        dims_makedir(realpath($this->tmppath)."/Pictures");

        copy(realpath($value),realpath($this->tmppath)."/Pictures/".$file);
        chmod(realpath($this->tmppath)."/Pictures".$file, 0777);

    }

	function render($optional_content = '') {
            
		if ($optional_content != '') $this->content = $optional_content;
		$tags=array();
		$values=array();
		foreach($this->array_tags as $key => $tag) {
			$tags[] = $key;
			$values[] = $tag['value'];
			//$values[] = utf8_encode($tag['value']);
			 //echo '<br>'.$tag['value'].' => '.mb_detect_encoding($tag['value'], 'ISO-8859-15, ISO-8859-1, ASCII').' => '.mb_convert_encoding($tag['value'],'UTF-8', 'ISO-8859-15');
			 //echo '<br>'.$values[] = mb_convert_encoding($tag['value'],'UTF-8', 'ISO-8859-15, ISO-8859-1, ASCII');
		}
                /*
                if (strpos($this->content,"VERSION")>0) {
                    echo $this->content;
                    dims_print_r($tags);
                    dims_print_r($values);
                    //echo "res:".str_replace($tags,$values,$this->content);die();
                }*/
                
		return(str_replace($tags,$values,$this->content));
	}
}
?>
