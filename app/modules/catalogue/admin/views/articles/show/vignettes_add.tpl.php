<?php
$view = view::getInstance();
$elem = $view->get('article');
$form = new Dims\form(array(
    'name'              => 'new_vign',
    'action'            => $view->get('action_path'),
    'validation'        => false,
    'back_name'         => dims_constant::getVal('_DIMS_CANCEL'),
    'back_url'          => $view->get('back_path'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'enctype'           => true
));
$form->addBlock('default',dims_constant::getVal('_NEW_THUMBNAIL'));

// Position
$positions = array();
for($i=1;$i<=$view->get('nb_thumbnails')+1;$i++)
    $positions[$i] = $i;
$form->add_select_field(array(
    'name'          => 'vign_position',
    'id'            => 'vign_position',
    'label'         => dims_constant::getVal('_POSITION'),
    'options'       => $positions,
    'value'         => $view->get('nb_thumbnails')+1,
    'block'         => 'default'
));
// Importer une photo
$form->add_file_field(array(
    'name'          => 'file',
    'id'            => 'file',
    'label'         => dims_constant::getVal('_DIMS_LABEL_PHOTO'),
    'block'         => 'default'
));
$form->build();
?>