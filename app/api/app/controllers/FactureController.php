<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_facture.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_facture_detail.php";
class FactureController extends APIController{

	public function getFacture($update, $getall = false){
		$db = dims::getInstance()->getDb();
		$req = '';
		if(!$update){
			$req = 'select * from dims_mod_cata_facture limit 10';
		}else{
			$req = 'select * from dims_mod_cata_facture where timestp_modify > '.$update;
		}
		$res = $db->query($req);
		$data = [];
		if($db->numrows($res) > 0){
			while($r = $db->fetchrow($res)){
			$elem = new cata_facture();
			$elem->openFromResultSet($r);
			$data[] = array('id'=>$elem->fields['id'], 'user'=>$elem->fields['id_user'], 'client'=>$elem->fields['code_client'], 'type'=>$elem->fields['type'], 'gauge'=>$elem->fields['gauge_document'], 'creation'=>$elem->fields['date_cree'], 'destinataire'=>$elem->fields['cli_liv_nom'], 'adrliv'=>$elem->fields['cli_liv_adr1'], 'villeliv'=>$elem->fields['cli_liv_ville'], 'paysliv'=>$elem->fields['cli_liv_pays'], 
				'total'=>$elem->fields['total_ttc'], 'condition'=>$elem->fields['payment_conditions'], 'modif'=>$elem->fields['timestp_modify']);
			}
			if($getall){
				return $data;
			}else{
				$json = json_encode(array('facture' => $data), JSON_UNESCAPED_UNICODE);
				echo $json;
			}
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour', 'datemaj'=>$update), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}

	public function getFactureDet($update, $getall = false){
		$db = dims::getInstance()->getDb();
		$req = '';
		if(!$update){
			$req = 'select * from dims_mod_cata_facture_det limit 100';
		}else{
			$req = 'select * from dims_mod_cata_facture_det where timestp_modify > '.$update;
		}
		$res = $db->query($req);
		$data = []; 
		if($db->numrows($res) > 0){
			while($r = $db->fetchrow($res)){
			$elem = new cata_facture_detail();
			$elem->openFromResultSet($r);
			$data[] = array('id'=>$elem->fields['id'], 'idfacture'=>$elem->fields['id_facture'], 'idarticle'=>$elem->fields['id_article'], 'refarticle'=>$elem->fields['ref'], 
				'position'=>$elem->fields['position'], 'qte'=>$elem->fields['qte'], 'unite'=>$elem->fields['unit_of_measure'], 'puttc'=>$elem->fields['pu_ttc']);
			}
			if($getall){
				return $data;
			}else{
				$json = json_encode(array('facturedet' => $data), JSON_UNESCAPED_UNICODE);
				echo $json;
			}
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour', 'datemaj'=>$update), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}
	public function getAllFacture(){
		$facture = $this->getFacture(false, true);
		$factureDet = $this->getFactureDet(false, true);
		$json = json_encode(array('facture' => $facture, 'facturedet' => $factureDet), JSON_UNESCAPED_UNICODE);
		echo $json;
	}
}