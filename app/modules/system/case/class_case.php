<?php

include_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture.php';

include_once DIMS_APP_PATH . 'modules/doc/class_docfolder.php';

/**
 * Description of dims_case
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class dims_case extends pagination{
	const TABLE_NAME = "dims_case";

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function save() {
		parent::save(dims_const::_SYSTEM_OBJECT_CASE);
	}

	/* getters */
	public function getLabel(){
		return $this->getAttribut("label", self::TYPE_ATTRIBUT_STRING);
	}

	public function getDatestart() {
		return $this->getAttribut("datestart", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getDateend() {
		return $this->getAttribut("dateend", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getIdModule() {
		return $this->getAttribut("id_module", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdWorkspace() {
		return $this->getAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY);
	}

	public function getIdUser() {
		return $this->getAttribut("id_user", self::TYPE_ATTRIBUT_KEY);
	}

	public function getStatus(){
		return $this->getAttribut("status", self::TYPE_ATTRIBUT_NUMERIC);
	}

	public function getUser(){
		require_once DIMS_APP_PATH.'modules/system/class_user.php';
		$user = new user();
		$user->open($this->getIdUser());
		return $user;
	}

	/* setters */
	public function setLabel($label, $save = false){
		$this->setAttribut("label", self::TYPE_ATTRIBUT_STRING, $label, $save);
	}

	public function setDatestart($dateStart, $save = false){
		$this->setAttribut("datestart", self::TYPE_ATTRIBUT_NUMERIC, $dateStart, $save);
	}

	public function setDateend($dateEnd, $save = false){
		$this->setAttribut("dateend", self::TYPE_ATTRIBUT_NUMERIC, $dateEnd, $save);
	}

	public function setStatus($status, $save = false){
		$this->setAttribut("status", self::TYPE_ATTRIBUT_NUMERIC, $status, $save);
	}

	/* retourne une liste de global object */
	public function getFilesAttach(){
		return $this->searchGbLink(dims_const::_SYSTEM_OBJECT_DOCFILE);
	}

	public function getContactAttach(){
		return $this->searchGbLink(dims_const::_SYSTEM_OBJECT_CONTACT);
	}
	public function getTiersAttach(){
		return $this->searchGbLink(dims_const::_SYSTEM_OBJECT_TIERS);
	}

	/* retourne liste object file */
	public function getObjectFilesAttach(){
		$res = array();
		$lst = $this->getFilesAttach();
		require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
		foreach($lst as $idfile){
			$file = new docfile();
			$file->openWithGB($idfile);
			$res[] = $file;
		}
		return $res;
	}

	/* retourne le contact associé à ce dossier */
	public function getObjectContactAttach(){
		$lst = $this->getContactAttach();
		if(count($lst) > 0){
			require_once DIMS_APP_PATH.'modules/system/class_contact.php';
			$ct = new contact();
			return $ct->openWithGB(current($lst));
		}
		return false;
	}
	public function getObjectTierAttach(){
		$lst = $this->getTiersAttach();
		if(count($lst) > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			$ct = new tiers();
			return $ct->openWithGB(current($lst));
		}
		return false;
	}

	/* retourne la liste des dossiers */
	static function getCurrentUserCases($limit = 0, $idUser = 0,$idCateg = 0,$idworkspace=0){
		$db = dims::getInstance()->getDb();
		$lstRetour = array();
		$param = array();
		$sqlfilter="";
		//if ($idUser == 0) $idUser = $_SESSION['dims']['userid'];
		if ($idUser > 0) {
			$sqlfilter.=" AND id_user= :iduser ";
			$param[':iduser'] = $idUser;
		}
		if ($idworkspace > 0) {
			$sqlfilter.=" AND id_workspace= :idworkspace ";
			$param[':idworkspace'] = $idworkspace;
		}

		$lstCase = '';
		if ($idCateg > 0){
			require_once DIMS_APP_PATH.'modules/system/class_category.php';
			$cat = new category();
			$cat->open($idCateg);
			$cat->initDescendance();
			$sqlfilter = ' AND	id_globalobject IN ('.implode(',',$cat->searchGbLinkChild(dims_const::_SYSTEM_OBJECT_CASE)).') ';
		}
		$sel = "SELECT		*
				FROM		dims_case
				WHERE		1=1
				".$sqlfilter."
				ORDER BY	id DESC";
		if ($limit > 0){
			$sel .= " LIMIT	:limit ";
			$param[':limit'] = $limit;
		}

		$res = $db->query($sel, $param);
		while($r = $db->fetchrow($res)){
			$case = new dims_case();
			$case->openWithFields($r);
			$lstRetour[] = $case;
		}
		return $lstRetour;
	}

	static function getUserAndLinksFromCases($lstcase,$id_module_type) {
		$db = dims::getInstance()->getDb();
		$struct=array();
		$struct['user']=array();
		$struct['case']=array();
		$listids = array();
		$listidusers = array();

		// on construit la liste des id de case
		foreach ($lstcase as $id => $case) {
			$listids[] = $case->fields['id_globalobject'];
			$listidusers[] = $case->fields['id_user'];
		}

		// user
		$sql = "select distinct id,lastname,firstname from dims_user where id in (".$db->getParamsFromArray($listidusers, 'iduser', $params).")";

		$res=$db->query($sql, $params);
		// boucle sur les users
		while ($u=$db->fetchrow($res)) {
			$struct['user'][$u['id']]=$u;
		}

		// boucle sur les links
		$myGlobalObject = new dims_globalobject();
		$struct['case']=$myGlobalObject->searchLinkFromListObjects($listids,$id_module_type);
		return $struct;
	}

	public function search($label = "", $type = -1, $id_workflow = 0, $id_state = 0, $id_client = 0, $id_responsable = 0, $pagination = false){
		$db = dims::getInstance()->getDb();

		$params = array(
			':idw' => $_SESSION['dims']['workspaceid'],
		);

		$limit = "";
		if(!$pagination){
			self::liste_page($this->search($label, $type, $id_workflow, $id_state, $id_client, $id_responsable,true));
			$params[':start'] = array('type'=>PDO::PARAM_INT,'value'=>$this->sql_debut);
			$params[':end'] = array('type'=>PDO::PARAM_INT,'value'=>$this->limite_key);
			$limit = " LIMIT :start, :end ";
		}

		$where = array();
		if(!empty($label)){
			$where[] = "AND (dossier.label LIKE :l1 OR dossier.long_label LIKE :l2 OR dossier.description LIKE :l3)";
			$params[':l1'] = "%$label%";
			$params[':l2'] = "%$label%";
			$params[':l3'] = "%$label%";
		}

		switch($type){
			case gescom_workflow_step::_TYPE_FINISHED:
			case gescom_workflow_step::_TYPE_CANCELLED:
			case gescom_workflow_step::_TYPE_WAITING:
				$where[] = "AND workflow_step.type = :type";
				$params[':type'] = $type;
				break;
			default:
				break;
		}

		if($id_workflow > 0){
			$where[] = "AND dossier.id_workflow = :idwork";
			$params[':idwork'] = $id_workflow;
		}

		if($id_state > 0){
			$where[] = "AND dossier.status = :state";
			$params[':state'] = $id_state;
		}

		if($id_client > 0){
			$where[] = "AND contact.id = :idc";
			$params[':idc'] = $id_client;
		}

		if($id_responsable > 0){
			$where[] = "AND user.id = :idu";
			$params[':idu'] = $id_responsable;
		}

		$sel = "SELECT 		dossier.*, workflow.*, workflow_step.*, contact.*, responsable.*
				FROM 		".self::TABLE_NAME." dossier
				INNER JOIN 	".gescom_workflow::TABLE_NAME." workflow
				ON 			dossier.id_workflow = workflow.id
				INNER JOIN 	".gescom_workflow_step::TABLE_NAME." workflow_step
				ON 			dossier.status = workflow_step.id
				AND 		workflow.id = workflow_step.id_workflow
				INNER JOIN 	".matrix::TABLE_NAME." matrix
				ON 			dossier.id_globalobject = matrix.id_case
				INNER JOIN 	".contact::TABLE_NAME." contact
				ON 			matrix.id_contact = contact.id_globalobject
				INNER JOIN 	".user::TABLE_NAME." responsable
				ON 			dossier.id_manager = responsable.id
				WHERE 		dossier.id_workspace = :idw
				".implode(" ", $where)."
				ORDER BY 	dossier.datestart DESC
				$limit";
		$res = $db->query($sel,$params);
		if ($pagination) {
			return $db->numrows($res);
		}else{
			return $db->split_resultset($res);
		}
	}

	public function initFolder() {
		if ($this->fields['id_folder'] == '' || $this->fields['id_folder'] <= 0) {
			$tmstp = dims_createtimestamp();

			$fold = new docfolder();
			$fold->init_description();
			$fold->setugm();

			$fold->fields['name'] = 'root_'.$this->fields['id_globalobject'];
			$fold->fields['parents'] = 0;
			$fold->fields['timestp_create'] = $tmstp;
			$fold->save();

			$this->fields['id_folder'] = $fold->fields['id'];

			$fold->save(); // pr la synchro

			$this->save();
		}

		return $this->fields['id_folder'];
	}
}

?>
