<?php
require_once DIMS_APP_PATH . '/modules/system/class_tag.php' ;

class search_expression_tag extends DIMS_DATA_OBJECT{
	const TABLE_NAME = 'dims_search_expression_tag';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	function getAllTagsResults($id_search, $type=null, $tags=null){
		$tags_sql = ' ';

		$where_tags = '';
		if(count($tags)) {
			$where_tags .= " AND t.tags REGEXP '";
			asort($tags);
			foreach($tags as $t){
				$where_tags .= '.*;'.$t.';.*';
			}
			$where_tags .= "'";
		}

		$sql = "	SELECT t.tags
				FROM dims_search_expression_result r
				INNER JOIN dims_search_expression_tag t ON t.id_search = r.id_search AND r.id_globalobject_ref = t.id_globalobject_ref
				WHERE r.id_search= :idsearch";
		$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $id_search);

		if(!empty($type)) {
			$sql .= ' AND r.type = :type';
			$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
		}

		$sql .= $where_tags;

		//echo $sql;
		$res = $this->db->query($sql, $params);
		$single_tags = array();
		while($tab = $this->db->fetchrow($res)){
			$tags = explode(";", $tab['tags']);
			foreach($tags as $t){
				if(isset($t) && !empty($t) && !in_array($t, $single_tags)) $single_tags[] = $t;
			}
		}

		if(!empty($single_tags)){
			$params = array();
			$res = $this->db->query("SELECT * FROM ".tag::TABLE_NAME." WHERE id IN (".$this->db->getParamsFromArray($single_tags, 'idtag', $params).") AND type = ".tag::TYPE_DEFAULT." ORDER BY tag", $params);
			$tags=array();
			while($table = $this->db->fetchrow($res)){
				$t = new tag();
				$t->openFromResultSet($table);
				$tags[] = $t;
			}
			return $tags;
		}
		else return null;

	}

	function loadMatchingTags($id_search, $user_id, $optionalTags = array()){

		//gestion des tags -------------------
		$res = $this->db->query("SELECT r.id_globalobject_ref, t.id_tag
						  FROM dims_search_expression_result r
						  INNER JOIN dims_tag_globalobject t ON t.id_globalobject = r.id_globalobject_ref
						  ORDER BY r.id_globalobject_ref, t.id_tag ASC"
						);

		$this->initBulkRows();
		$temp = array();
		while($tab = $this->db->fetchrow($res)){
			$temp[$tab['id_globalobject_ref']][] = $tab['id_tag'];
		}
		if(count($optionalTags)){
			$temp2 = array();
			foreach($temp as $i=>$i2){
				$temp2[$i] = array_merge($i2,$optionalTags);
				asort($temp2[$i]);
			}
			$temp = $temp2;
		}
		foreach($temp as $go => $go_tags){
			$this->addBulkRow(array($id_search,$user_id, $go,";".implode(';;',$go_tags).";"));
		}
		$this->bulkInsert();
	}
}
