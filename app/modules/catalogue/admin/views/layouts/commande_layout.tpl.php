<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<div id="lateral">
	<?php $this->yields('lateral'); ?>
</div>
<div id="main_content">
	<?= $this->partial($this->getTemplatePath('shared/_flash_board.tpl.php')); ?>
	<a href="<?= get_path('commandes', 'index'); ?>" class="a_h1">
		<h1>
			<img src="<?= $this->getTemplateWebPath('gfx/commandes50x30.png'); ?>">
			<?= dims_constant::getVal('CATA_WEB_ORDERS'); ?>
		</h1>
	</a>
	<?php $this->yields('info_cde'); ?>
	<?php $this->partial($this->getTemplatePath('commandes/sub_menu.tpl.php')); ?>
	<?php $this->yields('sub_menu'); ?>
	<p style="clear:both;"></p>
</div>
