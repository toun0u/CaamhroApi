<?
$view = view::getInstance();
$elem = $view->get('sel_elem');
?>
<div class="sub_actions">
    <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=attachart&id=".$elem->fields['id']; ?>">
        <img src="<?php echo $this->getTemplateWebPath("/gfx/trombone16.png"); ?>" title="<?= dims_constant::getVal('_ATTACH_ARTICLES'); ?>" alt="<?= dims_constant::getVal('_ATTACH_ARTICLES'); ?>" />
        <span><?= dims_constant::getVal('_ATTACH_ARTICLES'); ?></span>
    </a>
    <a href="<?= get_path('articles', 'new', array('referrer_family' => $elem->get('id'))); ?>">
        <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('CREATE_AN_ARTICLE'); ?>" alt="<?= dims_constant::getVal('CREATE_AN_ARTICLE'); ?>" />
        <span><?= dims_constant::getVal('CREATE_AN_ARTICLE'); ?></span>
    </a>
</div>
<div style="float:left;width:100%;">
    <?php
    $additional_js = <<< ADD_JS
$('div.sub_actions_tab a.art_actions').click(function(){
    if($('input[name="selection[]"]:checked').length || $(this).attr('ref') == 'paste'){
        $('input#act').val($(this).attr('ref'));
        document.actions_art.submit();
    }else{
        alert('Select elements');
    }
});
ADD_JS;
    $form = new Dims\form(array(
        'name'              => 'actions_art',
        'action'            => $view->get('action_path'),
        'include_actions'   => false,
        'additional_js'     => $additional_js,
        'validation'        => false
    ));
    echo $form->get_header();
    echo $form->hidden_field(array(
        'name'      => 'act',
        'id'        => 'act',
        'value'     => ""
    ));
    ?>
    <table class="tableau">
        <tr>
            <td style="width:50px;">
                &nbsp
            </td>
            <td>
                &nbsp
            </td>
            <td>
                &nbsp
            </td>
            <td>
                Réf.
            </td>
            <td>
                <?= dims_constant::getVal('_DESIGNATION'); ?>
            </td>
            <td style="width:75px;text-align:center;">
                <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
            </td>
        </tr>
        <?php
        if(count($view->get('lst_articles'))){
            foreach($view->get('lst_articles') as $article){
                ?>
                <tr>
                    <td>
                        <?php
                        echo $form->checkbox_field(array(
                            'name'      => 'selection[]',
                            'id'        => 'selection_'.$article->fields['id'],
                            'value'     => $article->fields['id']
                        ));
                        if(in_clipboard($article->fields['id']) ){
                            ?>
                            <a style="float: right;margin-top: 3px;" href="<?= get_path('familles', 'shift_clipboard', array('id' => $article->fields['id'])); ?>">
                                <img class="in_clipboard" src="<?= $view->getTemplateWebPath('gfx/del16min.png'); ?>" title="<?= dims_constant::getVal('DROP_FROM_CLIPBOARD'); ?>" alt="<?= dims_constant::getVal('DROP_FROM_CLIPBOARD'); ?>"/>
                            </a>
                            <img style="float: right;margin-top: 2px;" class="in_clipboard" src="<?= $view->getTemplateWebPath('gfx/clipboard16.png'); ?>" title="<?= dims_constant::getVal('IN_CLIPBOARD'); ?>" alt="<?= dims_constant::getVal('IN_CLIPBOARD'); ?>" />
                            <?php
                        }
                        ?>
                    </td>
                    <td style="width:25px;text-align:center;">
                        <?php
                        if($article->fields['published']){
                            ?>
                            <img src="<?= $view->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" title="Cet article est publié" alt="Article publié" />
                            <?php
                        }
                        else{
                            ?>
                            <img src="<?= $view->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" title="Cet article n'est pas publié" alt="Article non publié" />
                            <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if(!is_null($path = $article->getWebPhoto(20))){
                            ?>
                            <img src="<?= $path; ?>" />
                            <?
                        }
                        ?>
                    </td>
                    <td><?= $article->fields['reference']; ?></td>
                    <td><?= $article->getLabel(); ?></td>
                    <td style="text-align:center;">
                        <a href="<?= dims::getInstance()->getScriptEnv()."?c=articles&a=show&id=".$article->get('id'); ?>" style="text-decoration:none;">
                            <img src="<?= $view->getTemplateWebPath('gfx/ouvrir16.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" title="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" />
                        </a>
                        <a onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?c=familles&a=unlinkart&id=".$elem->get('id')."&idArt=".$article->get('id'); ?>','<?= dims_constant::getVal('_CONFIRM_DETACH_ARTICLE'); ?>');" href="javascript:void(0);" style="text-decoration:none;">
                            <img src="<?= $view->getTemplateWebPath('gfx/detacher16.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_DETACH'); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_DETACH'); ?>" />
                        </a>
                        <a onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?c=familles&a=deleteart&id=".$elem->get('id')."&idArt=".$article->get('id'); ?>','<?= dims_constant::getVal('_CONFIRM_DELETE_ARTICLE'); ?>');" href="javascript:void(0);" style="text-decoration:none;">
                            <img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" />
                        </a>
                    </td>
                <?php
            }
        }else{
            ?>
            <tr>
                <td colspan="6" style="text-align:center;">
                    <?= dims_constant::getVal('NO_RESULT'); ?>
                </td>
            </tr>
            <?
        }
        ?>
    </table>
    <div class="sub_actions_tab">
        <div style="float:left;">
            <img src="<?= $view->getTemplateWebPath('gfx/pour_la_selection20.png'); ?>" />
            <span><?= dims_constant::getVal('_FOR_SELECTION'); ?> : </span>
        </div>
        <div style="float:left;padding-top: 10px;padding-left:10px;">
            <?php
            if(count(get_clipboard())){
                ?>
                <a href="javascript:void(0);" class="art_actions" ref="paste">
                    <?= dims_constant::getVal('_PASTE'); ?>
                </a>
                <div class="separator"></div>
                <?php
            }
            ?>
            <a href="javascript:void(0);" class="art_actions" ref="copy">
                <?= dims_constant::getVal('_COPY'); ?>
            </a>
            <div class="separator"></div>
            <a href="javascript:void(0);" class="art_actions" ref="cut">
                <?= dims_constant::getVal('_CUT'); ?>
            </a>
            <div class="separator"></div>
            <a href="javascript:void(0);" class="art_actions" ref="invert">
                <?= dims_constant::getVal('_INVERT_PUBLICATION'); ?>
            </a>
        </div>
        <div class="pagination">
            <?= dims_constant::getVal('_DIMS_LABEL_PAGE'); ?> :
        </div>
    </div>
    <?= $form->close_form(); ?>
</div>
