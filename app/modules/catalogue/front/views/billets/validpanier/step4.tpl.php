<?php
$view = view::getInstance();
$articles = $view->get('articles');
$id_cde=$view->get('id_cde');
?>

<h1>Étape 4 : Édition des billets</h1>

<div class="line">
	<div class="bigger demo">Merci pour votre confiance. Vous pouvez désormais télécharger vos billets</div>
	<div>
		Vous allez recevoir votre confirmation de commande par email dans les minutes à venir. Les billets seront également intégrés en pièces jointes à cet email.
	</div>

	<input type="button" value="Téléchargez vos billets" onclick="javascript:document.location.href='<?= get_path('billets', 'generator', array('id_cde' => $id_cde)); ?>';"/>

</div>
