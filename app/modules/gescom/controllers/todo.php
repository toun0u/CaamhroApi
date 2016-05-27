<?php
switch($a) {
	case 'load':
		$start = dims_load_securvalue('start', dims_const::_DIMS_NUM_INPUT, true, true);
		$nb = dims_load_securvalue('nb', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_go = dims_load_securvalue('id_go', dims_const::_DIMS_NUM_INPUT, true, true);

		if($nb <= 0){
			$nb = _DASHBOARD_NB_ELEMS_DISPLAY;
		}

		$db = dims::getInstance()->getDb();
		$sql = "SELECT		*
				FROM		".todo::TABLE_NAME." t
				INNER JOIN	".todo_dest::TABLE_NAME." d
				ON			d.id_todo = t.id
				LEFT JOIN	dims_globalobject o
				ON			o.id = t.id_globalobject_ref
				INNER JOIN	".user::TABLE_NAME." u
				ON			t.id_user = u.id
				INNER JOIN	".contact::TABLE_NAME." c
				ON			c.id = u.id_contact
				WHERE		t.state = :todostate
				AND			d.id_user=:iduser
				AND 		t.id_workspace = :idw
				".(!empty($id_go)?" AND t.id_globalobject_ref = :go":"")."
				GROUP BY 	t.id
				ORDER BY	t.timestp_create DESC
				LIMIT 		:start, :end";
		$params = array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['userid']),
			':todostate' => array('type' => PDO::PARAM_STR, 'value' => todo::TODO_STATE_RELEASED),
			':idw' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['workspaceid']),
			':start' => array('type' => PDO::PARAM_INT, 'value' => $start),
			':end' => array('type' => PDO::PARAM_INT, 'value' => $nb),
		);
		if(!empty($id_go)){
			$params[':go'] = array('type' => PDO::PARAM_INT, 'value' => $id_go);
		}
		$res = $db->query($sql,$params);
		$separation = $db->split_resultset($res);
		$return = array();
		foreach ($separation as $tab) {
			$dd = dims_timestp2local($tab['t']['timestp_create']);
			$title_object = "";
			switch($tab['o']['id_object']){
				case dims_const::_SYSTEM_OBJECT_EVENT :
					$obj = new action();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['libelle'];
					break;
				case dims_const::_SYSTEM_OBJECT_ACTIVITY :
					require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
					$obj = new dims_activity();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->getLibelle();
					break;
				case dims_const::_SYSTEM_OBJECT_OPPORTUNITY :
					require_once DIMS_APP_PATH.'modules/system/opportunity/class_opportunity.php';
					$obj = new dims_opportunity();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['libelle'];
					break;
				case dims_const::_SYSTEM_OBJECT_CONTACT :
					$obj = new contact();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['firstname'].' '.$obj->fields['lastname'];
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS :
					$obj = new tiers();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['intitule'];
					break;
				case dims_const::_SYSTEM_OBJECT_DOCFILE :
					$obj = new docfile();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['name'];
					break;
				case dims_const::_SYSTEM_OBJECT_CASE :
					$obj = new dims_case();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['label'];
					break;
				case dims_const::_SYSTEM_OBJECT_SUIVI :
					$obj = new suivi();
					$obj->open($tab['o']['id_record']);
					$title_object = $obj->fields['libelle'];
					break;
			}
			$return[] = array(
				'id' => $tab['t']['id'],
				'content' => nl2br($tab['t']['content']),
				'lkObject' => $title_object,
				'lkObjectLk' => $tab['o']['id_record'],
				'user' => ($tab['u']['id']==$_SESSION['dims']['userid'])?'vous-même':$tab['c']['firstname']." ".$tab['c']['lastname'],
				'userLk' => $tab['c']['id'],
				'date' => $dd['time'],
				'echeance' => ($tab['t']['date']=='0000-00-00 00:00:00'?"Pas d'échéance":("Échéance : ".date('d/m/Y',strtotime($tab['t']['date'])))),
				'actionallowed' => $tab['t']['user_from']==$_SESSION['dims']['userid'],
			);
		}
		die(json_encode($return));
		break;
	case 'delete':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$todo = todo::find_by(array('id'=>$id,'user_from'=>$_SESSION['dims']['userid']),null,1);
		if(!empty($todo)){
			$todo->delete();
		}
		$id_go = dims_load_securvalue('id_go',dims_const::_DIMS_NUM_INPUT,true,true);
		$go = dims_globalobject::find_by(array('id'=>$id_go),null,1);
		if(!empty($go)){
			dims_redirect(dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true)."&id=".$go->get('id_record'));
		}else{
			dims_redirect(dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true));
		}
		break;
	case 'edit':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_go = dims_load_securvalue('id_go',dims_const::_DIMS_NUM_INPUT,true,true);
		if(!empty($id_go)){
			$todo = todo::find_by(array('id'=>$id,'user_from'=>$_SESSION['dims']['userid'],'id_globalobject_ref'=>$id_go),null,1);
		}else{
			$todo = todo::find_by(array('id'=>$id,'user_from'=>$_SESSION['dims']['userid']),null,1);
		}
		if(empty($todo)){
			$todo = new todo();
			$todo->init_description();
			$todo->set('id_globalobject_ref',$id_go);
		}
		$flash = array();
		$v = new view($flash);
		$v->set_tpl_webpath('modules/gescom/views/');
		$v->setLayout('layouts/empty.tpl.php');
		$v->assign('return',dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true));
		$v->assign('id_popup',dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true));
		$v->assign('todo',$todo);
		$v->assign('id_go',$id_go);
		$v->render('todos/_edit.tpl.php');
		die($v->compile());
		break;
	case 'save':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$todo = todo::find_by(array('id'=>$id,'user_from'=>$_SESSION['dims']['userid']),null,1);
		$create = dims_createtimestamp();
		if(empty($todo)){
			$todo = new todo();
			$todo->init_description();
			$todo->setugm();
			$todo->set('timestp_create',$create);
			$todo->set('user_from',$_SESSION['dims']['userid']);
		}
		$todo->setvalues($_POST, 'todo_');
		$dd = explode('/',$todo->get('date'));
		if(count($dd) == 3){
			$todo->set('date',$dd[2]."-".$dd[1]."-".$dd[0]." 00:00:00");
		}
		$todo->set('timestp_modify',$create);
		$todo->save();

		$todo->initDestinataires();
		$lstDest = $todo->getListDestinataires();

		$user_id = dims_load_securvalue('user_added',dims_const::_DIMS_NUM_INPUT, true, true,true);
		if(!in_array($_SESSION['dims']['userid'], $user_id)){
			unset($lstDest[$_SESSION['dims']['userid']]);
			$todo->addDestinataire($_SESSION['dims']['userid'],$_SESSION['dims']['userid']);
		}
		foreach($user_id as $id){
			unset($lstDest[$id]);
			$todo->addDestinataire($id,$_SESSION['dims']['userid']);
		}
		foreach($lstDest as $lk){
			$lk->delete();
		}
		$id_go = dims_load_securvalue('id_go',dims_const::_DIMS_NUM_INPUT,true,true);
		$go = dims_globalobject::find_by(array('id'=>$id_go),null,1);
		if(!empty($go)){
			dims_redirect(dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true)."&id=".$go->get('id_record'));
		}else{
			dims_redirect(dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true));
		}
		break;
	case 'search_user':
		ob_clean();
		$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$lstUsers = array();
		if($val != ''){
			require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
			$dimsearch = new search(dims::getInstance());
			$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_CONTACT,$_SESSION['cste']['_DIMS_LABEL_USER']);
			$dimsearch->initSearchObject();

			$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
			$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

			$dimsearch->executeSearch2($val, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

			$ids = array();
			$user = new user();
			$lstU = $user->getusersgroup("",$_SESSION['dims']['workspaceid']);
			foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
				foreach($tab_objects as $idobjet => $tab_ids){
					foreach($tab_ids as $kid => $id){
						$ids[$id['id_go']] = $id['id_go'];
					}
				}
			}
			if(count($ids)){
				$lu = dims_load_securvalue('lu',dims_const::_DIMS_NUM_INPUT,true,true,true);
				$more = "";
				$db = dims::getInstance()->getDb();
				$params = array();
				if(!empty($lu)){
					$more = "AND u.id NOT IN (".$db->getParamsFromArray($lu, 'id2', $params).")";
				}
				$sel = "SELECT 		u.*
						FROM 		".user::TABLE_NAME." u
						INNER JOIN 	".contact::TABLE_NAME." ct
						ON 			ct.id = u.id_contact
						WHERE 		ct.id_globalobject IN (".$db->getParamsFromArray($ids, 'id1', $params).")
						AND 		u.id IN (".$db->getParamsFromArray($lstU, 'id3',$params).")
						$more
						AND 		u.status = ".user::USER_ACTIF."
						ORDER BY 	u.firstname, u.lastname";
				$res = $db->query($sel,$params);
				if($db->numrows($res)){
					while($r = $db->fetchrow($res)){
						$lstUsers[] = array(
							'id' => $r['id'],
							'firstname' => $r['firstname'],
							'lastname' => $r['lastname'],
						);
					}
				}
			}
		}
		die(json_encode($lstUsers));
		break;
}
