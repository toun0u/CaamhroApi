<?php

include_once DIMS_APP_PATH . 'modules/forms/include/global.namespace.php';

if( ! function_exists('include_dir')){
	function include_dir($path){
		if(!empty($path) && file_exists($path) && is_dir($path)){
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					if(pathinfo($entry, PATHINFO_EXTENSION) == 'php'){//en chopan le file info on a juste text/plain
						include_once $path.'/'.$entry;
					}
				}
			}
		}
	}
}
include_dir(DIMS_APP_PATH.'modules/forms/models');

define ("_FORMS_TAB_LIST", 0);
define ("_FORMS_TAB_ADD", 1);

define ("_FORMS_ACTION_CREATEFORM", 1);
define ("_FORMS_ACTION_ADDREPLY", 	2);
define ("_FORMS_ACTION_EXPORT", 	3);
define ("_FORMS_ACTION_FILTER", 	4);


define('_FORMS_OBJECT_FORM',		1);
define('_FORMS_OBJECT_REPLY',		2);

global $field_types;
global $field_formats;
global $field_operators;
global $form_types;
global $form_modeles;
global $_DIMS;
$field_types = array(	'text' => $_DIMS['cste']['_DIMS_LABEL_SIMPLE_TEXT'],
						'textarea' => $_DIMS['cste']['_DIMS_LABEL_ADVANCED_TEXT'],
						'checkbox' => $_DIMS['cste']['_DIMS_LABEL_CHECKBOX'],
						'radio' => $_DIMS['cste']['_DIMS_LABEL_RADIO_BUTTON'],
						'select' => $_DIMS['cste']['_DIMS_LABEL_LIST'],
						//'tablelink' => $_DIMS['cste']['_DIMS_LABEL_FORM_LINK'],
						'file' => $_DIMS['cste']['_DIMS_LABEL_FILE'],
						//'autoincrement' => $_DIMS['cste']['_DIMS_LABEL_AUTO_NUMBER'],
						//'color' => $_DIMS['cste']['_DIMS_LABEL_COLOR']
					);

$field_formats = array(	'string' => $_DIMS['cste']['_DIMS_LABEL_STRING'],
						'integer' => $_DIMS['cste']['_DIMS_LABEL_INT_NUMBER'],
						'float' => $_DIMS['cste']['_DIMS_LABEL_FLOAT_NUMBER'],
						'date' => $_DIMS['cste']['_DIMS_DATE'],
						'time' => $_DIMS['cste']['_DIMS_LABEL_HOURS'],
						'email' => $_DIMS['cste']['_DIMS_LABEL_EMAIL'],
						'url' => $_DIMS['cste']['_DIMS_LABEL_WEB_ADDRESS'],
						'color' => $_DIMS['cste']['_DIMS_LABEL_COLOR'],
					);

$field_operators = array(	'=' => '=',
							'>' => '>',
							'<' => '<',
							'>=' => '>=',
							'<=' => '<=',
							'like' => $_DIMS['cste']['_DIMS_LABEL_CONTAIN'],
							'begin' => $_DIMS['cste']['_DIMS_LABEL_BEGIN_WITH']
						);

$form_types = array(	'cms' => 'Formulaire pour Gestion de Contenu',
						'app' => 'Application DIMS',
						'enq' => 'Enqu&ecirc;te',
						'son' => 'Sondage'
					);

// TODO : Récupérer les tpl rééllement existant
$form_modeles = array(	'forms_application.tpl'     => 'Application',
						'enquete.tpl'               => 'Enquete',
						'sondage.tpl'               => 'Sondage Express',
						'forms_application2c.tpl'   => 'Application 2 col.',
						'forms_application3c.tpl'   => 'Application 3 col.',
						'forms_application4c.tpl'   => 'Application 4 col.'
					);

function forms_convertchars($content)
{
	$chars = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
					"Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
					"Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
					"Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
					"Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
					"Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
					"Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
					"Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
					"à" => "a", "á" => "a", "â" => "a", "ã" => "a",
					"ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
					"è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
					"ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
					"ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
					"ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
					"ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
					"ý" => "y", "ÿ" => "y", "_" => " ", "-" => " ", "/" => " ");

	return(strtr($content, $chars));
}


function forms_createphysicalname($name)
{
	$chars = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
					"Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
					"Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
					"Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
					"Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
					"Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
					"Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
					"Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
					"à" => "a", "á" => "a", "â" => "a", "ã" => "a",
					"ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
					"è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
					"ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
					"ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
					"ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
					"ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
					"ý" => "y", "ÿ" => "y", " " => "_", "-" => "_");

	$name = preg_replace("([^[:alnum:]|_]+)", "", strtr(strtolower(trim($name)), $chars));
	if (strlen($name) && is_numeric($name{0})) $name  = "_$name";

	return(substr($name,0,32));
}


function forms_viewworkspaces($moduleid, $workspaceid, $viewmode) {
	$workspaces="";
	switch($viewmode) {
		default:
		case 'private':
			$workspaces = $workspaceid;
		break;

		case 'desc':
			$work = new workspace();
			$work->open($workspaceid);
			$lst=$work->getparents();
			foreach($lst as $i => $w) {
				if ($workspaces!='') $workspaces.=',';
				$workspaces .= $w['id'];
			}
			if ($workspaces!='') $workspaces.=',';
			$workspaces .= $workspaceid;
		break;

		case 'asc':
			$work = new workspace();
			$work->open($workspaceid);
			$lst=$work->getworkspacechildren();
			foreach($lst as $i => $w) {
				if ($workspaces!='') $workspaces.=',';
				$workspaces .= $w['id'];
			}
			if ($workspaces!='') $workspaces.=',';
			$workspaces .= $workspaceid;
		break;

		case 'global':
			$workspaces = dims_getAllWorkspaces();
		break;
	}

	return $workspaces;
}
