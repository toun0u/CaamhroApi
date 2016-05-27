<?php

/**
 * Description of dims_faq
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class dims_faq extends dims_data_object{
    const TABLE_NAME = "dims_faq";
	const TYPE_BO = 1;
	const TYPE_FO = 2;
	const TYPE_BOTH = 3;

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function save() {
        parent::save(dims_const::_SYSTEM_OBJECT_FAQ);
		$this->updateDocLinks();
    }

	public function delete(){
		if (!isset($this->languages))
			$this->getTraduction();
		foreach($this->languages as $lg)
			$lg->delete();
		parent::delete();
	}

	/* getters */
	public function getTraduction($lang = 0){
		if (isset($this->languages)){
			if ($lang > 0){
				if (isset($this->languages[$lang]))
					return $this->languages[$lang];
				else
					return false;
			}else
				return $this->languages;
		}else{
			require_once DIMS_APP_PATH.'/modules/system/faq/class_dims_faq_lang.php';
			$sel = "SELECT	*
					FROM	dims_faq_lang
					WHERE	id_faq = :idfaq ";
			$res = $this->db->query($sel, array(
				':idfaq' => $this->fields['id']
			));
			while ($r = $this->db->fetchrow($res)){
				$trad = new dims_faq_lang();
				$trad->openWithFields($r);
				$this->languages[$r['id_lang']] = $trad;
			}
			if ($lang > 0){
				if (isset($this->languages[$lang]))
					return $this->languages[$lang];
				else
					return false;
			}else
				return $this->languages;
		}
    }

	public function getTitle($lang = 0){
		if (!isset($this->languages))
			$this->getTraduction();
		if ($lang == 0)
			$lang = $_SESSION['dims']['faq']['lang'];
		if (isset($this->languages[$lang]))
			return $this->languages[$lang]->getTitle();
		elseif (isset($this->languages[$_SESSION['dims']['currentlang']]))
			return $this->languages[$_SESSION['dims']['currentlang']]->getTitle();
		elseif(count($this->languages) > 0)
			return current($this->languages)->getTitle();
		else
			return '';
    }

    public function getContent($lang = 0) {
        if (!isset($this->languages))
			$this->getTraduction();
		if ($lang == 0)
			$lang = $_SESSION['dims']['faq']['lang'];
		if (isset($this->languages[$lang]))
			return $this->languages[$lang]->getContent();
		elseif (isset($this->languages[$_SESSION['dims']['currentlang']]))
			return $this->languages[$_SESSION['dims']['currentlang']]->getContent();
		elseif(count($this->languages) > 0)
			return current($this->languages)->getContent();
		else
			return '';
    }

    public function getPosition() {
        return $this->getAttribut("position", self::TYPE_ATTRIBUT_NUMERIC);
    }

	public function getType() {
        return $this->getAttribut("type", self::TYPE_ATTRIBUT_NUMERIC);
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

	public function getIsBefore(){
		return $this->getAttribut("isBefore", self::TYPE_ATTRIBUT_BOOLEAN_TINYINT);
	}

	public function getUrls($string, $strict=true) {
		$types = array("href");
		while (list(, $type) = each($types)) {
			$innerT = $strict ? '[a-z0-9:?=&@/._-]+?' : '.+?';

			preg_match_all("|$type\=([\"'`])(" . $innerT . ")\\1|i", $string, $matches);

			$ret = $matches[2];
		}
		return $ret;
	}

	public function updateDocLinks() {
		$tabdocs = array();
		$urls = $this->getUrls($this->fields['content'], false);
		if (!empty($urls)) {
			foreach ($urls as $id => $url) {
				$pos = strpos($url, 'docfile_md5id=');
				if ($pos > 0) {
					$str = substr($url, $pos + 14);
					$tabdocs[$str] = "'" . $str . "'";
				}
			}
		}

		// on traite maintenant l'enregistrement des documents dans les articles // vérification des problemes de doc
		if (!empty($tabdocs)) {
			$params = array();
			$sql = "SELECT 	id, id_globalobject
					FROM	dims_mod_doc_file
					WHERE 	md5id LIKE (" . $db->getParamsFromArray($tabdocs, 'tabdocs', $params) . ")";
			$res = $this->db->query($sql, $params);
			$lstFile = array();

			while ($doc = $this->db->fetchrow($res))
				$lstFile[] = $doc['id_globalobject'];

			$gb = $this->getMyGlobalObject();
			$gb->addLink($lstFile);
		}
	}

	public static function displayAllFaqBO($name, $edit = false, $AddCateg = false, $id_module = 0, $id_workspace = 0){
		$db = dims::getInstance()->getDb();
		// gestion de la langue
		if (!isset($_SESSION['dims']['faq']['lang']))
			$_SESSION['dims']['faq']['lang'] = $_SESSION['dims']['currentlang'];
		$res_lg = $db->query("SELECT id, label FROM dims_lang WHERE isactive = 1");
		global $dims;
		echo '<span style="margin-top:5px;margin-left:15px;">'.$_SESSION['cste']['_DIMS_LABEL_LANG'].' : <select id="lang_choose" onchange="javascript:document.location=\''.$dims->getScriptEnv().'?dims_op=dims_faq_manager&action=changeLang&id=\'+this.options[this.selectedIndex].value;">';
		while ($lg = $db->fetchrow($res_lg)){
			if ($lg['id'] == $_SESSION['dims']['faq']['lang'])
				echo '<option value="'.$lg['id'].'" selected=true>'.ucfirst($lg['label']).'</option>';
			else
				echo '<option value="'.$lg['id'].'">'.ucfirst($lg['label']).'</option>';
		}
		echo '</select></span>';
		// affichage des faq en fonction de la langue
		if ($id_module == 0)
			$id_module = $_SESSION['dims']['moduleid'];
		if ($id_workspace == 0)
			$id_workspace = $_SESSION['dims']['workspaceid'];
		$params = array();
		$sel = "SELECT		*
				FROM		dims_faq
				WHERE		id_module = :idmodule
				AND			id_workspace = :idworkspace ";
		$params[':idmodule'] = $id_module;
		$params[':idworkspace'] = $id_workspace;
		if (!$edit){
			$sel .= " AND type IN ( :typeboth , :typebo )";
			$params[':typeboth'] = dims_faq::TYPE_BOTH;
			$params[':typebo'] = dims_faq::TYPE_BO;
		} else {
			$sel .= " AND type IN ( :typeboth , :typebo , :typefo )";
			$params[':typeboth'] = dims_faq::TYPE_BOTH;
			$params[':typebo'] = dims_faq::TYPE_BO;
			$params[':typefo'] = dims_faq::TYPE_FO;
		}

		$res = $db->query($sel, $params);
		$lst = array();
		if ($edit)
			$lst[] = array('data' => '<span style="font-weight:bold;width:100%;">'.$_SESSION['cste']['_RSS_LABELTAB_ADD'].'<img src="img/add.gif" style="cursor:pointer;float:right;" /></span>', 'child' => array(0 =>array('data' => 'X')));
		while ($r = $db->fetchrow($res)) {
			$faq = new dims_faq();
			$faq->openWithFields($r);
			$lst[] = array('data' => $faq->getTitle().'<img src="img/edit.gif" style="cursor:pointer;float:right;" onclick="javascript:dims_xmlhttprequest_todiv(\'admin.php\',\'dims_op=dims_faq_manager&action=edit&id='.$r['id'].'&categ='.$AddCateg.'\',\'\',\'content_'.$r['id'].'\');" />', 'child' => array(0 => array('data' => $r['id'])));
		}
		require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');

		$browser = new dims_browser(2,$lst,$name);
		$prop = $browser->getDesc_properties();
		$prop['type'][1] = 'form';
		$prop['link'][1] = './common/modules/system/faq/view_faq.tpl.php';
		$prop['width'][0] = '25%';
		$prop['width'][1] = '75%';
		$prop['categ'] = $AddCateg;
		$browser->setDesc_properties($prop);
		$browser->displayBrowser('./common/modules/system/faq/view_faq.tpl.php');
	}

	public static function saveForm($post){
		$faq = new dims_faq();
		$faq->init_description();
		if ($post['id'] > 0){
			$faq->open($post['id']);
		}
		else
			$faq->setugm();
		$faq->setvalues($post,'faq_');
		$faq->fields['isBefore'] = isset($post['isBefore']);
		$faq->save();

		require_once DIMS_APP_PATH.'/modules/system/faq/class_dims_faq_lang.php';
		$lang = new dims_faq_lang();
		if (!$lang->open($faq->fields['id'],$_SESSION['dims']['faq']['lang'])){
			$lang->fields['id_faq'] = $faq->fields['id'];
			$lang->fields['id_lang'] = $_SESSION['dims']['faq']['lang'];
		}
		if (isset($post['lang'.$post['id'].'_content']))
			$lang->fields['content'] = $post['lang'.$post['id'].'_content'];
		$lang->setvalues($post,'langFaq_');
		$lang->save();

		if (isset($_POST['idCateg']) && $_POST['idCateg'] > 0){
			require_once DIMS_APP_PATH.'modules/system/class_category.php';
			$categ = new category();
			$categ->open($_POST['idCateg']);
			$gb = $faq->getMyGlobalObject();
			$lk = $gb->searchLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
			if (count($lk) > 0)
				$gb->deleteLink($lk);
			$gb->addLink($categ->fields['id_globalobject']);
		}
		global $dims;
		header('location:'.$dims->getScriptEnv());
	}
}