<?php
$view = view::getInstance();
$client = $view->get('client');
$adr = $view->get('adr');

$additional_js = <<< ADDITIONAL_JS
function refreshCityOfCountry(id){
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op': 'desktopv2',
            'action' : 'client_refresh_city',
            'id': id
        },
        dataType: "text",
        async: false,
        success: function(data){
            $('#adr_id_city').html(data);
            $('#adr_id_city option[value="{$adr->fields['id_city']}"]').attr('selected',true);
            $('#adr_id_city').trigger("liszt:updated");
        },
        error: function(data){}
    });
}
$('#adr_id_country')
    .chosen({allow_single_deselect:true})
    .change(function(){
        if(($(this).val() != '' && $(this).val() > 0))
            $('#adr_id_city').removeAttr('disabled');
        else
            $('#adr_id_city').attr('disabled',true);
        console.log($(this).val());
        refreshCityOfCountry($(this).val(),'city');
    });
$('#adr_id_city').chosen({allow_single_deselect:true});
ADDITIONAL_JS;

$form = new Dims\form(array(
    'name'              => 'c_facturation',
    'action'            => $view->get('action_path'),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => $view->get('back_path'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'validation'        => false,
    'additional_js'     => $additional_js,
));

$form->addBlock ('livraison', dims_constant::getVal('_DELIVERY_ADDRESS'));

$form->add_text_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_nomlivr',
    'label'         => dims_constant::getVal('_DIMS_LABEL_NAME'),
    'value'         => $adr->fields['nomlivr']
));
$form->add_text_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_adr1',
    'label'         => dims_constant::getVal('_DIMS_LABEL_ADDRESS'),
    'value'         => $adr->fields['adr1']
));
$form->add_text_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_adr2',
    'label'         => dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'),
    'value'         => $adr->fields['adr2']
));
$form->add_text_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_adr3',
    'label'         => dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'),
    'value'         => $adr->fields['adr3']
));
$form->add_text_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_cp',
    'label'         => dims_constant::getVal('_DIMS_LABEL_CP'),
    'value'         => $adr->fields['cp']
));
$form->add_select_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_id_country',
    'label'         => dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
    'value'         => $adr->fields['id_country'],
    'options'       => $view->get('pays'),
    'additionnal_attributes' => 'style="width:250px;"'
));
$lstCity = array();
$disabled = " disabled=true";
if($adr->fields['id_country'] != '' && $adr->fields['id_country'] > 0){
    include_once DIMS_APP_PATH.'modules/system/class_country.php';
    $country = new country();
    $country->open($adr->fields['id_country']);
    foreach($country->getAllCity() as $city)
        $lstCity[$city->fields['id']] = $city->fields['label'];
    $disabled = "";
}
$form->add_select_field(array(
    'block'         => 'livraison',
    'name'          => 'adr_id_city',
    'label'         => dims_constant::getVal('_DIMS_LABEL_CITY'),
    'value'         => $adr->fields['id_city'],
    'options'       => $lstCity,
    'additionnal_attributes' => 'style="width:250px;"'.$disabled
));

$form->build();
?>