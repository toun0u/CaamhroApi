<?php
class client_category extends dims_data_object {

    const TABLE_NAME = 'dims_mod_cata_client_category';

    const CCAT_DEFAULT 		= 1;
    const CCAT_NOT_DEFAULT 	= 0;

    function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function create($lib, $default = self::CCAT_DEFAULT){
    	$this->init_description(true);
    	$this->setugm();
    	$this->setLibelle($lib);
    	$this->setAsDefault($default);
    	return $this->save();
    }

    public function save(){
    	if( $this->isDefault() ){
	    	$default = client_category::findByDefault();
	    	if(! is_null($default) && ( $this->isNew()  || $this->get('id') != $default->get('id') ) ){
	    		$default->setAsDefault(self::CCAT_NOT_DEFAULT);
	    		$default->save();
	    	}
	    }
	    return parent::save();
    }

    public function setLibelle($val){
    	$this->fields['libelle'] = $val;
    }

    public function setAsDefault($val){
    	$this->fields['as_default'] = $val;
    }

    public function getLibelle(){
    	return $this->fields['libelle'];
    }

    public function isDefault(){
    	return $this->fields['as_default'];
    }

    public static function findByDefault($create_it = false){
    	$db = dims::getInstance()->getDb();
    	$ccat = null;
    	$res = $db->query("SELECT * FROM ".self::TABLE_NAME." WHERE as_default = ".self::CCAT_DEFAULT);
    	if($db->numrows($res)){
    		$fields = $db->fetchrow($res);
    		$ccat = new client_category();
    		$ccat->openFromResultSet($fields);
    		return $ccat;
    	}
    	if( is_null($ccat) && $create_it ){
    		$ccat = new client_category();
    		$ccat->create('', true);
    	}
    	return $ccat;
    }

}
?>