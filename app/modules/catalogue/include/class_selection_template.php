<?php

class cata_selection_template extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_selections_templates';

	private $doc_file = null;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id', 'id_lang');
	}

	public static function getAll($id_module, $id_lang = 0) {
		// Langue par défaut
		if ($id_lang == 0) {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_param.php';
			$id_lang = cata_param::getDefaultLang();
		}

		// Liste des templates
		$a_templates = array();
		if ($id_module > 0) {
			$db = dims::getInstance()->getDb();
			$rs = $db->query('SELECT *
				FROM `'.self::TABLE_NAME.'`
				WHERE `id_module` = '.$id_module.'
				AND `id_lang` = '.$id_lang);
			while ($row = $db->fetchrow($rs)) {
				$template = new cata_selection_template();
				$template->openFromResultSet($row);
				$a_templates[] = $template;
			}
		}
		return $a_templates;
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

	public function getDoc() {
		if (is_null($this->doc_file)) {
			if ($this->fields['doc_id'] > 0) {
				require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
				$doc_file = new docfile();
				$doc_file->open($this->fields['doc_id']);
				$this->doc_file = $doc_file;
				return $this->doc_file;
			}
			else {
				return null;
			}
		}
		else {
			return $this->doc_file;
		}
	}

	public function getDocName() {
		$doc = $this->getDoc();
		if (!is_null($doc)) {
			return $doc->fields['name'];
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
				$translation = new cata_selection_template();
				$translation->openFromResultSet($row);
				$a_translations[$translation->get('id_lang')] = $translation;
			}
		}

		return $a_translations;
	}

	public function setTemplateTitle($title) {
		$this->fields['title'] = $title;
	}

	public function setDoc($doc) {
		$this->fields['doc_id'] = $doc->getId();
	}

	public function setLang($id_lang) {
		$this->fields['id_lang'] = $id_lang;
	}

	public function delete() {
		// Suppression du doc attaché
		$doc = $this->getDoc();
		if (!is_null($doc)) {
			$doc->delete();
		}

		// Suppression des traductions
		$this->db->query('DELETE FROM `'.self::TABLE_NAME.'` WHERE id = '.$this->get('id'));

		parent::delete();
	}

}
