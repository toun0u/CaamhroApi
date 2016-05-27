<?php
include_once DIMS_APP_PATH."modules/system/class_group.php";
class cata_group extends group{
	public function getIdAdr(){
		$return = 0;
		if (!$this->isNew()){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_group_livraison.php";
			$sel = "SELECT  *
					FROM    ".cata_gr_liv::TABLE_NAME."
					WHERE   id_group = ".$this->fields['id'];
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			if($r = $db->fetchrow($res))
				$return = $r['id_livraison'];
		}
		return $return;
	}

	public function getAdr(){
		include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
		$elem = new cata_depot();
		$elem->init_description();
		if (!$this->isNew()){
			include_once DIMS_APP_PATH."modules/catalogue/include/class_group_livraison.php";
			$sel = "SELECT      d.*
					FROM        ".cata_depot::TABLE_NAME." d
					INNER JOIN  ".cata_gr_liv::TABLE_NAME." gl
					ON          gl.id_livraison = d.id
					WHERE       gl.id_group = ".$this->fields['id'];
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel);
			if($r = $db->fetchrow($res))
				$elem->openFromResultSet($r);
		}
		return $elem;
	}

	public function addAdr($idAdr){
		if ($this->isNew())
			$this->save();

		include_once DIMS_APP_PATH."modules/catalogue/include/class_group_livraison.php";
		$sel = "SELECT      *
				FROM        ".cata_gr_liv::TABLE_NAME."
				WHERE       id_group = ".$this->fields['id'];
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel);
		while($r = $db->fetchrow($res)){
			$elem = new cata_gr_liv();
			$elem->openFromResultSet($r);
			$elem->delete();
		}
		$elem = new cata_gr_liv();
		$elem->fields['id_group'] = $this->get('id');
		$elem->fields['id_livraison'] = $idAdr;
		$elem->save();
	}
}
?>
