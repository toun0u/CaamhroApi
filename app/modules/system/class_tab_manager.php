<?php
class TabManager {

	/**
	 * Attribut qui contient tous les onglets
	 */
	private $tabs = array();

	public function get($attr) {
		if(property_exists($this, $attr))
			return $this->$attr;
	}

	/**
	 * Fonction qui ajoute un onglet dans la liste
	 * @param Tab : objet Tab à ajouter à la liste
	 */
	public function addTab(Tab $tab) {

		if(!empty($tab)) {
			$tab->set('id', count($this->tabs));
			$this->tabs[] = $tab;
		}
	}

	/**
	 * Fonction qui suprime un onglet de la liste
	 * @param Tab : objet Tab à supprimer de la liste
	 */
	public function removeTab(Tab $tab) {

		$res = false;

		foreach ($this->tabs as $key => $value) {
			if($value == $tab) {
				$res = $key; // On récupére la clé pour traiter les id ensuite
				unset($this->tabs[$key]);
				$this->tabs = array_values($this->tabs); // On retrie le tableau pour ne pas avoir de "trou" dans les clés
				break;
			}
		}

		// Si l'onglet a été trouvé on remet à jour les id des tab restants
		if(is_int($res) && count($this->tabs) > 0) {
			foreach ($this->tabs as $key => $value) {
				if($key >= $res) {
					$newid = $this->tabs[$key]->id - 1;
					$value->set('id', $newid);
					$this->tabs[$key] = $value;
				}
			}
		}
	}

	/**
	 * Fonction qui indique si un onglet existe déjà dans la liste
	 * @param Tab : objet tab à chercher
	 * @return Object|boolean : si trouvé retourne l'objet | false si il n'existe pas
	 */
	public function alreadyExist(Tab $tab) {

		$res = false;

		foreach ($this->tabs as $key => $value) {
			if($value->link == $tab->link) {
				$res = $value;
				break;
			}
		}

		return $res;
	}

	/**
	 * Fonction qui suprime tous les onglets d'un coup
	 */
	public function destroy() {
		unset($this->tabs);
	}


	/**
	 * Permet de capturer des appels des fonctions
	 * Comme la fonction findByX()
	 * @param string $method Nom de la méthode virtuelle appelée
	 * @param string $args condition de la recherche
	 * @return boolean|array|object
	 */
	public function __call($method, $args) {

		// Retourne un tableau ou false :
		if(preg_match('#^findBy#i',$method)) {

			$attribut = str_replace('findBy','',$method);
			$attribut = strtolower($attribut);

			if(property_exists('Tab', $attribut)){

				$res = array();

				foreach($this->tabs as $key => $value) {
					if(in_array($value->$attribut, $args)) {
						$res[] = $value;
					}
				}

				if( count($res) == 0)
					$res = false;

				return $res;
			}
			else {
				throw new Exception('Attribut <b>'.$attribut.'</b> introuvable dans la classe Tab');
			}
		}
		// Retourne un objet ou false :
		elseif(preg_match('#^findOneBy#i',$method)) {

			$attribut = str_replace('findOneBy','',$method);
			$attribut = strtolower($attribut);

			if(property_exists('Tab', $attribut)){

				$res = false;
				foreach($this->tabs as $key => $value) {
					if(in_array($value->$attribut, $args)) {
						$res = $value;
					}
				}

				return $res;
			}
			else {
				throw new Exception('Attribut <b>'.$attribut.'</b> introuvable dans la classe Tab');
			}
		}
		else {
			throw new Exception('Méthode '.$method.' inconnue');
		}

	}

}

// $reset_tabs = dims_load_securvalue('reset_tabs', dims_const::_DIMS_NUM_INPUT, true, false);

// if( isset($reset_tabs) && $reset_tabs){//on doit ouvrir un nouvel onglet
// 	if(isset($_SESSION['dims']['smile']['tabs']))
// 		unset($_SESSION['dims']['smile']['tabs']);
// }

// //initialisation des tabs
// if(empty($_SESSION['dims']['smile']['tabs']) && !empty($this)){
// 	$_SESSION['dims']['smile']['tabs'] = array();
// 	$idx = add_tab($_SESSION['dims']['smile']['tabs'], array('c' => 'anr', 'a' => 'show', 'id' => $this->get('id')), $this->get('label'), $this->get('id'), false);
// 	$_SESSION['dims']['smile']['tab_index'][] = $idx;
// }

// $new_tab = dims_load_securvalue('new_tab', dims_const::_DIMS_NUM_INPUT, true, false);

// if( isset($new_tab) && $new_tab){//on doit ouvrir un nouvel onglet
// 	$idx = add_tab($_SESSION['dims']['smile']['tabs'], get_params($_SERVER['QUERY_STRING']), '', $this->get('id'), true);
// 	$_SESSION['dims']['smile']['tab_index'][] = $idx;
// }

// $close_tab = dims_load_securvalue('close_tab', dims_const::_DIMS_NUM_INPUT, true, false);
// if( isset($close_tab) && $close_tab){//on doit ouvrir un nouvel onglet
// 	close_tab($_SESSION['dims']['smile']['tabs'], $close_tab);
// 	$temp = array();
// 	//élimination de toute trace de cet onglet dans l'historique
// 	foreach($_SESSION['dims']['smile']['tab_index'] as $idx){
// 		if($idx != $close_tab){
// 			$temp[] = $idx;
// 		}
// 	}
// 	$_SESSION['dims']['smile']['tab_index'] = $temp;
// }

// $sel_tab = dims_load_securvalue('sel_tab', dims_const::_DIMS_NUM_INPUT, true, false);
// if( isset($sel_tab) && $sel_tab){//on doit ouvrir un nouvel onglet
// 	select_tab($_SESSION['dims']['smile']['tabs'], $sel_tab);
// 	if($_SESSION['dims']['smile']['tab_index'][count($_SESSION['dims']['smile']['tab_index']) - 1] != $sel_tab){
// 		$_SESSION['dims']['smile']['tab_index'][] = $sel_tab;
// 	}
// }

// $view->assign('tab_index', $_SESSION['dims']['smile']['tab_index']);


// // Partie HELPERS --> méthode de classe

// function get_next_indice($tabs){
// 	$max = 0;

// 	foreach($tabs as $id => $tab){
// 		if($id > $max)
// 			$max = $id;
// 	}

// 	return $max;
// }

// function add_tab(&$tabs, $params, $libelle, $context_anr, $closable = true){
// 	$existing_tab = null;

// 	if(isset($params['c']) && isset($params['a']) && isset($params['id'])){
// 		$existing_tab = find_tab_by($tabs, $params['c'], $params['a'], $params['id']);
// 	}
// 	//die($existing_tab);
// 	if( is_null($existing_tab)){
// 		$idx = get_next_indice($tabs) + 1;
// 		$tabs[$idx]['params'] = $params;
// 		$tabs[$idx]['path'] = build_url($params, $idx);
// 		$tabs[$idx]['libelle'] = $libelle;
// 		$tabs[$idx]['closable'] = $closable;
// 		$tabs[$idx]['context_anr'] = $context_anr;
// 	}
// 	else{
// 		$idx = $existing_tab;
// 		update_tab($tabs, $idx, array('params' => $params, 'libelle' => $libelle, 'closable' => $closable));
// 	}

// 	select_tab($tabs, $idx);
// 	return $idx;
// }

// function build_url($params, $idx){
// 	//construction du chemin
// 	$url = dims::getInstance()->getScriptEnv() . '?';
// 	$first = true;
// 	foreach($params as $p => $val){
// 		if($p != 'sel_tab' && $p != 'close_tab' && $p != 'new_tab' && $p != 'anrid'){
// 			if(!$first)
// 			 $url .= '&';

// 			$url .= $p.'='.$val;

// 			if($first)
// 				$first = false;
// 		}
// 	}
// 	$url .= '&sel_tab='.$idx;
// 	return $url;
// }

// function get_selected_tab($tabs){
// 	foreach($tabs as $idx => $tab){
// 		if(isset($tab['selected']) && $tab['selected']){
// 			return $idx;
// 		}
// 	}
// 	return null;
// }

// function select_tab(&$tabs, $idx){
// 	$selected_idx = get_selected_tab($tabs);
// 	if(!is_null($selected_idx))
// 		$tabs[$selected_idx]['selected'] = false;

// 	$tabs[$idx]['selected'] = true;
// }

// function close_tab(&$tabs, $idx){
// 	if(isset($tabs[$idx]))
// 		unset($tabs[$idx]);
// }

// function update_tab(&$tabs, $idx, $keys){
// 	if(isset($tabs[$idx]) && !empty($keys)){
// 		foreach($keys as $k => $value){
// 			switch($k){

// 				default:
// 					$tabs[$idx][$k] = $value;
// 					break;
// 				case 'params':
// 					$tabs[$idx]['params'] = $value;
// 					$tabs[$idx]['path'] = build_url($value, $idx);
// 					break;
// 			}
// 		}
// 	}
// }

// function find_tab_by($tabs, $c, $a, $id){
// 	foreach($tabs as $idx => $tab){
// 		$params = $tab['params'];

// 		if(isset($params['c']) && $c == $params['c'] && isset($params['a']) && $a == $params['a'] && isset($params['id']) && $id == $params['id'] ){
// 			return $idx;
// 		}
// 	}
// 	//die();
// 	return null;
// }

// function get_params($q_string){

// 	$tab = explode('&', $q_string);
// 	$params = array();
// 	foreach($tab as $sub){
// 		$couple = explode('=', $sub);
// 		if(count($couple) == 2 && $couple[0] != 'new_tab' ){
// 			$params[$couple[0]] = dims_load_securvalue($couple[0], dims_const::_DIMS_CHAR_INPUT, true, false);//par mesure de précaution
// 		}
// 	}
// 	return $params;
// }