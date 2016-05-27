<?php
$view = view::getInstance();
$client = $view->get('client');
$parent = $view->get('parent');
$current = $view->get('current');
?>
<div class="root <?= ($current->get('id') == $parent->get('id'))?"selected":""; ?>">
    <img src="<?= $view->getTemplateWebPath('gfx/tiers16.png'); ?>" />
    <a href="<?= get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')); ?>">
        <?= $parent->fields['label']; ?>
    </a>
</div>
<?php $parent->display($view->getTemplatePath('clients/show/services_left_arbo.tpl.php')); ?>