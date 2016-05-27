<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
include_once DIMS_APP_PATH."api/app/helpers/functions.php";
class FamiliesController extends APIController{

	public function getAllFamily($update){
		//crée une instance de la base de donnée
		$db = dims::getInstance()->getDb();
		$req = '';
		//vérification du paramètre update pour savoir si c'est une mise à jour ou une récupération complète
		if(!$update){
			$req = 'select * from dims_mod_cata_famille';
		}else{
			$req = 'select * from dims_mod_cata_famille where date_modify > '.$update;
		}
		$res = $db->query($req);
		//instancie un tableau vide pour récupérer les données de l'ORM à encoder en JSON
		$data = [];
		if($db->numrows($res) > 0){
			//parcours les résultats de la requête
			while($r = $db->fetchrow($res)){
				//instancie l'ORM correspondant à la table (déjà prévu dans le framework)
				$elem = new cata_famille();
				//set les attributes de l'ORM
				$elem->openFromResultSet($r);
				//rajoute à chaque passage une ligne dans $data contenant un tableaux associatif répértoriant les données de l'ORM
				$data[] = array('id'=>$elem->fields['id'], 'depth'=>$elem->fields['depth'], 'label'=>$elem->fields['label'], 'parent'=>$elem->fields['id_parent'], 'parents'=>$elem->fields['parents'],
					'creation'=>$elem->fields['date_create'], 'modif'=>$elem->fields['date_modify']);
			}
			//encapsule $data dans un tableau associatif indiquant le type d'objet, puis l'encode en JSON
			$json = json_encode(array('famille'=>$data), JSON_UNESCAPED_UNICODE); 
			//affiche les données pour qu'elles soient lues par l'application
			echo $json;
		//Si pas de nouvelles informations dans la base, on renvoie un code 200.
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour'), JSON_UNESCAPED_UNICODE);
			echo $json;
		}
	}

}