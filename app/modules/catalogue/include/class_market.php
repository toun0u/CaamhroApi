<?php

class cata_market extends dims_data_object {

	const TABLE_NAME = 'dims_mod_cata_markets';

	private $a_restrictions = null;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public static function getByCode($code) {
		$ts = dims_createtimestamp();
		$db = dims::getInstance()->getDb();

		$rs = $db->query('
			SELECT *
			FROM `'.self::TABLE_NAME.'`
			WHERE code = "'.$code.'"
			AND ( ISNULL(date_from) OR date_from < '.$ts.' )
			AND ( ISNULL(date_to) OR date_to > '.$ts.' )
			LIMIT 0, 1');

		if ($db->numrows($rs)) {
			$market = new cata_market();
			$market->openFromResultSet($db->fetchrow($rs));
			return $market;
		}
		else {
			return null;
		}
	}

	public function getRestrictions() {
		if (is_null($this->a_restrictions)) {
			$this->a_restrictions = array();
			$rs = $this->db->query('
				SELECT id_article
				FROM `'.cata_market_restriction::TABLE_NAME.'`
				WHERE id_market = '.$this->get('id'));
			while ($row = $this->db->fetchrow($rs)) {
				$this->a_restrictions[$row['id_article']] = $row['id_article'];
			}
		}
		return $this->a_restrictions;
	}

	public function hasRestrictions() {
		if (is_null($this->a_restrictions)) {
			$this->getRestrictions();
		}
		return sizeof($this->a_restrictions) > 0;
	}

}
