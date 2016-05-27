<?php
class rss_cache extends dims_data_object {
	const TABLE_NAME = 'dims_mod_rsscache';
	const MY_GLOBALOBJECT_CODE = 502;

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function setid_object() {
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	function settitle(){
		$this->title = $this->get('title');
	}

	public function delete() {
		if($this->get('id_object') > 0){
			// supprimer les articles associés
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article.php";
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
			$corresp = article_object_corresp::find_by(array('id_heading'=>0,'id_rss'=>$this->get('id'),'id_object'=>$this->get('id_object')),null,1);
			if(!empty($corresp)){
				$obj = wce_article::find_by(array('id'=>$corresp->get('id_article')),null,1);
				if(!empty($obj))
					$obj->delete();
				$corresp->delete();
			}
		}

		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function save(){
		$isNew = $this->isNew();
		$return = parent::save(self::MY_GLOBALOBJECT_CODE);
		// on créé l'article associé
		if($isNew && $this->get('id_object') > 0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article.php";
			$obj = new wce_article();
			$wce_site= new wce_site(dims::getInstance()->getDb(),$this->get('id_module'));
			$obj->fields['id_lang'] = $wce_site->getDefaultLanguage();
			$obj->fields['id_user'] = $this->get('id_user');
			$obj->fields['id_module'] = $this->get('id_module');
			$obj->fields['id_workspace'] = $this->get('id_workspace');
			$db = dims::getInstance()->getDb();
			$select = "	SELECT 	MAX(position) as maxpos
						FROM 	".wce_article::TABLE_NAME."
						WHERE 	id_heading = 0
						AND 	id_module=:id_module";
			$res = $db->query($select,array(':id_module'=>array('value'=>$this->get('id_module'),'type'=>PDO::PARAM_INT)));
			$obj->fields['position'] = 1;
			if($r = $db->fetchrow($res))
				$obj->fields['position'] += $r['maxpos'];
			$obj->fields['id_heading'] = 0;
			$obj->fields['title'] = $this->get('title');
			$obj->fields['description'] = $this->get('description');
			$obj->fields['timestp'] = $obj->fields['timestp_modify'] = dims_createtimestamp();
			$obj->fields['timestp_published'] = $obj->fields['lastupdate_timestp'] = $this->get('timestp');
			$obj->fields['lastupdate_id_user'] = $this->get('id_user');
			$obj->fields['url'] = $this->get('link');
			$obj->save();

			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
			//$corresp = article_object_corresp::find_by(array('id_article'=>$obj->get('id'),'id_heading'=>0,'id_rss'=>$this->get('id')),null,1);
			//if(empty($corresp)){
				$corresp = new article_object_corresp();
				$corresp->init_description();
				$corresp->set('id_object',$this->get('id_object'));
				$corresp->set('id_article',$obj->get('id'));
				$corresp->set('id_heading',0);
				$corresp->set('id_rss',$this->get('id'));
				$corresp->save();
			//}
		}
		return $return;
	}
}
