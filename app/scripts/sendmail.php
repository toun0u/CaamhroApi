<?
function send_mail($from, $to, $subject, $message, $cc = null, $bcc = null)
{
	// from : Array('name','address')
	// to : Array('name','address')
	// cc : Array('name','address')
	// bcc : Array('name','address')


	if (is_array($to))
	{
		$str_to = '';
		foreach($to as $to_detail)
		{
			if ($str_to != '') $str_to .= ', ';
			$str_to .= "{$to_detail['name']} <{$to_detail['address']}>";
		}
	}
	else return(false);

	if (is_array($from))
	{
		$str_from = '';
		foreach($from as $from_detail)
		{
			if ($str_from != '') $str_from .= ', ';
			$str_from .= "{$from_detail['name']} <{$from_detail['address']}>";
		}
	}

	if (isset($cc) && is_array($cc))
	{
		$str_cc = '';
		foreach($cc as $cc_detail)
		{
			if ($str_cc != '') $str_cc .= ', ';
			$str_cc .= "{$cc_detail['name']} <{$cc_detail['address']}>";
		}
	}

	if (isset($bcc) && is_array($bcc))
	{
		$str_bcc = '';
		foreach($bcc as $bcc_detail)
		{
			if ($str_bcc != '') $str_bcc .= ', ';
			$str_bcc .= "{$bcc_detail['name']} <{$bcc_detail['address']}>";
		}
	}

	/* configure Content-type to HTML */
	$headers  = "MIME-Version: 1.0\r\n";
	if (!defined(_DIMS_ENCODING)) {
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	}
	else {
		$headers .= "Content-type: text/html; charset="._DIMS_ENCODING."\r\n";
	}
	//$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

	// add "to" to headers
	if (isset($str_to)) $headers .= "To: $str_to \r\n";
	// add "from" to headers
	if (isset($str_from)) $headers .= "From: $str_from \r\n";
	// add "cc" to headers
	if (isset($str_cc)) $headers .= "Cc: $str_cc \r\n";
	// add "bcc" to headers
	if (isset($str_bcc)) $headers .= "Bcc: $str_bcc \r\n";

	// send mail
	mail($str_to, $subject, $message, $headers);

	return(true);
}


function send_form($from, $to, $subject, $form, $cc = null, $bcc = null)
{

	// form['field'] = value

	$content = '';

	foreach($form as $field => $value)
	{
		$content.= 	"
				<tr bgcolor='#ffffff'>
					<td align='left'><b>$field</b></td>
					<td align='left'>". nl2br($value) ."</td>
				</tr>
				";
	}


	/* message */
	$message = 	"
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

	//echo $message;

	return(send_mail($from, $to, $subject, $message, $cc = null, $bcc = null));
}

foreach ($_GET as $clef => $value)
{
	${$clef} = $value ;
	if ($clef != 'email' && $clef != 'subject' && $clef != 'url') $form[$clef] = $value;
}

foreach ($_POST as $clef => $value)
{
	${$clef} = $value ;
	if ($clef != 'email' && $clef != 'subject' && $clef != 'url') $form[$clef] = $value;
}

foreach ($_COOKIE as $clef => $value)
{
	${$clef} = $value ;
}

/*
$email
$subject
$url
*/

if (isset($email) && isset($subject) && isset($url))
{
	$to[0]['name'] = $email;
	$to[0]['address'] = $email;

	if (isset($from))
	{
                $from[0]['name'] = $from;
                $from[0]['address'] = $from;
	}
	else
	{
		$from[0]['name'] = 'NETLOR CONCEPT SENDMAIL';
		$from[0]['address'] = 'sendmail@netlorconcept.fr';
	}

	send_form($from, $to, $subject, $form);

	header("Location: $url");
}
else echo "erreur de paramétrage";

?>
