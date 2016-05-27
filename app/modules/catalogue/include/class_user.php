<?php

//include_once DIMS_APP_PATH.'/modules/system/class_user.php';
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_contact.php';

class cata_user extends user {

	private $myCt = null;

	public function getContact() {
		if(is_null($this->myCt)) {
			$this->myCt = new cata_contact();
			if(empty($this->fields['id_contact'])) {
				$this->myCt->init_description();
				$this->myCt->setugm();
			}
			else {
				$this->myCt->open($this->fields['id_contact']);
			}
		}

		return $this->myCt;
	}

}
