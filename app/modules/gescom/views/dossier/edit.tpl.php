<?php
$form = $this->get('form');
$this->partial($this->getTemplatePath('dossier/_edit_description.tpl.php'));

$form->build();
