<?php
require_once DIMS_APP_PATH . '/modules/system/class_search_expression_result.php';
require_once DIMS_APP_PATH . '/modules/system/class_contact.php';
require_once DIMS_APP_PATH . '/modules/system/class_search.php';
/**
 * Description of class_search_expression
 *
 * @author cyrilrouyer
 */
class search_expression  extends pagination {

	function __construct($pagination=false) {
		parent::dims_data_object('dims_search_expression');
		$this->isPageLimited = $pagination;
	}

	function create($id_user, $expression, $type){
		$this->setUserId($id_user);
		$this->setExpression($expression);
		$this->setType($type);
		$this->setDate(date('YmdHis'));
		return $this->save();//retourne l'id créé
	}

	function setUserId($val){
		$this->fields['id_user'] = $val;
	}

	function setExpression($val){
		$this->fields['expression'] = $val;
	}

	function setDate($val){
		$this->fields['timestp_create'] = $val;
	}

	function setType($val){
		$this->fields['type'] = $val;
	}
	function getUserId(){
		return $this->fields['id_user'];
	}

	function getExpression(){
		return $this->fields['expression'];
	}

	function getDate(){
		return $this->fields['timestp_create'];
	}

	function getType(){
		return $this->fields['type'];
	}

	/*
	* fonction permettant d'éliminer une ligne de résultat après suppression d'un contact par exemple pour ne pas avoir à relancer la recherche
	* NOTE IMPORTANTE : on ne filtre pas sur l'id search courant, c'est normal, c'est pour que l'objet de recherche disparaisse aussi chez les autres puisqu'il n'est plus actif (au prochain refresh)
	*/
	function deleteRow($id_go){
		$this->db->query('DELETE FROM dims_search_expression_result WHERE id_globalobject_ref= :idglobalobject', array(
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
		));
		$this->db->query('DELETE FROM dims_search_expression_tag WHERE id_globalobject_ref= :idglobalobject', array(
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
		));
	}
	/*
	 * @description méthode qui permet de retourner toutes les recherches dont l'expression est identique
	 * @param $expression : expression à rechercher
	 * @param $light : ne retoure que les ids
	 * @deprecated expression contain sql - MUST be sanitized
	 */
	function getAllSearchesFor($expression, $light=true){
		$res = $this->db->query("SELECT * FROM ".$this->tablename." WHERE LOWER(".$expression.") = ".strtolower($expression));
		$searches = array();
		if(!$light){
			$separation = $this->db->split_resultset($res);
			foreach($separation as $elements){
				$s = new search_expression();
				$s->openFromResultSet($elements[$this->tablename]);
				$separation[] = $s;
			}
		}
		else{
			while($tab = $this->db->fetchrow($res)){
				$searches[] = $tab['id'];
			}
		}
	}

	function countResults($type=null, $tags=null){
		$where_tags = '';
		$left = '';
		if(count($tags)){
			$where_tags .= " AND t.tags REGEXP '";
			asort($tags);
			foreach($tags as $t){
				$where_tags .= '.*;'.$t.';';
			}
			$where_tags .= ".*'";
			$left = " LEFT JOIN dims_search_expression_tag t ON t.id_search = r.id_search AND r.id_globalobject_ref = t.id_globalobject_ref";
		}

		if($type != null){
			$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
			$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
			$res = $this->db->query('SELECT count(*) as total
						 FROM dims_search_expression_result r '.
						 $left.'
						 WHERE r.id_search= :idsearch AND r.type= :type'.$where_tags,
						 $params);
			$c = 0;
			if($this->db->numrows($res)){
				$tab = $this->db->fetchrow($res);
				$c = $tab['total'];
			}
			return $c;
		}
		else{
			$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
			$res = $this->db->query('SELECT r.type, count(*) as total
						 FROM dims_search_expression_result r'.
						 $left.'
						 WHERE r.id_search = :idsearch '.$where_tags.'
						 GROUP BY r.type',
						 $params);
			$counts = array();
			if($this->db->numrows($res)){
				while($tab = $this->db->fetchrow($res)){
					$counts[$tab['type']] = $tab['total'];
				}
			}
			return $counts;
		}
	}

	function getResults($type=null, $tags=null, $pagination=false){
		$results = array();
		$params = array();
		//gestion des tags (pas simple, il faut tenir compte du croisement
		$where_tags = '';
		$inner = '';
		$nb_tags = count($tags);
		if(count($tags)){
			$where_tags .= " AND t.tags REGEXP '";
			asort($tags); //hyper important pour le tri par tags
			foreach($tags as $t){
				$where_tags .= '.*;'.$t.';.*';
			}
			$where_tags .= "'";

			$inner = " INNER JOIN dims_search_expression_tag t ON t.id_search = r.id_search AND r.id_globalobject_ref = t.id_globalobject_ref";
		}

		if ($this->isPageLimited && !$pagination) {
			pagination::liste_page($this->getResults($type, $tags, true));
			$limit = "LIMIT :limitstart, :limitkey";
			$params[':limitstart'] = array('type' => PDO::PARAM_INT, 'value' => $this->sql_debut);
			$params[':limitkey'] = array('type' => PDO::PARAM_INT, 'value' => $this->limite_key);
		}
		else $limit="";

		/*
		* CYRIL - 24/01/2012 --> Hack demandé par André Hansen pour que les missions apparaissent dans l'ordre chrono inverse quand on est sur une catégorie de ce type
		*/
		switch($type){
			case search::RESULT_TYPE_ACTIVITY:
			case search::RESULT_TYPE_MISSION:
			case search::RESULT_TYPE_FAIR:
				$inner .= " INNER JOIN dims_mod_business_action a ON a.id_globalobject = r.id_globalobject_ref";
				$order_by = "a.datejour DESC";
				break;
			default:
				$order_by = "r.rank DESC";
				break;
		}

		$sql = "SELECT r.type, r.id_globalobject_ref, r.advanced_source, mbf.name, mbf.label, se.content
			FROM dims_search_expression_result r".
			$inner."
			LEFT JOIN dims_mb_field mbf ON mbf.id = r.id_metafield
			LEFT JOIN dims_keywords_sentence se ON se.id = r.id_sentence
			WHERE r.id_search= :idsearch";
		if(!empty($type)) {
			$sql .= ' AND r.type = :type';
			$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
		}
		$sql .= $where_tags.
			" GROUP BY r.id_globalobject_ref".//HACK : c'est parce que la table dims_keywords_sentence n'a pas d'autoincrement sur ID et du coup y'a des doublons
			(($this->isPageLimited && $pagination)?'':" ORDER BY ".$order_by." ").
			$limit;
		$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$result = $this->db->query($sql, $params);
		if ($this->isPageLimited && $pagination) {
			return $this->db->numrows($result);
		}
		else {
			while($tab = $this->db->fetchrow($result)){
				$res = array();
				$res['type'] = $tab['type'];
				$res['mb_label'] = $tab['label'];
				$res['mb_field'] = $tab['name'];
				$res['sentence'] = $tab['content'];
				$res['advanced_src'] = $tab['advanced_source'];
				$res['record'] = $tab['id_globalobject_ref'];
				$results[] = $res;
			}
			return $results;
		}
	}

	function getLightContacts($tags=null, $only_photos=false, $only_fields=false){

		$results = array();
		//gestion des tags (pas simple, il faut tenir compte du croisement
		$where_tags = '';
		$inner = '';
		$nb_tags = count($tags);

		if(count($tags)){
			$where_tags .= " AND t.tags REGEXP '";
			asort($tags);
			foreach($tags as $t){
				$where_tags .= '.*;'.$t.';.*';
			}
			$where_tags .= "'";

			$inner = " INNER JOIN dims_search_expression_tag t ON t.id_search = r.id_search AND r.id_globalobject_ref = t.id_globalobject_ref";
		}

		$sql =	"	SELECT c.*
				FROM dims_search_expression_result r
				INNER JOIN dims_mod_business_contact c ON r.id_globalobject_ref=c.id_globalobject ".(($only_photos)?" AND c.photo IS NOT NULL AND c.photo != ''":"").
				$inner."
				WHERE r.id_search= :idsearch AND r.type=".search::RESULT_TYPE_CONTACT.
				$where_tags.
				" ORDER BY r.rank DESC ";
		$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$res = $this->db->query($sql, $params);
		while ($fields = $this->db->fetchrow($res)){
			if(!$only_fields){
				$ct = new contact();
				$ct->openFromResultSet($fields);
				$results[] = $ct;
			}
			else $results[$fields['id']] = $fields;
		}

		return $results;
	}


	function getLightCompanies($tags=null, $only_photos=false, $only_fields=false){

		$results = array();
		//gestion des tags (pas simple, il faut tenir compte du croisement
		$inner = '';
		$nb_tags = count($tags);

		if(count($tags)){
			$inner = " INNER JOIN dims_search_expression_tag tag ON tag.id_search = r.id_search AND r.id_globalobject_ref = tag.id_globalobject_ref";
		}

		 $sql =  "	SELECT t.*
				FROM dims_search_expression_result r
				INNER JOIN dims_mod_business_tiers t ON r.id_globalobject_ref=t.id_globalobject ".(($only_photos)?" AND c.photo IS NOT NULL AND c.photo != ''":"").
				$inner."
				WHERE r.id_search= :idsearch AND r.type=".search::RESULT_TYPE_COMPANY.
				" ORDER BY r.rank DESC ";

		$res = $this->db->query($sql, array(
			':idsearch' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($fields = $this->db->fetchrow($res)){
			if(!$only_fields){
				$t = new tiers();
				$t->openFromResultSet($fields);
				$results[] = $t;
			}
			else $results[$fields['id']] = $fields;
		}

		return $results;
	}


	function getLightCompaniesWithContacts($tiersfields,$contactfields){
		$results = array();
		$tiers = array();
		$contacts = array();
		$listidtiers = array();


		$sql =  "	SELECT t.id,".$tiersfields."
				FROM dims_search_expression_result r
				INNER JOIN dims_mod_business_tiers t ON r.id_globalobject_ref=t.id_globalobject ".(($only_photos)?" AND c.photo IS NOT NULL AND c.photo != ''":"").

				" WHERE r.id_search=:idsearch AND r.type=".search::RESULT_TYPE_COMPANY.
				" ORDER BY t.intitule";

		$res = $this->db->query($sql, array(
			':idsearch' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		// corresp entre position et id de tiers pour maintenir l'ordre croissant des entreprises
		$corresp=array();

		$pos=0;
		$postiers=0;

		$copyfields=array();
		//die($this->db->numrows($res));
		while ($fields = $this->db->fetchrow($res)){
			$listidtiers[]=$fields['id'];
			if (!isset($corresp[$fields['id']])) {
				$corresp[$fields['id']]=$pos;
				$pos++;
			}
			$postiers=$corresp[$fields['id']];

			if (!isset($results[$postiers])) {
				unset($fields['id']);
				$results[$postiers]=$fields;
				$results[$postiers]['contacts']=array(); // init des contacts
			}

			if (empty($copyfields)) $copyfields=$fields;
		}

		foreach ($copyfields as $k=>$value) {
			$copyfields[$k]="";
		}

		//dims_print_r($corresp);
		//dims_print_r($results);

		$copyfields["id"]=0;
		$listidtiers[]=$copyfields;

		if (!isset($corresp[$copyfields['id']])) {
			$corresp[$copyfields['id']]=$pos;
			$postiers=$corresp[$copyfields['id']];
			$pos++;

			unset($copyfields['id']);
			$results[$postiers]=$copyfields;

			$results[$postiers]['contacts']=array(); // init des contacts

		}



		if (empty($listidtiers)) $listidtiers[]=0;

		$params=array();
		$params['type']=search::RESULT_TYPE_CONTACT;
		// on construit la liste des contacts taggés ou non
		$sql =	"	SELECT tc.id_tiers,".$contactfields.",tc.function
				FROM dims_search_expression_result r
				INNER JOIN dims_mod_business_contact c ON r.id_globalobject_ref=c.id_globalobject".
				" AND r.id_search=:idsearch AND r.type=:type".
				$innerct."
				LEFT JOIN dims_mod_business_tiers_contact as tc ON tc.id_tiers in (".$this->db->getParamsFromArray($listidtiers, 'idt', $params).")
				 AND tc.id_contact=c.id ".
				" ORDER BY tc.id_tiers,c.lastname,c.firstname";

		$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		$res = $this->db->query($sql, $params);
		while ($fields = $this->db->fetchrow($res)){
			//dims_print_r($fields);
			if ($fields['id_tiers']=='') $fields['id_tiers']=0;

			if ($fields['id_tiers']==0) echo $fields['id_tiers']." ";
			if (isset($corresp[$fields['id_tiers']])) {
				$postiers=$corresp[$fields['id_tiers']];
				unset($fields['id_tiers']); // on enlève la colonne
				$results[$postiers]['contacts'][]=$fields;
			}

		}

		return $results;
	}

	//Fonction permettant la génération du lien mailto pour envoyer un mail aux contacts présents dans les résultats de la recherche
	function getSearchContactMailData($object, $contacts = null, $tags_id=null){
		$tabmails=array();
		$tablayermails=array();

		//gestion de la tag list pour l'objet du mail
		if($contacts ==null){
			if($tags_id != null && !empty($tags))
				$exported_contacts = $this->getLightContacts($tags_id);
			else $exported_contacts = $search->getLightContacts();
		}
		else $exported_contacts = $contacts;

		if (!empty($exported_contacts)) {

			foreach($exported_contacts as $ct_id => $contact) {
				$ismail=false;

				if (isset($contact['email']) && !$ismail && $contact['email']!='') {
					if (!isset($tabmails[$contact['email']])) {
						$tabmails[$contact['email']]=$contact['email'];
						$ismail=true;
					}
				}

				if (isset($contact['email2']) && !$ismail && $contact['email2']!='') {
					if (!isset($tabmails[$contact['email2']])) {
						$tabmails[$contact['email2']]=$contact['email2'];
						$ismail=true;
					}
				}

				if (isset($contact['email3']) && !$ismail && $contact['email3']!='') {
					if (!isset($tabmails[$contact['email3']])) {
						$tabmails[$contact['email3']]=$contact['email3'];
						$ismail=true;
					}
				}
				if ($ismail==false) {
					// on ajoute pour regarder dans les layers
					$tablayermails[$ct_id]=$ct_id;
				}
			}

			//on va chercher les champs métier dans les layers
			if (isset($tablayermails) && !empty($tablayermails)) {
				$params = array();
				$sql_l =	"	SELECT	*
							FROM		dims_mod_business_contact_layer
							WHERE		id in (".$this->db->getParamsFromArray($tablayermails,'idcontactlayer', $params).")
							AND		type_layer = 1
							AND		id_layer = :idlayer";
				$params[':idlayer'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
				//$blocklink.=$sql_l;
				$res_l = $this->db->query($sql_l, $params);
				if($this->db->numrows($res_l) > 0) {
					while($lay = $this->db->fetchrow($res_l)) {
						$ismail = false;
						if($lay['email'] != '' && !$ismail) {
							if(!isset($tabmails[$lay['email']])){
								$tabmails[$lay['email']]=$lay['email'];
								$ismail = true;//pour arrêter le process sur ce contact
							}
						}
						if($lay['email2'] != '' && !$ismail) {
							if(!isset($tabmails[$lay['email2']])){
								$tabmails[$lay['email2']]=$lay['email2'];
								$ismail = true;
							}
						}
						if($lay['email3'] != '' && !$ismail) {
							if(!isset($tabmails[$lay['email3']])){
								$tabmails[$lay['email3']]=$lay['email3'];
								$ismail = true;
							}
						}

					}
				}
			}

			// construction de l'email
			$link='';
			foreach ($tabmails as  $mail) {
				if ($link=='') {
					$link='mailto:?subject='.$object.'&bcc='.$mail;
				}
				else {
					$link.=";".$mail;
				}
			}
			return $link;
		}

	}
}
