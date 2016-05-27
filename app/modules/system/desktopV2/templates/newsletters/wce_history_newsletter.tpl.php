<div class="list-newsletter">
	<span>
		<?= $this->get('label'); ?>
		<?php
		$dd = dims_timestamp2local($this->get('date_envoi'));
		echo $dd['date']." ".$dd['time'];
		?>
	</span>
	<a href="javascript:void(0);">HTML</a>
	<a href="javascript:void(0);">PDF</a>
</div>