<?php

class domain extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_domain','id');
	}

	function save() {
		$db = dims::getInstance()->getDb();
		return(parent::save());
	}

	function delete() {
		// on doit supprimer toutes les correspondances d'utilisation de ce nom de domaine
		$res=$this->db->query("delete from dims_workspace_domain where id_domain= :iddomain", array(
			':iddomain' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		parent::delete();
	}


	//Cyril - 19/07/2012 - GETTERs & SETTERs pour paramétrer les articles WCE de page d'accueil et de page de redirection
	//après connexion associés à un nom de domaine
	function setDefaultHomePage($val){
		$this->fields['id_home_wce_article'] = $val;
	}
	function getDefaultHomePage(){
		return $this->fields['id_home_wce_article'];
	}

	function setPostConnexionPage($val){
		$this->fields['id_post_connexion_wce_article'] = $val;
	}
	function getPostConnexionPage(){
		return $this->fields['id_post_connexion_wce_article'];
	}
}
