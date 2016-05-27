<?php
class suividetail extends dims_data_object {
    const TABLE_NAME = 'dims_mod_business_suivi_detail';
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function save() {
		if (!isset($this->fields['position']) || !$this->fields['position']) {
			$rs = $this->db->query("SELECT MAX(position) as maxpos
						FROM dims_mod_business_suivi_detail
						WHERE suivi_id = :idsuivi
						AND suivi_type = :typesuivi
						AND suivi_exercice = :exercicesuivie
						AND id_workspace = :idworkspace", array(
				':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['suivi_id']),
				':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['suivi_type']),
				':exercicesuivie' => array('type' => PDO::PARAM_STR, 'value' => $this->fields['suivi_exercice']),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));
			$fields = $this->db->fetchrow($rs);
			if (isset($fields['maxpos'])) $maxpos = $fields['maxpos'];
			else $maxpos = 0;

			$this->fields['position'] = $maxpos + 1;
		}

		// Remplacement de la virgule par un point (très utile sous Mac OS X !!)
		$this->fields['pu'] = str_replace(',', '.', $this->fields['pu']);
		$this->fields['tauxtva'] = str_replace(',', '.', $this->fields['tauxtva']);
		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

		return(parent::save());
	}

	public function moveup() {
		$totalelem = $this->getsiblingcount();

		if($this->fields['position'] < $totalelem) {
			$prev = self::find_by(array('position'=>($this->fields['position'] + 1),'suivi_id'=>$this->fields['suivi_id'],'suivi_type'=>$this->fields['suivi_type'],'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($prev)){
				$prev->fields['position']--;
				$prev->save();

				$this->fields['position']++;
				$this->save();
			}
		}
	}

	public function movedown() {
		$totalelem = $this->getsiblingcount();

		if($this->fields['position'] > 0) {
			$prev = self::find_by(array('position'=>($this->fields['position'] - 1),'suivi_id'=>$this->fields['suivi_id'],'suivi_type'=>$this->fields['suivi_type'],'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($prev)){
				$prev->fields['position']++;
				$prev->save();

				$this->fields['position']--;
				$this->save();
			}
		}
	}

	private function getsiblingcount() {
		$db = dims::getInstance()->getDb();
		$sql = 'SELECT  COUNT(id) AS count
				FROM    '.self::TABLE_NAME.'
				WHERE   suivi_id        = :sid
				AND     suivi_type      = :st
				AND     id_workspace    = :idw';
		$params = array(
			':sid' => $this->fields['suivi_id'],
			':st' => $this->fields['suivi_type'],
			':idw' => $_SESSION['dims']['workspaceid'],
		);

		$res = $db->query($sql,$params);

		$data = $db->fetchrow($res);

		return $data['count'];
	}
}
