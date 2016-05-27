<?php
require_once DIMS_APP_PATH.'modules/system/class_module.php';

class module_wce extends module {
	const STATIC_FILES_VERSION = "c8cb39409e01695b48c750dc2dc41269fa6cf4ef";
	const TEMPLATE_WEB_PATH = "modules/wce/wce/";
	const DEFAULT_MODELE = "ModeleBloc";
	const _RECH_TEXT_FRONT = 30;

	const _SUB_HOMEPAGE = 						1;
		const _DEFAULT =						'default';
	const _SUB_PARAM = 							2;
		const _PARAM_INFOS =					'infos';
			const _PARAM_INFOS_DEF = 			'default';
			const _PARAM_INFOS_EDIT_REF = 		'referencement';
			const _PARAM_INFOS_SAVE_REF = 		'save_referencement';
			const _PARAM_INFOS_EDIT_ACCUEIL =	'editAccueil';
			const _PARAM_INFOS_SAVE_ACCUEIL =	'saveAccueil';
			const _PARAM_INFOS_EDIT_ACCUEIL2 =	'editAccueil2'; // page accueil connecté
			const _PARAM_INFOS_SAVE_ACCUEIL2 =	'saveAccueil2';
		const _PARAM_CONF =						'conf';
			const _PARAM_CONF_DEF = 			'default';
			const _PARAM_CONF_EDIT_DOMAIN =		'edit_domain';
			const _PARAM_CONF_DEL_DOMAIN =		'del_domain';
			const _PARAM_CONF_SAVE_DOMAIN =		'save_domain';
			const _PARAM_CONF_EDIT_TEMPL =		'edit_template';
			const _PARAM_CONF_SAVE_TEMPL = 		'save_template';
			const _PARAM_CONF_DEL_TEMPL = 		'del_template';
			const _PARAM_CONF_EDIT_RESTR = 		'edit_restriction';
			const _PARAM_CONF_DEL_RESTR = 		'del_restriction';
			const _PARAM_CONF_SAVE_RESTR = 		'save_restriction';
			const _PARAM_CONF_TEMPL_WIKI = 		'change_tpl_wiki';
			const _PARAM_CONF_TEMPL_DEFAULT = 	'change_def_tpl';
		const _PARAM_TOOLS =					'tools';
			const _PARAM_TOOLS_DEF =			'default';
			const _PARAM_TOOLS_IMPORT =			'import';
			const _PARAM_TOOLS_EXPORT =			'export';
			const _PARAM_TOOLS_SITEMAP =		'sitemap';
		const _PARAM_EXECUTE =					'outils';
			const _PARAM_EXEC_DEF =				'default';
			const _PARAM_EXEC_PUBLISH_ALL =		'publish_all';
			const _PARAM_EXEC_GENERATE_URL =	'generate_url';
			const _PARAM_EXEC_STR_REPLACE =		'str_replace';
	const _SUB_SITE =							3;
		const _SITE_PREVIEW =					'preview';
			const _PREVIEW_DEF =				'default';
			const _PREVIEW_EDIT =				'edit_art';
			const _PREVIEW_SAVE = 				'save_art';
			const _PREVIEW_ART = 				'preview';
			const _DELETE_BLOC =				'delete_bloc';
			const _PREVIEW_BLOC_SAVE =			'prop_bloc';
			const _ACTION_ART_UP_BLOC =			'up_bloc';
			const _ACTION_ART_DOWN_BLOC =		'down_bloc';
			const _ACTION_ART_LEFT_BLOC =		'left_bloc';
			const _ACTION_ART_RIGHT_BLOC =		'right_bloc';
			const _ACTION_ART_SAVE_BLOC_AJAX = 	'save_bloc';
			const _ACTION_VALID_ARTICLE =		'valid_art';
			const _ACTION_ART_SAVE_BLOC_LITTLE ='save_block_little';
		const _SITE_PROPERTIES =				'properties';
			const _PROPERTIES_DEF =				'default';
			const _PROPERTIES_SAVE_ART = 		'save_art';
			const _PROPERTIES_SAVE_HEAD = 		'save_head';
			const _PROPERTIES_ADD_ART =			'add_art';
			const _PROPERTIES_ADD_HEAD =		'add_head';
			const _PROPERTIES_ADD_ROOT =		'add_root';
			const _PROPERTIES_DEL_ROOT = 		'del_root';
			const _PROPERTIES_DEL_ART = 		'del_art';
			const _PROPERTIES_CLONE_ART = 		'clone_art';
		const _SITE_REF = 						'ref';
			const _GEST_REF_DEF =				'default';
			const _GEST_REF_SAVE =				'save_ref';
		const _SITE_DIFF = 						'diffusion';
		const _SITE_LIST = 						'listart';
			const _LIST_ART_DEF =				'default';
			const _LIST_ART_MOVE =				'move_art';
			const _LIST_ART_DEL =				'del_art';
	const _SUB_ARTICLE = 						4;
	const _SUB_DYN = 							5;
		const _DYN_DEF =						'default';
		const _DYN_OBJ_EDIT =					'obj_edit';
		const _DYN_OBJ_VIEW =					'obj_view';
		const _DYN_OBJ_DEL =					'obj_del';
		const _DYN_OBJ_SAVE = 					'obj_save';
		const _DYN_OBJ_EDIT_BREVE =				'breve_edit';
		const _DYN_OBJ_SAVE_BREVE =				'breve_save';
		const _DYN_OBJ_DEL_BREVE =				'breve_del';
		const _DYN_OBJ_EDIT_ART =				'article_edit';
		const _DYN_OBJ_SAVE_ART =				'article_save';
		const _DYN_OBJ_DEL_ART =				'article_del';
		const _DYN_SLID_EDIT =					'slid_edit';
		const _DYN_SLID_VIEW =					'slid_view';
		const _DYN_SLID_DEL =					'slid_del';
		const _DYN_SLID_SAVE =					'slid_save';
		const _DYN_SLID_EDIT_ELEM =				'edit_elem';
		const _DYN_SLID_SAVE_ELEM =				'save_elem';
		const _DYN_SLID_DEL_ELEM =				'del_elem';
		const _DYN_SLID_UP_ELEM =				'up_elem';
		const _DYN_SLID_DOWN_ELEM =				'down_elem';
	const _SUB_STATS =							6;
	const _SUB_LIST = 							7;

	public static function getTemplatePath($file = '') {
		return DIMS_APP_PATH.self::TEMPLATE_WEB_PATH.$file;
	}

	public static function getTemplateWebPath($file = '') {
		$webPath = './common/'.self::TEMPLATE_WEB_PATH;

		if(!empty($file)) {
			$webPath .= $file.'?'.self::get_static_version();
		}

		return $webPath;
	}

	public static function get_static_version() {
		return self::STATIC_FILES_VERSION;
	}

	public static function get_url($sub = null){
		if (is_null($sub))
			return dims::getInstance()->getScriptEnv();
		else
			return dims::getInstance()->getScriptEnv()."?sub2=$sub";
	}

	public static function getLastUpdatedArticles($maxelem=5){
		$db = dims::getInstance()->db;
		$sel = "SELECT		a.*,c.firstname,c.lastname,c.photo, c.id as id_contact
				FROM		".wce_article::TABLE_NAME." as a
				LEFT JOIN	dims_mod_business_contact as c
				ON 			c.id=a.updated_by
				WHERE		a.type=0
				AND			a.id_module = :id_module
				AND			a.id_workspace = :id_workspace
				AND			a.id_heading > 0
				GROUP BY	a.id
				ORDER BY 	a.timestp_modify DESC
				LIMIT 		0,:limit";

		$params=array();
		$params[':limit']['value']=$maxelem;
		$params[':limit']['type']=PDO::PARAM_INT;
		$params[':id_module']=$_SESSION['dims']['moduleid'];
		$params[':id_workspace']=$_SESSION['dims']['workspaceid'];
		$res = $db->query($sel,$params);

		$lstart=array();
		while ($art = $db->fetchrow($res)){
			$ar = new wce_article();
			$ar->openFromResultSet($art);
			$lstart[$art['id']]=$ar;
		}

		return $lstart;
	}

	public static function bestArticles($limit = 5){
		$db = dims::getInstance()->db;

		$datesup=date('Ymd000000',mktime(0,0,0,date('m')-1,date('d'),date('Y')));

		$sel = "SELECT		a.*, SUM(m.meter) as meter
				FROM		".wce_article::TABLE_NAME." a
				INNER JOIN	dims_mod_wce_article_meter m
				ON			m.id_article = a.id
				WHERE		m.timestp > :datesup
				AND			a.type = 0
				AND			a.id_module = :id_module
				GROUP BY	a.id
				ORDER BY	meter DESC
				LIMIT		:limit";


		$params=array();
		$params[':datesup']=$datesup;
		$params[':limit']['value']=$limit;
		$params[':limit']['type']=PDO::PARAM_INT;
		$params[':id_module']=$_SESSION['dims']['moduleid'];
		$res = $db->query($sel,$params);
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
