<?php
$view = view::getInstance();
$article = $view->get('article');
$link = $view->get('link');

$ad_js = "
$('#search_article').focus(function(){
	if($(this).val() == '".dims_constant::getVal('SEARCH')."'){

		$(this).val('');
		$(this).removeClass('temp_message');
	}
})
.focusout(function(){
	if($(this).val() == ''){
			$(this).val('".dims_constant::getVal('SEARCH')."');
			$(this).addClass('temp_message');
		}
})
.dims_autocomplete( { c : 'articles', a : 'ac_articles' }, 3, 500, '#link_id_article_to', '#ac_references', '#ul_ac_references', '<li>\${label}</li>', '".dims_constant::getVal('NO_ARTICLE')."', null );
";

$form = new Dims\form(array(
	'name' 				=> 'form_articles',
	'object'			=> $link,
	'action'			=> get_path('articles', 'show', array('sc' => 'links', 'sa' => 'save', 'id' => $article->get('id'))),
	'back_url'			=> get_path('articles', 'show', array('sc' => 'links', 'sa' => 'index', 'id' => $article->get('id'))),
	'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
	'additional_js'		=> $ad_js,
	'continue'			=> true
	));

$default = $form->getBlock('default');
$default->setTitle(dims_constant::getVal('NEW_LINK'));

/*
if($link->exists() ){ #Pour l'enregistrement, sinon il faudrait ajouter une colonne globalobject inutile dans ce type d'objet simple
	$form->add_hidden_field(array(
	'name'		=> 'id_link',
	'db_field'	=> 'id'
	));
}*/
$form->add_hidden_field(array(
	'name'		=> 'link_id_article_to',
	'db_field'	=> 'id_article_to'
	));

$form->add_text_field(array(
	'name'						=> 'search_article',
	'label'						=> dims_constant::getVal('_LINK_TO_ARTICLE'),
	'value'						=> ( $view->has('linked_to') ) ? $view->get('linked_to')->fields['reference'] .' - '.$view->get('linked_to')->fields['label'] : dims_constant::getVal('SEARCH'),
	'classes'					=> 'w300p ' . ( ( $view->has('linked_to') ) ? '' : 'temp_message' ),
	'additionnal_attributes'	=> 'autocomplete="off"',
	'dom_extension'				=> '<div id="ac_references" class="ac_container" style="display:none;">
										<ul id="ul_ac_references">
										</ul>
									</div>'
	));

$form->add_select_field(array(
	'name'		=> 'link_type',
	'label'		=> dims_constant::getVal('_DIMS_LABEL_LINK_TYPE'),
	'options'	=> $view->get('types'),
	'db_field'	=> 'type'
	));

$form->add_checkbox_field(array(
	'name'		=> 'link_symetric',
	'label'		=> dims_constant::getVal('SYMETRIC_LINK'),
	'value'		=> 1,
	'db_field'	=> 'symetric'
	));

$form->build();
?>
