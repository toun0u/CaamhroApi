<?php
$view = view::getInstance();
$nbTodos = $view->get('nbDocfiles');
$nbElem = $view->get('nbElem');
?>
<h1><span class="icon-file"></span>&nbsp;Derniers documents</h1>
<?php
$view->partial($view->getTemplatePath('documents/_list.tpl.php'));
if($nbElem > 0){
?>
<a href="<?= Gescom\get_path(array('c'=>'dashboard','a'=>"list-docfile")); ?>" data-tabable="true">Voir tous les documents</a>
<?php } ?>
