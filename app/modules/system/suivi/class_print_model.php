<?php
class print_model extends dims_data_object
{

	const TABLE_NAME = 'dims_mod_business_print_model';

	function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function create($label, $id_type, $id_doc, $description = ''){
		$this->init_description(true);
		$this->setugm();
		$this->setLabel($label);
		$this->setTypeId($id_type);
		$this->setDocId($doc_id);
		$this->setDescription($description);
		return $this->save();
	}

	//SETTERS
	public function setLabel($val){
		$this->fields['label'] = $val;
	}
	public function setTypeId($val){
		$this->fields['id_type'] = $val;
	}
	public function setDocId($val){
		$this->fields['id_doc'] = $val;
	}
	public function setDescription($val){
		$this->fields['description'] = $val;
	}

	//GETTERS
	public function getLabel(){
		return $this->fields['label'];
	}
	public function getTypeId(){
		return $this->fields['id_type'];
	}
	public function getDocId(){
		return $this->fields['id_doc'];
	}
	public function getDescription(){
		return $this->fields['description'];
	}

	//surcharge de la fonction all - On récupère uniquement ce qui a été créé dans le workspace courant
	public static function all($conditions = '', $params = array()){
		$db = dims::getInstance()->getDb();
		$res = $db->query('SELECT pm.*, t.label as type_label, d.md5id
							FROM '.self::TABLE_NAME.' pm
							INNER JOIN '.suivi_type::TABLE_NAME.' t ON t.id = pm.id_type
							INNER JOIN '.docfile::TABLE_NAME.' d ON d.id = pm.id_doc
							WHERE pm.id_workspace=:idw '.$conditions.'
							ORDER BY pm.timestp_create ASC',array(':idw'=>$_SESSION['dims']['workspaceid']));

		$lst = array();
		while($fields = $db->fetchrow($res)){
			$model = new print_model();
			$model->openFromResultSet($fields);
			$lst[] = $model;
		}
		return $lst;
	}

	public static function getModelsForType($type){
		$db = dims::getInstance()->getDb();
		$res = $db->query('SELECT pm.*
							FROM '.self::TABLE_NAME.' pm
							INNER JOIN '.suivi_type::TABLE_NAME.' t ON t.id = pm.id_type AND t.label=:type
							WHERE pm.id_workspace=:idw
							ORDER BY pm.timestp_create ASC',array(':idw'=>$_SESSION['dims']['workspaceid'],':type'=>$type));

		$lst = array();
		while($fields = $db->fetchrow($res)){
			$model = new print_model();
			$model->openFromResultSet($fields);
			$lst[] = $model;
		}
		return $lst;
	}

	//surcharge de la fonction delete pour supprimer le document associé
	public function delete(){
		$doc = new docfile();
		if(!empty($this->fields['id_doc'])){
			$doc->open($this->fields['id_doc']);
			if( ! $doc->isNew() ){
				$doc->delete();
			}
		}
		parent::delete();
	}
}
