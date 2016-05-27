<?php
$view = view::getInstance();
$obj = $view->get('obj');
?>
<h2>
	<?= $_SESSION['cste']['_INVITATION']; ?> : <?= $obj->get('libelle'); ?>
	<a href="<?= dims::getInstance()->getScriptEnv()."?c=obj&a=edit&id=".$obj->get('id'); ?>"><img src="/common/modules/invitation/crayon16.png" /></a>
	<a href="<?= dims::getInstance()->getScriptEnv(); ?>?c=obj&a=param&id=<?= $obj->get('id'); ?>"><img src="/common/modules/invitation/shortcuts_params.png" /></a>
</h2>
<div class="actions">
	<a href="<?= dims::getInstance()->getScriptEnv(); ?>?c=list&a=view"><img src="/common/modules/invitation/icon_back.png" /></a>
</div>
