<div>
	<?php
	$view = view::getInstance();
	foreach($view->flushFlash() as $message){
		?>
		<div class="form-validated <?= empty($message['class']) ? 'success' : $message['class'] ; ?>"><?= $message['message']; ?></div>
		<?php
	}
	?>
</div>