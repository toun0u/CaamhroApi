<?php
$view = view::getInstance();
?>
<h1>Connectez-vous</h1>
<?php
$form = new Dims\form(array(
'name'			=> 'loginform',
'action'		=> get_path('espace', 'connexion')
));
echo $form->get_header();
?>
<p>
	<label for="dims_login"><strong>Adresse email<span class="required">*</span></strong></label><br/>
	<?= $form->text_field(array('name' => 'dims_login', 'mandatory' => true)); ?>
</p>
<p>
	<label for="dims_password"><strong>Login<span class="required">*</span></strong></label><br/>
	<?= $form->password_field(array('name' => 'dims_password', 'mandatory' => true)); ?>
</p>
<p>
	<?= $form->submit_field(array('value' => 'Connexion')); ?>
</p>
<p><a href="<?= get_path('espace', 'inscription'); ?>">Nouveau client ?</a><a href="<?= get_path('espace', 'pwd_lost'); ?>">Mot de passe perdu ?</a></p>
<?= $form->close_form(); ?>