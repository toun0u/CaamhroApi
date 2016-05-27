<?php
$view = view::getInstance();
$obj = $view->get('obj');
if($obj->isNew()){
	?>
	<h2>
		<?= $_SESSION['cste']['_NEW_INVITATION']; ?>
	</h2>
	<div class="actions">
		<a href="<?= dims::getInstance()->getScriptEnv(); ?>?c=list&a=view"><img src="/common/modules/invitation/icon_back.png" /></a>
	</div>
	<?php
}else{
	?>
	<h2>
		<?= $_SESSION['cste']['_EDIT_INVITATION']; ?> : <?= $obj->get('libelle'); ?>
	</h2>
	<div class="actions">
		<a href="<?= dims::getInstance()->getScriptEnv(); ?>?c=obj&a=view&id=<?= $obj->get('id'); ?>"><img src="/common/modules/invitation/icon_back.png" /></a>
	</div>
	<?php
}
?>