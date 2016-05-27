<?php
$view = view::getInstance();
$client = $view->get('client');

$additional_js = <<< ADDITIONAL_JS
$('#cli_id_pays').chosen({allow_single_deselect:true});
$("#cli_use_add_client").change(function(){
    if($(this).is(':checked')){
        $('tr.sub_factu').hide();
        console.log('hide');
    }else{
        $('tr.sub_factu').show();
        console.log('show');
    }
});
ADDITIONAL_JS;

$form = new Dims\form(array(
    'name'              => 'c_facturation',
    'action'            => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses','sa'=>'save')),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'validation'        => false,
    'additional_js'     => $additional_js,
));

$form->addBlock ('facturation', dims_constant::getVal('_BILLING_ADDRESS'), $this->getTemplatePath('clients/show/address_facturation.tpl.php'));

$form->add_checkbox_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_use_add_client',
    'id'            => 'cli_use_add_client',
    'label'         => dims_constant::getVal('_USE_ADDRESS_COMPANY'),
    'checked'       => $client->fields['use_add_client'],
    'value'         => 1
));

$form->add_text_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_nom',
    'label'         => dims_constant::getVal('_DIMS_LABEL_NAME'),
    'value'         => $client->fields['nom']
));
$form->add_text_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_adr1',
    'label'         => dims_constant::getVal('_DIMS_LABEL_ADDRESS'),
    'value'         => $client->fields['adr1']
));
$form->add_text_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_adr2',
    'label'         => dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'),
    'value'         => $client->fields['adr2']
));
$form->add_text_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_adr3',
    'label'         => dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'),
    'value'         => $client->fields['adr3']
));
$form->add_text_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_cp',
    'label'         => dims_constant::getVal('_DIMS_LABEL_CP'),
    'value'         => $client->fields['cp']
));
$form->add_text_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_ville',
    'label'         => dims_constant::getVal('_DIMS_LABEL_CITY'),
    'value'         => $client->fields['ville']
));
$form->add_select_field(array(
    'block'         => 'facturation',
    'name'          => 'cli_id_pays',
    'label'         => dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
    'value'         => $client->fields['id_pays'],
    'options'       => $view->get('pays'),
    'additionnal_attributes' => 'style="width:250px;"'
));

$form->build();
?>
<div class="form_object_block">
    <div class="sub_bloc">
        <h3><?= dims_constant::getVal('_SHIPPING_ADDRESSES'); ?></h3>
        <div class="sub_bloc_form">
            <div style="text-align:right;">
                <a href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses', 'sa' => 'editliv')); ?>" title="<?= dims_constant::getVal('_ADD_DELIVERY_ADDRESS'); ?>">
                    <img src="<?= $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
                    <?= dims_constant::getVal('_ADD_DELIVERY_ADDRESS'); ?>
                </a>
            </div>
            <table class="tableau">
                <tr>
                    <td class="title_tableau">
                        <?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?>
                    </td>
                    <td class="title_tableau">
                        <?= dims_constant::getVal('_DIMS_LABEL_ADDRESS'); ?>
                    </td>
                    <td class="title_tableau">
                        <?= dims_constant::getVal('_DIMS_LABEL_CP'); ?> / <?= dims_constant::getVal('_DIMS_LABEL_CITY'); ?> / <?= dims_constant::getVal('_DIMS_LABEL_COUNTRY'); ?>
                    </td>
                    <td class="title_tableau w100p">
                        <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
                    </td>
                </tr>
                <?php
                foreach($view->get('addresses') as $adr){
                    ?>
                    <tr>
                        <td>
                            <?= $adr->fields['nomlivr']; ?>
                        </td>
                        <td>
                            <?= $adr->fields['adr1'].(($adr->fields['adr2'] != '')?"<br />".$adr->fields['adr2']:"").(($adr->fields['adr3'] != '')?"<br />".$adr->fields['adr3']:""); ?>
                        </td>
                        <td>
                            <?= $adr->fields['cp']." ".$adr->getCity()->getLabel()."<br />".$adr->getCountry()->getLabel(); ?>
                        </td>
                        <td>
                            <a href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses', 'sa' => 'showliv', 'livid' => $adr->get('id'))); ?>">
                                <img src="<?= $view->getTemplateWebPath('gfx/pouce16.png'); ?>" />
                            </a>
                            <a href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses', 'sa' => 'editliv', 'livid' => $adr->get('id'))); ?>">
                                <img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>" />
                            </a>
                            <a onclick="javascript:dims_confirmlink('<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses', 'sa' => 'delliv', 'livid' => $adr->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>');" href="javascript:void(0);">
                                <img src="<?= view->getTemplateWebPath('gfx/supprimer16.png'); ?>" />
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
