<?php
$view = view::getInstance();
$commande = $view->get('commande');
$lignes = $view->get('lignes');
$articles = $view->get('articles');
$date = dims_timestamp2local($commande->get('date_cree'));
?>
<h1>Commande n°<strong><?= $commande->get('id_cde'); ?></strong></h1>

<div class="line grid3-1">
	<div class="mod">
		<?php
		if(!empty($lignes)){
			?>
			<table>
				<tr>
					<th class="w100p"></th>
					<th>Article</th>
					<th  class="w100p">PU TTC</th>
					<th class="w100p">Qté</th>
					<th class="w100p">Total TTC</th>
				</tr>
				<?php
				foreach($lignes as $line){
					$art = $articles[$line->get('id_article')];
					$img = $art->getOriginal();
					if( ! empty($img) ) $src = $img->getwebpath();
					else $src = '/assets/images/frontoffice/zooparis/design/logo_zoo.png';
					?>
					<tr>
						<td class="txtcenter">
							<img src="<?= $src; ?>" title="<?= $art->get('label'); ?>" />
						</td>
						<td>
							<strong><?= $art->get('label'); ?></strong><br/>
							<div class="small"><?= $art->get('description'); ?></div>
						</td>
						<td>
							<?= catalogue_formateprix($line->get('pu_ttc')). ' €'; ?>
						</td>
						<td>
							<?= $line->get('qte'); ?>
						</td>
						<td>
							<?= catalogue_formateprix($line->get('qte') * $line->get('pu_ttc')). ' €'; ?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		?>
	</div>
	<div class="mod">
		<table class="no-border w200p">
			<tr><td class="txtright"><strong>Date :</strong></td><td class="txtright"><?= $date['date']; ?></td></tr>
			<tr><td class="txtright"><strong>Total HT :</strong></td><td class="txtright"><?= catalogue_formateprix($commande->get('total_ht')) . ' €'; ?></td></tr>
			<tr><td class="txtright"><strong>TVA :</strong></td><td class="txtright"><?= catalogue_formateprix($commande->get('total_tva')) . ' €'; ?></td></tr>
			<tr><td class="txtright"><strong>Total TTC :</strong></td><td class="txtright"><?= catalogue_formateprix($commande->get('total_ttc')) . ' €'; ?></td></tr>
			</tr>
		</table>
		<input type="submit" value="Télécharger les billets" onclick="javascript:document.location.href='<?= get_path('espace', 'generator', array('id_cde' => $commande->get('id_cde'))); ?>';"/>
	</div>
</div>
