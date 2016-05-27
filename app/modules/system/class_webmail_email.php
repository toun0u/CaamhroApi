<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

/**
 * @author		NETLOR CONCEPT - Simon LIEB
 * @version	1.0
 * @package	WebMail
 * @access	public
 * @see			webmail_inbox
 */
class webmail_email extends dims_data_object
{


	private $m_attachedFiles;
	private $m_toAttachedFiles;
	private $m_attachedFilesTable;
	private $m_From ;
	private $m_destTo;
	private $m_destCc;
	private $m_adresseTable;
	private $m_linkTable;
	private $m_ressource;
	private $m_fileAttachFolder;
	/**
	* Class constructor
	*
	* @access public
	**/
	public function __construct() {
		parent::dims_data_object('dims_mod_webmail_email', 'id');

		$this->m_attachedFiles		= array();
		$this->m_toAttachedFiles	= array();
		$this->m_attachedFilesTable = 'dims_mod_webmail_email_docfile';
		$this->m_destTo = array();
		$this->m_destCc = array();
		$this->m_adresseTable = 'dims_mod_webmail_email_adresse';
		$this->m_linkTable = 'dims_mod_webmail_email_link';
		$this->m_From = '' ;
	}

	/**
	 * webmail_email::save()
	 *
	 * @return int mail's id
	 * @access public
	 */
	public function save()
	{
		$return_value	= null;
		$id_mail		= null;
		global $dims;

		$id_mail = parent::save();

		// ajout des liens fichier joint <-> mail
		if(count($this->m_toAttachedFiles) > 0)
		{
			$sql = '';

			$db = $dims->getDb();

			foreach($this->m_toAttachedFiles as $idFile)
			{
				$sql = 'INSERT INTO ';
				$sql .= $this->m_attachedFilesTable;
				$sql .= ' VALUES ("", :idmail , :idfile )';

				$db->query($sql, array(
					':idmail' => $id_mail,
					':idfile' => $idFile
				));
			}

			$this->m_toAttachedFiles = array();
		}

		// ajout lien expéditeur From <-> mail
		if ($this->m_From != NULL) {
			$sql = '' ;
			$db = $dims->getDb();
			$sql = 'INSERT INTO ';
			$sql .= $this->m_adresseTable;
			$sql .= ' VALUES ("", :idmail ,"1", :mfrom )';

			$db->query($sql, array(
					':idmail' => $id_mail,
					':mfrom' => $this->m_From
				));
		}

		// ajout liens destinataire To <-> mail
		if(count($this->m_destTo)>0){
			$sql = '' ;
			$db = $dims->getDb() ;
			foreach($this->m_destTo as $mailTo){
				$sql = 'INSERT INTO ';
				$sql .= $this->m_adresseTable;
				$sql .= ' VALUES ("", :idmail ,"2", :mailto )';

				$db->query($sql, array(
					':idmail' => $id_mail,
					':mailto' => $mailTo
				));

			}
			$this->m_destTo = array();
		}

		// ajout liens destinataire Cc <-> mail
		if(count($this->m_destCc)>0){
			$sql = '' ;
			$db = $dims->getDb() ;
			foreach($this->m_destCc as $mailCc){
				$sql = 'INSERT INTO ';
				$sql .= $this->m_adresseTable;
				$sql .= ' VALUES ("", :idmail ,"3", :mailcc )';

				$db->query($sql, array(
					':idmail' => $id_mail,
					':mailcc' => $mailCc
				));
			}
			$this->m_destCc = array();
		}

		$this->addMailLink($id_mail) ;

		$return_value = $id_mail;

		return $return_value;
	}

	public function addMailLink($id_mail) {
		$sql = '';

		global $dims;
		$db = $dims->getDb();

		// lien mails <-> destinataires et expéditeur
		$sql = 'SELECT id, mail FROM '.$this->m_adresseTable.' WHERE id_mail = :idmail ';

		$id_dest = $db->query($sql, array(
					':idmail' => $id_mail,
				));

		while($result = $db->fetchrow($id_dest)){

			$sql = 'SELECT	id
				FROM	dims_mod_business_contact
				WHERE	email = :email
				OR	email2 = :email
				OR	email3 = :email ' ;
			$linkContact = $db->query($sql, array(
					':email' => $result['mail']
				));

			if($db->numrows($linkContact) > 0){
				while ($resultContact = $db->fetchrow($linkContact)){
					$sql = 'INSERT INTO '.$this->m_linkTable.' VALUES ("'.$result['id'].'","'.$resultContact['id'].'")' ;
					$db->query($sql);
				}
			}else{
				$sql = 'INSERT INTO '.$this->m_linkTable.' VALUES ("'.$result['id'].'","")' ;
				$db->query($sql);
			}

			$sql = 'SELECT	id
				FROM	dims_mod_business_contact_layer
				WHERE	email = :email
				OR	email2 = :email
				OR	email3 = :email ' ;
			$linkContact = $db->query($sql, array(
					':email' => $result['mail']
				));

			if($db->numrows($linkContact) > 0){
				while ($resultContact = $db->fetchrow($linkContact)){
					$sql = 'INSERT INTO '.$this->m_linkTable.' VALUES ("'.$result['id'].'","'.$resultContact['id'].'")' ;
					$db->query($sql);
				}
			}else{
				$sql = 'INSERT INTO '.$this->m_linkTable.' VALUES ("'.$result['id'].'","")' ;
				$db->query($sql);
			}

		}
	}

	/**
	 * webmail_email::open()
	 *
	 * @return mixed parent::open();
	 * @access public
	 */
	/*public function open()
	{
		$return_value = null;

		$return_value = parent::open();

		return $return_value;
	}*/

	/**
	 * webmail_email::addFilesAttached()
	 *
	 * @param int|int array $idFiles docfile's id to attach
	 * @access public
	 */
	public function addFilesAttached($idFiles)
	{
		$return_value = false;

		$this->loadMailAttachment();

		if(is_array($idFiles))
		{
			foreach($idFiles as $idFile)
			{
				if(!isset($this->m_attachedFiles[$idFile]))
					$this->m_attachedFiles[$idFile]		= $idFile;

				if(!isset($this->m_toAttachedFiles[$idFile]))
					$this->m_toAttachedFiles[$idFile]	= $idFile;
			}

			$return_value = true;
		}
		elseif(is_int($idFiles))
		{
			if(!isset($this->m_attachedFiles[$idFiles]))
				$this->m_attachedFiles[$idFiles]	= $idFiles;

			if(!isset($this->m_toAttachedFiles[$idFiles]))
				$this->m_toAttachedFiles[$idFiles]	= $idFiles;
			$return_value = true;
		}

		return $return_value;
	}

	public function addDestTo($to){
		$this->m_destTo = array();
		if (preg_match_all("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$to,$regs)>0){
			foreach ($regs[0] as $val){
				$this->m_destTo[$val] = $val;
			}
		}
	}

	public function addDestCc($cc){
		$this->m_destCc = array();
		if (preg_match_all("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$cc,$regs)>0){
			foreach ($regs[0] as $val){
				$this->m_destCc[$val] = $val;
			}
		}
	}

	public function addFrom($from){
		//if (preg_match("/[\.0-9A-Za-z_-]*@[0-9A-Za-z]*\.[[:alpha:]]{2,4}/",$from,$regs)>0){

		//	$this->m_From = $regs[0] ;
		//}
		//else {
			$this->m_From =$from;
		//}
	}

	public function getUserFrom() {
		$dims = dims::getInstance();
		$id_user_from=0;
		$db = $dims->getDb();

		$sql="select u.id from dims_user as u  inner join dims_mod_business_contact as c on c.id=u.id_contact
				and (u.email like :emailaddress or c.email like :emailaddress )";

		$res=$db->query($sql, array(
			':emailaddress' => array('type' => PDO::PARAM_STR, 'value' => $this->m_From),
		));

		if ($f=$db->fetchrow($res)) {
			$id_user_from=$f['id'];
		}
		return $id_user_from;
	}

	/**
	 * webmail_email::getAttachedFiles()
	 *
	 * return int array mail attachment's docfile id
	 * @access public
	 */
	public function getAttachedFiles()
	{
		$this->loadMailAttachment();

		return $this->m_attachedFiles;
	}

	/**
	 * webmail_email::loadMailAttachment()
	 *
	 * @return bool True if mail's attachment loaded, false if no or error
	 * @access private
	 */
	private function loadMailAttachment()
	{
		global $dims;
		$return_value = false;

		if(!$this->new) {
			$sql	= '';

			$db = $dims->getDb();

			$sql = 'SELECT * FROM '.$this->m_attachedFilesTable.' WHERE id_email = :idemail ';

			$ressource = $db->query($sql, array(
				':idemail' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id'])
			));

			if($db->numrows($ressource) > 0)
			{
				$this->m_attachedFiles = array();

				while($result = $db->fetchrow($ressource))
				{
					if(!isset($this->m_attachedFiles[$result['id_docfile']]))
						$this->m_attachedFiles[$result['id_docfile']] = $result['id_docfile'];
				}

				$return_value = true;
			}
		}

		return $return_value;
	}

	public function SetFileAttachFolder($path) {
		$this->m_fileAttachFolder = $path;
	}

	public function getEmailContent2($filepath) {

		require_once(DIMS_APP_PATH . '/include/MimeMailParser.class.php');

		$path = $filepath;
		$Parser = new MimeMailParser();
		$Parser->setPath($path);

		$to = $Parser->getHeader('to');

		$from = $Parser->getHeader('from');
		$subject = $Parser->getHeader('subject');
		$text = $Parser->getMessageBody('text');
		$html = $Parser->getMessageBody('html');
		$attachments = $Parser->getAttachments();

		dims_print_r($attachments);
	}

	public function getEmailContent($fileindex) {
		$this->m_ressource=imap_open($fileindex,"","");
		// collecte des entetes
		$header = imap_header($this->m_ressource, 1);

		// analyse de la date
		if(isset($header->date)) {
		$date = trim(substr($header->date,5));
		$date = strtotime($date);
		$this->fields['date'] = date('YmdHis',$date);
		}

		// analyse du from
		 if(isset($header->from[0])) {

		$adr = trim(substr($header->from[0],5));
		$this->addFrom($adr);
		$id_user_from=$this->getUserFrom();
		if ($id_user_from>0) {
			// on a trouve le compte user de l'envoyeur de la vcard
			$this->fields['id_user'] = $id_user_from;
		}

		 }

		 // pas de traitement des cc pour l'instant, test si pas de from par le xsender
		 if ($this->fields['id_user']==0 && (isset($header->sender[0]))) {
		$adr = trim($header->sender[0]->mailbox.'@'.$header->sender[0]->host);
		$this->addFrom($adr);

		$id_user_from=$this->getUserFrom();
		if ($id_user_from>0) {
			// on a trouve le compte user de l'envoyeur de la vcard
			$this->fields['id_user'] = $id_user_from;
		}
		 }

		 // analyse du subject
		if(isset($header->subject)) {
		$subject = trim($header->subject);
		$this->fields['subject'] = $subject;
		}

		$mailUid=1;

		$structure = imap_fetchstructure($this->m_ressource, $mailUid, FT_UID);

		$text_section = 0;

		$text_section = $this->getContentSection($structure);

		$overview = imap_fetch_overview($this->m_ressource,1,0);
		$message = imap_fetchbody($this->m_ressource,1,1);

		$return_value = imap_fetchbody($this->m_ressource, $mailUid,"1", FT_UID);

	return $return_value;
		//$mail=$this->mail_mime_to_array($this->m_ressource, 1,true);
		//dims_print_r($mail);
		// dims_print_r($return_value);
		//dims_print_r(imap_fetchbody($this->m_ressource,$mailUid,'1'));
		//dims_print_r(imap_fetchbody($this->m_ressource,$mailUid,'1'));
		//$this->getFilesAttached($this->m_ressource,$mailUid);
		//$structure = imap_fetchstructure($this->m_ressource, $mailUid, '1');
		//dims_print_r($structure);
	}

	function mail_parse_headers($headers)
	{
		$headers=preg_replace('/\r\n\s+/m', '',$headers);
		preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
		foreach ($matches[1] as $key =>$value) $result[$value]=$matches[2][$key];
		return($result);
	}
	function mail_mime_to_array($imap,$mid,$parse_headers=false)
	{
		$mail = imap_fetchstructure($imap,$mid);
		$mail = $this->mail_get_parts($imap,$mid,$mail,0);
		if ($parse_headers) $mail[0]["parsed"]=$this->mail_parse_headers($mail[0]["data"]);
		return($mail);
	}

	function mail_get_parts($imap,$mid,$part,$prefix)
	{
		$attachments=array();
		$attachments[$prefix]=$this->mail_decode_part($imap,$mid,$part,$prefix);
		dims_print_r($part);
		if (isset($part->parts)) // multipart
		{
		$prefix = ($prefix == "0")?"":"$prefix.";
		foreach ($part->parts as $number=>$subpart)
			$attachments=array_merge($attachments, $this->mail_get_parts($imap,$mid,$subpart,$prefix.($number+1)));
		}
		return $attachments;
	}
	function mail_decode_part($connection,$message_number,$part,$prefix)
	{
		$attachment = array();

		if($part->ifdparameters) {
		foreach($part->dparameters as $object) {
			$attachment[strtolower($object->attribute)]=$object->value;
			if(strtolower($object->attribute) == 'filename') {
			$attachment['is_attachment'] = true;
			$attachment['filename'] = $object->value;
			}
		}
		}

		if($part->ifparameters) {
		foreach($part->parameters as $object) {
			$attachment[strtolower($object->attribute)]=$object->value;
			if(strtolower($object->attribute) == 'name') {
			$attachment['is_attachment'] = true;
			$attachment['name'] = $object->value;
			}
		}
		}
		echo $message_number."<br>";
		$attachment['data'] = imap_fetchbody($connection, $message_number, $prefix);
		if($part->encoding == 3) { // 3 = BASE64
		$attachment['data'] = base64_decode($attachment['data']);
		}
		elseif($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
		$attachment['data'] = quoted_printable_decode($attachment['data']);
		}
		return($attachment);
	}
	/**
	 * webmail_email::getContentSection()
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
	 * webmail_email::getFilesAttached()
	 *
	 * @return int array of docfile's id
	 * @param int $mailUid : mail's uid
	 * @access private
	 * @uses docfile
	 * @uses webmail_inbox::searchFileSections()
	 */
	private function getFilesAttached($m_ressource,$mailUid)
	{
		$this->m_ressource=$m_ressource;
		$return_value = array();
		$file_sections = array();

		$structure = imap_fetchstructure($this->m_ressource, $mailUid, 'FT_UID');

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

			echo $file_body;
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
		dims_print_r($structure);
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

	// fonction permettant de traiter differemment les fichiers .msg et d'en extraire le contenu
	public function GetContentFromMsgFile($fileindex,$temp_dir) {
		if (!file_exists($temp_dir)) {
			dims_makedir($temp_dir);
		}

		$this->fields['id_inbox'] = 0;
		$this->fields['uid'] = 0;
		$this->fields['read'] = 0;
		$this->fields['id_module'] = $_SESSION['dims']['moduleid'];
		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$this->fields['id_user'] = $_SESSION['dims']['userid'];

		//$content=$this->getEmailContent($fileindex);
		$id_user_from=0;
		$adr='';
		$fp = fopen($fileindex, 'r');
		$test_content = false ;
		$boundaries=array();

		$contenttype=false;
		$contentboundary=false;
		$started_boundary=false;
		$content_boundary='';
		$current_boundary = "";

		while($ligne = fgets($fp)){
			//print "l =>".$ligne."<br>";
			$boundary='';
			if (substr($ligne,0,5) == "Date:"){
				$date = trim(substr($ligne,5));
				$date = strtotime($date);
				$this->fields['date'] = date('YmdHis',$date);
			// recherche From
			}
			elseif(substr($ligne,0,5) == "From:"){
				$adr = trim(substr($ligne,5));
				$this->addFrom($adr);
				$id_user_from=$this->getUserFrom();
				if ($id_user_from>0) {
					$this->fields['id_user'] = $id_user_from;
				}
			}
			elseif(substr($ligne,0,5) == "From "){
				$adr = trim(substr($ligne,5));
				$res=explode(" ",$adr);

				if (isset($res[0])) $adr=$res[0];

				$this->addFrom($adr);
				$id_user_from=$this->getUserFrom();
				if ($id_user_from>0) {
					$this->fields['id_user'] = $id_user_from;
				}
			}
			elseif(substr($ligne,0,9) == "X-Sender:"){
				$adr = trim(substr($ligne,9));
				$this->addFrom($adr);
				$id_user_from=$this->getUserFrom();

				if ($id_user_from>0) {
					$this->fields['id_user'] = $id_user_from;
				}
			}elseif(substr($ligne,0,3) == "To:"){
				$adr = trim(substr($ligne,3)) ;
				while (substr($adr,-1) == ','){
					$ligne = fgets($fp);
					$adr .= trim(substr($ligne,3)) ;
				}
				$this->addDestTo($adr);
			// recherche Cc
			}elseif(substr($ligne,0,3) == "Cc:"){
				$adr = trim(substr($ligne,3)) ;
				while (substr($adr,-1) == ','){
					$ligne = fgets($fp);
					$adr .= trim(substr($ligne,3)) ;
				}
				$this->addDestCc($adr);
			// recherche Sujet
			}elseif(substr($ligne,0,8) == "Subject:"){
				$sujet = trim(substr($ligne,8)) ;
				$ligne = fgets($fp);
				$deb_sujet = ftell($fp);
				while((trim($ligne) == "") || (!preg_match("/^[[:upper:]][\-A-Za-z]*: /",trim($ligne)))){
					$sujet .= ' '.trim($ligne) ;
					$ligne = fgets($fp);
				}
				$this->fields['subject'] = $sujet;
				fseek($fp,$deb_sujet);
			}elseif(substr($ligne,0,26) == "Content-Transfer-Encoding:"){
				$test_content = true ;
			// recherche body + attachement
			}elseif(substr($ligne,0,13) == "Content-Type:"){

				if(strpos($ligne,"multipart") > 0){

					$contenttype=true;
					$positionbound=strpos($ligne,"boundary=");
					if ($positionbound > 0 ){
						// on raccourci la ligne car contient peut etre le content type
						$ligne=substr($ligne,$positionbound+strlen("boundary="));

						$ligne=str_replace('boundary=','',trim($ligne));
						$ligne=str_replace('\";','',$ligne);
						$ligne=str_replace('";','',$ligne);
						$ligne=str_replace('"','',$ligne);
						$boun=str_replace('\"','',$ligne);
						//$boun = explode('"',$ligne);
						//$boundary = '--'.$boun[1];
						$boundary = '--'.$boun;
						$contentboundary=true;

						// ajout de la balise ds le tableau des boundary
						//$boundaries[$boundary]=array();
						$boundaries[$boundary]['start']=false;
						$boundaries[$boundary]['end']=false;
						$boundaries[$boundary]['content']=array();
					}
				}
			}
			elseif(strpos($ligne,"boundary=") > 0 && $contenttype && trim($ligne)!=''){
				$ligne=str_replace('boundary=','',trim($ligne));
				$ligne=str_replace('\";','',$ligne);
				$boun=str_replace('\"','',$ligne);

				//$boun = explode('"',$ligne);
				/*if (sizeof($boundaries)>0) {
					dims_print_r($boun);die();
				}*/
				//$boundary = '--'.$boun[1];
				$boundary = '--'.$boun;
				$contentboundary=true;

				// ajout de la balise ds le tableau des boundary
				//$boundaries[$boundary]=array();
				$boundaries[$boundary]['start']=false;
				$boundaries[$boundary]['end']=false;
				$boundaries[$boundary]['content']=array();
				if (sizeof($boundaries)>1) {
					//dims_print_r($boundaries);die();
				}
			}


			// on dispose d'un boundary
			/*if ($boundary != "") {
				if(strpos($ligne,$boundary) > 0) {
					echo "<br> End of boundary<br>";
					// si ok on a un contenu de boundary à stocker
					if ($started_boundary && $content_boundary!='') {
						$boundaries[$boundary]['end']=true;
						$boundaries[$boundary]['content'][]=$content_boundary;
					}
				}



			}*/

			// check for boundary pattern
			if (isset($boundaries[trim($ligne)]) && trim($ligne)!='') {
				$ligne=trim($ligne);
				// si ok on a un contenu de boundary à stocker
				if ($started_boundary && $content_boundary!='') {
					//echo "<br> End of boundary<br>";
					$boundaries[$current_boundary]['end']=true;
					$boundaries[$current_boundary]['content'][]=$content_boundary;
				}

				// on a un debut de boundary
				$boundaries[$ligne]['start']=true;
				$started_boundary=true;
				$content_boundary='';
				$current_boundary=$ligne;
				//echo "<br> Begin boundary<br>";
			}
			else {
				// on construit le contenu du boundary
				if (strlen(trim($ligne))>2) {
					$subligne=substr(trim($ligne),0,strlen(trim($ligne))-2);
					//if ($subligne=="--_006_A8ACE35E4D93654589B6C43CFA60B93B5B31739CF4msex1MINECOlo_") {
					//	dims_print_r($boundaries[trim($subligne)]);
					//	die();
					//}
				}
				else {
					$subligne=$ligne;
				}
				//echo $subligne."<br>";
				if ($started_boundary && !isset($boundaries[trim($ligne)])	&& !isset($boundaries[trim($subligne)])) {
					$content_boundary.=$ligne;
				}
			}

		}

		// fin du dernier boundary
		if ($started_boundary && $content_boundary!='') {
			//echo "<br> End of boundary<br>";
			$boundaries[$current_boundary]['end']=true;
			$boundaries[$current_boundary]['content'][]=$content_boundary;
		}

		$finalcontent='';
		// définition des boundaries
		if (isset($boundaries)) {
			foreach ($boundaries as $k =>$boundary) {
				if (isset($boundary['content']) && $boundary['content']!='') {
					foreach ($boundary['content'] as $ind => $content) {
						$test_content_type = false ;
						$test_content_transfer = false ;
						$test_content_dispo = false ;
						$name_attachement = '';
						$encoding = false ;
						$body = false ;
						$convert=false;
						$beginnameattachement=false;
						//dims_print_r($content);die();
						// ecriture de chaque boundary pour debug
						$fileattach=$temp_dir.'/_attach'.$k."_".$ind.'.eml';
						file_put_contents($fileattach, $content);
						// lecture de chaque fichier
						$fp = fopen($fileattach, 'r');
						while ($ligne = fgets($fp)){
							$ligne=trim($ligne);
							//echo "New ligne ".$ligne."<br>";
							if (substr($ligne,0,8) == "Content-"){
								$type = explode(':',$ligne);
								$type_content = substr($type[0],7);

								switch($type_content){
									case "-Transfer-Encoding" :
										$test_content_transfer = true ;
										if (strpos($ligne,"base64") > 0) {
											$encoding = true ;
										}

										break ;
									case "-Type" :
										$test_content_type = true ;
										//echo "<br>TYPE :".$ligne."<br>";
										if (strpos($ligne,"text/plain;") > 0){
											$test_content_dispo = true ;
											$body = true ;
										}
										elseif (strpos($ligne,"text/vcard;") > 0){
											$test_content_dispo = true ;
											$body=false;
										}
										elseif (strpos($ligne,"text/x-vcard;") > 0){
											$test_content_dispo = true ;
											$body=false;

										}
										break ;
									case "-Disposition" :
										$test_content_dispo = true ;
										if (strpos($ligne,"name=") > 0){
											$ligne=str_replace('\"','"',$ligne);

											$name = explode('"',$ligne);

											if ($name[1]!='') {
												$name_attachement = $name[1];
												if (sizeof($name)!=3) // on doit prendre la ligne suivante car pas termine
												$beginnameattachement=true;
											}

										}
										else {
											while (($ligne = fgets($fp)) && ($name_attachement == '')){
												if (strpos($ligne,"name=") > 0){
													$ligne=str_replace('\"','"',$ligne);
													$name = explode('"',$ligne);
													if ($name[1]!='') {
														$name_attachement = $name[1];
														 if (sizeof($name)!=3) // on doit prendre la ligne suivante car pas termine
														$beginnameattachement=true;
													}

												}

											}
										}
										if ($name_attachement=='') {
											$name_attachement='no-name.txt';
										}
										break ;
									default :
										/*if ($test_content_type && $test_content_transfer && $test_content_dispo){
											$content .= $ligne;
										}*/
										break ;
								}
								if (strpos($ligne,"name=") > 0){
									$ligne=str_replace(array('\\\"','\\"','\"'),'"',$ligne);
									$name = explode('"',$ligne);
									if ($name[1]!='' && ($name_attachement=='' || $name_attachement=='no-name.txt')) {
										$name_attachement = $name[1];
										 if (sizeof($name)!=3) // on doit prendre la ligne suivante car pas termine
											$beginnameattachement=true;
									}
								}
								//echo $test_content_type."-".$test_content_transfer."-".$test_content_dispo."-".$name_attachement."-".$boundary."<br>";
							}
							else {
								if ($beginnameattachement) {

								$ligne=str_replace(array('\\\"','\\"','\"'),'"',$ligne);
								$name_attachement.=$ligne;
								$beginnameattachement=false;
								}
							}

							// on a le separateur strlen($ligne)==0 &&
							if( $test_content_type && $test_content_transfer && $test_content_dispo && $name_attachement!=''){
								$boundarytiret=$boundary."--";
								$content=''; // on vide pour ne prendre que le contenu du texte
								$ligne=fgets($fp);
								if ($beginnameattachement) { // on doit prendre la ligne suivante car pas termine
									$ligne=str_replace(array('\"','"'),'',$ligne);
									$name_attachement.=$ligne;
									$ligne=fgets($fp);
									if (trim($ligne)!="") $content .= $ligne;
								}
								else {
									$content .= $ligne;
								}

								$name_attachement=trim(str_replace(" ","_", $name_attachement));

								while (($ligne = fgets($fp)) && (trim($ligne) != $boundary) && (trim($ligne) != $boundarytiret)){
									$content .= $ligne;
								}

								if($body){
									// correspond au texte dans le mail
									$this->fields['content'] = $content ;
								}else{
									//echo "<BR>Pat ".$name_attachement." ".$encoding;
									// correspond ? un fichier joint
									// cr√©ation d'un fichier ...

									if ($encoding)
										$content = base64_decode($content);

									if ($name_attachement!='') {

										file_put_contents($temp_dir."/".$name_attachement, $content);

										if (!isset($_SESSION['dims']['moduleid'])) $_SESSION['dims']['moduleid']=1;
										// creation du do file associe
										$doc_file = new docfile();
										$doc_file->fields['id_module'] = $_SESSION['dims']['moduleid'] ;
										$doc_file->fields['id_workspace'] = $_SESSION['dims']['workspaceid'] ;
										$doc_file->fields['id_user'] = $this->fields['id_user'];
										$doc_file->tmpuploadedfile = $temp_dir."/".$name_attachement ;
										$doc_file->fields['name'] = $name_attachement ;
										$doc_file->fields['size'] = filesize($temp_dir."/".$name_attachement);

										$error = $doc_file->save() ;
										$id_doc = $doc_file->fields['id'] ;
										// extract content
										$doc_file->getcontent();
										$contentpath=str_replace("[dimscontentfile]","",$doc_file->fields['content']);
										if (file_exists($contentpath)) {
											$content=  file_get_contents($contentpath);
										}

										$doc_file->delete();
										$boundaries[$k]['content'][$ind]=$content;

										// on lie le fichier au mail
										//$this->addFilesAttached($id_doc);
									}
								}

								$test_content_type = false ;
								$test_content_transfer = false ;
								$test_content_dispo = false ;
								$encoding = false ;
								$content = "" ;
								$body = false ;
								$name_attachement = "" ;
								$ligne="";
								$beginnameattachement=false;
							}
						}

						// on cumule le resultat
						$finalcontent.= $boundaries[$k]['content'][$ind];
					}
				}
			}
		}

		//$this->save();
		//dims_print_r($boundaries);die();

		if (file_exists($fileindex)) {
			//unlink($fileindex);
		}
		return ($finalcontent);
	}
}
?>
