<?php
require_once DIMS_APP_PATH.'modules/system/class_newsletter.php';
require_once DIMS_APP_PATH.'modules/system/class_newsletter_inscription.php';

//Chargement de l'id
$id_nl = 0;

$id_nl = dims_load_securvalue('id_nl', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['dims']['newsletter']['id_nl']);

//Ouverture de l'objet
$newsletter = new newsletter();
$newsletter->open($id_nl);

echo '<div id="newsletter_front">';
if(!$newsletter->new && $newsletter->fields['etat'] == 1) {
	$erreur = array();

	echo '<div class="description">';
	echo '<h2>';
	echo $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER'].' : ';
	echo $newsletter->fields['label'];
	echo '</h2>';
	if($newsletter->fields['descriptif'] != '') echo '<p>'.$newsletter->fields['descriptif'].'</p>';
	echo '</div>';

	if(isset($_POST) && !empty($_POST) &&
	   isset($_SESSION['dims']['captcha']) && !empty($_SESSION['dims']['captcha'])) {

		$captcha = $_SESSION['dims']['captcha'];

		if($captcha == $_POST['captcha']) {

			if(!empty($_POST['nl_nom']) &&
			   !empty($_POST['nl_prenom']) &&
			   !empty($_POST['nl_email']) &&
			   !empty($_POST['nl_fonction']) &&
			   !empty($_POST['nl_entreprise']) &&
			   !empty($_POST['nl_pays']) &&
			   !empty($_POST['nl_tel']) &&
			   !empty($_POST['nl_adresse']) &&
			   !empty($_POST['nl_ville']) &&
			   !empty($_POST['nl_cp'])) {
				$inscription = new newsletter_inscription();
				$inscription->setvalues($_POST,'nl_');
				$inscription->fields['date_inscription'] = date('YmdHis');
				$inscription->save();

				echo '<p>';
				echo $_SESSION['cste']['_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'];
				echo '</p>';

				// envoi d'un email pour la personne qui demande

				$from	= array();
				$to		= array();
				$subject= '';
				$message= '';

				$work = new workspace();
				$work->open($_SESSION['dims']['workspaceid']);

				$to[0]['name']	   = dims_load_securvalue('nl_email', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$to[0]['address']  = dims_load_securvalue('nl_email', dims_const::_DIMS_CHAR_INPUT, true, true, true);

				$from[0]['name']   = $work->fields['email_noreply'];
				$from[0]['address']= $work->fields['email_noreply'];

				$subject = 'Register for an event.';
				$content = 'Dear '.$_POST['nl_nom'].' '.$_POST['nl_prenom'].",";
				$content .= '<br /><br />Thank you for your registration. Your email has now been added to our distribution list.';
				$content .= '<br /><br />Best regards,';

				$content.= '<br /><br />'.str_replace("\n","<br>",$work->fields['signature']);

				dims_send_mail($from, $to, $subject, $content);
			}
			else {
				if(empty($_POST['nl_nom']))
					$erreur[] = $_SESSION['cste']['_DIMS_FORM_MISSING_FIRSTNAME'];
				if(empty($_POST['nl_prenom']))
					$erreur[] = $_SESSION['cste']['_DIMS_FORM_MISSING_LASTNAME'];
				if(empty($_POST['nl_email']))
					$erreur[] = $_SESSION['cste']['_DIMS_FORM_MISSING_EMAIL'];
			}
		}
		else
			$erreur[] = $_SESSION['cste']['_DIMS_FORM_BAD_CAPTCHA'];
	}

	if(!isset($_POST) || empty($_POST) || !empty($erreur)) {

		echo '<p>';
		echo $_SESSION['cste']['_DIMS_FRONT_NL_TEXT_INSCRIPTION'];
		echo '</p>';

		if(!empty($erreur)) {
			foreach($erreur as $value) {
				echo '<div><font style="background-color:#F7D3D3;">'.$value.'.</div>';
			}
		}
		?>
		<div class="inscription">
			<form method="post" name="newsletter_subscribe">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("nl_id_newsletter",	$id_nl);
					$token->field("nl_nom");
					$token->field("nl_prenom");
					$token->field("nl_fonction");
					$token->field("nl_entreprise");
					$token->field("nl_email");
					$token->field("nl_tel");
					$token->field("nl_adresse");
					$token->field("nl_ville");
					$token->field("nl_cp");
					$token->field("nl_pays");
					$token->field("captcha");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<input type="hidden" name="nl_id_newsletter" value="<?php echo $id_nl; ?>" />
				<div class="bloc_info1">
					<p>
						<label for="nl_nom">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_nom" name="nl_nom" value="<?php if(isset($_POST['nl_nom'])) echo $_POST['nl_nom']; ?>" />
					</p>
					<p>
						<label for="nl_prenom">
						<?php echo $_SESSION['cste']['_FIRSTNAME']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_prenom" name="nl_prenom" value="<?php if(isset($_POST['nl_prenom'])) echo $_POST['nl_prenom']; ?>" />
					</p>
					  <p>
						<label for="nl_fonction">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>
						</label>
						<span class="red">*</span>
					</p>
					 <p>
						<input type="text" id="nl_fonction" name="nl_fonction" value="<?php if(isset($_POST['nl_fonction'])) echo $_POST['nl_fonction']; ?>" />
					</p>
					<p>
						<label for="nl_entreprise">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_entreprise" name="nl_entreprise" value="<?php if(isset($_POST['nl_entreprise'])) echo $_POST['nl_entreprise']; ?>" />
					</p>
					   <p>
						<label for="nl_email">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_email" name="nl_email" value="<?php if(isset($_POST['nl_email'])) echo $_POST['nl_email']; ?>" />
					</p>
				</div>
				<div class="bloc_info2">
					<p>
						<label for="nl_tel">
						<?php echo $_SESSION['cste']['_PHONE']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_tel" name="nl_tel" value="<?php if(isset($_POST['nl_tel'])) echo $_POST['nl_tel']; ?>" />
					</p>
					<p>
						<label for="nl_adresse">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_adresse" name="nl_adresse" value="<?php if(isset($_POST['nl_adresse'])) echo $_POST['nl_adresse']; ?>" />
					</p>
					<p>
						<label for="nl_ville">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_ville" name="nl_ville" value="<?php if(isset($_POST['nl_ville'])) echo $_POST['nl_ville']; ?>" />
					</p>
					<p>
						<label for="nl_cp">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_cp" name="nl_cp" value="<?php if(isset($_POST['nl_cp'])) echo $_POST['nl_cp']; ?>" />
					</p>
					<p>
						<label for="nl_pays">
						<?php echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?>
						</label>
						<span class="red">*</span>
					</p>
					<p>
						<input type="text" id="nl_pays" name="nl_pays" value="<?php if(isset($_POST['nl_pays'])) echo $_POST['nl_pays']; ?>" />
					</p>
				</div>
				<div class="captcha">
					<p>
						<?php echo $_SESSION['cste']['_DIMS_TEXT_CAPTCHA']; ?><br />
						<img src="./common/modules/system/cms_captcha.php?<?= mktime(); ?>" /><br />
						<input type="text" name="captcha" />
					</p>
				</div>
				<p style="clear: both;">
					<input class="submit" type="submit" value="<?php echo $_SESSION['cste']['_DIMS_REGISTER']; ?> >" /><br />
					<span class="red">*</span> <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?>
				</p>
			</form>
		</div>
		<?php
	}
}
else {
	echo '<div class="nonews">';
	echo $_SESSION['cste']['_DIMS_FRONT_NL_TEXT_NO_NEWSLETTER'];
	echo '</div>';
}
echo '</div>';
?>
