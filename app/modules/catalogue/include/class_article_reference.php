<?php

include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";

class article_reference extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_cata_article_references';

	const TYPE_URL      = 1;
	const TYPE_VIDEO    = 2;
	const TYPE_DOC      = 3;

	public function article_reference()
	{
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function delete()
	{
		$this->updatePosition($this->fields['position'], self::getMaxPosition($this->fields['id_article']));

		return parent::delete();
	}

	public function getLink() {
		$link = '';

		if ($this->fields['type'] == self::TYPE_DOC) {
			$doc = new docfile();
			$doc->open($this->fields['id_doc']);

			$link = $doc->getwebpath();
		} else {
			$link = $this->fields['url'];
		}

		return $link;
	}

	public function updatePosition($oldPosition, $newPosition = null) {
		if ($newPosition == null) {
			$newPosition = $this->fields['position'];
		}

		if ($oldPosition > $newPosition) {
			$sql = 'UPDATE '.self::TABLE_NAME.' SET position = position + 1
					WHERE id != :currentId
					AND POSITION BETWEEN :newPosition AND :oldPosition';

			$this->db->query($sql, array(
				'currentId' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
				'oldPosition' => array('type' => PDO::PARAM_INT, 'value' => $oldPosition),
				'newPosition' => array('type' => PDO::PARAM_INT, 'value' => $newPosition),
			));
		}
		if ($oldPosition < $newPosition) {
			$sql = 'UPDATE '.self::TABLE_NAME.' SET position = position - 1
					WHERE id != :currentId
					AND POSITION BETWEEN :oldPosition AND :newPosition';

			$this->db->query($sql, array(
				'currentId' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
				'oldPosition' => array('type' => PDO::PARAM_INT, 'value' => $oldPosition),
				'newPosition' => array('type' => PDO::PARAM_INT, 'value' => $newPosition),
			));
		}

		return true;
	}

	public static function getTypeLabel($type)
	{
		$labels = array(
			self::TYPE_URL      => dims_constant::getVal('ADDRESS'),
			self::TYPE_VIDEO    => dims_constant::getVal('VIDEO'),
			self::TYPE_DOC      => dims_constant::getVal('DOCUMENT'),
		);

		return isset($labels[$type]) ? $labels[$type] : '';
	}

	public static function getTypeList()
	{
		return array(
			self::TYPE_URL,
			self::TYPE_VIDEO,
			self::TYPE_DOC,
		);
	}

	public static function getMaxPosition($articleId) {
		$db = dims::getInstance()->getDb();

		$sql = 'SELECT MAX(position) max_position FROM '.self::TABLE_NAME.' WHERE id_article = :articleId';

		$res = $db->query($sql, array(
			':articleId' => array('type' => PDO::PARAM_INT, 'value' => $articleId),
		));

		$data = $db->fetchrow($res);

		return (int)$data['max_position'];
	}
}
