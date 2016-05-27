<?
$globaldata = '';
global $output;
global $etape;
$etape = '';

global $modeleligne;
global $ligne;
global $xml_debug;
$xml_debug = 0;


// XML PARSER
function startElement_content($parser, $name, $attribs)  {
	global $globaldata;
	$db = dims::getInstance()->getDb();
	global $output;
	global $ligne;
	global $etape;
	global $params;

	$globaldata = '';
	$name = strtolower($name);

	switch($name) {
		case 'table:table':
			if (isset($attribs['TABLE:NAME']) && $attribs['TABLE:NAME'] == 'Tableau')  {
				$etape = 'table';
			}
		break;

		case 'table:table-row':
			if ($etape == 'table') // on est dans le bon tableau
			{
				$ligne = '';
			}
		break;
	}

	$params = '';
	foreach($attribs as $param => $value) {
		$params .= strtolower(" $param=\"")."$value\"";
	}

	if ($etape == 'table' || $etape == 'modele') $ligne .= "<{$name} {$params}>";

	$output .= "<{$name} {$params}>";

}

function endElement_content($parser, $name) {
	global $globaldata;
	global $output;
	global $etape;
	global $ligne;
	global $modeleligne;

	global $xmlmodel;
	global $params;
	global $xml_debug;

	$name = strtolower($name);

	if ($globaldata != '')
	{
		$output .= preg_replace("/\r\n|\n|\r/", "</{$name}><{$name} {$params}>", $xmlmodel->render($globaldata))."</{$name}>";
	}
	else $output .= "</{$name}>";

	if ($etape == 'table' || $etape == 'modele') $ligne .= "$globaldata</$name>";

	if ($globaldata == '(CODE)' && $etape == 'table') // ligne modele
	{
		$etape = 'modele';
	}

	if (strstr($name,'table:table-row') && $etape == 'modele')
	{
		$modeleligne = $ligne;
		$etape = '';
	}

	$globaldata = '';
}

function characterData_content($parser, $data)
{
	global $globaldata;
	$globaldata .= $data;
}

function xmlparser_content()
{
	$xml_parser = xml_parser_create();

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);
	xml_set_element_handler($xml_parser, "startElement_content", "endElement_content");
	xml_set_character_data_handler($xml_parser, "characterData_content");

	return $xml_parser;
}

function xmlparser_content_free($xml_parser)
{
	xml_parser_free($xml_parser);
	$globaldata = '';
}

?>