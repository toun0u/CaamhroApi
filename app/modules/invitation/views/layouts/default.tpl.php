<?php
$view = view::getInstance();
?>
<div class="right-content">
	<?php
	foreach($view->flushFlash() as $message){
	    ?>
	    <div class="flash <?= empty($message['class']) ? 'success' : $message['class'] ; ?>"><?= $message['message']; ?></div>
	    <?php
	}
	?>
	<h1 class="title_invitation">
		<?= $_SESSION['cste']['_INVITATIONS']; ?>
	</h1>
	<div class="header">
		<?php
		$view->yields('header');
		?>
	</div>
	<div class="content table_invitation">
		<?php
		$view->yields('default');
		?>
	</div>
</div>
