<div class="flash">
<?php
$view = view::getInstance();
foreach($view->flushFlash() as $message) {
	?>
	<div class="<?= empty($message['class']) ? 'success' : $message['class'] ; ?>"><?= $message['message']; ?></div>
	<?php
}
?>
</div>
