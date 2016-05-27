<?php
/**
 * Description of ticket_status
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class ticket_status extends dims_data_object{
	const TABLE_NAME = "dims_ticket_status";

	const UNREAD = _TICKET_STATUT_UNREAD;
	const READ = _TICKET_STATUT_READ;
	const STARED = _TICKET_STATUT_STARED;
	const ARCHIVED = _TICKET_STATUT_ARCHIVED;
	const DELETED = _TICKET_STATUT_DELETED;

	public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getIdTicket() {
	return $this->getAttribut("id_ticket", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdUser() {
	return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
	}

	public function getStatus() {
	return $this->getAttribut("status", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getTimestp() {
	return $this->getAttribut("timestp", self::TYPE_ATTRIBUT_NUMERIC);
	}

	private $ticket = null ;

	public function getTicket(){
	if(is_null($this->ticket)){
		$this->ticket = new ticket();
		$this->ticket->open($this->getIdTicket());
	}
	return $this->ticket;
	}

	public function setTicket($ticket){
	$this->ticket = $ticket ;
	}

	const _INBOX = 1 ;
	const _JUNK = 2 ;
	const _ARCHIVES = 3;
	const _OUTBOX = 4;
	const _DRAFT = 5;

	/**
	 * Retourne la liste des tickets destinés à un destinataire et envoyé
	 * par une source donnée
	 *
	 * @param type $id_destinataire (un id d'utilisateur)
	 * @param type $id_source (un id d'utilisateur)
	 * @param type $type_ticket
	 * @return array of ticket_status
	 */
	public static function getTicketsForDestAndSender($id_destinataire, $id_source, $type_ticket){
	$list_tickets = array();
	 switch($type_ticket){
		case self::_INBOX :
		$listetype = array(self::UNREAD, self::READ, self::STARED);
		break;
		case self::_JUNK :
		$listetype = array(self::DELETED);
		break;
		case self::_ARCHIVES :
		$listetype = array(self::ARCHIVED);
		break;
		default :
		//bad parameters
		return $list_tickets ;
	}
	$params = array();
	$sql = "SELECT * FROM ".self::TABLE_NAME."
		INNER JOIN ".ticket::TABLE_NAME."
		ON ".self::TABLE_NAME.".id_ticket = ".ticket::TABLE_NAME.".id
		INNER JOIN ".user::TABLE_NAME."
		ON ".ticket::TABLE_NAME.".id_user = ".user::TABLE_NAME.".id
		WHERE ".self::TABLE_NAME.".status IN (".$this->db->getParamsFromArray($listetype, 'status', $params).")
		AND ".self::TABLE_NAME.".id_user = :iduserdest
		AND ".ticket::TABLE_NAME.".id_user = :iduserfrom
		AND ".ticket::TABLE_NAME.".status != ".ticket::DRAFT."
		";

	$params[':iduserfrom'] = array('type' => PDO::PARAM_INT, 'value' => $id_source);
	$params[':iduserdest'] = array('type' => PDO::PARAM_INT, 'value' => $id_destinataire);
	$list_tickets = self::doGettingForTickets($sql, $params);
	return $list_tickets;
	}

	/**
	 * Retourne la liste des tickets destinés à l'utilisateur connecté.
	 * @param type $id_user
	 * @param type $type_ticket
	 * @return array
	 */
	public static function getTicketsForUser($id_user, $type_ticket){
	$list_tickets = array();

	switch($type_ticket){
		case self::_INBOX :
		$listetype = array(self::UNREAD, self::READ, self::STARED);
		break;
		case self::_JUNK :
		$listetype = array(self::DELETED);
		break;
		case self::_ARCHIVES :
		$listetype = array(self::ARCHIVED);
		break;
		default :
		//bad parameters
		return $list_tickets ;
	}
	$params = array();
	$sql = "SELECT * FROM ".self::TABLE_NAME."
		INNER JOIN ".ticket::TABLE_NAME."
		ON ".self::TABLE_NAME.".id_ticket = ".ticket::TABLE_NAME.".id
		INNER JOIN ".user::TABLE_NAME."
		ON ".ticket::TABLE_NAME.".id_user = ".user::TABLE_NAME.".id
		AND ".self::TABLE_NAME.".id_user = :iduser
		AND ".ticket::TABLE_NAME.".status != ".ticket::DRAFT;

	$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $id_user);
	$list_tickets = self::doGettingForTickets($sql, $params);
	return $list_tickets;
	}

	private static function doGettingForTickets($sql, $params){
	$list_tickets = array();
	$list_id_ticket = array() ;

	$db = dims::getInstance()->getDb() ;

	$res = $db->query($sql, $params);

	$separation = $db->split_resultset($res);
	foreach ($separation as $row) {
		$ticket_status = new ticket_status();
		$ticket_status->openWithFields($row[ticket_status::TABLE_NAME]);

		$ticket = new ticket();
		$ticket->openWithFields($row[ticket::TABLE_NAME]);

		$user = new user();
		$user->openWithFields($row[user::TABLE_NAME]);
		$ticket->setUser($user);

		$ticket_status->setTicket($ticket);
		$list_tickets[$ticket->getId()] = $ticket_status;

		$list_id_ticket[] = $ticket->getId() ;
	}

	if(!empty($list_id_ticket)){
		/*Chargement des pièces jointes*/
		$liste_to = ticket_object::getListTicketObjectForListIdTicket($list_id_ticket);
		foreach ($liste_to as $ticket_object) {

		if(isset($list_tickets[$ticket_object->getIdTicket()])){
			$list_tickets[$ticket_object->getIdTicket()]->getTicket()->addObjetsJoints($ticket_object);
		}
		}
		/*Chargement des sender de type groupe*/
		$list_group = ticket::getGroupSendersForListIdTicket($list_id_ticket);
		foreach ($list_group as $key => $group){
		if(isset($list_tickets[$key])){
			$list_tickets[$key]->getTicket()->setGroupe($group);
		}
		}
	}

	return $list_tickets ;
	}

	/**
	 * Fonction qui contribue à l'envoi d'un ticket en définissant à UNREAD le statut
	 * pour tous les utilisateurs concernés.
	 * @param type $id_ticket - id du ticket
	 * @param type $liste_dest - liste d'id des destinataires
	 */
	public static function sendTicket($id_ticket, $liste_dest) {
	if($id_ticket > 0 && !empty($liste_dest)){
		$first = true ;
		$timestp = dims_createtimestamp();

		$sql = "INSERT INTO `".self::TABLE_NAME."`
		(`id`, `id_user`, `id_ticket`, `status`, `timestp`) VALUES ";

		$i = 0;
		$params = array();
		foreach ($liste_dest as $id_dest) {
			if(!$first){
				$sql .= " , ";
			}
			$i++;
			$first = false ;
			$sql .= "(NULL, :iddest".$i.", :idticket".$i.", ".self::UNREAD.", :timestamp)";
			$params[':iddest'.$i] = array('type' => PDO::PARAM_INT, 'value' => $id_dest);
			$params[':idticket'.$i] = array('type' => PDO::PARAM_INT, 'value' => $id_ticket);
			$params[':timestamp'.$i] = array('type' => PDO::PARAM_INT, 'value' => $timestp);
		}

		$sql .= ";";

		$db = dims::getInstance()->getDb();
		$db->query($sql, $params);
	}
	}
}
?>
