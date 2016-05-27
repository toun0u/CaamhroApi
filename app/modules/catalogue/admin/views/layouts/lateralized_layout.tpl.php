<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<div id="lateral">
	<?php $this->yields('lateral'); ?>
</div>
<div id="main_content">
	<?= $this->partial($this->getTemplatePath('shared/_flash_board.tpl.php')); ?>
	<?php $this->yields('default'); ?>
</div>
