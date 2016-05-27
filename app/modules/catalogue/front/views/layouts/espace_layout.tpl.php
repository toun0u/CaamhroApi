<?php
$view = view::getInstance();
$a = $view->get('a');
?>
<div class="navig">
	<ul class="onglets">
		<li class="<?= (empty($a) || $a == 'moncompte') ? 'active' : 'noactive'; ?>">
			<a href="<?= get_path('espace', 'moncompte'); ?>">
				Mon compte
			</a>
		</li>
		<li class="<?= (! isset($a) || $a == 'historique') ? 'active' : 'noactive'; ?>">
			<a href="<?= get_path('espace', 'historique'); ?>">
				Historique des achats
			</a>
		</li>
	</ul>
</div>
<div class="sub-navig">
	<?php $view->yields('default'); ?>
</div>
<div style="clear:both;"></div>