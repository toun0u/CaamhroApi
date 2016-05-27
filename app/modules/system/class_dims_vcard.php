<?php
class dims_vcard extends dims_data_object {
    const TABLE_NAME = "dims_mod_vcard";
    public function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_docfile','num');
	$this->docfile = null;
	$this->datas = null;
	}

    public function getDocfile(){
	if ($this->docfile == null){
	    require_once DIMS_APP_PATH."modules/doc/class_docfile.php";
	    $this->docfile = new docfile();
	    $this->docfile->open($this->fields['id_docfile']);
	}
	return $this->docfile;
    }

    public function getDatas(){
	if ($this->docfile == null) $this->getDocfile();
	if ($this->datas == null){
	    $lstDatas = $this->docfile->getParseVcf();
	    if (isset($lstDatas[$this->fields['num']-1]))
		$this->datas = $lstDatas[$this->fields['num']-1];
	    else
		$this->datas = $lstDatas;
	}
	return $this->datas;
    }
}
?>
