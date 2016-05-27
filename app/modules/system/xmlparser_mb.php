<?php
require_once DIMS_APP_PATH . '/modules/system/class_mb_table.php';
require_once DIMS_APP_PATH . '/modules/system/class_mb_field.php';
require_once DIMS_APP_PATH . '/modules/system/class_mb_schema.php';
require_once DIMS_APP_PATH . '/modules/system/class_mb_relation.php';

$globaldata = '';
$datatype = '';
$newrow = false;
$newfield = false;

// XML PARSER
function startElement_mb($parser, $name, $attribs) {
	global $globaldata;
	global $datatype;
	global $field;
	$db = dims::getInstance()->getDb();
	global $newrow;
	global $newfield;
	global $dataobject;

	$globaldata = '';

	$name = strtolower($name);

	// new table
	if (strpos($name,'dims_') === 0) {
		$datatype = $name;
	}

	if ($newrow) $newfield = true; // new field (all element in a row is a field)

	// new row in table
	if($name == 'row' && !$newrow) {
		switch($datatype) {
			case 'dims_mb_table':
				$dataobject = new mb_table();
				break;

			case 'dims_mb_field':
				$dataobject = new mb_field();
				break;

			case 'dims_mb_schema':
				$dataobject = new mb_schema();
				break;

			case 'dims_mb_relation':
				$dataobject = new mb_relation();
				break;

			default:
				$dataobject = new dims_data_object($datatype);
				break;
		}
		$newrow = true;
	}
}

function endElement_mb($parser, $name) {
	global $globaldata;
	global $datatype;
	global $newrow;
	global $newfield;
	global $dataobject;
	global $idmoduletype;
	$db = dims::getInstance()->getDb();
	global $tabwceobject;
	global $tabfieldobject;
	//global $module_type;

	$name = strtolower($name);

	if ($newrow) {
		// end row
		if($name == 'row' && !$newfield) {
			$dataobject->fields['id_module_type'] = $idmoduletype;//module_type->fields['id'];
			if ($datatype=="dims_mb_wce_object") {

				$res=$db->query("SELECT *
						FROM dims_mb_wce_object
						WHERE label LIKE :label
						AND id_module_type = :idmoduletype",
					array(
						':label' => array('type' => PDO::PARAM_STR, 'value' => $dataobject->fields['label']),
						':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $idmoduletype),
				));

				$newdataobject=new dims_data_object($datatype);

				if ($db->numrows($res)>0) {
					if ($fields = $db->fetchrow($res)) $newdataobject->open($fields['id']);
				} else {
					$newdataobject->fields['id_module_type']=$idmoduletype;
					$newdataobject->fields['label']=$dataobject->fields['label'];
				}

				$newdataobject->fields['script']=$dataobject->fields['script'];
				if (isset($dataobject->fields['select_id'])) $newdataobject->fields['select_id']=$dataobject->fields['select_id'];
				if (isset($dataobject->fields['select_label'])) $newdataobject->fields['select_label']=$dataobject->fields['select_label'];
				if (isset($dataobject->fields['select_table'])) $newdataobject->fields['select_table']=$dataobject->fields['select_table'];
				if (isset($dataobject->fields['select_params'])) $newdataobject->fields['select_params']=$dataobject->fields['select_params'];
				$newdataobject->save();

				if (isset($tabwceobject) && isset($tabwceobject[$fields['id']])) unset($tabwceobject[$fields['id']]);

			} else if ($datatype=="dims_mb_field") {
				$res=$db->query("SELECT * FROM dims_mb_field
						WHERE tablename LIKE :tablename
						AND name LIKE :name
						AND id_module_type = :idmoduletype",
					array(
						':tablename' => array('type' => PDO::PARAM_STR, 'value' => $dataobject->fields['tablename']),
						':name' => array('type' => PDO::PARAM_STR, 'value' => $dataobject->fields['name']),
						':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $idmoduletype),
				));

				$newdataobject=new dims_data_object($datatype);

				if ($db->numrows($res)>0) {
					if ($fields = $db->fetchrow($res)) $newdataobject->open($fields['id']);
				} else {
					$newdataobject->fields['id_module_type']=$idmoduletype;
					$newdataobject->fields['tablename']=$dataobject->fields['tablename'];
					$newdataobject->fields['name']=$dataobject->fields['name'];
				}

				$newdataobject->fields['label']=$dataobject->fields['label'];
				$newdataobject->fields['type']=$dataobject->fields['type'];
				if (isset($dataobject->fields['visible'])) $newdataobject->fields['visible']=$dataobject->fields['visible'];
				if (isset($dataobject->fields['id_object'])) $newdataobject->fields['id_object']=$dataobject->fields['id_object'];
				if (isset($dataobject->fields['indexed'])) $newdataobject->fields['indexed']=$dataobject->fields['indexed'];
				$newdataobject->save();

				if (isset($tabfieldobject) && isset($tabfieldobject[$fields['id']])) unset($tabfieldobject[$fields['id']]);
			} else $dataobject->save();

			$newrow = false;
		} else { // end field
			$dataobject->fields[$name] = $globaldata;
			$newfield = false;
		}
	}

	$globaldata = '';
}

function characterData_mb($parser, $data) {
	global $globaldata;
	$globaldata .= $data;
}

function xmlparser_mb($file) {
	$xml_parser = xml_parser_create();

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);
	xml_set_element_handler($xml_parser, "startElement_mb", "endElement_mb");
	xml_set_character_data_handler($xml_parser, "characterData_mb");

	if (!($fp = @fopen($file, "r"))) {
		return FALSE;
	}

	return array($xml_parser, $fp);
}

function xmlparser_mb_free($xml_parser) {
	xml_parser_free($xml_parser);
	$globaldata = '';
	$datatype = '';
	$newrow = false;
	$newfield = false;
}

?>
