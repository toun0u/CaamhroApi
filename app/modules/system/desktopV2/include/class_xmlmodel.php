<?
class xmlmodel
{
	var $array_tags;
	var $content;

	function xmlmodel($content)
	{
		$this->array_tags = array();
		$this->content = $content;
	}

	function addtag($tag, $value, $align = 'left')
	{
		//$this->array_tags[$tag]['value'] = mb_convert_encoding(str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $value),'UTF-8', 'LATIN1');
		//$value = mb_convert_encoding($value,'UTF-8', 'ISO-8859-15');
		//$value = iconv("ISO-8859-1", "UTF-8", $value);
		$this->array_tags[$tag]['value'] = str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $value);
		$this->array_tags[$tag]['align'] = $align;
		//echo '<br>'.$value.' => '.mb_detect_encoding($value, 'ISO-8859-15, ISO-8859-1, ASCII').' => '.mb_convert_encoding($value,'UTF-8', 'LATIN1');
	}

	function render($optional_content = '')
	{
		if ($optional_content != '') $this->content = $optional_content;

		foreach($this->array_tags as $key => $tag)
		{
			$tags[] = $key;
			$values[] = $tag['value'];
			//$values[] = utf8_encode($tag['value']);
			 //echo mb_detect_encoding('gna gna gna €', 'ISO-8859-15, ISO-8859-1, ASCII');
			 //echo '<br>'.$tag['value'].' => '.mb_detect_encoding($tag['value'], 'ISO-8859-15, ISO-8859-1, ASCII').' => '.mb_convert_encoding($tag['value'],'UTF-8', 'ISO-8859-15');
			 //echo '<br>'.$values[] = mb_convert_encoding($tag['value'],'UTF-8', 'ISO-8859-15, ISO-8859-1, ASCII');

		}
		return(str_replace($tags,$values,$this->content));
	}

}
?>
