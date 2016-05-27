<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';
require_once DIMS_APP_PATH.'modules/system/class_homepage_column.php';

class homepage_line extends dims_data_object
{
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function homepage_line()
	{
		parent::dims_data_object('dims_homepage_line');
	}


	function save()
	{
		$db = dims::getInstance()->getDb();

		if ($this->new) // insert
		{
			// get max from line position
			$select =	"
					SELECT max(position) as maxposition
					FROM dims_homepage_line
					WHERE id_group = :idgroup
					AND id_user = :iduser";

			$result = $db->query($select, array(
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_group']),
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_user']),
			));

			$this->fields['position'] = 1;

			if ($resfields = $db->fetchrow($result))
			{
				$this->fields['position'] = $resfields['maxposition'] + 1;
			}
		}

		parent::save();
	}

	function delete()
	{
		$db = dims::getInstance()->getDb();

		// update all line in page
		$update =	"
				UPDATE	dims_homepage_line
				SET	position = position - 1
				WHERE	position > :position";

		$db->query($update, array(
			':position' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
		));;

		$select =	"
				SELECT id
				FROM dims_homepage_column
				WHERE id_line = :idline";

		$result = $db->query($select, array(
			':idline' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($deletefields = $db->fetchrow($result))
		{
			$homepage_column = new homepage_column();
			$homepage_column->open($deletefields['id']);
			$homepage_column->delete();
		}


		parent::delete();
	}


}
?>
