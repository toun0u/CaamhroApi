<?php
require_once '../config.php'; // load config (mysql, path, etc.)
//vérification de l'ip source
$options=array(
	  CURLOPT_URL            => "http://ip.netlor.fr/",
	  CURLOPT_HEADER         => false,
	  CURLOPT_FAILONERROR    => false,
	  CURLOPT_RETURNTRANSFER => true,
);

// curl pour api
$CURL=curl_init();
if(empty($CURL)){die("ERREUR curl_init : Il semble que cURL ne soit pas disponible.");}
curl_setopt_array($CURL,$options);
$res=curl_exec($CURL);
if(curl_errno($CURL)){
	echo "ERREUR curl_exec : ".curl_error($CURL);
}
curl_close($CURL);

if($res == _DIMS_IP_AUTHORIZED){

	require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)
	include_once(DIMS_APP_PATH."modules/system/class_dims.php");
	include_once DIMS_APP_PATH.'include/class_dims_data_object.php';
	include_once(DIMS_APP_PATH."include/class_debug.php");
	include_once DIMS_APP_PATH.'include/global.php';
	ini_set('max_execution_time',0);
	ini_set('memory_limit',"1512M");
	require(DIMS_APP_PATH."include/class_exception.php");
	$dims = new dims();
	if (file_exists(DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) {
		include DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
	}
	$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
	//if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);
	$dims->setDb($db);
	$dims->loadHeader();
	$_DIMS['cste']=$dims->loadLanguage();
	dims::setInstance($dims);
	$dims->init_metabase();

	//CLASSE API_KEYYO
	//PERMET DE REFERENCER TOUTES LES INFOS
	// @author Simon Lejal

	define("_KEYYO_API", "https://ssl.keyyo.com/");


	class API_KEYYO{

	const TABLE_NAME = "dims_mod_telephony_tokens";


		//database dims
		protected $db;
		public $isquery=-1;
		public $qcomplex;

		//constructeur
		public function __construct(){
			$this->db=dims::getInstance()->getDb();// Initiate Database connection
		}

		//Public method for access api.
		//This method dynmically call the method based on the query string
		public function processApi(){
			//GET POST PUT DELETE
			$req=$_SERVER['REQUEST_METHOD'];
			//On speficie le content type
			header('Content-type: application/json');
			//RESSOURCES SUBSTRING DANS UN TABLEAU
			$ressources =explode("/",substr(htmlentities(urldecode($_SERVER['REQUEST_URI'])), 15));
			//on interdit les associations pour le moment
			if(count($ressources)>2){
				http_response_code(403); //Forbidden
				die("");
			}
			//query complexes
			//on verifie la precence d'une query complexe
			$this->isquery=stripos($ressources[count($ressources)-1],'?');
			if(($this->isquery)>0){

				//on explode les parametres
				$this->qcomplex=explode("?",$ressources[count($ressources)-1]);

				//maj des ressources
				if((count($ressources)==1)&(count($this->qcomplex)>1)){
					$ressources[0]=$this->qcomplex[0];
				}
				if((count($ressources)==2)&(count($this->qcomplex)>1)){
					$ressources[1]=$this->qcomplex[0];
				}


				//on recupere les parametres qui nous intéresses et non la ressource
				if(count($this->qcomplex)==2){
					$tmp=$this->qcomplex[1];
					$this->qcomplex=explode("&amp;",$tmp);
				}

				//on remet en forme le tableau des qcomplex..
				$tmp3=array();
				foreach ($this->qcomplex as $key => $value) {
					$tmp2=explode("=", $value);
					if(count($tmp2)==1){
						http_response_code(404); //404
						die("");
					}
					$tmp3[$tmp2[0]]=$tmp2[1];
				}
				$this->qcomplex=$tmp3;

			}


			//PREMIERE RESSOURCE
			$func=$ressources[0];

			//ON cherche la fonction associée a la ressource
			if((int)method_exists($this,$func) > 0){
				$this->$func($ressources);
			}
			//sinon on envoie un 404
			else{
				http_response_code(404);
				return "";
			}
		}

		//fonction associé a la ressources des appels
		public function appels($r){

			//champs possible dans la table des appels
			$fields_appels=array(
				'id' => '',
				'event' => '',
				'caller' => '',
				'callee' => '',
				'call' => '',
				'nom' => '',
				'resume' => '',
				'prenom' => '',
				'account' => '',
				'callref' => '',
				'desc' => '',
				'dateStart' => '',
				'dateEnd' => '',
				'idcontact' => '',
				'fields' => '*',
				'limit' => '',
				'offset' => ''
			);

			//si type requete GET -> lire la ressource
			if($_SERVER['REQUEST_METHOD'] == "GET"){
				//tous les appels
				if(count($r)==1){

					//query complexes a prendre en compte
					if(($this->isquery)>0){
						//on remplis les champs existant dans la base pour la requete sql
						foreach ($this->qcomplex as $key => $value) {
							if(array_key_exists($key, $fields_appels)){
								if($key=="fields" && !(strrpos($value,"id")) ){
									if($value==""){
										http_response_code(404); //404
										die("");
									}
									if(array_key_exists($value, $fields_appels))
										$fields_appels[$key]="id,$value";
									else{
										http_response_code(403); //Forbidden
										die("");
									}
								}else{
									if(($key=="limit") || ($key=="offset")){
										if (!(is_numeric($value))){
											http_response_code(404); //404
											die("");
										}
									}
								$fields_appels[$key]="$value";
								}
							}else{
								http_response_code(404); //404
								die("");
							}
						}

						//prepare la chaine du select
						$chaine_select="$fields_appels[fields]";

						//requete pour recherche via nom/prenom
						$n=$fields_appels['nom'];
						$p=$fields_appels['prenom'];
						if($n!='' && $p=='')
							$query_idcontact= $this->db->query("SELECT id FROM dims_mod_business_contact WHERE lastname = $n;");
						if($n=='' && $p!='')
							$query_idcontact= $this->db->query("SELECT id FROM dims_mod_business_contact WHERE firstname = $p;");
						if($n!='' && $p!='')
							$query_idcontact= $this->db->query("SELECT id FROM dims_mod_business_contact WHERE firstname = $p AND lastname = $n;");

						if(isset($query_idcontact)){
							$tmp_idcontact=array();
							$tmp_idcontact = $query_idcontact->fetchAll();
							for($p=0;$p<count($tmp_idcontact);$p++){
								$fields_appels['idcontact'].=$tmp_idcontact[$p]['id'].'-';
							}
							if($fields_appels['idcontact'] == ''){
								http_response_code(204); //pas de contenu
								die("");
							}else{
								$fields_appels['idcontact']=substr($fields_appels['idcontact'], 0, -1);
							}
						}

						//prepare le where
						$islimit=false;
						$chaine_where="WHERE ";
						foreach ($fields_appels as $key => $value) {
							if( ($key!='desc') && ($key!='fields') && ($key!= 'idcontact') && ($key!= 'nom') && ($key!= 'prenom') && ($key!='dateStart') && ($key!='dateEnd') && ($key!='call') && ($key!='limit') && ($key!='offset') && ($value!='') ){
								if(strcmp($chaine_where,"WHERE ")!=0){
									$chaine_where.=" AND ";
								}
								$chaine_where.="$key='$value'";
							}
							if( ($key=='limit') && ($value!='') ){
								$islimit=true;
								if(strcmp($chaine_where,"WHERE ")==0)
									$chaine_where="";
								$chaine_where.=" LIMIT $value";
							}
							if( ($key=='offset') && ($value!='') ){
								if(!$islimit){
									http_response_code(403); //403
									die("");
								}

								if(strcmp($chaine_where,"WHERE ")==0)
									$chaine_where="";
								$chaine_where.=" OFFSET $value";
							}

							if( ($key=='call') && ($value!='') ){
								if(strcmp($chaine_where,"WHERE ")!=0){
									$chaine_where.=" AND ";
								}
								$chaine_where.="(CALLER=$value XOR CALLEE=$value)";
							}

							if( ($key=='dateStart') && ($value!='') ){
								if(strcmp($chaine_where,"WHERE ")!=0){
									$chaine_where.=" AND ";
								}
								$chaine_where.="$key >= $value";
							}

							if( ($key=='dateEnd') && ($value!='') ){
								if(strcmp($chaine_where,"WHERE ")!=0){
									$chaine_where.=" AND ";
								}
								$chaine_where.="$key <= $value";
							}

							if( ($key=='idcontact') && ($value!='') ){
								if(strcmp($chaine_where,"WHERE ")!=0){
									$chaine_where.=" AND ";
								}
								$tabid=explode('-',$value);
								$chaine_where.='(';
								for($i=0; $i<count($tabid);$i++){
									if($i!=0)
										$chaine_where.=" OR ";
									$chaine_where.="$key = $tabid[$i]";
								}
								$chaine_where.=')';
							}

							if( ($key=='desc') && ($value=='1') ){
								$chaine_where.=" ORDER BY `id` DESC";
							}

						}
						if(strcmp($chaine_where,"WHERE ")==0)
							$chaine_where="";

						// echo($chaine_select);
						// die($chaine_where);

						//execution de la requete
						$res= $this->db->query("SELECT ".$chaine_select." FROM dims_mod_telephony_call_log ".$chaine_where.";");
						if($this->db->numrows($res)>0){
							$tab=array('total' => 0, 'data' => array());
							while ($fields = $this->db->fetchrow($res)) {
								$num_filtered = $fields['caller'] == $fields['account'] ? substr($fields['callee'], 2) : substr($fields['caller'], 2);
								$idcontact_query = $this->db->query("SELECT id FROM dims_mod_business_contact WHERE mobile LIKE :num OR phone LIKE :num ORDER BY id DESC LIMIT 1", array(
									'num' => '%' . $num_filtered
								));

								if ($this->db->numrows($idcontact_query)) {
									$row = $this->db->fetchrow($idcontact_query);
									$fields['idcontact'] = $row['id'];
								}

								$tab['data'][$fields["id"]]=$fields;
							}

							$res = $this->db->query("SELECT ".$chaine_select." FROM dims_mod_telephony_call_log ".$chaine_where."  GROUP BY `dateStart`, `event`, `caller` ORDER BY dateEnd DESC ;");
							$tab['total'] = $this->db->numrows($res);

							$answer = $this->json($tab);
							http_response_code(200); //OK
							echo $answer;
						}else{
							http_response_code(204); //pas de contenu
							echo "";
						}

					//pas de query complexes on envoie tous les champs
					}else{
						$res= $this->db->query("SELECT * FROM dims_mod_telephony_call_log");

						if($this->db->numrows($res)>0){
							while($fields=$this->db->fetchrow($res)){
								$tab[$fields["id"]]=$fields;
							}
							//$tab = $res->fetchAll();
							$answer=$this->json($tab);
							http_response_code(200); //OK
							echo $answer;
						}else{
							http_response_code(204); //pas de contenu
							echo "";
						}

					}
				}

				//un appel referencer par un id
				if(count($r)==2){

					//on verifie que l'id passé est bien un numerique
					if(is_numeric($r[1])){


						//query complexes a prendre en compte
						if(($this->isquery)>0){
							//on remplis les champs existant dans la base pour la requete sql

							foreach ($this->qcomplex as $key => $value) {
								if(array_key_exists($key, $fields_appels)){
									if($key=="fields" && !(strrpos($value,"id")) ){
										if($value==""){
											http_response_code(404); //404
											die("");
										}
										if(array_key_exists($value, $fields_appels))
											$fields_appels[$key]="id,$value";
										else{
											http_response_code(403); //Forbidden
											die("");
										}
									}else{
										if(($key=="limit") || ($key=="offset")){
											if (!(is_numeric($value))){
												http_response_code(404); //404
												die("");
											}
										}
										$fields_appels[$key]="$value";
									}
								}else{
									http_response_code(404); //404
									die("");
								}
							}


							//prepare la chaine du select
							$chaine_select="$fields_appels[fields]";
							//echo $chaine_select;

							//prepare le where
							$chaine_where="WHERE id=$r[1]";


							//execution de la requete
							$res= $this->db->query("SELECT ".$chaine_select." FROM dims_mod_telephony_call_log ".$chaine_where.";");
							if($this->db->numrows($res)>0){
								$tab=array();
								while($fields=$this->db->fetchrow($res)){
									$tab[$fields["id"]]=$fields;
								}
								$answer=$this->json($tab);
								http_response_code(200); //OK
								echo $answer;
							}else{
								http_response_code(204); //pas de contenu
								echo "";
							}

						}else{
							$res= $this->db->query("SELECT * FROM dims_mod_telephony_call_log WHERE id=$r[1]");
							if($this->db->numrows($res)>0){
								$tab=array();
								while($fields=$this->db->fetchrow($res)){
									$tab[$fields["id"]]=$fields;
								}
								$answer=$this->json($tab);
								http_response_code(200); //OK
								echo $answer;
							}else{
								http_response_code(204); //pas de contenu
								echo "";
							}
						}

					}else{
						//sinon on envoie un forbidden
						http_response_code(403); //Forbidden
						die ("");
					}
				}

				//un appel associé a une autre ressource.. interdit pour le moment
				if(count($r)==3){}

			}

			//si type requete POST -> crée une ressource
			if($_SERVER['REQUEST_METHOD'] == "POST"){

				//on refuse les querys avec filtres dans les requetes POST
				if(($this->isquery)>0){
					http_response_code(404); //NOT FOUND
					die("");
				}

				//on refuse les ressources multiples
				if(count($r)>1){
					http_response_code(404); //NOT FOUND
					die("");
				}

				//nettoie les $_POST
				foreach($_POST as $key => $val){
					$_POST[$key]=htmlentities($val);
				}

				//on verifie que les champs account et callee sont bien renseigner
				if( (array_key_exists("account", $_POST)) && (array_key_exists("callee", $_POST)) && (count($_POST)==2) ){
					//version avec verification de l'ip source
					if($fp=fopen(_KEYYO_API."makecall.html?ACCOUNT=$_POST[account]&CALLEE=$_POST[callee]", "r")){
						http_response_code(201); //CREATED
						echo $_SESSION['dims']['connected'];
					}else{
						http_response_code(404); //NOT FOUND

					}
				}
			}

			//aucune utilité dans notre cas
			if($_SERVER['REQUEST_METHOD'] == "UPDATE"){}

			//Requete delete
			if($_SERVER['REQUEST_METHOD'] == "DELETE"){
				http_response_code(200);
				echo("req_delete");
			}


		}


		//Encode array into JSON
		public function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	// Initiiate Library
	$api = new API_KEYYO();
	$api->processApi();
}
?>
