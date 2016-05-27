<?php
$view = view::getInstance();
?>
<div class="params_content">
    <h2><?= dims_constant::getVal('_TAG_CLOUD'); ?></h2>

    <div class="actions">
        <a href="<?= get_path('objects', 'edittags'); ?>" class="link_img">
            <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('_ADD_TAG_CLOUD'); ?>" alt="<?= dims_constant::getVal('_ADD_TAG_CLOUD'); ?>" />
            <span><?= dims_constant::getVal('_ADD_TAG_CLOUD'); ?></span>
        </a>
    </div>
    <table class="tableau">
        <tr>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_NUMBERS_ELEMENTS'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DIMS_LABEL_ENT_DATEC'); ?>
            </td>
            <td class="title_tableau">
                <?= dims_constant::getVal('_DATE_OF_UPDATE'); ?>
            </td>
            <td class="w70p title_tableau">
                <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
            </td>
        </tr>
        <?php foreach($view->get('clouds') as $tag){ ?>
            <tr>
                <td>
                    <?= $tag->fields['nom']; ?>
                </td>
                <td>
                    <?= $tag->fields['description']; ?>
                </td>
                <td>
                    <?= $tag->getLightAttribute('nb_elem'); ?>
                </td>
                <td>
                    <?php
                    $dd = dims_timestamp2local($tag->fields['timestp_create']);
                    echo $dd['date'];
                    ?>
                </td>
                <td>
                    <?php
                    $dd = dims_timestamp2local($tag->fields['timestp_modify']);
                    echo $dd['date'];
                    ?>
                </td>
                <td class="center">
                    <a style="text-decoration:none;" href="<?= get_path('objects', 'showtag', array('id'=>$tag->get('id'))); ?>">
                        <img src="<?= $this->getTemplateWebPath("/gfx/ouvrir16.png"); ?>" alt="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" title="<?= dims_constant::getVal('_DIMS_OPEN'); ?>" />
                    </a>
                    <a onclick="javascript:dims_confirmlink('<?= get_path('objects', 'deltag', array('id'=>$tag->get('id'))); ?>','<?= dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>');" style="text-decoration:none;" href="javascript:void(0);">
                        <img src="<?= $this->getTemplateWebPath("/gfx/poubelle16.png"); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" />
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
