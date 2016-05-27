<?php
$view = view::getInstance();
$prestations = $view->get('prestations');
?>
<h1>Billetterie du Zoo</h1>
<?php
if(!empty($prestations)){
	if( isset($prestations['familles'])){
		foreach($prestations['familles'] as $fams){
			foreach($fams as $fam){
				$view->partial($view->getTemplatePath('billets/_billet.tpl.php'), $fam);
			}
		}
	}
	if( isset($prestations['articles'])){
		foreach($prestations['articles'] as $arts){
			foreach($arts as $art){
				$view->partial($view->getTemplatePath('billets/_billet.tpl.php'), $art);
			}
		}
	}
}
else{
	?>
	<div class="no-elem"><?= dims_constant::getVal('NO_PRESTATION'); ?></div>
	<?php
}
?>