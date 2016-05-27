<?php
class wce_lang extends pagination{
	const TABLE_NAME = "dims_mod_wce_lang";

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id','id_module','id_workspace');
	}

	public function getFlag(){
		if ($this->fields['ref'] != ''){
			$url = './common/img/flag/flag_'.$this->fields['ref'].'.png';
			if (file_exists(DIMS_ROOT_PATH."www/common/img/flag/flag_".$this->fields['ref'].'.png'))
				return $url;
		}
		return null;
	}

	public function getLabel(){
		return $this->fields['label'];
	}

	public function openFromIdLang($id,$id_module = 0,$id_workspace = 0){
		if($id_module == 0) $id_module = $_SESSION['dims']['moduleid'];
		if($id_workspace == 0) $id_workspace = $_SESSION['dims']['workspaceid'];
		$this->open($id,$id_module,$id_workspace);
		if($this->isNew()){
			require_once DIMS_APP_PATH."modules/system/class_lang.php";
			$lang = new lang();
			$lang->open($id);
			if(isset($lang->fields['ref'])){
				$this->init_description();
				$this->setugm();
				$this->fields['id_module'] = $id_module;
				$this->fields['id_workspace'] = $id_workspace;
				$this->fields['label'] = $lang->getLabel();
				$this->fields['ref'] = $lang->fields['ref'];
				$this->fields['id'] = $lang->fields['id'];
				$this->fields['is_active'] = 0;
				$this->fields['default'] = 0;
				$this->save();
			}
		}
	}

	public static function initLangs($id_module = 0,$id_workspace = 0){
		if($id_module == 0) $id_module = $_SESSION['dims']['moduleid'];
		if($id_workspace == 0) $id_workspace = $_SESSION['dims']['workspaceid'];
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	id_module = :id_module
				AND	id_workspace = :id_work";
		$params=array();
        $params[':id_module']=intVal($_SESSION['dims']['moduleid']);
        $params[':id_work']=intVal($_SESSION['dims']['workspaceid']);
        $res = $db->query($sel,$params);
		if($db->numrows($res) <= 0){
			$first = true;
			require_once DIMS_APP_PATH."modules/system/class_lang.php";
			foreach(lang::getAllLanguageActiv() as $lg){
				$lang = new wce_lang();
				$lang->init_description();
				$lang->setugm();
				$lang->fields['id_module'] = $id_module;
				$lang->fields['id_workspace'] = $id_workspace;
				$lang->fields['label'] = $lg->getLabel();
				$lang->fields['ref'] = $lg->fields['ref'];
				$lang->fields['id'] = $lg->fields['id'];
				if($first){
					$lang->fields['is_active'] = 1;
					$lang->fields['default'] = 1;
					$first = false;
				}else{
					$lang->fields['is_active'] = 0;
					$lang->fields['default'] = 0;
				}
				$lang->save();
			}
		}

	}
	public function getAll($is_active = null){
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	id_module = :id_module
				AND	id_workspace = :id_workspace";

		if (!is_null($is_active)){
			$sel .= " AND	is_active = ".(($is_active)?1:0)."";
		}
		$db = dims::getInstance()->getDb();
		$params=array();
        $params[':id_module']=$_SESSION['dims']['moduleid'];
        $params[':id_workspace']=$_SESSION['dims']['workspaceid'];
        $res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$l = self::getInstance();
			$l->openFromResultSet($r);
			$lst[] = $l;
		}
		return $lst;
	}

	public static function getAllFront(){
		$lstwcemods=dims::getInstance()->getWceModules();
		$wce_module_id= current($lstwcemods);

		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	id_workspace = :id_workspace
				AND	id_module = :id_module
				AND	is_active = 1";

		$db = dims::getInstance()->getDb();

        $params=array();
        $params[':id_module']=$wce_module_id;
        $params[':id_workspace']=$_SESSION['dims']['workspaceid'];

        $res = $db->query($sel,$params);
		$lst = array();
		while($r = $db->fetchrow($res)){
			$l = self::getInstance();
			$l->openFromResultSet($r);
			$lst[] = $l;
		}
		return $lst;
	}

    public static function countAllFront(){
		$lstwcemods=dims::getInstance()->getWceModules();
		$wce_module_id= current($lstwcemods);

		$sel = "SELECT	COUNT(*) as nb
				FROM	".self::TABLE_NAME."
				WHERE	id_workspace = :id_workspace
				AND	id_module = :id_module
				AND	is_active = 1";

		$db = dims::getInstance()->getDb();

        $params=array();
        $params[':id_module']=$wce_module_id;
        $params[':id_workspace']=$_SESSION['dims']['workspaceid'];

        $res = $db->query($sel,$params);
		$lst = array();
		if($r = $db->fetchrow($res))
            return $r['nb'];
        else
            return 0;
	}

	public static function getInstance(){
		return new wce_lang();
	}

	public static function getLangFromRef($ref){
		if (isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid'] > 0)
			$wce_module_id = $_SESSION['dims']['moduleid'];
		else{
			$lstwcemods=dims::getInstance()->getWceModules();
			$wce_module_id = current($lstwcemods);
		}
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	ref LIKE :ref
				AND		id_workspace = :id_workspace
				AND		id_module = :id_module
				AND		is_active = 1";
		$db = dims::getInstance()->getDb();

        $params=array();
        $params[':id_module']=$wce_module_id;
        $params[':red']=$ref;
        $params[':id_workspace']=$_SESSION['dims']['workspaceid'];

		$res = $db->query($sel,$params);
		$l = self::getInstance();
		$l->init_description();
		if($r = $db->fetchrow($res)){
			$l->openFromResultSet($r);
		}
		return $l;
	}

	public static function isActive($id,$id_module=0,$id_workspace=0){
		if($id_module == 0) $id_module = $_SESSION['dims']['moduleid'];
		if($id_workspace == 0) $id_workspace = $_SESSION['dims']['workspaceid'];
		$lang = new wce_lang();
		$lang->open($id,$id_module,$id_workspace);
		return (isset($lang->fields['is_active']) && $lang->fields['is_active']);
	}

	public static function switchActive($id,$id_module=0,$id_workspace=0){
		if($id_module == 0) $id_module = $_SESSION['dims']['moduleid'];
		if($id_workspace == 0) $id_workspace = $_SESSION['dims']['workspaceid'];
		$lang = new wce_lang();
		$lang->open($id,$id_module,$id_workspace);
		if(isset($lang->fields['is_active'])){
			$lang->fields['is_active'] = !$lang->fields['is_active'];
			$lang->save();
		}else{
			require_once DIMS_APP_PATH."modules/system/class_lang.php";
			$lg = new lang();
			$lg->open($id);
			if(isset($lg->fields['ref'])){
				$lang = new wce_lang();
				$lang->init_description();
				$lang->setugm();
				$lang->fields['id_module'] = $id_module;
				$lang->fields['id_workspace'] = $id_workspace;
				$lang->fields['label'] = $lg->getLabel();
				$lang->fields['ref'] = $lg->fields['ref'];
				$lang->fields['id'] = $lg->fields['id'];
				$lang->fields['is_active'] = 1;
				$lang->fields['default'] = 0;
				$lang->save();
			}
		}
	}
}
?>
