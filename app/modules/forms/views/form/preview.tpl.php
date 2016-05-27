<?php
$view = view::getInstance();
$form = $view->get('form');
?>
<h1>
	<?= $_SESSION['cste']['_DIMS_LABEL_FORM']." : ".$form->get('label'); ?>
	<!--<a href="<?= form\get_path(array('c'=>'index')); ?>" class="btn btn-default btn-sm pull-right">
		<span class="glyphicon glyphicon-chevron-left"></span>
		<?= $_SESSION['cste']['_DIMS_LINK_BACK_LIST']; ?>
	</a>-->
</h1>
<?php
$view->partial($view->getTemplatePath('form/_preview.tpl.php'));
?>