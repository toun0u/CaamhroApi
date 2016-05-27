<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class homepage_column extends dims_data_object
{
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function homepage_column()
	{
		parent::dims_data_object('dims_homepage_column');
		$this->fields['id_module'] = 0;
	}


	function save()
	{
		$db = dims::getInstance()->getDb();

		if ($this->new) // insert
		{
			// get max from line position
			$select =	"
					SELECT max(position) as maxposition
					FROM dims_homepage_column
					WHERE id_line = :idline";

			$result = $db->query($select, array(
				':idline' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_line']),
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

		// update all columns in line
		$update =	"
				UPDATE	dims_homepage_column
				SET	position = position - 1
				WHERE	position > :position
				AND	id_line = :idline";

		$db->query($update, array(
			':position' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['position']),
			':idline' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_line']),
		));

		parent::delete();
	}

}
?>
