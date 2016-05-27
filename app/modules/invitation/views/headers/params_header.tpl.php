<?php
$view = view::getInstance();
$obj = $view->get('obj');
?>
<h2>
	<?= $_SESSION['cste']['_INVITATION_SETTINGS']; ?> : <?= $obj->get('libelle'); ?>
</h2>
<div class="actions">
	<a href="<?= dims::getInstance()->getScriptEnv(); ?>?c=obj&a=view&id=<?= $obj->get('id'); ?>"><img src="/common/modules/invitation/icon_back.png" /></a>
</div>
