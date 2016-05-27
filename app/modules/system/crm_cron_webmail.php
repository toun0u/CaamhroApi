<?php
require_once DIMS_APP_PATH . '/modules/system/class_webmail_email.php';
require_once DIMS_APP_PATH . '/modules/system/class_webmail_inbox.php';

ini_set('max_execution_time',-1);
ini_set('memory_limit','512M');

$sql = 'SELECT id FROM dims_mod_webmail_inbox';

echo 'recherche de boites mails\n';ob_flush();
$ressource = $db->query($sql);

if($db->numrows($ressource) > 0) {
	echo '\tboites mails existantes\n';ob_flush();
	while($result = $db->fetchrow($ressource)) {
		echo '\t\tBoite #'.$result['id'].'\n';ob_flush();
		$inbox = null;

		$inbox = new webmail_inbox();
		$inbox->open($result['id']);
		echo '\t\t\tConnexion #'.$inbox->fields['id'].'\n';ob_flush();
		$inbox->connect();

		echo '\t\t\tMise a jour des mails #'.$inbox->fields['id'].'\n';ob_flush();
		$inbox->updateMailBox();
		echo '\t\t\tDeconnexion #'.$inbox->fields['id'].'\n';ob_flush();
		$inbox->disconnect();

		unset($inbox);
	}
}

$sql = 'SELECT * FROM dims_mod_webmail_email WHERE attached = 0';

echo 'recherche des emails non traites\n';ob_flush();
$ressource = $db->query($sql);

if($db->numrows($ressource) > 0) {
	echo '\tmails non traites existantes\n';ob_flush();
	while($result = $db->fetchrow($ressource)) {

		echo '\t\tMail #'.$result['id'].'\n';ob_flush();
		$mail = null;

		$mail = new webmail_email();
		$mail->open($result['id']);

		$champs = array();
		$addresses_to_attach = array();
		$adresses = array();

		if(!empty($mail->fields['from']))
			$champs[] = $mail->fields['from'];

		if(!empty($mail->fields['cc']))
			$champs[] = $mail->fields['cc'];

		if(!empty($mail->fields['to']))
			$champs[] = $mail->fields['to'];

		foreach($champs as $champ) {
			$adresses = explode(',', $champ);

			foreach($adresses as $address) {
				if(strstr($address, '<') === false) {
					$addresses_to_attach[$address] = $address;
				} else {
					$tab = explode('<',(str_replace('>','',$address)));
					$tab[1] = trim($tab[1]);
					$addresses_to_attach[$tab[1]] = $tab[1];
				}
			}
		}

		$sql = '';
		$sql_insert = '';

		$sql = 'SELECT id FROM dims_user WHERE 0 ';

		foreach($addresses_to_attach as $address) {
			$sql .= ' OR email like "'.$address.'"';
		}

		$ress = $db->query($sql);

		if($db->numrows($ress) > 0) {
			while($t = $db->fetchrow($ress)) {
				$sql_insert = 'INSERT INTO dims_mod_business_contact_mail VALUES ("", :result , :t );';
				$db->query($sql_insert, array(
					':result' 	=> $result['id'],
					':t' 		=> $t['id']
				));
			}
		}

		$mail->fields['attached'] = 1;
		$mail->save();

		unset($mail);
		echo '\t\tMail #'.$result['id'].' rattache\n';ob_flush();
	}
}

?>
