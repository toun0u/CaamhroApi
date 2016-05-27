<?php $view = view::getInstance(); ?>
<?php
$view->partial($view->getTemplatePath('shared/_lateral_search_block.tpl.php'));
?>
<h3 class="h3_underline"><?= dims_constant::getVal('_RECENT_CLIENTS_CONSULTED'); ?></h3>
<?php
$last_clients = $view->get('last_clients');
if( ! empty($last_clients) ){
?>
	<table>
		<?php
		foreach($last_clients as $cli_id){
			$client = new client();
			$client->open($cli_id);
			if( ! $client->isNew() ){
				?>
				<tr>
					<td>
						<?php
						$client->getLogo(50);
						?>
					</td>
					<td>
						<a href="<?= get_path('clients', 'show', array('id' => $client->get('id_client')));?>">
							<?= $client->getName(); ?>
						</a>
					</td>
				</tr>
				<?php
				}
			}
		?>
	</table>
<?php
}
else{
	?>
	<span class="no_elem"><?= dims_constant::getVal('_NO_CUSTOMER_CONSULTED'); ?></span>
	<?php
}
$view->partial($view->getTemplatePath('shared/_lateral_actions.tpl.php'));

?>