<?php
$view = view::getInstance();
$slidart = $view->get('elem');

$back_path = get_path('objects', 'slidart');
$title = "";
if(!$slidart->isNew()){
    $back_path = get_path('objects', 'showart', array('id'=>$slidart->get('id')));
    $title = " > ".$slidart->fields['nom'];
}

$form = new Dims\form(array(
    'name'              => 'f_slidart',
    'action'            => get_path('objects', 'saveart'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => $back_path,
    'validation'        => true
));
$form->addBlock('default', dims_constant::getVal('_SLIDESHOW_ARTICLES').$title);
if(!$slidart->isNew()){
    $form->add_hidden_field(array(
        'name'          => 'id',
        'value'         => $slidart->get('id')
    ));
}

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_NAME'),
    'value'                     => $slidart->fields['nom'],
    'name'                      => 'slidart_nom',
    'mandatory'                 => true
));

$form->add_textarea_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
    'value'                     => $slidart->fields['description'],
    'name'                      => 'slidart_description'
));

$form->build();
?>