
<?php $view = view::getInstance(); ?>

<?php
$article = $view->get('article');
$form = new Dims\form(array(
		'name' 				=> 'kit_article',
		'object'			=> $article,
		'action'			=> get_path('articles', 'show', array('sc' => 'kit', 'sa' => 'update', 'id' => $article->get('id'))),
		'back_name'			=> dims_constant::getVal('REINITIALISER'),
		'back_url'			=> get_path('articles', 'show', array('sc' => 'kit', 'sa' => 'edit', 'id' => $article->get('id'))),
		'submit_value'		=> dims_constant::getVal('_DIMS_SAVE')
	));

$kit_block = $form->addBlock ('composition_article',dims_constant::getVal('COMPOSITION_OF_THE_PRODUCT_(KIT)'),$view->getTemplatePath('articles/kit_block.tpl.php'));
$kit_block->setForm($form);
$form->add_hidden_field(array(
	'block' => 'composition_article',
	'name' => 'kit_composition'	));
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