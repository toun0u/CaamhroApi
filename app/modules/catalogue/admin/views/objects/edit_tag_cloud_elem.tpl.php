<?php
$view = view::getInstance();
$tag = $view->get('elem');
$elem = $view->get('elems');

$additional_js = <<< ADDITIONAL_JS
$.fn.changeBgColor = function(){
    $('#elem_couleur').each(function(){
        $(this).next().css("background-color", $(this).val())
                .attr('id', 'ico_'+$(this).attr('id'));
    });
};
$('#elem_couleur')
.ColorPicker({
    onBeforeShow: function () {
        $(this).ColorPickerSetColor(this.value);
        $(this).next().css("background-color",this.value);
    },
    onChange: function(hsb, hex, rgb){
        $(this).next().css("background-color",hex);
    },
    onSubmit: function(hsb, hex, rgb, el){
        $(el).next().css("background-color","#"+hex);
        $(el).val("#"+hex);
        $(el).ColorPickerHide();
    }
})
.bind('keyup', function(){
    $(this).ColorPickerSetColor(this.value);
})
.css({'width':'50px', "float": 'left'})
.after('<div></div>')
.next()
    .css({"width":"20px", "height":"20px", "margin-left": "5px", "float":"left", "cursor":"pointer", "background": "url(/common/js/colorpicker/images/select2.png) no-repeat", "background-position":"-2px -2px", "background-size": "24px 24px"})
    .click(function(){
        $(this).prev().focus().click();
    })
    .changeBgColor();
ADDITIONAL_JS;

$form = new Dims\form(array(
    'name'              => 'f_cloud_tag_elem',
    'action'            => get_path('objects', 'showtag', array('id'=>$tag->get('id'), 'sa'=>'saveelemtag')),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('objects', 'showtag', array('id'=>$tag->get('id'))),
    'validation'        => true,
    'additional_js'     => $additional_js
));
$form->addBlock('default', dims_constant::getVal('_SLIDESHOW')." > ".$tag->fields['nom']);
if(!$elem->isNew()){
    $form->add_hidden_field(array(
        'name'          => 'sid',
        'value'         => $elem->get('id')
    ));
}

$form->add_hidden_field(array(
    'name'          => 'elem_id_cloud',
    'value'         => $elem->fields['id_cloud']
));

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_TITLE'),
    'value'                     => $elem->fields['titre'],
    'name'                      => 'elem_titre',
    'mandatory'                 => true
));

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_URL'),
    'value'                     => $elem->fields['lien'],
    'name'                      => 'elem_lien'
));

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_IMPORTANCE')." (&#37; 1-200)",
    'value'                     => ($elem->isNew())?1:$elem->fields['niveau'],
    'name'                      => 'elem_niveau',
    'classes'                   => 'w80p'
));

$form->add_text_field(array(
    'label'                     => dims_constant::getVal('_DIMS_LABEL_COLOR'),
    'value'                     => $elem->fields['couleur'],
    'name'                      => 'elem_couleur',
    'classes'                   => 'w80p'
));

$form->build();

?>