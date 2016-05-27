<?php
if (isset($dims_op) && $dims_op !== "") {
	if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
		if (file_exists(DIMS_APP_PATH . '/modules/system/' . $_SESSION['dims']['_PREFIX'] . '/op.php')) {
			require_once(DIMS_APP_PATH . '/modules/system/' . $_SESSION['dims']['_PREFIX'] . '/op.php');
		}
	}

	switch ($dims_op) {

		// Lejal Simon mod_telephony
		case 'telephony':
			ob_clean();
			$action = dims_load_securvalue('todo_op', dims_const::_DIMS_CHAR_INPUT, true, true);
			$number = dims_load_securvalue('number', dims_const::_DIMS_CHAR_INPUT, true, true);
			if(isset($action)){
				switch($action){
					default:

					//Nom du contact sinon tiers via un numero
					case 'getNameMatch':

						//test numero inconnu
						if($number=='Appel inconnu')
							die("inconnu inconnu");

						// Number keyyo au format 33606060606
						$number = substr($number, 2);
						$number = "0".$number; // on a maintenant 0606060606
						$sb = dims::getInstance()->getDb();

						//contact
						$query = $db->query("SELECT firstname, lastname FROM dims_mod_business_contact WHERE phone=:number OR mobile=:number LIMIT 1",
							array("number"=>$number));

						if ($db->numrows($query) > 0) {
							$row = $db->fetchrow($query);
							die($row['firstname'] . "|" . $row['lastname']);
						}else{

							//tiers
							$query = $db->query("SELECT intitule FROM dims_mod_business_tiers WHERE telephone=:number OR telmobile=:number LIMIT 1",
							array("number"=>$number));
							if ($db->numrows($query) > 0) {
								$row = $db->fetchrow($query);
								die("Société | ". $row['intitule']);
							}else{
								die("Contact  | Inconnu");
							}
						}
						break;

					//Récupère l'url de la photo si elle existe via un numero
					case 'getPhoto':
						if($number=='Appel inconnu')
							die("");
						$number = substr($number, 2);
						$number = "0".$number; //

						$sb = dims::getInstance()->getDb();
						$query = $sb->query("SELECT id,photo FROM dims_mod_business_contact WHERE phone=:number OR mobile=:number LIMIT 1",
							array("number"=>$number));
						if ($db->numrows($query) > 0) {
							$row = $db->fetchrow($query);
							$directory = DIMS_WEB_PATH.'data/photo_cts/contact_'.$row['id'];
							$pathsave= $directory.'/photo40'.$row['photo'].'.png';
							$path = $directory.'/photo100'.$row['photo'].'.png';
							if(file_exists($directory)){
								if(file_exists($path)){;
									//redimensionne pour notre module (40x40)
									dims_resizeimage2($path, 40, 40,'png',$pathsave);
									die($pathsave);
								}
							}
						}
						die("");
						break;

					//actualise la bdd des logs d'appels quand on ajoute un nouveau contact
					case 'update_contact':

						$sb = dims::getInstance()->getDb();
						$query = $sb->query("SELECT id FROM dims_mod_business_contact WHERE phone=:number OR mobile=:number LIMIT 1",
							array("number"=>$number));
						if ($sb->numrows($query) > 0) {
							$row = $sb->fetchrow($query);
							$id=$row['id'];
						}

						$number = substr($number, 1);
						$number = "33".$number; // on a maintenant 06

						echo($id.' '.$number);

						if($id!=null){
							$query2 = $sb->query("UPDATE dims_mod_telephony_call_log SET idcontact=:idco WHERE (caller=:num AND account<>:num) OR (callee=:num AND account<>:num)",
								array("idco"=>$id, "num"=>$number));
						}

						break;

					//actualise les notes d'un appel déjà pris
					case 'update_resume':
						//reference de l'appel
						$ref = dims_load_securvalue('ref', dims_const::_DIMS_CHAR_INPUT, true, true);
						//nouveau resume
						$res = dims_load_securvalue('res', dims_const::_DIMS_CHAR_INPUT, true, true);

						$sb = dims::getInstance()->getDb();
						$query = $sb->query("UPDATE dims_mod_telephony_call_log SET resume=:res WHERE callref=:ref",
							array("ref"=>$ref, "res"=>$res));
						if ($sb->numrows($query) > 0) {
							 echo 'ok';
						}else{
							echo 'erreur';
						}
						break;

					//retrouve ou genere un token de securité pour la ligne
					case 'reload_token':
						require_once DIMS_APP_PATH.'modules/gescom/models/model.php';
						$Model = new TelephonyModel();
						$account = $Model->getSipAccounts2();
						die($Model->retrieveOrGenerateToken($account[0]['sipaccount']));
						break;

					//enregistre le dernier event de telephony
					case 'lastEvent':
						$levent = dims_load_securvalue('levent', dims_const::_DIMS_CHAR_INPUT, true, true);
						$_SESSION['dims']['mod_telephony']['3']=$levent;
						break;

					//enregistre les données pour le journal d'appel test
					case 'journal':
						$journal = dims_load_securvalue('journal', dims_const::_DIMS_CHAR_INPUT, true, true);
						$_SESSION['dims']['mod_telephony']['journal']=$journal;
						break;

					//renvoie le numéro de la ligne téléphonique de l'utilisateur
					case 'sip':
						require_once DIMS_APP_PATH.'modules/gescom/models/model.php';
						$Model = new TelephonyModel();
						$account = $Model->getSipAccounts2();
						die($account[0]['sipaccount']);
						break;

					/** cases pour les prises de notes durant un appel*/

					case 'telephonynotes_send':
						$varsession = dims_load_securvalue('session', dims_const::_DIMS_CHAR_INPUT, true, true);
						$_SESSION['dims']['mod_telephony']['1']=$varsession;
						break;

					case 'telephonynotes_receive':
						die($_SESSION['dims']['mod_telephony']['1']);
						break;

					case 'callongoin':
						$_SESSION['dims']['mod_telephony']['0']=true;
						$_SESSION['dims']['mod_telephony']['1']="";
						break;

					case 'callmissed':
						$_SESSION['dims']['mod_telephony']['0']=false;
						$_SESSION['dims']['mod_telephony']['1']="";
						break;

					case 'callend':
						$ref = dims_load_securvalue('callref', dims_const::_DIMS_CHAR_INPUT, true, true);
						$note=$_SESSION['dims']['mod_telephony']['1'];
						$sb = dims::getInstance()->getDb();
						$query = $db->query("INSERT INTO dims_mod_telephony_call_log (dateStart, dateEnd, event, caller, callee, account, callref, resume, idcontact, type)
						VALUES ('', '', '', '', '', '', '".$ref."', '".$note."', '', '');");
						$_SESSION['dims']['mod_telephony']['1']="";
						$_SESSION['dims']['mod_telephony']['0']=false;
						break;
				}
			}
			die("");
			break;

		case 'todos':
			require_once DIMS_APP_PATH.'/include/class_todo.php';
			@ob_end_clean();
			$dims = dims::getInstance();
			$todo_op =dims_load_securvalue('todo_op', dims_const::_DIMS_CHAR_INPUT, true, true);
			$go_object = dims_load_securvalue('go_object', dims_const::_DIMS_NUM_INPUT, true, true);
			if(!empty($go_object)){
				$go = new dims_globalobject();
				$go->open($go_object);
				if( ! $go->isNew() ){
					$todo_id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					$keep_context = dims_load_securvalue('keep_context', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					if(!isset($keep_context)) $keep_context = '';
					else $keep_context = base64_decode($keep_context);
					switch($todo_op){
						case 'loadResponseForm':
							if( ! empty($todo_id) ){
								$parent = new todo();
								$parent->open($todo_id);

								$todo = new todo();
								$todo->init_description();
								$todo->setugm();
								//récupération de la liste des utilisateurs
								$todo->setLightAttribute('mode_todo', 'answer');

								$todo->setLightAttribute('action_path', $dims->getScriptEnv().'?todo_op='.dims_const::_SAVE_INTERVENTION.$keep_context);
								$todo->setLightAttribute('back_path',	$dims->getScriptEnv().'?todo_op='.dims_const::_SHOW_COLLABORATION.$keep_context);
								$todo->setLightAttribute('todo_id_globalobject_ref', $parent->fields['id_globalobject_ref']);
								$todo->setLightAttribute('todo_user_from', $_SESSION['dims']['userid']);
								$todo->setLightAttribute('todo_id_parent', $todo_id);
								$todo->display(DIMS_APP_PATH.'/include/views/todos/form.tpl.php');
							}
							break;

						case 'loadValidForm':
							$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true,true);
							if( ! empty($todo_id) ){
								$parent = new todo();
								$parent->open($todo_id);
								$gobject = new dims_globalobject();
								$gobject->open($parent->fields['id_globalobject_ref']);
								$todo = new todo();
								$todo->init_description();
								$todo->setugm();
								//récupération de la liste des utilisateurs
								$todo->setLightAttribute('mode_todo', 'validation');
								if( isset($from) && $from == 'desktop'){
									$redirect_on = dims_load_securvalue('redirect_on', dims_const::_DIMS_CHAR_INPUT, true, true, true);
									if(!isset($redirect_on)) $redirect_on = '';
									else $redirect_on = base64_decode($redirect_on);
									require_once DIMS_APP_PATH.'modules/system/desktopV2/include/class_const_desktopv2.php';
									$todo->setLightAttribute('action_path', $dims->getScriptEnv().'?todo_op='.dims_const::_SAVE_INTERVENTION.$keep_context);//retour sur le bureau
									$todo->setLightAttribute('back_path',	$redirect_on );
									$todo->setLightAttribute('from', 'desktop');

									$todo->setLightAttribute('redirect_on', $redirect_on);
								}
								else{
									$todo->setLightAttribute('action_path', $dims->getScriptEnv().'?todo_op='.dims_const::_SAVE_INTERVENTION.$keep_context);
									$todo->setLightAttribute('back_path',	$dims->getScriptEnv().'?todo_op='.dims_const::_SHOW_COLLABORATION.$keep_context);
								}
								$todo->setLightAttribute('todo_id_globalobject_ref', $parent->fields['id_globalobject_ref']);
								$todo->setLightAttribute('todo_user_from', $_SESSION['dims']['userid']);
								$todo->setLightAttribute('todo_id_parent', $todo_id);
								$todo->display(DIMS_APP_PATH.'/include/views/todos/form.tpl.php');
							}
							break;
					}
				}
			}
			die();
			break;
		case 'empty':
			ob_clean();
			die();
			break;
		case 'constantizer':
			@ob_end_clean();
			$db = dims::getInstance()->getDb();
			$value = dims_load_securvalue('value', dims_const::_DIMS_CHAR_INPUT, true, true);

			$search_in = 'value';

			// Flag !pv switch searching in dims_constant.phpvalue
			if(strpos($value, '!pv')!==false) {
				$value = str_replace('!pv', '', $value);
				$search_in = 'phpvalue';
			}

			$tab_words = explode(' ', trim($value));
			$and = '';
			$i=0;

			foreach($tab_words as $index => $word) {
				if(empty($word)) {
					unset($tab_words[$index]);
				} else {
					$tab_words[$index] = '%'.$word.'%';
				}
			}

			for($i = 0; $i < count($tab_words); $i++) {
				if(!empty($and)) $and .= ' AND ';
				$and .= ' '.$search_in.' LIKE ?';
			}

			$res = $db->query("SELECT * FROM dims_constant WHERE ".$and." ORDER BY value", $tab_words);
			$results = array();

			while($tab = $db->fetchrow($res)) {
				$results[] = $tab;
			}

			echo json_encode($results);
			die();
			break;
		case 'reloadConstantes':
			ob_clean();
			unset($_DIMS['cste']);
			unset($_SESSION['cste']);
			$dims->setLang($_SESSION['dims']['currentlang']);
			die();
			break;
		case 'getLangFields':
			@ob_end_clean();
			$db = dims::getInstance()->getDb();
			$res = $db->query("SELECT id, label FROM dims_lang");
			$tab = array();
			while($fields = $db->fetchrow($res)){
				$tab[$fields['id']] = $fields['label'];
			}
			echo json_encode($tab);
			die();
			break;

		case 'getConstanteInfos':
			@ob_end_clean();
			$phpvalue = dims_load_securvalue('phpvalue', dims_const::_DIMS_CHAR_INPUT, true, true);
			$res = $db->query("SELECT DISTINCT(id_lang), value FROM dims_constant WHERE phpvalue= :phpvalue", array(
				':phpvalue' => array('type' => PDO::PARAM_STR, 'value' => $phpvalue),
			));
			$tab = array();
			while($fields = $db->fetchrow($res)){
				$tab[] = $fields;
			}
			echo json_encode($tab);
			die();
			break;

		case 'saveConstante':
			@ob_end_clean();
			$value = dims_load_securvalue('current_cste', dims_const::_DIMS_CHAR_INPUT, true, true);
			$json_fields = dims_load_securvalue('fields', dims_const::_DIMS_CHAR_INPUT, true, true);
			$fields = json_decode(stripslashes($json_fields), true);

			if($fields['phpvalue']){
				$trouve = false;
				$values = array();
				foreach($fields as $name => $value){
					if(substr($name,0,5) == 'lang_' && (!empty($value) || (isset($fields['current_cste']) && !empty($fields['current_cste'])))){
						if(substr($name,0,5) == 'lang_' && !empty($value)) $trouve = true;
						$values[substr($name, 5)] = $value;
					}
				}
				if(!$trouve){
					echo '-1';//NO_VALUE
				}
				else{
					require_once(DIMS_APP_PATH.'include/class_constant.php');
					$return = array();
					$phpvalue = strtoupper(str_replace(array(" ", "'", "-","/"), '_', dims_convertaccents($fields['phpvalue'])));
					$return['phpvalue'] = $phpvalue;
					$currentLang = $_SESSION['dims']['currentlang'];
					if(isset($fields['current_cste']) && !empty($fields['current_cste'])){//édition
						foreach($values as $id_lang => $value){
							$save = false;
							$c = new dims_constant();
							if($c->openWithContext($fields['current_cste'], $id_lang)){//on est bel est bien en édition pour cette langue
								if(trim($value)==''){
									$c->delete();//suppresion de la constante pour cette langue
									$save = true;//implicite

									//on redéfinit à la main la requête parce que DDO fait l'update sur l'id de la constante (qui peut différer d'une base à l'autre)
									$sql = 'DELETE FROM dims_constant WHERE id_lang='.$id_lang.' AND phpvalue=\''.$fields['current_cste'].'\';';
								}
								else{
									if($c->fields['value'] != $value || $c->fields['phpvalue'] != $phpvalue){
										$c->fields['value'] = $value;
										$c->fields['phpvalue'] = $phpvalue;
										$c->save();
										$save = true;

										//on redéfinit à la main la requête parce que DDO fait l'update sur l'id de la constante (qui peut différer d'une base à l'autre)
										$sql = 'UPDATE dims_constant SET value=\''.dims_sql_filter($value).'\', phpvalue=\''.$phpvalue.'\' WHERE id_lang='.$id_lang.' AND phpvalue=\''.$fields['current_cste'].'\';';
									}
								}
								if($save)$return['sql'][] = $sql;
							}
							else if(isset($value) && $value != ''){ //la constante existe mais pas pour cette langue, il faut la créer
								$c->fields['moduletype'] = (isset($_SESSION['dims']['moduletype']) && trim($_SESSION['dims']['moduletype']) != '') ? $_SESSION['dims']['moduletype'] : 'system';
								$c->fields['id_lang'] = $id_lang;
								$c->fields['value'] = $value;
								$c->fields['phpvalue'] = $phpvalue;
								$c->save();
								$return['sql'][] = $c->sql;
							}
							if($id_lang == $currentLang) $_SESSION['cste'][$phpvalue] = $value;//chargement de la constante en session
						}
					}
					else{//création
						foreach($values as $id_lang => $value){
							$c = new dims_constant();
							$c->fields['moduletype'] = (isset($_SESSION['dims']['moduletype']) && trim($_SESSION['dims']['moduletype']) != '') ? $_SESSION['dims']['moduletype'] : 'system';
							$c->fields['id_lang'] = $id_lang;
							$c->fields['value'] = $value;
							$c->fields['phpvalue'] = $phpvalue;
							$c->save();
							if($id_lang == $currentLang) $_SESSION['cste'][$phpvalue] = $value;//chargement de la constante en session
							$return['sql'][] = $c->sql;
						}
					}

					// Thomas -- 14/03/2012 -- Sauvegarde dans un fichier : /modules/<moduletype>/scripts/constantizer.sql
					$foldScripts = DIMS_TMP_PATH.((isset($_SESSION['dims']['moduletype']) && trim($_SESSION['dims']['moduletype']) != '') ? $_SESSION['dims']['moduletype'] : 'system').'';
					if (!file_exists($foldScripts))
						dims_makedir($foldScripts);
					$src = fopen($foldScripts."/constantizer.sql",'a+');
					foreach($return['sql'] as $sql)
						fwrite($src,"$sql\n");
					fclose($src);

					echo json_encode($return);
				}
			}
			else echo '-2';//NO_PHP_VALUE
			die();
			break;

		case 'alm_log':
			@ob_end_clean();
			require_once DIMS_APP_PATH.'modules/system/class_action_log.php';
			$action = dims_load_securvalue('action', dims_const::_DIMS_NUM_INPUT, true, true);
			$goid = dims_load_securvalue('object', dims_const::_DIMS_NUM_INPUT, true, true);
			$log = new action_log();
			$log->create(session_id(), $action, $goid);
			dims_print_r($log);
			die();
		break;

		case 'exportContactFromSearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['business']['search_ent']);
				$lstct='0';
				if (isset($_SESSION['dims']['search']['result'][dims_const::_DIMS_MODULE_SYSTEM][dims_const::_SYSTEM_OBJECT_CONTACT])) {
					foreach ($_SESSION['dims']['search']['result'][dims_const::_DIMS_MODULE_SYSTEM][dims_const::_SYSTEM_OBJECT_CONTACT] as $k=>$cid) {
						$lstct.=",".$cid;
					}
				}
				$sql =	"
								SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
										mb.protected,mb.name as namefield,mb.label as titlefield
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mb_field as mb
								ON		mb.id=mf.id_mbfield
								RIGHT JOIN	dims_mod_business_meta_categ as mc
								ON		mf.id_metacateg=mc.id
								WHERE		mf.id_object = :idobject
								AND		mf.used=1
								AND		mf.option_exportview=1
								ORDER BY	mc.position, mf.position
								";
				$rs_fields=$db->query($sql, array(':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_TIERS)));
				$_SESSION['business']['exportdata']=array();
				while ($fields = $db->fetchrow($rs_fields)) {
						$sql_s .= ",c.".$fields['namefield'];

						if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
						else $namevalue=$fields['name'];
						$elem=array();
						$elem['title']=$namevalue;
						$elem['namefield']=$fields['namefield'];

						$_SESSION['business']['exportdata'][]=$elem;
				}
				$_SESSION['business']['search_ct_sql']='select c.id as id_ct,c.* from dims_mod_business_contact as c where c.id in ('.$lstct.')';
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_searchexport.php');
			}
			die();
			break;

		case 'exportEntFromSearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['business']['ent_search_ct']);
				$lstent='0';
				if (isset($_SESSION['dims']['search']['result'][dims_const::_DIMS_MODULE_SYSTEM][dims_const::_SYSTEM_OBJECT_TIERS])) {
					foreach ($_SESSION['dims']['search']['result'][dims_const::_DIMS_MODULE_SYSTEM][dims_const::_SYSTEM_OBJECT_TIERS] as $k=>$tid) {
						$lstent.=",".$tid;
					}
				}

				$sql =	"
							SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
									mb.protected,mb.name as namefield,mb.label as titlefield
							FROM		dims_mod_business_meta_field as mf
							INNER JOIN	dims_mb_field as mb
							ON		mb.id=mf.id_mbfield
							RIGHT JOIN	dims_mod_business_meta_categ as mc
							ON		mf.id_metacateg=mc.id
							WHERE		mf.id_object = :idobject
							AND		mf.used=1
							AND		mf.option_exportview=1
							ORDER BY	mc.position, mf.position
							";

				$rs_fields=$db->query($sql, array(':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_TIERS)));
				$_SESSION['business']['exportdata']=array();

				while ($fields = $db->fetchrow($rs_fields)) {
					$sql_s .= ",t.".$fields['namefield'];

					if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
					else $namevalue=$fields['name'];
					$elem=array();
					$elem['title']=$namevalue;
					$elem['namefield']=$fields['namefield'];

					$_SESSION['business']['exportdata'][]=$elem;
				}
				$sql_s = "";
				$sql_s .= "SELECT	t.id as id_ent,
									t.intitule,
									t.timestp_modify,
									t.adresse,
									t.codepostal,
									t.ville,
									t.telephone,
									t.telecopie,
									t.mel,
									t.ent_capital,
									t.ent_activiteprincipale,
									t.ent_effectif,
									t.ent_datecreation,
									t.pays,
									t.site_web,
									t.inactif,
									t.presentation ";
				$sql_s .= $sel_ct;
				$sql_s .= " FROM dims_mod_business_tiers t ";
				$sql_s .= " where t.id in (".$lstent.")";

				$_SESSION['business']['search_ent_sql']=$sql_s;
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_searchexport.php');
			}
			die();
			break;
		case 'createODT':
			require_once(DIMS_APP_PATH . '/include/class_opendocument.php');
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$od = new dims_opendocument(realpath('.').'/data/modele.odt');
				$data = array();
				$data['(COMPANY)'] = "NETLOR SAS";
				$data['(DOCUMENT)'] = "Rapport maturité";
				$data['(VERSION)'] = "2";
				$data['(CLASSIFICATION)'] = "L-8268";
				$data['(SMILE)'] = "SMILE";
				$data['(DATE)'] = Date("d/m/Y");
				$data['(STATE)'] = "Draft";
				$data['(CLIENT)'] = "Patrick Nourrissier";
								$data['(TYPE)'] = "Draft";
				$od->setData($data);
				$od->setFormat("ODT");

								// on s'occupe des images maintenant
								$images=array();

								// repartition sectorielle
								$elem=array();
								$elem["tag"]="svg:title";
								$elem["title"]="repartition_sectorielle";
								$elem["image"]="/Volumes/www/gescom/data/repartition_sectorielle.png";
								$images[]=$elem;

								//niveau de securite
								$elem=array();
								$elem["tag"]="svg:title";
								$elem["title"]="niveau_securite";
								$elem["image"]="/Volumes/www/gescom/data/niveau_sectoriel.png";
								$images[]=$elem;

								// conformite domaines
								$elem=array();
								$elem["tag"]="svg:title";
								$elem["title"]="conformite_domaines";
								$elem["image"]="/Volumes/www/gescom/data/conformite.png";
								$images[]=$elem;

								$od->setImages($images);
				$od->createOpenDocument('sample.odt',realpath('.').'/data/',$images,true);
			}
			die();
			break;
		case 'login_unique':
			ob_end_clean();
			$login = dims_load_securvalue('login', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$user = user::getUserByLogin($login);
			echo (empty($user))?'1':'0';
			die();
			break;
		case 'view_skins':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$select = 'SELECT `id_skin`,`nom_skin`,`id_user` FROM `dims_skin`';
				// FIXME : This is not the way to handle where condition !
				$answer = $db->query($select);
				while ($fields = $db->fetchrow($answer)) {
					if($fields['id_user'] == $_SESSION['dims']['userid'])
						echo dims_create_aff_skin($fields['id_skin'],$fields['nom_skin'],true);
					else
						echo dims_create_aff_skin($fields['id_skin'],$fields['nom_skin']);
				}
			}
			die();
			break;
		case 'visu_skin':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . '/modules/system/desktop_user_skin_visu.php');
			}
			die();
			break;
		case 'update_skin':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_skin = dims_load_securvalue('skin', dims_const::_DIMS_NUM_INPUT, true, true);
				require_once(DIMS_APP_PATH . '/modules/system/class_user.php');
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$user->fields['id_skin']=$id_skin;
				$user->save();
			}
			die();
			break;
		case 'delete_skin':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$select = 'SELECT `nom_skin` FROM `dims_skin` WHERE `id_skin`= :idskin ;';
				$answer = $db->query($select, array(':idskin' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('skin', dims_const::_DIMS_CHAR_INPUT, true, true, true))));
				if ($fields = $db->fetchrow($answer)) {
					$nom_skin = $fields['nom_skin'];
				}
				$dir = $_SESSION['dims']['template_path']."/css/jquery-ui/".$nom_skin;
				unlink($dir."/jquery-ui.css");
				$images = $dir."/images";
				$objects = scandir($images);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") unlink($images."/".$object);
				}
				reset($objects);
				rmdir($images);
				rmdir($dir);

				$select = 'DELETE FROM `dims_skin` WHERE `id_skin`= :idskin ;';
				$db->query($select, array(':idskin' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('skin', dims_const::_DIMS_CHAR_INPUT, true, true, true))));
				$select = 'UPDATE `dims_user` SET `id_skin`="1" WHERE `id_skin`= :idskin ;';
				$db->query($select, array(':idskin' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('skin', dims_const::_DIMS_CHAR_INPUT, true, true, true))));
			}
			die();
			break;
		case 'add_skin_form':
			ob_end_clean();
			?>
			<br>
			Il est possible de créer des designs directement sur le site Jquery-UI : <?= dims_create_button('Jquery-UI','link','','','','http://jqueryui.com/themeroller/'); ?>
			<br>Ensuite, il vous suffit d'uploader l'archive obtenu :
			<br>
			<form enctype="multipart/form-data" name="user_skin_add" method="post" action='admin.php?dims_op=add_skin'>
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("nom_skin");
				$token->field("archive");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<table>
				<tr>
					<td>
						<span>Nom :</span>
					</td>
					<td>
						<input type="text" name="nom_skin" />
					</td>
				<tr>
					<td>
						<span>Fichier :</span>
					</td>
					<td>
						<input type="file" name="archive" />
					</td>
				</tr>
			</table>
			<?= dims_create_button('Add','check','user_skin_add.submit();'); ?>
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("nom_skin");
				$token->field("archive");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			</form>
			<?
			die();
			break;
		case 'add_skin':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$iduser =  $_SESSION['dims']['userid'];
				$nom_skin = dims_load_securvalue('nom_skin', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				if($nom_skin!=""){
					$path = $_SESSION['dims']['template_path']."/css/jquery-ui/";
					$dest= $path.$nom_skin;
					$temp = $path."tmp_upload";
					mkdir($temp, 0777);

					$zipfile=$_FILES['archive']['tmp_name'];
					$files = array();
					$zip = new ZipArchive;
					if ($zip->open($zipfile) === TRUE) {
						for($i = 0; $i < $zip->numFiles; $i++) {
							$entry = $zip->getNameIndex($i);
							if ((!(strpos($entry, "css/")===false)) && (strpos($entry, ".php") === false)) $files[] = $entry;
						}
					$zip->extractTo($temp, $files);
					$zip->close();
					rename($temp."/css/custom-theme",$dest);
					rename($dest."/jquery-ui-1.8.7.custom.css",$dest."/jquery-ui.css");
					rmdir($temp."/css");
					rmdir($temp);
					chmod($dest, 0777);
					}else{
						echo "archive erronée";
					}
					$select = "INSERT INTO `dims_skin` VALUES ('',:nomskin,:iduser); ";
					$id = $db->query($select, array(
						':nomskin' => array('type' => PDO::PARAM_STR, 'value' => $nom_skin),
						':iduser' => array('type' => PDO::PARAM_STR, 'value' => $iduser),
					));
					$id = $db->insertid();
					$select = "UPDATE `dims_user` SET `id_skin`= :idskin WHERE `id`=:iduser;";
					$db->query($select, array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $iduser),
						':idskin' => array('type' => PDO::PARAM_INT, 'value' => $id),
					));
				}

				header("Location: $scriptenv");
			}
			die();
			break;
		case 'get_moreaction':
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_key = dims_load_securvalue('action_id', dims_const::_DIMS_CHAR_INPUT, true, false);
				if ($id_key>0) {
					if (isset($_SESSION['dims']['desktop_more_actions'][$id_key])) {
						echo "";
						unset($_SESSION['dims']['desktop_more_actions'][$id_key]);
						echo "|| <img src=\"./common/img/go-down.png\">";
					}
					else {
						$_SESSION['dims']['desktop_more_actions'][$id_key]=1;
						echo $_SESSION['dims']['desktopactions']["more_".$id_key];
						echo "|| <img src=\"./common/img/go-up.png\">";
					}
				}
			}
			die();
			break;
		case 'edit_stats':
			// edition de statistique
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				if (dims_isadmin()) {
					// edition du contenu
					require_once(DIMS_APP_PATH . "/modules/system/desktop_editstats.php");
				}
			}
			// pour deactiver le contenu admin desktop
			$_SESSION['dims']['moduletype']='';
			break;
		case 'delete_comment':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_comment = dims_load_securvalue('comment_id', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($id_comment>0) {
					// on ouvre l'objet et on v"rifie si c bien un de nos comments
					require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
					$action = new dims_action(/*$db*/);
					$action->open($id_comment);
					if ($action->fields['id_user']==$_SESSION['dims']['userid']) {
						$action->delete();
					}
				}
			}
			dims_redirect("/admin.php");
			break;
		case 'keep_connection':
			die();
			break;
		case 'forgot_password':

			require_once DIMS_APP_PATH.'modules/system/class_forgot_password.php';
			require_once DIMS_APP_PATH.'include/functions/mail.php';

			$mail = dims_load_securvalue('dims_email', dims_const::_DIMS_CHAR_INPUT, false, true);

			// Baptiste
			// On garde smarty dans l'immédiat pour
			// la compatibilité descendente
			$view = view::getInstance();

			if($mail != '') {

				//on verifie que l'email exist bien en base
				$sql_v = "SELECT id FROM dims_user WHERE email LIKE :mail";
				$res_v = $db->query($sql_v, array(':mail' => array('type' => PDO::PARAM_STR, 'value' => $mail)));
				if($db->numrows($res_v) > 0) {

					$forgot_password = new forgot_password();

					$uniqId = mt_rand();

					$forgot_password->fields['timestp_create'] = date('YmdHis');
					$forgot_password->fields['mail'] = $mail;
					$forgot_password->fields['id_ask'] = $uniqId;

					$rootpath="";
					$rootpath=$dims->getProtocol();
					$rootpath.=$_SERVER['HTTP_HOST'];

					$forgot_password->fields['link'] = dims_urlencode($rootpath.'/admin.php?dims_op=getPwd&id_ask='.$uniqId.'&mail='.$mail);
					$forgot_password->fields['validated'] = 0;

					$forgot_password->save();

					//mail
					$from	= array();
					$to		= array();
					$subject= '';

					$to[0]['name'] = '';
					$to[0]['address']  = $mail;

					$email=_DIMS_ADMINMAIL;

					$from[0]['name'] = '';
					$from[0]['address'] = $email;

					//$subject = 'Demande de mot de passe';
					$subject = 'You have asked for a new password';

					$message = 'Hello, <br /><br />';
					$message.= 'This is an automatically generated e-mail, please do not reply.<br /><br />';
					$message.= 'Thank you for requesting a new password, please confirm your request by clicking on the link below :<br />';
					$message.= '<a href="'.$forgot_password->fields['link'].'">'.$forgot_password->fields['link'].'</a><br /><br />';
					$message.= 'In case you have not made request for a new password, we kindly ask you to ignore this e-mail<br /><br />';
					$message .= 'Best regards,';
					$message.= '<br /><br />';

					dims_send_mail($from,$to, $subject, $message);

					$view->assign('pass_forgotten', 'Un email vient de vous être transmis. Si vous ne le recevez pas, merci de contacter l\'administrateur.');
					$view->assign('pass_renouv', true);
					$smarty->assign('pass_forgotten', 'Un email vient de vous être transmis. Si vous ne le recevez pas, merci de contacter l\'administrateur.');
					$smarty->assign('pass_renouv', true);
				}
				else {
					//le mail n'existe pas -> indiquer
					$view->assign('pass_forgotten', 'Un email vient de vous être transmis. Si vous ne le recevez pas, merci de contacter l\'administrateur.');
					$view->assign('pass_renouv', true);
					$smarty->assign('pass_forgotten', 'Un email vient de vous être transmis. Si vous ne le recevez pas, merci de contacter l\'administrateur.');
					$smarty->assign('pass_renouv', true);
				}
			}
			else {
				$view->assign('pass_forgotten', 'L\'adresse est vide !');
				$view->assign('pass_renouv', false);
				$smarty->assign('pass_forgotten', 'L\'adresse est vide !');
				$smarty->assign('pass_renouv', false);
			}
			break;

		case 'getPwd':

			require_once DIMS_APP_PATH.'modules/system/class_forgot_password.php';
			require_once DIMS_APP_PATH.'include/functions/mail.php';

			$mail = dims_load_securvalue('mail', dims_const::_DIMS_CHAR_INPUT, true, true);
			$id_ask = dims_load_securvalue('id_ask', dims_const::_DIMS_NUM_INPUT, true, true);

			if(isset($id_ask) && $id_ask != 0 && isset($mail) && !empty($mail)) {
				//on verifie la confirmation et envoie de mail/newPass

				$sql_ask = 'SELECT id
							FROM dims_user_ask_password
							WHERE validated = 0
							AND mail like :mail
							AND id_ask = :idask
							LIMIT 1';

				$ress_ask = $db->query($sql_ask, array(
					':mail' => array('type' => PDO::PARAM_STR, 'value' => $mail),
					':idask' => array('type' => PDO::PARAM_INT, 'value' => $id_ask),
				));

				if($db->numrows($ress_ask) > 0) {
					$sql_user = 'SELECT id FROM dims_user WHERE email like :mail LIMIT 1';

					$ress_user = $db->query($sql_user, array(':mail' => array('type' => PDO::PARAM_STR, 'value' => $mail)));

					if($db->numrows($ress_user) > 0) {
						$result_ask = $db->fetchrow($ress_ask);
						$result_user= $db->fetchrow($ress_user);

						$user = new user();
						$user->open($result_user['id']);

						$forgot_password = new forgot_password();
						$forgot_password->open($result_ask['id']);

						$forgot_password->fields['validated'] = 1;

						$forgot_password->save();

						$password = '';
						$hash_pwd = '';

						$char_list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
						$size_list	= strlen($char_list)-1;

						for($i = 0; $i < 8; $i++)
						{
							$rand_nb	= mt_rand(0, $size_list);
							$password  .= $char_list[$rand_nb];
						}

						//$hash_pwd = dims_getPasswordHash($password);
						dims::getInstance()->getPasswordHash($password,$user->fields['password'],$user->fields['salt']);
						//$user->fields['password'] = $hash_pwd;
						$user->save();

						//mail
						$from	= array();
						$to		= array();
						$subject= '';

						$to[0]['name']	   = $user->fields['lastname'].' '.$user->fields['firstname'];
						$to[0]['address']  = $user->fields['email'];

						$email=_DIMS_ADMINMAIL;

						$from[0]['name'] = '';
						$from[0]['address'] = $email;

						$subject = 'Your password has been changed';

						$rootpath="";
						$rootpath=$dims->getProtocol();
						$rootpath.=$_SERVER['HTTP_HOST'];

						$message = 'Dear '.$user->fields['firstname'].' '.$user->fields['lastname'].',<br /><br />';
						$message.= 'Your password has now been changed.<br /><br />';
						$message.= 'Login : <b>'.$user->fields['login'].'</b><br />';
						$message.= 'Password : <b>'.$password.'</b><br /><br />';
						$message .= 'Best regards,';
						$message.= '<br /><br />';

						dims_send_mail($from,$to, $subject, $message);

						// Baptiste
						// On garde smarty dans l'immédiat pour
						// la compatibilité descendente
						$view = view::getInstance();
						$smarty->assign('pass_forgotten', 'Le système a modifié votre mot de passe. Il vous sera envoyé par email dans quelques instants.');
						$smarty->assign('pass_renouv', true);
						$view->assign('pass_forgotten', 'Le système a modifié votre mot de passe. Il vous sera envoyé par email dans quelques instants.');
						$view->assign('pass_renouv', true);
					}
				}
				else {
					$smarty->assign('pass_forgotten', 'Erreur : le lien utilisé est cassé. Veuillez contacter votre administrateur');
					$smarty->assign('pass_renouv', false);
					$view->assign('pass_forgotten', 'Erreur : le lien utilisé est cassé. Veuillez contacter votre administrateur');
					$view->assign('pass_renouv', false);
				}
			}
			else {
				$smarty->assign('pass_forgotten', 'Erreur : le système ne reconnaît pas le lien que vous avez utilisé');
				$smarty->assign('pass_renouv', false);
				$view->assign('pass_forgotten', 'Erreur : le système ne reconnaît pas le lien que vous avez utilisé');
				$view->assign('pass_renouv', false);
			}

			break;
		case 'action_save':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				if (isset($_SESSION['dims']['actionadd'])) {
					$id_parent=$_SESSION['dims']['actionadd']['id_parent'];
					$id_workspace=$_SESSION['dims']['actionadd']['id_workspace'];
					//$id_object=$_SESSION['dims']['actionadd']['id_object'];
					//$id_record=$_SESSION['dims']['actionadd']['id_record'];
					$id_module=$_SESSION['dims']['actionadd']['id_module'];
					$id_type=$_SESSION['dims']['actionadd']['id_type'];
					$comment = dims_load_securvalue('action_content', dims_const::_DIMS_CHAR_INPUT, false, true, false);

					require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
					$action = new dims_action(/*$db*/);

					$action->fields['id_parent']=$id_parent;
					$action->fields['id_workspace']=$id_workspace;
					//$action->fields['id_object']=$id_object;
					//$action->fields['id_record']=$id_record;
					$action->fields['id_module']=$id_module;
										$action->setUser($_SESSION['dims']['userid']);
					$action->fields['id_user']=$_SESSION['dims']['userid'];
					$action->fields['comment']= $comment;
					$action->fields['timestp_modify']= dims_createtimestamp();

					$action->fields['id_parent']=$id_parent;
					// commentaire
					$action->fields['type']=$id_type;
					// save object
					$action->saveAlone();
										//dims_print_r($action);die();
					dims_redirect('/admin.php');
				}
			}
			break;
		case 'displayAction':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_parent = dims_load_securvalue('idparent', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_workspace = dims_load_securvalue('idworkspace', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_type = dims_load_securvalue('idtype', dims_const::_DIMS_NUM_INPUT, true, false, false);

				$_SESSION['dims']['actionadd']=array();
				$_SESSION['dims']['actionadd']['id_parent']=$id_parent;
				$_SESSION['dims']['actionadd']['id_workspace']=$id_workspace;
				$_SESSION['dims']['actionadd']['id_object']=$id_object;
				$_SESSION['dims']['actionadd']['id_record']=$id_record;
				$_SESSION['dims']['actionadd']['id_module']=$id_module;
				$_SESSION['dims']['actionadd']['id_type']=$id_type;

				require_once(DIMS_APP_PATH . '/modules/system/desktop_addaction.php');
			}
			die();
			break;
		case 'updateTypeSearch':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$typesearch = dims_load_securvalue('typesearch', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$_SESSION['dims']['typesearch']=$typesearch;
			}
			die();
			break;
		case 'refreshMenuSearch':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . '/modules/system/desktop_menusearch.php');
			}
			die();
			break;

		case "checkTag":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_todo = dims_load_securvalue('id_todo', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$check = dims_load_securvalue('check', dims_const::_DIMS_NUM_INPUT, true, false, false);

				if ($id_todo > 0 && $check > 0) {
					$todo = new todo($db, $_SESSION["dims"]["userid"]);
					$todo->open($id_todo);
					$todo->fields['state'] = $check;
					$todo->save();
				}
			}
			dims_redirect('./admin.php');
			break;
		case "updateDetailContentTag":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				if (isset($_SESSION['dims']['businness']['action']) && $_SESSION['dims']['businness']['action']==_BUSINESS_TAB_CONTACT_GROUP) {
					require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag_manage.php');
				}
				else {
					$tagsearch = dims_load_securvalue('tagsearch', dims_const::_DIMS_NUM_INPUT, true, false, false);
					if ($tagsearch)
						require_once(DIMS_APP_PATH . '/modules/system/desktop_tags_search_detail.php');
					else
						require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag.php');
				}
			}
			die();
			break;
		case "tag_addtemptag":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idtag = dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, false, false);

				if ($idtag>0) {
					$_SESSION['dims']['temp_tag'][$typetag][$idtag]=$idtag;
				}
			}
			die();
			break;
		case "tag_deletetemptag":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idtag = dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, false, false);

				if ($idtag>0 && isset($_SESSION['dims']['temp_tag'][$typtag][$idtag])) {
					unset($_SESSION['dims']['temp_tag'][$typetag][$idtag]);
				}
			}
			die();
			break;
		case "add_globaltag":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				// init de la structure � 0
				$_SESSION['dims']['current_object']['id_record'] = 0;
				$_SESSION['dims']['current_object']['id_object'] = 0;
				$_SESSION['dims']['current_object']['id_module'] = 0;
				echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');

				if (isset($_SESSION['dims']['businness']['action']) && $_SESSION['dims']['businness']['action']==_BUSINESS_TAB_CONTACT_GROUP) {
					require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag_manage.php');
				}
				else {
				require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag.php');
				}
				echo $skin->close_simplebloc();
			}
			die();
			break;
		case "addsel_search":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				if (!isset($_SESSION['dims']['selectedsearch'])) {
					$_SESSION['dims']['selectedsearch'] = array();
				}
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);

				if (!isset($_SESSION['dims']['selectedsearch'][$id_module][$id_object][$id_record])) {
					$_SESSION['dims']['selectedsearch'][$id_module][$id_object][$id_record] = 1;
				}
			}
			break;

		case "delsel_search":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				if (!isset($_SESSION['dims']['selectedsearch'])) {
					$_SESSION['dims']['selectedsearch'] = array();
				}

				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);

				if (isset($_SESSION['dims']['selectedsearch'][$id_module][$id_object][$id_record])) {
					unset($_SESSION['dims']['selectedsearch'][$id_module][$id_object][$id_record]);
				}
			}
			break;

		case "deleteWordSearch":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$ch='';
				$position = dims_load_securvalue('k', dims_const::_DIMS_CHAR_INPUT, true, true);

				foreach ($_SESSION['dims']['modsearch']['expression'] as $k => $w) {
					if ($ch!='') $ch.=" ";

					if ($k!=$position) {
						$ch.=$w['word'];
					}
				}
				// on reinit
				if ($ch =='') {
					unset($_SESSION['dims']['search']);
					unset($_SESSION['dims']['modsearch']);
					unset($_SESSION['dims']['modsearch']['expression_brut']);

				}

				echo $ch;
				die();
			}
			break;

		case "actualizeSearch":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$position = dims_load_securvalue('k', dims_const::_DIMS_CHAR_INPUT, true, true);
				$wordreplace = dims_load_securvalue('word', dims_const::_DIMS_CHAR_INPUT, true, true);
				$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true);
				$ch ='';
				if ($type==2) {
					// on a un tag
					require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
					$tag = new tag();
					$tag->open($wordreplace);
					if (isset($_DIMS['cste'][$tag->fields['tag']])) {
						$tag->fields['tag']=$_DIMS['cste'][$tag->fields['tag']];
					}
					$wordreplace=str_replace(array("\""," ","(",")"),array("'","_","",""),$tag->fields['tag']);
				}

				foreach ($_SESSION['dims']['modsearch']['expression'] as $k => $w) {
					if ($ch!='') $ch.=" ";

					if ($k==$position) {
						// on remplace par le nouveau mot
						$ch.=$wordreplace;
					}
					else {
						$ch.=$w['word'];
					}
				}
				echo $ch;
				die();
			}
			break;

		case "initSearchWord":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['dims']['modsearch']);
			}
			die();
			break;

		case "updateTypeWordSearch":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$position = dims_load_securvalue('k', dims_const::_DIMS_CHAR_INPUT, true, true);
				$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true);

				$_SESSION['dims']['modsearch']['tabfiltre'][$position]['type']=$type;
				$ch="";
				// on boucle sur les elements de l'expression
				foreach ($_SESSION['dims']['modsearch']['expression'] as $k => $w) {
					if ($ch!='') $ch.=" ";

					$ch.=$w['word'];
				}
				echo $ch;
			}
			die();
			break;

		case "updateOperatorWordSearch":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$position = dims_load_securvalue('k', dims_const::_DIMS_CHAR_INPUT, true, true);
				$operator = dims_load_securvalue('operator', dims_const::_DIMS_CHAR_INPUT, true, true);

				$_SESSION['dims']['modsearch']['tabfiltre'][$position]['op']=$operator;
				$ch="";
				// on boucle sur les elements de l'expression
				foreach ($_SESSION['dims']['modsearch']['expression'] as $k => $w) {
					if ($ch!='') $ch.=" ";

					$ch.=$w['word'];
				}
				echo $ch;
			}
			die();
			break;

		case "addTagSearch":
			ob_end_clean();
			$ch="";
			require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$idtag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, false, false);
				if ($idtag>0) {
					$tag = new tag();
					$tag->open($idtag);
					$chcompare= strtolower(str_replace(array("\""," ","(",")"),array("'","_","",""),$tag->fields['tag']));

					if ($tag->fields['tag']!=''){
						// on va parcourir la liste de la recherche
						$insert=true;
						$nb=-1;
						if (isset($_SESSION['dims']['modsearch']['expression'])) {

							foreach ($_SESSION['dims']['modsearch']['expression'] as $k => $w) {
								//if ($w==$chcompare) {
									// on compare le type
								if (isset($_SESSION['dims']['modsearch']['tabfiltre'][$k]['type']) && $_SESSION['dims']['modsearch']['tabfiltre'][$k]['type']==2) {
									if ($_SESSION['dims']['modsearch']['tabfiltre'][$k]['ref']==$idtag)
										$insert=false;
								}
								//}
								$nb++;
							}
						}

						if ($insert) {
							$elem = array();
							$elem['type']=2;
							$elem['ref']=$idtag;
							$elem['op']='OR'; // operator
							// on stocke
							$_SESSION['dims']['modsearch']['tabfiltre'][$nb+1]=$elem;
						}
					}

					foreach ($_SESSION['dims']['modsearch']['expression'] as $k => $w) {
						if ($ch!='') $ch.=" ";

						$ch.=$w['word'];
					}

					// on ajoute le tag à la recherche
					if (isset($_DIMS['cste'][$tag->fields['tag']])) {
						$tag->fields['tag']=$_DIMS['cste'][$tag->fields['tag']];
					}
					if ($insert)
						$ch.= " ".str_replace(array("\""," ","(",")"),array("'","_","",""),$tag->fields['tag']);
					echo $ch;

				}
			}
			die();
			break;

		case "details_newsletter":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && dims_isadmin()) {
				$id_workspace = dims_load_securvalue('id_workspace', dims_const::_DIMS_CHAR_INPUT, true, true);

				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();
				echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>", "", "");
				echo "<div style=\"overflow:auto;position:relative;background:#FFFFFF;\">";
				if ($id_workspace > 0) {
					$workspace = new workspace();
					$workspace->open($id_workspace);
					require_once(DIMS_APP_PATH . '/modules/system/admin_index_workspace_newsletter.php');
				}

				echo "</div>";
				echo $skin->close_simplebloc();
			}
			die();
			break;

		case "details_events":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && dims_isadmin()) {
				$id_workspace = dims_load_securvalue('id_workspace', dims_const::_DIMS_CHAR_INPUT, true, true);

				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();
				echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>", "", "");
				echo "<div style=\"overflow:auto;position:relative;background:#FFFFFF;\">";

				if ($id_workspace > 0) {
					$workspace = new workspace();
					$workspace->open($id_workspace);
					require_once(DIMS_APP_PATH . '/modules/system/admin_index_workspace_events.php');
				}

				echo "</div>";
				echo $skin->close_simplebloc();
			}
			die();
			break;

		case "object_initSearchObject":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);
				$_SESSION['obj'][$element]['currentsearch'] = "";
				if (!isset($_SESSION['obj'][$element]['labeldest'])) {
					$_SESSION['obj'][$element]['labeldest'] = $_DIMS['cste']['_DIMS_LABEL_DESTS'];
				}
				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();
				echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>", "", "");
				echo "<div style=\"overflow:auto;position:relative;background:#FFFFFF;\">";
				require_once(DIMS_APP_PATH . '/modules/system/form_searchusers.php');
				echo "</div>";
				echo $skin->close_simplebloc();
			}
			die();
			break;
		case "object_deleteActionUser":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);
				if ($id_user > 0) {
					if (isset($_SESSION['obj'][$element]['users'][$id_user])) {
						unset($_SESSION['obj'][$element]['users'][$id_user]);
						if (isset($_SESSION['obj'][$element]['update']))
							$_SESSION['obj'][$element]['update'] = true;
					}
				}
			}
			die();
			break;
		case "object_addActionUser":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);

				//$_SESSION[$element]['users'][$id_user]=$id_user;

				if ($id_user > 0) {
					if (!isset($_SESSION['obj'][$element]['users'][$id_user])) {
						$_SESSION['obj'][$element]['users'][$id_user] = $id_user;
						if (isset($_SESSION['obj'][$element]['update']))
							$_SESSION['obj'][$element]['update'] = true;
					}
				}
				//dims_print_r($_SESSION['obj'][$element]);
			}
			//die();
			break;
		case "object_deleteActionGroup":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_grp = dims_load_securvalue('id_grp', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);
				if ($id_grp > 0) {
					if (isset($_SESSION['obj'][$element]['groups'][$id_grp])) {
						unset($_SESSION['obj'][$element]['groups'][$id_grp]);
						if (isset($_SESSION['obj'][$element]['update']))
							$_SESSION['obj'][$element]['update'] = true;
					}
				}
			}
			die();
			break;
		case "object_addActionGroup":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_grp = dims_load_securvalue('id_grp', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);
				if ($id_grp > 0) {
					if (!isset($_SESSION['obj'][$element]['groups'][$id_grp])) {
						$_SESSION['obj'][$element]['groups'][$id_grp] = $id_grp;
						if (isset($_SESSION['obj'][$element]['update']))
							$_SESSION['obj'][$element]['update'] = true;
					}
				}
			}
			die();
			break;
		case "object_deleteActionContact":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);
				if ($id_contact > 0) {
					if (isset($_SESSION['obj'][$element]['contacts'][$id_contact])) {
						unset($_SESSION['obj'][$element]['contacts'][$id_contact]);
						if (isset($_SESSION['obj'][$element]['update']))
							$_SESSION['obj'][$element]['update'] = true;
					}
				}
			}
			die();
			break;

		case "object_addActionContact":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);
				if ($id_contact > 0) {
					if (!isset($_SESSION['obj'][$element]['contacts'][$id_contact])) {
						$_SESSION['obj'][$element]['contacts'][$id_contact] = $id_contact;
						if (isset($_SESSION['obj'][$element]['update']))
							$_SESSION['obj'][$element]['update'] = true;
					}
				}
			}
			die();
			break;
		case "object_search_user":
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {

							$element = dims_load_securvalue('element', dims_const::_DIMS_CHAR_INPUT, true, true);

							if (!isset($_SESSION['obj'][$element]['currentsearch']))
									$_SESSION['obj'][$element]['currentsearch'] = "";
							$nomsearch = dims_load_securvalue('nomsearch', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['obj'][$element]['currentsearch']);
							if (isset($_GET['nomsearch']) && $_GET['nomsearch']=='') $nomsearch='';

							$_SESSION['obj'][$element]['currentsearch'] = $nomsearch;

							require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
							$dims_user = new user();
							$dims_user->open($_SESSION['dims']['userid']);
							$groupslst = array();
							$lstusers = $dims_user->getusersgroup($nomsearch, $_SESSION['dims']['workspaceid'], 0, $groupslst,'dims_workspace_user.activeeventemail=1','dims_workspace_group.activeeventemail=1');

							$lstusersfavorites = array();

							if (isset($_SESSION['obj'][$element]['enabledfavorites']) && $_SESSION['obj'][$element]['enabledfavorites']) {
									$lst = $dims_user->getFavorites(1, null, dims_const::_SYSTEM_OBJECT_FAVORITES);
									if (isset($lst['list'])) {
										foreach ($lst['list'] as $k => $value) {
												$lstusersfavorites[$value['id_record']] = $value['id_record'];
										}
									}
							}

							$lstuserssel = array();
							$lstgroupssel = array();

							if (!empty($_SESSION['obj'][$element]['users']))
									$lstuserssel+=$_SESSION['obj'][$element]['users'];
							if (!empty($_SESSION['obj'][$element]['groups']))
									$lstgroupssel+=$_SESSION['obj'][$element]['groups'];

							echo "<div style=\"width:100%;height:160px;overflow:auto;\">";
							// affichage des groupes
							if (!isset($_SESSION['obj'][$element]['enabledgroups']) || $_SESSION['obj'][$element]['enabledgroups']) {
									if (!empty($groupslst) ) {
											$res = $db->query("select g.* from dims_group as g where id in (" . implode(",", array_fill(0, count($groupslst), '?')) . ") order by label", $groupslst);
											if ($db->numrows($res) > 0) {
												echo "<table style=\"width:100%;\">";
												while ($f = $db->fetchrow($res)) {
														if (!in_array($f['id'], $lstgroupssel)) {
																echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_group.gif\" border=\"0\">&nbsp;" . $f['label'] . "</td><td>";
																echo "<td><a href=\"javascript:void(0);\" onclick=\"object_updateGroupActionFromSelected(" . $element . ",'object_addActionGroup'," . $f['id'] . ");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
														}
												}
												echo "</table>";
											}
									} elseif (strlen($nomsearch) >= 2 && empty($lstusers))
									echo "<p style=\"width:100%;text-align:center\">" . $_DIMS['cste']['_DIMS_LABEL_NO_RESP'] . "</p>";
							}

							// affichage de la liste de resultat
							if (!empty($lstusers) ) {
									$tabcorresp = array();
									$res = $db->query("select id_user,code from dims_group_user inner join dims_group on dims_group.id=dims_group_user.id_group and id_user in (" . implode(",", array_fill(0, count($lstusers), '?')) . ")", $lstusers);
									if ($db->numrows($res)) {
											while ($gu = $db->fetchrow($res)) {
												if ($gu['code']!='') {
													if (!isset($tabcorresp[$gu['id_user']]))
															$tabcorresp[$gu['id_user']] = $gu['code'];
													else
															$tabcorresp[$gu['id_user']].=", " . $gu['code'];
												}
											}
									}

									// requete pour les noms
									$res = $db->query("select distinct id,firstname,lastname,color from dims_user where id in (" . implode(",", array_fill(0, count($lstusers), '?')) . ") order by lastname,firstname", $lstusers);
									if ($db->numrows($res) > 0) {
											echo "<table style=\"width:100%;\">";
											while ($f = $db->fetchrow($res)) {
													if (!in_array($f['id'], $lstuserssel)) {
															echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;" . $f['lastname'] . ". " . $f['firstname'];
															if (isset($tabcorresp[$f['id']]))
																	echo "&nbsp;(" . $tabcorresp[$f['id']] . ")";
															echo "</td><td>";
															echo "<td><a href=\"javascript:void(0);\" onclick=\"object_updateUserActionFromSelected(" . $element . ",'object_addActionUser'," . $f['id'] . ");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
													}
													else {
															/*		echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname'];
															  if (isset($tabcorresp[$f['id']])) echo "&nbsp;(".$tabcorresp[$f['id']].")";
															  echo "</td><td>";
															  echo "<td><a href=\"javascript:void(0);\" onclick=\"object_updateUserActionFromSelected(".$element.",'object_deleteActionUser',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
															 */
													}
											}
											echo "</table>";
									}
							}

							// affichage favoris
							if (!empty($lstusersfavorites)) {
									$res = $db->query("select distinct id,firstname,lastname,color from dims_user where id in (" . implode(",", array_fill(0, count($lstusersfavorites), '?')) . ") order by lastname,firstname", $lstusersfavorites);
									if ($db->numrows($res) > 0) {
											echo "<table style=\"width:100%;\">";
											while ($f = $db->fetchrow($res)) {
													if (!in_array($f['id'], $lstuserssel)) {
															echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;<img src=\"./common/img/fav1.png\" border=\"0\">&nbsp;" . $f['lastname'] . ". " . $f['firstname'];
															if (isset($tabcorresp[$f['id']]))
																	echo "&nbsp;(" . $tabcorresp[$f['id']] . ")";
															echo "</td><td>";
															echo "<td><a href=\"javascript:void(0);\" onclick=\"object_updateUserActionFromSelected(" . $element . ",'object_addActionUser'," . $f['id'] . ");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
													}
													else {
															/*		echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">&nbsp;".$f['lastname'].". ".$f['firstname'];
															  if (isset($tabcorresp[$f['id']])) echo "&nbsp;(".$tabcorresp[$f['id']].")";
															  echo "</td><td>";
															  echo "<td><a href=\"javascript:void(0);\" onclick=\"object_updateUserActionFromSelected(".$element.",'object_deleteActionUser',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
															 */
													}
											}
											echo "</table>";
									}
							}


							echo "</div>";

							echo "||";
			?>
			<span style="width: 80%;;display:block;float:left;font-size:14px;color:#BABABA;font-weight:bold;">
			<?
				if (isset($_SESSION['obj'][$element]['labeldest'])) {
					echo $_SESSION['obj'][$element]['labeldest'];
				} else {

				}

				if (isset($_SESSION['obj'][$element]['update']) && $_SESSION['obj'][$element]['update']) {
					echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_UPDATE'], "disk", 'javascript:document.location.href=\'' . $scriptenv . "?" . $_SESSION['obj'][$element]['query_update'] . '\'');
				}
	?>
				</span>
<?
				echo "<span style=\"width:60%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;\"></span>";
				echo "<div style=\"float:left;width:100%;height:160px;display:block;\">";

				// on affiche par d�faut les personnes s�lectionn�es en interne
				if (!empty($_SESSION['obj'][$element]['users'])) {
					$params = array();
					$params[':idlayer'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
					$res = $db->query("
						SELECT		u.*,
								c.email AS cemail,
								cl.email AS clemail
						FROM		dims_user AS u
						LEFT JOIN	dims_mod_business_contact AS c
						ON		c.id=u.id_contact
						LEFT JOIN	dims_mod_business_contact_layer AS cl
						ON		cl.id=u.id_contact
						AND		cl.id_layer= :idlayer
						AND		type_layer=1
						WHERE		u.id IN (" . $db->getParamsFromArray($_SESSION['obj'][$element]['users'], 'iduser', $params) . ")
						ORDER BY	lastname,firstname",
						$params
					);

					if ($db->numrows($res) > 0) {
						echo "<table style=\"width:100%;\">";
						while ($f = $db->fetchrow($res)) {
							echo "<tr><td width=\"60%\"><img src=\"./common/img/icon_user.gif\" border=\"0\">" . $f['lastname'] . ". " . $f['firstname'] . "</td><td>";
							echo "<td style=\"text-align:right;\">";

							if (isset($_SESSION['obj'][$element]['enabledInfousers']) && $_SESSION['obj'][$element]['enabledInfousers']) {
								echo "<a href=\"javascript:void(0);\" onclick=\"javascript:displayAddTodo(event,0,0,0," . $f['id'] . ");\"><img src=\"./common/img/ticket.png\" border=\"0\" title=\"" . $_DIMS['cste']['_FORM_TASK_TIME_TODO'] . "\"></a>";

								$email = "";
								if ($f['email'] != "")
									$email = $f['email'];
								elseif ($f['cemail'] != '') {
									$email = $f['cemail'];
								} elseif ($f['clemail'] != '') {
									$email = $f['clemail'];
								}

								if ($email != "") {
									echo " <a href=\"mailto:" . $email . "\" ><img src=\"./common/img/email.png\" border=\"0\" title=\"" . $_DIMS['cste']['_DIMS_LABEL_EMAIL'] . "\"></a>";
								} else {
									echo "<img src=\"./common/img/email_dis.png\" border=\"0\"";
								}
								echo " <img src=\"./common/img/phone_dis.png\" title=\"" . $_DIMS['cste']['_DIMS_LABEL_TEL'] . "\" border=\"0\">";
							}

							echo "<a href=\"javascript:void(0);\" onclick=\"object_updateUserActionFromSelected(" . $element . ",'object_deleteActionUser'," . $f['id'] . ");\"><img src=\"./common/img/delete.png\" title=\"" . $_DIMS['cste']['_DELETE'] . "\" border=\"0\"></a>";

							echo "</td></tr>";
						}
						echo "</table>";
					}
				}

				// on affiche par d�faut les personnes s�lectionn�es en interne
				if (!isset($_SESSION['obj'][$element]['groups']) || $_SESSION['obj'][$element]['groups']) {
					if (!empty($_SESSION['obj'][$element]['groups'])) {
						$params = array();
						$res = $db->query('
							SELECT g.*
							FROM dims_group AS g
							WHERE id in (' . $db->getParamsFromArray($_SESSION['obj'][$element]['groups'], 'idgroup', $params) . ')
							ORDER BY label',
							$params
						);
						if ($db->numrows($res) > 0) {
							echo "<table style=\"width:100%;\">";
							while ($f = $db->fetchrow($res)) {
								echo "<tr><td width=\"80%\"><img src=\"./common/img/icon_group.gif\" border=\"0\">" . $f['label'] . "</td><td>";
								echo "<td><a href=\"javascript:void(0);\" onclick=\"object_updateGroupActionFromSelected(" . $element . ",'object_deleteActionGroup'," . $f['id'] . ");\"><img src=\"./common/img/delete.png\" title=\"" . $_DIMS['cste']['_DELETE'] . "\" border=\"0\"></a></td></tr>";
							}
							echo "</table>";
						}
					}
				}
				echo "</div>";
			}
			die();
			break;

		case 'save_todo':
			$todo = new todo($db, $_SESSION["dims"]["userid"]);
			$element = dims_load_securvalue('element', dims_const::_DIMS_NUM_INPUT, true, true, false);

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				require_once(DIMS_APP_PATH . "/include/class_todo_dest.php");
				$todo->setvalues($_POST, "todo_");

				if (isset($_SESSION['dims']['todo']['objectid']) && $_SESSION['dims']['todo']['objectid'] > 0 && $_SESSION['dims']['todo']['moduleid'] > 0) {
					$todo->fields['id_object'] = $_SESSION['dims']['todo']['objectid'];
					$todo->fields['id_record'] = $_SESSION['dims']['todo']['recordid'];
					$todo->fields['id_module'] = $_SESSION['dims']['todo']['moduleid'];

					// recuperation du module_type
					require_once(DIMS_APP_PATH . 'modules/system/class_module.php');
					$currentmod = new module();
					$currentmod->open($_SESSION['dims']['todo']['moduleid']);
					$todo->fields['id_module_type'] = $currentmod->fields['id_module_type'];
				} else {
					$todo->fields['id_object'] = 0;
					$todo->fields['id_record'] = 0;
					$todo->fields['id_module'] = 0;
					$todo->fields['id_module_type'] = 0;
				}

				$todo->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$todo->fields['user_from'] = $_SESSION['dims']['userid'];
				$todo->fields['date'] = dims_createtimestamp();
				$todo->save();

				// lecture des personnes selectionn�es
				if ($todo->fields['type'] != 0 && isset($_SESSION['obj'][$element]['users'])) {
					// on s'occupe des users
					foreach ($_SESSION['obj'][$element]['users'] as $usr) {
						$tododest = new todo_dest();
						$tododest->fields['id_todo'] = $todo->fields['id'];
						$tododest->fields['id_object'] = 1;
						$tododest->fields['id_record'] = $usr['id'];
						$tododest->save();
					}

					// on s'occupe des groupe
					foreach ($_SESSION['obj'][$element]['groups'] as $grp) {
						$tododest = new todo_dest();
						$tododest->fields['id_todo'] = $todo->fields['id'];
						$tododest->fields['id_object'] = 2;
						$tododest->fields['id_record'] = $grp['id'];
						$tododest->save();
					}
				} else {
					if ($todo->fields['type'] == 0) { //personnel
						$tododest = new todo_dest();
						$tododest->fields['id_todo'] = $todo->fields['id'];
						$tododest->fields['id_object'] = 1;
						$tododest->fields['id_record'] = $_SESSION['dims']['userid'];
						$tododest->save();
					}
				}

				unset($_SESSION['obj'][$element]);
			}

			dims_redirect('./admin.php');
			break;
		case 'add_todo':
			require_once(DIMS_APP_PATH . '/modules/system/add_todo.php');
			die();
			break;

		case 'removetagobject':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				$idtagindex = dims_load_securvalue('idtagindex', dims_const::_DIMS_NUM_INPUT, true, false, false);
				if ($idtagindex > 0) {
					require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
					// on ajoute maintenant la liaison sur l'objet
					$tagi = new tag_index();
					$tagi->open($idtagindex);
					$tagi->delete();
				}
			}
			die();
			break;
		case 'removetagobjecttemp':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				$idtag = dims_load_securvalue('idtag', dims_const::_DIMS_NUM_INPUT, true, false, false);
				if ($idtag > 0) {
					if (isset($_SESSION['dims']['tag_temp'][$idtag]))
						unset($_SESSION['dims']['tag_temp'][$idtag]);
				}
			}
			die();
			break;
		case 'tagblockdisplay':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				echo dims_getBlockTag($dims, $_DIMS, $id_module, $id_object, $id_record);
			}
			die();
			break;
		case 'addtagobject':
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idtag = dims_load_securvalue('idtag', dims_const::_DIMS_NUM_INPUT, true, false, false);
				require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
				require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
				// on ajoute maintenant la liaison sur l'objet
				if ($id_record > 0 && $id_object > 0 && $id_module) {
					$tagi = new tag_index();
					$tagi->fields['id_tag'] = $idtag;
					$tagi->fields['id_record'] = $id_record;
					$tagi->fields['id_object'] = $id_object;
					$tagi->fields['id_module'] = $id_module;
					$tagi->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$tagi->fields['id_user'] = $_SESSION['dims']['userid'];
					$mod = $dims->getModule($id_module);
					$tagi->fields['id_module_type'] = $mod['id_module_type'];
					$tagi->save();
				} else {
					// liste temporaire de tags
					$tag = new tag();
					$tag->open($idtag);
					$_SESSION['dims']['tag_temp'][$idtag] = $tag->fields;
					//dims_print_r($_SESSION['dims']['tag_temp']);
				}
			}
			die();
		case 'addnewtagobject':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$private = dims_load_securvalue('tagprivate', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$nomtag = dims_load_securvalue('tag', dims_const::_DIMS_CHAR_INPUT, true, false, false);
				$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true, false, false);

				// ajout du nouveau tag
				require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
				require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');

				$tag = new tag();
				$tag->fields['tag'] = $nomtag;
				$tag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$tag->fields['id_user'] = $_SESSION['dims']['userid'];
				$tag->fields['private'] = $private;
				$tag->fields['type'] = $typetag;
				$tag->save();

				// on ajoute maintenant la liaison sur l'objet
				if ($id_record > 0 && $id_object > 0 && $id_module > 0) {
					$tagi = new tag_index();
					$tagi->fields['id_tag'] = $tag->fields['id'];
					$tagi->fields['id_record'] = $id_record;
					$tagi->fields['id_object'] = $id_object;
					$tagi->fields['id_module'] = $id_module;
					$tagi->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$tagi->fields['id_user'] = $_SESSION['dims']['userid'];
					$mod = $dims->getModule($id_module);
					$tagi->fields['id_module_type'] = $mod['id_module_type'];
					$tagi->save();
				} else {
					// liste temporaire de tags
					$_SESSION['dims']['tag_temp'][$tag->fields['id']] = $tag->fields;
				}
			}
			die();
			break;
		case 'searchtag':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$tag = dims_load_securvalue('tag', dims_const::_DIMS_CHAR_INPUT, true, false, true);
				if (!empty($tag) && $tag != ''){
					$sql = "	SELECT	dims_tag.*
							FROM	dims_tag
							WHERE	ucase(tag) like :tag
							AND	(id_workspace=:idworkspace
							OR	(private=1 and id_user=:iduser))";

					$rs = $db->query($sql, array(
						':tag' => array('type' => PDO::PARAM_STR, 'value' => '%'.$tag.'%'),
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					));
					$key = "";
					if ($db->numrows($rs) > 0) {
						while ($f = $db->fetchrow($rs)) {
							echo "<a href=\"javascript:void(0);\" onclick=\"javascript:addTagObject(" . $f['id'] . ");\">" . $f['tag'] . "</a>&nbsp;&nbsp;";
						}
					} else {
						// on propose l'ajout du tag
						echo "<a href=\"javascript:void(0);\" onclick=\"javascript:addNewTagObject('" . $tag . "');\">" . $_DIMS['cste']['_DIMS_LABEL_NEWTAG'] . "&nbsp;<img border=\"0\" src=\"./common/img/add.gif\" alt=\"\"></a>&nbsp;&nbsp;";
					}
				}
			}
			die();
			break;
		case 'refresh_desktop_right':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				if ($_SESSION['dims']['submainmenu'] != dims_const::_DIMS_SUBMENU_SEARCH)
					require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_widget.php');
				else
					require_once(DIMS_APP_PATH . '/modules/system/lfb_desktop_widget_search.php');
			}
			die();
			break;

		case "displayImportExample" :
			ob_end_clean();
			require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
			echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>","","");
			echo '<table border="1" cellspacing="0" cellpadding="0" style="margin:10px;">
						<tr>
							<td>
								&nbsp;Firstname&nbsp;
							</td>
							<td>
								&nbsp;Lastname&nbsp;
							</td>
							<td>
								&nbsp;<b>Email</b>&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;Firstname 1&nbsp;
							</td>
							<td>
								&nbsp;Lastname 1&nbsp;
							</td>
							<td>
								&nbsp;<b>email1@test.com</b>&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;Firstname 2&nbsp;
							</td>
							<td>
								&nbsp;Lastname 2&nbsp;
							</td>
							<td>
								&nbsp;<b>email2@test.com</b>&nbsp;
							</td>
						</tr>
					</table>';
			echo $skin->close_simplebloc();
			die();
			break;

		case "displayImportExampleEnt" :
			ob_end_clean();
			require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
			echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>","","");
			echo '<table border="1" cellspacing="0" cellpadding="0" style="margin:10px;">
						<tr>
							<td>
								&nbsp;<b>Company name</b>&nbsp;
							</td>
							<td>
								&nbsp;Head of compagny&nbsp;
							</td>
							<td>
								&nbsp;Address&nbsp;
							</td>
							<td>
								&nbsp;...&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;<b>Compagny name 1</b>&nbsp;
							</td>
							<td>
								&nbsp;Head of compagny 1&nbsp;
							</td>
							<td>
								&nbsp;Address 1&nbsp;
							</td>
							<td>
								&nbsp;...&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;<b>Compagny name 2</b>&nbsp;
							</td>
							<td>
								&nbsp;Head of compagny 2&nbsp;
							</td>
							<td>
								&nbsp;Address 2&nbsp;
							</td>
							<td>
								&nbsp;...&nbsp;
							</td>
						</tr>
					</table>';
			echo $skin->close_simplebloc();
			die();
			break;


		case "displayObjectOptions":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_workspace = dims_load_securvalue('idworkspace', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$displayfavorite = dims_load_securvalue('displayfavorite', dims_const::_DIMS_NUM_INPUT, true, false, false);

				if ($id_workspace > 0 && $id_record > 0 && $id_object > 0 && $id_module > 0) {
					require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
					$skin = new skin();
					echo $skin->open_simplebloc("<a href=\"#\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';initDisplayOptions(0);\"><img src=\"./common/img/no.png\" border=\"0\"></a>", "", "");
					//echo "<div style=\"overflow:auto;position:relative;background:#FFFFFF;\">";
					dims_getOptions($id_workspace, $id_module, $id_object, $id_record, $displayfavorite);
					//echo "</div>";

					echo $skin->close_simplebloc();
				}
			}
			die();
			break;
		case "tickets_refresh":
			ob_end_clean();
			ob_start();
			require_once(DIMS_APP_PATH . "/modules/system/public_tickets.php");
			ob_end_flush();
			die();
			break;
		case "eraseticket":
		case "deleteselticket":
			$op = $dims_op;
			require_once(DIMS_APP_PATH . "/modules/system/public_tickets.php");
			die();
			break;

		case "ticket_properties":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$idticket = dims_load_securvalue('idticket', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idobject = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idrecord = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idmodule = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);

				//	catch detail level from object
				if ($idticket > 0) {
					unset($_SESSION['dims']['current_ticket']);
					$_SESSION['dims']['current_ticket']['id_ticket'] = $idticket;

					require_once(DIMS_APP_PATH . '/modules/system/class_ticket.php');

					$ticket = new ticket();
					$ticket->open($idticket);

					//	catch detail level from object
					if ($ticket->fields['id_record'] > 0 && $ticket->fields['id_object'] > 0 && $ticket->fields['id_module'] > 0) {
						unset($_SESSION['dims']['current_object']);
						$_SESSION['dims']['current_object']['id_record'] = $ticket->fields['id_record'];
						$_SESSION['dims']['current_object']['id_object'] = $ticket->fields['id_object'];
						$_SESSION['dims']['current_object']['id_module'] = $ticket->fields['id_module'];
					}

					require_once(DIMS_APP_PATH . "/modules/system/ticket_properties.php");
				}
			}
			die();
			break;

		case "object_properties":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {

				$idobject = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idrecord = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idmodule = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$auto = dims_load_securvalue('auto', dims_const::_DIMS_NUM_INPUT, true, false, false);

				//	catch detail level from object
				if ($idrecord > 0 && $idobject > 0 && $idmodule > 0) {
					unset($_SESSION['dims']['current_object']);
					$_SESSION['dims']['current_object']['id_record'] = $idrecord;
					$_SESSION['dims']['current_object']['id_object'] = $idobject;
					$_SESSION['dims']['current_object']['id_module'] = $idmodule;

					if (!isset($_SESSION['dims']['history_object'])) {
						$_SESSION['dims']['history_object']=array();
					}

					// on regarde mon mettre en session cet element courant et raffraichir la liste des derniers consultes
					$key=$idrecord."_".$idobject."_".$idmodule;

					// indice du tableau
					// verification de l'existence de l'objet dans la base
					$res =$db->query('
						SELECT id
						FROM dims_user_history_object
						WHERE id_user=:iduser
						AND id_module=:idmodule
						AND id_object=:idobject
						AND id_record=:idrecord',
						array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $idmodule),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => $idobject),
							':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $idrecord),
						)
					);

					if ($db->numrows($res)==0) {
						// on update � position + 1 les autres
						$db->query('update dims_user_history_object set position=position+1 where id_user=:iduser', array(
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
						));

						// on insert le nouveau � la position 1
						$db->query('
							INSERT INTO dims_user_history_object
							SET	position=1,
								id_user= :iduser,
								id_module= :idmodule,
								id_object= :idobject,
								id_record= :idrecord',
							array(
								':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
								':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $idmodule),
								':idobject' => array('type' => PDO::PARAM_INT, 'value' => $idobject),
								':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $idrecord),
							)
						);

						$_SESSION['dims']['history_object'][$key] = $_SESSION['dims']['current_object'];

						$siz=sizeof($_SESSION['dims']['history_object']);
						if ($siz>5) {
							// on prend le dernier et on supprime
							$db->query("delete from dims_user_history_object where id_user= :iduser and position>5", array(
								':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							));
						}

						// on garde les 5 derniers
						$_SESSION['dims']['history_object'] = array_slice($_SESSION['dims']['history_object'], 0, 5, true);
					}
					if (!$auto) {
						$_SESSION['dims']['typesearch']=2;// on switch sur la liste des derniers elements consultes
					}
				}
			}

			die();
			break;
		case "object_detail_properties":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$preview = dims_load_securvalue('preview', dims_const::_DIMS_CHAR_INPUT, true, false, false);
				if ($preview == 'all') {
					require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
					$skin = new skin();
					echo $skin->open_widgetbloc("", 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', "./common/img/close.png", '26px', '26px', '-10px', '-2px', 'javascript:dims_hidepopup(\'dims_popup\');', '', '');
					echo "<div style=\"width:100%;overflow:auto;\">";
				}
				$idmodule = $_SESSION['dims']['current_object']['id_module'];
				$idobject = $_SESSION['dims']['current_object']['id_object'];
				$idrecord = $_SESSION['dims']['current_object']['id_record'];

				//	catch detail level from object
				if ($idrecord > 0 && $idobject > 0 && $idmodule > 0) {
					//{$_SESSION['dims']['modules'][$idmodule]['moduletype']}
					$mod = $dims->getModule($idmodule);
					if (isset($mod['label'])) {
						$modtype = $mod['label'];
						$dims_mod_opfile = DIMS_APP_PATH . "modules/{$modtype}/op.php";
						if (file_exists($dims_mod_opfile))
							require_once $dims_mod_opfile;
					}else {
						echo $_DIMS['cste']['_DIMS_TICKET_NO_OBJECT'];
					}
				}
				if ($preview == 'all') {
					echo "</div>";
					echo $skin->close_widgetbloc();
				}
			}
			die();
			break;
		case "object_close":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['dims']['current_object']);
				unset($_SESSION['dims']['current_ticket']);
			}
			die();
			break;
		case "updatefavoriteobject":

			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$idfav = dims_load_securvalue('idfav', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$iduser = dims_load_securvalue('iduser', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$iduserfrom = dims_load_securvalue('iduserfrom', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idmodule = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idworkspace = dims_load_securvalue('idworkspace', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idobject = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$idrecord = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$value = dims_load_securvalue('value', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$passivemode = dims_load_securvalue('passivemode', dims_const::_DIMS_NUM_INPUT, true, false, false);

				$mods = $dims->getModules($idworkspace);

				if ($dims->isModuleEnabled($idmodule, $idworkspace) && $iduser == $_SESSION['dims']['userid']) {

					require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
					require_once(DIMS_APP_PATH . "/modules/system/class_favorite.php");
					$dims_user = new user();
					$dims_user->open($_SESSION['dims']['userid']);
					$favorite = new favorite();


					// recherche d'une ligne existante sur la table
					$res = $db->query('SELECT	id
							FROM		dims_favorite
							WHERE		id_module= :idmodule
							AND		id_object= :idobject
							AND		id_record= :idrecord',
							array(
								':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $idmodule),
								':idobject' => array('type' => PDO::PARAM_INT, 'value' => $idobject),
								':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $idrecord),
							)
					);
					if ($f = $db->fetchrow($res))
						$idfav = $f['id'];

					if ($idfav > 0) {
						$favorite->open($idfav);
						// verify if own fav.
						if ($favorite->fields['id_user'] != $_SESSION['dims']['userid'])
							die();
					}

					// update status off the element
					$favorite->fields['type'] = $value;

					switch ($value) {
						case 0: // delete
							/* if ($idfav>0) {
							  $favorite->delete();
							  } */
							$favorite->save();
							$idfav = $favorite->fields['id'];
							if (!$passivemode)
								echo $dims_user->refreshFavorites($idfav, 0, $idmodule, $idworkspace, $idobject, $idrecord, $iduserfrom);
							break;
						case 1: // insert
							$favorite->fields['id_user'] = $iduser;
							$favorite->fields['id_user_from'] = $iduserfrom;
							$favorite->fields['id_module'] = $idmodule;
							$favorite->fields['id_module_type'] = $mods[$id_module]['id_module_type'];
							$favorite->fields['id_workspace'] = $idworkspace;
							$favorite->fields['id_object'] = $idobject;
							$favorite->fields['id_record'] = $idrecord;
							$favorite->fields['timestp'] = dims_createtimestamp();
							$favorite->save();
							$idfav = $favorite->fields['id'];
							if (!$passivemode)
								echo $dims_user->refreshFavorites($idfav, $value, $idmodule, $idworkspace, $idobject, $idrecord, $iduserfrom);
							break;

						case 2: // update
							if ($idfav == 0) {

								$favorite->fields['id_user'] = $iduser;
								$favorite->fields['id_user_from'] = $iduserfrom;
								$favorite->fields['id_module'] = $idmodule;
								$favorite->fields['id_module_type'] = $mods[$idmodule]['id_module_type'];
								$favorite->fields['id_workspace'] = $idworkspace;
								$favorite->fields['id_object'] = $idobject;
								$favorite->fields['id_record'] = $idrecord;
								$favorite->fields['timestp'] = dims_createtimestamp();
							} else {
								$favorite->fields['timestp'] = dims_createtimestamp();
							}
							$favorite->save();
							$idfav = $favorite->fields['id'];
							echo $dims_user->refreshFavorites($idfav, $value, $idmodule, $idworkspace, $idobject, $idrecord, $iduserfrom);
							break;
					}
				}
			}
			die();
			break;
		case "change_projectlist":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$state = dims_load_securvalue('state', dims_const::_DIMS_NUM_INPUT, true, false, false);
				if ($state == 1)
					$_SESSION['dims']['projectlistview'] = true;
				else
					$_SESSION['dims']['projectlistview'] = false;
			}
			break;

		case "view_workspace":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();
				echo $skin->open_dialog("Workspace");
				echo '<div id="containerworkspace" style="overflow: auto; position: relative; background: none repeat scroll 0% 0% rgb(255, 255, 255);">';
				echo " <!--[if IE]><script type=\"text/javascript\" src=\"./js/excanvas.js\"></script><![endif]-->";
				get_mapview();
				echo "</div>";
				echo $skin->close_dialog("dims_getelem('dims_popup').style.display='none'");
			}

			die();
			break;
		case "view_code_of_conduct":
			ob_end_clean();

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . "/modules/system/class_lang.php");

				$objlang = new lang();

				$objlang->open(1);

				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();

				echo $skin->open_simplebloc("");
				echo "<div style=\"width:100%;height:500px;background:#FFFFFF; overflow:auto;\">";
				echo $objlang->fields['code_of_conduct'];
				echo "</div>";
				echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
				" . $_DIMS['cste']['_DIMS_LABEL_ACCEPT_CONDITION'] . "<input type=\"checkbox\" name=\"acceptcheckbox\" onclick=\"document.getElementById('inputvalidcoc').style.visibility='visible';\"/>
				<input id=\"inputvalidcoc\" style=\"visibility:hidden;\" type=\"button\" onclick=\"javascript:document.location.href='" . $dims->getScriptEnv() . "?dims_op=valid_codeofconduct';\" value=\"" . $_DIMS['cste']['_DIMS_VALID'] . "\" class=\"flatbutton\"/>
				<input type=\"button\" onclick=\"javascript:document.location.href='" . $dims->getScriptEnv() . "?dims_op=bypass_codeofconduct';\" value=\"" . $_DIMS['cste']['_DIMS_CLOSE'] . "\" class=\"flatbutton\"/></div>";
				echo $skin->close_simplebloc();
			}

			die();
			break;
		case "bypass_codeofconduct":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$_SESSION['dims']['user_code_of_conduct'] = 1;
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case "valid_codeofconduct":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$user->fields['code_of_conduct'] = 1;
				$_SESSION['dims']['user_code_of_conduct'] = 1;
				$user->save();
				dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'displaymodinfo':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$idmodule = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, false, false);
				if ($idmodule > 0) {
					require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
					$mod = new module();
					$mod->open($idmodule);
					dims_init_module("system");
					$tabinfo = $mod->getInformation();
					$i = 0;
					if (sizeof($tabinfo) > 0) {
						require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
						$skin = new skin();
						echo $skin->open_simplebloc("");
						echo "<table style=\"width:100%;border:0px;background:#FFFFFF;\" cellpadding=\"0\" cellspacing=\"0\">";
						echo "<tr class=\"trtitle\"><td colspan=\"2\" align=\"center\">" . str_replace("<MODULE>", $mod->fields['label'], $_DIMS['cste']['_MODULE_PROPERTIES']) . "</td></tr>";
						foreach ($tabinfo as $label => $info) {
							if ($i % 2 == 0)
								$i = 1;
							else
								$i=2;
							echo "<tr class=\"trl$i\"><td>" . $label . "</td><td>" . $info . "</td></tr>";
						}

						echo "</table>";
						echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
						<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/></div>";
						echo $skin->close_simplebloc();
					}
				}
			}
			die();
			break;
		case 'uploadstatus':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				// on est connect, on peut regarder le status
				require_once(DIMS_APP_PATH . "/include/upload/status.php");
			}
			die();
			break;
		case 'upload_progress':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				// on est connect, on peut regarder le status
				require_once(DIMS_APP_PATH . "/include/upload/upload_progress.php");
			}
			die();
			break;
		case 'extract_progress':

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				// on est connect, on peut regarder le status

				require_once(DIMS_APP_PATH . "/modules/doc/extract_progress.php");
			}
			die();
			break;
		case 'doc_file_download':
			require_once DIMS_APP_PATH . '/include/class_dims_data_object.php';
			require_once DIMS_APP_PATH . '/include/functions/date.php';
			require_once DIMS_APP_PATH . '/include/functions/filesystem.php';
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

			$version = dims_load_securvalue('version',dims_const::_DIMS_NUM_INPUT, true, true);
			$docfile_md5id = dims_load_securvalue('docfile_md5id',dims_const::_DIMS_CHAR_INPUT, true, true);

			if (!empty($docfile_md5id)) {
				$res=$db->query('SELECT id FROM dims_mod_doc_file WHERE md5id = :md5', array(
					':md5' => array('type' => PDO::PARAM_STR, 'value' => $docfile_md5id),
				));
				if ($fields = $db->fetchrow($res)) {

					$docfile = new docfile();
					$docfile->open($fields['id']);

					if (file_exists($docfile->getfilepath($version))) dims_downloadfile($docfile->getfilepath($version),$docfile->fields['name']);
					elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);

				}
			}
			die();
			break;
		case 'updateallvalidate':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$datenow = dims_createtimestamp();
				$res = $db->query('update dims_param_block_user set date_lastvalidate= :datelastvalidate where id_workspace= :idworkspace and id_user= :iduser', array(
					':datelastvalidate' => array('type' => PDO::PARAM_INT, 'value' => $datenow),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $idmodule),
				));
			}
			break;
		case 'updatevalidate':
			// security filter
			$idmodule = dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['dims']['workspaceid']) && $idmodule > 0) {
				// vrification si module et workspace appartient bien  l'utilisateur
				if (isset($_SESSION['dims']['modules'][$idmodule])) {
					$datenow = dims_createtimestamp();
					$res = $db->query('update dims_param_block_user set date_lastvalidate = :datelastvalidate where id_workspace= :idworkspace and id_user=:iduser and id_module=:idmodule', array(
						':datelastvalidate' => array('type' => PDO::PARAM_INT, 'value' => $datenow),
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
						':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $idmodule),
					));
				}
			}
			break;
		case 'addtags':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['id_object']) && isset($_GET['id_record']) && isset($_GET['moduleid'])) {
							require_once DIMS_APP_PATH.'include/functions/annotations.php';
							dims_annotation(dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, true), dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true, true, true), '', $_SESSION['dims']['userid'], $_SESSION['dims']['workspaceid'], dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true), true);
			}
			die();
			break;
		case 'useCampaign':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['campaignid'])) {
				// test si deja existante
				unset($_SESSION['dims']['search']);
				$_SESSION['dims']['search'] = array();
				$_SESSION['dims']['search']['result'] = array();
				$res = $db->query("select id,id_user from dims_campaign where id = :idcampaign and id_user= :iduser or (share=2 and id_workspace= :idworkspace )", array(
					':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('campaignid', dims_const::_DIMS_NUM_INPUT, true, true, true)),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));

				if ($db->numrows($res) > 0) {
					/* construction du cache sur la campagne */

					$res = $db->query('select * from dims_keywords_campaigncache where id_campaign =:idcampaign', array(
						':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('campaignid', dims_const::_DIMS_NUM_INPUT, true, true, true)),
					));

					if ($db->numrows($res) > 0) {
						while ($f = $db->fetchrow($res)) {
							$_SESSION['dims']['search']['result'][$f['id_module']][$f['id_object']][$f['id_record']] = $f['count'];
						}
					}
				}
			}

			die();
			break;
		case 'execRefreshCacheCampaign':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['campaignid'])) {
				$sql = "
					SELECT		*
					FROM		dims_campaign_keyword
					WHERE		id_campaign=:idcampaign
					ORDER BY	position";

				$resu = $db->query($sql, array(
					':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('campaignid', dims_const::_DIMS_NUM_INPUT, true, true, true)),
				));
				$nbelement = $db->numrows($res);
				if ($nbelement > 0) {
					if (isset($_SESSION['dims']['search']['listselectedword']))
						unset($_SESSION['dims']['search']['listselectedword']);
					if (isset($_SESSION['dims']['search']['listuniqueword']))
						unset($_SESSION['dims']['search']['listuniqueword']);
					if (isset($_SESSION['dims']['search']['cacheresult']))
						unset($_SESSION['dims']['search']['cacheresult']);

					while ($elem = $db->fetchrow($resu)) {
						$wordcampaign = $elem['word'];
						if (addWordSearch($wordcampaign)) {
							include(DIMS_APP_PATH . '/include/functions/system_index_cachesearch.php');
						}
					}
				}
				echo getSearchExpression();
			}
			die();
			break;
		case 'execRefreshCampaign':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				// test si deja existante
				$res = $db->query("select * from dims_campaign where state=1 and (id_user=:iduser and id_workspace=:idworkspace)", array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));

				if ($db->numrows($res) > 0) {
					echo "<img src=\"" . $_SESSION['dims']['template_path'] . "./common/img/system/alert.png\" alt=\"info\" border=\"0\">";
				}
				echo "||";

				foreach (getCampaigns () as $campaign) {
					echo "<a href=\"javascript:void(0);\" onclick=\"javascript:if (confirm('" . $_DIMS['cste']['_DIMS_CONFIRM_DELETE_CAMPAIGN'] . "')) deleteCampaign(" . $campaign['id'] . ");\"><img src=\"./common/img/delete.png\" alt=\"\"></a>";
					echo "<a href=\"#\" onclick=\"useCampaign(" . $campaign['id'] . ");\" alt=\"\">" . $campaign['label'] . "</a>";

					if ($campaign['state'] > 0)
						$state = "state_green.png";
					else
						$state="state_grey.png";

					echo "<a href=\"javascript:void(0);\" onclick=\"javascript:if (confirm('" . $_DIMS['cste']['_DIMS_CONFIRM_UPDATE_CAMPAIGN'] . "')) updateCampaign(" . $campaign['id'] . ");\">
						<img src=\"" . $_SESSION['dims']['template_path'] . "./common/img/system/" . $state . "\" alt=\"\"/></a>&nbsp;&nbsp;";
				}
			}
			die();
			break;
		case 'updateCampaign':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['campaignid'])) {
				require_once DIMS_APP_PATH . '/include/class_campaign.php';
				require_once DIMS_APP_PATH . '/include/class_campaign_keyword.php';

				$campaign = new campaign();

				// test si deja existante
				$res = $db->query('select id,id_user from dims_campaign where id = :idcampaign', array(
					':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('campaignid', dims_const::_DIMS_NUM_INPUT, true, true, true)),
				));

				if ($db->numrows($res) > 0) {

					if ($f = $db->fetchrow($res)) {
						$campaign->open($f['id']);
						// security filter
						if ($campaign->fields['id_user'] == $_SESSION['dims']['userid']) {
							$campaign->update();
							$campaign->save();

							// redirection pour affichage des choix des mots
							dims_redirect("{$scriptenv}?dims_op=execRefreshCampaign");
						}
						else
							session_destroy();
					}
				}
			}
			die();
			break;
		case 'deleteCampaign':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['campaignid'])) {
				require_once DIMS_APP_PATH . '/include/class_campaign.php';
				require_once DIMS_APP_PATH . '/include/class_campaign_keyword.php';

				$campaign = new campaign();

				// test si deja existante
				$res = $db->query('select id,id_user from dims_campaign where id =:idcampaign', array(
					':idcampaign' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('campaignid', dims_const::_DIMS_NUM_INPUT, true, true, true)),
				));

				if ($db->numrows($res) > 0) {
					if ($f = $db->fetchrow($res)) {
						$campaign->open($f['id']);
						// security filter
						if ($campaign->fields['id_user'] == $_SESSION['dims']['userid'])
							$campaign->delete();
						else
							session_destroy();
					}
				}
			}
			dims_redirect("{$scriptenv}?dims_op=execRefreshCampaign");
			die();
			break;
		case 'add_campain':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$idcampaign = 0;
				$campaign = new campaign();

				require_once DIMS_APP_PATH . '/include/class_campaign.php';

				foreach ($_SESSION['dims']['search']['listselectedword'] as $pos => $elem) {
					if ($elem['id_campaign'] != 0)
						$idcampaign = $elem['id_campaign'];
				}

				if ($idcampaign > 0) {
					$campaign->open($idcampaign);
				}
				else
					$campaign->init_description();

				echo "<form action=\"admin.php\" method=\"post\" name=\"form_addcampain\">";
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("dims_op",	"save_campaign");
				$token->field("campaign_label");
				$token->field("campaign_description");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
				echo "<div style=\"width:100%;display:block;\">
							<input type=\"hidden\" name=\"dims_op\" value=\"save_campaign\">
							" . $_DIMS['cste']['_DIMS_LABEL'] . "
						</div>
						<div style=\"width:100%; display:block;\">
							<input style=\"width:99%;border:1px solid #cecece;font-size:11px;vertical-align: top;height:14px;\" type=\"text\" name=\"campaign_label\" value=\"" . $campaign->fields['label'] . "\"></input>
						</div>
						<div style=\"width:100%; display:block;\">
							" . $_DIMS['cste']['_DIMS_DESCRIPTION'] . "
						</div>
						<div style=\"width:100%; display:block;\">
							<textarea style=\"width:99%;border:1px solid #cecece;font-size:11px;\" name=\"campaign_description\" rows=\"10\">" . $campaign->fields['description'] . "</textarea>
						</div>
						<div style=\"width:100%; display:block;\">
							<input type=\"button\" onclick=\"document.form_addcampain.dims_op.value=''; document.form_addcampain.submit()\" class=\"flatbutton\" value=\"" . $_DIMS['cste']['_DIMS_LABEL_CANCEL'] . "\">
							<input type=\"submit\" onclick=\"document.form_addcampain.submit();\" class=\"flatbutton\" value=\"" . $_DIMS['cste']['_DIMS_SAVE'] . "\">
						</div>
					</form>";
			}
			die();
			break;
		case "save_campaign";
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['search']['listselectedword'])) {
				require_once(DIMS_APP_PATH . "/include/functions/string.php");
				require_once DIMS_APP_PATH . '/include/class_campaign.php';
				require_once DIMS_APP_PATH . '/include/class_campaign_keyword.php';

				$campaign = new campaign();

				// test si deja existante
				$res = $db->query('select id from dims_campaign where label like :label and id_user=:iduser', array(
					':label' => array('type' => PDO::PARAM_STR, 'value' => dims_load_securvalue('campaign_label', dims_const::_DIMS_CHAR_INPUT, true, true, true)),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
				));

				if ($db->numrows($res) > 0) {
					if ($f = $db->fetchrow($res))
						$campaign->open($f['id']);
					// on supprime les correspondances
					$campaign->deleteCorresp();
				}
				else
					$campaign->init_description();

				$campaign->setvalues($_POST, "campaign_");
				$campaign->fields['id_user'] = $_SESSION['dims']['userid'];
				$campaign->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$campaign->save();

				// on enrgistre la composition de la campagne
				foreach ($_SESSION['dims']['search']['listselectedword'] as $pos => $elem) {
					$campaign_word = new campaign_keyword();
					$campaign_word->init_description();
					$campaign_word->fields['id_campaign'] = $campaign->fields['id'];
					$campaign_word->fields['position'] = $pos;
					$campaign_word->fields['('] = $elem['('];
					$campaign_word->fields[')'] = $elem[')'];
					$campaign_word->fields['op'] = $elem['op'];
					$campaign_word->fields['word'] = $elem['word'];
					$campaign_word->fields['key'] = $elem['key'];
					$campaign_word->fields['state'] = 0;
					$campaign_word->save();
				}
			}
			dims_redirect("{$scriptenv}?dims_mainmenu=" . dims_const::_DIMS_MENU_HOME . "&dims_desktop=portal");
			break;

		case 'word_addparenthese':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['begin']) && isset($_GET['last'])) {
				if (isset($_SESSION['dims']['search']['listselectedword'])) {
					// premire chose, regarder si la chaine contient du contenu
					$begin = -1;
					$last = -1;

					// traitement des guillemets
					$wordbegin = str_replace("\"", "", dims_load_securvalue('begin', dims_const::_DIMS_CHAR_INPUT, true, true, true));
					$wordlast = str_replace("\"", "", dims_load_securvalue('last', dims_const::_DIMS_CHAR_INPUT, true, true, true));
					foreach ($_SESSION['dims']['search']['listselectedword'] as $key => $elem) {
						if ($wordbegin == $elem['word'])
							$begin = $key;
						if ($wordlast == $elem['word'])
							$last = $key;
					}

					if ($begin >= 0 && $last >= 0 && $begin < $last) {
						// verification de non parenthesage existante entre les deux bornes
						$i = $begin;
						$pouvrante = 0;
						$pfermante = 0;

						while ($i <= $last) {
							$pouvrante+=$_SESSION['dims']['search']['listselectedword'][$i]['('];
							$pfermante+=$_SESSION['dims']['search']['listselectedword'][$i][')'];
							$i++;
						}
						$cpte = 0;
						// on compte ventuellement le nbre de parenthse en plus fermante
						// cas ou on enleverait des parenthses	l'interieur d'autres qui commencent avec le meme premier mais autre dernier
						for ($i = $last + 1; $i < sizeof($_SESSION['dims']['search']['listselectedword']); $i++) {
							$cpte+=$_SESSION['dims']['search']['listselectedword'][$i][')'] - $_SESSION['dims']['search']['listselectedword'][$i]['('];
						}

						// si on a le mme nombre on peut ajouter les parenthses

						if ($pouvrante == $pfermante || $pouvrante == ($pfermante + $cpte)) {

							if ($_SESSION['dims']['search']['listselectedword'][$begin]['('] > 0 && $_SESSION['dims']['search']['listselectedword'][$last][')'] > 0) {
								// on a deja alors on enlve
								$_SESSION['dims']['search']['listselectedword'][$begin]['(']--;
								$_SESSION['dims']['search']['listselectedword'][$last][')']--;
							} else {
								$_SESSION['dims']['search']['listselectedword'][$begin]['(']++;
								$_SESSION['dims']['search']['listselectedword'][$last][')']++;
							}
						}
					}
				}
				// redirection pour affichage des choix des mots
				dims_redirect("{$scriptenv}?dims_op=word_refreshselectedword");
			}
			die();
			break;
		case 'word_updateoperatorsearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['wordid'])) {
				$idword = dims_load_securvalue('wordid', dims_const::_DIMS_NUM_INPUT, true, true, true);
				if (isset($_SESSION['dims']['search']['listselectedword'][$idword])) {
					$_SESSION['dims']['search']['listselectedword'][$idword]['op'] = ($_GET['val'] == 1) ? "AND" : "OR";
				}
				// redirection pour affichage des choix des mots
				dims_redirect("{$scriptenv}?dims_op=word_refreshselectedword");
			}
			break;

		case 'word_addsearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['word'])) {
				addWordSearch(dims_load_securvalue('word'));
			}
			// redirection pour affichage des choix des mots
			//dims_redirect("{$scriptenv}?dims_op=word_refreshselectedword");
			echo getSearchExpression();
			die();
			break;

		case 'reset_selection':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['dims']['search']);
				unset($_SESSION['dims']['modsearch']);
								dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'word_deleteselected':
			if (isset($_SESSION['dims']['search']['listselectedword']))
				unset($_SESSION['dims']['search']['listselectedword']);
			if (isset($_SESSION['dims']['search']['listuniqueword']))
				unset($_SESSION['dims']['search']['listuniqueword']);
			if (isset($_SESSION['dims']['search']['cacheresult']))
				unset($_SESSION['dims']['search']['cacheresult']);
			if (isset($_SESSION['dims']['search']['result']))
				unset($_SESSION['dims']['search']['result']);
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])
				$rs = $db->query('delete from dims_keywords_usercache where id_user=:iduser', array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
				));

			// reset de la session temporaire
			unset($_SESSION['dims']['search']['id_campaign']);
			// redirection pour affichage des choix des mots
			dims_redirect("{$scriptenv}?dims_op=word_refreshselectedword");
			break;
		case 'word_deletesearch':
			$wordid = dims_load_securvalue('wordid', dims_const::_DIMS_NUM_INPUT, true, true, true);
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($wordid)) {
				if (isset($_SESSION['dims']['search']['listselectedword'][$wordid])) {
					// on va traiter les parenthses avant de supprimer l'lment
					// onn regarde	gauche
					if ($_SESSION['dims']['search']['listselectedword'][$wordid]['('] > 0) {
						// on compte le nbre  supprimer
						$nb = $_SESSION['dims']['search']['listselectedword'][$wordid]['('];

						//compteur de surprus
						$nbplus = 0;
						$i = $wordid + 1;

						while ($i < sizeof($_SESSION['dims']['search']['listselectedword']) && $nb > 0) {
							// on ajout celles pouvant etre cres en plus
							$nbplus+=$_SESSION['dims']['search']['listselectedword'][$i]['('];
							$nbtemp = $_SESSION['dims']['search']['listselectedword'][$i][')'];
							if ($nbtemp > 0) {
								//on en a  dduire, soit $nbplus>0, ou sinon sur $nb
								if ($nbplus > 0) {
									// on soustrait aux deux
									while ($nbplus > 0 && $nbtemp > 0) {
										$nbplus--;
										$nbtemp--;
									}
								}

								// on enleve sur le surplus =0 et nbtemp>0
								if ($nbplus == 0 && $nbtemp > 0) {
									while ($nbtemp > 0 && $nb > 0) {
										$nbtemp--;
										$nb--;
									}
									//on met a jour
									$_SESSION['dims']['search']['listselectedword'][$i][')'] = $nbtemp;
								}
							}
							$i++;
						}
					}
					// on traite la partie droite
					if ($_SESSION['dims']['search']['listselectedword'][$wordid][')'] > 0) {
						// on compte le nbre  supprimer
						$nb = $_SESSION['dims']['search']['listselectedword'][$wordid][')'];

						//compteur de surprus
						$nbplus = 0;
						$i = $wordid - 1;

						while ($i >= 0 && $nb > 0) {
							// on ajout celles pouvant etre cres en plus
							$nbplus+=$_SESSION['dims']['search']['listselectedword'][$i][')'];
							$nbtemp = $_SESSION['dims']['search']['listselectedword'][$i]['('];
							if ($nbtemp > 0) {
								//on en a  dduire, soit $nbplus>0, ou sinon sur $nb
								if ($nbplus > 0) {
									// on soustrait aux deux
									while ($nbplus > 0 && $nbtemp > 0) {
										$nbplus--;
										$nbtemp--;
									}
								}

								// on enleve sur le surplus =0 et nbtemp>0
								if ($nbplus == 0 && $nbtemp > 0) {
									while ($nbtemp > 0 && $nb > 0) {
										$nbtemp--;
										$nb--;
									}
									//on met  jour
									$_SESSION['dims']['search']['listselectedword'][$i]['('] = $nbtemp;
								}
							}
							$i--;
						}
					}
					unset($_SESSION['dims']['search']['listselectedword'][$wordid]);
				}
				// redirection pour affichage des choix des mots
				dims_redirect("{$scriptenv}?dims_op=word_refreshselectedword");
			}
		case 'word_refreshselectedword':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				//echo getSearchExpression();
				if (isset($_SESSION['dims']['modsearch']['expression_brut'])) {
					echo $_SESSION['dims']['modsearch']['expression_brut'];
				} else {
					echo "";
				}
			}
			die();
			break;

		case 'word_addsearchcache':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && $_GET['word'] != '') {
				require_once(DIMS_APP_PATH . '/include/functions/system_index_cachesearch.php');
			}
			die();
			break;

		case 'word_presearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && $_GET['word'] != '') {
				require_once(DIMS_APP_PATH . '/include/functions/system_index_presearch.php');
			}
			die();
			break;
		case 'displaycontent':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['moduleid']) && isset($_GET['idobject']) && isset($_GET['idrecord'])) {
				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();
				echo $skin->open_simplebloc("");

				$block_moduleid = dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$modtype = $_SESSION['dims']['modules'][$block_moduleid]['moduletype'];
				$blockpath = DIMS_APP_PATH . "/modules/{$modtype}/block_portal.php";

				if (file_exists($blockpath)) {
					$dims_op = "content";
					require_once($blockpath);
				} else {
					echo "";
				}
				echo $skin->close_simplebloc();
			}
			die();
			break;
		case 'displaysearchresult':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['idmodule']) && isset($_GET['idobject']) && isset($_GET['idrecord'])) {
				// on a quelque chose  afficher
				$sql = "select distinct typecontent,parag,content,id_sentence from dims_keywords_usercache
					inner join dims_keywords_sentence on dims_keywords_sentence.id=dims_keywords_usercache.id_sentence
					AND id_module=:idmodule
					and id_object=:idobject
					and id_record=:idrecord
					order by typecontent,parag,count desc";
				$rs = $db->query($sql, array(
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, true, true)),
					':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, true, true)),
					':idrecord' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, true, true)),
				));

				$tabsentence = array();
				if ($db->numrows($rs) > 0) {
					require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
					$skin = new skin();
					echo $skin->open_simplebloc("");

					echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
					<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/></div>";
					echo "<div style=\"background-color:#FFFFFF;width:100%;float:left;overflow:auto;height:200px;\">";
					$paragcour = 1;
					$typecour = "";
					while ($f = $db->fetchrow($rs)) {
						$sentence = $f['content'];

						// on parcourt les sentences afin de les afficher
						foreach ($_SESSION['dims']['search']['listselectedword'] as $elem) {
							$tabword = explode(" ", $elem['word']);
							foreach ($tabword as $word)
								$sentence = str_replace($word, "<b>" . $word . "</b>", $sentence);
						}

						if ($typecour != $f['typecontent']) {
							$paragcour = $f['parag'];
							if ($typecour != "")
								echo "</p>";
							echo "<img src=\"./common/img/puce.png\" border=\"0\">&nbsp;" . $f['typecontent'];
							echo "<p style=\"text-align:justify; padding-top: 0px; margin-top: 0px;\">" . $sentence;
						}
						else {
							if ($paragcour != $f['parag']) {
								$paragcour = $f['parag'];
								echo "</p><p style=\"text-align:justify; padding-top: 0px; margin-top: 0px;\">" . $sentence;
							}
							else
								echo " " . $sentence;
						}
						$typecour = $f['typecontent'];
					}

					echo $skin->close_simplebloc();
				}
			}
			die();
			break;

		case 'tag_presearch':
			$word = strtoupper(dims_load_securvalue('word', dims_const::_DIMS_CHAR_INPUT, true, false, false));
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && $word != '') {
				// verification de l'appartenance du tag dans l'espace de travail courant
				$tagresult = array();
				$len = strlen($word);
				$rs = $db->query("
					SELECT DISTINCT dims_tag.*
					FROM dims_tag
					WHERE (type<2
					AND id_workspace=:idworkspace)
					OR type>=2", array(
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));

				if ($db->numrows($rs) > 0) {
					while ($fields = $db->fetchrow($rs)) {
						if ($fields['type'] == 3 && isset($_DIMS['cste'][$fields['tag']])) {
							$fields['tag'] = $_DIMS['cste'][$fields['tag']];
						}

						$res = similar_text($word, strtoupper(substr($fields['tag'], 0, $len)), $percent);
						//$pourcent= levenshtein($wordsearch , $fields['word']);
						if ($percent > 80) {
							$tagresult[$percent] = $fields;
						}
						krsort($tagresult);
					}
				}

				$c = 0;
				foreach ($tagresult as $res) {
					if ($c++)
						echo '|';
					echo $res['id'] . "," . $res['tag'];
				}
			}
			die();
			break;

		case 'tag_addsearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['tag'])) {
				require_once(DIMS_APP_PATH . "/include/functions/string.php");
				if (!isset($_SESSION['dims']['search'])) {
					$_SESSION['dims']['search'] = array();
					$_SESSION['dims']['search']['listselectedtag'] = array();
				}

				if (is_numeric($_GET['tag'])) {
					$rs = $db->query("select dims_tag.* from dims_tag where id = :idtag", array(
						':idtag' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, true, true)),
					));
				} else {
					$rs = $db->query("select dims_tag.* from dims_tag where ucase(tag) like :tag", array(
						':tag' => array('type' => PDO::PARAM_STR, 'value' => '%'.dims_load_securvalue('tag', dims_const::_DIMS_CHAR_INPUT, true, true, true).'%'),
					));
				}
				$key = "";
				if ($db->numrows($rs) > 0) {
					if ($f = $db->fetchrow($rs))
						$key = $f['id'];

					// verification de l'ajout du mot cle dans la recherche
					if ((!in_array($key, $_SESSION['dims']['search']['listselectedtag'])) && $key != "") {
						// on dfinit une nouvelle structure de mots
						if (isset($_DIMS['cste'][$f['tag']])) {
							$f['tag'] = $_DIMS['cste'][$f['tag']];
						}
						$_SESSION['dims']['search']['listselectedtag'][$key] = $f['tag'];

						if (!in_array($_SESSION['dims']['search']['lastlistselectedtag'][$f['id']])) {
							require_once DIMS_APP_PATH . '/modules/system/class_tag.php';
							$tag = new tag();
							$tag->open($f['id']);

							// traduction
							if (isset($_DIMS['cste'][$tag->fields['tag']])) {
								$tag->fields['tag'] = $_DIMS['cste'][$tag->fields['tag']];
							}

							// indice du tableau
							$siz=sizeof($_SESSION['dims']['search']['lastlistselectedtag']);
							if ($siz>0 && isset($_SESSION['dims']['search']['lastlistselectedtagindex'][$siz])) {
								if ($siz>5) {
									// on prend le dernier et on supprime
									$db->query("
										DELETE FROM dims_user_search_tag
										WHERE id_user=:iduser
										AND position>4",
										array(
											':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
										)
									);
								}
								// on update � position + 1 les autres
								$db->query('
									UPDATE dims_user_search_tag
									SET position=position+1
									WHERE id_user=:iduser',
									array(
										':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
									));

							}
							// on insert le nouveau � la position 1
							$db->query('
								INSERT INTO dims_user_search_tag
								SET position=1, id_user=:iduser, id_tag=:idtag',
								array(
									':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
									':idtag' => array('type' => PDO::PARAM_INT, 'value' => $f['id']),
							));


							$_SESSION['dims']['search']['lastlistselectedtag'][$f['id']] = $tag->fields['tag'];

							// on garde les 5 derniers
							$_SESSION['dims']['search']['lastlistselectedtag'] = array_slice($_SESSION['dims']['search']['lastlistselectedtag'], 0, 5, true);
						}
					}
				}
			}
			// redirection pour affichage des choix des mots
			dims_redirect("{$scriptenv}?dims_op=tag_refreshselectedtag");
			break;
		case 'tag_deleteselected':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['search']['listselectedtag']))
				unset($_SESSION['dims']['search']['listselectedtag']);
			// redirection pour affichage des choix des mots
			dims_redirect("{$scriptenv}?dims_op=tag_refreshselectedtag");
			break;
		case 'tag_deletesearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['tagid'])) {
				$id_tag = dims_load_securvalue('tagid', dims_const::_DIMS_NUM_INPUT, true, false, false);
				if (isset($_SESSION['dims']['search']['listselectedtag'][$id_tag])) {

					unset($_SESSION['dims']['search']['listselectedtag'][$id_tag]);
				}
				// redirection pour affichage des choix des mots
				dims_redirect("{$scriptenv}?dims_op=tag_refreshselectedtag");
			}
			break;
		case 'tag_refreshselectedtag':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])		echo getSearchTags();
			die();
			break;

		case 'searchnews':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				require_once(DIMS_APP_PATH . "/modules/system/desktop_search.php");
			}
			die();
			break;

		case 'searchfavorites':
			// security filter
			$idmodule = dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid']) && $idmodule > 0) {
				$mod = $dims->getModule($idmodule);
				if (($mod['id_module_type'] == 1 && dims_ismanager()) || $dims->isModuleEnabled($idmodule)) {
					// position courante de la recherche
					$pos = array_search($_GET['moduleid'], $_SESSION['dims']['search']['listmodules']);
					$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, false, false);
					if ($type > 0) {
						if (!isset($_SESSION['dims']['favorites']) || $type != $_SESSION['dims']['favorites'])
							$_SESSION['dims']['favorites'] = $type;
						$modtype = $mod['label'];
						$blockpath = DIMS_APP_PATH . "/modules/{$modtype}/block_portal.php";

						if (file_exists($blockpath)) {
							require_once($blockpath);
						} else {
							echo "";
						}
					}
				}
			}
			die();
			break;
		case 'searchunique':
			// security filter
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid']) && isset($_GET['moduleid'])) {
				$dims_op = "search";
				// on initialise

				unset($_SESSION['dims']['search']['listword']);
				unset($_SESSION['dims']['search']['lengthword']);
				unset($_SESSION['dims']['search']['sqlselectedword']);
				unset($_SESSION['dims']['search']['sqlselectedtag']);

				$_SESSION['dims']['search']['listword'] = "";
				$_SESSION['dims']['search']['lengthword'] = "";
				$_SESSION['dims']['search']['sqlselectedword'] = "";
				$_SESSION['dims']['search']['sqlselectedtag'] = "";

				$tabword = array();
				$tablength = array();
				// construction de la liste des cls de correspondances
				// traitement des recherches
				$position = 0;

				if (!empty($_SESSION['dims']['search']['listselectedword'])) {
					foreach ($_SESSION['dims']['search']['listselectedword'] as $id => $elem) {
						$_SESSION['dims']['search']['listselectedword'][$id]['present'] = 0;
						if ($_SESSION['dims']['search']['sqlselectedword'] == "")
							$_SESSION['dims']['search']['sqlselectedword'] = "'" . $elem['key'] . "'";
						else
							$_SESSION['dims']['search']['sqlselectedword'].=",'" . $elem['key'] . "'";

						$wlength = strlen($elem['word']);

						// construction de la restriction sur la longueur
						if (!in_array($wlength, $tablength)) {
							array_push($tablength, $wlength);
							if ($_SESSION['dims']['search']['lengthword'] != "")
								$_SESSION['dims']['search']['lengthword'].=",";
							$_SESSION['dims']['search']['lengthword'].=$wlength;
						}

						// construction de la liste des mots cles
						if (!isset($_SESSION['dims']['search']['listpositionword'][$elem['key']]))
							$_SESSION['dims']['search']['listpositionword'][$elem['key']] = array();

						array_push($_SESSION['dims']['search']['listpositionword'][$elem['key']], $position);
						$position++;
					}

					if ($_SESSION['dims']['search']['sqlselectedword'] != "")
						$_SESSION['dims']['search']['execute'] = true;
				}


				// manage tags selected list
				if (!empty($_SESSION['dims']['search']['listselectedtag'])) {
					foreach ($_SESSION['dims']['search']['listselectedtag'] as $key => $tag) {
						if ($_SESSION['dims']['search']['sqlselectedtag'] == "")
							$_SESSION['dims']['search']['sqlselectedtag'] = $key;
						else
							$_SESSION['dims']['search']['sqlselectedtag'].="," . $key;
					}

					if ($_SESSION['dims']['search']['sqlselectedtag'] != "")
						$_SESSION['dims']['search']['execute'] = true;
				}

				// verify access rule
				if ($_SESSION['dims']['search']['execute']) {
					$block_moduleid = dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
					$modtype = $_SESSION['dims']['modules'][$block_moduleid]['moduletype'];
					$blockpath = DIMS_APP_PATH . "/modules/{$modtype}/block_portal.php";

					if (file_exists($blockpath)) {
						require_once($blockpath);
					}
				} else {
					echo "Non trouve";
				}
			}
			die();
			break;
		case 'active_search':
			// security filter
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				if (!isset($_SESSION['dims']['active_search']))
					$_SESSION['dims']['active_search'] = array();

				if (isset($_GET['state'])) {
					$_SESSION['dims']['active_search'][$_SESSION['dims']['workspaceid']] = dims_load_securvalue('state', dims_const::_DIMS_NUM_INPUT, true, true, true);
				}
			}
			break;
		case 'search':
			// security filter
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {
				if (isset($_GET['campaignid']) && $_GET['campaignid'] > 0) {
					$_SESSION['dims']['search']['execute'] = true;
				} else {
					// position courante de la recherche
					$moduleid = dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, false);
					if ($moduleid == 0)
						$moduleid = $_SESSION['dims']['moduleid'];

					$pos = array_search($_GET['moduleid'], $_SESSION['dims']['search']['listmodules']);

					$_SESSION['dims']['search']['execute'] = false;
					if ($pos != FALSE) {
						// on initialise
						unset($_SESSION['dims']['search']['listword']);
						unset($_SESSION['dims']['search']['lengthword']);
						unset($_SESSION['dims']['search']['sqlselectedword']);
						unset($_SESSION['dims']['search']['sqlselectedtag']);

						$_SESSION['dims']['search']['listword'] = "";
						$_SESSION['dims']['search']['lengthword'] = "";
						$_SESSION['dims']['search']['sqlselectedword'] = "";
						$_SESSION['dims']['search']['sqlselectedtag'] = "";

						$tabword = array();
						$tablength = array();
						// construction de la liste des cls de correspondances
						// traitement des recherches
						$position = 0;

						if (!empty($_SESSION['dims']['search']['listselectedword'])) {
							foreach ($_SESSION['dims']['search']['listselectedword'] as $id => $elem) {
								$_SESSION['dims']['search']['listselectedword'][$id]['present'] = 0;
								if ($_SESSION['dims']['search']['sqlselectedword'] == "")
									$_SESSION['dims']['search']['sqlselectedword'] = "'" . $elem['key'] . "'";
								else
									$_SESSION['dims']['search']['sqlselectedword'].=",'" . $elem['key'] . "'";

								$wlength = strlen($elem['word']);

								// construction de la restriction sur la longueur
								if (!in_array($wlength, $tablength)) {
									array_push($tablength, $wlength);
									if ($_SESSION['dims']['search']['lengthword'] != "")
										$_SESSION['dims']['search']['lengthword'].=",";
									$_SESSION['dims']['search']['lengthword'].=$wlength;
								}

								// construction de la liste des mots cles
								if (!isset($_SESSION['dims']['search']['listpositionword'][$elem['key']]))
									$_SESSION['dims']['search']['listpositionword'][$elem['key']] = array();

								array_push($_SESSION['dims']['search']['listpositionword'][$elem['key']], $position);
								$position++;
							}

							if ($_SESSION['dims']['search']['sqlselectedword'] != "")
								$_SESSION['dims']['search']['execute'] = true;
						}
					}
					elseif (!empty($_SESSION['dims']['search']['listselectedword']))
						$_SESSION['dims']['search']['execute'] = true;

					// manage tags selected list
					if (!empty($_SESSION['dims']['search']['listselectedtag'])) {
						foreach ($_SESSION['dims']['search']['listselectedtag'] as $key => $tag) {
							if ($_SESSION['dims']['search']['sqlselectedtag'] == "")
								$_SESSION['dims']['search']['sqlselectedtag'] = $key;
							else
								$_SESSION['dims']['search']['sqlselectedtag'].="," . $key;
						}

						if ($_SESSION['dims']['search']['sqlselectedtag'] != "")
							$_SESSION['dims']['search']['execute'] = true;
					}

					// manage tags selected list
					if (!empty($_SESSION['dims']['search']['listselectedtag'])) {
						foreach ($_SESSION['dims']['search']['listselectedtag'] as $id => $tag) {
							if ($_SESSION['dims']['search']['sqlselectedtag'] == "")
								$_SESSION['dims']['search']['sqlselectedtag'] = "'" . $tag . "'";
							else
								$_SESSION['dims']['search']['sqlselectedtag'].=",'" . $tag . "'";
						}

						if ($_SESSION['dims']['search']['sqlselectedtag'] != "")
							$_SESSION['dims']['search']['execute'] = true;
					}
				}

				// verify access rule
				if ($_SESSION['dims']['search']['execute']) {
					$block_moduleid = $moduleid;
					$modtype = $_SESSION['dims']['modules'][$block_moduleid]['moduletype'];
					$blockpath = DIMS_APP_PATH . "/modules/{$modtype}/block_portal.php";

					// ajout du test sur le lancement au non de la recherche
					$workspaceid = $_SESSION['dims']['workspaceid'];
					$state = $_SESSION['dims']['modules'][$moduleid];
					if (file_exists($blockpath) && $state) {
						require_once($blockpath);
					}
				} else {
					echo "Non trouve";
				}
			}
			die();
			break;
		case 'executesearch':
			// security filter
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_SESSION['dims']['workspaceid'])) {

				require_once(DIMS_APP_PATH . '/include/functions/system_index_executesearch.php');

				require_once(DIMS_APP_PATH . "/include/functions/string.php");
				require_once DIMS_APP_PATH . '/include/class_campaign.php';
				require_once DIMS_APP_PATH . '/include/class_campaign_keyword.php';

				$campaign = new campaign();
				$query = getSearchExpression(false);

				if (isset($_SESSION['dims']['search']['id_campaign'])) {
					$campaign->open($_SESSION['dims']['search']['id_campaign']);
					$campaign->deleteCorresp();
				} else {
					// recherche si cette exite deja ou non, auquel cas on r�ouvre la campagne et on modifie le timestamp
					$res = $db->query('
						SELECT id,query
						FROM dims_campaign
						WHERE query like :query',
						array(
							':query' => array('type' => PDO::PARAM_STR, 'value' => $query),
						));
					if ($db->numrows($res) > 0) {
						while ($f = $db->fetchrow($res)) {
							$campaign->open($f['id']);
							$campaign->deleteCorresp();
						}
					}
					else
						$campaign->init_description();
				}

				$campaign->setvalues($_POST, "campaign_");
				$campaign->fields['id_user'] = $_SESSION['dims']['userid'];
				$campaign->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$campaign->fields['temporary'] = 1;
				$campaign->fields['query'] = $query;
				$campaign->save();

				$_SESSION['dims']['search']['id_campaign'] = $campaign->fields['id'];
				// on enrgistre la composition de la campagne
				foreach ($_SESSION['dims']['search']['listselectedword'] as $pos => $elem) {
					$campaign_word = new campaign_keyword();
					$campaign_word->init_description();
					$campaign_word->fields['id_campaign'] = $campaign->fields['id'];
					$campaign_word->fields['position'] = $pos;
					$campaign_word->fields['('] = $elem['('];
					$campaign_word->fields[')'] = $elem[')'];
					$campaign_word->fields['op'] = $elem['op'];
					$campaign_word->fields['word'] = $elem['word'];
					$campaign_word->fields['key'] = $elem['key'];
					$campaign_word->fields['state'] = 0;
					$campaign_word->save();
				}
			}
			die();
			break;
		case 'refresh_blockcontent':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['moduleid'])) {
				// verify access rule
				$block_moduleid = dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
				$modtype = $_SESSION['dims']['modules'][$block_moduleid]['moduletype'];
				$blockpath = DIMS_APP_PATH . "/modules/{$modtype}/block-portal.php";

				if (file_exists($blockpath)) {
					require_once($blockpath);
				}
			}
			die();
			break;
		case 'updatestate':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && isset($_GET['module']))
				require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');
			$res = $db->query('
				UPDATE dims_param_block_user
				SET state=:state
				WHERE id_user=:iduser
				AND id_workspace=:idworkspace
				AND id_module=:idmodule',
				array(
					':state' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('state', dims_const::_DIMS_NUM_INPUT, true, true, true)),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => dims_load_securvalue('module', dims_const::_DIMS_NUM_INPUT, true, true, true)),
				));
			die();
			break;
		case 'refresh_blockportal':
			require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');

			// recuperation des etats avant suppression
			$res = $db->query("
				SELECT *
				FROM dims_param_block_user
				WHERE id_user=:iduser
				AND id_workspace=:idworkspace",
				array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));
			$tabstate = array();

			while ($fields = $db->fetchrow($res)) {
				$tabstate[$fields['id_module']] = $fields['state'];
			}

			$res = $db->query("
				DELETE FROM dims_param_block_user
				WHERE id_user=:iduser
				AND id_workspace=:idworkspace",
				array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));
			$buser = new block_user();

			$currentcol = 0;

			$block = dims_load_securvalue($_GET, dims_const::_DIMS_CHAR_INPUT, true, true, true);
			foreach ($_GET as $key => $value) {
				if (substr($key, 0, 3) == "col") {
					$idcol = substr($key, 3);
					$position = 1;
					$tabmod = split(',', trim($value));

					foreach ($tabmod as $mod) {
						$buser->fields['id_user'] = $_SESSION['dims']['userid'];
						$buser->fields['id_module'] = $mod;
						$buser->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$buser->fields['id_column'] = $idcol - 1;
						$buser->fields['position'] = $position;
						if (isset($tabstate[$mod]))
							$buser->fields['state'] = $tabstate[$mod];
						$buser->save();
						$buser->new = true;
						$position++;
					}
				}
			}
			die();
			break;
		case 'dims_switchdisplay':
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$_SESSION['dims']['switchdisplay'][$id] = $id;
			break;

		case 'dims_checkpasswordvalidity':
					$password = dims_load_securvalue('password', dims_const::_DIMS_CHAR_INPUT, true, true, false);

			if (_DIMS_USE_COMPLEXE_PASSWORD) {
				echo dims_checkpasswordvalidity($password);
			} else {
				echo true;
			}
			die();
			break;

		case 'workflow_select_user':

			$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, true, false);
			$id_object = dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, false);
			// gestion des users
			if (isset($_GET['user_id']))
				$_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action][dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
			if (isset($_GET['remove_user_id']))
				unset($_SESSION['dims']['workflow']['users_selected'][$id_object][$id_action][dims_load_securvalue('remove_user_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);
			// gestion des groupes
			if (isset($_GET['group_id']))
				$_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action][dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
			if (isset($_GET['remove_group_id']))
				unset($_SESSION['dims']['workflow']['groups_selected'][$id_object][$id_action][dims_load_securvalue('remove_group_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);

			$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, true, false);
			$id_object = dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, false);

			require_once DIMS_APP_PATH . '/include/functions/workflow.php';
			dims_workflow_getSelectedUsers($id_object, -1, -1, $id_action);
			die();
			break;

		case 'workflow_search_users':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$workspace = new workspace();
				$workspace->open($_SESSION['dims']['workspaceid']);
				$nomsearch = dims_load_securvalue('dims_workflow_userfilter', dims_const::_DIMS_CHAR_INPUT, true, true, false);
				$id_action = dims_load_securvalue('id_action', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_object = dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, false);

				require_once(DIMS_APP_PATH . "/include/functions/workflow.php");

				if (empty($nomsearch))
					$lstresult = array();
				else{
					$lstresult = $workspace->getGroupsUsers($nomsearch, true);
					if (count($lstresult['users']) <= 0)
						echo $_DIMS['cste']['_DIMS_LABEL_NO_RESP_CONT_SEARCH'];
					else
						dims_workflow_display($lstresult, $id_object, $id_action);
				}
				//dims_print_r($lstresult);
				// on regarde si on a des resultats
			}
			die();
			break;

		case 'shares_select_user':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				// gestion des users
				if (isset($_GET['user_id']))
					$_SESSION['dims']['shares']['users_selected'][dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
				if (isset($_GET['remove_user_id']))
					unset($_SESSION['dims']['shares']['users_selected'][dims_load_securvalue('remove_user_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);
				// gestion des groupes
				if (isset($_GET['group_id']))
					$_SESSION['dims']['shares']['groups_selected'][dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
				if (isset($_GET['remove_group_id']))
					unset($_SESSION['dims']['shares']['groups_selected'][dims_load_securvalue('remove_group_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);
				require_once DIMS_APP_PATH . '/include/functions/shares.php';
				dims_shares_getSelectedUsers();
			}
			die();
			break;
		case 'shares_viewmodule':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, true, false);
				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				require_once(DIMS_APP_PATH . '/modules/system/class_domain.php');
				dims_init_module('system');
				require_once(DIMS_APP_PATH . '/modules/system/include/functions.php');

				$skin = new skin();
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ACCES']);

				echo "<div style=\"height:400px;width:100%\">";
				if ($id_record > 0 && $id_module > 0 && $id_object > 0 && isModuleEnabled($id_module)) {
					require_once DIMS_APP_PATH . '/include/functions/shares.php';
					// verification des droits de l'utilisateur sur le module
					dims_shares_selectusers($id_object, $id_record, $id_module);
				}
				echo "</div>";
				echo "<div style=\"width:100%;tewt-align:center;\">
					<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/>
					<input type=\"button\" onclick=\"saveSharesModules(" . $id_object . "," . $id_record . "," . $id_module . ");\" value=\"Valider\" class=\"flatbutton\"/>
				</div>";
			}
			die();
			break;
		case "shares_savemodule":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, true, false);
				if ($id_record > 0 && $id_module > 0 && $id_object > 0 && isModuleEnabled($id_module)) {
					require_once DIMS_APP_PATH . '/include/functions/shares.php';
					dims_shares_save($id_object, $id_record, $id_module);

					// on vrifie le mode de vue lors du partage
					$module = new module();
					$module->open($id_module);

					$module->verifyShares();

					// redirection pour affichage des choix des mots
					dims_redirect("{$scriptenv}?dims_op=shares_refreshmodule&idmodule=" . $id_module);
				}
			}
			die();
			break;

		case "shares_refreshmodule":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, true, false);
				if ($id_module > 0 ){ //&& isModuleEnabled($id_module)) {
					require_once(DIMS_APP_PATH . '/modules/system/include/global.php');
					require_once(DIMS_APP_PATH . '/modules/system/class_module.php');
					require_once DIMS_APP_PATH . '/include/functions/shares.php';

					// on recalcule les accs pour ce module
					$lstrules = dims_shares_get(0, dims_const::_SYSTEM_OBJECT_GROUP, $id_module, dims_const::_DIMS_MODULE_SYSTEM,0);

					if (sizeof($lstrules) > 0)
						$rules = sizeof($lstrules);
					else
						$rules=$_DIMS['cste']['_DIMS_LABEL_UNDEFINED'];

					echo $rules;

					echo "||";

					$module = new module();
					$module->open($id_module);
					// verification des droits sur le partage des modules
					$module->verifyShares();
					// View modes for modules
					$dims_viewmodes = array(dims_const::_DIMS_VIEWMODE_UNDEFINED => $_DIMS['cste']['_DIMS_LABEL_UNDEFINED'],
						dims_const::_DIMS_VIEWMODE_PRIVATE => $_DIMS['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE'],
						dims_const::_DIMS_VIEWMODE_DESC => $_DIMS['cste']['_LABEL_VIEWMODE_DESC'],
						dims_const::_DIMS_VIEWMODE_ASC => $_DIMS['cste']['_LABEL_VIEWMODE_ASC'],
						dims_const::_DIMS_VIEWMODE_GLOBAL => $_DIMS['cste']['_LABEL_VIEWMODE_GLOBAL']
					);

					// on recalcule la vue sur l'information
					echo $dims_viewmodes[$module->fields['viewmode']];
				}
			}
			die();
			break;
		case 'shareobject_delete':
		case 'shareobject_view':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . "/modules/system/admin_index_workspace_shareobject.php");

				/* echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
				  <input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/>
				  <input type=\"submit\"  value=\"Valider\" class=\"flatbutton\"/></div></form>"; */
				//echo $skin->close_simplebloc();
			}
			die();
			break;

		case 'shares_search_users':

		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
			$workspace = new workspace();
			$workspace->open($_SESSION['dims']['workspaceid']);
			$nomsearch=dims_load_securvalue('dims_shares_userfilter',dims_const::_DIMS_CHAR_INPUT,true,true,false);

			if (isset($_SESSION['dims']['sharemodule']['idrecord'])) {
				$lstresult=$workspace->getGroupsUsers($nomsearch,false,-1,$_SESSION['dims']['sharemodule']['idrecord']);
			}
			else {
				$lstresult=$workspace->getGroupsUsers($nomsearch,true);
			}
			//dims_print_r($lstresult);
			// on regarde si on a des resultats
			require_once DIMS_APP_PATH . '/include/functions/shares.php';
			dims_share_display($lstresult);
		}
		die();
		break;

		case 'templates_view':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_workspace = dims_load_securvalue('idworkspace', dims_const::_DIMS_NUM_INPUT, true, false, false);

				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				require_once(DIMS_APP_PATH . '/modules/system/class_domain.php');
				dims_init_module('system');
				require_once(DIMS_APP_PATH . '/modules/system/include/functions.php');

				$work = new workspace();
				$work->open($id_workspace);

				$skin = new skin();
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_TEMPLATEWORKSPACE_LIST'] . "<b>" . $work->fields['label'] . "</b>");

				echo "<form action=\"\" method=\"post\">";
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("tplworkspace", "1");
				$token->field("seltpl");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
				echo "	<input type=\"hidden\" name=\"tplworkspace\" value=\"1\">
						<div style=\"height:400px;overflow:auto;\">";

				// collecte la liste des templates disponibles
				$availabletpl = dims_getavailabletemplates();

				$lsttplcurrent = array();

				$sql = 'select * from dims_workspace_template where id_workspace=:idworkspace';
				$res = $db->query($sql, array(
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
				));

				while ($f = $db->fetchrow($res)) {
					if (in_array($f['template'], $availabletpl)) {
						$lsttplcurrent[] = $f['template'];
					}
				}

				// on affiche maintenant la liste de tous les templates et on coche ceux s�lectionnes
				echo "<table style=\"width:100%;\">";
				$c = 0;
				foreach ($availabletpl as $tpl) {
					if (in_array($tpl, $lsttplcurrent))
						$check = "checked";
					else
						$check="";
					if ($c % 2 == 0)
						$st = "trl1";
					else
						$st="trl2";

					echo "<tr class=\"$st\"><td><input type=\"checkbox\" name=\"seltpl[]\" $check value=\"" . $tpl . "\">&nbsp;" . $tpl . "</td></tr>";
					$c++;
				}
				echo "</table>";

				echo "</div>";

				echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
				<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/>
				<input type=\"submit\"	value=\"Valider\" class=\"flatbutton\"/></div></form>";
				echo $skin->close_simplebloc();
			}
			die();
			break;
		case 'domains_viewdomain':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_domain = dims_load_securvalue('iddomain', dims_const::_DIMS_NUM_INPUT, true, false, false);
				$typeaccess = dims_load_securvalue('typeaccess', dims_const::_DIMS_NUM_INPUT, true, false, false);

				// FIXME : We MAY store a template_path var without "common" in path or class_skin.php SHOULD be include from core.
				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				require_once(DIMS_APP_PATH . '/modules/system/class_domain.php');
				dims_init_module('system');
				require_once(DIMS_APP_PATH . '/modules/system/include/functions.php');

				$domain = new domain();
				$domain->open($id_domain);

				$skin = new skin();
				if ($typeaccess == 0)
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_BACKOFFICE_DOMAIN_LIST'] . "<b>" . $domain->fields['domain'] . "</b>");
				else
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_FRONTOFFICE_DOMAIN_LIST'] . "<b>" . $domain->fields['domain'] . "</b>");

				echo "<form action=\"\" method=\"post\"><input type=\"hidden\" name=\"savedomainworkspace\">";
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("typeaccess",	$typeaccess);
				$token->field("iddomain",	$domain->fields['id']);
				$token->field("savedomainworkspace");
				$token->field("selwork");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
				echo "	<input type=\"hidden\" name=\"typeaccess\" value=\"$typeaccess\">
						<input type=\"hidden\" name=\"iddomain\" value=\"" . $domain->fields['id'] . "\">
						<div style=\"height:300px;overflow:auto;background-color:#FFFFFF;\">";

				$workspaces = system_getworkspaces();
				$selectedworkspaces = array();

				$sql = "select * from dims_workspace_domain where id_domain=:iddomain and (access=:typeaccess or access=2)";

				$selectedworkspaces = array();

				$res = $db->query($sql, array(
					':iddomain' => array('type' => PDO::PARAM_INT, 'value' => $id_domain),
					':typeaccess' => array('type' => PDO::PARAM_INT, 'value' => $typeaccess),
				));

				while ($f = $db->fetchrow($res)) {
					$selectedworkspaces[] = $f['id_workspace'];
				}

				echo system_build_tree_domain($workspaces, $selectedworkspaces);
				echo "</div>";

				echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
				<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/>
				<input type=\"submit\"	value=\"Valider\" class=\"flatbutton\"/></div></form>";
				echo $skin->close_simplebloc();
			}
			die();
			break;

		case "shares_savemodule":
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$id_record = dims_load_securvalue('idrecord', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_module = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, true, true, false);
				$id_object = dims_load_securvalue('idobject', dims_const::_DIMS_NUM_INPUT, true, true, false);
				if ($id_record > 0 && $id_module > 0 && $id_object > 0 && isModuleEnabled($id_module)) {
					require_once DIMS_APP_PATH . '/include/functions/shares.php';
					dims_shares_save($id_object, $id_record, $id_module);

					// on vrifie le mode de vue lors du partage
					$module = new module();
					$module->open($moduleid);

					$module->verifyShares();

					// redirection pour affichage des choix des mots
					dims_redirect("{$scriptenv}?dims_op=shares_refreshmodule&idmodule=" . $id_module);
				}
			}
			die();
			break;



		case 'tags_annotationsearch':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				require_once(DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php");
				$skin = new skin();
				echo $skin->open_widgetbloc("", 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', "./common/img/close.png", '26px', '26px', '-10px', '-2px', 'javascript:dims_hidepopup(\'dims_popup\');', '', '');
				echo "<div style=\"overflow:auto;position:relative;\">";

				if (isset($_GET['id_tag'])) {
					require_once DIMS_APP_PATH . '/include/global.php';
					require_once DIMS_APP_PATH . '/modules/system/class_tag.php';

					$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true, true);

					$tag = new tag();
					$tag->open($id_tag);
?>
				<div style="padding:4px;">Le tag <b><?= $tag->fields['tag']; ?></b> a aussi ete utilise sur les annotations suivantes :</div>
				<div class="dims_annotation_popup_list">
<?
					$select = "
								SELECT		a.*,
											o.script,
											o.label as object_name,
											m.label as module_name

								FROM		dims_annotation a

								INNER JOIN	dims_annotation_tag at
								ON			at.id_annotation = a.id
								AND			at.id_tag = :idtag

								INNER JOIN	dims_module m
								ON			a.id_module = m.id

								LEFT JOIN	dims_mb_object o ON o.id = a.id_object AND o.id_module_type = m.id_module_type

								ORDER BY	a.date_annotation DESC
								";

					$rs = $db->query($select, array(
						':idtag' => array('type' => PDO::PARAM_INT, 'value' => $id_tag),
					));

					$mods = $dims->getModules($_SESSION['dims']['workspaceid']);

					while ($fields = $db->fetchrow($rs)) {
						$ld = dims_timestamp2local($fields['date_annotation']);
?>
					<div class="dims_annotations_row_<?= $numrow = (!isset($numrow) || $numrow == 2) ? 1 : 2; ?>" style="padding:4px;">
						<div style="float:right;"><?= "le {$ld['date']}  {$ld['time']}"; ?></div>
							<div style="font-weight:bold;"><?= "{$fields['title']}"; ?></div>
							<div style="clear:both;padding-top:4px;"><?= dims_make_links(dims_nl2br($fields['content'])); ?></div>
<?
						if ($fields['id_record'] != '') {
							$object_script = str_replace('<IDRECORD>', $fields['id_record'], $fields['script']);
							$object_script = str_replace('<IDMODULE>', $fields['id_module'], $object_script);
							$extimg = $dims->getImageByObject($fields['id_module_type'], $fields['id_object']);

							// get name object
							$modtype = $mods[$fields['id_module_type']]['label'];
							$dims_op = 'title';
							$idobject = $fields['id_object'];
							$idrecord = $fields['id_record'];
							$_GET['moduleid'] = $fields['id_module'];
							$blockpath = DIMS_APP_PATH . "/modules/" . $modtype . "/block_portal.php";
							if (file_exists($blockpath)) {
								include($blockpath);
								$fields['object_label'] = $label;
							}
?>
									<div style="clear:both;padding-top:4px;text-align:right;"><a href="<?= "admin.php?dims_mainmenu=1&{$object_script}"; ?>"><?= "<img src=\"" . $_SESSION['dims']['template_path'] . "./common/img/system/link.png\">&nbsp;{$fields['module_name']} / {$fields['object_name']} / {$fields['object_label']} <img src='" . $extimg . "'>"; ?></a></div>
<?
						}
?>
							</div>
<?
					}
?>
					</div>
<?
				}
				else
					echo "erreur";

				echo "</div>";

				echo $skin->close_simplebloc();
			}
			die();
			break;

		case 'tags_search':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$tag = dims_load_securvalue('tag', dims_const::_DIMS_CHAR_INPUT, true, true, false);
				if ($tag != '') {
					$select = "
					SELECT	t.id,
						t.tag,
						COUNT(*) AS c
					FROM	dims_tag t

					INNER JOIN	dims_annotation_tag at
					ON			at.id_tag = t.id

					INNER JOIN	dims_annotation a
					ON			a.id = at.id_annotation
					AND			a.id_workspace = :idworkspace

					WHERE	t.tag LIKE :tag

					GROUP BY t.id
					ORDER BY c DESC
				";

					$rs = $db->query($select, array(
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						':tag' => array('type' => PDO::PARAM_STR, 'value' => $tag.'%'),
					));
					$c = 0;

					while ($fields = $db->fetchrow($rs)) {
						if ($c++)
							echo '|';
						echo "{$fields['tag']};{$fields['c']}";
					}
				}
			}
			break;

		case 'annotation_delete':
			require_once DIMS_APP_PATH . '/modules/system/class_annotation.php';
			$dims_annotation_id = dims_load_securvalue("dims_annotation_id", dims_const::_DIMS_NUM_INPUT, true, true);

			if (is_numeric($dims_annotation_id)) {
				$annotation = new annotation();
				if ($annotation->open($dims_annotation_id)) {
					if ($annotation->fields['id_user'] == $_SESSION['dims']['userid']) {
						$annotation->delete();
					}
				}
			}
			dims_redirect('/admin.php');
			break;

		case 'annotation_save':
			require_once DIMS_APP_PATH . '/modules/system/class_annotation.php';
			require_once DIMS_APP_PATH . '/include/functions/tickets.php';
			$annotation = new annotation();
			$annotation->setvalues($_POST, 'dims_annotation_');
			if (isset($_POST['dims_annotationtags']))
				$annotation->tags = dims_load_securvalue('dims_annotationtags', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if (!isset($_POST['dims_annotation_private']))
				$annotation->fields['private'] = 0;

			$annotation->fields['date_annotation'] = dims_createtimestamp();

			$annotation->save();

			dims_tickets_send($annotation->fields['id_object'], $annotation->fields['id_record'], $annotation->fields['object_label'], $annotation->fields['title'], $annotation->fields['content']);
			dims_redirect('/admin.php');
			break;

		case 'annotation_show':
			if (isset($_GET['object_id'])) {
				$idobject = dims_load_securvalue('object_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
				if (isset($_SESSION['dims']['annotations']['show'][$idobject])) {
					unset($_SESSION['dims']['annotations']['show'][$idobject]);
				} else {
					$_SESSION['dims']['annotations']['show'][$idobject] = 1;
				}
			}
			break;

		// TICKETS CASES
		case 'tickets_send':
			require_once DIMS_APP_PATH . '/include/functions/tickets.php';
			$title = dims_load_securvalue('ticket_title', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$message = dims_load_securvalue('ticket_message', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$needvalidation = (int)dims_load_securvalue('ticket_needed_validation', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$delivrynotification = (int)dims_load_securvalue('ticket_delivery_notification', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$idobject = dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$idrecord = dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$objectlabel = dims_load_securvalue('object_label', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			dims_tickets_send($title, $message, $needvalidation, $delivrynotification, $idobject, $idrecord, $objectlabel);
			break;

		case 'tickets_new':
			require_once DIMS_APP_PATH . '/include/functions/tickets.php';
			require_once(DIMS_APP_PATH . '/include/op_tickets_new.php');
					break;

				case 'tickets_search_users':
					require_once(DIMS_APP_PATH . '/include/op_tickets_search_users.php');
					die();
					break;

				case 'tickets_select_user':
					require_once(DIMS_APP_PATH . '/include/op_tickets_select_user.php');
					break;

				// FAVORITES CASES
				case 'favorites_save':
					require_once DIMS_APP_PATH . '/modules/system/class_favorite.php';
					require_once DIMS_APP_PATH . '/modules/system/class_favorite_heading.php';

					$favorite = new favorite();
					$favorite->setvalues($_GET, 'favorite_');

					if (isset($fav_new_heading) && $fav_new_heading != '') {
						$favorite_heading = new favorite_heading();
						$favorite_heading->open($favorite_id_heading);
						$child = $favorite_heading->createchild($fav_new_heading);
						$favorite->fields['id_heading'] = $child->save();
					}
					$favorite->setugm();
					$favorite->save();
					//dims_print_r($favorite);
?>
					<table style="padding:2px">
						<tr>
							<td><img src="./common/img/loading.gif"></td>
							<td>Enregistrement en cours</td>
						</tr>
					</table>
<?
					die();
					break;

				case 'favorites_addto':
					dims_init_module('system');
					$headings = system_favorite_getheadings();
					if (!isset($label))
						$label = '';
					?>
					<form action="" onsubmit="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=favorites_save&favorite_id_object=<?= $id_object; ?>&favorite_id_record=<?= $id_record; ?>&favorite_label='+this.favorite_label.value+'&favorite_id_heading='+this.favorite_id_heading.value+'&fav_new_heading='+this.fav_new_heading.value,'','dims_popup');setTimeout('dims_hidepopup()', 1500);return(false);">
						<?
							// Sécurisation du formulaire par token
							require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
							$token = new FormToken\TokenField;
							$token->field("favorite_label");
							$token->field("favorite_id_heading");
							$token->field("fav_new_heading");
							$tokenHTML = $token->generate();
							echo $tokenHTML;
						?>
						<table style="padding:0px;padding-bottom:10px;width:100%;" cellspacing="0">
							<tr>
								<td style="padding:2px; font-weight:bold;"><strong>Ajout aux favoris</strong></td>
								<td style="padding:2px;text-align:right;"><a href="javascript:dims_hidepopup();">Fermer</a></td>
							</tr>
						</table>

						<table style="padding:0px" cellspacing="0">
							<tr>
								<td style="padding:2px;">Intitul:</td>
							</tr>
							<tr>
								<td style="padding:2px;text-align:left;"><input name="favorite_label" type="text" style="width:195px" class="text" value="<?= $label; ?>"></td>
							</tr>
							<tr>
								<td style="padding:2px;">Rubrique:</td>
							</tr>
							<tr>
								<td style="padding:2px;text-align:left;">
									<select name="favorite_id_heading" style="width:195px" class="select"><?= system_favorite_build_select($headings); ?></select>
								</td>
							</tr>
							<tr>
								<td style="padding:2px;">Sous-Rubrique:</td>
							</tr>
							<tr>
								<td style="padding:2px;text-align:left;"><input name="fav_new_heading" type="text" style="width:195px" class="text"></td>
							</tr>
						</table>

						<table style="padding:0px;width:100%;" cellspacing="0">
							<tr>
								<td align="right">
									<table style="padding:0px;padding-top:4px;" cellspacing="0">
										<tr>
											<td style="padding:2px;text-align:right"><input class="button" type="submit" value="<?= $_DIMS['cste']['_DIMS_SAVE']; ?>"></td>
											<td style="padding:2px;text-align:right"><input class="button" type="button" onclick="javascript:dims_hidepopup()" value="<?= $_DIMS['cste']['_DIMS_LABEL_CANCEL']; ?>"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</form>
					<?
					die();
					break;

				case 'colorpicker_open':
					?>
					<div style="overflow:hidden;padding:2px;background-color:#ffffff;z-index:1;">
						<div style="margin-bottom:2px;overflow:hidden;padding-left:0px !important;">
							<div style="float:left;position:relative;width:35px;height:200px;z-index:3;">
								<img style="display:block;position:absolute;cursor:pointer;z-index:5;" src="./common/img/colorpicker/h.png" id="colorpicker_h">
								<img style="display:block;position:absolute;cursor:pointer;z-index:10;" src="./common/img/colorpicker/position.png" id="colorpicker_position">
							</div>
							<div style="float:left;position:relative;width:200px;height:200px;margin-left:2px;z-index:3;">
								<img style="display:block;position:absolute;cursor:pointer;z-index:5;" src="./common/img/colorpicker/sv.png" id="colorpicker_sv">
								<img style="display:block;position:absolute;cursor:pointer;z-index:10;" src="./common/img/colorpicker/crosshairs.png" id="colorpicker_crosshairs">
							</div>
						</div>
						<div style="clear:both;width:237px;height:37px;z-index:5;padding-left:0px !important;" id="colorpicker_selectedcolor">
							<input type="button" class="button" style="margin:6px;float:right;" value="<?= $_SESSION['cste']['_DIMS_CLOSE']; ?>" onclick="javascript:dims_getelem('<?= dims_load_securvalue('inputfield_id', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>').value = dims_getelem('colorpicker_inputcolor').value;$('#<?= dims_load_securvalue('inputfield_id', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>').change(); dims_hidepopup();">
							<input type="text" class="text" id="colorpicker_inputcolor" style="margin:6px;width:65px;float:left;" value="<?= dims_load_securvalue('colorpicker_value', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>">
						</div>
					</div>
					<?
					die();
					break;

				case 'calendar_open':
					require_once(DIMS_APP_PATH . '/include/op_calendar_open.php');
					break;

				/*				 * ******************** */
				/** DOCUMENTS_BROWSER * */
				/*				 * ******************** */

				case 'documents_browser':
				require_once(DIMS_APP_PATH . '/include/op_documents_browser.php');
					break;

				case 'documents_downloadfile':
					if (!empty($_GET['documentsfile_id'])) {
						require_once(DIMS_APP_PATH . '/include/class_documentsfile.php');

						$documentsfile = new documentsfile();
						$documentsfile->open(dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

						if (file_exists($documentsfile->getfilepath()))
							dims_downloadfile($documentsfile->getfilepath(), $documentsfile->fields['name']);
					}
					die();
					break;

				case 'documents_downloadfile_zip':

					$zip_path = dims_documents_getpath() . _DIMS_SEP . 'zip';
					if (!is_dir($zip_path)) {
						mkdir($zip_path);
					}

					if (!empty($_GET['documentsfile_id'])) {
						require_once DIMS_APP_PATH . '/lib/pclzip-2-5/pclzip.lib.php';
						require_once(DIMS_APP_PATH . '/include/class_documentsfile.php');

						$documentsfile = new documentsfile();
						$documentsfile->open(dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

						if (file_exists($documentsfile->getfilepath()) && is_writeable($zip_path)) {
							// create a temporary file with the real name
							$tmpfilename = $zip_path . _DIMS_SEP . $documentsfile->fields['name'];

							copy($documentsfile->getfilepath(), $tmpfilename);

							// create zip file
							$zip_filename = "archive_".dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true)."zip";
							echo $zip_filepath = $zip_path . _DIMS_SEP . $zip_filename;
							$zip = new PclZip($zip_filepath);
							$zip->create($tmpfilename, PCLZIP_OPT_REMOVE_ALL_PATH);

							// delete temporary file
							unlink($tmpfilename);

							// download zip file
							dims_downloadfile($zip_filepath, $zip_filename, true);
						}
					}

					die();
					break;

				case 'documents_savefolder':
					require_once(DIMS_APP_PATH . '/include/class_documentsfolder.php');
					$documentsfolder = new documentsfolder();

					if (!empty($_POST['documentsfolder_id'])) {
						$documentsfolder->open(dims_load_securvalue('documentsfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
						$documentsfolder->setvalues($_POST, 'documentsfolder_');
						$documentsfolder->save();
					} else {// new folder {
						$documentsfolder->setvalues($_POST, 'documentsfolder_');
						$documentsfolder->fields['id_folder'] = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);
						$documentsfolder->fields['id_object'] = $_SESSION['documents']['id_object'];
						$documentsfolder->fields['id_record'] = $_SESSION['documents']['id_record'];
						$documentsfolder->fields['id_module'] = $_SESSION['documents']['id_module'];
						$documentsfolder->fields['id_user'] = $_SESSION['documents']['id_user'];
						$documentsfolder->fields['id_workspace'] = $_SESSION['documents']['id_workspace'];
						$documentsfolder->save();
					}
					?>
					<script type="text/javascript">
						window.parent.dims_documents_browser('<?= dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>', '<?= $_SESSION['documents']['documents_id']; ?>')
						window.parent.dims_hidepopup();
					</script>
					<?
					die();
					break;

				case 'documents_openfolder':
					require_once(DIMS_APP_PATH . '/include/class_documentsfolder.php');
					require_once(DIMS_APP_PATH . '/include/op_documents_openfolder.php');
					die();
					break;

				case 'documents_savefile':
					require_once(DIMS_APP_PATH . '/include/class_documentsfile.php');
					$documentsfile = new documentsfile();

					if (!empty($_POST['documentsfile_id']))
						$documentsfile->open(dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
					else {
						$documentsfile->fields['id_object'] = $_SESSION['documents']['id_object'];
						$documentsfile->fields['id_record'] = $_SESSION['documents']['id_record'];
						$documentsfile->fields['id_module'] = $_SESSION['documents']['id_module'];
						$documentsfile->fields['id_user'] = $_SESSION['documents']['id_user'];
						$documentsfile->fields['id_workspace'] = $_SESSION['documents']['id_workspace'];
					}

					$documentsfile->setvalues($_POST, 'documentsfile_');
					$documentsfile->fields['id_folder'] = dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true);

					if (!empty($_FILES['documentsfile_file']['name'])) {
						$documentsfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
						$documentsfile->tmpfile = $_FILES['documentsfile_file']['tmp_name'];
						$documentsfile->fields['name'] = $_FILES['documentsfile_file']['name'];
						$documentsfile->fields['size'] = $_FILES['documentsfile_file']['size'];
					}

					$error = $documentsfile->save();
		?>
					<script type="text/javascript">
						window.parent.dims_documents_browser('<?= dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>', '<?= $_SESSION['documents']['documents_id']; ?>')
						window.parent.dims_hidepopup();
					</script>
		<?
					die();
					break;

				case 'documents_openfile':
					require_once(DIMS_APP_PATH . '/include/class_documentsfile.php');
					require_once(DIMS_APP_PATH . '/include/op_documents_openfile.php');
					die();
					break;

				case 'documents_deletefile':
					if (!empty($_GET['documentsfile_id'])) {
						require_once(DIMS_APP_PATH . '/include/class_documentsfile.php');

						$documentsfile = new documentsfile();
						$documentsfile->open(dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

						$documentsfile->delete();
					}

					dims_redirect("{$scriptenv}?dims_op=documents_browser&currentfolder=".dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true));
					break;

				case 'documents_deletefolder':
					if (!empty($_GET['documentsfolder_id'])) {
						require_once(DIMS_APP_PATH . '/include/class_documentsfolder.php');

						$documentsfolder = new documentsfolder();
						$documentsfolder->open(dims_load_securvalue('documentsfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

						$documentsfolder->delete();
					}
					dims_redirect("{$scriptenv}?dims_op=documents_browser&currentfolder=".dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true));
					break;

				/** CLIPBOARD * */
				case 'clipboard_add':
					//ob_start();
					if (isset($_GET['paste']))
						$_SESSION['dims']['clipboard'][] = dims_load_securvalue('paste', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					//dims_show_clipboard_content();
					//$reponse = ob_get_contents();
					//ob_end_clean();
					//echo $reponse;
					die();
					break;
				case 'clipboard_pasteall':
					//ob_start();
					echo dims_get_all_clipboard_content();
					//$reponse = ob_get_contents();
					//ob_end_clean();
					//echo $reponse;
					die();
					break;
				case 'clipboard_delete':
					ob_start();
					$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
					unset($_SESSION['dims']['clipboard'][$id]);
					dims_show_clipboard_content();
					$reponse = ob_get_contents();
					ob_end_clean();
					echo $reponse;
					die();
					break;

				case 'clipboard_showcmd';
					ob_start();
					echo dims_clipboard();
					$reponse = ob_get_contents();
					ob_end_clean();
					echo $reponse;
					die();
					break;
				case 'clipboard_showcmdget';
					ob_start();
					echo dims_clipboardGet();
					$reponse = ob_get_contents();
					ob_end_clean();
					echo $reponse;
					die();
					break;
				case 'clipboard_show';
					ob_start();
					dims_show_clipboard_content();
					$reponse = ob_get_contents();
					ob_end_clean();
					echo $reponse;
					die();
					break;

				case 'dynfield_manager':
					include 'modules/system/dynfield/controler_op_dynfield.php';
					break;

				default: // look for dims_op in modules

					$mods = $dims->getModules($_SESSION['dims']['workspaceid']);
					$moduleid = dims_load_securvalue("moduleid", dims_const::_DIMS_NUM_INPUT, true, true);

					if (!empty($mods)) {
						// test system case
						require_once(DIMS_APP_PATH . "/modules/system/op.php");
						$isdoc = false;

						foreach ($mods as $struct) {
							$idm = $struct['instanceid'];

							if ($struct['active'] && ($moduleid ==0 || $moduleid>0 && $moduleid==$idm)) {
								if ($struct['label'] == "doc")
									$isdoc = true;
								$dims_mod_opfile = DIMS_APP_PATH . "/modules/{$struct['label']}/op.php";

								if (file_exists($dims_mod_opfile))
									require_once $dims_mod_opfile;
							}
						}

						$dims_mod_opfile = DIMS_APP_PATH . "/modules/doc/op.php";
						// test si présence de doc
						if (!$isdoc && file_exists($dims_mod_opfile)) {
							require_once $dims_mod_opfile;
						}
					}
					break;

		// TICKETS CASES
		case 'scribus':
			require_once DIMS_APP_PATH.'/modules/scribus/op.php';
			break;

			}
		}
?>
