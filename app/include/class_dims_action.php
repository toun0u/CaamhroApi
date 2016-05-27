<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH.'include/class_dims_action_matrix.php');

class dims_action extends DIMS_DATA_OBJECT {
//	public $db;						// connector to database abstraction layer : Mysql, Oracle
	private $id_user;				// id du dims_user createur
	private $id_workspace;			// id du workspace
	private $id_module;				// id du module
	private $lstobjects;			// liste des objets concernes
	private $lsttags;				// liste des tags concernes
	private $lstcorrespobject;

	function __construct(/*$db*/){
		parent::dims_data_object('dims_action');
//		$this->db=$db;
		$this->id_user=0;
		$this->id_workspace=0;
				$this->id_module=0;
		$this->lstobjects=array();
		$this->lsttags=array();
				$this->lstcorrespobject=array();
	}

	public function setWorkspace($id_workspace) {
		$this->id_workspace=$id_workspace;
	}

		public function setModule($id_module) {
		$this->id_module=$id_module;
	}

	public function setUser($id_user) {
		$this->id_user=$id_user;
	}

		public function addTempTags() {

			if (isset($_SESSION['dims']['temp_tag']) && !empty($_SESSION['dims']['temp_tag'])) {
				foreach ($_SESSION['dims']['temp_tag'] as $categ=>$elems) {
					foreach ($elems as $i => $elem) {
						$this->addTag($elem);
					}
				}
			}
		}

	public function setTags($lstags) {
		$this->lsttags=$lstags;
	}

	public function addTag($id_tag) {
		$this->lsttags[$id_tag]=$id_tag;
	}

	public function setObjects($lstobj) {
		$this->lstobjects=$lstobj;
	}

	public function addObject($id_globalobject=0,$id_module=0,$id_object=0,$id_record=0,$title='') {
		// test on doit rechercher l'objet
		if ($id_globalobject==0 && $id_module>0 && $id_object>0 && $id_record>0) {
					$id_globalobject=$this->getObjectByReference($id_module,$id_object,$id_record,$title);
		}

		// on ajoute si on a l'objet
		if ($id_globalobject>0) {
			$this->lstobjects[$id_globalobject]=$id_globalobject;
			$elem=array();
			$elem['id_module']=$id_module;
			$elem['id_object']=$id_object;
			$elem['id_record']=$id_record;

			$this->lstcorrespobject[$id_globalobject]=$elem;
		}
	}

	// fonction permettant la recherche et/ou la recuperation d'un objet dans la table dims_globalobject
	public function getObjectByReference($id_module,$id_object,$id_record,$title) {
		require_once(DIMS_APP_PATH."include/class_dims_globalobject.php");

		$gobject = new dims_globalobject(/*$this->db*/);
		$id_gobject=$gobject->getObject($id_module,$id_object,$id_record,$title);

		return $id_gobject;
	}

		public function searchSimilarAction($id_objglobal,$type,$id_user) {
			global $db ;
			$id_sim=0;
			$id_objglobal = (int) $id_objglobal;
			$datedeb_timestp = mktime(0,0,0,date('n'),date('j'),date('Y'));

			$datedeb_jour = intval($datedeb_timestp/(60*60*24));

			// ajout de la date du jour, si on trouve on
			$sql = "SELECT		DISTINCT a.id
						  FROM			dims_action as a
						  INNER JOIN			dims_action_matrix as m
						  ON			m.id_globalobject=:idglobalobject
						  AND			a.type=:type
						  AND			m.id_user=:iduser
						  AND			m.id_workspace=:idworkspace
						  AND			m.id_action=a.id";

			$res=$db->query($sql, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_objglobal), //  kevin : j'ai remplacé id_globalobject par id_objglobal
				':type' => array('type' => PDO::PARAM_INT, 'value' => $type),
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));

			if ($db->numrows($res)>0) {
				if ($elem=$db->fetchrow($res)) {
					$id_sim=$elem['id'];
				}
			}
			return $id_sim;
		}

		// fonction de recherche d'un objet lie a son action
		// si objet n'existe pas, on crée
		// si action n'existe pas, on la crée sinon on la reprend en la mettant à jour
		public function searchByObjectAction($moduleid,$objectid,$recordid,$userid,$title,$type,$comment) {
			$this->fields['id_parent']=0;
			if (!isset($this->fields['timestp_modify']))
				if ($this->fields['timestp_modify'] < dims_createtimestamp())
					$actparent->fields['timestp_modify']=$this->fields['timestp_modify'];
				else
					$actparent->fields['timestp_modify']=dims_createtimestamp();
			else
				$actparent->fields['timestp_modify']=$this->fields['timestp_modify'];
			$this->setModule($moduleid);
			$this->setWorkspace($_SESSION['dims']['workspaceid']);
			$this->setUser($userid);
			$this->fields['comment']= $comment;
			$this->fields['type'] = $type;

			$this->addObject(0,$moduleid,$objectid,$recordid,$title);
			$this->save(); // on enregistre l'action et la matrice
		}

		public function saveAlone() {
			parent::save();
		}

		private function updateYearTag($db,$dims,$idtagyear,$id_globalobject) {
			// on enregistre l'index_tag de l'annee si il n'existe pas

			if (isset($this->lstcorrespobject[$id_globalobject])) {
				$id_module=$this->lstcorrespobject[$id_globalobject]['id_module'];
				$id_object=$this->lstcorrespobject[$id_globalobject]['id_object'];
				$id_record=$this->lstcorrespobject[$id_globalobject]['id_record'];

				$res=$db->query("SELECT		id
			FROM		dims_tag_index as ti
			WHERE		id_tag =:idtagyear and id_record=:idrecord and id_object=:idobject and id_module=:idmodule", array(
				':idtagyear' => array('type' => PDO::PARAM_INT, 'value' => $idtagyear),
				':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
				':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
			));

				if ($db->numrows($res)==0) {
					// on insert le tag_index

					require_once(DIMS_APP_PATH.'modules/system/class_tag_index.php');

					$tag_index = new tag_index();
					$tag_index->fields['id_tag']=$idtagyear;
					$tag_index->fields['id_record']=$id_record;
					$tag_index->fields['id_object']=$id_object;
					$tag_index->fields['id_user']=$this->id_user;
					$tag_index->fields['id_workspace']=$this->id_workspace;
					$tag_index->fields['id_module']=$this->id_module;


			require_once(DIMS_APP_PATH."modules/system/class_module.php");
			$mod = new module();
			$mod->open($this->id_module);
			$tag_index->fields['id_module_type'] = $mod->fields['id_module_type'];
					$tag_index->save();
				}
			}

		}

	public function save($recursive=true) {
		global $db ;
		global $dims;
		require_once(DIMS_APP_PATH."modules/system/class_user.php");

		$user = new user(); //todo : Seems to be useless

		if(!isset ($this->fields['timestp_modify']) || $this->fields['timestp_modify']== ""){
			$this->fields['timestp_modify'] = dims_createtimestamp() ;
		}

		// check for Day tags
		$tag=date("Y");
		$idtag=0;
		$res=$db->query("SELECT		id
			FROM		dims_tag
			WHERE		tag like :tag", array(
			':tag' => array('type' => PDO::PARAM_STR, 'value' => $tag)
		));

		$idtagyear=0;
		if ($db->numrows($res)>0) {
			if ($t=$db->fetchrow($res)) {
			  $idtagyear=$t['id'];
			}
		}
		else {
			// on créé le tag
			require_once(DIMS_APP_PATH."modules/system/class_tag.php");
			$objtag = new tag();
			$objtag->fields['type']=4; // date
			$objtag->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$objtag->fields['tag']=$tag;
			$objtag->save();
			$idtagyear=$objtag->fields['id'];
		}

		// on traite tous les objets
		if (!empty($this->lstobjects) && $recursive) {
			// recherche de l'objet qui existerait deja par la personne => on met à jour
					if (sizeof($this->lstobjects)==1) {
				//dims_print_r($this->lstobjects);die();
				$id_objglobal=current($this->lstobjects);
				// recherche d'une action portant sur l'objet global + action spec recherche
				$id_actionsim=$this->searchSimilarAction($id_objglobal,$this->fields['type'],$this->id_user);

				if ($id_actionsim>0) {
					$actparent= new dims_action(/*$this->db*/);
					$actparent->open($id_actionsim);
					if (!isset($this->fields['timestp_modify']))
						if ($this->fields['timestp_modify'] < dims_createtimestamp())
							$actparent->fields['timestp_modify']=$this->fields['timestp_modify'];
						else
							$actparent->fields['timestp_modify']=dims_createtimestamp();
					else
						$actparent->fields['timestp_modify']=$this->fields['timestp_modify'];
					$actparent->save(false);

					 // boucle sur les tags
					foreach ($this->lsttags as $itag) {
						$act_matrix = new dims_action_matrix($db);
						$act_matrix->fields['id_globalobject']=$id_objglobal;
						$act_matrix->fields['id_user']=$this->id_user;
						$act_matrix->fields['id_workspace']=$this->id_workspace;
						if (isset($this->fields['timestp_modify']) && $this->fields['timestp_modify'] > 0){
							$datedeb_timestp = mktime(0,0,0,substr($this->fields['timestp_modify'],4,2),substr($this->fields['timestp_modify'],6,2),substr($this->fields['timestp_modify'],0,4));
						}else{
							$datedeb_timestp = mktime(0,0,0,date('n'),date('j'),date('Y'));
						}
						$act_matrix->fields['id_date']=intval($datedeb_timestp/(60*60*24)); // number of days since 1970 1er january
						$act_matrix->fields['id_action']=$id_actionsim;
						$act_matrix->fields['id_tag']=$itag;
						$act_matrix->save();
					}

					$this->updateYearTag($db,$dims,$idtagyear,$id_objglobal);

					return;
				}
			}

			$this->fields['id_module']=$this->id_module;
			$this->fields['id_workspace']=$this->id_workspace;
			$this->fields['id_user']=$this->id_user;
			if (!isset($this->fields['timestp_modify']))
				if ($this->fields['timestp_modify'] >= dims_createtimestamp())
					$this->fields['timestp_modify']=dims_createtimestamp();

			parent::save(false);

			// on vient de creer l'action
			$id_action=$this->fields['id'];

			if (isset($id_parent) && $id_parent>0) {
				// rattachement eventuel a l'id de room
				$actparent= new dims_action($db);
				$actparent->open($id_parent);
				$this->fields['id_room']=$actparent->fields['id_room'];

				if ($this->fields['type']==1) {
					// on incrémente le père et on sauve
					$actparent->fields['nbcomment']++;
					$actparent->save(false);
				}
			}

			if ($idtagyear>0) {
			   $this->lsttags[$idtagyear]=$idtagyear; // on ajoute eventuellement le tag annee
			}

			// on traite maintenant les objets et les tags
			foreach ($this->lstobjects as $idobj=>$gobj) {
				$act_matrix = new dims_action_matrix($db);
				$act_matrix->fields['id_globalobject']=$gobj;
				$act_matrix->fields['id_user']=$this->id_user;
				$act_matrix->fields['id_workspace']=$this->id_workspace;
				if (isset($this->fields['timestp_modify']) && $this->fields['timestp_modify'] > 0){
						$datedeb_timestp = mktime(0,0,0,substr($this->fields['timestp_modify'],4,2),substr($this->fields['timestp_modify'],6,2),substr($this->fields['timestp_modify'],0,4));
				}else{
						$datedeb_timestp = mktime(0,0,0,date('n'),date('j'),date('Y'));
				}
				$act_matrix->fields['id_date']=intval($datedeb_timestp/(60*60*24));
				//echo intval($datedeb_timestp/(60*60*24));die();
				$act_matrix->fields['id_action']=$id_action;
				if (empty($this->lsttags)) {
					$act_matrix->save();
				}
				else {
					$act_matrix->save();

					// boucle sur les tags
					foreach ($this->lsttags as $k=>$itag) {
						$act_matrix = new dims_action_matrix($db);
						$act_matrix->fields['id_globalobject']=$gobj;
						$act_matrix->fields['id_user']=$this->id_user;
						$act_matrix->fields['id_workspace']=$this->id_workspace;
						if (isset($this->fields['timestp_modify']) && $this->fields['timestp_modify'] > 0){
								$datedeb_timestp = mktime(0,0,0,substr($this->fields['timestp_modify'],4,2),substr($this->fields['timestp_modify'],6,2),substr($this->fields['timestp_modify'],0,4));
						}else{
								$datedeb_timestp = mktime(0,0,0,date('n'),date('j'),date('Y'));
						}
						$act_matrix->fields['id_date']=intval($datedeb_timestp/(60*60*24));
						//$act_matrix->fields['id_date']=time()/(60*60*24); // number of days since 1970 1er january
						$act_matrix->fields['id_action']=$id_action;
						$act_matrix->fields['id_tag']=$itag;
						$act_matrix->save();
					}
				}

				// on enregistre l'index_tag de l'annee si il n'existe pas
				$this->updateYearTag($db,$dims,$idtagyear,$gobj);
			}
		}
		else {
			parent::save(false);
		}
	}

	public function save_old($recursive=true) {
		global $dims;
		require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");
		require_once(DIMS_APP_PATH . "/modules/system/class_user.php");

		$contact= new contact();
		$user = new user();
		$id_contact=0;


		if (isset($this->fields['id_record']) && $this->fields['id_record']>0) {

			// traitement des tags
			if (!isset($this->fields['tags']) || $this->fields['tags']=='') {
				$lsttags=dims_getTags($dims,$this->fields['id_module'],$this->fields['id_object'],$this->fields['id_record']);
				$lsttagsTemp=dims_getTagsTemp();

				$str='';
				if (!empty($lsttags)) {
					foreach ($lsttags as $idtag=>$t) {
						if ($str=='') $str=$idtag;
						else $str.=";".$idtag;
					}
				}

				if (!empty($lsttagsTemp)) {
					foreach ($lsttagsTemp as $idtag=>$t) {
						if ($str=='') $str=$idtag;
						else $str.=";".$idtag;
					}
				}
				$this->fields['tags'] =$str;
			}
			if (isset($this->fields['id_user_create']) && $this->fields['id_user'] == $this->fields['id_user_create'] || $this->fields['id_user']>0 ) {
				$id_contact=$_SESSION['dims']['user']['id_contact'];
				$this->fields['author']=$_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
			}


			if (isset($this->fields['link_title_org']) && $this->fields['author']=='' && $this->fields['id_user']>0) {
				// on prend l'autre personne s'y rapportant
				$user->open($this->fields['id_user']);
				$id_contact=$user->fields['id_contact'];
				$this->fields['author']=$user->fields['firstname']." ".$user->fields['lastname'];

			}

			if (isset($this->fields['link_title_org']) && $this->fields['link_title_org']!='' && $this->fields['id_object_org']>0 && $this->fields['id_module_org']>0 && $this->fields['id_record_org']>0) {
				$this->fields['link_org']='javascript:viewPropertiesObject('.$this->fields['id_object_org'].','.$this->fields['id_record_org'].','.$this->fields['id_module_org'].',1);';
			}

			if ($id_contact>0) {
				$contact->open($id_contact);
				// reprise du nom et prenom
				//$this->fields['author_create']=$_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];

				// test de photo
				if (isset($this->fields['photo']) && $this->fields['photo']!='') {
					if($contact['photo'] == "") {
						$photo='';
					}
					else {
						$filephoto=DIMS_WEB_PATH . 'data/photo_cts/contact_'.$contact['id'].'/photo60'.$contact['photo'].'.png';


						if (file_exists($filephoto)) {
							$photo= _DIMS_WEBPATHDATA . 'photo_cts/contact_'.$contact['id'].'/photo60'.$contact['photo'].'.png';
						}
						else $photo='';
					}


					$this->fields['photo']=$photo;
				}


				if (isset($id_parent) && $id_parent>0) {
					// rattachement eventuel a l'id de room
					$actparent= new dims_action($db);
					$actparent->open($id_parent);
					$this->fields['id_room']=$actparent->fields['id_room'];

					if ($this->fields['type']==1) {
						// on incrémente le père et on sauve
						$actparent->fields['nbcomment']++;
						$actparent->save();
					}
				}

				// creation du lien
				if (isset($this->fields['link']) && isset($this->fields['link_title']) && $this->fields['link']=='' && $this->fields['link_title']!='') {
					// creation auto du lien
					$this->fields['link']='javascript:viewPropertiesObject('.$this->fields['id_object'].','.$this->fields['id_record'].','.$this->fields['id_module'].',1);';
				}

				// recherche de l'objet qui existerait deja par la personne => on met à jour
				if ($recursive) {
					//echo "select id from dims_action where id_object=".$this->fields['id_object']." and id_module=".$this->fields['id_module']." and id_record=".$this->fields['id_record']." and type=".$this->fields['type']." and id_user=".$this->fields['id_user'];
					$res=$db->query('SELECT	id
							FROM	dims_action
							WHERE	id_object=:idobject
							AND	id_module=:idmodule
							AND	id_record=idrecord
							AND	type=:type
							AND	id_user=:iduser',
						array(
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_object']),
							':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module']),
							':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_record']),
							':type' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['type']),
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
					));
					if ($db->numrows($res)>0) {
						$a=$db->fetchrow($res);
						$newaction = new dims_action(/*$db*/);
						$newaction->open($a['id']);
						$newaction->fields['timestp_modify']=$this->fields['timestp_modify'];
						$newaction->fields['tags']=$this->fields['tags'];
						if (isset($this->fields['id_room']) && $this->fields['id_room']>0) {
							$newaction->fields['id_room']=$this->fields['id_room'];
						}
						if (isset($this->fields['nbcomment']) && $this->fields['nbcomment']>0) {
							$newaction->fields['nbcomment']=$this->fields['nbcomment'];
						}
						$newaction->save(false);
						$this->fields=$newaction->fields;
					}
					else {
						parent::save();
					}
				}
				else {
					parent::save();
				}
			}
		}
	}

	public function delete() {
		parent::delete();
	}

		/**
		 * @param type $id_action_parente
		 * @return dims_historique (todo : a voir si on ne devrait pas renvoyer un objet dédié)
		 */
		public static function getListeCommentaires($id_action_parente){
			$liste_action = array() ;

			if($id_action_parente!= null && $id_action_parente != ""){

				$sql = 'SELECT a.type, a.comment, a.nbcomment,
			a.id, a.id_user, u.lastname, u.firstname,
			a.timestp_modify
			FROM dims_action a
			LEFT OUTER JOIN dims_user u
			ON a.id_user = u.id
			WHERE a.id_parent = :idactionparente
			AND a.type = :type';

		require_once(DIMS_APP_PATH."include/dims_historique.php");
		$res = dims::getInstance()->getDb()->query($sql, array(
			':idactionparente' => array('type' => PDO::PARAM_INT, 'value' => $id_action_parente),
			':type' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_ACTION_COMMENT),
		));
				while($row_action = dims::getInstance()->getDb()->fetchrow($res)){
					$action_information = new dims_historique(
							0,
							$row_action['id'],
							$row_action['type'],
							$row_action['comment'],
							$row_action['nbcomment'],
							$row_action['id_user'],
							$row_action['timestp_modify'],
							0,
							$row_action['lastname'],
							$row_action['firstname'],
							0,
							0,
							0);

				$liste_action[] = $action_information ;
				}
			}
			return $liste_action ;

		}

	public static function getActionsByType($id_origine, $type = dims_const::_ACTION_COMMENT) {
		$tab_action = array();

		$db = dims::getInstance()->db;

		$params = array();
		if(is_array($type)){
			$types = $db->getParamsFromArray($type, 'type', $params);
		}
		else{
			$types = ':types';
			$params[':types'] = array( 'value' => $type, 'type' => PDO::PARAM_INT );
		}

		if($id_origine != null) {

			$params[':idglobalobjectorigin'] = array('type' => PDO::PARAM_INT, 'value' => $id_origine);
			$sql_history_action = '	SELECT		*
						FROM		dims_action
						WHERE		globalobject_origin = :idglobalobjectorigin ';
						if (isset($params[':types'])){
							$sql_history_action.=
							' AND		type IN ( :types ) ';
						} else {
							$sql_history_action.=
							' AND		type IN ('.$types.') ';
						}
						$sql_history_action.=
						' ORDER BY	timestp_modify DESC';

			$res_action = $db->query($sql_history_action, $params);

			while($row_action = $db->fetchrow($res_action)){
				$action = new dims_action();
				$action->fields = $row_action;
				$tab_action[] = $action ;
			}
		}
		return $tab_action ;
	}

		public function incrementNbAction(){
			if(isset($this->fields['nbcomment'])){
				$this->fields['nbcomment']++;
			}else{
				//TODO ERROR
			}
		}

		public static function getActionByIdOrigin($id_origine, $db){
			$tab_action = array();

			if($id_origine != null){
				$sql_history_action = "SELECT *
					FROM dims_action
					WHERE globalobject_origin = :idorigine
					ORDER BY timestp_modify DESC";

				$res_action = $db->query($sql_history_action, array(':idorigine' => array('type' => PDO::PARAM_INT, 'value' => $id_origine)));
				while($row_action = $db->fetchrow($res_action)){
					$action = new dims_action();
					$action->fields = $row_action;
					$tab_action[] = $action ;
				}
			}
			return $tab_action ;
		}

		public function getGlobalObjectOrigin(){
			if(isset($this->fields['globalobject_origin'])){
				return $this->fields['globalobject_origin'];
			}else{
				return null ;
			}
		}

		public function getType(){
			if(isset($this->fields['type'])){
				return $this->fields['type'];
			}else{
				return null ;
			}
		}

		public function getTimestpModify(){
			if(isset($this->fields['timestp_modify'])){
				return $this->fields['timestp_modify'];
			}else{
				return null ;
			}
		}

		public function getComment(){
			if(isset($this->fields['comment'])){
				return $this->fields['comment'];
			}else{
				return null ;
			}
		}

		public function getNbComment(){
			if(isset($this->fields['nbcomment'])){
				return $this->fields['nbcomment'];
			}else{
				return null ;
			}
		}

		public function getIdUser(){
			if(isset($this->fields['id_user'])){
				return $this->fields['id_user'];
			}else{
				return null ;
			}
		}
}
