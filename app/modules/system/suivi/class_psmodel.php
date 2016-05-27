<?
class psmodel
{
	var $array_tags;
	var $ps;

	function psmodel($ps)
	{
		$this->array_tags = array();
		$this->ps = $ps;
	}

	function addtag($tag, $value, $align = 'left')
	{
		$this->array_tags[$tag]['value'] = $value;
		$this->array_tags[$tag]['align'] = $align;
	}


	function render()
	{
		foreach($this->array_tags as $key => $tag)
		{
			$tags[] = $key;
			if ($tag['align'] == 'right')
			{
				$diff = strlen($key) - 2 - strlen($tag['value']);
				for($i=0;$i<$diff;$i++) $tag['value'] = " {$tag['value']}";
			}
			$values[] = $tag['value'];
		}

		return(str_replace($tags,$values,$this->ps));
	}

}
