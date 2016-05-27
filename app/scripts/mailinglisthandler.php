<?php
chdir (dirname(__FILE__) . '/../..');
require_once 'config.php'; // load config (mysql, path, etc.)
require_once 'app/include/default_config.php'; // load config (mysql, path, etc.)

include_once(DIMS_APP_PATH . "modules/system/class_dims.php");
include_once(DIMS_APP_PATH . "modules/system/class_workspace.php");

//Charge la class des gestions d'exceptions
require(DIMS_APP_PATH . "include/class_exception.php");

try {
	// INITIALIZE DIMS OBJECT
	$dims = new dims();
	dims::setInstance($dims);

	include_once DIMS_APP_PATH . 'include/errors.php';

	// load DIMS global classes
	include_once DIMS_APP_PATH . 'include/class_dims_data_object.php';

	// initialize DIMS
	include_once DIMS_APP_PATH . 'include/global.php'; 		// load dims global functions & constants
	include_once DIMS_APP_PATH . 'modules/system/class_module.php';
	/**
	* Database connection
	*
	* Don't forget to param db connection in ./include/config.php
	*/
	if (file_exists(DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) include_once DIMS_APP_PATH . '/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	global $db;

	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	if(!$db->connection_id) trigger_error(_DIMS_MSG_DBERROR, E_USER_ERROR);

	$dims->setDb($db);

	// ------------------------------------------------------------------------
	// Init
	// ------------------------------------------------------------------------
	ini_set("iconv.internal_encoding", 'UTF-8');
	$_SESSION['dims']['workspaceid'] = $id_workspace = 64;

	include 'Mail.php'; // pear

	require DIMS_APP_PATH . 'include/class_email.php';
	require DIMS_APP_PATH . 'modules/system/class_newsletter.php';

	$recipientaddr = $argv[1];

	$email = email::parsecontent(file_get_contents('php://stdin'));

	$originalrecipients = array();
	$originalrecipients = array_merge($originalrecipients, email::extractemailsaddr($email->getfrom()));
	$originalrecipients = array_merge($originalrecipients, email::extractemailsaddr($email->getto()));
	$originalrecipients = array_merge($originalrecipients, email::extractemailsaddr($email->getcc()));

	$mailinglists = array();

	$sql = 'SELECT * FROM '.newsletter::TABLE_NAME.' WHERE address LIKE :addr';

	/* Temporary fix to allow mailing list to be call with two domain (Similar local part requiried) */
	list($localpart, $domainpart) = explode('@', $recipientaddr);
	$res = $db->query($sql, array(':addr' => array('type' => PDO::PARAM_STR, 'value' => $localpart.'@%')));

	//$res = $db->query($sql, array(':addr' => array('type' => PDO::PARAM_STR, 'value' => $recipientaddr)));

	if($db->numrows($res)) {
		while($data = $db->fetchrow($res)) {
			$mailinglist = new newsletter();
			$mailinglist->openFromResultSet($data);

			$mailinglists[$mailinglist->getId()] = $mailinglist;
		}
	}

	$newrecipients = array();
	$mailobject =& Mail::factory('sendmail');
	foreach($mailinglists as $mailinglist) {
		$subject = trim($email->getsubject());
		if(strpos($subject, '['.$mailinglist->fields['label'].']') === false) {
			$subject = '['.$mailinglist->fields['label'].'] ' . $subject;
		}

		list(,$subject) = explode(':', str_replace("\r\n", '', iconv_mime_encode('Subject', $subject)));

		$emailaddresses = $mailinglist->getAllEmailRegistration(true);
		$senderaddress = current(email::extractemailsaddr($email->getFrom()));
		if(isset($emailaddresses[$senderaddress])) {
			foreach($emailaddresses as $emailaddress) {
				if(!in_array($emailaddress, $originalrecipients)) {
					$newrecipients[$emailaddress] = $emailaddress;

					$ret = $mailobject->send(
						$emailaddress,
						array(
							'Return-Path'       => $mailinglist->getbounceaddress(),
							'Subject'           => trim($subject),
							'Precedence'        => 'list',
							'List-Id'           => $mailinglist->getlistid(),
							'List-Post'         => $mailinglist->getlistmailtoaddr(),
							'List-Unsubscribe'  => $mailinglist->getlistmailtoaddr(),
							'List-Subscribe'    => $mailinglist->getlistmailtoaddr(),
							'List-Help'         => $mailinglist->getlistmailtoaddr(),
						) + $email->getheaders(),
						$email->getbody()
					);
				}
			}
		}

				// XXX DEBUG : Send copy to simon.
				$subject = $email->getsubject();
				if(strpos($subject, '['.$mailinglist->fields['label'].']') === false) {
					$subject = '['.$mailinglist->fields['label'].'] ' . $subject;
				}

				list(,$subject) = explode(':', iconv_mime_encode('Subject', $subject));
				$ret = $mailobject->send(
					'simon@netlor.fr',
					array(
						'Return-Path'       => 'simon@netlor.fr', //$mailinglist->getbounceaddress(),
						'Subject'           => trim($subject),
						'Precedence'        => 'list',
						'List-Id'           => $mailinglist->getlistid(),
						'List-Post'         => $mailinglist->getlistmailtoaddr(),
						'List-Unsubscribe'  => $mailinglist->getlistmailtoaddr(),
						'List-Subscribe'    => $mailinglist->getlistmailtoaddr(),
						'List-Help'         => $mailinglist->getlistmailtoaddr(),
					) + $email->getheaders(),
					$email->getbody()
				);
	}

	// Save server copy.
	//$email->save();

	// ------------------------------------------------------------------------
	// FIN
	// ------------------------------------------------------------------------
}
catch(Error_class $e){
	//Gestion des erreurs dans les class.
	$e->getError();
}
