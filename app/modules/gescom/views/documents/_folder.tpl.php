<?php
$view = view::getInstance();
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');
$fold = $object;
?>
<div class="elem-index">
	<a href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'), 'cc' => 'documents', 'aa' => 'index', 'folder' => $fold->get('id'))); ?>">
		<i class="icon-folder doc-index"></i>
		<br/><span><?= $fold->get('name'); ?></span>
	</a>

</div>