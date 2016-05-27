<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
$fam_keywords = $view->get('fam_keywords');
$defaultFam_search = $view->get('defaultFam_search');
?>
<div style="clear: both;">
    <div class="new_info_article">
        <table>
            <tr>
                <td>
                    <img src="<?= $view->getTemplateWebPath('gfx/info32.png'); ?>" title="Info" alt="info"/>
                </td>
                <td>
                    <?= str_replace('{URL}',get_path('params', 'champs'),dims_constant::getVal('_INFOS_FAMILLE_CHP_LIBRE')); ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="sub_actions" style="padding-top: 10px;">
        <?
    $add_sj = <<< ADDITIONAL_JS
$('#fam_keywords').focus(function(){
    //focus on
    if($(this).val() == '{$defaultFam_search}'){
        $(this).val('');
        $(this).removeClass('default');
    }
});

$('#fam_keywords').focusout(function(){
    //focus out
    if($(this).val() == ''){
        $(this).val('{$defaultFam_search}');
        $(this).addClass('default');
    }
});
ADDITIONAL_JS;
        $form2 = new Dims\form(array(
            'name'              => 'filtre',
            'id'                => 'filtre',
            'action'            => $view->get('action_path2'),
            'include_actions'   => false,
            'additional_js'     => $add_sj
        ));
        echo $form2->get_header();
        ?>
        <table>
            <tr>
                <td>
                    <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=editchamp&id=".$elem->fields['id']; ?>">
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('_CREATE_FIELD'); ?>" alt="<?= dims_constant::getVal('_CREATE_FIELD'); ?>" />
                        <span><?= dims_constant::getVal('_CREATE_FIELD'); ?></span>
                    </a>
                </td>
                <td>
                    <div class="input_container">
                        <?php
                        echo $form2->text_field(array(
                            'name'      => 'fam_keywords',
                            'id'        => 'fam_keywords',
                            'value'     => (!empty($fam_keywords)) ? $fam_keywords : $defaultFam_search,
                            'classes'   => 'keywords_search '.((!empty($fam_keywords)) ? 'search_input': 'default search_input')
                        ));
                        $img = ( ! empty($fam_keywords) ) ? 'vider16.png' : 'loupe16.png';
                        if(empty($fam_keywords)){
                            ?>
                            <input type="image" src="<?= $view->getTemplateWebPath('gfx/'.$img);?>" value=""/>
                            <?php
                        }
                        else{
                            ?>
                            <a href="<?= $view->get('action_path2').'&filter_init=1' ; ?>">
                                <img style="padding-top: 5px;" src="<?= $view->getTemplateWebPath('gfx/'.$img);?>" />
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                </td>
                <td>
                    <?php
                    echo $form2->checkbox_field(array(
                        'name'      => 'my_champ',
                        'id'        => 'my_champ',
                        'value'     => 1,
                        'checked'   => false,
                        'additionnal_attributes' => 'onchange="javascript:document.filtre.submit();"'
                    ));
                    ?>
                    <label for="my_champ">
                        <?= dims_constant::getVal('_SHOW_ONLY_FIELDS_PRESENT_FAMILY'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?
        echo $form2->close_form();
        ?>
    </div>
    <p style="clear: both; height: 1px;"></p>
    <?php
    $puceVerte = $view->getTemplateWebPath('gfx/pastille_verte12.png');
    $puceRouge = $view->getTemplateWebPath('gfx/pastille_rouge12.png');
    $additional_js = <<< ADDITIONAL_JS
$('form#lst_champs input.pastilles').each(function(){
    var url = "{$puceRouge}";
    if($(this).is(':checked'))
        url = "{$puceVerte}";
    $(this).hide();
    $(this).after('<img src="'+url+'" title="" alt="" style="cursor:pointer;" />');
    $(this).next("img").click(function(){
            if($(this).prev("input").is(':checked')){
                $(this).attr("src","{$puceRouge}");
                $(this).prev("input:first").attr("checked",false);
            }else{
                $(this).attr("src","{$puceVerte}");
                $(this).prev("input:first").attr("checked",true);
            }
            var firstInput = $("input[type='checkbox']:first",$(this).parents("tr:first"));
            if(firstInput.not(":checked"))
                firstInput.attr("checked",true);
        });
});
ADDITIONAL_JS;
    $form = new Dims\form(array(
        'name'              => 'lst_champs',
        'action'            => $view->get('action_path'),
        'validation'        => false,
        'back_name'         => dims_constant::getVal('REINITIALISER'),
        'back_url'          => $view->get('back_path'),
        'submit_value'      => dims_constant::getVal('_SAVE_SELECTION'),
        'include_actions'   => true,
        'additional_js'     => $additional_js
    ));

    echo $form->get_header();
    $lstChampsSearch  = $view->get('lst_champs');
    foreach($view->get('lst_categ') as $categ){
        if(count($lstChampsSearch ))
            $lstChamps = array_intersect($categ->searchGbLink(cata_champ::MY_GLOBALOBJECT_CODE), $lstChampsSearch);
        else
            $lstChamps = $categ->searchGbLink(cata_champ::MY_GLOBALOBJECT_CODE);
        if(count($lstChamps)){
            ?>
            <h3>
                <?= $categ->getLabel(); ?>
            </h3>
            <table class="tableau">
                <tr>
                    <td colspan="2">
                        <?= dims_constant::getVal('_CLICK_DOT_SET_USE_LOCAL_FIELDS'); ?>
                    </td>
                    <td style="width:100px;">
                        <?= dims_constant::getVal('_TECHNICAL'); ?>
                    </td>
                    <td style="width:100px;">
                        <?= dims_constant::getVal('_FORMS_FILTER'); ?>
                    </td>
                </tr>
                <?php
                foreach($lstChamps as $goChamp){
                    $champ = new cata_champ();
                    $champ->openWithGB($goChamp);
                    if(!$champ->isNew()){
                        $prop = $champ->getPropertiesFamille($elem->get('id'));
                        ?>
                        <tr>
                            <td style="width:20px;">
                                <?php
                                echo $form->checkbox_field(array(
                                    'name'      => 'selection[]',
                                    'value'     => $champ->get('id'),
                                    'checked'   => !is_null($prop)
                                ));
                                ?>
                            </td>
                            <td>
                                <?= $champ->fields['libelle']; ?>
                            </td>
                            <?php
                            if(is_null($prop)){
                                ?>
                                <td style="text-align:center;">
                                    <?php
                                    echo $form->checkbox_field(array(
                                        'name'      => 'fiche_'.$champ->get('id'),
                                        'id'        => 'fiche_'.$champ->get('id'),
                                        'value'     => 1,
                                        'checked'   => $champ->fields['fiche'],
                                        'classes'   => "pastilles"
                                    ));
                                    ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php
                                    echo $form->checkbox_field(array(
                                        'name'      => 'filtre_'.$champ->get('id'),
                                        'id'        => 'filtre_'.$champ->get('id'),
                                        'value'     => 1,
                                        'checked'   => $champ->fields['filtre'],
                                        'classes'   => "pastilles"
                                    ));
                                    ?>
                                </td>
                                <?
                            }else{
                                ?>
                                <td style="text-align:center;">
                                    <?php
                                    echo $form->checkbox_field(array(
                                        'name'      => 'fiche_'.$champ->get('id'),
                                        'id'        => 'fiche_'.$champ->get('id'),
                                        'value'     => 1,
                                        'checked'   => $prop->fields['fiche'],
                                        'classes'   => "pastilles"
                                    ));
                                    ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php
                                    echo $form->checkbox_field(array(
                                        'name'      => 'filtre_'.$champ->get('id'),
                                        'id'        => 'filtre_'.$champ->get('id'),
                                        'value'     => 1,
                                        'checked'   => $prop->fields['filtre'],
                                        'classes'   => "pastilles"
                                    ));
                                    ?>
                                </td>
                                <?
                            }
                            ?>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php
        }
    }
    if( $form->includesActionsBlock() ){
        $form->displayActionsBlock();
    }
    echo $form->close_form();
?>
</div>
