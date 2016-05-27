<?php
$view = view::getInstance();
$current = $view->get('current');
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');
$categ = $object;

?>
<div class="elem-index">
	<a href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'id_categ' => $categ['id'], 'cc' => 'documents', 'aa' => 'create_newfolder_categ', 'id_folder' => $current->get('id'))); ?>">
		<?= image_tag('preexist_folder32.png', array('title' => dims_constant::getVal('_ADD_FOLDER'))); ?>
		<br/><span><?= $categ['label']; ?></span>
	</a>
</div>
