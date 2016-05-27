<?php
class xmlmodel {
	var $array_tags;
	var $content;

	function xmlmodel($content) {
		$this->array_tags = array();
		$this->content = $content;
	}

	function addtag($tag, $value, $align = 'left') {
		$this->array_tags[$tag]['value'] = $value;
		$this->array_tags[$tag]['align'] = $align;
	}

	function render($optional_content = '') {
		$tags = array();
		$values = array();

		if ($optional_content != '') $this->content = $optional_content;

		foreach ($this->array_tags as $key => $tag) {
			$tags[] = $key;
			$values[] = mb_convert_encoding($tag['value'], 'UTF-8', 'ISO-8859-15');
		}

		return (str_replace($tags, $values, $this->content));
	}

}
