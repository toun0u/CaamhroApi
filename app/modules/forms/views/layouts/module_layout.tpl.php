<?php
$view = view::getInstance();
?>
<div class="module-form">
	<?php /*<div id="form-header">
		<?= $view->partial($view->getTemplatePath('layouts/_menu.tpl.php')); ?>
	</div> */ ?>
	<div id="form-main">
			<?php $view->partial($view->getTemplatePath('layouts/_flash.tpl.php')); ?>
			<?php $view->yields('default'); ?>
		</div>
	</div>
</div>
<p style="clear:both;"></p>
