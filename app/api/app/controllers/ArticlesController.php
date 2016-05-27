<?php
require_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
include_once DIMS_APP_PATH."api/app/helpers/functions.php";
class ArticlesController extends APIController{
	
	public function getAllArticles($update){
		$db = dims::getInstance()->getDb();
		$req = '';
		if(!$update){
			$req = 'select * from dims_mod_cata_article where status != \'DELETED\' limit 100';
		}else{
			$req = 'select * from dims_mod_cata_article where status != \'DELETED\' and date_modify > '.$update;
		}
		//$req = 'select * from dims_mod_cata_article where putarif_0 != 0'; //prds actifs only
		$res = $db->query($req);
		$data = []; 
		$json = '';
		if($db->numrows($res) > 0){
			while($r = $db->fetchrow($res)){
				$elem = new article();
				$elem->openFromResultSet($r);
				$data[] = array('id'=>$elem->fields['id'], 'label'=>$elem->fields['label'], 'description'=>$elem->fields['description'], 'ref'=>$elem->fields['reference'], 
					'taxe_phyto'=>$elem->fields['taxe_certiphyto'], 'putarif'=>$elem->fields['putarif_0'], 'creation'=>$elem->fields['date_create'], 'modif'=>$elem->fields['date_modify']);
			}
			$json = json_encode(array('articles' => $data), JSON_UNESCAPED_UNICODE);
			echo $json;
		}else{
			$json = json_encode(array('statusCode'=>200, 'statusMessage'=>'les données de l\'application sont à jour'), JSON_UNESCAPED_UNICODE);
			echo $json;
		}	
	}

	public function getArticleFamily(){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_mod_cata_article_famille where id_article not in (select id from dims_mod_cata_article where status = \'DELETED\')';
		$res = $db->query($req);
		$data = []; 
		$json = '';
		while($r = $db->fetchrow($res)){
			$elem = new cata_article_famille();
			$elem->openFromResultSet($r);
			$data[] = array('id'=>$elem->fields['id'], 'article'=>$elem->fields['id_article'], 'famille'=>$elem->fields['id_famille']);
		}
		$json = json_encode(array('article_famille' => $data), JSON_UNESCAPED_UNICODE);
		echo $json;	
	}
}

?>