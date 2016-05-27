<?php

require_once DIMS_APP_PATH.'modules/system/faq/class_dims_faq.php';

class dims_glossaire extends dims_faq{
	const TYPE_GLOSSAIRE = 4;

	public function __construct() {
        parent::dims_data_object(parent::TABLE_NAME, 'id');
    }

	public function save() {
		$this->fields['type'] = dims_glossaire::TYPE_GLOSSAIRE;
        parent::save(dims_const::_SYSTEM_OBJECT_GLOSSAIRE);
		$this->updateDocLinks();
    }

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
			$this->languages = $this->getTraduction();
		if ($lang == 0)
			$lang = $_SESSION['dims']['glossaire']['lang'];
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
			$lang = $_SESSION['dims']['glossaire']['lang'];
		if (isset($this->languages[$lang]))
			return $this->languages[$lang]->getContent();
		elseif (isset($this->languages[$_SESSION['dims']['currentlang']]))
			return $this->languages[$_SESSION['dims']['currentlang']]->getContent();
		elseif(count($this->languages) > 0)
			return current($this->languages)->getContent();
		else
			return '';
    }

	public static function displayAllFaqBO($name, $edit = false, $AddCateg = false, $id_module = 0, $id_workspace = 0){
		$db = dims::getInstance()->getDb();

		// gestion de la langue
		if (!isset($_SESSION['dims']['glossaire']['lang']))
			$_SESSION['dims']['glossaire']['lang'] = $_SESSION['dims']['currentlang'];
		$res_lg = $db->query("SELECT id, label FROM dims_lang WHERE isactive = 1");
		global $dims;
		echo '<span style="margin-top:5px;margin-left:15px;">'.$_SESSION['cste']['_DIMS_LABEL_LANG'].' : <select id="lang_choose" onchange="javascript:document.location=\''.$dims->getScriptEnv().'?dims_op=dims_glossaire_manager&action=changeLang&id=\'+this.options[this.selectedIndex].value;">';
		while ($lg = $db->fetchrow($res_lg)){
			if ($lg['id'] == $_SESSION['dims']['glossaire']['lang'])
				echo '<option value="'.$lg['id'].'" selected=true>'.ucfirst($lg['label']).'</option>';
			else
				echo '<option value="'.$lg['id'].'">'.ucfirst($lg['label']).'</option>';
		}
		echo '</select></span>';

		if ($id_module == 0)
			$id_module = $_SESSION['dims']['moduleid'];
		if ($id_workspace == 0)
			$id_workspace = $_SESSION['dims']['workspaceid'];
		$sel = "SELECT	*
				FROM	dims_faq
				WHERE	id_module = $id_module
				AND		id_workspace = $id_workspace
				AND type = :type ";

		$res = $db->query($sel, array(
			':idmodule' => $id_module,
			':idworkspace' => $id_workspace,
			':type' => dims_glossaire::TYPE_GLOSSAIRE
		));
		$lst = array();
		if ($edit)
			$lst[] = array('data' => '<span style="font-weight:bold;width:100%;">'.$_SESSION['cste']['_RSS_LABELTAB_ADD'].'<img src="img/add.gif" style="cursor:pointer;float:right;" /></span>', 'child' => array('data' => 'X'));
		while ($r = $db->fetchrow($res)){
			$faq = new dims_glossaire();
			$faq->openWithFields($r);
			$lst[] = array('data' => $faq->getTitle().'<img src="img/edit.gif" style="cursor:pointer;float:right;" onclick="javascript:dims_xmlhttprequest_todiv(\'admin.php\',\'dims_op=dims_glossaire_manager&action=edit&id='.$r['id'].'&categ='.$AddCateg.'\',\'\',\'content_'.$r['id'].'\');" />', 'child' => array(0 => array('data' => $r['id'])));
		}
		require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');

		$browser = new dims_browser(2,$lst,$name);
		$prop = $browser->getDesc_properties();
		$prop['type'][1] = 'form';
		$prop['link'][1] = './common/modules/system/faq/view_glossaire.tpl.php';
		$prop['width'][0] = '25%';
		$prop['width'][1] = '75%';
		$prop['categ'] = $AddCateg;
		$browser->setDesc_properties($prop);
		$browser->displayBrowser('./common/modules/system/faq/view_glossaire.tpl.php');
	}

	public static function saveForm($post){
		$faq = new dims_glossaire();
		$faq->init_description();
		if ($post['id'] > 0)
			$faq->open($post['id']);
		else
			$faq->setugm();
		$faq->setvalues($post,'glossaire_');
		$faq->fields['isBefore'] = isset($post['isBefore']);
		$faq->save();

		require_once DIMS_APP_PATH.'/modules/system/faq/class_dims_faq_lang.php';
		$lang = new dims_faq_lang();
		if (!$lang->open($faq->fields['id'],$_SESSION['dims']['glossaire']['lang'])){
			$lang->fields['id_faq'] = $faq->fields['id'];
			$lang->fields['id_lang'] = $_SESSION['dims']['glossaire']['lang'];
		}
		if (isset($post['lang'.$post['id'].'_content']))
			$lang->fields['content'] = $post['lang'.$post['id'].'_content'];
		$lang->setvalues($post,'langGlossaire_');
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

?>