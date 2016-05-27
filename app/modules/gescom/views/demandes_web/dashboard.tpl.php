<?php
$view = view::getInstance();
$nbWebAsks = $view->get('nbWebAsks');
$nbElem = $view->get('nbElem');
?>
<h1><span class="icon-podcast"></span>&nbsp;Demandes web en attente de traitement (<?= $nbWebAsks; ?>)</h1>
<?php
$view->partial($view->getTemplatePath('demandes_web/_list.tpl.php'));
if($nbElem > 0){
?>
<a href="<?= Gescom\get_path(array('c'=>'dashboard','a'=>"list-web-ask")); ?>" data-tabable="true">Voir toutes les demandes</a>
<?php } ?>
