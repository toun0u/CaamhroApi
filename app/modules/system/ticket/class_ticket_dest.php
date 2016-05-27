<?php

/**
 * Description of ticket_dest
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class ticket_dest extends dims_data_object{
    const TABLE_NAME = "dims_ticket_dest";

    const DESTINATAIRE_USER = _TICKET_USER;
    const DESTINATAIRE_GROUPE = _TICKET_GROUP ;

    const TYPE_DESTINATAIRE_FOR = 1 ;
    const TYPE_DESTINATAIRE_CC = 2 ;
    const TYPE_DESTINATAIRE_CCI = 3 ;

    public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdDestinataire() {
	return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdTicket() {
	return $this->getAttribut("id_ticket", self::TYPE_ATTRIBUT_KEY);
    }

    public function getTypeDestinataire() {
	return $this->getAttribut("type_destinataire", self::TYPE_ATTRIBUT_KEY);
    }

    public function getTypeLienDestinataire() {
	return $this->getAttribut("type_lien_destinataire", self::TYPE_ATTRIBUT_KEY);
    }

    private $user = null ;
    private $group = null ;

    /**
     *
     * @return user
     */
    private function getUser(){
	if($this->user == null){
	    $this->user = new user();
	    $this->user->open($this->getIdDestinataire());
	}
	return $this->user;
    }

    /**
     *
     * @return group
     */
    private function getGroup(){
	if($this->group == null){
	    $this->group = new group();
	    $this->group->open($this->getIdDestinataire());
	}
	return $this->group;
    }

    public function getDest(){
	switch($this->getTypeDestinataire()){
	    case self::DESTINATAIRE_USER :
		return $this->getUser();
		break;
	    case self::DESTINATAIRE_GROUPE :
		return $this->getGroup();
		break;
	    default :
				return false ;
		//Error - type de sender erroné
	}
    }

    public function setDest($dest){
	if($dest instanceof group){
	    $this->group = $dest ;
	}else if($dest instanceof  user){
	    $this->user = $dest ;
	}
    }

    public function setGroup($dest){
	$this->group = $dest ;
    }

    public function setUser($dest){
	$this->user = $dest ;
    }

    /**
     *
     * @param type $id_ticket
     * @return array of ticket_dest linked to group and/or user
     */
    public static function getDestinatairesForTicket($id_ticket){
	$list_destinataire = array();

	$db = dims::getInstance()->getDb();

	$sql = "SELECT * from ".self::TABLE_NAME."
	    INNER JOIN ".group::TABLE_NAME."
	    ON ".self::TABLE_NAME.".id_destinataire = ".group::TABLE_NAME.".id
	    WHERE ".self::TABLE_NAME.".type_destinataire = ".self::DESTINATAIRE_GROUPE."
	    AND ".self::TABLE_NAME.".id_ticket = :idticket
	    ";

	$res = $db->query($sql, array(
		':idticket' => array('type' => PDO::PARAM_INT, 'value' => $id_ticket),
	));

	$separation = $db->split_resultset($res);
	foreach ($separation as $row) {
	    $ticket_dest =  new ticket_dest();
	    $ticket_dest->openWithFields($row[ticket_dest::TABLE_NAME]);

	    $group = new group();
	    $group->openWithFields($row[group::TABLE_NAME]);

	    $ticket_dest->setGroup($group);

	    $list_destinataire[$ticket_dest->getId()] = $ticket_dest ;
	}

	$sql = "SELECT * from ".self::TABLE_NAME."
	    INNER JOIN ".user::TABLE_NAME."
	    ON ".self::TABLE_NAME.".id_destinataire = ".user::TABLE_NAME.".id
	    WHERE ".self::TABLE_NAME.".type_destinataire = ".self::DESTINATAIRE_USER."
	    AND ".self::TABLE_NAME.".id_ticket = :idticket ";

	$res = $db->query($sql, array(
		':idticket' => array('type' => PDO::PARAM_INT, 'value' => $id_ticket),
	));

	$separation = $db->split_resultset($res);
	foreach ($separation as $row) {
	    $ticket_dest =  new ticket_dest();
	    $ticket_dest->openWithFields($row[ticket_dest::TABLE_NAME]);

	    $user = new user();
	    $user->openWithFields($row[user::TABLE_NAME]);

	    $ticket_dest->setUser($user);

	    $list_destinataire[$ticket_dest->getId()] = $ticket_dest ;
	}

	return $list_destinataire ;
    }

    /**
     *
     * @param int $id_ticket
     * @param array $liste_dest doit être de la forme suivante :<br/>
     * $liste_dest = array <br/>
     *	    | [ticket_dest::TYPE_DESTINATAIRE_CC] = array <br/>
     *		# ['id'] = id du destinataire <br/>
     *		# ['type] = type du destinaire groupe(DESTINATAIRE_GROUPE) ou user (DESTINATAIRE_USER)<br/>
     *	    | [ticket_dest::TYPE_DESTINATAIRE_CCI] = array<br/>
     *		# idem ci-dessus.<br/>
     *	    | [ticket_dest::TYPE_DESTINATAIRE_FOR] = array<br/>
     *		# idem ci-dessus.<br/>
     */
    public static function addDestinataire($id_ticket, array $liste_dest) {
	if(!empty($liste_dest)) {
	    $first = true ;

	    $sql = "INSERT INTO `".self::TABLE_NAME."` (`id`, `id_destinataire`, `type_destinataire`, `type_lien_destinataire`, `id_ticket`) VALUES ";
	    $params = array();
	    foreach ($liste_dest as $key => $liste_sous_ensemble_dest) {
			foreach ($liste_sous_ensemble_dest as $dest_for) {
				if(!$first){
					$sql .= " , ";
				}
				$first = false ;
				$sql .= "(NULL, ? , ? , ? , ? )";
				$params[] = $dest_for['id'];
				$params[] = $dest_for['type'];
				$params[] = $key;
				$params[] = $idticket;
			}
	    }

	    if(!$first) {
		$sql .= ";";

		$db = dims::getInstance()->getDb();
		$db->query($sql, $params);
	    }
	}


    }
}
?>
