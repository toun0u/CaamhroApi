<?php
require_once DIMS_APP_PATH.'modules/system/class_module.php';

class module_wiki extends module {
	const TEMPLATE_WEB_PATH = 'modules/wce/wiki';
	const STATIC_FILES_VERSION = 'd3cc39400e01695b48c590dc2dc41269fa6bf3EE';
	const DEFAULT_USER_SKIN = 24;
	const _CATEG_ROOT_NAME = "WIKI";

	/*** Spécifique WCE ***/
	const _HEADING_ROOT_NAME = "WIKI";
	const _HEADING_ROOT_TEMPLATE = "cases";
	const _ARTICLE_DEFAULT_MODEL = "wiki";
        const _TYPE_WIKI = 1;

	// Max uploaded files size
	const MAX_SIZE_UPLOAD_PHOTO = 1572864; // 1.5 Mo - 1.5 * 1024 * 1024

	/*** Sous-menu ***/
	const _SUB_HOMEPAGE = 								1;
	const _SUB_LST_ARTICLES = 							2;
	const _SUB_NEW_ARTICLE =							3;
        const _ACTION_SHOW_ARTICLE = 					'show_article';
        const _ACTION_SHOW_ARTICLE_NEWSLETTER = 			'show_article_newsletter';
        const _ACTION_PROPERTIES_ARTICLE =              "properties_article";
        const _ACTION_EDIT_ARTICLE =					"edit_article";
		const _ACTION_ART_SAVE_BLOC =					"save_block";
        const _ACTION_ART_SAVE_BLOC_LITTLE =            "save_block_little";
		const _ACTION_ART_DEL_BLOC =					'delete_bloc';
        const _ACTION_ART_UP_BLOC =						'up_bloc';
        const _ACTION_ART_DOWN_BLOC =					'down_bloc';
        const _ACTION_ART_POSITION_UP_BLOC =			'pos_up_bloc';
        const _ACTION_ART_POSITION_DOWN_BLOC =			'pos_down_bloc';
		const _ACTION_ART_SAVE_BLOC_C =					'save_content_bloc';
        const _ACTION_ART_SAVE_BLOC_C_AJAX =			'save_content_bloc_ajax';
		const _ACTION_ART_SAVE_PROPERTIES = 			'save_prop_art';
		const _ACTION_VALID_ARTICLE = 					'valid_article';
		const _ACTION_IMPORT_LANG_ART = 				'import_article_lang';
		const _ACTION_GENERATE_NEW_LANG =				'generate_new_lang';

		//partie collaboration -- Cyril
		const _COLLABORATION_VIEW = 					'display_collab';
			const _SHOW_COLLABORATION = 				'show_collaboration';
			const _EDIT_INTERVENTION = 					'edit_intervention';
			const _SAVE_INTERVENTION =					'save_intervention';

		//partie sur les paramètres de l'article -- Cyril
		const _PARAMETERS_VIEW = 						'display_params';
			const _SHOW_INFO_GENERALES =				'params_infos_generales';
			const _SAVE_PARAMETERS = 					'save_infos_generales';
			const _REFERENCING =		 				'referencement';
			const _REFERENCES =		 					'references';
			const _SAVE_REFERENCES =					'save_references';
			const _ADD_REFERENCES =         			'add_references';
			const _DUPLICATE_REFERENCES =      			'duplic_references';
            const _CHANGEPOS_REFERENCES =         		'changepos_references';
			const _LINKS =								'links';
			const _REPLACE_LINKS =						'replace_links';
			const _DELETE_LINKS =						'delete_links';

	const _SUB_CATEGORIES =								4;
		const _ACTION_EDIT_CATEG = 						'edit_categ';
		const _ACTION_SAVE_CATEG = 						'save_categ';
		const _ACTION_DEL_CATEG = 						'del_categ';
	const _SUB_COLLAB = 								5;
		const _SUB_SUB_COLLAB_LIST_C = 					'collab';
			const _ACTION_EDIT_COLLAB = 				'edit_collab';
			const _ACTION_SAVE_COLLAB = 				'save_collab';
			const _ACTION_SWITCH_COLLAB = 				'switch_collab';
		const _SUB_SUB_COLLAB_LIST_R = 					'roles';
			const _ACTION_EDIT_ROLES = 					'edit_role';
			const _ACTION_SAVE_ROLES = 					'save_role';
		const _SUB_SUB_COLLAB_LIST_S = 					'services';
			const _ACTION_EDIT_SERVICE = 				'edit_serv';
			const _ACTION_SAVE_SERVICE = 				'save_serv';
	const _SUB_LANGU =	 								6;
		const _ACTION_SWITCH_ACTIVE =					'switch_active';
		const _ACTION_EDIT_LANG =						'edit_lang';
		const _ACTION_SAVE_LANG =						'save_lang';
		const _ACTION_EDIT_TAG = 						'edit_tag';
		const _ACTION_SAVE_TAG = 						'save_tag';

	/*********** Administration : filtre *********/
	const _WIKI_ADMIN_STATUT_ALL = 				0;
	const _WIKI_ADMIN_STATUT_ACTIF = 			1;
	const _WIKI_ADMIN_STATUT_INACTIF = 			2;

	/************ Liste des types actions ************/
	const _ACTION_ADD_COMMENT =						5;
	const _ACTION_ADMIN_EDIT_COLLAB = 				6;
	const _ACTION_ADMIN_VALID_COLLAB =				7;
	const _ACTION_ADMIN_ROLES = 					8;
	const _ACTION_ADMIN_SERVICES =					9;

	public static function getTemplateWebPath($file = '') {
		$webPath = './common/'.self::TEMPLATE_WEB_PATH;

		if(!empty($file)) {
			$webPath .= $file.'?'.self::get_static_version();
		}

		return $webPath;
	}

	public static function getTemplatePath($file = '') {
		return DIMS_APP_PATH.self::TEMPLATE_WEB_PATH.$file;
	}

	public static function getScriptEnv($path = ""){
		return dims::getInstance()->getScriptEnv()."?op=wiki&$path";
	}

	public static function get_static_version() {
		return self::STATIC_FILES_VERSION;
	}

	public static function getGrDispo($id_workspace=''){
		$lst = array();

		if ($id_workspace=='') $id_workspace=$_SESSION['dims']['workspaceid'];

		$db = dims::getInstance()->getDb();
		$sel = "SELECT	id
				FROM	dims_group
				WHERE 	system = 1
				AND		id_group = 0"; // séléction du/des groupe(s) racine/système
		$res = $db->query($sel);
		$tmp = array();
		while($r = $db->fetchrow($res)){
			$tmp[] = $r['id'];
		}

				$sel = "SELECT		id, parents
						FROM		dims_group
						INNER JOIN	dims_workspace_group as wg
						ON			wg.id_group = dims_group.id
						AND			wg.id_workspace = :id_workspace
						AND			system = 0";
				//AND			dims_group.id_group IN (".implode(',',$tmp).")"; // séléction des groupes directement attachés aux groupes la racine/système

		$res = $db->query($sel,array(':id_workspace'=>array('value'=>$id_workspace,'type'=>PDO::PARAM_INT)));
		$where = '';
		$params = array();
		$i = 0;
		while($r = $db->fetchrow($res)){
			$where .= " OR parents LIKE :parents$i ";
			$params[":parents$i"] = array('value'=>$r['parents'].";%",'type'=>PDO::PARAM_STR);
		}

		if ($where != ''){
			require_once DIMS_APP_PATH."modules/system/class_group.php";
			$sel2 = "SELECT	*
					FROM	dims_group
					WHERE 	system = 0
					AND		(".substr($where,3).")";
			$res = $db->query($sel2,$params);
			while ($r = $db->fetchrow($res)){
				$gr = new group();
				$gr->openWithFields($r);
				$lst[$r['id']] = $gr;
			}
		}

		return $lst;
	}

	public static function getGrRoot(){
		$gr = null;

		$db = dims::getInstance()->getDb();
		$sel = "SELECT	id
				FROM	dims_group
				WHERE 	system = 1
				AND		id_group = 0"; // séléction du/des groupe(s) racine/système
		$res = $db->query($sel);
		$tmp = array();
		while($r = $db->fetchrow($res)){
			$tmp[] = $r['id'];
		}

		$params = array();
		$sel = "SELECT	*
				FROM	dims_group
				WHERE	system = 0
				AND		id_group IN (".$db->getParamsFromArray($tmp,'id_group',$params).")"; // séléction des groupes directement attachés aux groupes la racine/système
		$res = $db->query($sel,$params);
		if($r = $db->fetchrow($res)){
			$gr = new group();
			$gr->openWithFields($r);
		}

		return $gr;
	}

	public static function getCategRoot(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		dc.*
				FROM		".category::TABLE_NAME." dc
				INNER JOIN	dims_category_object dco
				ON			dc.id = dco.id_category
				WHERE		dco.object_id_module_type = :object_id_module_type
				AND			dco.id_object = :id_object ";
		$res = $db->query($sel,array(
			':object_id_module_type'=>array('value'=>$_SESSION['dims']['moduletypeid'],'type'=>PDO::PARAM_INT),
			':id_object'=>array('value'=>dims_const::_SYSTEM_OBJECT_WCE_ARTICLE,'type'=>PDO::PARAM_INT)
		));
		$micro = new category();
		if ($r = $db->fetchrow($res)){
			$micro->openFromResultSet($r);
			$micro->initDescendance();
		}else{
			$micro->createRoot(self::_CATEG_ROOT_NAME, category::DIMS_CATEGORY_PRIVATE, dims_const::_SYSTEM_OBJECT_WCE_ARTICLE);
		}
		return $micro;
	}

	public static function getCategForList($id){
		if ($id != '' && $id > 0){
			$root = new category();
			$root->open($id);
			$filtre = $root->fields['parents'].";".$root->fields['id'];
		}else{
			$root = self::getCategRoot();
			$filtre = $root->fields['id'];
		}

		$db = dims::getInstance()->getDb();
		$sel = "SELECT		*
				FROM		".category::TABLE_NAME."
				WHERE		parents = :parents
				ORDER BY 	label ASC";
		$res = $db->query($sel,array(':parents'=>array('value'=>$filtre,'type'=>PDO::PARAM_STR)));
		$lst = array();
		while($r = $db->fetchrow($res)){
			$cat = new category();
			$cat->openFromResultSet($r);
			$lst[substr(strtoupper($r['label']),0,1)][] = $cat;
		}
		return $lst;
	}

	public static function initLanguagesWiki(){
		require_once DIMS_APP_PATH."modules/wce/wiki/include/class_wce_lang.php";
		wce_lang::initLangs();
	}

	public static function getRootHeading(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		*
				FROM		".wce_heading::TABLE_NAME."
				WHERE		type=".self::_TYPE_WIKI."
				AND			id_heading = 0
				AND			parents = 0
				AND			id_module = :id_module
				AND			id_workspace = :id_workspace
				LIMIT 		1";
		$res = $db->query($sel,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
									':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
		$heading = new wce_heading();
		if ($r = $db->fetchrow($res)){
			$heading->openFromResultSet($r);
		}else{
			$heading->init_description();
			$heading->fields['label'] = self::_HEADING_ROOT_NAME;
			$heading->fields['id_heading'] = 0;
            $heading->fields['type'] = self::_TYPE_WIKI;
			$heading->fields['depth'] = 1;
			$heading->fields['parents'] = 0;
			$heading->fields['template'] = self::_HEADING_ROOT_TEMPLATE;
			$heading->setugm();
			$heading->fields['position'] = 0;
			$heading->fields['visible'] = 1;
			$heading->fields['visible_if_connected'] = 1;
			$heading->save();
		}
		return $heading;
	}

	public static function getRootHeadingFront(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		*
				FROM		".wce_heading::TABLE_NAME."
				WHERE		type=".self::_TYPE_WIKI."
				AND			id_heading = 0
				AND			parents = 0
				AND			id_workspace = :id_workspace
				LIMIT 		1";
		$res = $db->query($sel,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
		$heading = null;
		if ($r = $db->fetchrow($res)){
			$heading = new wce_heading();
			$heading->openFromResultSet($r);
		}
		return $heading;
	}

    public static function getLastUpdatedArticles($maxelem=5){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		a.*,c.firstname,c.lastname,c.photo, c.id as id_contact
				FROM		(SELECT 	a.*
							FROM 		".wce_article::TABLE_NAME." as a
							WHERE		a.type=".self::_TYPE_WIKI."
							AND			a.id_module = :id_module
							AND			a.id_workspace = :id_workspace
							GROUP BY 	a.id
							ORDER BY 	a.id_lang
							) as a
                LEFT JOIN 	dims_mod_business_contact as c
                ON 			c.id=a.updated_by
				ORDER BY 	a.timestp_modify DESC
				LIMIT 		0,:max";
		$res = $db->query($sel,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
									':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':max'=>array('value'=>$maxelem,'type'=>PDO::PARAM_INT)));

        $lstart=array();

		while ($art = $db->fetchrow($res)){
            $ar = new wce_article();
			$ar->openFromResultSet($art);
            $lstart[$art['id']]=$ar;
		}
		return $lstart;
	}

	public static function handleHistoric(&$historic, $article, $nb_elems){
		if( ! isset($historic)) $historic = array();
		$temp = array();
		$temp[0]['id'] = $article->getId();
		$temp[0]['title'] = $article->fields['title'];

		$idx = 1;
		for($i=0;$i<=$nb_elems-1; $i++){
			if(count($historic) > $i){
				if($historic[$i]['id'] != $article->getId()){
					$temp[$idx] = $historic[$i];
					$idx++;
				}
			}
			else break;
		}
		$historic= $temp;
	}

	public static function bestArticles($limit = 5){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT		a.*, SUM(m.meter) as meter
				FROM		".wce_article::TABLE_NAME." a
				INNER JOIN	dims_mod_wce_article_meter m
				ON			m.id_article = a.id
				WHERE		m.timestp >= :timestp
				AND			a.type = 1
				AND			a.id_workspace = :id_workspace
				AND			a.id_module = :id_module
				GROUP BY	a.id
				ORDER BY	meter DESC
				LIMIT		$limit";
		$res = $db->query($sel,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
									':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':timestp'=>array('value'=>date('Ymd000000',mktime(0,0,0,date('m')-1,date('d'),date('Y'))),'type'=>PDO::PARAM_INT)));
		$lst = array();
		foreach($db->split_resultset($res) as $r){
			//dims_print_r($r);
			$art = new wce_article();
			$art->openFromResultSet($r['a']);
			$art->setLightAttribute('meter',$r['unknown_table']['meter']);
			$lst[] = $art;
		}
		return $lst;
	}
}
?>
