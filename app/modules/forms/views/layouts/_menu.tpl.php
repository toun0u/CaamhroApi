<?php
$view = view::getInstance();
?>
<div class="heading">
	<div class="item left txtcenter <?= $view->get('c') == 'events' ? 'active' : ''; ?>">
		<a href="<?= form\get_path('events', 'index'); ?>">
			<img src="<?= view::getInstance()->getTemplateWebPath('gfx/calendar.png'); ?>" class="icon" /> <br>
			<?= dims_constant::getVal('_DIMS_LABEL_EVT_MANAGEMENT'); ?>
		</a>
	</div>
	<div class="item left txtcenter <?= $view->get('c') == 'params' ? 'active' : ''; ?>">
		<a href="<?= form\get_path('params', ''); ?>">
			<img src="<?= view::getInstance()->getTemplateWebPath('gfx/shield.png'); ?>" class="icon" /> <br>
			<?= dims_constant::getVal('_PARAMETERS'); ?>
		</a>
	</div>
	<div class="item left txtcenter <?= $view->get('c') == 'ventes' ? 'active' : ''; ?>">
		<a href="<?= form\get_path('ventes', ''); ?>">
			<img src="<?= view::getInstance()->getTemplateWebPath('gfx/dollar.png'); ?>" class="icon" /> <br>
			Ventes
		</a>
	</div>
	<div class="item left txtcenter <?= $view->get('c') == 'partners' ? 'active' : ''; ?>">
		<a href="<?= form\get_path('partners', ''); ?>">
			<img src="<?= view::getInstance()->getTemplateWebPath('gfx/user.png'); ?>" class="icon" /> <br>
			Partenaires
		</a>
	</div>
	<div class="line"></div>
</div>