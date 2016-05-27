<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
$fam_keywords = $view->get('fam_keywords');
$defaultFam_search = $view->get('defaultFam_search');
?>
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
                        <a href="<?= $view->get('path_lateral_search').'&filter_init=1' ; ?>"><img style="padding-top: 5px;" src="<?= $view->getTemplateWebPath('gfx/'.$img);?>" /></a>
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
                    'checked'   => true,
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
$form = new Dims\form(array(
    'name'              => 'lst_champs',
    'action'            => $view->get('action_path'),
    'validation'        => false
));
echo $form->get_header();
?>
<table class="tableau">
    <tr>
        <td style="width:100px;">
            <?= dims_constant::getVal('_POSITION'); ?>
        </td>
        <td>
            <?= dims_constant::getVal('_DIMS_LABEL'); ?>
        </td>
        <td>
            <?= dims_constant::getVal('_RSS_LABEL_CATEGORY'); ?>
        </td>
        <td style="width:100px;">
            <?= dims_constant::getVal('_TECHNICAL'); ?>
        </td>
        <td style="width:100px;">
            <?= dims_constant::getVal('_FORMS_FILTER'); ?>
        </td>
    </tr>
<?php
$lstCateg = array();
$nbElem = count($view->get('sel_champs'));
$lstPos = array();
for ($i=1;$i<=$nbElem;$i++)
    $lstPos[$i] = $i;
foreach($view->get('sel_champs') as $champ){
    $prop = $champ->getPropertiesFamille($elem->get('id'));
    ?>
    <tr>
        <td style="width:75px;">
            <?php
            echo $form->select_field(array(
                'name'      => 'pos_'.$champ->fields['id'],
                'options'   => $lstPos,
                'value'     => $prop->fields['position'],
                'additionnal_attributes' => 'onchange="javascript:document.lst_champs.submit();"'
            ));
            ?>
        </td>
        <td>
            <?= $champ->fields['libelle']; ?>
        </td>
        <td>
            <?= $champ->getLabelCateg($lstCateg); ?>
        </td>
        <td style="text-align:center;">
            <?php
            if($prop->fields['fiche']){
                ?>
                <img src="<?= $view->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" alt="" title="" />
                <?php
            }else{
                ?>
                <img src="<?= $view->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" alt="" title="" />
                <?php
            }
            ?>
        </td>
        <td style="text-align:center;">
            <?php
            if($prop->fields['filtre']){
                ?>
                <img src="<?= $view->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" alt="" title="" />
                <?php
            }else{
                ?>
                <img src="<?= $view->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" alt="" title="" />
                <?php
            }
            ?>
        </td>
    </tr>
    <?php
}
?>
</table>
<?php
echo $form->close_form();
?>
