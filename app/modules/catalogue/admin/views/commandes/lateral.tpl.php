<?php
$view = view::getInstance();
$view->partial($view->getTemplatePath('shared/_lateral_search_block.tpl.php'));
?>
<h3><?= dims_constant::getVal('LAST_ORDERS'); ?></h3>
<?php
$last_commandes = $this->get('last_commandes');
if(count($last_commandes)){
    ?>
    <table>
        <?php
        foreach($last_commandes as $id_comm){
            $cde = new commande();
            $cde->open($id_comm);
            $cli = $cde->getClient();
            if (is_null($cli)) {
                continue;
            }
            ?>
            <tr>
                <td>
                    <img src="<?= $view->getTemplateWebPath('gfx/info16.png'); ?>" />
                </td>
                <td>
                    <a href="<?= dims::getInstance()->getScriptEnv()."?c=commandes&a=show&id=".$cde->get('id'); ?>">
                        <?= $cde->get('id_cde')." - ".$cli->fields['nom']; ?>
                    </a>
                </td>
            <?php
        }
        ?>
    </table>
    <?php
}else{
    ?>
    <span class="no_elem">
        <?= dims_constant::getVal('_NO_COMMAND_CONSULTED'); ?>
    </span>
    <?php
}
$this->partial($this->getTemplatePath('shared/_lateral_actions.tpl.php'));
$this->partial($this->getTemplatePath('shared/_lateral_clipboard.tpl.php'));
?>
