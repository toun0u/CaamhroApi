<?php
class dims_alert extends dims_data_object {

	const MODE_RELATIVE = 1;
	const MODE_ABSOLUTE = 2;

	const TABLENAME = 'dims_alerts';
	const MY_GLOBALOBJECT_CODE = 42;

	private $object;
	private $method;
	private $emailTemplate;

	public function __construct() {
		$this->object = null;
		$this->method = null;
		$this->emailTemplate = null;
		parent::dims_data_object('dims_alerts');
	}

	public function save() {
		$ts = dims_createtimestamp();

		if ($this->new) {
			$this->setugm();
			$this->fields['timestp_create'] = $ts;
		}
		$this->fields['timestp_modify'] = $ts;
		$this->fields['protocol'] = dims::getInstance()->getProtocol();
		$this->fields['domain'] = $_SERVER['HTTP_HOST'];

		parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function setid_object() {
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle() {
		$this->title = 'New Alert';
	}

	/*
	 * @param ts_event is an UNIX timestamp
	 */
	public function setRelative($ts_event, $period, $nb_period) {
		$this->fields['mode'] = self::MODE_RELATIVE;

		switch ($period) {
			// days
			case 'd':
				$ts_alert = $ts_event - $nb_period * 60 * 60 * 24;
				break;
			// hours
			case 'H':
				$ts_alert = $ts_event - $nb_period * 60 * 60;
				break;
			// minutes
			case 'i':
				$ts_alert = $ts_event - $nb_period * 60;
				break;
		}

		$this->fields['timestp_alert'] = date('YmdHis', $ts_alert);
		$this->fields['period'] = $period;
		$this->fields['nb_period'] = $nb_period;
	}

	public function setAbsolute($date, $time = '00:00:00') {
		$this->fields['mode'] = self::MODE_ABSOLUTE;
		$this->fields['timestp_alert'] = substr($date, 6, 4).substr($date, 3, 2).substr($date, 0, 2).substr($time, 0, 2).substr($time, 3, 2).substr($time, 6, 2);
	}

	public function setGOOrigin($id_go_origin) {
		$this->fields['id_go_origin'] = $id_go_origin;
	}

	public static function getAllByGOOrigin($id_go_origin = 0) {
		$db = dims::getInstance()->getDb();

		$a_alerts = array();
		$rs = $db->query('SELECT * FROM `'.self::TABLENAME.'` WHERE id_go_origin = :idgoorigin', array(
			':idgoorigin' => array('type' => PDO::PARAM_INT, 'value' => $id_go_origin),
		));
		while ($row = $db->fetchrow($rs)) {
			$alert = new dims_alert();
			$alert->openFromresultSet($row);
			$a_alerts[] = $alert;
		}

		return $a_alerts;
	}

	public static function getAllByTimeStamp($ts) {
		$db = dims::getInstance()->getDb();

		$a_alerts = array();
		$rs = $db->query('SELECT * FROM `'.self::TABLENAME.'` WHERE timestp_alert <= :timestamp', array(
			':timestamp' => array('type' => PDO::PARAM_INT, 'value' => $ts - 100),
		));
		while ($row = $db->fetchrow($rs)) {
			$alert = new dims_alert();
			$alert->openFromresultSet($row);
			$a_alerts[] = $alert;
		}

		return $a_alerts;
	}

	public function setNotifObject($object) {
		$this->object = $object;
	}

	public function getNotifObject() {
		return $this->object;
	}

	public function setNotifMethod($method) {
		$this->method = $method;
	}

	public function setEmailTemplate($template) {
		if (file_exists($template)) {
			$this->emailTemplate = $template;
		}
	}

	private function getEmailTemplate() {
		return $this->emailTemplate;
	}

	public function notify() {
		if ($this->method !== null) {
			switch ($this->method) {
				case 'email':
					if ($this->getEmailTemplate() !== null) {
						global $db;

						// recherche des objets liés à l'activité
						require_once DIMS_APP_PATH.'modules/system/class_search.php';
						$matrix = new search();
						$matrix->db = $db;
						$linkedObjectsIds = $matrix->exploreMatrice($this->getNotifObject()->fields['id_workspace'], null, array($this->getNotifObject()->fields['id_globalobject']));

						// recherche des destinataires
						$a_dests = array();

						// responsable
						$resp = new user();
						$resp->open($this->getNotifObject()->fields['id_responsible']);
						$a_dests[$resp->fields['id']] = $resp->fields['firstname'].' '.$resp->fields['lastname'].' <'.$resp->fields['email'].'>';

						// participants avec un dims_user
						if (!empty($linkedObjectsIds['distribution']['contacts'])) {
							$params = array();
							$rs = $db->query('
								SELECT	u.*
								FROM	dims_mod_business_contact c
								INNER JOIN	dims_user u
								ON			u.id_contact = c.id
								WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
								GROUP BY c.id', $params);
							if ($db->numrows($rs)) {
								while ($row = $db->fetchrow($rs)) {
									$a_dests[$row['id']] = $row['firstname'].' '.$row['lastname'].' <'.$row['email'].'>';
								}
							}
						}

						foreach ($a_dests as $id_dest => $dest) {
							require $this->getEmailTemplate();
							dims_send_mail_with_pear($expeditor, $dest, $subject, $message);
						}
					}
					break;
			}
		}
	}

	public function getProtocol() {
		return $this->fields['protocol'];
	}
	public function getDomain() {
		return $this->fields['domain'];
	}

}
