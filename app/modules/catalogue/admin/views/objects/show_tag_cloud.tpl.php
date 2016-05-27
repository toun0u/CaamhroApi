<?php
$view = view::getInstance();
$tag = $view->get('elem');
?>
<div class="params_content">
    <h2><?= dims_constant::getVal('_TAG_CLOUD'); ?> > <?= $tag->fields['nom']; ?></h2>
    <div class="infos">
        <span style="font-size:16px;color:#FD661F;font-weight:bold;">"</span>
        <?= $tag->fields['description']; ?>
        <span style="font-size:16px;color:#FD661F;font-weight:bold;">"</span>
    </div>
    <div class="actions">
        <a href="<?= get_path('objects', 'edittags', array('id'=>$tag->get('id'))); ?>" class="link_img">
            <img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" />
            <span><?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?></span>
        </a>
    </div>

    <h3>
        <?= dims_constant::getVal('_LIST_OF_ITEMS'); ?>
    </h3>
    <div class="actions">
        <a href="<?= get_path('objects', 'showtag', array('id'=>$tag->get('id'), 'sa'=>'addelemtag')); ?>" class="link_img">
            <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('_ADD_AN_ITEM'); ?>" alt="<?= dims_constant::getVal('_ADD_AN_ITEM'); ?>" />
            <span><?= dims_constant::getVal('_ADD_AN_ITEM'); ?></span>
        </a>
    </div>
    <table class="tableau">
        <tr>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_TITLE'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_URL'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_IMPORTANCE'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_ENT_DATEC'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DATE_OF_UPDATE'); ?>
            </td>
            <td class="w60p title_tableau">
                <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
            </td>
        </tr>
        <?php
        $elements = $view->get('elements');
        $nbElem = count($elements)-1;
        foreach($elements as $key => $elem){
            ?>
            <tr>
                <td>
                    <?= $elem->fields['titre']; ?>
                </td>
                <td>
                    <?= $elem->fields['lien']; ?>
                </td>
                <td>
                    <?= $elem->fields['niveau']; ?>
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
                    <a style="text-decoration:none;" href="<?= get_path('objects', 'showtag', array('id'=>$tag->get('id'), 'sa'=>'addelemtag', 'sid'=>$elem->get('id'))); ?>">
                        <img src="<?= $this->getTemplateWebPath("/gfx/ouvrir16.png"); ?>" alt="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" title="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" />
                    </a>
                    <a onclick="javascript:dims_confirmlink('<?= get_path('objects', 'showtag', array('id'=>$tag->get('id'), 'sa'=>'delelemtag', 'sid'=>$elem->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>');" style="text-decoration:none;" href="javascript:void(0);">
                        <img src="<?= $this->getTemplateWebPath("/gfx/poubelle16.png"); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" />
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>