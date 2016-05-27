<?php
$view = view::getInstance();
$client = $view->get('client');
?>

<h1>
	<img src="<?= $view->getTemplateWebPath('gfx/clients50x30.png'); ?>">
	<?= dims_constant::getVal('CATA_CLIENTS'); ?>
</h1>

<table id="client_main_infos">
	<tr>
		<td id="client_logo">
			<?= $client->getLogo(80); ?>
		</td>
		<td>
			<table width="100%">
				<tr>
					<td id="client_title" colspan="2">
						<span class="client_name"><?= $client->getName(); ?></span> - <?= $client->getCode(); ?>
					</td>
				</tr>
				<tr>
					<td>
						<em>Créé le <?= $client->getDateCreation(); ?> par <?= $client->getUserCreation(); ?></em>
					</td>
					<?php
					if ($client->hasComment()) {
						?>
						<td>
							<div id="client_comment">
								<?= $client->getCommentHTML(); ?>
							</div>
						</td>
						<?php
					}
					?>
				</tr>
				<tr>
					<td colspan="2">
						Mot de passe initial : <span style="font-family: monospace; font-size: 1.2em;"><?= $client->getInitialPassword(); ?></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
