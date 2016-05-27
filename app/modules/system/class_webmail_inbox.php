<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_webmail_email.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

/**
 * @author		NETLOR CONCEPT - Simon LIEB
 * @version	1.0
 * @package	WebMail
 * @access	public
 * @uses		webmail_email
 */
class webmail_inbox extends dims_data_object
{
	/**
	 * Class constructor
	 *
	 * @access public
	 */
	public function __construct()
	{
		parent::dims_data_object('dims_mod_webmail_inbox','id');
		$this->m_fileAttachFolder = DIMS_TMP_PATH . '/webmail/Attachment/'.date('Ymd0000').'/';

		if(!file_exists($this->m_fileAttachFolder))
		{
			$last_path	= '';
			$array_path = explode('/', $this->m_fileAttachFolder);

			foreach($array_path as $folder)
			{
				if(!empty($folder))
				{
					$path = $last_path.'/'.$folder;

					if(!file_exists($path))
					{
						mkdir($path);
					}

					$last_path = $path;
				}
			}
		}
	}

	/**
	 * Class destructor
	 *
	 * @access public
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

	/*public function open($id)
	{
		parent::open($id);

		// Connexion automatique ?

	}*/

	/**
	 * webmail_inbox::delete()
	 *
	 * Delete mailBox, and mails
	 *
	 * @param bool $keepMail if true do not delete mails, if false (default) delete mails
	 * @return mixed parent::delete()
	 * @access public
	 */
	public function delete($keepMail = false)
	{
		/**
		 * @todo Erase mails AND link to attached files.
		 */
		if(!$keepMail)
		{
		}

		return parent::delete();
	}

	/**
	 * webmail_inbox::connect()
	 *
	 * Connect/Reconnect to a mail box, use 'dims_data_object::fields' to do this
	 *		- fields['server']
	 *		- fields['port']
	 *		- fields['protocol']
	 *		- fields['crypto']
	 *		- fields['login']
	 *		- fields['password']
	 *
	 * @return bool True if connected, false if not
	 * @access public
	 */
	public function connect()
	{
		$return_value = false;

		$mailbox	= '';
		$login		= '';
		$password	= '';

		$mailbox = '{'.$this->fields['server'].':'.$this->fields['port'].'/'.$this->fields['protocol'];

		if(empty($this->fields['crypto']))
			$mailbox .= '/notls';
		else
			$mailbox .= '/'.$this->fields['crypto'];

		$mailbox .= '}INBOX';

		$this->m_mailBox = $mailbox;

		$login		= $this->fields['login'];
		$password	= $this->fields['password'];

		$this->m_ressource = imap_open(imap_utf7_encode($mailbox), $login, $password) or die(imap_last_error());

		if($this->m_ressource)
			$return_value = true;
		else
			$return_value = false;

		//$this->checkIfUnseen();

		return $return_value;
	}

	/**
	 * webmail_inbox::disconnect()
	 *
	 * @return bool true if succes, false if cannot
	 * @access public
	 */
	public function disconnect()
	{
		$return_value = false;

		if($this->m_ressource != null)
			$return_value = imap_close($this->m_ressource);

		$this->m_ressource = null;

		return $return_value;
	}

	/**
	 * webmail_inbox::updateMailBox()
	 *
	 * @param bool $getAll if false(default) get only new mails, if true get ALL mails
	 * @return int number of retrieved mail
	 * @access public
	 * @uses webmail_inbox::getMailContent()
	 * @uses webmail_email
	 * @todo Add FORCE param to get ALL mails even if the mails has already been downloaded
	 */
	public function updateMailBox($getAll = false) {
		$return_value = 0;
		$status		= null;
		$header		= array();

		$status = imap_status($this->m_ressource, $this->m_mailBox, SA_MESSAGES | SA_UNSEEN);

		if($status->unseen > 0) {
			global $dims;
			$mailUid = 0;
			$tabUidDownload = array();
			$db = $dims->getDb();

			$sql = 'SELECT	uid
					FROM	dims_mod_webmail_email
					WHERE	id_inbox = :idinbox ';

			$ressource = $db->query($sql, array(
				':idinbox' => $this->fields['id']
			));

			if($db->numrows($ressource) > 0) {
				while($result = $db->fetchrow($ressource)) {
					$tabUidDownload[$result['uid']] = $result['uid'];
				}
			}

			$mails_tot	= $status->messages;

			for($no_mail = 1; $no_mail < $mails_tot; $no_mail++) {
				$alreadyGet = false;
				$header		= imap_headerinfo($this->m_ressource, $no_mail);
				$mailUid	= imap_uid($this->m_ressource, $no_mail);

				if(isset($tabUidDownload[$mailUid]))
					$alreadyGet = true;

				if(($header->Recent == 'N' || $header->Unseen == 'U' || $getAll) && !$alreadyGet) {
					$mail = null;
					$idFilesAttached = array();

					$content = $this->getMailContent($mailUid);

					$mail = new webmail_email();

					$mail->fields['id_inbox']		= $this->fields['id'];
					$mail->fields['uid']			= $mailUid;

					#$mail->fields['from']			= (isset($header->fromaddress)) ? $header->fromaddress : '';
					$from							= (isset($header->fromaddress)) ? $header->fromaddress : '';
					//$mail->fields['to']			= (isset($header->toaddress)) ? $header->toaddress : '';
					$to								= (isset($header->toaddress)) ? $header->toaddress : '';
					//$mail->fields['cc']			= (isset($header->ccaddress)) ? $header->ccaddress : '';
					$cc								= (isset($header->ccaddress)) ? $header->ccaddress : '';

					//$mail->fields['subject']		= (isset($header->Subject)) ? $header->Subject : '';
					if (isset($header->Subject))
						$mail->fields['subject']	= html_entity_decode(htmlentities(imap_utf8($header->Subject)));
					else
						$mail->fields['subject']	= '';

					mb_detect_encoding($content, "UTF-8") == "UTF-8" ? : $content = utf8_encode($content);
					$mail->fields['content']		= html_entity_decode(htmlentities($content));

					$mail->fields['date']			= (isset($header->udate)) ? date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL,$header->udate) : '';
					$mail->fields['read']			= 0;
					$mail->fields['id_module']		= $_SESSION['dims']['moduleid'];
					$mail->fields['id_workspace']	= $_SESSION['dims']['workspaceid'];
					$mail->fields['id_user']		= $_SESSION['dims']['userid'];

					$idFilesAttached = $this->getFilesAttached($mailUid);
					$mail->addFilesAttached($idFilesAttached);

					$mail->addFrom($from);
					$mail->addDestTo($to);
					$mail->addDestCc($cc);

					$mail->save();

					imap_setflag_full($this->m_ressource, $mailUid, '\\Seen', ST_UID);

					$this->m_mails[] = $mail;

					$return_value++;

					// partie enregistrement des mails en docfile peux être à enlever

					// création d'un fichier contenant les informations essentielles du mail reçu
					$filename = DIMS_TMP_PATH . $mailUid.".eml";
					$fp = fopen($filename,"w+");
					$eml = "Date: ".$mail->fields['date']."\n" ;
					$eml .= "From: ".$from."\n"; // à modifier : from, to & cc ne sont plus dans le fields
					$eml .= "To: ".$to."\n" ;
					$eml .= "CC: ".$cc."\n";
					$eml .= "Subject: ".$mail->fields['subject']."\n";
					$eml .= "\n\n".$content ;
					fwrite($fp,$eml);
					fclose($fp);

					// création d'un objet docfile à partir du fichier créé précédemment
					$docFileMail = new docfile();
					$docFileMail->fields['id_module'] = $_SESSION['dims']['moduleid'];
					$docFileMail->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$docFileMail->tmpuploadedfile = $filename;
					$docFileMail->fields['name'] = $mailUid.".eml";
					$docFileMail->fields['size'] = filesize($filename);

					$error = $docFileMail->save();

					// création des liens
					require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
					$sql = 'SELECT		DISTINCT (id_contact)
						FROM		dims_mod_webmail_email_link wel
						INNER JOIN	dims_mod_webmail_email_adresse wa
						ON		wa.id_mail = :idmail
						AND		wa.type = 1';
					$from = $db->query($sql, array(
						':idmail' => $mail->fields['id']
					));

					$sql = 'SELECT		DISTINCT (id_contact)
						FROM		dims_mod_webmail_email_link wel
						INNER JOIN	dims_mod_webmail_email_adresse wa
						ON		wa.id_mail = :idmail
						AND		wa.type = 2
						OR		wa.type = 3';
					$des = $db->query($sql, array(
						':idmail' => $mail->fields['id']
					));

					$list_d = array();
					while ($d = $db->fetchrow($des)){
						if($d['id_contact'] > 0) {
							$list_d[$d['id_contact']] = $d['id_contact'] ;
						}
					}

					while ($f = $db->fetchrow($from)){
						if ($f['id_contact'] > 0){
							$user = $db->query('SELECT	id
									FROM	dims_user
									WHERE	id_contact = :idcontact ',array(
										':idcontact' => $f['id_contact']
									));
							if ($db->numrows($user) == 1){
								$u = $db->fetchrow($user);
								$action = new dims_action(/*$this->db*/);
								$action->fields['id_parent']=0;
								$action->setModule($mail->fields['id_module']);
								$action->fields['timestp_modify']= dims_createtimestamp();
								$action->fields['comment']= '_DIMS_LABEL_MAILING_ADD_EMAIL';
								$action->fields['type'] = _DIMS_LABEL_EMAIL_ADD; // link
								$action->addObject(0, $mail->fields['id_module'], dims_const::_SYSTEM_OBJECT_MAIL, $mail->fields['id'],$mail->fields['subject']);
								$action->setWorkspace($_SESSION['dims']['workspaceid']);
								$action->setUser($u['id']);
								//$action->setvalues($list_d);
								// adding temp tags
								$action->addTempTags();
								$tag = $db->query("SELECT	id
										FROM	dims_tag
										WHERE	tag LIKE '_DIMS_LABEL_EMAIL'");
								if ($db->numrows($tag) > 0){
									$t = $db->fetchrow($tag);
									$action->addTag($t['id']);
								} else {
									require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
									$objtag = new tag();
									$objtag->fields['type'] = 0;
									$objtag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$objtag->fields['tag'] = '_DIMS_LABEL_EMAIL';
									$id_t = $objtag->save();
									$action->addTag($id_t);
								}

								// save object action
								$action->save();

								foreach ($idFilesAttached as $id_doc){
									$sql = 'SELECT		DISTINCT da.id
										FROM		dims_action da
										INNER JOIN	dims_action_matrix dam
										ON		dam.id_action = da.id
										INNER JOIN	dims_globalobject dg
										ON		dg.id_record = :idrecord
										AND		dg.id = dam.id_globalobject';

									$act = $db->query($sql,array(
										':idrecord' => $id_doc
									));
									$a = $db->fetchrow($act);

									$action = new dims_action();
									$action->open($a['id']);
									$action->setUser($u['id']);
									$tag = $db->query("SELECT	id
													   FROM		dims_tag
													   WHERE	tag LIKE '_DIMS_LABEL_MAIL_ATTACHMENT'");
									$id_tag ;
									if ($db->numrows($tag) > 0){
											$t = $db->fetchrow($tag);
											$id_tag = $t['id'];
									}else{
											require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
											$objtag = new tag();
											$objtag->fields['type'] = 0;
											$objtag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
											$objtag->fields['tag'] = '_DIMS_LABEL_MAIL_ATTACHMENT';
											$id_tag = $objtag->save();
											//$action->addTag($id_t);
									}
									$action->save();

									$sql = "SELECT	DISTINCT id_globalobject, id_user, id_workspace, id_date
											FROM	dims_action_matrix
											WHERE	id_action = :idaction ";

									$matrix = $db->query($sql, array(
										':idaction' => $a['id']
									));
									$m = $db->fetchrow($matrix);
									$sql = "INSERT INTO dims_action_matrix
											VALUES ('', :idglobalobject , :id , :iduser , :idworkspace , :iddate , :idtag )";
									$db->query($sql, array(
										':idglobalobject' 	=> $m['id_globalobject'],
										':id' 				=> $a['id'],
										':iduser' 			=> $m['id_user'],
										':idworkspace' 		=> $m['id_workspace'],
										':iddate' 			=> $m['id_date'],
										':idtag' 			=> $id_tag
									));

									$sql = "UPDATE	dims_mod_doc_file
											SET	id_user = :iduser
											WHERE	id = :iddoc " ;
									$db->query($sql, array(
										':iduser' => $u['id'],
										':iddoc' => $id_doc
									));
								}
							}
						}
					}
				}
			}
		}

		return $return_value;
	}

	/**
	 * webmail_inbox::getNewMails()
	 *
	 * @return class webmail_email array last updated mails
	 * @access public
	 */
	public function getNewMails()
	{
		return $this->m_mails;
	}

	/**
	 * webmail_inbox::getMailContent()
	 *
	 * @return string mail's content
	 * @param int $mailUid	mail's *uid*
	 * @access private
	 * @uses webmail_inbox::getContentSection()
	 */
	private function getMailContent($mailUid)
	{
		$return_value = '';

		$structure = imap_fetchstructure($this->m_ressource, $mailUid, FT_UID);

		$text_section = 0;

		$text_section = $this->getContentSection($structure);

		$return_value = imap_fetchbody($this->m_ressource, $mailUid, $text_section, FT_UID);

		return $return_value;
	}

	/**
	 * webmail_inbox::getContentSection()
	 *
	 * @return int content's first text section
	 * @param object $structure mail's structure
	 * @access private
	 */
	private function getContentSection($structure)
	{
		$return_value	= 0;
		$section		= 0;

		if($structure->type == 1)
		{
			foreach($structure->parts as $key => $part)
			{
				if($part->type == 0)
				{
					$section = $key;
					break;
				}
				elseif($part->type == 1)
				{
					$subSection = 0;
					$subSection = $this->getContentSection($part);
					$section = $section + ($subSection / 10);
					break;
				}
		}
		}

		$section++;

		$return_value = $section;

		return $return_value;
	}

	/**
	 * webmail_inbox::getFilesAttached()
	 *
	 * @return int array of docfile's id
	 * @param int $mailUid : mail's uid
	 * @access private
	 * @uses docfile
	 * @uses webmail_inbox::searchFileSections()
	 */
	private function getFilesAttached($mailUid)
	{
		$return_value = array();
		$file_sections = array();

		$structure = imap_fetchstructure($this->m_ressource, $mailUid, FT_UID);

		$file_sections = $this->searchFileSections($structure);

		foreach($file_sections as $section) {
			$file_structure = null;
			$file_body		= null;
			$file_handler	= null;
			$doc_file		= null;
			$file_name		= '';
			$no_mail		= 0;

			$no_mail = imap_msgno($this->m_ressource, $mailUid);

			$file_structure = imap_bodystruct($this->m_ressource, $no_mail, $section);
			$file_body		= imap_fetchbody($this->m_ressource, $mailUid, $section, FT_UID);

			if($file_structure->ifparameters == 1 && is_array($file_structure->parameters)) {
				foreach($file_structure->parameters as $parameter) {
					if($parameter->attribute == 'NAME')
						$file_name = $parameter->value;
				}
			}
			elseif($file_structure->ifdparameters == 1 && is_array($file_structure->dparameters)) {
				foreach($file_structure->dparameters as $parameter) {
					if($parameter->attribute == 'FILENAME')
						$file_name = $parameter->value;
				}
			}
			else
				$file_name = '';

			if(empty($file_name)) {
				$file_name = 'file_attach_'.date('YmdHis');
			}

			switch($file_structure->type)
			{
				case 0: //TextFile
					$file_handler = fopen($this->m_fileAttachFolder.$file_name, 'a+');
					break;
				default:
					$file_handler = fopen($this->m_fileAttachFolder.$file_name, 'ab+');
					break;
			}

			if($file_structure->encoding == 3)
				$file_body = imap_base64($file_body);

			if (mb_check_encoding($file_body,'ISO-8859-1'))
				$file_body = mb_convert_encoding($file_body,'UTF-8','ISO-8859-1');

			fwrite($file_handler, $file_body);

			fclose($file_handler);

			$doc_file = new docfile();

			$doc_file->fields['id_module'] = $_SESSION['dims']['moduleid'];
			$doc_file->fields['id_workspace'] = $_SESSION['dims']['workspaceid']; // pas de workspace pour le module de webmail
			$doc_file->tmpuploadedfile = $this->m_fileAttachFolder.$file_name;
			$doc_file->fields['name'] = $file_name;
			$doc_file->fields['size'] = filesize($this->m_fileAttachFolder.$file_name);

			$error = $doc_file->save();

			$id_doc = $doc_file->fields['id'];

			$return_value[] = $id_doc;

		}

		return $return_value;
	}

	/**
	 * webmail_inbox::searchFileSections()
	 *
	 * @return string array of Section's number
	 * @param object $structure mail's structure
	 * @access private
	 */
	private function searchFileSections($structure)
	{
		$return_value	= array();
		$section		= 0;
		if($structure->type != 1 &&
		   $structure->ifdisposition == 1 &&
			(strtolower($structure->disposition) == 'inline' ||
			 strtolower($structure->disposition) == 'attachment')) {
				$return_value[] = $section++;
		}
		elseif($structure->type == 1){
			foreach($structure->parts as $key => $part) {
				$section = $key + 1;
				$subSections = array();
				$subSections = $this->searchFileSections($part);
				foreach($subSections as $subSection) {
					if($subSection != 0) {
						$return_value[] = $section.'.'.$subSection;
					} else {
						$return_value[] = $section;
					}
				}
			}
		}
		return $return_value;
	}

	/**
	 * webmail_inbox::checkIfUnseen()
	 *
	 * Set private members
	 *
	 * @return bool true if Unseen mails, false if not
	 * @access private
	 * @uses webmail_inbox::getMailContent()
	 * @uses webmail_email
	 */
	private function checkIfUnseen()
	{
		$return_value = false;

		$status		= null;

		$status = imap_status($this->m_ressource, $this->m_mailBox, SA_MESSAGES | SA_UNSEEN);

		$this->m_remoteUnseenMails	= $status->unseen;
		$this->m_remoteTotalMails	= $status->messages;

		if($this->m_remoteUnseenMails > 0)
			$return_value = true;

		return $return_value;
	}

	private $m_mailBox;
	private $m_ressource;
	private $m_mails;
	private $m_fileAttachFolder;
	private $m_remoteTotalMails;
	private $m_remoteUnseenMails;
}
?>
