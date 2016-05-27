<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<div id="lateral">
    <?php $this->yields('lateral'); ?>
</div>
<div id="main_content">
	<?php $this->partial($this->getTemplatePath('shared/_flash_board.tpl.php')); ?>
    <?php $this->partial($this->getTemplatePath('familles/famille_finder.tpl.php')); ?>
    <?php $this->partial($this->getTemplatePath('familles/famille_selected.tpl.php')); ?>
    <?php $this->partial($this->getTemplatePath('familles/sub_menu.tpl.php')); ?>
    <?php $this->yields('sub_menu'); ?>
    <p style="clear: both;"></p>
</div>
