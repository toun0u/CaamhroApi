<?php
$view = view::getInstance();
$view->partial($view->getTemplatePath('shared/_lateral_search_block.tpl.php'));
?>
<h3><?= dims_constant::getVal('_LAST_ACCESSED_FAMILIES'); ?></h3>
<?php
$last_familles = $this->get('last_familles');
if(count($last_familles)){
    ?>
    <table>
        <?php
        foreach($last_familles as $id_fam){
            $fam = new cata_famille();
            $fam->open($id_fam);
            if (!$fam->isNew()) {
                $thumb = $fam->getThumbnails(1);
                ?>
                <tr>
                    <td>
                        <?php
                        if(count($thumb) > 0){
                            $thu = current($thumb);
                            $doc = $thu->getDocFile();
                            ?>
                            <img class="last_items_ico" src="<?= $doc->getwebpath(); ?>" />
                            <?php
                        }
                        ?>
                    </td>
                    <td>
                        <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=articles&id=".$fam->get('id'); ?>">
                            <?= $fam->getLabel(); ?>
                        </a>
                    </td>
                <?php
            }
        }
        ?>
    </table>
    <?php
}else{
    ?>
    <span class="no_elem">
        <?= dims_constant::getVal('_NO_FAMILY_CONSULTED'); ?>
    </span>
    <?php
}
$this->partial($this->getTemplatePath('shared/_lateral_actions.tpl.php'));
$this->partial($this->getTemplatePath('shared/_lateral_clipboard.tpl.php'));
?>
