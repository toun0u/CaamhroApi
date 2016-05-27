<?php

class cata_selection extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_selections';

	private $template = null;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id', 'id_lang');
	}

	public static function getAll($id_module, $id_lang = 0) {
		// Langue par défaut
		if ($id_lang == 0) {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_param.php';
			$id_lang = cata_param::getDefaultLang();
		}

		// Liste des sélections
		$a_selections = array();
		if ($id_module > 0) {
			$db = dims::getInstance()->getDb();
			$rs = $db->query('SELECT *
				FROM `'.self::TABLE_NAME.'`
				WHERE `id_module` = '.$id_module.'
				AND `id_lang` = '.$id_lang);
			while ($row = $db->fetchrow($rs)) {
				$selection = new cata_selection();
				$selection->openFromResultSet($row);
				$a_selections[] = $selection;
			}
		}
		return $a_selections;
	}

	public function open() {
		$id = func_get_arg(0);
		if (func_num_args() > 1) {
			$id_lang = func_get_arg(1);
		}
		else {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_param.php';
			$id_lang = cata_param::getDefaultLang();
		}
		parent::open($id, $id_lang);
	}

	public function getTitle() {
		return $this->fields['title'];
	}

	public function getTemplate() {
		if (is_null($this->template)) {
			if ($this->fields['template_id'] > 0) {
				require_once DIMS_APP_PATH.'modules/catalogue/include/class_selection_template.php';
				$template = new cata_selection_template();
				$template->open($this->fields['template_id']);
				$this->template = $template;
				return $this->template;
			}
			else {
				return null;
			}
		}
		else {
			return $this->template;
		}
	}

	public function getTemplateName() {
		$template = $this->getTemplate();
		if (!is_null($template)) {
			return $template->get('title');
		}
		else {
			return '<em>'.dims_constant::getVal('_DIMS_LABEL_NONE').'</em>';
		}
	}

	public function getTranslations() {
		$a_translations = array();

		if (!$this->isNew()) {
			$a_translations[$this->get('id_lang')] = $this;

			$rs = $this->db->query('SELECT * FROM `'.self::TABLE_NAME.'` WHERE `id` = '.$this->get('id').' AND `id_lang` != '.$this->get('id_lang'));
			while ($row = $this->db->fetchrow($rs)) {
				$translation = new cata_selection();
				$translation->openFromResultSet($row);
				$a_translations[$translation->get('id_lang')] = $translation;
			}
		}

		return $a_translations;
	}

	public function setSelectionTitle($title) {
		$this->fields['title'] = $title;
	}

	public function setTemplate($template) {
		$this->fields['template_id'] = $template->get('id');
	}

	public function setTemplateId($template_id) {
		$this->fields['template_id'] = $template_id;
	}

	public function setLang($id_lang) {
		$this->fields['id_lang'] = $id_lang;
	}

	public function delete() {
		// Suppression des traductions
		$this->db->query('DELETE FROM `'.self::TABLE_NAME.'` WHERE `id` = '.$this->get('id'));
		parent::delete();
	}

}

// class selection extends dims_data_object {
// 	const TABLE_NAME = 'dims_mod_vpc_selection';
// 	function __construct() {
// 		parent::dims_data_object(self::TABLE_NAME, 'ref_client', 'ref_article');
// 	}
// }
