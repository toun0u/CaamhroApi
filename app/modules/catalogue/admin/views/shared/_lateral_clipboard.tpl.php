<?php $view = view::getInstance();
$lst_clipboard = $view->get('lst_clipboard');
if( isset($lst_clipboard) && count($lst_clipboard)){
    include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
    ?>
    <h3 class="h3_underline">
        <?= dims_constant::getVal('_CLIPBOARD'); ?>
        <a href="<?= get_path($view->get('c'), 'empty_clipboard'); ?>" style="float:right;">
            <img src="<?= $view->getTemplateWebPath('gfx/poubelle16.png'); ?>" title="<?= dims_constant::getVal('EMPTY_CLIPBOARD'); ?>" alt="<?= dims_constant::getVal('EMPTY_CLIPBOARD'); ?>" />
        </a>
    </h3>
    <ul>
        <?php
        foreach($lst_clipboard as $idArt){
            $article = new article();
            $article->open($idArt);
            ?>
            <li>
                <a href="<?= dims::getInstance()->getScriptEnv()."?c=articles&a=show&id=".$article->get('id'); ?>">
                    <?= $article->getLabel(); ?>
                </a>
                <a style="float: right;margin-top: 3px;" href="<?= get_path($view->get('c'), 'shift_clipboard', array('id' => $article->fields['id'])); ?>">
                    <img class="in_clipboard" src="<?= $view->getTemplateWebPath('gfx/del16min.png'); ?>" title="<?= dims_constant::getVal('DROP_FROM_CLIPBOARD'); ?>" alt="<?= dims_constant::getVal('DROP_FROM_CLIPBOARD'); ?>"/>
                </a>
            </li>
            <?php
        }
    ?>
    </ul>
    <?php
}
?>
