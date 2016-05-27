<h2>Moyens de paiement</h2>

<?php
$view = view::getInstance();
?>

<p class="information">
	<img src="<?= $this->getTemplateWebPath('gfx/info32.png'); ?>" alt="<?= dims_constant::getVal('_INFORMATION'); ?>" />
	Différents moyens de paiement vous sont mis à disposition. Cette interface vous permet de sélectionner et paramétrer ceux que vous souhaitez utiliser.<br/>
	Le panneau d'avertissement indique que certains paramètres sont manquants.
</p>

<table class="tableau">
	<tr>
		<td class="w20p title_tableau">
		</td>
		<td class="w20p title_tableau">
		</td>
		<td class="title_tableau">
			<?= dims_constant::getVal('_DIMS_LABEL'); ?>
		</td>
		<td class="w20p title_tableau">
			<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
		</td>
	</tr>
	<?php
	foreach ($view->get('a_mp') as $mp) {
		?>
		<tr>
			<td class="center">
				<?php
				if ($mp->isActive()) {
					?>
					<a href="<?= get_path('params', 'payment_mean_switch_active', array('id' => $mp->get('id'))); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>">
						<img src="<?= $this->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>" /></a>
					<?php
				}
				else {
					?>
					<a href="<?= get_path('params', 'payment_mean_switch_active', array('id' => $mp->get('id'))); ?>" title="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>">
						<img src="<?= $this->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>" /></a>
					<?php
				}
				?>
			</td>
			<td class="center">
				<!-- <img src="<?= $this->getTemplateWebPath('gfx/attention16.png'); ?>" alt="" /></a> -->
			</td>
			<td><?= $mp->getLabel(); ?></td>
			<td class="center">
				<a href="<?= get_path('params', 'payment_mean_edit', array('id' => $mp->get('id'))); ?>" title="<?= dims_constant::getVal('_MODIFY'); ?>">
					<img src="<?= $this->getTemplateWebPath('gfx/edit16.png'); ?>" alt="<?= dims_constant::getVal('_MODIFY'); ?>" /></a>
				<?php
				if ($mp->isActive()) {
					?>
					<a href="<?= get_path('params', 'payment_mean_switch_active', array('id' => $mp->get('id'))); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>">
						<img src="<?= $this->getTemplateWebPath('gfx/cadenas16.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_DISABLED'); ?>" /></a>
					<?php
				}
				else {
					?>
					<a href="<?= get_path('params', 'payment_mean_switch_active', array('id' => $mp->get('id'))); ?>" title="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>">
						<img src="<?= $this->getTemplateWebPath('gfx/cadenas16.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_ENABLED'); ?>" /></a>
					<?php
				}
				?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
