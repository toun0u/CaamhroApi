<?php

/**
 * Description of ticket_watch
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class ticket_watch extends dims_data_object{
    const TABLE_NAME = "dims_ticket_watch";

    const WATCH_TYPE_NOTIFICATION_MAIL = 1;
    const WATCH_TYPE_NOTIFICATION_TICKET = 2;

    public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function setIdTicket($id_ticket, $save = false) {
	$this->setAttribut("id_ticket", self::TYPE_ATTRIBUT_KEY, $id_ticket, $save);
    }

    public function setIdUser($id_user, $save = false) {
	$this->setAttribut("id_user", self::TYPE_ATTRIBUT_KEY, $id_user, $save);
    }

    public function setTypeNotification($type_notification, $save = false) {
	$this->setAttribut("type_notification", self::TYPE_ATTRIBUT_NUMERIC, $type_notification, $save);
    }

    public function setActive($active, $save = false) {
	$this->setAttribut("active", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT, $active, $save);
    }

    public function getIdTicket() {
	return $this->getAttribut("id_ticket", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdUser() {
	return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
    }

    public function getTypeNotification() {
	return $this->getAttribut("type_notification", self::TYPE_ATTRIBUT_NUMERIC);
    }

    public function isActive() {
	return $this->getAttribut("active", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT);
    }


    public static function isWatchingForIdUserAndTicket($id_user, $id_ticket){
	$res = false ;

	$db = dims::getInstance()->getDb();

	$sql = "SELECT * FROM ".self::TABLE_NAME."
		WHERE id_user = :iduser
		AND id_ticket = :idticket
		AND active = 1";

	$res = $db->query($sql, array(
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		':idticket' => array('type' => PDO::PARAM_INT, 'value' => $id_ticket),
	));
	$row = $db->fetchrow($res);
	if($row){
	    $res = true ;
	}

	return $res ;
    }


}

?>
