<?php

//include_once DIMS_APP_PATH.'/modules/system/class_contact.php';

class cata_contact extends contact {

	// mets a jour le user lié au contact
	public function updateLinkedUser($values = array()) {
		// $user = new user();
		// if(!empty($this->fields['account_id']))	$user->open($this->fields['account_id']);

		// if($user->isNew()){
		// 	$new = true;
		// 	$user->init_description();
		// 	$user->setugm();
		// 	$user->fields = array_merge($user->fields, $values);
		// 	$clear_pwd = $user->fields['password'];
		// 	$user->fields['password'] 		= dims_getPasswordHash($clear_pwd);
		// 	$user->fields['status'] 		= user::USER_ACTIF;
		// 	// $user->fields['id_skin'] 		= module_smile::DEFAULT_USER_SKIN;//en dur dans le module smile pour l'instant
		// 	$user->fields['date_creation'] = dims_createtimestamp();
		// }
		// else {
		// 	$new = false;
		// 	if($values['password'] != ''){
		// 		$user->fields['password'] 	= dims_getPasswordHash($values['password']);
		// 	}
		// 	if($values['login'] != ''){
		// 		$user->fields['login'] 		= $values['login'];
		// 	}
		// }

		// //dans tous les cas les infos suivantes sont remises à jour
		// $user->fields['lastname'] 		= $this->fields['lastname'];
		// $user->fields['firstname'] 		= $this->fields['firstname'];
		// $user->fields['phone'] 			= $this->fields['phone'];
		// $user->fields['fax'] 			= $this->fields['fax'];
		// $user->fields['email'] 			= $this->fields['email'];
		// $user->fields['address'] 		= $this->fields['address'];
		// $user->fields['postalcode']		= $this->fields['postalcode'];
		// $user->fields['city'] 			= $this->fields['city'];
		// $user->fields['country'] 		= $this->fields['country'];

		// return $user->save($this->get('id'));
	}
}
