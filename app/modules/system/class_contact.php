<?php
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	media
* @access	public
*/
//require_once(DIMS_APP_PATH . "/modules/system/include/business.php");
require_once DIMS_APP_PATH . '/include/class_constant.php';
require_once DIMS_APP_PATH . '/modules/system/include/business.php';
require_once DIMS_APP_PATH . '/modules/system/class_address.php';

class contact extends DIMS_DATA_OBJECT {
	const TABLE_NAME = "dims_mod_business_contact";
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_CONTACT;
	const CONTACT_INACTIF = 1;
	const CONTACT_ACTIF = 0;

	protected $myTiers = null;
	private $linked_user = null;
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
		$this->categories = array();
	}

	public function updateFieldLog($field,$value,$id_mbfield,$private,$type_layer=0) {
		require_once(DIMS_APP_PATH . '/modules/system/class_contact_mbfield.php');
		$ct_mbf = new contactmbfield();
		$ct_mbf->init_description();
		$ct_mbf->fields['id_contact'] = $this->fields['id']; //la fiche concernee
		$ct_mbf->fields['id_mbfield'] = $id_mbfield;
		$ct_mbf->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$ct_mbf->fields['id_user'] = $_SESSION['dims']['userid']; //la personne qui modifie
		$ct_mbf->fields['id_module'] = $_SESSION['dims']['moduleid'];
		$ct_mbf->fields['value'] = $value;
		$ct_mbf->fields['timestp_modify'] = date("YmdHis");
		$ct_mbf->fields['id_lang'] = $_SESSION['dims']['currentlang'];
		$ct_mbf->fields['private'] = $private;
		$ct_mbf->fields['type_layer'] = $type_layer;
		$ct_mbf->save();
	}

	public function delete() {
		parent::delete(dims_const::_SYSTEM_OBJECT_CONTACT);
	}

	public function save($light = false,$mustupdatetimestamp=true) {
		global $dims;

		require_once(DIMS_APP_PATH.'modules/system/include/business.php');

		//if (isset($this->fields['lastname'])) $this->fields['lastname'] = business_format_lastname($this->fields['lastname']);
		//if (isset($this->fields['firstname'])) $this->fields['firstname'] = business_format_firstname($this->fields['firstname']);
		if (isset($this->fields['city'])) $this->fields['city'] = business_format_search($this->fields['city']);

		if (isset($this->fields['phone'])) $this->fields['phone'] = business_format_tel($this->fields['phone']);

		if (isset($this->fields['fax'])) $this->fields['fax'] = business_format_tel($this->fields['fax']);
		if (isset($this->fields['mobile'])) $this->fields['mobile'] = business_format_tel($this->fields['mobile']);

		//if ($this->fields['date_creation'] == '' || $this->fields['date_creation'] == '0000-00-00') $this->fields['date_creation'] = date(dims_const::DIMS_DATEFORMAT_US);

		//dans id_user, on place toujours id_contact qui modifie la fiche
		//$this->fields['id_user'] = $_SESSION['dims']['user']['id_contact'];

		if (isset($this->fields['ref']) && trim($this->fields['ref']) == ''){
			$this->fields['ref'] = md5(uniqid(true).$this->fields['id'].time());
		}

		if ($this->new) {
			if (!isset($this->fields['date_create']) || (isset($this->fields['date_create']) && $this->fields['date_create']=='' ) ) $this->fields['date_create']=dims_createtimestamp();
			 if (!isset($this->fields['id_user']) || $this->fields['id_user']=='' || $this->fields['id_user']==0) {
				if (!isset($_SESSION['dims']['userid']) && ($this->fields['id_user']<=0 || $this->fields['id_user']=='')) {
					$this->fields['id_user']=0;
					$this->fields['id_module']=0;
					$this->fields['id_workspace']=0;
				}
				else {
			$this->fields['id_user']=$_SESSION['dims']['userid'];
				}
			}
			if (!isset($this->fields['id_module']) || $this->fields['id_module']=='' || $this->fields['id_module']==0) {
				$this->fields['id_module']=1; //$_SESSION['dims']['moduleid'];
			}
			if (!isset($this->fields['id_workspace']) || $this->fields['id_workspace']=='' || $this->fields['id_workspace']==0) {
			if (isset($_SESSION['dims']['workspaceid'])) {
				$this->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			}
			}

			if (!isset($this->fields['id_user_create']) || $this->fields['id_user_create']=="" || $this->fields['id_user_create']==0) {
				$this->fields['id_user_create']=(isset($_SESSION['dims']['user']['id_contact'])) ? $_SESSION['dims']['user']['id_contact'] : 0;
			}
			$this->fields['timestp_modify']=dims_createtimestamp();
		}
		if ($mustupdatetimestamp) $this->fields['timestp_modify']=dims_createtimestamp();


		if(!$light){
			$id_user=0;
			if ($this->fields['id']>0) {
				$res=$this->db->query('select id from dims_user where id_contact=:idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				));
				if ($this->db->numrows($res)>0) {
					while ($u=$this->db->fetchrow($res)) {
						$id_user=$u['id'];
					}
				}
			}

			// ajout du suivi des maj par user
			$sql = "INSERT INTO	`dims_mod_business_activity_contact` (`id`,`id_user`,`id_contact`,`date_modify`)
				VALUE		(NULL, :iduser,:idcontact,'".dims_createtimestamp()."')";
			$this->db->query($sql, array(
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
		}

		//dims_print_r($action);die();
		//parent::save(dims_const::_SYSTEM_OBJECT_CONTACT);
		//dims_print_r($this);die();
		$res = parent::save(dims_const::_SYSTEM_OBJECT_CONTACT);
		//dims_print_r($this);
		$this->updateMatrice();
		return($res);
	}

	public function getLinkedUser(){
		if( is_null($this->linked_user)){
			$this->linked_user = new user();
			if(!empty($this->fields['account_id'])){
				$this->linked_user->open($this->fields['account_id']);
			}
			else{
				$this->linked_user->init_description();
			}
		}
		return $this->linked_user;
	}

	public function updateMatrice(){
	require_once DIMS_APP_PATH.'modules/system/class_matrix.php';

		if( ! method_exists($this, 'getMatrixInstance') )//permet de manière générique, d'utiliser l'éventuelle classe matrix du module métier, si tant est que la classe contact soit héritée
			$matrix = new matrix();
		else $matrix = $this->getMatrixInstance();

		$row = $matrix->getInitializationRowOf('id_contact', $this->fields['id_globalobject']);

		if( $row->isNew() ){
			$row->init_description(true);
			$row->fields['id_contact'] = $this->fields['id_globalobject'];
			$row->fields['year'] = date('Y');
			$row->fields['month'] = date('n');
		}
		//par défaut on met à jour ces informations suivantes, qu'on soit en édition ou création
		$row->fields['id_country'] = (isset($this->fields['id_country'])) ? $this->fields['id_country'] : 0;
		$row->fields['timestp_modify'] = dims_createtimestamp();
		$row->save();
		return $row;//pour la surcharge de la méthode avec des axes métiers de la matrice
	}

	public function getDynamicFields($share=false) {
		$lst=array();

		$sql= "SELECT		mf.*,
							mb.name as namefield,
							mb.label as labelfield
				from		dims_mod_business_meta_field as mf
				INNER JOIN	dims_mb_field as mb
				ON			mb.id=mf.id_mbfield
				where		mf.id_object= :idobject ";

		if ($share) {
			$sql.=" and mode=1";
		}
		$sql.=" order by mf.position";
		$res=$this->db->query($sql, array(':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT) );
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$lst[$f['id']]=$f;
			}
		}
		return $lst;
	}


	public function getMbFields() {
		$lst=array();

		$res=$this->db->query("	SELECT		f.*,t.name as tablename
								FROM		dims_mb_field f
								INNER JOIN	dims_mb_table t
								ON			f.id_table = t.id
								WHERE		t.name='dims_mod_business_contact'");
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$lst[$f['id']]=$f;
			}
		}
		return $lst;
	}

	// fonction permettant de regrouper des contacts
	// doit tenir compte : liens contact et entreprise, event + layer sur contact fusionne
	public function mergeContact($ctfrom) {
		require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');

		$sql =	"
						SELECT		*
						FROM		dims_mod_business_contact_layer
						WHERE		id = :idcontact";

		// on commence par recuperer les layers eventuels du contact to
		$layerto=array();
		$res=$this->db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$layerto[$f['type_layer']][$f['id_layer']]=$f['id'];
			}
		}

		// on recupere les layers eventuels du contact from
		$sql =	"
						SELECT		*
						FROM		dims_mod_business_contact_layer
						WHERE		id = :idcontact";

		$layerfrom=array();

		$res=$this->db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				if (isset($layerto[$f['type_layer']][$f['id_layer']])) {

					// on a deja un layer de ce type
					$contactfrom = new contact_layer();
					$contactfrom->open($f['id'],$f['type_layer'],$f['id_layer']);

					$contactto = new contact_layer();
					$contactto->open($layerto[$f['type_layer']][$f['id_layer']],$f['type_layer'],$f['id_layer']);

					foreach($contactto->fields as $f=> $v) {
						if ($f!='id_user' && $f!="id_workspace" && $f!='id_module' && $f!='id_user_create') {
							if ($contactto->fields[$f]=='' && $contactfrom->fields[$f]!='') {
								$contactto->fields[$f] = $contactfrom->fields[$f];
							}
						}
					}
					$contactto->save();
					$contactfrom->delete();
				}
				else {
					// on doit juste mettre à jour car n'existe pas
					// on transforme le from en to
					$res=$this->db->query("UPDATE dims_mod_business_contact_layer set id= :idcontactto where id= :idcontactfrom and type_layer= :typelayer and id_layer= :layer", array(
						':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
						':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
						':typelayer' => array('type' => PDO::PARAM_INT, 'value' => $f['type_layer']),
						':layer' => array('type' => PDO::PARAM_INT, 'value' => $f['id_layer']),
					));
				}
			}
		}

		// on traite maintenant les rattachements
		$sql = "SELECT	id_contact1,id_contact2
				from dims_mod_business_ct_link as link
				where id_contact1 = :idcontact
				OR id_contact2 = :idcontact";

		$res = $this->db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$alreadyct=array();
		$idpers=0;
		$alreadyct[0]=0;

		while ($f=$this->db->fetchrow($res)) {
			if ($f['id_contact1']!=$this->fields['id']) {
				$idpers=$f['id_contact1'];
			}
			else {
				$idpers=$f['id_contact2'];
			}
			$alreadyct[$idpers]=$idpers;
		}

		$params = array();
		$sql = "SELECT	id_contact1,id_contact2
				from dims_mod_business_ct_link as link
				where id_contact1 = :contactfrom and id_contact2 not in (".$this->db->getParamsFromArray($alreadyct, 'idcontact2', $params).")
				OR id_contact2 = :contactfrom and id_contact1 not in (".$this->db->getParamsFromArray($alreadyct, 'idcontact1', $params).")";
		$params[':contactfrom'] = array('type' => PDO::PARAM_INT, 'value' => $ctfrom);

		$idpers=0;
		$res = $this->db->query($sql, $params);
		$field='';
		$fielddest='';

		while ($f=$this->db->fetchrow($res)) {
			if ($f['id_contact1']!=$ctfrom) {
				$idpers=$f['id_contact1'];
				$field='id_contact2';
				$fielddest='id_contact1';
			}
			else {
				$idpers=$f['id_contact2'];
				$field='id_contact1';
				$fielddest='id_contact2';
			}

			// on rattache maintenant ce contact au nouveau et on supprime l'ancien
			$sql="UPDATE dims_mod_business_ct_link set ".$field." = :idcontactto where ".$fielddest." = :idcontactdest and ".$field." = :idcontactfrom";

			$this->db->query($sql, array(
				':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
				':idcontactdest' => array('type' => PDO::PARAM_INT, 'value' => $idpers),
				':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
			));
		}

		// on va traiter les entreprises
		$sql="update dims_mod_business_tiers_contact set id_contact = :idcontactto where id_contact = :idcontactfrom";
		$this->db->query($sql, array(
			':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));

		// newsletter
		$sql="update dims_mod_newsletter_subscribed set id_contact = :idcontactto where id_contact = :idcontactfrom";
		$this->db->query($sql, array(
			':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));

		// RDV et autre event
		$sql="update dims_mod_business_action_detail set contact_id = :idcontactto where contact_id = :idcontactfrom";
		$this->db->query($sql, array(
			':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));

		// inscription des events
		$sql="update dims_mod_business_event_inscription set id_contact = :idcontactto where id_contact = :idcontactfrom";
		$this->db->query($sql, array(
			':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));

		// validation des etapes / user
		$sql="update dims_mod_business_event_etap_user set id_ee_contact = :idcontactto where id_ee_contact = :idcontactfrom";
		$this->db->query($sql, array(
			':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));

		// file created by user
		$sql="update dims_mod_business_event_etap_file_user set id_contact = :idcontactto where id_contact = :idcontactfrom";
		$this->db->query($sql, array(
			':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $ctfrom),
		));

		$ctfromct = new contact();
		$ctfromct->open($ctfrom);

		  // last update photo
		if ($this->fields['photo']=='' && $ctfromct->fields['photo']!='') {
			$this->fields['photo']=$ctfromct->fields['photo'];
			$this->save();
		}

		$ctfromct->delete();
		//die();
		// on supprime l'historique de l'ancien contact
	}

	public function getIdFavoriteLanguage(){
		return $this->getAttribut('favorite_language', parent::TYPE_ATTRIBUT_KEY);
	}

	private $favorite_language = null ;
	public function getFavoriteLanguage(){
		if(is_null($this->favorite_language)){
			$this->favorite_language = new language();
			$this->favorite_language->open($this->getIdFavoriteLanguage());
		}
		return $this->favorite_language ;
	}

	private $list_spoken_languages = null ;
	public function getSpokenLanguages(){
		if(is_null($this->list_spoken_languages)){
			$this->list_spoken_languages = contact_language::getSpokenLanguagesForContact($this->getId());
		}
		return $this->list_spoken_languages ;
	}

	public function getComments(){
		return $this->getAttribut('comments', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getAddress(){
		return $this->getAttribut('address', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getPostalCode(){
		return $this->getAttribut('postalcode', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getCity(){
		return $this->getAttribut('city', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getPhone(){
		return $this->getAttribut('phone', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getFax(){
		return $this->getAttribut('fax', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getMobile(){
		return $this->getAttribut('mobile', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getEmail(){
		return $this->getAttribut('email', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getBirthday(){
		return $this->getAttribut('birthday', parent::TYPE_ATTRIBUT_NUMERIC);
	}

	public function setBirthday($birthday, $save = false){
		$this->setAttribut('birthday', parent::TYPE_ATTRIBUT_NUMERIC, $birthday, $save);
	}

	public function getIdUser() {
		return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
	}

	public function getLastname() {
		return $this->getAttribut("lastname", self::TYPE_ATTRIBUT_STRING);
	}

	public function getFirstname() {
		return $this->getAttribut("firstname", self::TYPE_ATTRIBUT_STRING);
	}

	public function setFirstname($firstname, $save = false){
		$this->setAttribut("firstname", self::TYPE_ATTRIBUT_STRING, $firstname, $save);
	}

	public function setLastname($lastname, $save = false){
		$this->setAttribut("lastname", self::TYPE_ATTRIBUT_STRING, $lastname, $save);
	}

	public function setType($type, $save = false){
		$this->setAttribut("type", self::TYPE_ATTRIBUT_STRING, $type , $save);
	}

	public function setIdModule($id_module, $save = false){
		$this->setAttribut("id_module", self::TYPE_ATTRIBUT_KEY, $id_module, $save);
	}
	public function setIdWorkspace($id_workspace, $save = false){
		$this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workspace, $save);
	}

	public function setPostalcode($postalcode, $save = false){
		$this->setAttribut("postalcode", self::TYPE_ATTRIBUT_STRING, $postalcode, $save);
	}

	public function setCity($city, $save = false){
		$this->setAttribut("city", self::TYPE_ATTRIBUT_STRING, $city, $save);
	}

	public function setAddress($address, $save = false){
		$this->setAttribut("address", self::TYPE_ATTRIBUT_STRING, $address, $save);
	}

	public function getCanton() {
		return $this->getAttribut("canton", self::TYPE_ATTRIBUT_STRING);
	}

	public function setCanton($canton, $save = false){
		$this->setAttribut("canton", self::TYPE_ATTRIBUT_STRING, $canton, $save);
	}

	public function setCivilite($civilite, $save = false){
		$this->setAttribut("civilite", self::TYPE_ATTRIBUT_STRING, $civilite, $save);
	}

	public function getCivilite() {
		return $this->getAttribut("civilite", self::TYPE_ATTRIBUT_STRING);
	}

	public function setCountry($country, $save = false){
		$this->setAttribut("country", self::TYPE_ATTRIBUT_STRING, $country, $save);
	}

	public function setMobile($mobile, $save = false){
		$this->setAttribut("mobile", self::TYPE_ATTRIBUT_STRING, $mobile, $save);
	}

	public function setPhone($phone, $save = false){
		$this->setAttribut("phone", self::TYPE_ATTRIBUT_STRING, $phone, $save);
	}

	public function setFax($fax, $save = false) {
		$this->setAttribut("fax", self::TYPE_ATTRIBUT_STRING, $fax, $save);
	}

	public function setComments($comments, $save = false) {
		$this->setAttribut("comments", self::TYPE_ATTRIBUT_STRING, $comments, $save);
	}

	public function buildNewContactStep1($actionform='',$saveredirect='',$popupid) {
		$db=$this->db;
		global $dims;
		global $_DIMS;
		$this->init_description(); // met les champs a vides
		$contact = $this;
		unset($_SESSION['business']['contact_id']);
	?>

	<div >
		<div class="actions">
			<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $popupid; ?>');">
				<img src="modules/system/desktopV2/templates/gfx/common/close.png" />
			</a>
		</div>
		<h2>
			<?php
			echo $_DIMS['cste']['_DIMS_LABEL_CT_FICHE'];
			?>
		</h2>

		<div style="padding:2px;float:left;width:98%;height:600px;overflow:auto;"  id="formCRM">
		  <?
		  // creation de la fiche dynamique
		  $_SESSION['dims']['crm_newcontact_actionform'] = $actionform;
		  $_SESSION['dims']['crm_newcontact_saveredirect'] = $saveredirect;
		  include(DIMS_APP_PATH . "/modules/system/crm_public_contact_form.php");
		  ?>
		</div>

	</div>

	<?
	}

	/*
	 * Fonction permettant la modification des données
	 */
	public function buildContactForm($actionform='',$saveredirect='',$op='display') {
		$db = dims::getInstance()->getDb();
		global $dims;
		global $_DIMS;
		 // creation de la fiche dynamique
		  $_SESSION['dims']['crm_newcontact_actionform'] = $actionform;
		  $_SESSION['dims']['crm_newcontact_saveredirect'] = $saveredirect;
		 // echo "<table>";
		  $contact=$this;
		  include(DIMS_APP_PATH . "/modules/system/crm_public_contact_form.php");
		  //echo "</table>";
	}

	public function getIdGlobalobject() {
		return $this->getAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY);
	}

	public function setSexe($sexe, $save = false){
		$this->setAttribut("sexe", self::TYPE_ATTRIBUT_STRING, $sexe, $save);
	}

	public function getSexe() {
		return $this->getAttribut("sexe", self::TYPE_ATTRIBUT_STRING);
	}

	public function setEmail($email, $save = false){
		$this->setAttribut("email", self::TYPE_ATTRIBUT_STRING, $email, $save);
	}

	public function getNationalite() {
		return $this->getAttribut("nationalite", self::TYPE_ATTRIBUT_STRING);
	}

	public function setNationalite($nationalite, $save = false){
		$this->setAttribut("nationalite", self::TYPE_ATTRIBUT_STRING, $nationalite, $save);
	}

	// type de lien entre le contact et le tiers passé en paramètre
	public function getTypeLien($tiers_id) {
		$rs = $this->db->query('SELECT type_lien FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtier AND id_contact = :idcontact LIMIT 0, 1', array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $tiers_id),
		));
		if ($this->db->numrows($rs)) {
		$row = $this->db->fetchrow($rs);
		return $row['type_lien'];
		}
		else {
		return false;
		}
	}

	public function getTimestpModify() {
		return $this->getAttribut("timestp_modify", self::TYPE_ATTRIBUT_NUMERIC);
	}


	private  $has_account = null ;
	/**
	 * @complexity (requete=1, longueur=n)
	 * @return type
	 */
	public function hasAccount(){
	if($this->has_account == null){
		$db = dims::getInstance()->getDb();
		if (!isset($this->id_user)){
		$this->id_user = 0;
		$sel = "SELECT	id
			FROM	dims_user
			WHERE	id_contact = :idcontact";
		$res = $db->query($sel, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if ($db->numrows($res) > 0){
			$r = $db->fetchrow($res);
			$this->id_user = $r['id'];
		}
		}
		$this->has_account = ($this->id_user > 0);
	}
	return $this->has_account;
	}


	/**
	 * @complexity (requete=1, longueur=n) :: open
	 * @return type
	 */
	public function isActiveAccount(){
	if ($this->hasAccount()){
		require_once DIMS_APP_PATH.'/modules/system/class_user.php';
		$user = new user();
		$user->open($this->id_user);
		return $user->isActivated();
	}
	return false;
	}


	private $user = null ;
	/**
	 *
	 * @return user
	 */
	public function getUser(){
	require_once DIMS_APP_PATH.'/modules/system/class_user.php';
	if($this->user == null){
		$user = new user();
		$user->init_description();
		if (!isset($this->id_user)){
		if ($this->hasAccount())
			$user->open($this->id_user);
		}elseif($this->id_user > 0)
		$user->open($this->id_user);
		$this->user = $user;
	}
	return $this->user;
	}

	public function getDateCreate() {
	return $this->getAttribut("date_create", self::TYPE_ATTRIBUT_NUMERIC);
	}


	public function getType() {
	return $this->getAttribut("type", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getPhotoPath($size=60){
		$directory = DIMS_WEB_PATH.'data/photo_cts/contact_'.$this->fields['id'];
		$path = $directory.'/photo'.$size.$this->fields['photo'].'.png';
		if(file_exists($directory)){
			if(!file_exists($path)){
				require_once(DIMS_APP_PATH."/include/functions/image.php");
				$original = $directory.'/original.png';
				if(file_exists($original)){
					dims_resizeimage2($original, $size, $size,'png',$path);
				}
				else{//dernière chance
					$content = scandir($directory);
					if(!empty($content)){
						$pattern = '/^photo([0-9]+)_.*$/';
						$max = 0;
						$ref = '';
						foreach($content as $photo) {
							if(preg_match($pattern, $photo, $matches)){
								if($matches[1] > $max){
									$max = $matches[1];
									$ref = $photo;
								}
							}
						}
						if($max > 0 && $ref != ''){
							dims_resizeimage2($directory.'/'.$ref, $size, $size,'png',$path);
						}
					}
				}
			}
		}
		return $path;
	}
	public function getPhotoWebPath($size=60){
		return _DIMS_WEBPATHDATA.'photo_cts/contact_'.$this->fields['id'].'/photo'.$size.$this->fields['photo'].'.png';
	}

	public function getAllCompaniesLinked() {
		$employers = array();

		$res = $this->db->query("SELECT		distinct id_tiers
								 FROM		dims_matrix
								 WHERE		id_contact = :idcontact
								 ", array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while($tab = $this->db->fetchrow($res)){
			$employers[$tab['id_tiers']] = $tab['id_tiers'];
		}
		return $employers;
	}


	public function getAllContactsLinked() {
		$contacts = array();

		$res = $this->db->query("SELECT		distinct id_contact
								 FROM		dims_matrix
								 WHERE		id_contact2 = :idcontact
								 ", array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while($tab = $this->db->fetchrow($res)){
			$contacts[$tab['id_contact']] = $tab['id_contact'];
		}

		$res = $this->db->query("SELECT		distinct id_contact2
								 FROM		dims_matrix
								 WHERE		id_contact = :idcontact
								 ", array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while($tab = $this->db->fetchrow($res)){
			if (!isset($contacts[$tab['id_contact2']])) $contacts[$tab['id_contact2']] = $tab['id_contact2'];
		}

		return $contacts;
	}

	public function getCompaniesLinkedByType($type_link){
		$employers = array();
		$c = new dims_constant();
		$types = $c->getAllValues($type_link);
		$types = str_replace("'","''",$types);

		$i=0;
		$params = array();
		$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$res = $this->db->query("SELECT		tc.function, t.intitule, t.id, t.id_tiers, tc.id_ct_user_create, tc.date_create, tc.date_fin, tc.date_deb, t.id_globalobject
								 FROM		dims_mod_business_tiers_contact tc
								 INNER JOIN dims_mod_business_tiers t ON t.id = tc.id_tiers
								 WHERE		id_contact = :idcontact
								 AND		type_lien IN (".$this->db->getParamsFromArray($types, 'typelien', $params).")
								 AND		(date_fin = 0 OR ISNULL(date_fin) OR date_fin >= ".dims_createtimestamp().")
								 ORDER BY	tc.date_create DESC", $params);
		while($tab = $this->db->fetchrow($res)){
			$employers[] = $tab;
		}
		return $employers;
	}

	public function getAllCompaniesLinkedByType($type_link){
		require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
		require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
		$employers = array();
		$c = new dims_constant();
		$types = $c->getAllValues($type_link);
		$types = str_replace("'","''",$types);

		$i=0;
		$params = array();
		$db = dims::getInstance()->getDb();
		$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$res = $db->query("	SELECT		t.*, tc.function, tc.id_ct_user_create, tc.date_create, tc.date_fin, tc.date_deb
							FROM		".tiersct::TABLE_NAME." tc
							INNER JOIN 	".tiers::TABLE_NAME." t
							ON 			t.id = tc.id_tiers
							WHERE		id_contact = :idcontact
							AND			type_lien IN (".$this->db->getParamsFromArray($types, 'typelien', $params).")
							ORDER BY	tc.date_create DESC", $params);
		$sep = $db->split_resultset($res);
		foreach($sep as $r){
			$tiers = new tiers();
			$tiers->openFromResultSet($r['t']);
			$tiers->setLightAttribute('id_ct',$this->get('id'));
			$tiers->setLightAttribute('function',$r['tc']['function']);
			$tiers->setLightAttribute('id_ct_user_create',$r['tc']['id_ct_user_create']);
			$tiers->setLightAttribute('date_create',$r['tc']['date_create']);
			$tiers->setLightAttribute('date_fin',$r['tc']['date_fin']);
			$employers[] = $tiers;
		}
		return $employers;
	}

	public function getVcard(){
		$courPath = DIMS_APP_PATH;
		$dirExport = DIMS_ROOT_PATH.'tmp/vcardexport/';
		if (!file_exists($dirExport))
			mkdir($dirExport);
		$sid = session_id();
		if (!file_exists($dirExport.$sid))
			mkdir($dirExport.$sid);
		$photo = '';
		if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
			$photo = 'PHOTO;ENCODING=b;TYPE=PNG:'.base64_encode(file_get_contents($this->getPhotoWebPath(60)));
		$vcard = fopen($dirExport.$sid.'/'.$this->fields['firstname']."_".$this->fields['lastname'].".vcf",'w+');
	$org = (isset($this->fields['field13']) && trim($this->fields['field13']) != '')?"org:".trim($this->fields['field13']).";":"";
		$content = "begin:vcard
version:2.1
fn;CHARSET=utf-8:".dims_convertaccents($this->fields['firstname']." ".$this->fields['lastname'])."
n;CHARSET=utf-8:".dims_convertaccents($this->fields['lastname'].";".$this->fields['firstname'])."
adr;CHARSET=utf-8;TYPE=WORK:;;".$this->fields['address'].";".$this->fields['city'].";;".$this->fields['postalcode'].";".$this->fields['country']."
email;internet:".$this->fields['email']."
tel;work:".$this->fields['phone']."
".(isset($this->fields['field11']) && (trim($this->fields['field11']) != '')?"role:".trim($this->fields['field11']):"")."
$org
$photo
end:vcard
";

		fwrite($vcard,$content);
		fclose($vcard);
		chdir($courPath);
		return $dirExport.$sid.'/'.$this->fields['firstname']."_".$this->fields['lastname'].".vcf";
	}

	public function getLdif(){
	$courPath = realpath('.');
		$dirExport = DIMS_TMP_PATH . '/vcardexport/';
		if (!file_exists($dirExport))
			mkdir($dirExport);
		$sid = session_id();
		if (!file_exists($dirExport.$sid))
			mkdir($dirExport.$sid);
		$photo = '';
		$vcard = fopen($dirExport.$sid.'/'.$this->fields['firstname']."_".$this->fields['lastname'].".ldif",'w+');
	$org = (trim($this->fields['field13']) != '')?"org:".trim($this->fields['field13']).";":"";
		$content = "dn: cn=".$this->fields['firstname']." ".$this->fields['lastname'].((trim($this->fields['email']) != '')?",mail=".$this->fields['email']:"")."
objectclass: top
objectclass: person
givenName: ".$this->fields['firstname']."
sn: ".$this->fields['lastname']."
cn:".$this->fields['firstname']." ".$this->fields['lastname']."
mail: ".$this->fields['email']."
modifytimestamp: ".dims_timestamp2unix($this->fields['timestp_modify'])."
telephoneNumber: ".$this->fields['phone']."
street: ".$this->fields['address']."
l: ".$this->fields['city']."
postalCode: ".$this->fields['postalcode']."
c: ".$this->fields['country']."
o: ".trim($this->fields['field13'])."
";
		fwrite($vcard,$content);
		fclose($vcard);
		chdir($courPath);
		return $dirExport.$sid.'/'.$this->fields['firstname']."_".$this->fields['lastname'].".ldif";
	}

	public function updateIdCountry($countryArray = array()) {
		if(empty($countryArray)) {
			// conversion des tags vers country
			$resu=$this->db->query('SELECT * FROM dims_country');
			$c=0;
			if ($this->db->numrows($resu)>0) {
			while($a = $this->db->fetchrow($resu)) {
				$countryArray[strtoupper($a['printable_name'])]=$a['id'];
				$countryArray[strtoupper($a['fr'])]=$a['id'];
			}
			}
		}

		$id_country = 0;
		if ($this->fields['country']!='') {
			// traitement des pays type France - Europe

			$lieux=str_replace(array("-",";"),",",$this->fields['country']);

			$alieux=explode(',',$lieux);

			foreach ($alieux as $lieu) {
				//echo $lieu." ";
				$lieu=trim($lieu);
				$wordlength=strlen($lieu);

				if ($wordlength>0) {
				if (isset($countryArray[strtoupper($lieu)])) {
					$id_country=$countryArray[strtoupper($lieu)];
				}
				else {
					// recherche du pays pour ct / entreprise
					foreach ($countryArray as $country=>$idc) {
						$res = similar_text(trim(strtoupper($lieu)) ,substr($country,0,$wordlength),$percent);

						if ($percent>=80) {
							$id_country=$idc; // on a trouve le pays
							break;
						}
					}
				}
				}
			}

			if ($id_country>0) $this->db->query('UPDATE '.self::TABLE_NAME.' SET id_country = :idcountry WHERE '.self::TABLE_NAME.'.'.$this->idfields[0].' = :idcontact', array(
				':idcountry' => array('type' => PDO::PARAM_INT, 'value' => $id_country),
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));

			$this->fields['id_country']=$id_country;
		}
		return $id_country;
	}

	//Cyril - 19/01/2012 - fonction permettant de désactiver un contact
	public function desactive(){
		$this->fields['inactif'] = self::CONTACT_INACTIF;
	}
	//Cyril - 19/01/2012 - fonction permettant de resactiver un contact
	public function reactive(){
		$this->fields['inactif'] = self::CONTACT_ACTIF;
	}

	//Cyril - 19/01/2012 - fonction permettant de déterminer si le contact est actif ou non
	public function isActif(){
		return $this->fields['inactif'] == self::CONTACT_ACTIF;
	}

	//Cyril - 19/01/2012 - fonction permettant de dégager tous les liens d'un contact
	//fonction non-testée encore (mise en stand-by avant validation client du processus métier)
	public function cutMyLinks(){
		//on commence par les ct_links
		$this->db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact1= :idcontact id_object='.dims_const::_SYSTEM_OBJECT_CONTACT, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$this->db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact2= :idcontact id_object='.dims_const::_SYSTEM_OBJECT_CONTACT, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		//les tiers_contact
		$this->db->query('DELETE FROM dims_mod_business_tiers_contact WHERE id_contact= :idcontact', array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		//les keywords index
		$this->db->query('DELETE FROM dims_keywords_index WHERE id_globalobject= :idglobalobject', array(
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
		));
		//la matrice
		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		$matrix = new matrix();
		$matrix->purgeData('id_contact', $this->fields['id_globalobject']);
	}

	public function mergeSave(){
		$leven=array();
		if ($this->fields['firstname']!="" || $this->fields['lastname']!="") {

			$params=array();
			$params[':firstname']=trim($this->fields['firstname']);
			$params[':lastname']=trim($this->fields['lastname']);
			$params[':email']=trim($this->fields['email']);

			$resu=$this->db->query('SELECT * FROM dims_mod_business_contact where firstname like :firstname and lastname like :lastname OR (email !="" AND email = :email)',$params);
			$c=0;
			if ($this->db->numrows($resu)>0) {
				if($a = $this->db->fetchrow($resu)) {
					//echo "direct:";$a['id']."<br>";
					$this->open($a['id']);
					return $this->fields['id'];
				}

			}
			$leven = dims::getInstance()->dims_levenshtein($this->fields['firstname'],$this->fields['lastname'],1);

		}
		if (count($leven) > 0) {

			if (count($leven==1) && isset($leven[0]['id_contact'])) {
				//echo "leven:".$leven[0]['id_contact']."<br>";
				$this->open($leven[0]['id_contact']);
				return $this->fields['id'];
			}
			return $leven;
		}

		else{
			$this->setugm();
			$this->save();
			return $this->fields['id'];
		}
	}

	// Ben - 01/08/2012
	// ouvre un contact a partir l'id du user correspondant
	public function getByIdUser($id_user) {
		$rs = $this->db->query('
			SELECT c.*
			FROM dims_mod_business_contact c
			INNER JOIN dims_user u
			ON u.id_contact = c.id
			AND u.id = :iduser
			LIMIT 0, 1', array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		));
		if ($this->db->numrows($rs)) {
			$this->openFromResultSet($this->db->fetchrow($rs));
		}
	}

	// Ben - 01/07/2014
	// ouvre un contact a partir l'id de l'ERP
	public function getByERPId($erp_id) {
		$rs = $this->db->query('
			SELECT *
			FROM dims_mod_business_contact
			WHERE erp_id = :erp_id
			LIMIT 0, 1', array(
			':erp_id' => array('type' => PDO::PARAM_INT, 'value' => $erp_id),
		));
		if ($this->db->numrows($rs)) {
			$this->openFromResultSet($this->db->fetchrow($rs));
		}
	}

	public function linkToCompany($id_tiers) {
		$tiers = new tiers();
		if ($tiers->open($id_tiers)) {
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			if(!tiersct::isLinked($tiers->get('id'),$this->get('id'))){ // pour éviter les doublons
				$tc = new tiersct();
				$tc->fields['id_tiers'] = $id_tiers;
				$tc->fields['id_contact'] = $this->getId();
				$tc->fields['link_level'] = 2;
				$tc->fields['type_lien'] = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
				$tc->save();
			}
		}
	}

	public function getTiers() {
		if(is_null($this->myTiers)) {
			$this->myTier = new tiers();
			$this->myTier->open($this->fields['id_tiers']);
		}

		return $this->myTier;
	}

	public function getLabel(){
		return $this->fields['firstname']." ".$this->fields['lastname'];
	}

	public function getTypeObject(){
		return $_SESSION['cste']['_DIMS_LABEL_CONTACT'];
	}

	public function initFolder(){
		if ($this->fields['id_folder'] == '' || $this->fields['id_folder'] <= 0){
			require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
			$tmstp = dims_createtimestamp();
			$fold = new docfolder();
			$fold->init_description();
			$fold->fields['name'] = 'root_'.$this->fields['id_globalobject'];
			$fold->fields['parents'] = 0;
			$fold->setugm();
			$fold->fields['timestp_create'] = $tmstp;
			$fold->save();
			$this->fields['id_folder'] = $fold->fields['id'];
			$fold->save(); // pr la synchro
			$this->save();
		}
		return $this->get('id_folder');
	}
}
