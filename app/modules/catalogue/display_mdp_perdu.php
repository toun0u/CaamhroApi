<?php
$email = dims_load_securvalue('email', dims_const::_DIMS_CHAR_INPUT, true, true);

$msg = 0;
$msg = dims_load_securvalue('msg',dims_const::_DIMS_NUM_INPUT, true, true, false,$msg);

$mdp_perdu = array('errno' => $msg, 'msg' => '');

switch($msg) {
	case 0:
		break;
	case 1:
		$mdp_perdu['msg'] = 'Veuillez renouveller votre demande.';
		break;
	case 2:
		$mdp_perdu['msg'] = 'Cette adresse e-mail n\'existe pas.';
		break;
	case 3:
		$mdp_perdu['msg'] = 'Les mots de passe renseignés ne correspondent pas';
		break;
}

if (!empty($email)) {
	$key = dims_load_securvalue('key', dims_const::_DIMS_CHAR_INPUT, true, true);
	$password1 = dims_load_securvalue('password1', dims_const::_DIMS_CHAR_INPUT, false, true);
	$password2 = dims_load_securvalue('password2', dims_const::_DIMS_CHAR_INPUT, false, true);

	// Recherche de l'utilisatreur
	if (empty($key)) {
		$rs = $db->query('SELECT id FROM dims_user WHERE email = \''.trim($email).'\' LIMIT 0, 1');
		if ($db->numrows($rs)) {
			$row = $db->fetchrow($rs);

			// Enregistrement de la demande de mot de passe
			include_once DIMS_APP_PATH.'/modules/catalogue/include/class_demande_pwd.php';
			$dmd_pwd = new demande_pwd();
			$dmd_pwd->open($email);
			$dmd_pwd->fields['id_user'] = $row['id'];
			$dmd_pwd->fields['email'] = $email;
			$dmd_pwd->fields['password'] = passgen();
			$dmd_pwd->fields['key'] = keygen();
			$dmd_pwd->fields['date_demand'] = dims_createtimestamp();
			$dmd_pwd->save();

			// Envoi du mail
			$from[0]['name'] = '';
			$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

			$to[0]['name'] = "";
			$to[0]['address'] = $email;

			$subject = "[".$_SESSION['dims']['currentworkspace']['label'] ."] Demande de mot de passe";
			$link = "http://{$_SERVER['HTTP_HOST']}/index.php?op=mdp_perdu&email=$email&key={$dmd_pwd->fields['key']}";
			$message = str_replace('<DMD_PWD_LINK>', $link, _MAIL_DMD_PWD_CONTENT);

			dims_send_mail($from, $to, $subject, $message);

			dims_redirect('/index.php?op=mdp_envoye');
		}
		// Utilisateur n'existe pas
		else {
			dims_redirect('/index.php?op=mdp_perdu&msg=2');
		}
	}
	// Comparaison de la clé fournie avec celle enregistrée
	elseif (empty($password1)) {
		include_once DIMS_APP_PATH.'/modules/catalogue/include/class_demande_pwd.php';
		$dmd_pwd = new demande_pwd();
		if ($dmd_pwd->open($email)) {
			if ($key == $dmd_pwd->fields['key']) {
				$smarty->assign('pwd_email', $email);
				$smarty->assign('pwd_key', $key);
				$smarty->assign('tpl_name', 'mdp_generation');
			}
			else {
				dims_redirect('/index.php?op=mdp_perdu&msg=1');
			}
		}
		else {
			dims_redirect('/index.php?op=mdp_perdu&msg=1');
		}
	}
	else {
		include_once DIMS_APP_PATH.'/modules/catalogue/include/class_demande_pwd.php';
		$dmd_pwd = new demande_pwd();
		if ($dmd_pwd->open($email)) {
			if ($key == $dmd_pwd->fields['key']) {
				if ($password1 == $password2) {
					$dims->getPasswordHash($password1, $hash, $saltuser);

					$user = new user();
					$user->open($dmd_pwd->fields['id_user']);
					$user->fields['salt'] = $saltuser;
					$user->fields['password'] = $hash;
					$user->save();

					$dmd_pwd->delete();
					dims_redirect('/index.php?op=mdp_confirmation');
				}
				else {
					dims_redirect('/index.php?op=mdp_perdu&email='.$email.'&key='.$key.'&msg=3');
				}
			}
			else {
				dims_redirect('/index.php?op=mdp_perdu&msg=1');
			}
		}
		else {
			dims_redirect('/index.php?op=mdp_perdu&msg=1');
		}
	}
}
else {
	$smarty->assign('tpl_name', 'mdp_perdu');
}

$smarty->assign('mdp_perdu',$mdp_perdu);
