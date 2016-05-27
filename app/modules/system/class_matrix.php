<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class matrix extends dims_data_object {
	const TABLE_NAME ='dims_matrix';

	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public function save(){
		if($this->isNew()){
			$this->set('timestp_create',dims_createtimestamp());
			$this->set('timestp_modify',dims_createtimestamp());
			$this->set('id_workspace',$_SESSION['dims']['workspaceid']);
		}elseif($this->get('timestp_end') == 0){
			$this->set('timestp_modify',dims_createtimestamp());
		}
		if($this->get('id_city') > 0){

			require_once DIMS_APP_PATH.'/modules/system/class_city.php';
			$c = city::find_by(array('id'=>$this->get('id_city')),null,1);

			if(!empty($c)){
				require_once DIMS_APP_PATH.'/modules/system/class_region.php';
				$r = region::find_by(array('code'=>$c->get('code_reg')),null,1);
				if(!empty($r)) $this->set('id_region',$r->get('id_globalobject'));

				require_once DIMS_APP_PATH.'/modules/system/class_departement.php';
				$d = departement::find_by(array('code'=>$c->get('code_dep'),'code_reg'=>$c->get('code_reg')),null,1);
				if(!empty($d)) $this->set('id_departement',$d->get('id_globalobject'));

				require_once DIMS_APP_PATH.'/modules/system/class_canton.php';
				$ca = canton::find_by(array('code'=>$c->get('code_canton'),'code_arrond'=>$c->get('code_arrondissement'),'code_dep'=>$c->get('code_dep'),'code_reg'=>$c->get('code_reg')),null,1);
				if(!empty($ca)) $this->set('id_canton',$ca->get('id_globalobject'));

				require_once DIMS_APP_PATH.'/modules/system/class_arrondissement.php';
				$a = arrondissement::find_by(array('code'=>$c->get('code_arrondissement'),'code_dep'=>$c->get('code_dep'),'code_reg'=>$c->get('code_reg')),null,1);
				if(!empty($a)) $this->set('id_arrondissement',$a->get('id_globalobject'));
			}

		}
		return parent::save();
	}

	/*
	 * Cyril - 30/12/2011 > Fonction retournant la proportion d'activité des pays actifs dans le système
	 */
	function getCountriesActivity($lang=dims_const::_SYSTEM_LANG_EN, $mode='d15'){//2 = anglais
		if($lang==dims_const::_SYSTEM_LANG_FR) $column = 'c.fr';
		else $column = 'c.printable_name';
		$type = substr($mode, 0,1);
		$value = substr($mode, 1);
		$date_filter = ' ';
		$param = array();
		switch($type){
			default:
			case 'd'://days
				$date_filter = ' WHERE m.timestp_modify >= ?';
				$param[] = date( 'YmdHis', mktime( 0,0,0,date('n'),date('j')-$value,date('Y') ) );
				break;
			case 'a'://all
				$date_filter = ' ';
				break;
			case 'y'://specific year
				$date_filter = ' WHERE m.year = ?';
				$param[] = $value;
				break;
		}
		$sql = 'SELECT c.iso, count(*) as total, '.$column.' as label, c.id
				FROM dims_matrix m
				INNER JOIN dims_country c ON m.id_country = c.id '
				.$date_filter.'
				GROUP BY m.id_country
				ORDER BY total';

		$res = $this->db->query($sql,$param);
		$result = array();
		$reference = 0;
		while($tab = $this->db->fetchrow($res)){
			$iso = strtolower($tab['iso']);
			$result[$iso]['total'] = $tab['total'];
			$result[$iso]['label'] = $tab['label'];
			$result[$iso]['id'] = $tab['id'];
			$reference += $tab['total'];
		}

		foreach($result as $iso => $tab){
			if($reference > 0){//à priori si on est ici, il ne peut pas y'avoir de $reference 0, mais c'est pour prévenir une erreur de division par 0
				$result[$iso]['total'] = ceil(($tab['total']*100)/$reference);
			}
			else $result[$iso]['total'] = 50;//sinon pas de jaloux, tout le monde à 50%;
		}
		return $result;
	}

	//Cyril - 19/01/2012 - fonction permettant de traiter la purge d'une info selon une colonne et un id_go
	public function purgeData($col, $id_go){
		switch($col){
			case 'id_contact'://purge des liens d'un contact
				$this->db->query('UPDATE dims_matrix SET id_contact=0 WHERE id_contact= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));//mise à 0 de ses liens
				$this->db->query('UPDATE dims_matrix SET id_contact2=0 WHERE id_contact2= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));//mis à 0 si le contact était pote avec un autre
				break;
			case 'id_tiers'://purge des liens d'un tiers
				$this->db->query('UPDATE dims_matrix SET id_tiers=0 WHERE id_tiers= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));//mise à 0 de ses liens
				$this->db->query('UPDATE dims_matrix SET id_tiers2=0 WHERE id_tiers2= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));//mis à 0 si le contact était pote avec un autre
				break;
			case 'id_doc'://purge des docs
				$this->db->query('UPDATE dims_matrix SET id_doc=0 WHERE id_doc= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));
				break;
			case 'id_activity'://purge des activités
				$this->db->query('UPDATE dims_matrix SET id_activity=0 WHERE id_activity= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));
				break;
			case 'id_opportunity'://purge des activités
				$this->db->query('UPDATE dims_matrix SET id_opportunity=0 WHERE id_opportunity= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));
				break;
			case 'id_appointment_offer':
				$this->db->query('UPDATE dims_matrix SET id_appointment_offer=0 WHERE id_appointment_offer= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
				));
				break;
		}
		$this->renderCleanMatrix();
	}

	//Cyril - 26/01/2012 - Fonction permettant d'annuler un lien entre deux éléments, quelque soit les colonnes
	//Ben - 20/02/2012 - Fonction permettant d'annuler un lien entre plusieurs éléments, quelque soit les colonnes
	public function cutLink($keys){
		$clean = new matrix();
		$clean->init_description();
		$sql = 'UPDATE dims_matrix SET ';
		foreach ($keys as $key => $value) {
			if(isset($clean->fields[$key]))
				$sql .= $key.' = 0, ';
		}
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE ';
		$param = array();
		foreach ($keys as $key => $value) {
			if(isset($clean->fields[$key])){
				$sql .= $key.' = ? AND ';
				$param[] = $value;
			}
		}
		$sql = substr($sql, 0, -5);

		$this->db->query($sql,$param);
		$this->renderCleanMatrix();
	}

	//Cyril - 19/01/2012 - fonction permettant de cleaner les abérattions de la matrice pouvant émaner de manipulations diverses
	//fonction non testée car en stand-by suite au manque de connaissance sur ce que le client veut pour la suppression de contact / tiers / ...
	public function renderCleanMatrix(){
		//On supprime tout ce qui est vide sur les concepts majeurs
		$this->db->query('DELETE FROM dims_matrix WHERE id_action=0 AND
														id_activity=0 AND
														id_tiers=0 AND
														id_contact=0 AND
														id_doc=0 AND
														id_case=0 AND
														id_suivi=0');

		//on remet à 0 id_contact2 où on a un id_contact à 0 et id_contact2 > 0
		//NOTE : Ben préfèrerait faire un DELETE plutôt qu'un update mais j'ai peur qu'on supprime des infos qui faudrait pas.
		//Le seul risque c'est que ça génère des doublons mais les doublons seront éliminés le soir par un CRON
		$this->db->query('UPDATE dims_matrix SET id_contact2=0 WHERE id_contact=0 AND id_contact2>0');
	}

	public function addLink($values) {
		// on initialise this vu qu'on ajoute un lien à chaque fois
		// (c'est pratique pour les script de remontée d'infos)
		$this->new = true;
		$this->init_description();

		$yearInserted = false;
		$monthInserted = false;
		$workspaceInserted = false;

		foreach ($values as $key => $value) {
			if ($key == 'year') $yearInserted = true;
			if ($key == 'month') $monthInserted = true;
			if ($key == 'id_workspace') $workspaceInserted = true;
			$this->fields[$key] = $value;
		}

		if (!$yearInserted) {
			$this->fields['year'] = date('Y');
		}
		if (!$monthInserted) {
			$this->fields['month'] = date('m');
		}
		if (!$workspaceInserted) {
			$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		}

		$this->save();
	}

	//trouve la ligne d'initialisation d'un objet, quand celui est seul à être setté sur la table
	public static function getInitializationRowOf($key, $go){
		$matrix = new matrix();
		$matrix->init_description(true);
		$sql = 'SELECT * FROM '.$matrix->tablename.' WHERE ';
		$trouve = false;
		if ($go=="") $go=0;

		foreach($matrix->fields as $k => $v){
			if($k != 'id' && $k != 'month' && $k != 'timestp_modify' && $k != 'timestp_create' && $k != 'id_workspace' && $k != 'year' && $k != 'id_country'){
				if($k!=$key){
					$sql .= $k.'=0 AND ';
				}
				else{
					$trouve = true;
					$sql .= $k.'='.$go.' AND ';
				}
			}
		}
		if($trouve){
			$sql = substr($sql, 0, -5);
			//echo $sql;
			$res = $matrix->db->query($sql);
			$m = $matrix->db->fetchrow($res);
			$matrix->openFromResultSet($m);
			return $matrix;
		}
		else return null;
	}

	public static function exists($keys){
		$matrix = new matrix();
		$matrix->init_description();
		unset($matrix->fields['id']);
		unset($matrix->fields['timestp_modify']);
		unset($matrix->fields['id_workspace']);
		unset($matrix->fields['id_module']);

		if (!isset($keys['month'])) unset($matrix->fields['month']);
		if (!isset($keys['year'])) unset($matrix->fields['year']);

		$trouve = false;
		$sql = 'SELECT * FROM '.$matrix->tablename.' WHERE ';
		foreach($matrix->fields as $k => $v){
			if (isset($keys[$k]) && $keys[$k] > 0 && $keys[$k] != ''){
				$sql .= " $k = ".$keys[$k]." AND ";
				$trouve = true;
			}
			else {
				$sql .= " $k = 0 AND ";
			}
		}

		if ($trouve){
			$sql = substr($sql, 0, -5);
			$res = $matrix->db->query($sql);
			return $matrix->db->numrows($res) > 0;
		}
		else {
			return false;
		}
	}

	/*
	* Fonction qui recherche selon un pivot donné toutes les lignes de la matrix où la colonne $external_search > 0
	*/
	public static function getLinksOf($pivot, $pivot_value, $external_search){
		$matrix = new matrix();
		$matrix->init_description();
		$rows =  array();
		if(isset($matrix->fields[$pivot]) && isset($matrix->fields[$external_search])){
			$sql = "SELECT * FROM dims_matrix WHERE ".$pivot." = :pivot_value AND ".$external_search." > 0";
			$param = array();
			$param[":pivot_value"] = $pivot_value;
			$db = dims::getInstance()->getDb();
			$res = $db->query($sql,$param);
			while($fields = $db->fetchrow($res)){
				$m = new matrix();
				$m->openFromResultSet($fields);
				$rows[] = $m;
			}
		}
		return $rows;
	}

}
?>
