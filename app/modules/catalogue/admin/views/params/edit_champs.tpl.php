<div class="global_message error" style="display:none;"></div>
<?php
$dims = dims::getInstance();
$view = view::getInstance();
$elem = $view->get('champ');

$languages = "";
foreach($view->get('languages') as $id => $lg)
    $languages .= '<option value="'.$id.'">'.$lg.'</option>';
$infoLg = dims_constant::getVal('_CHOOSE_LANGUAGE_TRANSLATE_FILTER');
$noResult = dims_constant::getVal('NO_RESULT');

$additional_js = <<< ADDITIONAL_JS
var isFirst = true;
$(".champ_libelle").each(function(){
    if(!isFirst){
        $(this).parents("tr").hide();
        $("#def_"+$(this).attr('name')).parents("tr").hide();
    }else
        isFirst = false;
});
isFirst = true;
$(".champ_values").each(function(){
    if(!isFirst){
        $(this).parents("tr").hide();
        $("#def_"+$(this).attr('name')).parents("tr").hide();
    }else
        isFirst = false;
});
$("#chp_type").change(function(){
    if($(this).val() == "liste"){
        $(".champ_values").attr('disabled',false);
    }else{
        $(".champ_values").attr('disabled',true);
    }
});
$('div#default h3:first')
    .append('<select style="margin-left:10px;" name="lang">$languages</select>')
    .append('<span class="infos">$infoLg</span>');
$('div#default h3:first select').change(function(){
    var id = $(this).val();
    $(".champ_libelle").each(function(){
        if($(this).attr('name') == "libelle_"+id){
            $(this).parents("tr").show();
            $("#def_"+$(this).attr('name')).parents("tr").show();
        }else{
            $(this).parents("tr").hide();
            $("#def_"+$(this).attr('name')).parents("tr").hide();
        }
    });
    $(".champ_values").each(function(){
        if($(this).attr('name') == "values_"+id){
            $(this).parents("tr").show();
            $("#def_"+$(this).attr('name')).parents("tr").show();
        }else{
            $(this).parents("tr").hide();
            $("#def_"+$(this).attr('name')).parents("tr").hide();
        }
    });
});
$('#champ_categorie').after('<img id="add_categ" style="cursor:pointer;" src="{$this->getTemplateWebPath("/gfx/ajouter16.png")}" />');
var categVal = "";
$('div#champ_categorie_chzn div.chzn-search input').live('change',function(){
    categVal = $(this).val();
});
$('img#add_categ').click(function(){
    if(categVal != ""){
        $.ajax({
            type: "GET",
            url: "{$view->get('add_categ')}",
            async: false,
            dataType : "json",
            data : {
                "val" : categVal
            },
            success : function(data){
                var options = "";
                for(i=0;i<=data.lst.length-1;i++){
                    if(data.lst[i].id == data.selected)
                        options += '<option value="'+data.lst[i].id+'" selected=true>'+data.lst[i].label+'</option>';
                    else
                        options += '<option value="'+data.lst[i].id+'">'+data.lst[i].label+'</option>';
                }
                $('select#champ_categorie').html(options).trigger("liszt:updated");
            }
        });
    }
});
$('#champ_categorie').chosen({allow_single_deselect:false, no_results_text: '$noResult'});
ADDITIONAL_JS;

// formulaire
$form = new Dims\form(array(
    'name'                  => 'f_champ',
    'action'                => $this->get('action_path'),
    'back_url'              => $this->get('back_path'),
    'submit_value'          => dims_constant::getVal('_SAVE_FIELD'),
    'back_name'             => dims_constant::getVal('_DIMS_CANCEL'),
    'additional_js'         => $additional_js,
    'global_message_error'  => addslashes(dims_constant::getVal('PLEASE_VERIFY_EACH_LANG'))
    ));
$form->addBlock('default', ($elem->isNew())?dims_constant::getVal('_NEW_FIELD'):dims_constant::getVal('_EDITING_FIELD')." : ".$elem->getLibelle());
$form->add_hidden_field(array('name'          => 'lang'));
$form->add_hidden_field(array(
        'name'          => 'id_chp',
        'value'         => $elem->get('id'),
        'block'         => 'default'
    ));
$lstCateg = array(0 => ucfirst(dims_constant::getVal('NO_ELEMENT_FEMININ')));
foreach($view->get('lst_cat') as $cat){
    $lstCateg[$cat->fields['id_globalobject']] = $cat->getLabel();
}
$form->add_select_field(array(
    'name'          => 'categorie',
    'id'            => 'champ_categorie',
    'label'         => dims_constant::getVal('_RSS_LABEL_CATEGORY'),
    'options'       => $lstCateg,
    'value'         => $view->get('my_categ'),
    'block'         => 'default',
    'additionnal_attributes'    => 'style="width:250px;" data-placeholder=""'
));
foreach ($view->get('languages') as $id => $lg) {
    $form->add_text_field(array(
        'name'          => "libelle_$id",
        'label'         => dims_constant::getVal('_FIELD_FIELDNAME'),
        'value'         => $elem->getLibelle($id),
        'block'         => 'default',
        'mandatory'     => true,
        'classes'       => 'champ_libelle'
    ));
}
$form->add_select_field(array(
    'name'          => 'chp_type',
    'id'            => 'chp_type',
    'label'         => dims_constant::getVal('_TYPE'),
    'options'       => cata_champ::getTypes(),
    'value'         => $elem->fields['type'],
    'block'         => 'default',
    'mandatory'     => true
));
foreach ($view->get('languages') as $id => $lg) {
    $form->add_textarea_field(array(
        'name'          => "values_$id",
        'label'         => dims_constant::getVal('_LIST_OF_VALUES'),
        'value'         => implode("\r\n",$elem->getvaleurs($id,true)),
        'block'         => 'default',
        'mandatory'     => $elem->fields['type'] == "liste",
        'additionnal_attributes'    => ($elem->fields['type'] != "liste")?'disabled=true':"",
        "classes"       => "champ_values"
    ));
}
$form->add_checkbox_field(array(
    'id'            => 'chp_filtre',
    'name'          => 'chp_filtre',
    'label'         => dims_constant::getVal('_USE_FIELD_AS_FILTER'),
    'value'         => "1",
    'block'         => 'default',
    'checked'       => $elem->fields['filtre']
));
$form->add_checkbox_field(array(
    'id'            => 'chp_fiche',
    'name'          => 'chp_fiche',
    'label'         => dims_constant::getVal('_USED_IN_TECHNICAL'),
    'value'         => "1",
    'block'         => 'default',
    'checked'       => $elem->fields['fiche']
));
$form->add_checkbox_field(array(
    'id'            => 'allow_unique_option',
    'name'          => 'allow_unique_option',
    'label'         => dims_constant::getVal('CATA_ALLOW_UNIQUE_OPTION'),
    'value'         => "1",
    'block'         => 'default',
    'checked'       => $elem->fields['allow_unique_option']
));

$form->add_checkbox_field(array(
    'id'            => 'chp_global_filter',
    'name'          => 'chp_global_filter',
    'label'         => dims_constant::getVal('DEFINE_AS_GLOBAL_FILTER'),
    'value'         => "1",
    'block'         => 'default',
    'checked'       => $elem->fields['global_filter']
));
foreach ($view->get('languages') as $id => $lg) {
    $form->add_text_field(array(
        'name'          => "global_filter_label_$id",
        'label'         => dims_constant::getVal('GLOBAL_FILTER_LABEL'),
        'value'         => $elem->getGlobalFilterLabel($id),
        'block'         => 'default'
    ));
    $form->add_select_field(array(
        'name'          => 'chp_global_filter_value',
        'id'            => 'chp_global_filter_value',
        'label'         => dims_constant::getVal('GLOBAL_FILTER_VALUE'),
        'options'       => $elem->getvaleurs($id, true),
        'value'         => $elem->getGlobalFilterValue(),
        'block'         => 'default'
    ));
}
$form->build();
