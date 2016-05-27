<?php
$view = view::getInstance();
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');
$uploaded = $view->get('uploaded');
$doc = $object;
?>
<div class="elem-index <?= (isset($uploaded) && $uploaded == $doc->get('id')) ? ' uploaded' : '';?> document" data-id="<?= $doc->get('id'); ?>">
	<a href="javascript:void(0);">
		<i class="icon-file doc-index"></i>
		<br/><span><?= $doc->get('name'); ?></span>
	</a>
</div>