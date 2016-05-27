<?php
$view = view::getInstance();
$form = $view->get('form');
?>
<h1><?= $_SESSION['cste']['_LABEL_IMPORT_MISSION_STEP1']." (".$form->get('label').")"; ?></h1>
<?php
$f = new Dims\form(array(
	'name' 			=> 'form_import_1',
	'object'		=> $form,
	'action'		=> form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>1)),
	'submit_value' 	=> $_SESSION['cste']['_SYSTEM_LABELTAB_USERIMPORT'],
	'back_url'		=> form\get_path(array('c'=>'index')),
	'enctype'		=> true,
));
$f->add_file_field(array(
	'name' 		=> 'add_file_field',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_FILE'],
	'mandatory' => true,
));
$f->build();
