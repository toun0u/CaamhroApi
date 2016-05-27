<?php
$view = view::getInstance();
$current = $view->get('current');
$client = $view->get('client');
$lstChild = $this->getChildrens();
if(count($lstChild)){
    ?>
    <ul style="margin-left:<?= ($this->fields['depth']-2 > 0)?($this->fields['depth']-2)*10:0; ?>px">
        <?php
        foreach($lstChild as $child){
            ?>
            <li class="<?= ($current->get('id') == $child->get('id'))?"selected":""; ?>">
                <img src="<?= $view->getTemplateWebPath('gfx/equerre16.png'); ?>" />
                <a href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $child->get('id'))); ?>">
                    <?= $child->fields['label']; ?>
                </a>
                <?php $child->display($view->getTemplatePath('clients/show/services_left_arbo.tpl.php')); ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}
?>