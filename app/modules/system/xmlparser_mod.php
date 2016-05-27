<?
$globaldata = '';
$datatype = '';
$newrow = false;
$newfield = false;

// XML PARSER
function startElement_mod($parser, $name, $attribs)
{
	global $globaldata;
	global $datatype;
	global $field;
	$db = dims::getInstance()->getDb();
	global $newrow;
	global $newfield;
	global $dataobject;

	$globaldata = '';

	$name = strtolower($name);

	if (strpos($name,'dims_') === 0) // new table
	{
		$datatype = $name;
	}

	if ($newrow) $newfield = true; // new field (all element in a row is a field)

	if($name == 'row' && !$newrow) // new row in table
	{
		$dataobject = new dims_data_object($datatype);
		$newrow = true;
	}

}

function endElement_mod($parser, $name)
{
	global $globaldata;
	global $datatype;
	global $newrow;
	global $newfield;
	global $dataobject;

	$name = strtolower($name);

	if ($newrow)
	{
		if($name == 'row' && !$newfield) // end row
		{
			$dataobject->save();
			$newrow = false;
		}
		else // end field
		{
			$dataobject->fields[$name] = $globaldata;
			$newfield = false;
		}
	}

	$globaldata = '';
}

function characterData_mod($parser, $data)
{
	global $globaldata;
	$globaldata .= $data;
}

function xmlparser_mod($file)
{
	$xml_parser = xml_parser_create();

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);
	xml_set_element_handler($xml_parser, "startElement_mod", "endElement_mod");
	xml_set_character_data_handler($xml_parser, "characterData_mod");

	if (!($fp = @fopen($file, "r")))
	{
		return FALSE;
	}

	return array($xml_parser, $fp);
}

function xmlparser_mod_free($xml_parser)
{
	xml_parser_free($xml_parser);
	$globaldata = '';
	$datatype = '';
	$newrow = false;
	$newfield = false;
}

?>