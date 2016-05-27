<?php
class departement extends dims_data_object {
	const TABLE_NAME = 'dims_departement';
	const MY_GLOBALOBJECT_CODE = 452;

	public function __construct() {
		parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function delete(){
		parent::delete(self::MY_GLOBALOBJECT_CODE);
	}

	public function getLabel(){
		return $this->get('name');
	}

	public function save(){
		$this->set('name',strtoupper(dims_convertaccents($this->get('name'))));
		return parent::save(self::MY_GLOBALOBJECT_CODE);
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = $this->get('name');
	}

	public function getPopulation($year = 2006){
		if(isset($this->fields["pop_$year"]))
			return $this->get("pop_$year");
		else
			return 0;
	}
}
