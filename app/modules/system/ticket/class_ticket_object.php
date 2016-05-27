<?php


/**
 * Description of ticket_object
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class ticket_object  extends dims_data_object{
	const TABLE_NAME = "dims_ticket_object";

	public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getIdTicket() {
	return $this->getAttribut("id_ticket", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdGlobalObject() {
	return $this->getAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY);
	}

	private $globalobject = null ;

	public function getGlobalObject(){
	if(is_null($this->globalobject)){
		$this->globalobject = new dims_globalobject();
		$this->globalobject->open($this->getIdGlobalObject());
	}
	return $this->globalobject;
	}

	public function setGlobalObject(dims_globalobject $globalobject){
	$this->globalobject = $globalobject ;
	}

	/**
	 *
	 * @param array $list_id_ticket
	 * @return array of ticket_object
	 */
	public static function getListTicketObjectForListIdTicket(array $list_id_ticket){
	$list_ticket_object = array() ;

	$db = dims::getInstance()->getDb() ;

	if(!empty ($list_id_ticket)){
		$params = array();
		$sql = "SELECT * FROM ".self::TABLE_NAME."
		WHERE id_ticket IN (".$db->getParamsFromArray($list_id_ticket, 'idticket', $params).")
		";


		$res = $db->query($sql, $params);
		while($row = $db->fetchrow($res)){
		$ticket_object = new ticket_object();
		$ticket_object->openWithFields($row);

		$list_ticket_object[$ticket_object->getIdTicket()] = $ticket_object ;
		}

	}

	return $list_ticket_object ;
	}

	public static function getCountOfObjetJointsForIdTicket($id_ticket){
	$nb_objets_joints = 0 ;

	$db = dims::getInstance()->getDb() ;

	$sql = "SELECT COUNT(*) AS numticket FROM ".self::TABLE_NAME."
		WHERE id_ticket = :idticket";

	$res = $db->query($sql, array(
		':idticket' => array('type' => PDO::PARAM_INT, 'value' => $id_ticket),
	));

	if($row = $db->fetchrow($res)){
		$nb_objets_joints = $row['numticket'];
	}

	return $nb_objets_joints;
	}

	public static function addListeLinkedObjects($id_ticket, array $list_id_globalobject) {
	if(!empty($list_id_globalobject)){
		$first = true ;
		$sql = "INSERT INTO `".self::TABLE_NAME."` (`id`, `id_ticket`, `id_globalobject`) VALUES ";

		$i = 0;
		$params = array();
		foreach ($list_id_globalobject as $id_globalobject) {
			if(!$first){
				$sql .= " , ";
			}
			$i++;
			$first = false ;
			$sql .= "(NULL, :idticket".$i.", :idglobalobject".$i.")";
			$params[':idticket'.$i] = array('type' => PDO::PARAM_INT, 'value' => $id_ticket);
			$params[':idglobalobject'.$i] = array('type' => PDO::PARAM_INT, 'value' => $id_globalobject);
		}

		$sql .= ";";

		$db = dims::getInstance()->getDb();
		$db->query($sql, $paramss);

	}
	}
}

?>
