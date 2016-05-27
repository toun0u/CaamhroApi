<?php
$lstcata=dims::getInstance()->getModuleByType('catalogue');
if (!empty($lstcata)) {
	require_once(DIMS_APP_PATH . '/modules/catalogue/include/class_catalogue.php');
	$cata = new catalogue();
	$searchArttext=' onkeyup="javascript:searchArticleCatalogueKey();" ';
}
else {
	$searchArttext='';
}

$form = new Dims\form(array(
	'name' 			=> "imprimer",
	'method'		=> "POST",
	'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=suivi&action=save_detail",
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_CANCEL'],
	'back_url'		=> "javascript:void(0);\" onclick=\"javascript:$('#form_add_line').hide();$('#list_lines').show();$('#form_add_line').html('');",
	'object'		=> $this,
));
$form->add_hidden_field(array(
	'name'		=> 'id',
	'db_field'	=> 'suivi_id',
));
$form->add_hidden_field(array(
	'name'		=> 'suivi_type',
	'db_field'	=> 'suivi_type',
));
$form->add_hidden_field(array(
	'name'		=> 'idd',
	'db_field'	=> 'id',
));
$form->add_text_field(array(
	'name'						=> 'suivi_detail_code',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_GROUP_CODE'],
	'db_field'					=> "code",
	'additionnal_attributes'	=> '',
	'mandatory'					=> true, // TODO : gérer la recherche dans les code cata // <div id="dyncontentArticle" name="dyncontentArticle" style="height:80px;"></div>
));
$form->add_textarea_field(array(
	'name'						=> 'suivi_detail_libelle',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_LABEL'],
	'db_field'					=> "libelle",
	'additionnal_attributes'	=> '',
));
$form->add_text_field(array(
	'name'						=> 'suivi_detail_pu',
	'label' 					=> "Prix unitaire",
	'db_field'					=> "pu",
	'additionnal_attributes'	=> '',
	'revision'					=> 'number',
));
$form->add_text_field(array(
	'name'						=> 'suivi_detail_qte',
	'label' 					=> "Quantité",
	'db_field'					=> "qte",
	'additionnal_attributes'	=> '',
	'revision'					=> 'number',
));
if (!empty($lstcata)) {
	$lsttva=$cata->getTva();
	$lstTva = array();
	foreach ($lsttva as $tva) {
		$lstTva[$tva] = $tva;
	}
	$form->add_select_field(array(
		'name'						=> 'suivi_detail_tauxtva',
		'label' 					=> "Taux TVA",
		'db_field'					=> "tauxtva",
		'options'					=> $lstTva,
	));
}else{
	$form->add_text_field(array(
		'name'						=> 'suivi_detail_tauxtva',
		'label' 					=> "Taux TVA",
		'db_field'					=> "tauxtva",
		'additionnal_attributes'	=> '',
		'revision'					=> 'number',
	));
}
$form->add_textarea_field(array(
	'name'						=> 'suivi_detail_description',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION'],
	'db_field'					=> "description",
	'additionnal_attributes'	=> '',
));
$form->build();
