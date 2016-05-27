<?php
$view = view::getInstance();
?>
<?php
$view->partial($view->getTemplatePath('billets/validpanier/_header.tpl.php'));
?>
<h1>Étape 3 : Paiement</h1>
<div class="line">
	<div class="bigger">Rappel du montant de la transaction :</div>
	<table class="no-border w200p">
		<tr><td class="txtright"><strong>Total HT :</strong></td><td class="txtright"><?= catalogue_formateprix($_SESSION['catalogue']['panier']['megatotalht']) . ' €'; ?></td></tr>
		<tr><td class="txtright"><strong>TVA :</strong></td><td class="txtright"><?= catalogue_formateprix($_SESSION['catalogue']['panier']['megatotalttc'] - $_SESSION['catalogue']['panier']['megatotalht']) . ' €'; ?></td></tr>
		<tr><td class="txtright"><strong>Total TTC :</strong></td><td class="txtright"><?= catalogue_formateprix($_SESSION['catalogue']['panier']['megatotalttc']) . ' €'; ?></td></tr>
		</tr>
	</table>
</div>
<div class="line">
	<div class="bigger">Sélectionnez votre moyen de paiement <strong class="demo">[mode démo]</strong> :</div>
	<p class="modpaiement"><input type="radio" name="modpaiement" value="cb" id="cb" checked="checked"/><label for="cb"><?= image_tag('logocb.jpg'); ?>Carte bleue</label></p>
	<p class="modpaiement"><input type="radio" name="modpaiement" value="paypal" id="paypal"/><label for="paypal"><?= image_tag('paypal_logo.png'); ?>Paypal</label></p>
</div>

<div class="line">
	<div class="left">
		<input type="button" value="&lt; Etape 2" onclick="javascript:document.location.href='<?= get_path('billets', 'validpanier', array('step' => 2));?>';"/>
	</div>
	<div class="right">
		<input type="submit" value="Etape 4 &gt;" onclick="javascript:document.location.href='<?= get_path('billets', 'validpanier', array('step' => 4));?>';"/>
	</div>
</div
