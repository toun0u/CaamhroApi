<?php
$view = view::getInstance();
?>
<div class="main">

	<?php $view->partial($view->getTemplatePath('layouts/main_menu.tpl.php'));?>
	<?php $view->partial($view->getTemplatePath('layouts/_flash_board.tpl.php'));?>

	<!-- mod telephony -->
	<aside class="pam mod right w250p mls aside phone-hidden">
		<?php $view->partial($view->getTemplatePath('layouts/telephony_layout.tpl.php'));?>
	</aside>

	<div id="main" role="main" class="mod pam">
		<?php $view->yields('default'); ?>
	</div>

</div>

