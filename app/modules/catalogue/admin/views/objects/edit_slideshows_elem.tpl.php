<?php
$view = view::getInstance();
$slide = $view->get('elem');
$elem = $view->get('elems');

$additional_js = <<< ADDITIONAL_JS
CKEDITOR.replace('fck_elem_descr_longue',
    {
        customConfig : '/assets/javascripts/libs/ckeditor/ckeditor_config_simple_fr.js',
        stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
        contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
    });
ADDITIONAL_JS;

$form = new Dims\form(array(
    'name'              => 'f_slideshow_elem',
    'action'            => get_path('objects', 'showslid', array('id'=>$slide->get('id'), 'sa'=>'saveelemslid')),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('objects', 'showslid', array('id'=>$slide->get('id'))),
    'validation'        => true,
    'enctype'           => true,
    'additional_js'     => $additional_js
));
$form->addBlock('default', dims_constant::getVal('_SLIDESHOW')." > ".$slide->fields['nom']);
if(!$elem->isNew()){
    $form->add_hidden_field(array(
        'name'          => 'sid',
        'value'         => $elem->get('id')
    ));
}

$form->add_hidden_field(array(
    'name'          => 'elem_id_slideshow',
    'value'         => $elem->fields['id_slideshow']
));

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_TITLE'),
    'value'                     => $elem->fields['titre'],
    'name'                      => 'elem_titre',
    'mandatory'                 => true
));

$form->add_textarea_field(array(
    'label'                     => dims_constant::getVal('DESCRIPTION_COURTE'),
    'value'                     => $elem->fields['descr_courte'],
    'name'                      => 'elem_descr_courte'
));

$form->add_textarea_field(array(
    'label'                     => dims_constant::getVal('_LONG_DESCRIPTION'),
    'value'                     => $elem->fields['descr_longue'],
    'name'                      => 'fck_elem_descr_longue',
    'id'                        => 'fck_elem_descr_longue'
));

$form->add_select_field(array(
    'label'                     => dims_constant::getVal('_POSITION_DESCRIPTION'),
    'value'                     => $elem->fields['descr_position'],
    'name'                      => 'elem_descr_position',
    'options'                   => $view->get('a_descr_positions')
));

$image = "";
if(!$elem->isNew() && $elem->fields['image'] != '' && $elem->fields['image'] > 0){
    $img = new docfile();
    $img->open($elem->fields['image']);
    $image = '<img src="'.$img->getThumbnail(25).'" />';
}
$form->add_file_field(array(
    'label'                     => dims_constant::getVal('PICTURE_SINGULIER')." (755x253)",
    'name'                      => 'elem_image',
    'dom_extension'             => $image
));

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_URL'),
    'value'                     => ($elem->fields['lien']=="")?"http://":$elem->fields['lien'],
    'name'                      => 'elem_lien'
));

$miniature = "";
if(!$elem->isNew() && $elem->fields['miniature'] != '' && $elem->fields['miniature'] > 0){
    $img = new docfile();
    $img->open($elem->fields['miniature']);
    $miniature = '<img src="'.$img->getThumbnail(25).'" />';
}
$form->add_file_field(array(
    'label'                     => dims_constant::getVal('_MINIATURE')." (48x48)",
    'name'                      => 'elem_miniature',
    'dom_extension'             => $miniature
));
$form->add_checkbox_field(array(
    'label'                     => dims_constant::getVal('_VISIBLE_ONLY_AUTH_USERS'),
    'checked'                   => $elem->fields['connected_only'],
    'value'                     => 1,
    'name'                      => 'elem_connected_only'
));


$form->build();

?>