<?php
$view = view::getInstance();
$article = $view->get('article');

$scriptenv = dims::getInstance()->getScriptEnv().'?c=articles&a=show';

$sc = $view->get('sc') ;
?>
<div class="sub_menu">
    <a href="<?= $scriptenv."&sc=tarifs&id=".$article->get('id'); ?>" <?php if( ! isset($sc) || $sc == 'tarifs') echo 'class="selected"';?>>
        <div><?= dims_constant::getVal('PRICES_AND_STOCK'); ?></div>
    </a>
    <a href="<?= $scriptenv."&sc=description&id=".$article->get('id'); ?>" <?php if($sc == 'description') echo 'class="selected"';?>>
        <div><?= dims_constant::getVal('DESCRIPTION_RECORD'); ?></div>
    </a>
    <a href="<?= $scriptenv."&sc=kit&id=".$article->get('id'); ?>" <?php if($sc == 'kit') echo 'class="selected"';?>>
        <div>
            <?php
            if($article->isKit()){
                ?>
                <img src="<?= $view->getTemplateWebPath('gfx/pastille_verte12.png');?>"/>
                <?php
            }
            else{
                 ?>
                <img src="<?= $view->getTemplateWebPath('gfx/pastille_rouge12.png');?>"/>
                <?php
            }
            ?>
            <?= dims_constant::getVal('KIT'); ?>
        </div>
    </a>
    <a href="<?= $scriptenv."&sc=vignettes&id=".$article->get('id'); ?>" <?php if($sc == 'vignettes') echo 'class="selected"';?>>
        <div><?= dims_constant::getVal('VIGNETTES'); ?></div>
    </a>
    <a href="<?= $scriptenv."&sc=links&id=".$article->get('id'); ?>" <?php if($sc == 'links') echo 'class="selected"';?>>
        <div><?= dims_constant::getVal('ARTICLE_S_LINKS'); ?></div>
    </a>
    <a href="<?= $scriptenv."&sc=references&id=".$article->get('id'); ?>" <?php if($sc == 'references') echo 'class="selected"';?>>
        <div><?= dims_constant::getVal('REFERENCES'); ?></div>
    </a>
</div>
