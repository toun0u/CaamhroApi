<?php
$view = view::getInstance();
$lstFam = $view->get('result');
?>
<table class="tableau">
    <tr>
        <td>
            &nbsp;
        </td>
        <td>
            Réf.
        </td>
        <td>
            <?= dims_constant::getVal('_DIMS_LABEL'); ?>
        </td>
        <td>
            <?= dims_constant::getVal('_SENTENCE'); ?>
        </td>
        <td style="width:75px;text-align:center;">
            <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
        </td>
    </tr>
    <?php
    if(count($lstFam)){
        foreach($lstFam as $fam){
            ?>
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    <?= $fam->fields['code']; ?>
                </td>
                <td>
                    <?= $fam->getLabel(); ?>
                </td>
                <td>
                    <?= $fam->getLightAttribute('sentence'); ?>
                </td>
                <td style="text-align:center;">
                    <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$fam->get('id'); ?>">
                        <img src="<?= $this->getTemplateWebPath("/gfx/ouvrir16.png"); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_VIEW'); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_VIEW'); ?>" />
                    </a>
                </td>
            </tr>
            <?php
        }
    }else{
        ?>
        <tr>
            <td colspan="5" style="text-align:center;">
                <?= dims_constant::getVal('NO_RESULT'); ?>
            </td>
        </tr>
        <?php
    }
    ?>
</table>