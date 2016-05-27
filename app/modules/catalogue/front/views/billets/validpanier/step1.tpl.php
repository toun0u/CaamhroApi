<?php
$view = view::getInstance();
$articles = $view->get("articles");
?>
<?php
$view->partial($view->getTemplatePath('billets/validpanier/_header.tpl.php'));
?>
<h1>Étape 1 : Récapitulatif de votre commande</h1>
<table>
	<tr>
		<th class="w100p"></th>
		<th>Article</th>
		<th>PU TTC</th>
		<th class="w100p">Qté</th>
		<th class="w100p">Total TTC</th>
	</tr>
	<?php
	foreach($articles as $art){
		?>
		<tr>
			<td class="txtcenter">
				<img src="<?= $art['photo_path']; ?>" title="<?= $art['label']; ?>" />
			</td>
			<td>
				<strong><?= $art['label']; ?></strong><br/>
				<div class="small"><?= $art['description']; ?></div>
			</td>
			<td>
				<?= $art['puttc']; ?>
			</td>
			<td>
				<?= $art['qte']; ?>
			</td>
			<td>
				<?= $art['totalttc']; ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<div class="line">
	<div>
		<table class="no-border w200p right">
			<tr><td class="txtright"><strong>Total HT :</strong></td><td class="txtright"><?= catalogue_formateprix($_SESSION['catalogue']['panier']['megatotalht']) . ' €'; ?></td></tr>
			<tr><td class="txtright"><strong>TVA :</strong></td><td class="txtright"><?= catalogue_formateprix($_SESSION['catalogue']['panier']['megatotalttc'] - $_SESSION['catalogue']['panier']['megatotalht']) . ' €'; ?></td></tr>
			<tr><td class="txtright"><strong>Total TTC :</strong></td><td class="txtright"><?= catalogue_formateprix($_SESSION['catalogue']['panier']['megatotalttc']) . ' €'; ?></td></tr>
			</tr>
		</table>
	</div>
</div>
<div class="line">
	<div class="left">
		<input type="button" value="&lt; Retourner à la billetterie" onclick="javascript:document.location.href='<?= get_path('billets', 'index');?>';"/>
	</div>
	<div class="right">
		<input type="submit" value="Etape 2 &gt;" onclick="javascript:document.location.href='<?= get_path('billets', 'validpanier', array('step' => 2));?>';"/>
	</div>
</div