<?php
$view = view::getInstance();
?>
<?php
$view->partial($view->getTemplatePath('billets/validpanier/_header.tpl.php'));
?>
<h1>Étape 2 : Renseignez vos coordonnées</h1>
<div>
	Toutes les informations relatives à votre commande vous seront transmises par email. Assurez vous que l'adresse que vous allez renseignées est bien fonctionnelle
</div>
<?php
if( ! $_SESSION['dims']['connected'] ){
	?>
	<div class="ligne ligneclient">
		<div class="bigger title_client">Déjà client ?</div>
		<?php
		$form = new Dims\form(array(
		'name'			=> 'loginform',
		'action'		=> get_path('billets', 'validpanier', array('step' => 3)),
		'object'		=> $view->get('client')
		));
		echo $form->get_header();
		?>
		<p class="styleformulaire">
			<label for="dims_login"><strong>Adresse email<span class="required">*</span></strong></label>
			<?= $form->text_field(array('name' => 'dims_login', 'mandatory' => true)); ?>
		</p>
		<p class="styleformulaire">
			<label for="dims_password"><strong>Mot de passe<span class="required">*</span></strong></label>
			<?= $form->password_field(array('name' => 'dims_password', 'mandatory' => true)); ?>
		</p>
		<p class="form_connexion">
			<a href="<?= get_path('billets', 'pwd_lost', array('from' => 'tunnel')); ?>">Mot de passe perdu ?</a> <?= $form->submit_field(array('value' => 'Connexion')); ?>
		</p>
		<?= $form->close_form(); ?>
	</div>
	<?php
	}
	$form2 = new Dims\form(array(
		'name'			=> 'newcoords',
		'action'		=> get_path('billets', 'validpanier', array('step' => 3)),
		'object'		=> $view->get('client')
		));
	echo $form2->get_header();
	?>

<div class="ligne lignenouveauclient">
	<div class="bigger title_client"><?= ( ! $_SESSION['dims']['connected'] ) ? 'Nouveau client ?' : 'Validez vos informations personnelles'; ?></div>
	<p class="styleformulaire2">
		<label for="nom"><strong>Nom<span class="required">*</span></strong></label>
		<?= $form2->text_field(array('name' => 'nom', 'db_field' => 'nom', 'mandatory' => true)); ?>
	</p>
	<p class="styleformulaire2">
		<label for="prenom"><strong>Prénom<span class="required">*</span></strong></label>
		<?= $form2->text_field(array('name' => 'prenom', 'db_field' => 'prenom', 'mandatory' => true)); ?>
	</p>
	<p class="styleformulaire2">
		<label for="port"><strong>Téléphone mobile<span class="required">*</span></strong></label>
		<?= $form2->text_field(array('name' => 'port', 'db_field' => 'port', 'mandatory' => true)); ?>
	</p>
	<p class="styleformulaire2">
		<label for="email"><strong>Adresse email<span class="required">*</span></strong></label>
		<?= $form2->text_field(array('name' => 'email', 'db_field' => 'email', 'mandatory' => true, 'revision' => 'email', 'additionnal_attributes' => 'autocomplete="off"')); ?>
	</p>
	<p class="styleformulaire2">
		<label for="confirmemail"><strong>Confirmez votre adresse email<span class="required">*</span></strong></label>
		<?= $form2->text_field(array('name' => 'confirmemail', 'db_field' => 'email', 'mandatory' => true, 'revision' => 'email', 'additionnal_attributes' => 'autocomplete="off"')); ?>
	</p>
</div>
<div class="line">
	<div class="left">
		<input type="button" value="&lt; Etape 1" onclick="javascript:document.location.href='<?= get_path('billets', 'validpanier', array('step' => 1));?>';"/>
	</div>
	<div class="right">
		<input type="submit" value="Etape 3 &gt;" />
	</div>
</div>
<?= $form2->close_form(); ?>