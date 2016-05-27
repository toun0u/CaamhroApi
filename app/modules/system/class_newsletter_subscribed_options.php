<?php

class newsletter_subscribed_options extends dims_data_object {
	const TABLE_NAME = 'dims_mod_newsletter_subscribed_options';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public static function finduseroptions(user $user) {
		$subscribedoptions = array();
		$db = dims::getInstance()->getDb();
		$sql = 'SELECT  *
				FROM    '.self::TABLE_NAME.'
				WHERE   id_subscribeduser = :iduser';

		$res = $db->query($sql, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $user->getId()),
		));

		while($data = $db->fetchrow($res)) {
			$subscribedoptions[$data['id_mailinglist']] = new self();
			$subscribedoptions[$data['id_mailinglist']]->openFromResultSet($data);
		}

		return $subscribedoptions;
	}
}
