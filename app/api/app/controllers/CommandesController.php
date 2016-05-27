<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_commande.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_commande_ligne.php";
class CommandesController extends APIController{

	public function getCde($update = null, $getall = false){
		$db = dims::getInstance()->getDb();
		$req = '';
		if(!$update){
			$req = 'select * from dims_mod_cata_cde limit 100';
		}else{
			$req = 'select * from dims_mod_cata_cde where date_cree > '.$update.' OR date_validation > '.$update;
		}
		$res = $db->query($req);
		$data = [];
		if($db->numrows($res) > 0){
			while($r = $db->fetchrow($res)){
			$elem = new commande();
			$elem->openFromResultSet($r);
			$data[] = array('id'=>$elem->fields['id_cde'], 'user'=>$elem->fields['id_user'], 'client'=>$elem->fields['id_client'], 'creation'=>$elem->fields['date_cree'], 
				'validation'=>$elem->fields['date_validation'], 'createur'=>$elem->fields['representative_creator'], 'valideur'=>$elem->fields['representative_validator'], 
				'etat'=>$elem->fields['etat'], 'commentaire'=>$elem->fields['commentaire'], 'prb'=>$elem->fields['impossibilites_lirvaison'], 'id'=>$elem->fields['id_cde'], 
				'c_nom'=>$elem->fields['contact_nom'], 'c_prenom'=>$elem->fields['contact_prenom'], 'c_tel'=>$elem->fields['contact_tel'], 'totalttc'=>$elem->fields['total_ttc']);
			}
			if($getall){
				return $data;
			}else{
				$json = json_encode(array('commande' => $data), JSON_UNESCAPED_UNICODE);
				echo $json;
			}
		/*}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour'), JSON_UNESCAPED_UNICODE);
			echo $json;
		*/}
	}

	public function getCdeContent($getall = false){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_mod_cata_cde_lignes limit 100';
		$res = $db->query($req);
		$data = []; 
		while($r = $db->fetchrow($res)){
			$elem = new commande_ligne();
			$elem->openFromResultSet($r);
			$data[] = array('id'=>$elem->fields['id_cde_ligne'], 'cde'=>$elem->fields['id_cde'], 'article'=>$elem->fields['id_article'], 'refart'=>$elem->fields['ref'], 
				'quantite'=>$elem->fields['qte'], 'puttc'=>$elem->fields['pu_ttc']);
		}
		if($getall){
			return $data;
		}else{
			$json = json_encode(array('cdeContent' => $data), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}

	public function getAllCde($update){
		$cde = $this->getCde($update, true);
		$cdeContent = '';
		if(count($cde) > 0){
			if($update){
				$cdeContent = $this->updateCdeContent($cde);
			}else{
				$cdeContent = $this->getCdeContent(true);
			}
			$json = json_encode(array('commandes' => $cde, 'commandes_content' => $cdeContent), JSON_UNESCAPED_UNICODE);
			echo $json;
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour', 'datemaj'=>$update), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}

	public function updateCdeContent($cde){
		$data= [];
		$db = dims::getInstance()->getDb();
		foreach($cde as $ligne){
			$req = 'select * from dims_mod_cata_cde_lignes where id_cde = '.$ligne['id'];
			$res = $db->query($req);
			while($r = $db->fetchrow($res)){
				$elem = new commande_ligne();
				$elem->openFromResultSet($r);
				$data[] = array('id'=>$elem->fields['id_cde_ligne'], 'cde'=>$elem->fields['id_cde'], 'article'=>$elem->fields['id_article'], 'refart'=>$elem->fields['ref'], 
					'quantite'=>$elem->fields['qte'], 'puttc'=>$elem->fields['pu_ttc']);
			}
		}
		return($data);
	}
}