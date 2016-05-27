<?php
class article_ratt extends dims_data_object {
	function article_ratt() {
		parent::dims_data_object('dims_mod_vpc_article_ratt', 'ref_article', 'id_grp');
	}

	function save() {
		if ($this->fields['type'] == 'grp') {
			$a_artratt = explode(';', $this->fields['rattachements']);
			$a_oldratt = explode(';', $this->old_rattachements);
			$a_delratt = array_diff($a_oldratt, $a_artratt);

			// on supprime les rattachements aux articles qui ont ete enleves
			foreach ($a_delratt as $ref_article) {
				if (!empty($ref_article)) {
					$this->db->query("DELETE FROM dims_mod_vpc_article_ratt WHERE ref_article = '$ref_article' AND id_grp = {$this->fields['id_grp']}");
				}
			}

			// on refait les rattachements aux autres articles
			foreach ($a_artratt as $ref_article) {
				if (!empty($ref_article)) {
					// liste des refs a rattacher a l'article en cours
					$a_curratt = array_diff($a_artratt, array($ref_article));
					$a_curratt[] = $this->fields['ref_article'];

					$this->db->query("DELETE FROM dims_mod_vpc_article_ratt WHERE ref_article = '$ref_article' AND id_grp = {$this->fields['id_grp']}");
					if (sizeof($a_curratt)) {
						$this->db->query("INSERT INTO dims_mod_vpc_article_ratt SET ref_article = '$ref_article', rattachements = '".implode(';', $a_curratt)."', id_grp = {$this->fields['id_grp']}, type = 'grp'");
					}
				}
			}
		}
		parent::save();
	}

	function delete() {
		if ($this->fields['type'] == 'grp') {
			$a_artratt = explode(';', $this->fields['rattachements']);

			// on refait les rattachements aux autres articles
			foreach ($a_artratt as $ref_article) {
				if (!empty($ref_article)) {
					// liste des refs a rattacher a l'article en cours
					$a_curratt = array_diff($a_artratt, array($ref_article));

					$this->db->query("DELETE FROM dims_mod_vpc_article_ratt WHERE ref_article = '$ref_article' AND id_grp = {$this->fields['id_grp']}");
					if (sizeof($a_curratt)) {
						$this->db->query("INSERT INTO dims_mod_vpc_article_ratt SET ref_article = '$ref_article', rattachements = '".implode(';', $a_curratt)."', id_grp = {$this->fields['id_grp']}, type = 'grp'");
					}
				}
			}
		}
		parent::delete();
	}
}
