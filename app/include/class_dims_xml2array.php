<?
class dims_xml2array {

	var $parser;
	var $node_stack = array();
	var $content;
	var $array;
	var $c;
	var $currentdata;

	function parse($xmlcontent="") {
		// set up a new XML parser to do all the work for us
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser, "characterData");

		// Build a Root node and initialize the node_stack...
		$this->node_stack = array();

		$this->xmlarray['root'] = array();
		$this->node_stack[] = &$this->xmlarray['root'];

		// parse the data and free the parser...
		xml_parse($this->parser, $xmlcontent);
		xml_parser_free($this->parser);
		return($this->xmlarray);
	}

	function parseFile($filename)
	{
		$xmlcontent = '';

		if (file_exists($filename))
		{
			$fd = fopen($filename,"r");
			if ($fd)
			{
				while (!feof($fd)) $xmlcontent .= fgets($fd, 4096);
				fclose($fd);
			}

			return($this->parse($xmlcontent));
		}
		else return(false);
	}

	function startElement($parser, $name, $attrs)
	{
		$this->currentdata = '';
		$this->node_stack[] = &$this->node_stack[sizeof($this->node_stack)-1][$name][];
	}

	function endElement($parser, $name)
	{
		if (trim($this->currentdata) != '') $this->node_stack[(sizeof($this->node_stack)-1)] = $this->currentdata;
		array_pop($this->node_stack);
		$this->currentdata = '';
	}

	function characterData($parser, $data)
	{
		$this->currentdata .= $data;
	}



/*
	function dims_xml2array()
	{
	}

	function parse($xmlcontent="")
	{
		// set up a new XML parser to do all the work for us
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser, "characterData");

		// Build a Root node and initialize the node_stack...
		$this->node_stack = array();
		$this->startElement(null, "root", array());

		// parse the data and free the parser...
		xml_parse($this->parser, $xmlcontent);
		xml_parser_free($this->parser);

		// recover the root node from the node stack
		$rnode = array_pop($this->node_stack);

		// return the root node...
		return($rnode);
	}

	function startElement($parser, $name, $attrs)
	{
		// create a new node...
		$node = array();
		$node["_NAME"] = $name;
		foreach ($attrs as $key => $value) $node[$key] = $value;

		$node["_DATA"] = "";
		$node["_ELEMENTS"] = array();

		// add the new node to the end of the node stack
		array_push($this->node_stack, $node);
	}

	function endElement($parser, $name)
	{
		// pop this element off the node stack
		$node = array_pop($this->node_stack);
		$node["_DATA"] = trim($node["_DATA"]);

		// and add it an an element of the last node in the stack...
		$lastnode = count($this->node_stack);
		array_push($this->node_stack[$lastnode-1]["_ELEMENTS"], $node);
	}

	function characterData($parser, $data)
	{
		// add this data to the last node in the stack...
		$lastnode = count($this->node_stack);
		$this->node_stack[$lastnode-1]["_DATA"] .= $data;
	}
	*/

}
