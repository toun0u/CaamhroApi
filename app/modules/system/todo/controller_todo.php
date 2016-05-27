<?php

/**
 * Description of controller_todos
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
require_once DIMS_APP_PATH . '/include/class_todo.php';
require_once DIMS_APP_PATH . '/include/class_todo_dest.php';
class controller_todo {

	public static function getTodoConcernedForUser($id_user) {
	$liste_todo = array();

	if($id_user > 0 ){
		$db = dims::getInstance()->getDb();
		$sql = "SELECT		*
			FROM		".todo::TABLE_NAME." AS todo
			INNER JOIN	".todo_dest::TABLE_NAME." AS tododest
			ON		todo.id = tododest.id_todo
			AND		tododest.type = :desttype
			WHERE		todo.id_user = :iduser
			OR		todo.user_from = :iduser
			OR		todo.user_to = :iduser
			OR		todo.user_by = :iduser
			OR		tododest.id_user = :iduser";

		$res = $db->query($sql, array(
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		':desttype' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_USER),
		));
		$separation = $db->split_resultset($res);
		foreach ($separation as $row) {
		$todo = new todo();
		$todo->openWithFields($row[todo::TABLE_NAME], true);

		$todo_dest = new todo_dest();
		$todo_dest->openWithFields($row[todo_dest::TABLE_NAME], true);

		$todo->addTodoDest($todo_dest);

		$liste_todo[$todo->getId()] = $todo;
		}

		$sql = "SELECT		*
			FROM		".todo::TABLE_NAME." AS todo
			INNER JOIN	".todo_dest::TABLE_NAME." AS tododest
			ON		todo.id = tododest.id_todo
			AND		tododest.type = :desttype
			INNER JOIN	dims_group_user
			ON		dims_group_user.id_group = tododest.id_user
			WHERE		todo.id_user = :iduser
			OR		todo.user_from = :iduser
			OR		todo.user_to = :iduser
			OR		todo.user_by = :iduser
			OR		dims_group_user.id_user = :iduser";

		$res = $db->query($sql, array(
		':iduser' => array('type' => PDO::PARAM_INT, 'value' => $id_user),
		':desttype' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_GROUP),
		));
		$separation = $db->split_resultset($res);
		foreach ($separation as $row) {
		$todo = new todo();
		$todo->openWithFields($row[todo::TABLE_NAME], true);

		$todo_dest = new todo_dest();
		$todo_dest->openWithFields($row[todo_dest::TABLE_NAME], true);

		$todo->addTodoDest($todo_dest);

		$liste_todo[$todo->getId()] = $todo;
		}
	}

	return $liste_todo;
	}
}

?>
