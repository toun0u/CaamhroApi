<?php
include_once DIMS_APP_PATH.'modules/system/class_city.php';
include_once DIMS_APP_PATH.'modules/system/class_country.php';
include_once DIMS_APP_PATH.'modules/system/class_tiers.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_user.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_const.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_facture.php';

class client extends pagination {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	const TABLE_NAME			= 'dims_mod_cata_client';
	const ID_FIELD				= 'id_client';
	const MY_GLOBALOBJECT_CODE	= 232;

	const STATUS_OK				= 0;
	const STATUS_BLOCKED		= 1;

	const TYPE_PROFESSIONAL		= 0;
	const TYPE_PARTICULAR		= 1;

	// business_tiers lié au client
	private $tiers = null;

	// dims_user lié au client et correspondant à l'utilisateur principal
	private $user = null;

	// propriété du tiers qu'on a pas dans le client
	private $nic = '';
	private $ape = '';
	private $siren = '';

	private $paginated = true;


	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, self::ID_FIELD);
		$this->to_index(array('nom', 'code_client'));
	}

	public function setid_object() {
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle() {
		$this->title = $this->getName();
	}

	public function save() {
		if ($this->isNew()) {
			$this->fields['date_cree'] = dims_createtimestamp();
		}

		// mise a jour du business_tiers correspondant
		$this->updateTiers();

		// Création de l'utilisateur principal si non existant
		if ($this->fields['dims_user'] == 0) {
			$this->createMainUser();
		}
		else $this->updateMainUser();

		// Création d'une adresse de livraison si non existante
		if (!sizeof($this->getDepots())) {
			$this->createMainDepot();
		}

		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function save_lite() {
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function getById($id) {
		$rs = $this->db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE id_client = '.$id.' LIMIT 0, 1');
		if ($this->db->numrows($rs)) {
			$this->openFromResultSet($this->db->fetchrow($rs));
			return $this;
		}
		else {
			return null;
		}
	}

	public function openByCode($code){
		$res = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE code_client = '".$code."' LIMIT 1");
		if($this->db->numrows($res)){
			$fields = $this->db->fetchrow($res);
			$this->openFromResultSet($fields);
		}
		else{
			$this->init_description();
			$this->setugm();
		}
	}

	public function updateTiers() {
		require_once DIMS_APP_PATH.'/modules/system/class_tiers.php';
		$tiers = new tiers();

		if (!empty($this->fields['tiers_id'])) {
			$tiers->open($this->fields['tiers_id']);
		}
		else {
			$tiers->init_description();
			$tiers->setugm();
		}

		$tiers->setIntitule($this->getName());
		$tiers->setMel($this->getEmail());
		$tiers->setNic($this->nic);
		$tiers->setApe($this->ape);
		$tiers->setSiren($this->siren);
		$tiers->setAdresse($this->getAddress());
		$tiers->setAdresse2($this->getAddress2());
		$tiers->setAdresse3($this->getAddress3());
		$tiers->setCodePostal($this->getPostalCode());
		$tiers->setVille($this->getCity());
		$tiers->setPays($this->getCountry());
		$tiers->setIdPays($this->getCountryId());
		$tiers->save(dims_const::_SYSTEM_OBJECT_TIERS);

		// on affecte le tiers au client
		$this->setTiers($tiers);

		parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function setUserinfos($values) {
		$this->userinfos = $values;
	}

	//Cyril - ajouté depuis la billetterie
	private function updateMainUser(){
		$user = user::find_by(array('id' => $this->get('dims_user')), null, 1);
		if(!empty($this->userinfos) && is_array($this->userinfos)) {
			if(!empty($user) && ! $user->isNew()){
				$user->fields['lastname']	= $this->userinfos['lastname'];
				$user->fields['firstname']	= $this->userinfos['firstname'];
				$user->fields['login']		= $this->getLogin();
				if(! is_null($this->userinfos['salt'])){
					$user->fields['password']	= $this->getHashedPassword();
					$user->fields['salt']		= isset($this->userinfos['salt']) ? $this->userinfos['salt'] : '';
				}
				$user->fields['email']		= $this->getEmail();
				$user->fields['phone']		= $this->getTel1();
				$user->fields['fax']		= $this->getFax();
				$user->fields['mobile']		= $this->getMobile();
				$user->fields['address']	= $this->getAddress().' '.$this->getAddress2().' '.$this->getAddress3();
				$user->fields['postalcode']	= $this->getPostalCode();
				$user->fields['city']		= $this->getCity();
				$user->save();
			}
		}
	}

	private function createMainUser() {
		if(!empty($this->userinfos) && is_array($this->userinfos)) {
			require_once DIMS_APP_PATH.'/modules/system/class_user.php';
			require_once DIMS_APP_PATH.'/modules/catalogue/include/class_param.php';

			// chargement du niveau d'utilisateur par défaut
			$lstParam = cata_param::initComptesClients();

			$user = new user();
			$user->fields['date_creation']	= dims_createtimestamp();
			$user->fields['lastname']	= $this->userinfos['lastname'];
			$user->fields['firstname']	= $this->userinfos['firstname'];
			$user->fields['login']		= $this->getLogin();
			$user->fields['password']	= $this->getHashedPassword();
			$user->fields['salt']		= isset($this->userinfos['salt']) ? $this->userinfos['salt'] : '';
			$user->fields['email']		= $this->getEmail();
			$user->fields['phone']		= $this->getTel1();
			$user->fields['fax']		= $this->getFax();
			$user->fields['mobile']		= $this->getMobile();
			$user->fields['address']	= $this->getAddress().' '.$this->getAddress2().' '.$this->getAddress3();
			$user->fields['postalcode']	= $this->getPostalCode();
			$user->fields['city']		= $this->getCity();
			$user->save();

			// On met à jour l'adresse et téléphone sur le contact
			$contact = $user->getContact();
			$contact->fields['address'] 	= $this->getAddress();
			$contact->fields['address2'] 	= $this->getAddress2();
			$contact->fields['address3'] 	= $this->getAddress3();
			$contact->fields['postalcode'] 	= $this->getPostalCode();
			$contact->fields['city'] 		= $this->getCity();
			$contact->fields['phone'] 		= $this->getTel1();
			$contact->save();

			$firstAddress = new address();
			$firstAddress->init_description();
			$firstAddress->setugm();

			$firstAddress->fields['address']    = $contact->fields['address'];
			$firstAddress->fields['address2']   = $contact->fields['address2'];
			$firstAddress->fields['address3']   = $contact->fields['address3'];
			$firstAddress->fields['postalcode'] = $contact->fields['postalcode'];
			$firstAddress->fields['city']       = $contact->fields['city'];

			$firstAddress->save();

			$firstAddress->addLink($contact->get('id_globalobject'));

			$this->setUser($user->getId());

			$this->setLogin($user->fields['login']);

			$this->fields['dims_user'] = $user->get('id');

			if(empty($this->fields['dims_group'])) {
				// ouverture du groupe racine
				$parent_group = group::getByCode('CLIENTS');

				if ($parent_group != null) {
					// creation du groupe
					$child = $parent_group->createchild();
				} else {
					$child = new group();
				}

				$child->fields['label'] = $this->getName();
				$child->fields['protected'] = 0;
				$child->fields['id_workspace'] = 0;
				$child->save();

				$this->fields['dims_group'] = $child->get('id');
			} else {
				$child = new group();
				$child->open($this->fields['dims_group']);
			}

			$user->attachtogroup($child->get('id'), $lstParam['default_lvl_registration']->getValue());

			//parent::save();
		}
	}

	// On prend le code client pour le login
	// Si on a plusieurs utilisateurs, on suffixe les suivants
	public function getUniqueLogin() {
		$login = $this->fields['code_client'];

		$resexist = $this->db->query("SELECT `login` FROM `dims_user` WHERE `login` LIKE '".$login."%'");
		$suffixe = 0;
		$count = $this->db->numrows($resexist);
		while ($tab = $this->db->fetchrow($resexist)) {
			$current = $tab["login"];

			if (preg_match('/_[0-9]+$/', $current)) {
				$cursuf = substr($current, strpos($current, '_') + 1);
				if($cursuf >= $suffixe) $suffixe = $cursuf;
			}
		}
		$login .= ($count==0)?'':'_'.($suffixe+1);

		return $login;
	}

	public function getGroup() {
		// Création du groupe du client
		if(empty($this->fields['dims_group'])) {
			// ouverture du groupe racine
			$parent_group = group::getByCode('CLIENTS');

			// creation du groupe
			$child = $parent_group->createchild();
			$child->fields['label'] = $this->getName();
			$child->fields['protected'] = 0;
			$child->fields['id_workspace'] = 0;
			$child->fields['code'] = '';
			$child->save();

			$this->fields['dims_group'] = $child->get('id');
			parent::save(self::MY_GLOBALOBJECT_CODE);
		}
		else {
			$child = new group();
			$child->open($this->fields['dims_group']);
		}

		return $child;
	}

	private function createMainDepot() {
		require_once DIMS_APP_PATH.'/modules/catalogue/include/class_cata_depot.php';
		$depot = new cata_depot();
		$depot->setClient($this->getCode());
		$depot->setErpId($this->get('erp_id_adr'));
		$depot->setNumDepot($this->getCode());
		$depot->setNomLivr($this->getLivName());
		$depot->setAddress1($this->getLivAddress1());
		$depot->setAddress2($this->getLivAddress2());
		$depot->setPostalCode($this->getLivPostalCode());
		$depot->setCountry($this->getCountryId());
		$depot->setCity($this->getLivCity());
		$depot->save();
	}

	public function setPaginated($val){
		$this->paginated = $val;
	}

	#Cyril -> 28/11/2012 <- Construction de la liste des clients
	public function build_index($status = self::STATUS_OK, $keywords = '', $pagination = false, $sort_by = 'code', $sort_way = 'ASC', $all_clients = false){
		$db = dims::getInstance()->getDb();

		if ($this->paginated && !$pagination) {
			$this->total_index = $this->build_index($status, $keywords, true, $sort_by, $sort_way);
			pagination::liste_page($this->total_index);
			$limit = " LIMIT ".$this->sql_debut.", ".$this->limite_key;
		}
		else $limit="";
		$where = "WHERE 1=1 ";

		if (!empty($keywords)) {
			include_once DIMS_APP_PATH.'/modules/system/class_search.php';
			$dims = dims::getInstance();
			$dimsearch = new search($dims);
			global $_DIMS;
			// ajout des objects sur lequel la recherche va se baser
			$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], self::MY_GLOBALOBJECT_CODE, '');
			// reinitialise la recherche sur ce module courant, n'efface pas le cache result
			$dimsearch->initSearchObject();

			$dimsearch->executeSearch2($keywords, '', $_SESSION['dims']['moduleid'], self::MY_GLOBALOBJECT_CODE, 0, '');

			#Récupération des id_globalobject de clients correspondant aux keywords
			$go_ids = array_keys($dimsearch->tabresultat[$_SESSION['dims']['moduleid']][self::MY_GLOBALOBJECT_CODE]);
			if(!empty($go_ids)){
				$where .= " AND c.id_globalobject IN (".implode(',', $go_ids).")";
			}
			else{
				$where .= " AND c.id_globalobject IN (-1) ";//sinon il ressortira tous les résultats sans tenir compte des mots clefs pourris
			}
		}

		if ( !$all_clients && !empty($_SESSION['dims']['user']['representative_id']) ) {
			$where .= " AND c.representative_id = ".$_SESSION['dims']['user']['representative_id'];
		}

		switch($status){
			case self::STATUS_OK:
				$where .= ' AND c.bloque = \''.self::STATUS_OK.'\'';
				break;
			case self::STATUS_BLOCKED:
				$where .= ' AND c.bloque = \''.self::STATUS_BLOCKED.'\'';
				break;
			default: //Tous
				break;
		}

		$order_by = ' ORDER BY ';
		if( $sort_by == 'code' ) {
			$order_by .= 'c.code_client ';
		}
		else {
			$order_by .= 'c.nom ';
		}
		$order_by .= $sort_way;

		$sql = 'SELECT c.* FROM '.self::TABLE_NAME.' c '.$where.' '.$order_by.' '.$limit;

		$res = $this->db->query($sql);

		if ($pagination) {
			return $this->db->numrows($res);
		}
		else {
			$lst = array();
			while($fields = $this->db->fetchrow($res)){
				$cli = new client();
				$cli->openFromResultSet($fields);
				$lst[] = $cli;
			}
			return $lst;
		}
	}

	// GETTERS
	public function getTiers() {
		if ($this->tiers == null) {
			$tiers = new tiers();
			if (isset($this->fields['tiers_id']) && !($this->fields['tiers_id'] > 0 && $tiers->open($this->fields['tiers_id']))) {
				$tiers->init_description();
				$tiers->setugm();
			}
			$this->tiers = $tiers;
		}
		return $this->tiers;
	}

	public function assignTiers($t){
		$this->tiers = $t;
	}

	public function getMainUser() {
		if ($this->user == null) {
			$user = new cata_user();
			if (!($this->fields['dims_user'] > 0 && $user->open($this->fields['dims_user']))) {
				$user->init_description();
				$user->setugm();
			}
			$this->user = $user;
		}
		return $this->user;
	}

	public function getCode() {
		return stripslashes($this->fields['code_client']);
	}

	private function getLogin() {
		return stripslashes($this->fields['login']);
	}

	private function getHashedPassword() {
		return $this->fields['password'];
	}

	public function getInitialPassword() {
		return $this->fields['librcha2'];
	}

	public function getName() {
		return stripslashes($this->fields['nom']);
	}

	public function isBlocked() {
		return $this->fields['bloque'] == 1;
	}

	public function isProfessional() {
		return $this->fields['type'] == self::TYPE_PROFESSIONAL;
	}

	public function isParticular() {
		return $this->fields['type'] == self::TYPE_PARTICULAR;
	}

	public function hasComment() {
		return $this->fields['commentaire'] != '';
	}

	public function getCommentRaw() {
		return stripslashes(str_replace('\r\n', "\r\n", $this->fields['commentaire']));
	}

	public function getCommentHTML() {
		return stripslashes(str_replace('\r\n', '<br/>', $this->fields['commentaire']));
	}

	public function getDateCreation() {
		$date_creation = dims_timestamp2local($this->fields['date_cree']);
		return $date_creation['date'];
	}

	public function getUserCreation() {
		if ($this->fields['id_user'] > 0) {
			$user = new user();
			$user->open($this->fields['id_user']);
			return $user->fields['firstname'].' '.$user->fields['lastname'];
		}
		else {
			return dims_constant::getVal('NOT_SPECIFIED');
		}
	}

	public function getLogo($size) {
		$image = '';
		if (file_exists($this->getTiers()->getPhotoPath($size))) {
			$image = '<img src="'.$this->getTiers()->getPhotoWebPath($size).'" alt="'.$this->getName().'" />';
		}
		return $image;
	}

	public function getEmail() {
		return stripslashes($this->fields['email']);
	}

	public function getSiren() {
		return $this->getTiers()->getSiren();
	}

	public function getNic() {
		return $this->getTiers()->getNic();
	}

	public function getApe() {
		return $this->getTiers()->getApe();
	}

	public function getAddress() {
		return stripslashes($this->fields['adr1']);
	}

	public function getAddress2() {
		return stripslashes($this->fields['adr2']);
	}

	public function getAddress3() {
		return stripslashes($this->fields['adr3']);
	}

	public function getPostalCode() {
		return stripslashes($this->fields['cp']);
	}

	public function getCity() {
		return stripslashes($this->fields['ville']);
	}

	public function getCityId() {
		$city = city::getByLabel($this->getcity(), $this->getCountryId());
		if ($city != null) {
			return $city->get('id');
		}
		else {
			return 0;
		}
	}

	public function getCountryId() {
		return $this->fields['id_pays'];
	}

	public function getCountry() {
		$country = new country();
		$country->open($this->getCountryId());
		return $country->getLabel();
	}

	public function getLivName() {
		if ($this->fields['liv_nom'] != '') {
			return stripslashes($this->fields['liv_nom']);
		}
		else {
			return stripslashes($this->fields['nom']);

		}
	}

	public function getLivAddress1() {
		if ($this->fields['liv_adr1'] != '') {
			return stripslashes($this->fields['liv_adr1']);
		}
		else {
			return stripslashes($this->fields['adr1']);
		}
	}

	public function getLivAddress2() {
		if ($this->fields['liv_adr2'] != '') {
			return stripslashes($this->fields['liv_adr2']);
		}
		else {
			return stripslashes($this->fields['adr2']);
		}
	}

	public function getLivPostalCode() {
		if ($this->fields['liv_cp'] != '') {
			return stripslashes($this->fields['liv_cp']);
		}
		else {
			return stripslashes($this->fields['cp']);
		}
	}

	public function getLivCity() {
		if ($this->fields['liv_ville'] != '') {
			return stripslashes($this->fields['liv_ville']);
		}
		else {
			return stripslashes($this->fields['ville']);
		}
	}

	public function getTel1() {
		return $this->fields['tel1'];
	}

	public function getFax() {
		return $this->fields['fax'];
	}

	public function getMobile() {
		return $this->fields['port'];
	}

	public function setTel1($tel) {
		$this->fields['tel1'] = $tel;
	}

	public function setFax($fax) {
		$this->fields['fax'] = $fax;
	}

	public function setMobile($port) {
		$this->fields['port'] = $port;
	}

	// setters
	public function setUser($id_user) {
		$this->fields['id_user'] = $id_user;
	}

	public function setModule($id_module) {
		$this->fields['id_module'] = $id_module;
	}

	public function setWorkspace($id_workspace) {
		$this->fields['id_workspace'] = $id_workspace;
	}

	// génération du code client
	public function generateCode() {
		// Recherche du code client à affecter
		$rs = $this->db->query('SELECT MAX(RIGHT(code_client, LENGTH(code_client) - 4)) AS max FROM dims_mod_cata_client WHERE code_client LIKE \'WEB_%\'');
		$row = $this->db->fetchrow($rs);
		$max = $row['max'];
		$this->setCode($codeclient = "WEB_". sprintf("%06d", $max + 1));
	}

	public function setCode($code) {
		$this->fields['code_client'] = $code;
	}

	public function setProfessional() {
		$this->fields['type'] = self::TYPE_PROFESSIONAL;
	}

	public function setParticular() {
		$this->fields['type'] = self::TYPE_PARTICULAR;
	}

	public function setTiers($tiers) {
		$this->tiers = $tiers;
		$this->fields['tiers_id'] = $tiers->get('id');
	}

	public function block() {
		$this->fields['bloque'] = 1;
	}

	public function unblock() {
		$this->fields['bloque'] = 0;
	}

	public function setName($name) {
		$this->fields['nom'] = $name;
	}

	public function setType($type) {
		$this->fields['type'] = $type;
	}

	public function setCommentaire($commentaire) {
		$this->fields['commentaire'] = $commentaire;
	}

	public function setEmail($email) {
		$this->fields['email'] = $email;
	}

	public function setNic($nic) {
		$this->nic = $nic;
	}

	public function setApe($ape) {
		$this->ape = $ape;
	}

	public function setSiren($siren) {
		$this->siren = $siren;
	}

	public function setAddress1($adr) {
		$this->fields['adr1'] = $adr;
	}

	public function setAddress2($adr) {
		$this->fields['adr2'] = $adr;
	}

	public function setAddress3($adr) {
		$this->fields['adr3'] = $adr;
	}

	public function setPostalCode($cp) {
		$this->fields['cp'] = $cp;
	}

	public function setCity($id_city) {
		$city = new city();
		$city->open($id_city);
		$this->fields['ville'] = $city->getLabel();
	}

	public function setCityLabel($cityLabel) {
		$this->fields['ville'] = $cityLabel;
	}

	public function setCountry($id_country) {
		$this->fields['id_pays'] = $id_country;
	}

	public function setLivName($nom) {
		$this->fields['liv_nom'] = $nom;
	}

	public function setLivAddress1($adr) {
		$this->fields['liv_adr1'] = $adr;
	}

	public function setLivAddress2($adr) {
		$this->fields['liv_adr2'] = $adr;
	}

	public function setLivAddress3($adr) {
		$this->fields['liv_adr3'] = $adr;
	}

	public function setLivCP($cp) {
		$this->fields['liv_cp'] = $cp;
	}

	public function setLivCity($city) {
		$this->fields['liv_ville'] = $city;
	}

	public function setLivCityId($id_city) {
		$city = new city();
		$city->open($id_city);
		$this->fields['liv_ville'] = $city->getLabel();
	}

	public function setLivCountry($id_country) {
		$this->fields['liv_id_pays'] = $id_country;
	}

	public function setLogin($login) {
		$this->fields['login'] = $login;
		$this->setLibrcha1($login);
	}

	public function setPassword($password) {
		$this->fields['password'] = dims_getPasswordHash($password);
		$this->setLibrcha2($password);
	}

	public function setLibrcha1($text) {
		$this->fields['librcha1'] = $text;
	}

	public function setLibrcha2($text) {
		$this->fields['librcha2'] = $text;
	}

	public function setEscompte($escompte) {
		$this->fields['escompte'] = $escompte;
	}

	public function setMinimumCde($minimum_cde) {
		$this->fields['minimum_cde'] = $minimum_cde;
	}

	public function setFranco($franco) {
		$this->fields['franco'] = $franco;
	}

	public function setPaymentMeans($a_paymentMeans = array()) {
		if(!empty($a_paymentMeans)) {
			$this->db->query('DELETE FROM `'.client_moyen_paiement::TABLE_NAME.'` WHERE id_client = '.$this->get('id_client'));
			foreach ($a_paymentMeans as $mp_id) {
				$this->db->query('INSERT INTO `'.client_moyen_paiement::TABLE_NAME.'` (id_client, id_moyen_paiement) VALUES ('.$this->get('id_client').', '.$mp_id.')');
			}
		}
	}

	public function getPrixNets(){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".cata_prix_nets::TABLE_NAME."
				WHERE	type = 'C'
				AND 	code_cm = '".$this->fields['code_client']."'
				AND		(datedeb = '00000000000000' OR datedeb <= '".dims_createtimestamp()."')
				AND		(datefin = '00000000000000' OR datefin >= '".dims_createtimestamp()."')";
		$lst = array();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$elem = new cata_prix_nets();
			$elem->openFromResultSet($r);
			$lst[$r['reference']] = $elem;
		}
		return $lst;
	}

	public function createPrixNets($ref, $puht){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
		$elem = new cata_prix_nets();
		$elem->open($this->fields['code_client'],$ref);
		if($elem->isNew()){
			$elem = new cata_prix_nets();
			$elem->init_description();
			$elem->fields['datedeb'] = dims_createtimestamp();
			$elem->fields['datefin'] = '00000000000000';
		}
		$elem->fields['type'] = 'C';
		$elem->fields['code_cm'] = $this->fields['code_client'];
		$elem->fields['reference'] = $ref;
		$elem->fields['puht'] = $puht;
		$elem->save();
	}

	// renvoie tous les utilisateurs attachés au client
	public function getAllUsers() {
		$a_users = array();
		$rs = $this->db->query('
			SELECT 	u.*
			FROM 	dims_user u
			INNER JOIN 	dims_group_user gu
			ON 			gu.id_user = u.id
			INNER JOIN 	dims_mod_cata_client c
			ON 			c.dims_group = gu.id_group
			AND 		c.id_client = '.$this->get('id_client'));
		while ($row = $this->db->fetchrow($rs)) {
			$user = new user();
			$user->openFromResultSet($row);
			$a_users[] = $user;
		}
		return $a_users;
	}

	public function getDepots(){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".cata_depot::TABLE_NAME."
				WHERE	client = '".$this->fields['code_client']."'
				ORDER BY depot";
		$lst = array();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$elem = new cata_depot();
			$elem->openFromResultSet($r);
			$lst[$r['depot']] = $elem;
		}
		return $lst;
	}

	public function getAdressesFacturations() {
		include_once DIMS_APP_PATH."modules/catalogue/include/class_facturation.php";
		include_once DIMS_APP_PATH."modules/catalogue/include/class_client_ratt.php";

		$db = dims::getInstance()->getDb();
		$sel = "SELECT	f.*, c.*
				FROM	".client_ratt::TABLE_NAME." c
				INNER JOIN	".cata_facturation::TABLE_NAME." f
				ON		f.id = c.facturation_id
				WHERE	c.code_client_enfant = '".$this->getCode()."'";
		$lst = array();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$elem = new cata_facturation();
			$elem->openFromResultSet($r);
			$lst[$r['id']] = $elem;
		}
		return $lst;
	}

	public function getComptesFacturations() {
		include_once DIMS_APP_PATH."modules/catalogue/include/class_client_ratt.php";

		$db = dims::getInstance()->getDb();
		$sel = "SELECT	c.*, cr.*
				FROM	".client_ratt::TABLE_NAME." cr
				INNER JOIN	".self::TABLE_NAME." c
				ON		c.code_client = cr.code_client_parent
				AND 	cr.code_client_parent != ''
				WHERE	cr.code_client_enfant = '".$this->getCode()."'";
		$lst = array();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$elem = new client();
			$elem->openFromResultSet($r);
			$lst[$r['code_client']] = $elem;
		}
		return $lst;
	}

	public function getAdrFact() {
		$a_adrfact = array(
			'C'.$this->getCode() => array(
				'type'		=> 'C',
				'id'		=> $this->getCode(),
				'nom'		=> stripslashes($this->fields['nom']),
				'adr1'		=> stripslashes($this->fields['adr1']),
				'adr2'		=> stripslashes($this->fields['adr2']),
				'adr3'		=> stripslashes($this->fields['adr3']),
				'cp'		=> stripslashes($this->fields['cp']),
				'ville'		=> stripslashes($this->fields['ville']),
				'force'		=> 0
				));

		$rs = $this->db->query('SELECT
				IF (cr.facturation_id = 0, "C", "F") AS type,
				IF (cr.facturation_id = 0, c.code_client, f.id) as id,
				IF (cr.facturation_id = 0, c.nom, f.nom) as nom,
				IF (cr.facturation_id = 0, c.adr1, f.adr1) as adr1,
				IF (cr.facturation_id = 0, c.adr2, f.adr2) as adr2,
				IF (cr.facturation_id = 0, c.cp, f.cp) as cp,
				IF (cr.facturation_id = 0, c.ville, f.ville) as ville,
				cr.force,
				cr.valid_oblig
			FROM	dims_mod_cata_client_ratt cr

			LEFT JOIN	dims_mod_cata_client c
			ON			c.code_client = cr.code_client_parent

			LEFT JOIN	dims_mod_cata_facturation f
			ON			f.id = cr.facturation_id

			WHERE	cr.code_client_enfant = "'.$this->getCode().'"');

		while ($row = $this->db->fetchrow($rs)) {
			foreach ($row as $k => $v) { $row[$k] = stripslashes($v); }

			// si l'adresse est forcée, elle sera la seule disponible
			if ($row['force']) {
				$a_adrfact = array();
				$a_adrfact["{$row['type']}{$row['id']}"] = $row;
				break;
			}

			$a_adrfact["{$row['type']}{$row['id']}"] = $row;
		}
		return $a_adrfact;
	}

	public function getService(){
		include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
		$elem = new cata_group();
		$elem->open($this->fields['dims_group']);
		if($elem->isNew()){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_module.php';
			$root = module_cata::getInstance()->getRootGroup();

			$elem = new cata_group();
			$elem->init_description();
			$elem->fields['label'] = $this->getName();
			$elem->fields['id_group'] = $root->get('id');
			$elem->fields['parents'] = $root->fields['parents'].";".$root->get('id');
			$elem->fields['id_workspace'] = $root->fields['id_workspace'];
			$elem->save();

			$this->fields['dims_group'] = $elem->get('id');
			$this->save();
		}
		return $elem;
	}

	public function getVignette($size = 60){
		$tiers = $this->getTiers();
		$path = $tiers->getPhotoPath($size);
		if( file_exists($path) ){
			return $tiers->getPhotoWebPath($size);
		}
		else return null;
	}

	// Renvoie la liste des modes de paiement disponibles
	public function getPaymentMeans() {
		// soit le client a un ou des moyens de règlement fixés
		$a_paymentMeans = $this->getFixedPaymentMeans();

		// soit il a accès à tous les moyens de paiement activés
		if (!sizeof($a_paymentMeans)) {
			$mods = dims::getInstance()->getModuleByType('catalogue');
			$a_paymentMeans = moyen_paiement::getActivePaiement($mods[0]['instanceid']);
		}

		// si le client n'est pas un pro, il n'a pas accès au paiement différé
		if ($this->isParticular()) {
			unset($a_paymentMeans[moyen_paiement::_TYPE_DIFFERE]);
		}

		return $a_paymentMeans;
	}

	// Renvoie la liste des modes de paiement fixés pour le client
	public function getFixedPaymentMeans() {
		require_once DIMS_APP_PATH.'modules/catalogue/include/class_moyen_paiement.php';
		require_once DIMS_APP_PATH.'modules/catalogue/include/class_client_moyen_paiement.php';

		$a_paymentMeans = array();

		// soit le client a un ou des moyens de règlement fixés
		$rs = $this->db->query('
			SELECT	mp.*
			FROM	`'.moyen_paiement::TABLE_NAME.'` mp
			INNER JOIN	`'.client_moyen_paiement::TABLE_NAME.'` cmp
			ON			cmp.id_client = '.$this->get('id_client').'
			AND			cmp.id_moyen_paiement = mp.id');
		while ($row = $this->db->fetchrow($rs)) {
			$mp = new moyen_paiement();
			$mp->openFromResultSet($row);
			$a_paymentMeans[$mp->getType()] = $mp;
		}

		return $a_paymentMeans;
	}

	public function getquotations() {
		$quotations = array();

		if(!$this->isNew()) {
			$quotations = cata_facture::find_by(array(
				'id_client' => $this->getId(),
				'type'      => cata_facture::TYPE_QUOTATION,
				'deleted'   => 0,
			));
		}

		return $quotations;
	}

	// Static functions
	public static function getAllSelect(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		*
				FROM		".self::TABLE_NAME."
				WHERE		id_module = ".$_SESSION['dims']['moduleid']."
				ORDER BY	nom";
		$res = $db->query($sel);
		$lst = array();
		$lst[0] = dims_constant::getVal('_DIMS_ALLS');
		while($r = $db->fetchrow($res)){
			$lst[$r['id_client']] = $r['nom'];
		}
		return $lst;
	}

	public static function codeExists($code_client) {
		$db = dims::getInstance()->getDb();
		$rs = $db->query("SELECT id_client FROM ".self::TABLE_NAME." WHERE code_client = '".$code_client."' LIMIT 1");
		return $db->numrows($rs);
	}

	public static function findbyuser(user $user) {
		$db = dims::getInstance()->getDb();
		$client = new self();

		$rs = $db->query('
			SELECT      c.*
			FROM        dims_mod_cata_client c
			INNER JOIN  dims_group_user gu
			ON          c.dims_group = gu.id_group
			WHERE       gu.id_user = :userid',
			array(
				':userid' => array('type' => PDO::PARAM_INT, 'value' => $user->get('id')),
			)
		);

		if($db->numrows($rs)) {
			$row = $db->fetchrow($rs);

			$client->openFromResultSet($row);
		}

		return $client;
	}
}
