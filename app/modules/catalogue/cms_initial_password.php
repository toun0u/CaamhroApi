<?php

if (empty($_POST)) {
	// Affichage du formulaire
	?>
	<h2 style="color:#DC5B40;font-family:helvetica;font-size:18px;">
		Je suis déjà client Caahmro et je souhaite recevoir mes identifiants
	</h2>

	<form name="f_initial_password" action="/index.php" method="post">
		<div class="form_row">
			<span class="label">
				<label class="col-sm-2 control-label" for="client_code">
					Mon n° de client est : *
				</label>
			</span>
			<span class="field">
				<input id="client_code" name="client_code" class="form-control" type="text" rel="requis" placeholder="Ex : 000999 votre n° de client à 6 chiffres">
			</span>
		</div>
		<div class="form_row">
			<span class="label">
				<label class="col-sm-2 control-label" for="mail_address">
					Mon adresse mail est : *
				</label>
			</span>
			<span class="field">
				<input id="mail_address" name="mail_address" class="form-control" type="text" rel="requis">
			</span>
		</div>
		<div class="txtright">
			<button class="btn btn-primary" type="submit">Valider</button>
		</div>
	</form>
	<?php
}
else {
	dims_print_r($_POST);
}
