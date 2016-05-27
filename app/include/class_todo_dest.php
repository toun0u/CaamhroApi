<?php
class todo_dest extends dims_data_object {
	const TABLE_NAME = "dims_todo_dest";
	const TODO_UNFLAGGED = 0;
	const TODO_FLAGGED_BY_USER = 1;

	const TODO_NOT_ALREADY_VALIDATED = 0;
	const TODO_VALIDATED_BY_USER = 1;
	function __construct(){
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function addTodoUser($todo, $user, $flag=self::TODO_UNFLAGGED){
		$this->fields['id_todo'] = $todo;
		$this->fields['id_user'] = $user;
		$this->fields['flag'] = $flag;
	}

	public function getFlag(){
		return $this->fields['flag'];
	}

	public function setFlag($f){
		$this->fields['flag'] = $f;
	}

	public function getUserID(){
		return $this->fields['id_user'];
	}
}
?>
