<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<?= $this->partial($this->getTemplatePath('shared/_flash_board.tpl.php')); ?>
<h1 class="blocked_header">
    <img src="<?php echo $this->getTemplateWebPath("/gfx/params30x20.png"); ?>" />
    <?php echo dims_constant::getVal('CATA_GENERAL_PARAMETERS'); ?>
</h1>

<div id="params_sidebar">
    <?php $this->yields('params_sidebar'); ?>
</div>

<div style="margin-left:205px">
    <?php $this->yields('default'); ?>
</div>