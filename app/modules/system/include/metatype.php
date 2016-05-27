<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$metafield_types = array(	'text' => $_DIMS['cste']['_DIMS_LABEL_SIMPLE_TEXT'],
						'textarea' => $_DIMS['cste']['_DIMS_LABEL_ADVANCED_TEXT'],
						'checkbox' => $_DIMS['cste']['_DIMS_LABEL_CHECKBOX'],
						'radio' => $_DIMS['cste']['_DIMS_LABEL_RADIO_BUTTON'],
						'select' => $_DIMS['cste']['_DIMS_LABEL_LIST'],
						'tablelink' => $_DIMS['cste']['_DIMS_LABEL_FORM_LINK'],
						'file' => $_DIMS['cste']['_DIMS_LABEL_FILE'],
						'autoincrement' => $_DIMS['cste']['_DIMS_LABEL_AUTO_NUMBER'],
						'color' => $_DIMS['cste']['_DIMS_LABEL_COLOR']
					);

$metafield_formats = array(	'string' => $_DIMS['cste']['_DIMS_LABEL_STRING'],
						'integer' => $_DIMS['cste']['_DIMS_LABEL_INT_NUMBER'],
						'float' => $_DIMS['cste']['_DIMS_LABEL_FLOAT_NUMBER'],
						'date' => $_DIMS['cste']['_DIMS_DATE'],
						'time' => $_DIMS['cste']['_DIMS_LABEL_HOURS'],
						'email' => $_DIMS['cste']['_DIMS_LABEL_EMAIL'],
						'phone'	=> $_DIMS['cste']['_DIRECTORY_PHONE'],
						'url' => $_DIMS['cste']['_DIMS_LABEL_WEB_ADDRESS']
					);

$metafield_operators = array(	'=' => '=',
							'>' => '>',
							'<' => '<',
							'>=' => '>=',
							'<=' => '<=',
							'like' => $_DIMS['cste']['_DIMS_LABEL_CONTAIN'],
							'begin' => $_DIMS['cste']['_DIMS_LABEL_BEGIN_WITH']
						);

?>
