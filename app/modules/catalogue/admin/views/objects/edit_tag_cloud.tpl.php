<?php
$view = view::getInstance();
$tag = $view->get('elem');

$back_path = get_path('objects', 'tags');
$title = "";
if(!$tag->isNew()){
    $back_path = get_path('objects', 'showtag', array('id'=>$tag->get('id')));
    $title = " > ".$tag->fields['nom'];
}

$form = new Dims\form(array(
    'name'              => 'f_tag',
    'action'            => get_path('objects', 'savetag'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => $back_path,
    'validation'        => true
));
$form->addBlock('default', dims_constant::getVal('_TAG_CLOUD').$title);
if(!$tag->isNew()){
    $form->add_hidden_field(array(
        'name'          => 'id',
        'value'         => $tag->get('id')
    ));
}

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_NAME'),
    'value'                     => $tag->fields['nom'],
    'name'                      => 'tag_nom',
    'mandatory'                 => true
));

$form->add_textarea_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
    'value'                     => $tag->fields['description'],
    'name'                      => 'tag_description'
));

$form->add_radio_field(array(
    'label'                     => dims_constant::getVal('_ALEATORY'),
    'value'                     => cloud::_MODE_ALEATOIRE,
    'checked'                   => (cloud::_MODE_ALEATOIRE == $tag->fields['mode'] || $tag->isNew()),
    'name'                      => 'tag_mode'
));
$form->add_radio_field(array(
    'label'                     => dims_constant::getVal('_BY_IMPORTANCE'),
    'value'                     => cloud::_MODE_IMPORTANCE,
    'checked'                   => (cloud::_MODE_IMPORTANCE == $tag->fields['mode']),
    'name'                      => 'tag_mode'
));

$form->build();
?>