<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
class SysController extends APIController{
	const _WS_VERSION								='xxx';
	const _W_LASTUPDATE								='xxxx-xx-xx';

	public function getVersion(){
		echo json_encode(array('version' =>$this::_WS_VERSION,'lastupdate' =>$this::_W_LASTUPDATE, 'status'=>array('statusCode'=>200,'statusMessage'=>'Successful request')));
	}

	public function test($param){
		echo 'ceci est une route test';
	}
}
