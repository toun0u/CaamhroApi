<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<?= $this->partial($this->getTemplatePath('shared/_flash_board.tpl.php')); ?>
<h1 class="blocked_header">
    <img src="<?= $this->getTemplateWebPath("/gfx/objets_visuels50x30.png"); ?>" />
    <?php echo dims_constant::getVal('CATA_WEB_OBJECTS'); ?>
</h1>

<div id="params_sidebar">
    <?php $this->yields('params_sidebar'); ?>
</div>

<div style="margin-left:205px">
    <?php $this->yields('default'); ?>
</div>