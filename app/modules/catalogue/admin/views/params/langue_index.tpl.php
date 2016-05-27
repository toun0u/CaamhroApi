<?php
$view = view::getInstance();
$isActive = $view->get('active_lg');

$additional_js = <<< ADDITIONAL_JS
// boutons ON / OFF
$('#cata_active_lg').buttonset();
$('#cata_active_lg input').change(function(){
    $("#lg_params").submit();
});
// fermeture de tous les popups
function closeAllPopups() {
    $('#popup_cata_active_lg').fadeOut();
}
// popups info
$('#info_cata_active_lg').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_cata_active_lg'));
    $('#popup_cata_active_lg').fadeToggle('fast');
});
ADDITIONAL_JS;

$form = new Dims\form(array(
    'name'              => 'lg_params',
    'action'            => get_path('params', 'active_lg'),
    'validation'        => false,
    'back_name'         => dims_constant::getVal('REINITIALISER'),
    'back_url'          => get_path('params', 'edit'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => false,
    'additional_js'     => $additional_js
));
$form->addBlock('active_langue', dims_constant::getVal('CATA_CATALOG_CONFIGURATION'));
$form->add_radio_field(array(
    'block'     => 'active_langue',
    'id'        => 'cata_active_lg_0',
    'name'      => 'cata_active_lg',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $isActive
));
$form->add_radio_field(array(
    'block'     => 'active_langue',
    'id'        => 'cata_active_lg_1',
    'name'      => 'cata_active_lg',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$isActive
));
$block1 = $form->getBlock('active_langue');
echo $form->get_header();
?>
<div class="sub_bloc">
    <div class="sub_bloc_form">
        <table>
            <tr>
                <td class="label_field">
                    <label><?php echo dims_constant::getVal('CATA_LANGUAGES'); ?></label>
                </td>
                <td class="value_field">
                    <span id="cata_active_lg">
                        <?php echo $block1->get_field_html('cata_active_lg', '0'); ?>
                        <label for="<?php echo $block1->get_field_id('cata_active_lg', '0'); ?>">
                            <?php echo $block1->get_field_label('cata_active_lg', '0'); ?>
                        </label>

                        <?php echo $block1->get_field_html('cata_active_lg', '1'); ?>
                        <label for="<?php echo $block1->get_field_id('cata_active_lg', '1'); ?>">
                            <?php echo $block1->get_field_label('cata_active_lg', '1'); ?>
                        </label>
                    </span>
                    <span id="info_cata_active_lg" class="info_link">
                        <img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
                    </span>
                    <span id="popup_cata_active_lg" class="info_popup">
                        <p><strong><?php echo dims_constant::getVal('CATA_LANGUAGES'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_cata_active_lg').fadeOut();">
                                <?= dims_constant::getVal('_DIMS_CLOSE'); ?>
                            </a>
                        </p>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php
echo $form->close_form();
if($isActive){
    ?>
    <div class="actions">
        <a class="link_img" href="<?= get_path('params', 'lg_add'); ?>">
            <img src="<?= $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
            <span><?= dims_constant::getVal('_NEW_LANGUAGE'); ?></span>
        </a>
    </div>
    <table class="tableau">
        <tr>
            <td class="w20p title_tableau">
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_LANG'); ?>
            </td>
            <td class="w50p title_tableau">
                <?= dims_constant::getVal('_DIMS_DEFAULT'); ?>
            </td>
            <td class="w50p title_tableau">
                <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
            </td>
        </tr>
        <?php
        $lstLang = $view->get('active_langues');
        $defLang = $view->get('default_lg');
        foreach($view->get('langues') as $lang){
            ?>
            <tr>
                <td class="center">
                    <?php
                    if(!is_null($img = $lang->getFlag())){
                        ?>
                        <img src="<?= $img; ?>" alt="<?= $lang->fields['ref']; ?>" title="<?= $lang->fields['ref']; ?>" />
                        <?php
                    }
                    ?>
                </td>
                <td>
                    <?= $lang->fields['label']; ?>
                </td>
                <td class="center">
                    <?php
                    if($lang->get('id') == $defLang){
                        ?>
                        <img src="<?= $this->getTemplateWebPath("/gfx/pastille_verte16.png"); ?>" alt="<?= dims_constant::getVal('_DIMS_DEFAULT'); ?>" title="<?= dims_constant::getVal('_DIMS_DEFAULT'); ?>" />
                        <?php
                    }else{
                        ?>
                        <a href="<?= get_path('params', 'default_lg',array('id'=>$lang->get('id'))); ?>">
                            <img src="<?= $this->getTemplateWebPath("/gfx/pastille_rouge16.png"); ?>" />
                        </a>
                        <?php
                    }
                    ?>
                </td>
                <td class="center">
                    <a href="<?= get_path('params', 'switch_lg',array('id'=>$lang->get('id'))); ?>">
                        <?php
                        if(isset($lstLang[$lang->get('id')])){
                            ?>
                            <img src="<?= $this->getTemplateWebPath("/gfx/main.png"); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>" />
                            <?php
                        }else{
                            ?>
                            <img src="<?= $this->getTemplateWebPath("/gfx/pouce16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>" alt="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>" />
                            <?php
                        }
                        ?>

                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}
?>
