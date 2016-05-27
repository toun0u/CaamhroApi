<?php
include_once DIMS_APP_PATH.'modules/system/class_module.php';

class catalogue extends module {

	const ACTION_VIEW_ALL_CLIENTS = 1;

	/**
	 * @todo params pourrait être générique, i.e : Dans la classe module
	 * */
	private $params = array();

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	public function __construct() {
		parent::__construct();
	}

	public function getDomainId() {
		if (!empty($_SERVER['HTTP_HOST'])) {
			$sql = 'SELECT id FROM dims_domain WHERE domain = \''.$_SERVER['HTTP_HOST'].'\' LIMIT 0,1';
		}
		else {
			$sql = 'SELECT id FROM dims_domain LIMIT 0,1';
		}
		$rs = $this->db->query($sql);
		$row = $this->db->fetchrow($rs);
		return $row['id'];
	}

	public function loadParams() {
		// get default params
		$select =   '
			SELECT      pd.id_module,
						pt.name,
						pt.label,
						pd.value,
						pd.id_domain

			FROM        dims_param_default pd

			LEFT JOIN   dims_param_type pt
			ON          pt.name = pd.name
			AND         pt.id_module_type = pd.id_module_type

			WHERE       pd.id_module = '.$this->fields['id'];
		$res = $this->db->query($select);

		while ($fields = $this->db->fetchrow($res)) {
			// on charge les parametres generaux, et ceux lie au domaine actuel
			if ($fields['id_domain'] == 0 || $fields['id_domain'] == $this->getDomainId()) {
				$_SESSION['session_modules'][$fields['id_module']][$fields['name']] = $fields['value'];
				$this->params[$fields['name']] = $fields['value'];
			}
		}
	}

	public function getParams($label) {
		if(!isset($this->params[$label])) {
			if (!isset($_SERVER['catalogue']['params'][$this->fields['id']])) {
				$this->loadParams();
				$_SERVER['catalogue']['params'][$this->fields['id']]=$this->params;
			}
			else {
				$this->params=$_SERVER['catalogue']['params'][$this->fields['id']];
			}

		}
		return (isset($this->params[$label]))?$this->params[$label]:false;
	}
}
