<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_tarif_qte.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
include_once DIMS_APP_PATH."api/app/helpers/functions.php";
class TarifsController extends APIController{

	public function getPrixNets($getall = false){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_mod_cata_prix_nets where reference in (select reference from dims_mod_cata_article where putarif_0 != 0) limit 100'; //prds actifs only
		$res = $db->query($req);
		$data = [];
		while($r = $db->fetchrow($res)){
			$elem = new cata_prix_nets();
			$elem->openFromResultSet($r);
			$data[] = array('type'=>$elem->fields['type'], 'marketcode'=>$elem->fields['code_cm'], 'ref'=>$elem->fields['reference'], 'puht'=>$elem->fields['puht']);
		}
		if($getall){
			return $data;
		}else{
			$json = json_encode(array('prixnets' => $data), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}

	public function getTarQte($update, $getall = false){
		$db = dims::getInstance()->getDb();
		$req = '';
		if(!$update){
			$req = 'select * from dims_mod_cata_tarqte where reference in (select reference from dims_mod_cata_article where putarif_0 != 0) limit 100';
		}else{
			$req = 'select * from dims_mod_cata_tarqte where timestp_modify > '.$update.' and reference in (select reference from dims_mod_cata_article where putarif_0 != 0)';
		}
		$res = $db->query($req);
		$data = [];
		if($db->numrows($res) > 0){
			while($r = $db->fetchrow($res)){
				$elem = new tarif_qte();
				$elem->openFromResultSet($r);
				$data[] = array('type'=>$elem->fields['type'], 'id'=>$elem->fields['id_article'], 'ref'=>$elem->fields['reference'], 'seuil'=>$elem->fields['qtedeb'], 'limite'=>$elem->fields['qtefin'], 
						'puqte'=>$elem->fields['puqte'], 'modif'=>$elem->fields['timestp_modify']);
			}
			if($getall){
				return $data;
			}else{
				$json = json_encode(array('tarqte' => $data), JSON_UNESCAPED_UNICODE);
				echo $json;
			}	
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour', 'datemaj'=>$update), JSON_UNESCAPED_UNICODE);
			echo $json;
		}		
	}

	public function getAllTarifs(){
		$tarqte = $this->getTarQte(false, true);			
		$prixnets = $this->getPrixNets(true);
		$json = json_encode(array('tarqte' => $tarqte, 'prixnets' => $prixnets), JSON_UNESCAPED_UNICODE);
		echo $json;
	}

	//va chercher les modifications dans la table dims_api_modif qui enregistre le type, le code marché et la reference produit ainsi que le type de modif du prix_nets modifié
	//ressort le tout en format json pour que l'appli puisse se mettre à jour de son côté
	public function updatePrixnets($date){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_mod_cata_api_historique where date_modif > '.$date. ' and table_modif = dims_mod_cata_prix_nets';
		$res = $db->query($req);
		$data = [];
		$datemaj = date('YmdHis');
		if($db->numrows($res)>0){
			while($r = $db->fetchrow($res)){
				$elem = new cata_prix_nets();
				$idTarifs = explode('/', $r['id_tarifs']);// on continue demain
				$req = 'select * from dims_mod_cata_prix_nets where type = "'.$r['type_prix'].'" and code_cm = "'.$r['code_m'].'" and reference = "'.$r['ref'].'"';
				switch($r['nature_modif']){
					case 'INSERT':
						$res2 = $db->query($req);
						if($db->numrows($res2)){
							$insert = $db->fetchrow($res2);
							$elem->openFromResultSet($insert);
							$data['insert'][] = array('type'=>$elem->fields['type'], 'marketcode'=>$elem->fields['code_cm'], 'ref'=>$elem->fields['reference'], 'puht'=>$elem->fields['puht']);
						}else{
							/*une ligne qui a été update ne pourra pas être récup dans ce case: 
							dims_mod_cata_prix_nets aura les champs de la close where avec les valeurs à jour donc différentes dims_api_modif_prix_nets
							on rajoute juste les valeurs qui ont été inséré en premier pour vérifier côté client*/
							$data['insert'][] = array('type'=>$r['type_prix'], 'marketcode'=>$r['code_m'], 'ref'=>$r['ref'], 'commentaire'=>'has been updated');
						}
						break;
					case 'UPDATE':
						$res2 = $db->query($req);
						if($db->numrows($res2)){
							$update = $db->fetchrow($res2);
							$elem->openFromResultSet($update);
							$data['update'][] = array('type'=>$elem->fields['type'], 'marketcode'=>$elem->fields['code_cm'], 'ref'=>$elem->fields['reference'], 'puht'=>$elem->fields['puht'],
								'oldType'=>$r['old_type'], 'oldCode'=>$r['old_code'], 'oldRef'=>$r['old_ref']);
						}
						break;
				}			
			}
			$json = json_encode(array('datemaj'=>$datemaj, 'insert'=>(isset($data['insert']) ? $data['insert'] : 'no_insert'), 'delete'=>(isset($data['delete'])? $data['delete'] : 'no_delete'), 'update' => (isset($data['update']) ? $data['update'] : 'no_update')));
			echo $json;
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour', 'datemaj'=>$datemaj), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}
}
?>