<?php
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class dims_server extends pagination {
	const TABLE_NAME = 'dims_server';

	const SSH_DISABLE	= 0;
	const SSH_ENABLE	= 1;

	const SSL_DISABLE	= 0;
	const SSL_ENABLE	= 1;

	const STATE_ACTIVE			= 2;
	const STATE_INACTIVE		= 0;
	const STATE_INACTIVE_SSH	= 1;

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');

		if (file_exists('./common/modules/smile')){
			require_once DIMS_APP_PATH.'modules/smile/include/classes/common/class_smile_company.php';
			require_once DIMS_APP_PATH.'modules/smile/include/classes/common/class_smile_client.php';

			$this->has_many('smile_company', smile_company::TABLE_NAME, 'id', 'id_smile_server');
			$this->has_many('smile_client', smile_client::TABLE_NAME, 'id', 'id_smile_server');
		}
	}

	public function checkConnection () {
		if(!$this->isNew()) {

			exec(escapeshellcmd('ping -c1 '.escapeshellarg($this->fields['address']).' > /dev/null 2>&1'), $output, $retval);

			$this->fields['status'] = (bool) !$retval;

			if(!$retval && $this->fields['ssh']) {
				$randVal = uniqid();

				//echo 'echo "testdims" | ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i'.escapeshellarg($this->fields['identity_file']).' -p'.escapeshellarg($this->fields['port']).' '.escapeshellarg($this->fields['login']).'@'.escapeshellarg($this->fields['address']).' "cat > testdims_'.$randVal.'" ';
				exec(escapeshellcmd('echo "testdims" | ssh -q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i'.escapeshellarg($this->fields['identity_file']).' -p'.escapeshellarg($this->fields['port']).' '.escapeshellarg($this->fields['login']).'@'.escapeshellarg($this->fields['address']).' "cat > testdims_'.$randVal.'" '), $output, $retval);

				if(!$retval) {
					$this->fields['status'] = self::STATE_ACTIVE;

					exec(escapeshellcmd('ssh -q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i'.escapeshellarg($this->fields['identity_file']).' -p'.escapeshellarg($this->fields['port']).' '.escapeshellarg($this->fields['login']).'@'.escapeshellarg($this->fields['address']).' "rm testdims_'.$randVal.'"'), $output, $retval);
				}
			}
		}

		return $this->fields['status'];
	}

	public function getContent($pagination=false) {
		$params = array();
		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getContent(true));
			$limit = " LIMIT :limitstart, :limitkey";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}
		else $limit="";

		$sql = 'SELECT * FROM '.self::TABLE_NAME.$limit;

		$result_object = $this->db->query($sql, $params);

		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($result_object);
		}
		else {
			return $result_object;
		}
	}
}
?>
