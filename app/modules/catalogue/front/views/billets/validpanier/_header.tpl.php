<?php
$view = view::getInstance();
$step = $view->get('currentstep');
?>
<div class="line grid4 header-command">
	<div class="mod step <?= ($step == 1) ? ' current' : ''?>">
		<span class="biggest">1. </span>Récapitulatif
	</div>
	<div class="mod step <?= ($step == 2) ? ' current' : ''?>">
		<span class="biggest">2. </span>Coordonnées
	</div>
	<div class="mod step <?= ($step == 3) ? ' current' : ''?>">
		<span class="biggest">3. </span>Paiement
	</div>
	<div class="mod step <?= ($step == 4) ? ' current' : ''?>">
		<span class="biggest">4. </span>Tickets
	</div>
</div>