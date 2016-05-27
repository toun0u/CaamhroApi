<?php
$view = view::getInstance();
$nbTodos = $view->get('nbTodos');
$nbElem = $view->get('nbElem');
$id_go = $view->get('id_go');
?>
<h1><span class="icon-foursquare"></span>&nbsp;Vos todos (<?= "$nbTodos"; ?>)</h1>
<?php
$view->partial($view->getTemplatePath('todos/_list.tpl.php'));
if($nbElem > 0 && empty($id_go)){
?>
<a href="<?= Gescom\get_path(array('c'=>'dashboard','a'=>"list-todos")); ?>" data-tabable="true">Voir tous vos todos</a>
<?php } ?>
