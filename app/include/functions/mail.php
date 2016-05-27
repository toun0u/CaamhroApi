<?php

// from : Array('name','address')
// to : Array('name','address')
// cc : Array('name','address')
// bcc : Array('name','address')
function dims_send_mail($from, $to, $subject, $html_msg, $cc = null, $bcc = null) {
	if (is_array($from)) {
		$str_from = '';
		foreach ($from as $from_detail) {
			if(empty($from_detail['name'])) $from_detail['name'] = $from_detail['address'];
			if ($str_from != '') $str_from .= ', ';
			if (_DIMS_ENCODING=="UTF-8") {
				$str_from .='=?UTF-8?B?'.base64_encode($from_detail['name'])."?= <{$from_detail['address']}>";
			}
			else {
				$str_from .= "{$from_detail['name']} <{$from_detail['address']}>";
			}
		}
	}
	else {
		$str_from = $from;
	}

	if (is_array($to)) {
		$str_to = '';
		foreach ($to as $to_detail) {
			if(empty($to_detail['name'])) $to_detail['name'] = $to_detail['address'];
			if ($str_to != '') $str_to .= ', ';
			$str_to .= "{$to_detail['name']} <{$to_detail['address']}>";
		}
	}
	else {
		$str_to = $to;
	}

	if (isset($cc) && is_array($cc)) {
		$str_cc = '';
		foreach ($cc as $cc_detail) {
			if(empty($cc_detail['name'])) $cc_detail['name'] = $cc_detail['address'];
			if ($str_cc != '') $str_cc .= ', ';
			if (_DIMS_ENCODING=="UTF-8") {
				$str_cc .='=?UTF-8?B?'.base64_encode($cc_detail['name'])."?= <{$cc_detail['address']}>";
			}
			else {
				$str_cc .= "{$cc_detail['name']} <{$cc_detail['address']}>";
			}
		}
	}

	if (isset($bcc) && is_array($bcc)) {
		$str_bcc = '';
		foreach ($bcc as $bcc_detail) {
			if(empty($bcc_detail['name'])) $bcc_detail['name'] = $bcc_detail['address'];
			if ($str_bcc != '') $str_bcc .= ', ';
			if (_DIMS_ENCODING=="UTF-8") {
				$str_cc .='=?UTF-8?B?'.base64_encode($bcc_detail['name'])."?= <{$bcc_detail['address']}>";
			}
			else {
				$str_bcc .= "{$bcc_detail['name']} <{$bcc_detail['address']}>";
			}
		}
	}

	# MAIL
	$eol = "\n";
	$boundary1 = md5(uniqid(rand()));
	$boundary2 = md5(uniqid(rand()));

	# CONTENTS
	// remplacement des simples quotes de MS OFFICE par des simples quotes basiques
	$text_msg = str_replace(
		'&rsquo;',
		"'",
		html_entity_decode(
			strip_tags(
				str_replace(
					array('<br>', '<br >', '<br />', '<br/>'),
					PHP_EOL,
					preg_replace(
						"#<script.*/script>#is",
						"",
						preg_replace(
							"#<style.*/style>#is",
							"",
							$html_msg
						)
					)
				)
			),
			ENT_QUOTES
		)
	);

	# COMMON HEADERS
	$headers = 'Date: '.date("r").$eol;
	// add "from" to headers
	if (isset($str_from)) $headers  .= 'From: '.$str_from.$eol;

	// add "cc" to headers
	if (isset($str_cc)) $headers    .= 'Cc: '.$str_cc.$eol;

	// add "bcc" to headers
	if (isset($str_bcc)) $headers   .= 'Bcc: '.$str_bcc.$eol;

	$headers .= 'Return-Path: '.$str_from.$eol; // these two to set reply address
	$headers .= 'Message-ID: <system'.uniqid().'@'.$_SERVER['SERVER_NAME'].'>'.$eol;
	$headers .= 'X-Mailer: PHP v'.phpversion().$eol; // These two to help avoid spam-filters

	# BOUNDRY FOR MARKING THE SPLIT & MULTITYPE HEADERS
	$headers .= 'MIME-Version: 1.0'.$eol;
	$headers .= 'Content-Type: multipart/alternative; boundary="'.$boundary1.'"'.$eol.$eol;

	$msg = '--'.$boundary1.$eol;
	$msg .= 'Content-Type: text/plain; charset=UTF-8'.$eol.$eol;

	$msg .= $text_msg.$eol.$eol;

	$msg .= '--'.$boundary1.$eol;
	$msg .= 'Content-Type: multipart/related; boundary="'.$boundary2.'"'.$eol.$eol;

	$msg .= '--'.$boundary2.$eol;
	$msg .= 'Content-Type: text/html; charset=UTF-8; format=flowed'.$eol.$eol;

	$msg .= $html_msg.$eol.$eol;


	$msg .= '--'.$boundary2.'--'.$eol.$eol;

	$msg .= '--'.$boundary1.'--';

	if (defined('_DIMS_DEBUGMODE') && _DIMS_DEBUGMODE && defined('_DEBUG_EMAIL_ADDRESS') && _DEBUG_EMAIL_ADDRESS != '') {
		$subject .= " (Destinataire original: $str_to)";
		$str_to = _DEBUG_EMAIL_ADDRESS;
	}

	if (_DIMS_ENCODING=="UTF-8") {
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
	}

	// send mail
	return mail($str_to, $subject, $msg, $headers);
}

function dims_form2html($form)
{
	$content = '';

	foreach($form as $field => $value)
	{
		if (is_array($value))
		{
			$content.=	"
					<tr bgcolor='#ffffff'>
						<td align='left'><b>$field</b></td>
						<td align='left' valign='top'>
						<table cellpadding='3' cellspacing='1' bgcolor='#000000'>".dims_form2html($value)."</table>
						</td>
					</tr>
					";
		}
		else
		{
			$content.=	"
					<tr bgcolor='#ffffff'>
						<td align='left' valign='top'><b>$field</b></td>
						<td align='left'>$value</td>
					</tr>
					";
		}
	}

	return($content);
}

function dims_send_form($from, $to, $subject, $form, $cc = null, $bcc = null)
{

	// form['field'] = value

	$content = dims_form2html($form);

	/* message */
	$message =	"
			<html>
			<head>
			<title>$subject</title>
			</head>
			<body>
			<table cellpadding='3' cellspacing='1' bgcolor='#000000'>
			$content
			</table>
			</body>
			</html>
			";

	return(dims_send_mail($from, $to, $subject, $message, $cc = null, $bcc = null));
}


function dims_checkdns($hostname, $rectype = '') {
	// Disabled for dev use
	if (_DIMS_DEBUGMODE) {
		return true;
	}

	if(!empty($hostname)) {
		if( $rectype == '' ) $recType = "MX";

		exec(escapeshellcmd("nslookup -type=".escapeshellarg($rectype)." ".escapeshellarg($hostname)), $result);

		// check each line to find the one that starts with the host
		// name. If it exists then the function succeeded.
		foreach ($result as $line) {
			if(eregi("^$hostname",$line)) {
				return true;
			}
		}
		// otherwise there was no mail handler for the domain
		return false;
	}

	return false;
}

function dims_verifyemail($email)
{
	// check @ in email
	$array = explode("@", $email);
	if (sizeof($array) != 2) return false;

	$maildomain = $array[1];

	// check . in maildomain
	if (!strstr($maildomain, ".")) return false;

	// check dns
	if (!dims_checkdns($maildomain, "MX")) return false;

	return true;
}

/**
* PHP "send_mail_with_files" Mail Sender
*
* @param string from adresse expediteur
* @param string to adresse destinataire
* @param string subject sujet du mail
* @param string message message du mail (HTML)
* @param array	files tableau a  2 dimensions :<br/>
*				array[index]['name'] => Nom du fichier qui apparaitra dans le mail<br/><br/>
*				array[index]['filename'] => adresse absolu ou relative ou lien de la ged<br/><br/>
*				array[index]['mime-type'] => Type mime du fichier (ex: image/png)<br/><br/>
*				array[index]['content'] => Contenu du fichier (Dans le cas de generation de fichier a la volee -> filename devient inutile dans ce cas
* @param string cc champ CC (copie a )
* @param string bcc champ CCI (copie cachee a )
* @return bool return renvois false ou true si le mail est transmis au serveur
*
* @version 0.1
* @since 0.1
*
* @category mailing / mail sender
*/
function dims_send_mail_with_files($from, $to, $subject, $message, $files, $cc = null, $bcc = null, $eol = "\n") {
	// from : Array('name','address')
	// to : Array('name','address')
	// cc : Array('name','address')
	// bcc : Array('name','address')
	// files : Array('name','filename','mime-type')

	if (is_array($to)) {
		$str_to = '';
		foreach($to as $to_detail) {
			if ($str_to != '') $str_to .= ', ';
			$str_to .= "{$to_detail['name']} <{$to_detail['address']}>";
		}
	}
	else {
		$str_to = $to;
	}

	if (is_array($from)) {
		$str_from = '';
		foreach($from as $from_detail) {
			if ($str_from != '') $str_from .= ', ';
			if (_DIMS_ENCODING=="UTF-8") {
				$str_from .='=?UTF-8?B?'.base64_encode($from_detail['name'])."?= <{$from_detail['address']}>";
			}
			else {
				$str_from .= "{$from_detail['name']} <{$from_detail['address']}>";
			}
		}
	}
	else {
		$str_from = $from;
	}

	if (isset($cc) && is_array($cc)) {
		$str_cc = '';
		foreach($cc as $cc_detail) {
			if ($str_cc != '') $str_cc .= ', ';
			if (_DIMS_ENCODING=="UTF-8") {
				$str_cc .='=?UTF-8?B?'.base64_encode($cc_detail['name'])."?= <{$cc_detail['address']}>";
			}
			else {
				$str_cc .= "{$cc_detail['name']} <{$cc_detail['address']}>";
			}
		}
	}

	if (isset($bcc) && is_array($bcc)) {
		$str_bcc = '';
		foreach($bcc as $bcc_detail) {
			if (_DIMS_ENCODING=="UTF-8") {
				$str_cc .='=?UTF-8?B?'.base64_encode($bcc_detail['name'])."?= <{$bcc_detail['address']}>";
			}
			else {
				$str_bcc .= "{$bcc_detail['name']} <{$bcc_detail['address']}>";
			}
		}
	}

	//----------------------------------
	// Construction de l'ent?te
	//----------------------------------
	// On choisi generalement de construire une frontiere generee aleatoirement
	// comme suit. (le document pourra ainsi etre attache dans un autre mail
	// dans le cas d'un transfert par exemple)
	$boundary_mixed = md5(uniqid(rand()));

	// Ici, on construit un entete contenant les informations
	// minimales requises.
	// Version du format MIME utilise
	$headers = 'MIME-Version: 1.0'.$eol;
	if (isset($str_from)) $headers .= 'From: '.$str_from.$eol;
	if (isset($str_from)) $headers .= 'Reply-To: '.$str_from.$eol;

	// add 'cc' to headers
	if (isset($str_cc)) $headers .= 'Cc: '.$str_cc.$eol;

	// add 'bcc' to headers
	if (isset($str_bcc)) $headers .= 'Bcc: '.$str_bcc.$eol;

	$headers .= 'Content-Type: multipart/mixed;'.$eol.' boundary="'.$boundary_mixed.'"'.$eol;
	$text = 'This is a multi-part message in MIME format'.$eol;
	$text .= '--'.$boundary_mixed.$eol;

	// Type de contenu. Ici plusieurs parties de type different 'multipart/mixed'
	// Avec un frontiere definie par $boundary
	$boundary_alternative = md5(uniqid(rand()));

	$text .= 'Content-Type: multipart/alternative;'.$eol.' boundary="'.$boundary_alternative.'"'.$eol;
	$text .= $eol;
	$text .= '--'.$boundary_alternative.$eol;
	if (_DIMS_ENCODING=="UTF-8")
		$text .= 'Content-Transfer-Encoding: 8bit'.$eol;
	else
		$text .= 'Content-Transfer-Encoding: 7bit'.$eol;

	$text .= 'Content-Type: text/plain; charset="'._DIMS_ENCODING.'"'.$eol;


	$text .= $eol;
	$text .= strip_tags(str_replace(array('<br />', '<br>'),$eol, $message)).$eol;
	$text .= $eol;
	$text .= '--'.$boundary_alternative.$eol;

	// on traite maintenant des images qui sont dans le corps du texte
	foreach($files as $k=> $file) {
		$files[$k]['contentid']='';

		// on traite l'image
		if ($file['type']=='image') {
			// on va générer un id de content pour l'image et on remplace la signature par cid:REFERENCE
			$boundary_contentid = md5(uniqid(rand()));
			$files[$k]['contentid']=$boundary_contentid;
			$message=str_replace($file['name'],'cid:'.$boundary_contentid,$message);
		}
	}

	$boundary_related = md5(uniqid(rand()));
	$text .= 'Content-type: multipart/related;'.$eol.' boundary="'.$boundary_related.'"'.$eol;
	$text .= $eol;
	$text .= '--'.$boundary_related.$eol;

	// Et pour chaque partie on en indique le type
	$text .= 'Content-type: text/html; charset='._DIMS_ENCODING.$eol;

	$text .= 'Content-Transfer-Encoding:quoted-printable '.$eol.$eol;
	$message = $text.$message.$eol; //Message version html
	$message .= $eol;

	//Fichier faisait partie du multipart/related (lié a la version html)
	foreach($files as $k=> $file) {
		if (!empty($file['contentid'])) {
			// Tout d'abord lire le contenu des fichiers
			if(isset($file['content']) && !empty($file['content']))
				$attachment = $file['content'];
			else
				$attachment = file_get_contents($file['filename']);

			// puis convertir le contenu du fichier en une chaine de caractere
			// certe totalement illisible mais sans caracteres exotiques
			// et avec des retours a la ligne tout les 76 caract?res
			// pour etre conforme au format RFC 2045
			$attachment = chunk_split(base64_encode($attachment),72);

			// Ne pas oublier que chaque partie du message est separe par une frontiere
			$message .= '--'.$boundary_related.$eol;

			$message .= 'Content-Transfer-Encoding: base64'.$eol;
			// on indique le content-ID
			$message .= 'Content-ID: <'.$file['contentid'].'>'.$eol;

			// Et pour chaque partie on en indique le type
			$message .= 'Content-Type: '.$file['mime-type'].';'.$eol.' name="'.$file['name'].'";'.$eol;

			// Petit plus pour les fichiers joints
			// Il est possible de demander a ce que le fichier
			// soit si possible affiche dans le corps du mail
			$message .= 'Content-Disposition: inline; filename="'.$file['name'].'";'.$eol;

			// Il est indispensable d'introduire une ligne vide entre l'entete et le texte
			$message .= $eol;
			// C'est ici que l'on insere le code du fichier lu
			$message .= $attachment;
		}
	}

	//Fin de partie : boundary + '--'
	$message .= '--'.$boundary_related.'--'.$eol;
	$message .= $eol;

	//Fin de partie : boundary + '--'
	$message .= '--'.$boundary_alternative.'--'.$eol;
	$message .= $eol;

	foreach($files as $k=> $file) {
		if (empty($file['contentid'])) {
			// Tout d'abord lire le contenu des fichiers
			if(isset($file['content']) && !empty($file['content']))
				$attachment = $file['content'];
			else
				$attachment = file_get_contents($file['filename']);

			// puis convertir le contenu du fichier en une chaine de caractere
			// certe totalement illisible mais sans caracteres exotiques
			// et avec des retours a la ligne tout les 76 caract?res
			// pour etre conforme au format RFC 2045
			$attachment = chunk_split(base64_encode($attachment),72);

			// Ne pas oublier que chaque partie du message est separe par une frontiere
			$message .= '--'.$boundary_mixed.$eol;

			$message .= 'Content-Transfer-Encoding: base64'.$eol;

			// Et pour chaque partie on en indique le type
			$message .= 'Content-Type: '.$file['mime-type'].';'.$eol.' name="'.$file['name'].'";'.$eol;

			//C'est un attachment, car non rattache au corps
			$message .= 'Content-Disposition: attachment; filename="'.$file['name'].'";'.$eol;

			// Il est indispensable d'introduire une ligne vide entre l'entete et le texte
			$message .= $eol;
			// C'est ici que l'on insere le code du fichier lu
			$message .= $attachment;
		}
	}

	//Fin de partie : boundary + '--'
	$message .= '--'.$boundary_mixed.'--'.$eol;

	if (_DIMS_ENCODING=="UTF-8") {
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
	}

	return mail($str_to, $subject, $message, $headers);
}


function dims_send_mail_with_pear($from, $to, $subject, $html_content, $files = array(), $text_content = "", $cc = null, $bcc = null, $eol = "\n"){
	require_once 'Mail.php';
	require_once 'Mail/mime.php';

	$mime = new Mail_mime(array('eol' => $eol));


	$headers = array();

	if (is_array($from) && isset($from[0])) {
		$headers['from'] = $from[0]['name']." <".$from[0]['address'].">";
	}
	else {
		$headers['from'] = $from;
	}

	if (isset($_SESSION['dims']['user']['email']))
		$headers['reply-to'] =$_SESSION['dims']['user']['email'];
	else
		$headers['reply-to'] = $headers['from'];

	if (is_array($to)) {
		$str_to = '';
		foreach ($to as $to_detail) {
			if(empty($to_detail['name'])) $to_detail['name'] = $to_detail['address'];
			if ($str_to != '') $str_to .= ', ';
			$str_to .= "{$to_detail['name']} <{$to_detail['address']}>";
		}
	}
	else {
		$str_to = $to;
	}

	$types = array("src");
	while (list(, $type) = each($types)) {
			$innerT = ( isset($strict) && $strict ) ? '[a-z0-9:?=&@/._-]+?' : '.+?';
			preg_match_all("|" . $type . "[\=\(]+([\"'`])(" . $innerT . ")\\1|i", $html_content, $matches);
			$ret[$type] = $matches[2];
	}

	$fileto=array();
	if (isset($ret['src'])) {
		foreach ($ret['src'] as $elem) {
			$elem = dims::getInstance()->getPathAfterDomain($elem);
			if(substr(realpath('.'), -1, 1) != '/') $elem = '/'.$elem;
			$file=DIMS_ROOT_PATH.'www/'.$elem;
			$name=substr(strrchr($file, "/"),1);
			$ref=$mime->addHTMLImage($file, mime_content_type($file), $file['filename'], true);
			$cid=$mime->_html_images[count($mime->_html_images)-1]['cid'];

			// on remplace le code par celui complet pour effectuer le remplacement
			$fileto[]="cid:".$cid;
		}
		$html_content =  str_replace($ret['src'], $fileto, $html_content);
	}

	if (defined('_DIMS_DEBUGMODE') && _DIMS_DEBUGMODE && defined('_DEBUG_EMAIL_ADDRESS') && _DEBUG_EMAIL_ADDRESS != '') {
		$subject .= " (Destinataire original: $str_to)";
		$str_to = _DEBUG_EMAIL_ADDRESS;
	}

	if ($text_content=='') {
		$text_content=html_entity_decode($html_content);
		/*require_once DIMS_APP_PATH.'include/class_inputfilter.php';
		$ifilter = new InputFilter('','', 0, 0,0);
		$text_content = $ifilter->process($html_content);*/
	}

	$mime->setTXTBody($text_content);
	$mime->setHTMLBody($html_content);
	$mime->setSubject($subject);

	foreach ($files as $file) {
		//$ref=$mime->addAttachment($file['file_path'], $file['mime-type'], '', true, $file['filename']);
		$ref=$mime->addAttachment($file['filename'], $file['mime-type'], $file['name'], true);
	}

	$param_getter = array();

	if (_DIMS_ENCODING=="UTF-8"){
		$param_getter["text_encoding"] = "8bit";
		$param_getter["html_encoding"] = "quoted-printable";
	}
	$param_getter["text_charset"] = _DIMS_ENCODING;
	$param_getter["html_charset"] = _DIMS_ENCODING;
	// adding encoding
	$param_getter["head_charset"] = _DIMS_ENCODING;

	$final_body = $mime->get($param_getter);
	$final_headers = $mime->headers($headers);

	$mail =& Mail::factory('mail');
	$result = $mail->send($str_to, $final_headers, $final_body);

	return $result;
}
?>
