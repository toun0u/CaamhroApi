<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class tiersct extends dims_data_object {
	const TABLE_NAME = "dims_mod_business_tiers_contact";

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	function save() {
		// verification si new
		if ($this->isNew()) {
			$this->set('id_ct_user_create',$_SESSION['dims']['user']['id_contact']);
			$this->set('id_user',$_SESSION['dims']['userid']);
			$this->set('date_create',dims_createtimestamp());
			// on ajoute une surcouche pour la creation d'une action entre la personne et l'entreprise
			require_once(DIMS_APP_PATH.'modules/system/class_contact.php');
			require_once(DIMS_APP_PATH.'modules/system/class_tiers.php');
			require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');

			if ($this->fields['link_level'] == 2 && $this->fields['type_lien'] == $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']){
				$c = new dims_constant();
				$types = $c->getAllValues('_DIMS_LABEL_EMPLOYEUR');
				$types = str_replace("'","''",$types);
				$in = "'".implode("','",$types)."'";
				$tiers = new tiers();
				$tiers->open($this->fields['id_tiers']);
				$ct = new contact();
				$ct->open($this->fields['id_contact']);

				$matrice = new matrix();
				$matrice->init_description(true);
				$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
				$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
				$matrice->fields['year'] = substr($this->fields['date_create'],0,4);
				$matrice->fields['month'] = substr($this->fields['date_create'],4,2);
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$matrice->save();
				$sel = "SELECT		DISTINCT ".contact::TABLE_NAME.".id_globalobject
					FROM		".contact::TABLE_NAME."
					INNER JOIN	".self::TABLE_NAME."
					ON		".self::TABLE_NAME.".link_level = 2
					AND		".self::TABLE_NAME.".type_lien IN (".$in.")
					AND		".self::TABLE_NAME.".id_tiers = :idtiers
					AND		".contact::TABLE_NAME.".id = ".self::TABLE_NAME.".id_contact";
				$db = dims::getInstance()->getDb();
				$res = $this->db->query($sel, array(
					':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_tiers'])
				));
				while ($r = $this->db->fetchrow($res)){
					$matrice = new matrix();
					$matrice->init_description(true);
					$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
					$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
					$matrice->fields['id_contact2'] = $r['id_globalobject'];
					$matrice->fields['year'] = substr($this->fields['date_create'],0,4);
					$matrice->fields['month'] = substr($this->fields['date_create'],4,2);
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();
					$matrice = new matrix();
					$matrice->init_description(true);
					$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
					$matrice->fields['id_contact2'] = $ct->fields['id_globalobject'];
					$matrice->fields['id_contact'] = $r['id_globalobject'];
					$matrice->fields['year'] = substr($this->fields['date_create'],0,4);
					$matrice->fields['month'] = substr($this->fields['date_create'],4,2);
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();
				}
			}
		}
		if($this->get('id_user') == '' || $this->get('id_user') <=0)
			$this->set('id_user',$_SESSION['dims']['userid']);
		if($this->get('id_workspace') == '' || $this->get('id_workspace') <=0)
			$this->set('id_workspace',$_SESSION['dims']['workspaceid']);
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		parent::delete();
	}

	public function getIdTiers(){
		return $this->getAttribut('id_tiers', parent::TYPE_ATTRIBUT_KEY);
	}

	public function getIdContact(){
		return $this->getAttribut('id_contact', parent::TYPE_ATTRIBUT_KEY);
	}

	public function getService(){
		return $this->getAttribut('service', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getFunction(){
		return $this->getAttribut('function', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getDepartement(){
		return $this->getAttribut('departement', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getCommentaire(){
		return $this->getAttribut('commentaire', parent::TYPE_ATTRIBUT_STRING);
	}


	public static function getListContactByIdTiers($id_tiers, $type_link = 0){
		$liste_tierscontact = array() ;

		$db = dims::getInstance()->getDb();

		if($type_link == 0){
			$sql = "SELECT * FROM ".self::TABLE_NAME."
				INNER JOIN ".contact::TABLE_NAME."
				ON ".self::TABLE_NAME.".id_contact = ".contact::TABLE_NAME.".id
				WHERE ".self::TABLE_NAME.".id_tiers = :idtiers
				";
			$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
		}else{
			$sql = "SELECT * FROM ".self::TABLE_NAME."
				INNER JOIN ".contact::TABLE_NAME."
				ON ".self::TABLE_NAME.".id_contact = ".contact::TABLE_NAME.".id
				WHERE ".self::TABLE_NAME.".id_tiers = :idtiers
				AND ".self::TABLE_NAME.".type_lien = :typelink
				";
			$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
			$params[':typelink'] = array('type' => PDO::PARAM_INT, 'value' => $type_link);
		}

		$res = $db->query($sql, $params);
		$separation = $db->split_resultset($res);
		foreach ($separation as $row) {
			$tierscontact = new tiersct();
			$tierscontact->openWithFields($row[tiersct::TABLE_NAME]);

			$contact = new contact();
			$contact->openWithFields($row[contact::TABLE_NAME]);

			$tierscontact->setContact($contact);

			$liste_tierscontact[$tierscontact->getId()] = $tierscontact ;
		}
		return $liste_tierscontact;
	}

	private $contact = null ;
	public function getContact(){
		if(is_null($this->contact)){
			$this->contact = new contact();
			$this->contact->open($this->getIdContact());
		}
		return $this->contact ;
	}

	public function setContact(contact $contact){
		$this->contact = $contact ;
	}

	public function setIdTiers($id_tiers, $save = false){
		$this->setAttribut("id_tiers", self::TYPE_ATTRIBUT_KEY, $id_tiers, $save);
	}

	public function setIdContact($id_contact, $save = false){
		$this->setAttribut("id_contact", self::TYPE_ATTRIBUT_KEY, $id_contact, $save);
	}

	public function setTypeLien($type_lien, $save = false){
		$this->setAttribut("type_lien", self::TYPE_ATTRIBUT_STRING, $type_lien, $save);
	}

	public function setDateCreate($date_create, $save = false){
		$this->setAttribut("date_create", self::TYPE_ATTRIBUT_NUMERIC, $date_create, $save);
	}

	public function setIdWorkspace($id_workspace, $save = false){
		$this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workspace, $save);
	}

	public static function isLinked($id_tiers, $id_contact) {
		if ($id_tiers != null && $id_contact != null) {
			$db = dims::getInstance()->getDb();
			$rs = $db->query('SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtiers AND id_contact = :idcontact', array(
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
				':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $id_tiers),
			));
			return $db->numrows($rs);
		}
		else return false;
	}
}
?>
