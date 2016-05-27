<?php
$view = view::getInstance();

$view->partial($view->getTemplatePath('demandes_web/dashboard.tpl.php'));
$view->partial($view->getTemplatePath('todos/dashboard.tpl.php'));
//$view->partial($view->getTemplatePath('discussions/dashboard.tpl.php'));
$view->partial($view->getTemplatePath('documents/dashboard.tpl.php'));

