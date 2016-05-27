<?php
require_once DIMS_APP_PATH.'/modules/wce/include/classes/class_dynobject.php';
require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
require_once DIMS_APP_PATH.'modules/system/activity/class_type.php';
class PlanningController extends DynObject{
	public function buildIHM(){
		if( ! is_null($this->smarty)){
			switch($this->getParam('mode')){
				default:
				case 'home':
					$this->buildView();
					$path = $this->getParam('path');
					if(!is_null($path) && file_exists(_WCE_MODELS_PATH."/objects/".$path)){
						$this->setTPLPath(_WCE_MODELS_PATH."/objects/".$path);
					}else{
						$this->setTPLPath(DIMS_APP_PATH.'templates/objects/planning/index_home.tpl');
					}
					$dims = dims::getInstance();
					$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/templates/objects/planning/styles_home.css');
					break;
				case 'rss':
					$this->buildRSS();
					$this->setTPLPath(DIMS_APP_PATH.'templates/objects/planning/rss.tpl');
					$dims = dims::getInstance();
					break;
				case 'full_index':
					$this->buildView(true);
					$path = $this->getParam('path');
					if(!is_null($path) && file_exists(_WCE_MODELS_PATH."/objects/".$path)){
						$this->setTPLPath(_WCE_MODELS_PATH."/objects/".$path);
					}else{
						$this->setTPLPath(DIMS_APP_PATH.'templates/objects/planning/full_index.tpl');
					}
					$dims = dims::getInstance();
					$this->smarty->assign('styles_path', $dims->getProtocol().$dims->getHttpHost().'/templates/objects/planning/full_styles.css');
					break;
			}
			return $this->getTPLPath();
		}
	}

	public function buildView($full = false){
		$dims = dims::getInstance();
		$db = $dims->getDb();
		$max_elems = $this->getParam('max-elem');
		if( empty($max_elems) ) $max_elems = 3;

		//récupération des N dernières alertes
		$now = date('YmdHis');
		$params = array(
			':idw' => array('value'=>$_SESSION['dims']['workspaceid'], 'type'=>PDO::PARAM_INT),
			':datejour' => array('value'=>date('Y-m-d'), 'type'=>PDO::PARAM_STR),
			':datejour2' => array('value'=>date('Y-m-d'), 'type'=>PDO::PARAM_STR),
			':heurefin' => array('value'=>date('H:i:s'), 'type'=>PDO::PARAM_STR),
		);
		if( ! $full ){
			$limit = ' LIMIT 0, :max ';
			$params[':max'] = array('value'=>intval($max_elems), 'type'=>PDO::PARAM_INT);
		}else 
			$limit = '';
		$sql = "SELECT 		a.*, t.label as type_label, u.firstname, u.lastname
				FROM 		".dims_activity::TABLE_NAME." a
				INNER JOIN 	".activity_type::TABLE_NAME." t
				ON 			a.activity_type_id = t.id
				INNER JOIN 	".user::TABLE_NAME." u
				ON 			a.id_responsible = u.id
				WHERE 		a.id_workspace = :idw
				AND 		a.private = 1
				AND 		a.typeaction = '_DIMS_EVENT_ACTIVITIES'
				AND 		(a.datejour > :datejour
					OR (
						a.datejour = :datejour2
						AND a.heurefin >= :heurefin
					))
				ORDER BY 	a.datejour, a.heuredeb
				$limit";
		$res = $db->query($sql, $params);

		$planning_list = array();
		while($fields = $db->fetchrow($res)){
			//Gestion de l'assignation smarty des variables utilisées
			$planning = array();
			$planning['id'] = $fields['id'];
			$planning['libelle'] = $fields['libelle'];
			$planning['description'] = $fields['description'];
			$planning['datejour'] = $fields['datejour'];
			$planning['datefin'] = $fields['datefin'];
			$planning['heuredeb'] = $fields['heuredeb'];
			$planning['heurefin'] = $fields['heurefin'];
			$planning['type'] = $fields['type_label'];
			$planning['address'] = $fields['address'];
			$planning['cp'] = $fields['cp'];
			$planning['city'] = $fields['lieu'];
			$planning['organizer'] = $fields['firstname']." ".$fields['lastname'];

			$planning_list[] = $planning;
		}

		$this->smarty->assign('planning', $planning_list);
	}

	public function buildRSS(){
		$dims = dims::getInstance();
		$db = $dims->getDb();
		if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $rootpath="http://";
		else $rootpath="https://";
		$rootpath.=$_SERVER['HTTP_HOST'];

		$this->smarty->assign('rootpath', $rootpath);

		$params = array(
			':idw' => array('value'=>$_SESSION['dims']['workspaceid'], 'type'=>PDO::PARAM_INT),
		);
		$sql = "SELECT 		a.*, t.label as type_label, u.firstname, u.lastname
				FROM 		".dims_activity::TABLE_NAME." a
				INNER JOIN 	".activity_type::TABLE_NAME." t
				ON 			a.activity_type_id = t.id
				INNER JOIN 	".user::TABLE_NAME." u
				ON 			a.id_responsible = u.id
				WHERE 		a.id_workspace = :idw
				AND 		a.private = 1
				AND 		a.typeaction = '_DIMS_EVENT_ACTIVITIES'
				ORDER BY 	a.datejour, a.heuredeb";
		$res = $db->query($sql, $params);

		$cpte=1;

		$planning_list = array();
		while($fields = $db->fetchrow($res)){
			//Gestion de l'assignation smarty des variables utilisées
			$planning = array();
			$planning['id'] = $fields['id'];
			$planning['libelle'] = $fields['libelle'];
			$planning['description'] = nl2br($fields['description']);
			$planning['datejour'] = $fields['datejour'];
			$planning['datefin'] = $fields['datefin'];
			$planning['heuredeb'] = $fields['heuredeb'];
			$planning['heurefin'] = $fields['heurefin'];
			$planning['type'] = $fields['type_label'];
			$planning['address'] = $fields['address'];
			$planning['cp'] = $fields['cp'];
			$planning['city'] = $fields['lieu'];
			$planning['organizer'] = $fields['firstname']." ".$fields['lastname'];
			$planning['cpte'] = $cpte;

			$ldate_pub = ($fields['timestp_create']!='') ? dims_timestamp2local($fields['timestp_create']) : array('date' => '//','time'=>'');
			$tab_date = explode('/', $ldate_pub['date']);
			$planning['date']= $tab_date[2].'-'.$tab_date[1].'-'.$tab_date[0].'T'.$ldate_pub['time'].'Z';

			$planning_list[] = $planning;
			$cpte++;
		}
		$this->smarty->assign('rss_elems', $planning_list);
	}

}
