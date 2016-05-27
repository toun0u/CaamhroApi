<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<div>
	<?= $this->partial($this->getTemplatePath('shared/_flash_board.tpl.php')); ?>
	<?php $this->yields('default'); ?>
</div>
