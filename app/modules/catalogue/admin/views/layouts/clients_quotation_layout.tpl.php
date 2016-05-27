<?php $this->partial($this->getTemplatePath('layouts/_menu.tpl.php')); ?>
<div id="lateral">
	<?php $this->yields('lateral'); ?>
</div>
<div id="main_content">
	<?php $this->partial($this->getTemplatePath('clients/show/_current_client.tpl.php')); ?>
	<?php $this->partial($this->getTemplatePath('clients/show/_sub_menu.tpl.php')); ?>
	<?php $this->yields('default'); ?>
</div>
