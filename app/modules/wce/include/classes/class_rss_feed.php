<?php
/* 
sudo pear install XML_Feed_Parser
sudo apt-get install tidy
*/
/* Maintenue ??? https://github.com/pear/XML_Feed_Parser */
require_once "XML/Feed/Parser.php";
require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_cache.php";

class rss_feed extends dims_data_object {
	const TABLE_NAME = 'dims_mod_rssfeed';
	const MY_GLOBALOBJECT_CODE = 501;

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
		$cache = $this->getRssCache();
		foreach($cache as $c){
			$c->delete();
		}
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function save(){
		$this->setugm();
		$return = parent::save(self::MY_GLOBALOBJECT_CODE);
		$this->updateCache();
		return $return;
	}

	public function updateInfos(){
		$url = $this->get('url');
		if(!empty($url)){
			$r = new XML_Feed_Parser(file_get_contents($url),false,false);
			if($r->title !== false)
				$this->set('title',$r->title);
			if($r->link !== false)
				$this->set('link',$r->link);
			if($r->language !== false)
				$this->set('language',$r->language);
			if($r->description !== false)
				$this->set('description',$r->description);
			// images / icones
			if($r->url !== false)
				$this->set('ico',$r->url);
			elseif($r->icon !== false)
				$this->set('ico',$r->icon);
			if($this->get('title') != ''){
				return $this;
			}else{
				return false;
			}
		}
		return false;
	}

	public function getRssCache(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		*
				FROM 		".rss_cache::TABLE_NAME."
				WHERE 		id_rssfeed = :idfeed
				AND 		id_workspace = :idw
				ORDER BY 	timestp";
		$params = array(
			':idfeed' => array('value'=>$this->get('id'),'type'=>PDO::PARAM_INT),
			':idw' => array('value'=>$this->get('id_workspace'),'type'=>PDO::PARAM_INT),
		);
		$lst = array();
		$res = $db->query($sel,$params);
		while($r = $db->fetchrow($res)){
			$c = new rss_cache();
			$c->openFromResultSet($r);
			$lst[] = $c;
		}
		return $lst;
	}

	public function updateCache(){
		if(!$this->isNew()){
			$url = $this->get('url');
			if(!empty($url)){
				$r = new XML_Feed_Parser(file_get_contents($url),false,false);
				foreach($r as $i){
					if($i->guid !== false)
						$c = rss_cache::find_by(array('id_workspace'=>$this->get('id_workspace'),'id_rssfeed'=>$this->get('id'),'guid'=>$i->guid),null,1);
					elseif($i->id !== false)
						$c = rss_cache::find_by(array('id_workspace'=>$this->get('id_workspace'),'id_rssfeed'=>$this->get('id'),'guid'=>$i->id),null,1);
					if(empty($c)){
						$c = new rss_cache();
						$c->init_description();
						$c->set('id_rssfeed',$this->get('id'));
						$c->set('id_object',$this->get('id_object'));
						$c->set('id_workspace',$this->get('id_workspace'));
						$c->set('id_module',$this->get('id_module'));
						$c->set('id_user',$this->get('id_user'));
						if($i->guid !== false)
							$c->set('guid',$i->guid);
						elseif($i->id !== false)
							$c->set('guid',$i->id);
						$c->set('title',$i->title);
						$c->set('link',$i->link);
						if($i->description !== false)
							$c->set('description',$i->description);
						elseif($i->content !== false)
							$c->set('description',$i->content);
						if($i->pubdate !== false)
							$c->set('timestp',date('YmdHis',$i->pubdate));
						elseif($i->published !== false)
							$c->set('timestp',date('YmdHis',$i->published));
						// gérer l'auteur
						$c->set('author',$this->get('title'));
						$c->save();
					}
				}
				return $this;
			}
		}
		return false;
	}
}
