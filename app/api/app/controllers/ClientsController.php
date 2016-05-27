<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";
include_once DIMS_APP_PATH."api/app/helpers/functions.php";
class ClientsController extends APIController{
	
	public function getClients(){
	$db = dims::getInstance()->getDb();
	$req = 'select * from dims_mod_cata_client limit 100';
	$res = $db->query($req);
	$data = []; 
	while($r = $db->fetchrow($res)){
		$elem = new client();
		$elem->openFromResultSet($r);
		$data[] = array('id'=>$elem->fields['id_client'], 'code'=>$elem->fields['code_client'], 'nom'=>$elem->fields['nom'], 'adr1'=>$elem->fields['adr1'],
			'cp'=>$elem->fields['cp'], 'ville'=>$elem->fields['ville'], 'id_pays'=>$elem->fields['id_pays'], 'marche'=>$elem->fields['code_market'], 'commentaire'=>$elem->fields['commentaire']);
	}
	$json = json_encode(array('clients' => $data), JSON_UNESCAPED_UNICODE);
	echo $json;			
	}

	//va chercher les modifications dans la table dims_api_modif qui enregistre les id des clients modifiés et le type de modification. ressort ça en format json pour que l'appli puisse se mettre à jour
	//de son côté
	public function updateClients($date){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_api_modif_client where date_modif > '.$date;
		$res = $db->query($req);
		$data = [];
		$datemaj = date('YmdHis');
		if($db->numrows($res)>0){
			while($r = $db->fetchrow($res)){
				$elem = new client();
				$req = 'select * from dims_mod_cata_client where id_client = '.$r['id_cli'];
				switch($r['type_modif']){
					case 'INSERT':
						$res2 = $db->query($req);
						if($db->numrows($res2)>0){
							$insert = $db->fetchrow($res2);
							$elem->openFromResultSet($insert);
							$data['insert'][] = array('id'=>$elem->fields['id_client'], 'code'=>$elem->fields['code_client'], 'nom'=>$elem->fields['nom'], 'adr1'=>$elem->fields['adr1'],
					'cp'=>$elem->fields['cp'], 'ville'=>$elem->fields['ville'], 'id_pays'=>$elem->fields['id_pays'], 'marche'=>$elem->fields['code_market'], 'commentaire'=>$elem->fields['commentaire']);
						}
						break;
					case 'DELETE':
						$data['delete'][] = array('id_client' => $r['id_cli']);
						break;
					case 'UPDATE':
						$res2 = $db->query($req);
						if($db->numrows($res2)>0){
							$update = $db->fetchrow($res2);
							$elem->openFromResultSet($update);
							$data['update'][] = array('id'=>$elem->fields['id_client'], 'code'=>$elem->fields['code_client'], 'nom'=>$elem->fields['nom'], 'adr1'=>$elem->fields['adr1'],
					'cp'=>$elem->fields['cp'], 'ville'=>$elem->fields['ville'], 'id_pays'=>$elem->fields['id_pays'], 'marche'=>$elem->fields['code_market'], 'commentaire'=>$elem->fields['commentaire']);
						}
						break;
				}			
			}
			$json = json_encode(array('datemaj'=>$datemaj, 'insert'=>(isset($data['insert']) ? $data['insert'] : 'no_insert'), 'delete'=>(isset($data['delete'])? $data['delete'] : 'no_delete'), 'update' => (isset($data['update']) ? $data['update'] : 'no_update')));
			echo $json;
			//on vide la table des modifs antérieures à celle qu'on vient de récupérer.
			//$req = 'delete from dims_api_modif_client where date_modif < '.$datemaj; 
			//$db->query($req);
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour', 'datemaj'=>$datemaj), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}
}
?>