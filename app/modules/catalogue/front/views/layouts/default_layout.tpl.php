<?php
$view = view::getInstance();
$view->partial($view->getTemplatePath('layouts/_flash.tpl.php'));
$view->yields();
?>