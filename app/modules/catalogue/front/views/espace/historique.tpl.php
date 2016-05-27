<?php
$view = view::getInstance();
$commandes = $view->get('commandes');
?>
<h1>Historique de vos commandes</h1>
<?php
if( ! empty($commandes) ){
	?>
	<table>
		<tr>
			<th class="w100p">Numéro</th>
			<th>Date</th>
			<th>Total HT</th>
			<th>Total TVA</th>
			<th>Total TTC</th>
			<th class="w100p">Actions</th>
		</tr>
		<?php
		foreach($commandes as $id => $commande){
			?>
			<tr>
				<td><a href="<?= get_path('espace', 'commande', array('id_cde' => $commande->get('id_cde'))); ?>"><?= $commande->get('id_cde'); ?></a></td>
				<td>
					<?php
					$date = dims_timestamp2local($commande->get('date_cree'));
					echo $date['date'];
					?>
				</td>
				<td class="txtright"><?= catalogue_formateprix($commande->get('total_ht')) . ' €'; ?></td>
				<td class="txtright"><?= catalogue_formateprix($commande->get('total_tva')) . ' €'; ?></td>
				<td class="txtright"><?= catalogue_formateprix($commande->get('total_ttc')) . ' €'; ?></td>
				<td class="txtcenter"><a href="<?= get_path('espace', 'commande', array('id_cde' => $commande->get('id_cde'))); ?>">ouvrir</a></td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
}
else{
	?>
	<div class="no-elem">Aucune commande pour l'instant</div>
	<?php
}