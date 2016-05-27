<?php
if ($this->get('a') == "show") {
	$cde = $this->get('commande');
	?>
	<div class="sub_menu" style="clear:both;">
		<a href="<?= get_path('commandes', 'show', array('ca' => 'detail', 'id' => $cde->get('id_cde'))); ?>" <?= ($this->get('ca') == 'detail') ? 'class="selected"' : '';?>>
			<div><?php echo dims_constant::getVal('_ORDER_DETAIL'); ?></div>
		</a>
		<a href="<?= get_path('commandes', 'show', array('ca' => 'livraison', 'id' => $cde->get('id_cde'))); ?>" <?= ($this->get('ca') == 'livraison') ? 'class="selected"' : '';?>>
			<div><?php echo dims_constant::getVal('_DELIVERY'); ?></div>
		</a>
	</div>
	<?php
}
