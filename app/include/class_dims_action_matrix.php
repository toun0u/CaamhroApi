<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class dims_action_matrix extends dims_data_object {
	public $db;						// connector to database abstraction layer : Mysql, Oracle

	function __construct($db){
		parent::dims_data_object('dims_action_matrix');
		$this->db=$db;
	}

	public function set_iddate_from_timestp($timestp){
		if($timestp != NULL && $timestp > 0){
			$datedeb_timestp = mktime(0,0,0,substr($timestp,4,2),substr($timestp,6,2),substr($timestp,0,4));
		}else{
			$datedeb_timestp = mktime(0,0,0,date('n'),date('j'),date('Y'));
		}
		$id_date = intval($datedeb_timestp/(60*60*24));

		$this->fields['id_date'] = $id_date ;
	}

	/**
	* Le résultat est trié par id action en tant qu'indice de tableau
	* @param type $tab_id_action
	* @param type $db
	* @return dims_action_matrix
	*/
	public static function getActionMatrixFromListIdAction($tab_id_action, $db){
		$tab_matrix = array();
		if(!empty($tab_id_action)){
			$params = array();
			$sql_history_matrix = "	SELECT *
						FROM dims_action_matrix
						WHERE id_action IN(".$db->getParamsFromArray($tab_id_action, 'idaction', $params).")";

			$res_matrix = $db->query($sql_history_matrix, $params);
			while($row_matrix = $db->fetchrow($res_matrix)){
				$matrix = new dims_action_matrix($db);
				$matrix->fields = $row_matrix;
				$tab_matrix[$matrix->getIdAction()][] = $matrix ;
			}
		}

		return $tab_matrix ;
	}

	public function getIdAction(){
		if(isset($this->fields['id_action'])){
			return $this->fields['id_action'];
		}else{
			//TODO ERROR
			return null;
		}
	}

	public function getId_globalobject(){
		if(isset($this->fields['id_globalobject'])){
			return $this->fields['id_globalobject'] ;
		}else{
			//TODO ERROR
			return null;
		}
	}
}
