<?php
$view = view::getInstance();
$slidart = $view->get('elem');
?>
<div class="params_content">
    <h2><?= dims_constant::getVal('_SLIDESHOW_ARTICLES'); ?> > <?= $slidart->fields['nom']; ?></h2>
    <div class="infos">
        <span style="font-size:16px;color:#FD661F;font-weight:bold;">"</span>
        <?= $slidart->fields['description']; ?>
        <span style="font-size:16px;color:#FD661F;font-weight:bold;">"</span>
    </div>
    <div class="actions">
        <a href="<?= get_path('objects', 'editart', array('id'=>$slidart->get('id'))); ?>" class="link_img">
            <img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" />
            <span><?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?></span>
        </a>
    </div>

    <h3>
        <?= dims_constant::getVal('_LIST_OF_ITEMS'); ?>
    </h3>
    <div class="actions">
        <a href="<?= get_path('objects', 'showart', array('id'=>$slidart->get('id'), 'sa'=>'addelemart')); ?>" class="link_img">
            <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('_ADD_AN_ITEM'); ?>" alt="<?= dims_constant::getVal('_ADD_AN_ITEM'); ?>" />
            <span><?= dims_constant::getVal('_ADD_AN_ITEM'); ?></span>
        </a>
    </div>
    <table class="tableau">
        <tr>
            <td class="title_tableau w60p">

            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('REFERENCE'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_ENT_DATEC'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DATE_OF_UPDATE'); ?>
            </td>
            <td class="w80p title_tableau">
                <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
            </td>
        </tr>
        <?php
        $elements = $view->get('elements');
        $nbElem = count($elements)-1;
        foreach($elements as $key => $elem){
            $art = $elem->getArticle();
            $url = $art->getVignette(50);
            ?>
            <tr>
                <td>
                    <?php
                    if(!is_null($url)){
                        ?>
                        <img src="<?= $url; ?>" />
                        <?php
                    }
                    ?>
                </td>
                <td>
                    <?= $art->fields['reference']; ?>
                </td>
                <td>
                    <?= $art->fields['label']; ?>
                </td>
                <td>
                    <?php
                    $dd = dims_timestamp2local($elem->fields['timestp_create']);
                    echo $dd['date'];
                    ?>
                </td>
                <td>
                    <?php
                    $dd = dims_timestamp2local($elem->fields['timestp_modify']);
                    echo $dd['date'];
                    ?>
                </td>
                <td class="center">
                    <a style="text-decoration:none;" href="<?= get_path('objects', 'showart', array('id'=>$slidart->get('id'), 'sa'=>'addelemart', 'sid'=>$elem->get('id'))); ?>">
                        <img src="<?= $this->getTemplateWebPath("/gfx/ouvrir16.png"); ?>" alt="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" title="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" />
                    </a>
                    <?php if($key > 0){ ?>
                        <a style="text-decoration:none;" href="<?= get_path('objects', 'showart', array('id'=>$slidart->get('id'), 'sa'=>'leftelemart', 'sid'=>$elem->get('id'))); ?>">
                            <img src="<?= $this->getTemplateWebPath("/gfx/haut16_s.png"); ?>" alt="<?= dims_constant::getVal('_LEFT'); ?>" title="<?= dims_constant::getVal('_LEFT'); ?>" />
                        </a>
                    <?php }else{ ?>
                        <img src="<?= $this->getTemplateWebPath("/gfx/haut16_ns.png"); ?>" alt="<?= dims_constant::getVal('_LEFT'); ?>" title="<?= dims_constant::getVal('_LEFT'); ?>" />
                    <?php } ?>
                    <?php if($key < $nbElem){ ?>
                        <a style="text-decoration:none;" href="<?= get_path('objects', 'showart', array('id'=>$slidart->get('id'), 'sa'=>'rightelemart', 'sid'=>$elem->get('id'))); ?>">
                            <img src="<?= $this->getTemplateWebPath("/gfx/bas16_s.png"); ?>" alt="<?= dims_constant::getVal('_RIGHT'); ?>" title="<?= dims_constant::getVal('_RIGHT'); ?>" />
                        </a>
                    <?php }else{ ?>
                        <img src="<?= $this->getTemplateWebPath("/gfx/bas16_ns.png"); ?>" alt="<?= dims_constant::getVal('_RIGHT'); ?>" title="<?= dims_constant::getVal('_RIGHT'); ?>" />
                    <?php } ?>
                    <a onclick="javascript:dims_confirmlink('<?= get_path('objects', 'showart', array('id'=>$slidart->get('id'), 'sa'=>'delelemart', 'sid'=>$elem->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>');" style="text-decoration:none;" href="javascript:void(0);">
                        <img src="<?= $this->getTemplateWebPath("/gfx/poubelle16.png"); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" />
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>