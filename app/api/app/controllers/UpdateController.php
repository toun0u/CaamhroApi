<?php
require_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_famille.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_commande.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_commande_ligne.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_facture.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_facture_detail.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_famille.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_tarif_qte.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
class UpdateController extends APIController{

	public function getDeleted($lastupdate){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_mod_cata_api_delete where timestp_delete >'.$lastupdate;
		$res = $db->query($req);
		$data = [];
		while($r=$db->fetchrow($res)){
			if($r['id_ligne'] == NULL){
				$idTarifs = explode('/', $r['id_tarifs']);
				$data[] = array('id'=>array('type'=>$idTarifs[0], 'marketcode'=>$idTarifs[1], 'ref'=>$idTarifs[2]), 'deletedFrom'=>$r['deleted_from']);	
			}else{
				$data[] = array('id'=>$r['id_ligne'], 'deletedFrom'=>$r['deleted_from']);	
			}
		}
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);
		echo $json;
	}

/******récupère la liste des modifs dans l'historique de la db, puis va chercher les lignes correspondantes dans les autres tables pour les renvoyer en json******/
	public function getUpdatedRows($lastupdate){
		$db = dims::getInstance()->getDb();
		$req = 'select * from dims_mod_cata_api_historique where timestp_modify >'.$lastupdate;
		$resu = $db->query($req);
		global $data, $table, $insert, $update;
		while($r=$db->fetchrow($resu)){
			switch($r['table_modif']){				
				case 'dims_mod_cata_famille':
					$sel = 'select * from dims_mod_cata_famille where id = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new cata_famille();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'familles';
						$data = array('id'=>$elem->fields['id'], 'depth'=>$elem->fields['depth'], 'label'=>$elem->fields['label'], 'parent'=>$elem->fields['id_parent'], 'parents'=>$elem->fields['parents'],
						'creation'=>$elem->fields['date_create'], 'modif'=>$elem->fields['date_modify']);
					}
					break;

				case 'dims_mod_cata_article':
					$sel = 'select * from dims_mod_cata_article where id = :id';
				 	$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new article();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'articles';
						$data = array('id'=>$elem->fields['id'], 'label'=>$elem->fields['label'], 'description'=>$elem->fields['description'], 'ref'=>$elem->fields['reference'], 
						'taxe_phyto'=>$elem->fields['taxe_certiphyto'], 'putarif'=>$elem->fields['putarif_0'], 'creation'=>$elem->fields['date_create'], 'modif'=>$elem->fields['date_modify']);
					}
					break;

				case 'dims_mod_cata_article_famille':
					$sel = 'select * from dims_mod_cata_article_famille where id = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new cata_article_famille();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'familleArticles';
						$data = array('id'=>$elem->fields['id'], 'article'=>$elem->fields['id_article'], 'famille'=>$elem->fields['id_famille']);
					}
					break;

				case 'dims_mod_cata_client':
					$sel = 'select * from dims_mod_cata_client where id_client = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new client();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'clients';
						$data = array('id'=>$elem->fields['id_client'], 'code'=>$elem->fields['code_client'], 'nom'=>$elem->fields['nom'], 'adr1'=>$elem->fields['adr1'],
						'cp'=>$elem->fields['cp'], 'ville'=>$elem->fields['ville'], 'id_pays'=>$elem->fields['id_pays'], 'marche'=>$elem->fields['code_market'], 'commentaire'=>$elem->fields['commentaire']);
					}
					break;

				case 'dims_mod_cata_prix_nets':
					$idTarifs = explode('/', $r['id_tarifs']);
					$selTarifs = 'select * from dims_mod_cata_prix_nets where type = :type and code_cm = :code_cm and reference = :ref';
					$param = array(
							':type' => $idTarifs[0],
							':code_cm' => $idTarifs[1],
							':ref' => $idTarifs[2],
						);
					$elem = new cata_prix_nets();
					$res = $db->query($selTarifs,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'prixNets';
						$data = array('type'=>$elem->fields['type'], 'marketcode'=>$elem->fields['code_cm'], 'ref'=>$elem->fields['reference'], 'puht'=>$elem->fields['puht']);
					}
					break;

				case 'dims_mod_cata_tarqte':
					$idTarifs = explode('/', $r['id_tarifs']);
					$selTarifs = 'select * from dims_mod_cata_tarqte where type = :type and code_cm = :code_cm and reference = :ref';
					$param = array(
							':type' => $idTarifs[0],
							':code_cm' => $idTarifs[1],
							':ref' => $idTarifs[2],
						);
					$elem = new tarif_qte();
					$res = $db->query($selTarifs,$param);					
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'tarifsQte';
						$data = array('type'=>$elem->fields['type'], 'id'=>$elem->fields['id_article'], 'ref'=>$elem->fields['reference'], 'seuil'=>$elem->fields['qtedeb'], 'limite'=>$elem->fields['qtefin'], 
						'puqte'=>$elem->fields['puqte'], 'modif'=>$elem->fields['timestp_modify']);
					}
					break;

				case 'dims_mod_cata_facture':
					$sel = 'select * from dims_mod_cata_facture where id = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new cata_facture();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'factures';
						$data = array('id'=>$elem->fields['id'], 'user'=>$elem->fields['id_user'], 'client'=>$elem->fields['code_client'], 'type'=>$elem->fields['type'], 'gauge'=>$elem->fields['gauge_document'], 'creation'=>$elem->fields['date_cree'], 'destinataire'=>$elem->fields['cli_liv_nom'], 'adrliv'=>$elem->fields['cli_liv_adr1'], 'villeliv'=>$elem->fields['cli_liv_ville'], 'paysliv'=>$elem->fields['cli_liv_pays'], 
						'total'=>$elem->fields['total_ttc'], 'condition'=>$elem->fields['payment_conditions'], 'modif'=>$elem->fields['timestp_modify']);
					}
					break;

				case 'dims_mod_cata_facture_det':
					$sel = 'select * from dims_mod_cata_facture_det where id = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new cata_facture_detail();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'detailFactures';
						$data = array('id'=>$elem->fields['id'], 'idfacture'=>$elem->fields['id_facture'], 'idarticle'=>$elem->fields['id_article'], 'refarticle'=>$elem->fields['ref'], 
						'position'=>$elem->fields['position'], 'qte'=>$elem->fields['qte'], 'unite'=>$elem->fields['unit_of_measure'], 'puttc'=>$elem->fields['pu_ttc']);
					}
					break;

				case 'dims_mod_cata_cde':
					$sel = 'select * from dims_mod_cata_cde where id_cde = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new commande();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'commandes';
						$data = array('id'=>$elem->fields['id_cde'], 'user'=>$elem->fields['id_user'], 'client'=>$elem->fields['id_client'], 'creation'=>$elem->fields['date_cree'], 
						'validation'=>$elem->fields['date_validation'], 'createur'=>$elem->fields['representative_creator'], 'valideur'=>$elem->fields['representative_validator'], 
						'etat'=>$elem->fields['etat'], 'commentaire'=>$elem->fields['commentaire'], 'prb'=>$elem->fields['impossibilites_lirvaison'], 'id'=>$elem->fields['id_cde'], 
						'c_nom'=>$elem->fields['contact_nom'], 'c_prenom'=>$elem->fields['contact_prenom'], 'c_tel'=>$elem->fields['contact_tel'], 'totalttc'=>$elem->fields['total_ttc']);
					}
					break;

				case 'dims_mod_cata_cde_lignes':
					$sel = 'select * from dims_mod_cata_cde_lignes where id_cde_ligne = :id';
					$param = array(
							':id' => $r['id_ligne']
						);
					$elem = new commande_ligne();
					$res = $db->query($sel,$param);
					if($db->numrows($res) > 0){
						$dataBrut = $db->fetchrow($res);
						$elem->OpenFromResultSet($dataBrut);
						$table = 'contenuCommandes';
						$data =  array('id'=>$elem->fields['id_cde_ligne'], 'cde'=>$elem->fields['id_cde'], 'article'=>$elem->fields['id_article'], 'refart'=>$elem->fields['ref'], 
						'quantite'=>$elem->fields['qte'], 'puttc'=>$elem->fields['pu_ttc']);
					}
					break;
			}
			if($r['nature_modif'] == 'INSERT' && isset($data)){	
				$insert[$table][] = $data; 
			}elseif(isset($data)){
				$update[$table][] = $data;
			}
		}
		$data = array('insert' => $insert, 'update' => $update);
		$json = json_encode($data);
		echo $json;
	}
}
?>