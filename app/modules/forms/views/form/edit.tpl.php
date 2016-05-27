<?php
$view = view::getInstance();
$form = $view->get('form');
?>
<h1><?= $_SESSION['cste']['_DIMS_LABEL_FORM'].((!$form->isNew())?" : ".$form->get('label'):""); ?></h1>
<?php
$f = new Dims\form(array(
	'name' 			=> 'form_edit',
	'object'		=> $form,
	'action'		=> form\get_path(array('c'=>'form','a'=>($form->isNew()?"create":"update"))),
	'submit_value' 	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_url'		=> form\get_path(array('c'=>'index')),
));
if(!$form->isNew()){
	$f->add_hidden_field(array(
		'name' 		=> 'id',
		'db_field' 	=> 'id',
		'mandatory'	=> true
	));
}
$f->add_text_field(array(
	'name' 		=> 'form_label',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_LABEL'],
	'db_field' 	=> 'label',
	'mandatory'	=> true
));
$f->add_text_field(array(
	'name' 		=> 'form_tablename',
	'label'		=> $_SESSION['cste']['_FORMS_TABLENAME'],
	'db_field' 	=> 'tablename',
));
$f->add_text_field(array(
	'name' 						=> 'form_pubdate_start',
	'label'						=> $_SESSION['cste']['_FORMS_PUBDATESTART'],
	'db_field' 					=> 'pubdate_start',
	'additionnal_attributes'	=> 'style="width:75px;"',
	'classes'					=> 'datepicker',
));
$f->add_text_field(array(
	'name' 						=> 'form_pubdate_end',
	'label'						=> $_SESSION['cste']['_FORMS_PUBDATEEND'],
	'db_field' 					=> 'pubdate_end',
	'additionnal_attributes'	=> 'style="width:75px;"',
	'classes'					=> 'datepicker',
));
$f->add_text_field(array(
	'name' 		=> 'form_sender',
	'label'		=> $_SESSION['cste']['_SENDER'],
	'db_field' 	=> 'sender',
	'revision'	=> 'email',
));
$f->add_text_field(array(
	'name' 		=> 'form_email',
	'label'		=> $_SESSION['cste']['_FORMS_HELP_EMAIL'],
	'db_field' 	=> 'email',
));
/*$f->add_text_field(array(
	'name' 		=> 'form_width',
	'label'		=> $_SESSION['cste']['_FORMS_WIDTH'],
	'db_field' 	=> 'width',
));*/
$f->add_textarea_field(array(
	'name' 		=> 'form_description',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION'],
	'db_field' 	=> 'description',
));
if ($form->get('typeform') == 'cms'){
	$f->add_textarea_field(array(
		'name' 		=> 'form_cms_response',
		'label'		=> $_SESSION['cste']['_FORMS_RESPONSE'],
		'db_field' 	=> 'cms_response',
	));
	$f->add_checkbox_field(array(
		'name' 		=> 'form_cms_link',
		'label'		=> "Lien vers Fiche D&eacute;taill&eacute;e (CMS)",
		'db_field' 	=> 'cms_link',
		'value'		=> 1,
	));
}
global $form_types;
$f->add_select_field(array(
	'name' 		=> 'form_typeform',
	'label'		=> $_SESSION['cste']['_FORMS_TYPEFORM'],
	'db_field' 	=> 'typeform',
	'options'	=> $form_types,
));

$f->add_text_field(array(
	'name' 		=> 'form_nb_col',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_NBCOLUMNS'],
	'db_field' 	=> 'nb_col',
	'revision'	=> 'number',
));

/*global $form_modeles;
$f->add_select_field(array(
	'name' 		=> 'form_model',
	'label'		=> $_SESSION['cste']['_FORMS_MODEL'],
	'db_field' 	=> 'model',
	'options'	=> $form_modeles,
));
$f->add_text_field(array(
	'name' 		=> 'form_nbline',
	'label'		=> $_SESSION['cste']['_FORMS_NBLINE'],
	'db_field' 	=> 'nbline',
));*/
if ($form->get('typeform') == 'app'){
	$f->add_checkbox_field(array(
		'name' 		=> 'form_option_onlyone',
		'id' 		=> 'form_option_onlyone',
		'label'		=> $_SESSION['cste']['_FORMS_OPTION_ONLYONE'],
		'db_field' 	=> 'option_onlyone',
		'value'		=> 1,
	));
	$f->add_checkbox_field(array(
		'name' 		=> 'form_option_onlyoneday',
		'id' 		=> 'form_option_onlyoneday',
		'label'		=> $_SESSION['cste']['_FORMS_OPTION_ONLYONEDAY'],
		'db_field' 	=> 'option_onlyoneday',
		'value'		=> 1,
	));
	$f->add_checkbox_field(array(
		'name' 		=> 'form_option_displayuser',
		'id' 		=> 'form_option_displayuser',
		'label'		=> $_SESSION['cste']['_FORMS_OPTION_DISPLAY_USER'],
		'db_field' 	=> 'option_displayuser',
		'value'		=> 1,
	));
	$f->add_checkbox_field(array(
		'name' 		=> 'form_option_displaygroup',
		'id' 		=> 'form_option_displaygroup',
		'label'		=> $_SESSION['cste']['_FORMS_OPTION_DISPLAY_GROUP'],
		'db_field' 	=> 'option_displaygroup',
		'value'		=> 1,
	));
}
$f->add_checkbox_field(array(
	'name' 		=> 'form_option_displaydate',
	'id' 		=> 'form_option_displaydate',
	'label'		=> $_SESSION['cste']['_FORMS_OPTION_DISPLAY_DATE'],
	'db_field' 	=> 'option_displaydate',
	'value'		=> 1,
));
$f->add_checkbox_field(array(
	'name' 		=> 'form_option_displayip',
	'id' 		=> 'form_option_displayip',
	'label'		=> $_SESSION['cste']['_FORMS_OPTION_DISPLAY_IP'],
	'db_field' 	=> 'option_displayip',
	'value'		=> 1,
));
$f->add_select_field(array(
	'name' 		=> 'form_option_modify',
	'label'		=> $_SESSION['cste']['_FORMS_OPTION_MODIFY'],
	'db_field' 	=> 'option_modify',
	'options'	=> array(
						'nobody'=>$_SESSION['cste']['_OPTION_MODIFY_NOBODY'],
						'user'=>$_SESSION['cste']['_FORMS_OPTION_MODIFY_USER'],
						'group'=>$_SESSION['cste']['_FORMS_OPTION_MODIFY_GROUP'],
						'all'=>$_SESSION['cste']['_FORMS_OPTION_MODIFY_ALL'],
					),
));
$f->add_select_field(array(
	'name' 		=> 'form_option_view',
	'label'		=> $_SESSION['cste']['_FORMS_OPTION_VIEW'],
	'db_field' 	=> 'option_view',
	'options'	=> array(
						//'private'=>$_SESSION['cste']['_VIEW_PRIVATE'],
						'global'=>$_SESSION['cste']['_LABEL_VIEWMODE_GLOBAL'],
						'asc'=>$_SESSION['cste']['_LABEL_VIEWMODE_ASC'],
						'desc'=>$_SESSION['cste']['_LABEL_VIEWMODE_DESC'],
					),
));
$f->add_text_field(array(
	'name' 		=> 'form_autobackup',
	'label'		=> $_SESSION['cste']['_FORMS_AUTOBACKUP'],
	'db_field' 	=> 'autobackup',
));
$f->add_text_field(array(
	'name' 		=> 'form_accesscode',
	'label'		=> $_SESSION['cste']['ACCESS_CODE'],
	'db_field' 	=> 'accesscode',
));
$f->build();
?>
<script type="text/javascript">
	$('document').ready(function(){
		$(".datepicker").datepicker({
			buttonImage: '/common/img/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
		});
	}
   )
</script>
