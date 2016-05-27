<?php
$view = view::getInstance();
$article = $view->get('article');

$form = new Dims\form(array(
		'name' 				=> 'form_articles',
		'object'			=> $article,
		'action'			=> get_path('articles', 'show', array('sc' => 'tarifs', 'sa' => 'update', 'id' => $article->get('id'))),
		'back_name'			=> dims_constant::getVal('REINITIALISER'),
		'back_url'			=> get_path('articles', 'show', array('sc' => 'tarifs', 'sa' => 'edit', 'id' => $article->get('id'))),
		'submit_value'		=> dims_constant::getVal('_DIMS_SAVE')
	));

$tarif_block = $form->addBlock('tarifs_stocks',dims_constant::getVal('PRICES_AND_STOCK'),$view->getTemplatePath('articles/article_prices_block.tpl.php'));
$tarif_block->setForm($form);

$form->add_text_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_putarif_0',
		'db_field'		=> 'putarif_0',
		'label'			=> ($view->get('cata_base_ttc')) ? dims_constant::getVal('PU_TTC') : dims_constant::getVal('PU_HT'),
		'revision'		=> 'number'
	));

$form->add_text_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_qte',
		'db_field'		=> 'qte',
		'label'			=> dims_constant::getVal('QTY_IN_STOCK'),
		'revision'		=> 'number'
	));


$form->add_select_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_ctva',
		'db_field'		=> 'ctva',
		'options'		=> $view->get('tvas'),
		'empty_message'	=> '',
		'label'			=> dims_constant::getVal('CODE_TVA'),
	));

$form->add_text_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_qte_mini',
		'db_field'		=> 'qte_mini',
		'label'			=> dims_constant::getVal('STOCK_MINI'),
		'revision'		=> 'number'
	));

$form->add_text_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_rempromo_1',
		'db_field'		=> 'rempromo_1',
		'label'			=> dims_constant::getVal('_DISCOUNT'),
		'revision'		=> 'number'
	));

$form->add_text_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_uvente',
		'db_field'		=> 'uvente',
		'label'			=> dims_constant::getVal('UNIT_OF_SALE'),
	));


$form->add_text_field(array(
	'block'			=> 'tarifs_stocks',
	'name'			=> 'article_txt_delai_livraison_stock',
	'db_field'		=> 'txt_delai_livraison_stock',
	'label'			=> dims_constant::getVal('INFO_LIVRAISON_SI_STOCK'),
));

$form->add_text_field(array(
	'block'			=> 'tarifs_stocks',
	'name'			=> 'article_txt_delai_livraison_rupture',
	'db_field'		=> 'txt_delai_livraison_rupture',
	'label'			=> dims_constant::getVal('INFO_LIVRAISON_SI_RUPTURE'),
));

$form->add_checkbox_field(array(
	'block'			=> 'tarifs_stocks',
	'name'			=> 'article_degressif',
	'id'			=> 'article_degressif',
	'db_field'		=> 'degressif',
	'label'			=> dims_constant::getVal('DEGRESSIVE_RATE'),
	'value'			=> 1,
));

$form->add_hidden_field(array(
	'block'			=> 'tarifs_stocks',
	'name'			=> 'degressifs'
	));
$form->add_hidden_field(array(
	'block'			=> 'tarifs_stocks',
	'name'			=> 'np_elements'
	));

$prixnets = $form->addBlock('prix_nets',dims_constant::getVal('NET_PRICES'),$view->getTemplatePath('articles/prix_nets_block.tpl.php'));
$prixnets->setForm($form);
$form->build();
?>
