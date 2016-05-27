<?php
global $field_formats;
$f->add_select_field(array(
	'name' 		=> 'field_format',
	'label'		=> $_SESSION['cste']['_FIELD_FORMAT'],
	'db_field' 	=> 'format',
	'options'	=> $field_formats,
));