<?php
$view = view::getInstance();
$slide = $view->get('elem');

$back_path = get_path('objects', 'slide');
$title = "";
if(!$slide->isNew()){
    $back_path = get_path('objects', 'showslid', array('id'=>$slide->get('id')));
    $title = " > ".$slide->fields['nom'];
}

$form = new Dims\form(array(
    'name'              => 'f_slideshow',
    'action'            => get_path('objects', 'saveslid'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => $back_path,
    'validation'        => true
));
$form->addBlock('default', dims_constant::getVal('_SLIDESHOW').$title);
if(!$slide->isNew()){
    $form->add_hidden_field(array(
        'name'          => 'id',
        'value'         => $slide->get('id')
    ));
}

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_NAME'),
    'value'                     => $slide->fields['nom'],
    'name'                      => 'slide_nom',
    'mandatory'                 => true
));

$form->add_textarea_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
    'value'                     => $slide->fields['description'],
    'name'                      => 'slide_description'
));

$form->add_select_field(array(
    'label'                     => dims_constant::getVal('_TEMPLATE'),
    'value'                     => $slide->fields['template'],
    'name'                      => 'slide_template',
    'options'                   => $view->get('slide_tpl')
));

$form->build();
?>