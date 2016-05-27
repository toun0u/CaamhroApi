<?php

/**
 * Description of class_tickets
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class ticket extends dims_data_object{
	const TABLE_NAME = "dims_ticket";
	const TYPE_SENDER_GROUP = _TICKET_GROUP;
	const TYPE_SENDER_USER = _TICKET_USER ;
	const TYPE_TICKET_DEFAULT = _TICKET_TYPE_DEFAULT ;

	const DRAFT = _TICKET_STATUT_DRAFT;
	const ARCHIVE = _TICKET_STATUT_ARCHIVE;
	const DELETED = _TICKET_STATUT_DELETED;
	const UNREAD = _TICKET_STATUT_UNREAD;
	const READ = _TICKET_STATUT_READ;

	public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getTitle(){
	return $this->getAttribut("title", self::TYPE_ATTRIBUT_STRING);
	}

	public function getMessage() {
	return $this->getAttribut("message", self::TYPE_ATTRIBUT_STRING);
	}

	public function isNeededValidation() {
	return $this->getAttribut("needed_validation", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT);
	}

	public function getTimeLimit() {
	return $this->getAttribut("time_limit", self::TYPE_ATTRIBUT_STRING);
	}

	public function isDeliveryNotification() {
	return $this->getAttribut("delivery_notification", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT);
	}

	public function getStatus() {
	return $this->getAttribut("status", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getObjectLabel() {
	return $this->getAttribut("object_label", self::TYPE_ATTRIBUT_STRING);
	}

	public function getTimestp() {
	return $this->getAttribut("timestp", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getLastreplyTimestp() {
	return $this->getAttribut("lastreply_timestp", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getCountRead() {
	return $this->getAttribut("count_read", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getCountReplies() {
	return $this->getAttribut("count_replies", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getIdUser() {
	return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdGroupe() {
	return $this->getAttribut("id_groupe", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdObject() {
	return $this->getAttribut("id_object", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdModule() {
	return $this->getAttribut("id_module", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdRecord() {
	return $this->getAttribut("id_record", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdWorkspace() {
	return $this->getAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY);
	}

	public function getParentId() {
	return $this->getAttribut("parent_id", self::TYPE_ATTRIBUT_KEY);
	}

	public function getRootId() {
	return $this->getAttribut("root_id", self::TYPE_ATTRIBUT_KEY);
	}

	public function isDeleted() {
	return $this->getAttribut("deleted", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT);
	}

	public function getIdModuletype() {
	return $this->getAttribut("id_module_type", self::TYPE_ATTRIBUT_KEY);
	}

	public function setTitle($title, $save = false){
	$this->setAttribut("title", self::TYPE_ATTRIBUT_STRING, $title, $save);
	}

	public function setMessage($message, $save = false){
	$this->setAttribut("message", self::TYPE_ATTRIBUT_STRING, $message, $save);
	}

	public function setNeededValidation($needed_validation, $save = false){
	$this->setAttribut("needed_validation", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT, $needed_validation, $save);
	}

	public function setTimeLimit($time_limit, $save = false){
	$this->setAttribut("time_limit", self::TYPE_ATTRIBUT_STRING, $time_limit, $save);
	}

	public function setDeliveryNotification($delivery_notification, $save = false){
	$this->setAttribut("delivery_notification", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT, $delivery_notification, $save);
	}

	public function setStatus($status, $save = false){
	$this->setAttribut("status", self::TYPE_ATTRIBUT_NUMERIC, $status, $save);
	}

	public function setObjectLabel($object_label, $save = false){
	$this->setAttribut("object_label", self::TYPE_ATTRIBUT_STRING, $object_label, $save);
	}

	public function setTimestp($timestp, $save = false){
	$this->setAttribut("timestp", self::TYPE_ATTRIBUT_NUMERIC, $timestp, $save);
	}

	public function setLastReplyTimestp($lastreply_timestp, $save = false){
	$this->setAttribut("lastreply_timestp", self::TYPE_ATTRIBUT_NUMERIC, $lastreply_timestp, $save);
	}

	public function setCountRead($count_read, $save = false){
	$this->setAttribut("count_read", self::TYPE_ATTRIBUT_NUMERIC, $count_read, $save);
	}

	public function setCountReplies($count_replies, $save = false){
	$this->setAttribut("count_replies", self::TYPE_ATTRIBUT_NUMERIC, $count_replies, $save);
	}

	public function setIdObject($id_object, $save = false){
	$this->setAttribut("id_object", self::TYPE_ATTRIBUT_KEY, $id_object, $save);
	}

	public function setIdModule($id_module, $save = false){
	$this->setAttribut("id_module", self::TYPE_ATTRIBUT_KEY, $id_module, $save);
	}

	public function setIdRecord($id_record, $save = false){
	$this->setAttribut("id_record", self::TYPE_ATTRIBUT_KEY, $id_record, $save);
	}

	public function setIdUser($id_user, $save = false){
	$this->setAttribut("id_user", self::TYPE_ATTRIBUT_KEY, $id_user, $save);
	}

	public function setIdGroupe($id_groupe, $save = false){
	$this->setAttribut("id_groupe", self::TYPE_ATTRIBUT_KEY, $id_groupe, $save);
	}

	public function setIdWorkspace($id_workspace, $save = false){
	$this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workspace , $save);
	}

	public function setParentId($parent_id, $save = false){
	$this->setAttribut("parent_id", self::TYPE_ATTRIBUT_KEY, $parent_id, $save);
	}

	public function setRootId($root_id, $save = false){
	$this->setAttribut("root_id", self::TYPE_ATTRIBUT_KEY, $root_id, $save);
	}

	public function setDeleted($deleted, $save = false){
	$this->setAttribut("deleted", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT, $deleted , $save);
	}

	public function setIdModuleType($id_module_type, $save = false){
	$this->setAttribut("id_module_type", self::TYPE_ATTRIBUT_KEY, $id_module_type, $save);
	}

	public function incrementReadingCount(){
	$cpt = $this->getCountRead() ;
	$this->setCountRead($cpt+1, true);
	}

	public function incrementRepliesCount(){
	$this->setCountReplies($this->getCountReplies(), true);
	}

	private $group = null ;
	/**
	 * Retourne l'entité ayant envoyé le message. Ceci peut être un groupe, ou
	 * un utilisateur.
	 * @return group or user
	 */
	public function getSender(){
	if($this->getIdGroupe() > 0){
		return $this->getGroupe();
	}else{
		return $this->getUser();
	}
	}

	/**
	 *
	 * @return group
	 */
	public function getGroupe(){
	if(is_null($this->group)){
		$this->group = new group();
		$this->group->open($this->getIdGroupe());
	}
	return $this->group ;
	}

	public function setGroupe(group $group){
	$this->group = $group ;
	}

	public function setSender($sender){
	if($sender instanceof group){
		$this->group = $sender ;
	}else if($sender instanceof user){
		$this->user = $user ;
	}
	}

	private $user = null ;
	/**
	 *
	 * @return user
	 */
	public function getUser(){
	if(is_null($this->user)){
		$this->user = new user();
		$this->user->open($this->getIdUser());
	}
	return $this->user;
	}

	public function setUser(user $user){
	$this->user = $user ;
	}

	private $liste_objets_joints = null ;

	public function getListeObjetsJoints(){
	if(is_null($this->liste_objets_joints)){
		$this->liste_objets_joints = array();
		//todo faire la fonction dans class_ticket_object
	}
	return $this->liste_objets_joints;
	}

	public function setListeObjetsJoints(array $liste_oj){
	$this->liste_objets_joints = $liste_oj ;
	}

	public function addObjetsJoints(ticket_object $ticket_object){
	if(is_null($this->liste_objets_joints)){
		$this->liste_objets_joints = array() ;
	}
	$this->liste_objets_joints[$ticket_object->getId()] = $ticket_object;
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasObjetJoint(){
	if(empty($this->liste_objets_joints)){
		return false ;
	}else{
		return true ;
	}
	}

	public function setListDestinataire(array $list_destinataire){
	$this->list_destinataire = $list_destinataire;
	}

	public function getListDestinataire(){
	if($this->list_destinataire == null){
		$this->list_destinataire = ticket_dest::getDestinatairesForTicket($this->getId());
	}
	return $this->list_destinataire;
	}

	public function setWatchByUser($watch_by_user){
	$this->watch_by_user = $watch_by_user ;
	}

	public function isWatchByUser($id_user){
	if($this->watch_by_user == null){
		ticket_watch::isWatchingForIdUserAndTicket($id_user, $this->getId());
	}
	return $this->watch_by_user ;
	}

	private $list_destinataire = null ;
	private $watch_by_user = null ;

	/**
	 * Requête les tables dest, object, watch et ticket
	 * @return ticket
	 */
	public static function getTicketInformationsById($id_ticket){
	$ticket = new ticket() ;

	$db = dims::getInstance()->getDb();

	$sql = "SELECT * FROM ".self::TABLE_NAME."
		INNER JOIN ".user::TABLE_NAME."
		ON ".self::TABLE_NAME.".id_user = ".user::TABLE_NAME.".id
		LEFT OUTER JOIN ".group::TABLE_NAME."
		ON ".self::TABLE_NAME.".id_groupe = ".group::TABLE_NAME.".id
		WHERE ".self::TABLE_NAME.".id = :idticket
		";

	$res = $db->query($sql, array(
		':idticket' => array('type' => PDO::PARAM_INT, 'value' => $id_ticket),
	));

	$separation = $db->split_resultset($res);

	if(!empty ($separation)){
		$row = array_shift($separation);

		$ticket->openWithFields($row[ticket::TABLE_NAME]);

		$user = new user();
		$user->openWithFields($row[user::TABLE_NAME]);
		$ticket->setUser($user);

		if($ticket->getIdGroupe() > 0){
		$group = new group();
		$group->openWithFields($row[group::TABLE_NAME]);
		$ticket->setGroupe($group);
		}

		$list_destinataire = ticket_dest::getDestinatairesForTicket($id_ticket);
		$ticket->setListDestinataire($list_destinataire);

		$is_watching = ticket_watch::isWatchingForIdUserAndTicket(dims::getInstance()->getUserId(), $id_ticket);
		$ticket->setWatchByUser($is_watching);

		$nb_object = ticket_object::getCountOfObjetJointsForIdTicket($id_ticket);
		$ticket->setCountObjetsJoints($nb_object);

	}


	//ouverture avec requete sur destinataire
	//ouverture avec requete pour les watch
	//ouverture pour les objets

	return $ticket;
	}

	private $count_objets_joints = null;

	public function getCountObjetsJoints(){
	if($this->count_objets_joints == null){
		if($this->liste_objets_joints != null){
		   $this->count_objets_joints = count($this->liste_objets_joints);
		}else{
		$this->count_objets_joints = ticket_object::getCountOfObjetJointsForIdTicket($this->getId());
		}
	}
	return $this->count_objets_joints;
	}
	public function setCountObjetsJoints($count_oj){
	$this->count_objets_joints = $count_oj;
	}

	public static function getGroupSendersForListIdTicket($liste_id_ticket){
	$liste_sender = null ;
	$params = array();

	$db = dims::getInstance()->getDb() ;

	$sql = "SELECT * from ".self::TABLE_NAME."
		INNER JOIN ".group::TABLE_NAME."
		ON ".self::TABLE_NAME.".id_groupe = ".group::TABLE_NAME.".id
		WHERE ".self::TABLE_NAME.".id_groupe != 0
		AND ".self::TABLE_NAME.".id IN (".$db->getParamsFromArray($liste_id_ticket, 'ticketid', $params).") ";

	$res = $db->query($sql, $params);

	$separation = $db->split_resultset($res);
	foreach ($separation as $row) {
		$ticket = new ticket() ;
		$ticket->openWithFields($row[ticket::TABLE_NAME]);

		$group = new group();
		$group->openWithFields($row[group::TABLE_NAME]);

		$liste_sender[$ticket->getId()] = $group ;
	}

	return $liste_sender ;
	}

	/**
	 *
	 * @param type $title
	 * @param type $message
	 * @param type $dest
	 * @param type $notifiying
	 * @param type $id_user
	 * @param type $id_groupe
	 * @param type $parent_id
	 * @param type $root_id
	 * @return int id du message envoyé, 0 si échec
	 */
	public static function sendMessage($title, $message,
		$list_dest_cc_gr, $list_dest_cc_user,
		$list_dest_for_gr, $list_dest_for_user,
		$list_dest_cci_gr, $list_dest_cci_user,
		$list_objets_joints, $notifiying, $id_user,
		$id_groupe, $parent_id, $root_id, $watching, $draft_save = false){

	$id_ticket = 0 ;

	if($title != ''){
		$ticket = new ticket();

		$ticket->setTitle($title);
		$ticket->setMessage($message);
		$ticket->setDeliveryNotification($notifiying);
		$ticket->setIdUser($id_user);
		$ticket->setIdGroupe($id_groupe);
		$ticket->setParentId($parent_id);
		$ticket->setRootId($root_id);
		if($draft_save){
		$ticket->setStatus(self::DRAFT);
		}else{
		$ticket->setStatus(self::UNREAD);
		}
		$ticket->setTimestp(dims_createtimestamp());

		$ticket->setIdModule(dims_const::_DIMS_MODULE_SYSTEM);
		$ticket->setIdWorkspace(dims::getInstance()->getCurrentWorkspaceId());

		$ticket->save();

		$id_ticket = $ticket->getId();

		if($watching){
		$watch = new ticket_watch();
		$watch->setIdTicket($id_ticket);
		$watch->setIdUser($id_user);
		$watch->setActive(true);
		$watch->setTypeNotification(ticket_watch::WATCH_TYPE_NOTIFICATION_MAIL);
		$watch->save();
		}

		$liste_dest = array();
		$list_group = array();
		$list_user	= array();
		//todo with ticket_dest
		$i = 0 ;
		if(!empty($list_dest_cc_gr)){
		foreach ($list_dest_cc_gr as $dest_cc_gr) {
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CC][$i]['id'] = $dest_cc_gr ;
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CC][$i]['type'] = ticket::TYPE_SENDER_GROUP ;
			$i++ ;
			$list_group[$dest_cc_gr] = $dest_cc_gr ;
		}
		}
		if(!empty($list_dest_cc_user)){
		foreach ($list_dest_cc_user as $dest_cc_user) {
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CC][$i]['id'] = $dest_cc_user ;
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CC][$i]['type'] = ticket::TYPE_SENDER_USER ;
			$i++ ;
			$list_user[$dest_cc_user] = $dest_cc_user;
		}
		}
		$i = 0;
		if(!empty($list_dest_cci_gr)){
		foreach ($list_dest_cci_gr as $dest_cci_gr) {
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CCI][$i]['id'] = $dest_cci_gr ;
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CCI][$i]['type'] = ticket::TYPE_SENDER_GROUP ;
			$i++ ;
			$list_group[$dest_cci_gr] = $dest_cci_gr;
		}
		}
		if(!empty($list_dest_cci_user)){
		foreach ($list_dest_cci_user as $dest_cci_user) {
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CCI][$i]['id'] = $dest_cci_user ;
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_CCI][$i]['type'] = ticket::TYPE_SENDER_USER ;
			$i++ ;
			$list_user[$dest_cci_user] = $dest_cci_user;
		}
		}
		$i = 0;
		if(!empty($list_dest_for_gr)){
		foreach ($list_dest_for_gr as $dest_for_gr) {
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_FOR][$i]['id'] = $dest_for_gr ;
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_FOR][$i]['type'] = ticket::TYPE_SENDER_GROUP ;
			$i++ ;
			$list_group[$dest_for_gr] = $dest_for_gr;
		}
		}
		if(!empty($list_dest_for_user)){
		foreach ($list_dest_for_user as $dest_for_user) {
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_FOR][$i]['id'] = $dest_for_user ;
			$liste_dest[ticket_dest::TYPE_DESTINATAIRE_FOR][$i]['type'] = ticket::TYPE_SENDER_USER ;
			$i++ ;
			$list_user[$dest_for_user] = $dest_for_user;
		}
		}

		ticket_dest::addDestinataire($id_ticket, $liste_dest);

		//on requête la liste des utilisateurs pour tous les groupes.
		$list_user_from_grp = group_user::getIdUsersForListGroups($list_group);

		foreach ($list_user_from_grp as $id_user) {
		$list_user[$id_user] = $id_user;
		}

		ticket_status::sendTicket($id_ticket, $list_user);

		if(!empty($list_objets_joints)){
		ticket_object::addListeLinkedObjects($id_ticket, $list_objets_joints);
		}

	}

	return $id_ticket ;

	}

	public static function getTicketsBySender($id_user, $type_ticket=ticket_status::_OUTBOX) {
	$liste_ticket = array();

	switch($type_ticket){
		case ticket_status::_OUTBOX :
		$listetype = array(self::READ, self::UNREAD);
		break;
		case ticket_status::_DRAFTS :
		$listetype = array(self::DRAFT);
		break;
		default :
		//bad parameters
		return $liste_ticket ;
	}

	if($id_user > 0 && !empty ($listetype)){
		$lgroup = group_user::getIdGroupsForUserId($id_user);
		$db = dims::getInstance()->getDb();

		$params = array();
		if(!empty($lgroup)){
		$sql = "SELECT * FROM ".self::TABLE_NAME."
			WHERE (id_user = :iduser
			OR id_groupe IN (".$db->getParamsFromArray($lgroup, 'idgroup', $params)."))
			AND status IN (".$db->getParamsFromArray($listetype, 'status', $params).")";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $id_user);
		}else{
		$sql = "SELECT * FROM ".self::TABLE_NAME."
			WHERE id_user = :iduser
			AND status IN (".$db->getParamsFromArray($listetype, 'status', $params).")";
			$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $id_user);
		}

		$res = $db->query($sql, $params);
		while ($row = $db->fetchrow($res)) {
		$ticket = new ticket();
		$ticket->openWithFields($row, true);

		$liste_ticket[] = $ticket;
		}
	}

	return $liste_ticket ;
	}
}

?>
