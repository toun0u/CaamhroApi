<?php
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	media
* @access	public
*/

//Cyril 07/03/2012 - gestion des services rattachés à ce tiers
include_once DIMS_APP_PATH.'/modules/system/class_dims_sync.php';
include_once DIMS_APP_PATH.'/modules/system/class_tiers_service.php';

class tiers extends DIMS_DATA_OBJECT {
	const TABLE_NAME = "dims_mod_business_tiers";
	const TIERS_INACTIF = 1;
	const TIERS_ACTIF = 0;
	const CRM_TYPE_CLIENT = 0;
	const CRM_TYPE_FOURNISSEUR = 1;
	const MY_GLOBALOBJECT_CODE = dims_const::_SYSTEM_OBJECT_TIERS;
	private $services;
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	public $contacts;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
		$this->fields['date_creation'] = date('YmdHis');
		$contacts="";
		$this->has_one('dims_sync'	, dims_sync::TABLE_NAME, 'id', 'id_tiers');
	}

	function updateFieldLog($field,$value,$id_mbfield,$private,$type_layer=0) {
		require_once(DIMS_APP_PATH . '/modules/system/class_tiers_mbfield.php');
		$t_mbf = new tiersmbfield();
		$t_mbf->init_description();
		$t_mbf->fields['id_tiers'] = $this->fields['id']; //la fiche concernee
		$t_mbf->fields['id_mbfield'] = $id_mbfield;
		$t_mbf->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$t_mbf->fields['id_user'] = $_SESSION['dims']['userid']; //la personne qui modifie
		$t_mbf->fields['id_module'] = $_SESSION['dims']['moduleid'];
		$t_mbf->fields['value'] = $value;
		$t_mbf->fields['timestp_modify'] = date("YmdHis");
		$t_mbf->fields['id_lang'] = $_SESSION['dims']['currentlang'];
		$t_mbf->fields['private'] = $private;
		$t_mbf->fields['type_layer'] = $type_layer;

		$t_mbf->save();
		///dims_print_r($t_mbf);die();
	}

	public function getVcard(){
		$courPath = realpath('.');
		$dirExport = DIMS_TMP_PATH . '/vcardexport/';
		if (!file_exists($dirExport))
			mkdir($dirExport);
		$sid = session_id();
		if (!file_exists($dirExport.$sid))
			mkdir($dirExport.$sid);
		$photo = '';
		if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
			$photo = 'PHOTO;ENCODING=b;TYPE=PNG:'.base64_encode(file_get_contents($this->getPhotoWebPath(60)));
		$vcard = fopen($dirExport.$sid.'/'.$this->fields['intitule'].".vcf",'w+');
		$content = "begin:vcard
version:2.1
fn:".$this->fields['intitule']."
adr;TYPE=WORK:;;".$this->fields['adresse'].";".$this->fields['ville'].";;".$this->fields['codepostal'].";".$this->fields['pays']."
email;internet:".$this->fields['mel']."
tel;work:".$this->fields['telephone']."
$photo
end:vcard
";
		fwrite($vcard,$content);
		fclose($vcard);
		chdir($courPath);
		return $dirExport.$sid.'/'.$this->fields['intitule'].".vcf";
	}

	public function updateMatrice(){
		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		if( ! method_exists($this, 'getMatrixInstance') )
			$matrix = new matrix();
		else $matrix = $this->getMatrixInstance();
		$row = $matrix->getInitializationRowOf('id_tiers', $this->fields['id_globalobject']);

		if( $row->isNew() ){
			$row->init_description(true);
			$row->fields['id_tiers'] = $this->fields['id_globalobject'];
			$row->fields['year'] = date('Y');
			$row->fields['month'] = date('n');
		}
		//par défaut on met à jour ces informations suivantes, qu'on soit en édition ou création
		$row->fields['id_country'] = $this->fields['id_country'];
		$row->fields['timestp_modify'] = dims_createtimestamp();
		$row->save();

		// gestion des liens fils/père des tiers
		if($this->get('id_tiers') != '' && $this->get('id_tiers') > 0){
			$parent = tiers::find_by(array('id'=>$this->get('id_tiers')),null,1);
			if(!empty($parent)){
				$matrix = matrix::find_by(array('id_tiers'=>$this->get('id_globalobject'),'id_tiers2'=>$parent->get('id_globalobject')),null,1);
				$matrix2 = matrix::find_by(array('id_tiers2'=>$this->get('id_globalobject'),'id_tiers'=>$parent->get('id_globalobject')),null,1);
				if(empty($matrix) && empty($matrix2)){
					$matrice = new matrix();
					$matrice->fields['id_tiers'] = $this->fields['id_globalobject'];
					$matrice->fields['id_tiers2'] = $parent->fields['id_globalobject'];
					$matrice->fields['year'] = substr($this->fields['date_creation'],0,4);
					$matrice->fields['month'] = substr($this->fields['date_creation'],4,2);
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();

					$matrice = new matrix();
					$matrice->fields['id_tiers2'] = $this->fields['id_globalobject'];
					$matrice->fields['id_tiers'] = $parent->fields['id_globalobject'];
					$matrice->fields['year'] = substr($this->fields['date_creation'],0,4);
					$matrice->fields['month'] = substr($this->fields['date_creation'],4,2);
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();
				}
			}
		}
		return $row;
	}

	function save() {

		if (empty($this->fields['id_workspace'])) {
			$this->setIdWorkspace($_SESSION['dims']['workspaceid']);
		}

		if (isset($this->fields['intitule']) && !isset($this->fields['intitule_search'])) {
			$this->fields['intitule_search'] = business_format_search($this->fields['intitule']);
		$this->fields['intitule_search'] = business_format_lastname($this->fields['intitule']);
		}
		require_once DIMS_APP_PATH.'/modules/system/include/business.php';
		if (isset($this->fields['motscles'])) $this->fields['motscles_search'] = business_format_search($this->fields['motscles']);
		if (isset($this->fields['commentaire'])) $this->fields['commentaire_search'] = business_format_search($this->fields['commentaire']);
		if (isset($this->fields['telephone'])) $this->fields['telephone'] = business_format_tel($this->fields['telephone']);
		if (isset($this->fields['telecopie'])) $this->fields['telecopie'] = business_format_tel($this->fields['telecopie']);
		if (isset($this->fields['telmobile'])) $this->fields['telmobile'] = business_format_tel($this->fields['telmobile']);
		if (isset($this->fields['ville'])) $this->fields['ville'] = business_format_search($this->fields['ville']);

		$this->fields['date_maj'] = date(dims_const::DIMS_DATEFORMAT_US);
		if ($this->fields['date_creation'] == '' || $this->fields['date_creation'] == '0000-00-00') $this->fields['date_creation'] = date("YmdHis");

		$this->fields['id_user'] = $_SESSION['dims']['userid'];
		if($this->new && $this->fields['id_user_create'] == '') {
			$this->fields['id_user_create'] = $_SESSION['dims']['userid'];
		}

		//Cyril 07/03/2012 - création du service associé
		$need_service_creation = false;
		if($this->isNew()){
			$need_service_creation = true;
		}
		//$this->fields['id_module']=1; // hack
		$res = parent::save(self::MY_GLOBALOBJECT_CODE);

		//dims_print_r($this->fields);die();
		// voir avec Cyril
		$this->createMyRootService($need_service_creation);
		$this->updateMatrice();
		return($res);
	}

	/* Cyril - 15/03/2012 - Fonction permettant de créer le service racine associé au tiers courant */
	private function createMyRootService($need_service_creation = true){
		if($need_service_creation){
			$service = $this->getServiceInstance();//si dans une classe héritée, PENSER À surcharger la fonction
			if (isset($this->fields['intitule']) && $this->fields['intitule']!="")
				$service->create($this->fields['intitule'], '', $this->getId());
		}
	}

	function getDynamicFields($share=false) {
		$lst=array();

		$sql = "select		mf.*,
					mb.name as namefield,
					mb.label as labelfield
			from		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON		mb.id=mf.id_mbfield
			inner join	dims_mod_business_meta_categ as mc
			ON		mc.id = mf.id_metacateg
			where		mf.id_object= :idobject";

		if ($share) {
			$sql.=" and mode=1";
		}
		$sql.= " order by mc.position,mf.position";

		$res=$this->db->query($sql, array(
			':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
		));
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$lst[$f['id']]=$f;
			}
		}

		return $lst;
	}


	public function getMbFields() {
		$lst=array();

		$res=$this->db->query("	SELECT		f.*,t.name as tablename
					FROM		dims_mb_field f
					INNER JOIN	dims_mb_table t
					ON		f.id_table = t.id
					WHERE		t.name='dims_mod_business_tiers'");
		if ($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$lst[$f['id']]=$f;
			}
		}
		return $lst;
	}

	/**
	 *
	 * @return type
	 * @deprecated
	 */
	public function getContacts() {
		if ($this->contacts=='') {
			$this->contacts=array();
			$this->contacts[0]=0;
			$res=$this->db->query("SELECT distinct id_contact from dims_mod_business_tiers_contact where id_tiers= :idtier", array(
				':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
			if ($this->db->numrows($res)>0) {
				while ($f=$this->db->fetchrow($res)) {
					$this->contacts[$f['id_contact']]=$f['id_contact'];
				}
			}
		}

		return $this->contacts;
	}

	public function getIntitule(){
		return $this->getAttribut('intitule', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getCommentaire(){
		return $this->getAttribut('commentaire', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getAdresse(){
		return $this->getAttribut('adresse', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getAdresse2(){
		return $this->getAttribut('adresse2', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getAdresse3(){
		return $this->getAttribut('adresse3', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getCodePostal(){
		return $this->getAttribut('codepostal', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getPays() {
		return $this->getAttribut("pays", self::TYPE_ATTRIBUT_STRING);
	}
	public function getIdPays() {
		return $this->getAttribut("id_country", self::TYPE_ATTRIBUT_KEY);
	}

	public function getVille(){
		return $this->getAttribut('ville', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getTelephone(){
		return $this->getAttribut('telephone', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getTelecopie(){
		return $this->getAttribut('telecopie', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getTelMobile(){
		return $this->getAttribut('telmobile', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getMel(){
		return $this->getAttribut('mel', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getSiren(){
		return $this->getAttribut('ent_siren', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getNic(){
		return $this->getAttribut('ent_nic', parent::TYPE_ATTRIBUT_STRING);
	}
	public function getApe(){
		return $this->getAttribut('ent_ape', parent::TYPE_ATTRIBUT_STRING);
	}

	public function getPhotoPath($size=60){
		$path = null;
		if(isset($this->fields['photo'])){
			$directory = realpath('.').'/data/photo_ent/ent_'.$this->fields['id'];
			if(file_exists($directory)){
				$path = $directory.'/photo'.$size.$this->fields['photo'].'.png';
				if(!file_exists($path)){
					$original = $directory.'/original.png';
					if(file_exists($original)){
						dims_resizeimage2($original, $size, $size,'png',$path);
					}
					else{//dernière chance
						$content = scandir($directory);
						if(!empty($content)){
							$pattern = '/^photo([0-9]+)_.*$/';
							$max = 0;
							$ref = '';
							foreach($content as $photo) {
								if(preg_match($pattern, $photo, $matches)){
									if($matches[1] > $max){
										$max = $matches[1];
										$ref = $photo;
									}
								}
							}
							if($max > 0 && $ref != ''){
								dims_resizeimage2($directory.'/'.$ref, $size, $size,'png',$path);
							}
						}
					}
				}
			}
		}
		return $path;
	}

	public function getPhotoWebPath($size=60){
		return _DIMS_WEBPATHDATA.'photo_ent/ent_'.$this->fields['id'].'/photo'.$size.$this->fields['photo'].'.png';
	}

	public function setIntitule($intitule, $save = false){
		$this->setAttribut("intitule", self::TYPE_ATTRIBUT_STRING, $intitule, $save);
	}

	public function setTypeTiers($type_tiers, $save = false){
		$this->setAttribut("type_tiers", self::TYPE_ATTRIBUT_NUMERIC, $type_tiers, $save);
	}

	public function setIdModule($id_module, $save = false){
		$this->setAttribut("id_module", self::TYPE_ATTRIBUT_KEY, $id_module, $save);
	}

	public function setIdWorkspace($id_workpsace, $save = false){
		$this->setAttribut("id_workspace", self::TYPE_ATTRIBUT_KEY, $id_workpsace, $save);
	}

	public function getIdGlobalobject() {
		return $this->getAttribut("id_globalobject", self::TYPE_ATTRIBUT_KEY);
	}


	public function buildNewEntStep1($actionform='',$saveredirect='',$popupid) {
		$db=$this->db;
		global $dims;
		global $_DIMS;
		$this->init_description(); // met les champs a vides
		$ent=$this;
		unset($_SESSION['business']['tiers_id']);
		?>

		<div>
			<div class="actions">
				<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $popupid; ?>');">
					<img src="modules/system/desktopV2/templates/gfx/common/close.png" />
				</a>
			</div>
			<h2>
				<?php
				echo $_DIMS['cste']['_IMPORT_TAB_NEW_COMPANY'];
				?>
			</h2>
			<div style="padding:2px;height:450px;overflow-x:auto;">
				<?
				// creation de la fiche dynamique
				$_SESSION['dims']['crm_newent_actionform'] = $actionform;
				$_SESSION['dims']['crm_newent_saveredirect'] = $saveredirect;
				include(DIMS_APP_PATH . "/modules/system/crm_public_ent_form.php");
				?>
			</div>
		</div>
		<?
	}

	/*
	 * Fonction permettant la modification des données
	 */
	public function buildEntForm($actionform='',$saveredirect='',$op='display') {
		$db = dims::getInstance()->getDb();
		global $dims;
		global $_DIMS;
		// creation de la fiche dynamique
		$_SESSION['dims']['crm_newent_actionform'] = $actionform;
		$_SESSION['dims']['crm_newent_saveredirect'] = $saveredirect;

		$ent=$this;
		include(DIMS_APP_PATH . "/modules/system/crm_public_ent_form.php");
	}

	/*
	 * Fonction permettant d'ajouter un service
	 */
	public function addService($idChild){
		$child = new tiers();
		$child->open($idChild);
		$child->fields['id_parent'] = $this->fields['id'];
		$child->fields['type_tiers'] = $this->fields['type_tiers'];
		if ($this->fields['id_parent'] == 0)
			$child->fields['parents'] = $this->fields['id'];
		else
			$child->fields['parents'] = $this->fields['parents'].';'.$this->fields['id'];
		$child->save();
	}
	/*
	 * Function retournant la liste des services directs de l'entreprise
	 */
	public function getServices(){
		$lst = array();
		$sel = "SELECT	*
				FROM	dims_mod_business_tiers
				WHERE	id_parent = :idtier";
		$res = $this->db->query($sel, array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		while ($r = $this->db->fetchrow($res)){
			$tiers = new tiers();
			$tiers->openWithFields($r);
			$lst[$r['id']] = $tiers;
		}
		return $lst;
	}
	/*
	 * Function retournant la liste complète des services de l'entreprise
	 */
	public function getAllServices(){
		$lst = $this->getServices();
		$res = array();
		foreach($lst as $child){
			$child['service_child'] = $child->getAllServices();
			$res[$child->fields['id']] = $child;
		}
		return $res;
	}

	 public function setAdresse($adresse, $save = false) {
		$this->setAttribut("adresse", self::TYPE_ATTRIBUT_STRING, $adresse, $save);
	}

	public function setAdresse2($adresse2, $save = false) {
		$this->setAttribut("adresse2", self::TYPE_ATTRIBUT_STRING, $adresse2, $save);
	}

	public function setAdresse3($adresse3, $save = false) {
		$this->setAttribut("adresse3", self::TYPE_ATTRIBUT_STRING, $adresse3, $save);
	}

	public function setCodePostal($codepostal, $save = false) {
		$this->setAttribut("codepostal", self::TYPE_ATTRIBUT_STRING, $codepostal, $save);
	}

	public function setVille($ville, $save = false) {
		$this->setAttribut("ville", self::TYPE_ATTRIBUT_STRING, $ville, $save);
	}

	public function setidCity($id_city, $save = false) {
		$this->setAttribut("id_city", self::TYPE_ATTRIBUT_KEY, $id_city, $save);
	}

	public function setTelephone($telephone, $save = false) {
		$this->setAttribut("telephone", self::TYPE_ATTRIBUT_STRING, $telephone, $save);
	}

	public function setTelecopie($telecopie, $save = false) {
		$this->setAttribut("telecopie", self::TYPE_ATTRIBUT_STRING, $telecopie, $save);
	}

	public function setMel($mel, $save = false) {
		$this->setAttribut("mel", self::TYPE_ATTRIBUT_STRING, $mel, $save);
	}

	public function setPays($pays, $save = false) {
		$this->setAttribut("pays", self::TYPE_ATTRIBUT_STRING, $pays, $save);
	}

	public function setIdPays($id_pays, $save = false) {
		$this->setAttribut("id_country", self::TYPE_ATTRIBUT_KEY, $id_pays, $save);
	}

	public function setNic($nic, $save = false) {
		$this->setAttribut("ent_nic", self::TYPE_ATTRIBUT_STRING, $nic, $save);
	}

	public function setApe($ape, $save = false) {
		$this->setAttribut("ent_ape", self::TYPE_ATTRIBUT_STRING, $ape, $save);
	}

	public function setSiren($siren, $save = false) {
		$this->setAttribut("ent_siren", self::TYPE_ATTRIBUT_STRING, $siren, $save);
	}

	public function updateIdCountry($countryArray = array()) {
		if(empty($countryArray)) {
			// conversion des tags vers country
			$resu=$this->db->query('SELECT * FROM dims_country');
			$c=0;
			if ($this->db->numrows($resu)>0) {
				while($a = $this->db->fetchrow($resu)) {
					$countryArray[strtoupper($a['printable_name'])]=$a['id'];
					$countryArray[strtoupper($a['fr'])]=$a['id'];
				}
			}
		}

		$id_country = 0;
		if ($this->fields['pays']!='') {
			// traitement des pays type France - Europe
			$lieux=str_replace(array("-",";"),",",$this->fields['pays']);
			$alieux=explode(',',$lieux);

			foreach ($alieux as $lieu) {
				$lieu=trim($lieu);
				$wordlength=strlen($lieu);

				if ($wordlength>0) {
					if (isset($countryArray[strtoupper($lieu)])) {
						$id_country=$countryArray[strtoupper($lieu)];
					}
					else {
						// recherche du pays pour ct / entreprise
						foreach ($countryArray as $country=>$idc) {
							$res = similar_text(trim(strtoupper($lieu)) ,substr($country,0,$wordlength),$percent);

							if ($percent>=85) {
								$id_country=$idc; // on a trouve le pays
								break;
							}
						}
					}
				}
			}

			// on test un dernier cas
			if ($id_country==0) {
				// on essaie avec les espaces
				$alieux=explode(' ',$this->fields['pays']);

				foreach ($alieux as $lieu) {
					$lieu=trim($lieu);
					$wordlength=strlen($lieu);

					if ($wordlength>0) {
						if (isset($countryArray[strtoupper($lieu)])) {
							$id_country=$countryArray[strtoupper($lieu)];
						}
						else {
							// recherche du pays pour ct / entreprise
							foreach ($countryArray as $country=>$idc) {
								$res = similar_text(trim(strtoupper($lieu)) ,substr($country,0,$wordlength),$percent);

								if ($percent>=85) {
									$id_country=$idc; // on a trouve le pays
									break;
								}
							}
						}
					}
				}
			}

			if ($id_country>0) $this->db->query('UPDATE '.self::TABLE_NAME.' SET id_country = :idcountry WHERE '.self::TABLE_NAME.'.'.$this->idfields[0].' = :idtier', array(
				':idcountry' => array('type' => PDO::PARAM_INT, 'value' => $id_country),
				':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			));
		}
		return $id_country;
	}

	public function getContactsLinkedByType($type_link){
		$employees = array();
		$c = new dims_constant();
		$types = $c->getAllValues($type_link);
		$types = str_replace("'","''",$types);
		$i=0;
		$in = '';
		$in = "'".implode("','",$types)."'";
		$res = $this->db->query("
			SELECT		tc.function, c.*
			FROM		dims_mod_business_tiers_contact tc
			INNER JOIN	dims_mod_business_contact c
			ON			c.id = tc.id_contact
			AND			c.inactif = 0
			WHERE		tc.id_tiers = :idtier
			AND			type_lien IN (".$in.")
			AND			(date_fin = 0 OR date_fin >= ".dims_createtimestamp().")
			GROUP BY	c.id
			ORDER BY	tc.date_create DESC", array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while($tab = $this->db->fetchrow($res)){
			$employee = new contact();
			$employee->openFromResultSet($tab);
			$employees[] = $employee;
		}
		return $employees;
	}
	public function getAllContactsLinkedByType($type_link){
		require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
		require_once DIMS_APP_PATH.'modules/system/class_contact.php';
		$employers = array();
		$c = new dims_constant();
		$types = $c->getAllValues($type_link);
		$types = str_replace("'","''",$types);

		$params = array();
		$db = dims::getInstance()->getDb();
		$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		$res = $db->query("	SELECT		c.*, tc.function, tc.id_ct_user_create, tc.date_create, tc.date_fin, tc.date_deb
							FROM		".tiersct::TABLE_NAME." tc
							INNER JOIN 	".contact::TABLE_NAME." c
							ON 			c.id = tc.id_contact
							WHERE		tc.id_tiers = :idtiers
							AND			tc.type_lien IN (".$this->db->getParamsFromArray($types, 'typelien', $params).")
							ORDER BY	tc.date_create DESC", $params);
		$sep = $db->split_resultset($res);
		foreach($sep as $r){
			$contact = new contact();
			$contact->openFromResultSet($r['c']);
			$contact->setLightAttribute('id_ct',$this->get('id'));
			$contact->setLightAttribute('function',$r['tc']['function']);
			$contact->setLightAttribute('id_ct_user_create',$r['tc']['id_ct_user_create']);
			$contact->setLightAttribute('date_create',$r['tc']['date_create']);
			$contact->setLightAttribute('date_fin',$r['tc']['date_fin']);
			$employers[] = $contact;
		}
		return $employers;
	}

	//Cyril - 19/01/2012 - fonction permettant de désactiver un contact
	public function desactive(){
		$this->fields['inactif'] = self::TIERS_INACTIF;
	}

	//Cyril - 19/01/2012 - fonction permettant de resactiver un contact
	public function reactive(){
		$this->fields['inactif'] = self::TIERS_ACTIF;
	}

	//Cyril - 19/01/2012 - fonction permettant de déterminer si le contact est actif ou non
	public function isActif(){
		return $this->fields['inactif'] == self::TIERS_ACTIF;
	}

	//Cyril - 20/01/2012 - fonction permettant de dégager tous les liens d'un tiers
	public function cutMyLinks(){
		//on commence par les ct_links
		$this->db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact1= :idtier AND id_object='.dims_const::_SYSTEM_OBJECT_TIERS, array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		$this->db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact2= :idtier AND id_object='.dims_const::_SYSTEM_OBJECT_TIERS, array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		//les tiers_contact
		$this->db->query('DELETE FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtier', array(
			':idtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		//les keywords index
		$this->db->query('DELETE FROM dims_keywords_index WHERE id_globalobject= :idglobalobject', array(
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
		));
		//la matrice
		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		$matrix = new matrix();
		$matrix->purgeData('id_tiers', $this->fields['id_globalobject']);
	}

	public function fusionTiers($id,$idgb){
	//on commence par les ct_links
	$this->db->query('UPDATE dims_mod_business_ct_link SET id_contact1 = :idcontact WHERE id_contact1= :currentidtier AND id_object='.dims_const::_SYSTEM_OBJECT_TIERS, array(
		':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id),
		':currentidtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	$this->db->query('UPDATE dims_mod_business_ct_link SET id_contact2 = :idcontact WHERE id_contact2= :currentidtier AND id_object='.dims_const::_SYSTEM_OBJECT_TIERS, array(
		':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id),
		':currentidtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	//les tiers_contact
	$this->db->query('UPDATE dims_mod_business_tiers_contact SET id_tiers = :idtier WHERE id_tiers= :currentidtier', array(
		':idtier' => array('type' => PDO::PARAM_INT, 'value' => $id),
		':currentidtier' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	//les keywords index
	$this->db->query('DELETE FROM dims_keywords_index WHERE id_globalobject= :idglobalobject', array(
		':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
	));
	//la matrice
	$this->db->query('UPDATE dims_matrix SET id_tiers = :idtier WHERE id_tiers= :currentidtier', array(
		':idtier' => array('type' => PDO::PARAM_INT, 'value' => $idgb),
		':currentidtier' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
	));
	$this->db->query('UPDATE dims_matrix SET id_tiers2 = :idtier WHERE id_tiers2= :currentidtier', array(
		':idtier' => array('type' => PDO::PARAM_INT, 'value' => $idgb),
		':currentidtier' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
	));
	}

	public function mergeSave(){

	/*$leven = dims::getInstance()->dims_levenshteinTiers($this->fields['intitule'],1);
	if (count($leven) > 0)
		return $leven;
	else{
		$this->setugm();
		$this->save();
		return $this->fields['id'];
	}*/


		if ($this->fields['intitule']!="") {

			$params=array();
			$params[':intitule']=trim($this->fields['intitule']);

			$resu=$this->db->query('SELECT * FROM dims_mod_business_tiers where intitule like :intitule',$params);
			$c=0;
			if ($this->db->numrows($resu)>0) {
				if($a = $this->db->fetchrow($resu)) {
					$this->open($a['id']);
					return $this->fields['id'];
				}

			}
			$leven = dims::getInstance()->dims_levenshteinTiers($this->fields['intitule'],1);

		}
		if (count($leven) > 0) {

			if (count($leven==1) && isset($leven[0]['id_tiers'])) {
				$this->open($leven[0]['id_tiers']);
				return $this->fields['id'];
			}
			return $leven;
		}

		else{
			$this->setugm();
			$this->save();
			return $this->fields['id'];
		}
	}

	public static function findByTitle($title, $limit = null, $max_distance = 3){
	$db = dims::getInstance()->getDb();
	$limit_sql = '';
	$params = array();
	if( ! is_null($limit) && !empty($limit) ) {
		$limit_sql = ' LIMIT 0,:limit';
		$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limit);
	}
	$params[':title'] = array('type' => PDO::PARAM_STR, 'value' => $title);
	$res = $db->query("SELECT * FROM ".self::TABLE_NAME." WHERE intitule LIKE :title ".$limit_sql, $params);
	$tab_tiers = array();
	if($db->numrows($res)){
		while($fields = $db->fetchrow($res)){
			$t = new tiers();
			$t->openFromResultSet($fields);
			$tab_tiers[0][] = $t;//0 est la distance minimale
		}
	}
	else{//on tente un levenshtein
		$res = $db->query("SELECT * FROM ".self::TABLE_NAME);
		while($fields = $db->fetchrow($res)){
			$dist = levenshtein($title, $fields['intitule']);
				if($dist <= $max_distance){
					$t = new tiers();
					$t->openFromResultSet($fields);
					$tab_tiers[$dist][$t->getId()] = $t;
				}
		}
		}
		return $tab_tiers;
	}

	public static function getAllTiers($id_workspace = 0, $order = '') {
	if ($id_workspace == 0) {
		$id_workspace = $_SESSION['dims']['workspaceid'];
	}
	if ($order == '') {
		$order = 'intitule ASC';
	}

	$db = dims::getInstance()->getDb();

	$a_tiers = array();
	$rs = $db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE id_workspace = :idworkspace ORDER BY '.$order, array(
		':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
	));
	while ($row = $db->fetchrow($rs)) {
		$tiers = new tiers();
		$tiers->openFromResultSet($row);
		$a_tiers[] = $tiers;
	}

	return $a_tiers;
	}

	//Cyril - 08/03/2012 - Fonction permettant de retourner l'id du service root du tiers
	public function getMyRootServiceID(){
		$sql = "SELECT id
			FROM ".tiers_service::TABLE_NAME."
			WHERE id_tiers= :idtiers
			AND id_parent=".tiers_service::SERVICE_ROOT."
			LIMIT 0,1";//on assume qu'il ne peut y en avoir qu'un
	$res = $this->db->query($sql, array(
		':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	if($this->db->numrows()){
		$elem = $this->db->fetchrow($res);
		return $elem['id'];
	}
	else return null;
	}

	//Cyril - 15/03/2012 - Fonction permettant de retourner l'objet du service racine
	public function getMyRootServiceObject(){
		$sql = "SELECT *
			FROM ".tiers_service::TABLE_NAME."
			WHERE id_tiers= :idtiers
			AND id_parent=".tiers_service::SERVICE_ROOT."
			LIMIT 0,1";//on assume qu'il ne peut y en avoir qu'un
	$res = $this->db->query($sql, array(
		':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	if($this->db->numrows()){
		$elem = $this->db->fetchrow($res);
		$service = $this->getServiceInstance();
		$service->openFromResultSet($elem);
		return $service;
	}
	else return null;
	}

	//Cyril - 07/03/2012 - Fonction permettant d'initialiser les services associés à ce tiers
	private function initMyServices(){
	$sql = "SELECT *
		FROM ".tiers_service::TABLE_NAME."
		WHERE id_tiers= :idtiers
		AND id_parent=".tiers_service::SERVICE_ROOT."
		LIMIT 0,1";//on assume qu'il ne peut y en avoir qu'un

	$res = $this->db->query($sql, array(
		':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	if($this->db->numrows()){
		$elem = $this->db->fetchrow($res);
		$service = $this->getServiceInstance();
		$service->openFromResultSet($elem);
		$service->initDescendance();
		$this->services = $service;
	}
	}

	//Cyril - 07/03/2012 - Fonction permettant de récupérer les services associés à ce tiers
	//Note une fonction s'appelle déjà getServices() et s'appuie sur la structure récursives de business_tiers
	//Là ça s'appuie sur les business_tiers_service / décidé avec Pat le même jour
	public function getExternalServices(){
	if(empty($this->services)){
		$this->initMyServices();
	}
	return $this->services;
	}

	/*
	* Cyril - 15/03/2012 - fonction permettant de savoir si en dehors de la racine, le tiers a d'autres services
	*/
	public function hasSubServices(){
	$sql = "SELECT count(*) as count_sub
		FROM ".tiers_service::TABLE_NAME."
		WHERE id_tiers = :idtiers
		AND id_parent > ".tiers_service::SERVICE_ROOT;
	$res = $this->db->query($sql, array(
		':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
	));
	$first = $this->db->fetchrow($res);
	return $first['count_sub'] > 0;
	}

	/*
	 *
	 * name: getAllExternalServices
	 * @return array All services's tier list
	 */
	public function getAllExternalServices(){
		$serivceList = array();

		$sql = 'SELECT *
			FROM '.tiers_service::TABLE_NAME.'
			WHERE id_tiers = :idtiers
			ORDER BY id_parent ASC';

		$res = $this->db->query($sql, array(
			':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while($rowService = $this->db->fetchrow($res)) {
			$service = $this->getServiceInstance();
			$service->openFromResultSet($rowService);

			$serivceList[] = $service;
		}

		return $serivceList;
	}

	/*
	 *
	 * name: getSubService
	 * @param int id service identifier
	 * @return tiers_service Service to get
	 */
	public function getSubService($id_service) {
		$sql = 'SELECT *
				FROM '.tiers_service::TABLE_NAME.'
				WHERE id_tiers = :idtiers
				AND id = :idservice
				LIMIT 1';

		$res = $this->db->query($sql, array(
			':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idservice' => array('type' => PDO::PARAM_INT, 'value' => $id_service),
		));

		$rowService = $this->db->fetchrow($res);

		$service = $this->getServiceInstance();
		$service->openFromResultSet($rowService);

		return $service;
	}

	/*
	 *
	 * name: hasSubService
	 * @param int id service identifier
	 * @return bool True if service is attached, False is not
	 */
	public function hasSubService($id_service) {
		$sql = 'SELECT id
			FROM '.tiers_service::TABLE_NAME.'
			WHERE id_tiers = :idtiers
			AND id = :idservice
			LIMIT 1';

		return (bool) $this->db->numrows($this->db->query($sql, array(
			':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idservice' => array('type' => PDO::PARAM_INT, 'value' => $id_service),
		)));
	}

	public function getHumans($filter = '') {
		$listCt = array();

		$params = array();
		$sql = 'SELECT *
			FROM dims_mod_business_contact
			WHERE id_tiers = :idtiers';
		$params[':idtiers'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());

		if(!empty($filter)) {
			$sql .= ' AND (firstname LIKE :filter OR lastname LIKE :filter OR email LIKE :filter) ';
			$params[':filter'] = array('type' => PDO::PARAM_INT, 'value' => '%'.$filter.'%');
		}

		$res = $this->db->query($sql, $params);

		while($dataCt = $this->db->fetchrow($res)) {
			$ct = new contact();
			$ct->openFromResultSet($dataCt);

			$listCt[$ct->getId()] = $ct;
		}

		return $listCt;
	}

	/* Cyril - 06-04/2012 - Fonction permettant de retourner la bonne instance de service selon l'héritage */
	public function getServiceInstance(){
		return new tiers_service();
	}

	public function getLabel(){
	return $this->fields['intitule'];
	}

	public function getTypeObject(){
	return $_SESSION['cste']['_DIMS_LABEL_COMPANY'];
	}

	public function initFolder(){
		if ($this->fields['id_folder'] == '' || $this->fields['id_folder'] <= 0){
			require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
			$tmstp = dims_createtimestamp();
			$fold = new docfolder();
			$fold->init_description();
			$fold->fields['name'] = 'root_'.$this->fields['id_globalobject'];
			$fold->fields['parents'] = 0;
			$fold->setugm();
			$fold->fields['timestp_create'] = $tmstp;
			$fold->save();
			$this->fields['id_folder'] = $fold->fields['id'];
			$fold->save(); // pr la synchro
			$this->save();
		}
		return $this->get('id_folder');
	}

	/*
	 * Fonction permettant de collecter les adresses provenant du tiers rattaches
	 */
	public function getAllAdresses() {
		require_once DIMS_APP_PATH.'modules/system/class_address.php';
		require_once DIMS_APP_PATH.'modules/system/class_address_type.php';
		$listadr = array();

		$sel = "SELECT 		a.*, t.*, lk.default
				FROM 		".address::TABLE_NAME." a
				INNER JOIN 	".address_link::TABLE_NAME." lk
				ON 			lk.id_goaddress = a.id_globalobject
				INNER JOIN 	".address_type::TABLE_NAME." t
				ON 			t.id = lk.id_type
				WHERE 		lk.id_goobject = :idgo";
		$params = array(
			':idgo' => array('type' => PDO::PARAM_INT, 'value' => $this->get('id_globalobject')),
		);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$rows = $db->split_resultset($res);
		foreach ($rows as $r) {
			if(!isset($listadr[$r['t']['id']])){
				$listadr[$r['t']['id']]['obj'] = new address_type();
				$listadr[$r['t']['id']]['obj']->openFromResultSet($r['t']);
			}
			$add = new address();
			$add->openFromResultSet($r['a']);
			$add->setIsDefault($r['lk']['default']);
			$listadr[$r['t']['id']]['add'][] = $add;
		}

		return $listadr;
	}

	public function isParent(){
		if($this->isNew()){
			return false;
		}else{
			$db = dims::getInstance()->getDb();
			$sel = "SELECT 	id
					FROM 	".self::TABLE_NAME."
					WHERE 	id_tiers = :id";
			$params = array(
				':id'=>array('value'=>$this->get('id'),'type'=>PDO::PARAM_INT),
			);
			$res = $db->query($sel,$params);
			return ($db->numrows($res) > 0);
		}
	}
}
