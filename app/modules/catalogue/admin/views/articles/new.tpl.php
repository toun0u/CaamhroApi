<?php
$view = view::getInstance();
$article = $view->get('article');
?>
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/articles30x20.png'); ?>">
		<?= dims_constant::getVal('_LIST_OF_ARTICLES'); ?>
	</h1>

<?=show_guide( dims_constant::getVal('NEW_ARTICLE_GUIDAGE').'.'); ?>


<?php
$additional_js = <<< ADDITIONAL_JS
$(document).ready(function(){
    var instance=CKEDITOR.replace('article_description',
        {
            customConfig : '/assets/javascripts/libs/ckeditor/ckeditor_config_simple_fr.js',
            stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
            contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
        });
});
$("select#families").chosen({allow_single_deselect:true});
ADDITIONAL_JS;

$form = new Dims\form(array(
		'name'				=> 'form_articles',
		'object'			=> $article,
		'action'			=> dims::getInstance()->getScriptEnv() . '?c=articles&a=save',
		'back_name'			=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'			=> dims::getInstance()->getScriptEnv() . '?c=articles&a=index',
		'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
		'continue'			=> true,
		'enctype'			=> true,
		'additional_js'		=> $additional_js
	));

$form->addBlock ('main_info',dims_constant::getVal('MAIN_INFORMATION'));

$families = $view->get('familles');

$form->add_select_field(array(
	'block'						=> 'main_info',
	'name'						=> 'families[]',
	'id'						=> 'families',
	'label'						=> dims_constant::getVal('_FAMILY'),
	'options'					=> $families,
	'value' 					=> $view->get('referrer_family'),
	'classes'					=> 'family_select',
	'additionnal_attributes'	=> 'multiple'
));

$form->add_checkbox_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_published',
	'id'			=> 'article_published',
	'db_field'		=> 'published',
	'label'			=> dims_constant::getVal('_DIMS_LABEL_ACTIVE'),
	'value'			=> 1,

));
$form->add_text_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_reference',
	'db_field'		=> 'reference',
	'label'			=> dims_constant::getVal('_WCE_ARTICLE_REFERENCE'),
	'mandatory'		=> true,
));

$form->add_text_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_label',
	'label'			=> dims_constant::getVal('_DESIGNATION'),
	'mandatory'		=> true,
));

$form->add_textarea_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_description',
	'label'			=> dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
));

$form->add_text_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_poids',
	'db_field'		=> 'poids',
	'label'			=> dims_constant::getVal('POIDS_KG'),
	'classes'		=> 'w50p',
	'revision'		=> 'number'
));

$tarif_block = $form->addBlock('tarifs_stocks',dims_constant::getVal('PRICES_AND_STOCK'),$view->getTemplatePath('articles/article_prices_block.tpl.php'));
$tarif_block->setForm($form);

$form->add_text_field(array(
		'block'			=> 'tarifs_stocks',
		'name'			=> 'article_putarif_0',
		'db_field'		=> 'putarif_0',
		'label'			=> dims_constant::getVal('PU_HT'),
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


$form->add_checkbox_field(array(
	'block'			=> 'tarifs_stocks',
	'name'			=> 'article_degressif',
	'id'			=> 'article_degressif',
	'db_field'		=> 'degressif',
	'label'			=> dims_constant::getVal('DEGRESSIVE_RATE'),
	'value'			=> 1,
));



$form->addBlock ('vignette_photos',dims_constant::getVal('PHOTO_THUMBNAILS'),$view->getTemplatePath('articles/vignettes_block.tpl.php'));

// Ajouter un fichier

$form->add_file_field(array(
	'name'		=> 'vignette',
	'label'		=> dims_constant::getVal('FIRST_PICTURE'),
	'block'		=> 'vignette_photos',
	'revision'	=> 'ext:jpg,jpeg,gif,png',
	));

$kit_block = $form->addBlock ('composition_article',dims_constant::getVal('COMPOSITION_OF_THE_PRODUCT_(KIT)'),$view->getTemplatePath('articles/kit_block.tpl.php'));
$kit_block->setForm($form);

$form->add_radio_field(array(
	'block'		=> 'composition_article',
	'id'		=> 'kit_1',
	'name'		=> 'article_kit',
	'value'		=> 1,
	'db_field'	=> 'kit',
	'label'		=> 'On'
	));
$form->add_radio_field(array(
	'block'		=> 'composition_article',
	'id'		=> 'kit_0',
	'name'		=> 'article_kit',
	'value'		=> 0,
	'db_field'	=> 'kit',
	'label'		=> 'Off',
	'checked'	=> ( ! $article->isKit() ) ? true : false
	));
$form->build();


?>
