<?php
$view = view::getInstance();
?>
<h1>Mon compte</h1>
<?php
$form = new Dims\form(array(
	'name'			=> 'newcoords',
	'action'		=> get_path('espace', 'savecompte'),
	'object'		=> $view->get('client')
	));
echo $form->get_header();
echo $form->hidden_field(array('name' => 'id_client', 'value' => $view->get('client')->get('id_client')));
?>
<div class="ligne">
	<p>
		<label for="nom"><strong>Nom<span class="required">*</span></strong></label><br/>
		<?= $form->text_field(array('name' => 'nom', 'db_field' => 'nom', 'mandatory' => true)); ?>
	</p>
	<p>
		<label for="prenom"><strong>Prénom<span class="required">*</span></strong></label><br/>
		<?= $form->text_field(array('name' => 'prenom', 'db_field' => 'prenom', 'mandatory' => true)); ?>
	</p>
	<p>
		<label for="port"><strong>Téléphone mobile<span class="required">*</span></strong></label><br/>
		<?= $form->text_field(array('name' => 'port', 'db_field' => 'port', 'mandatory' => true)); ?>
	</p>
	<p>
		<label for="email"><strong>Adresse email<span class="required">*</span></strong></label><br/>
		<?= $form->text_field(array('name' => 'email', 'db_field' => 'email', 'mandatory' => true, 'revision' => 'email', 'additionnal_attributes' => 'autocomplete="off"')); ?>
	</p>
	<p>
		<label for="confirmemail"><strong>Confirmez votre adresse email<span class="required">*</span></strong></label><br/>
		<?= $form->text_field(array('name' => 'confirmemail', 'db_field' => 'email', 'mandatory' => true, 'revision' => 'email', 'additionnal_attributes' => 'autocomplete="off"')); ?>
	</p>
	<p>
		<label for="password"><strong>Mot de passe</strong></label><br/>
		<?= $form->password_field(array('name' => 'password', 'additionnal_attributes' => 'autocomplete="off"')); ?>
	</p>
	<p>
		<label for="passwordconfirm"><strong>Confirmez le nouveau mot de passe</strong></label><br/>
		<?= $form->password_field(array('name' => 'passwordconfirm',  'additionnal_attributes' => 'autocomplete="off"')); ?>
	</p>
	<p><?= $form->submit_field(array('value' => 'Modifier mes informations personnelles')); ?></p>
</div>
<?= $form->close_form(); ?>

