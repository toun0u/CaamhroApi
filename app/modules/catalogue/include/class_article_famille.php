<?php
class cata_article_famille extends dims_data_object {
	const TABLE_NAME = 'dims_mod_cata_article_famille';
	public function cata_article_famille() {
		parent::dims_data_object(self::TABLE_NAME, 'id_article', 'id_famille');
	}

	public function create($id_famille, $id_article){
		$this->init_description(true);
		$this->setugm();
		$this->fields['id_famille'] = $id_famille;
		$this->fields['id_article'] = $id_article;
		$this->save();
	}

	public function save() {
		if (empty($this->fields['position'])) {
			$rs = $this->db->query("
				SELECT	COUNT(*) AS last_pos
				FROM	dims_mod_cata_article_famille
				WHERE	id_famille = {$this->fields['id_famille']}");
			$row = $this->db->fetchrow($rs);
			$this->fields['position'] = $row['last_pos'] + 1;
		}
		parent::save();
	}

	public function delete() {
		// On décale tous les articles qui sont en-dessous
		$rs = $this->db->query("
			SELECT	id_article, position
			FROM	".self::TABLE_NAME."
			WHERE	id_famille = {$this->fields['id_famille']}
			AND		position > {$this->fields['position']}");
		while ($row = $this->db->fetchrow($rs)) {
			$this->db->query("
				UPDATE	".self::TABLE_NAME."
				SET		position = ". ($row['position'] - 1) ."
				WHERE	id_article = {$row['id_article']}
				AND		id_famille = {$this->fields['id_famille']}");
		}
		parent::delete();
	}
}
