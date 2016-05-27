<?php
$form = $this->get('form');
$case = $this->get('case');

$form->addBlock('description',"Description du dossier");

$form->add_hidden_field(array(
	'name' => 'idcase',
	'db_field' => 'id',
	'block' => 'description',
));

$form->add_text_field(array(
	'name' => 'case_label',
	'db_field' => 'label',
	'label' => "Nom court",
	'mandatory' => true,
	'block' => 'description',
));

$form->add_text_field(array(
	'name' => 'case_long_label',
	'db_field' => 'long_label',
	'label' => "Libellé long",
	'mandatory' => true,
	'block' => 'description',
));

// responsable
$form->add_select_field(array(
	'name' => 'case_id_manager',
	'db_field' => 'id_manager',
	'label' => "Responsable",
	'options' => $this->get('managers'),
	'mandatory' => true,
	'block' => 'description',
));

// workflow
$form->add_select_field(array(
	'name' => 'case_id_workflow',
	'db_field' => 'id_workflow',
	'label' => "Procédure",
	'options' => $this->get('workflows'),
	'block' => 'description',
	'mandatory' => true,
));

$form->add_textarea_field(array(
	'name' => 'case_description',
	'db_field' => 'description',
	'label' => 'Description',
	'classes' => 'w100',
	'block' => 'description',
));
