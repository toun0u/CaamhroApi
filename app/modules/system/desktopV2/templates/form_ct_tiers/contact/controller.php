<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_address.php';
switch($action){
	default :
	case 'new':
	case 'edit':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$contact = new contact();
		if($id != '' && $id > 0){
			$contact->open($id);
			if($contact->isNew() || $contact->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$contact = new contact();
				$contact->init_description();
			}
		}else
			$contact->init_description();
		if($contact->isNew()){
			$contact->setLightAttribute('save_url', dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=save");
			$contact->setLightAttribute('back_url', dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=new");
		}else{
			$contact->setLightAttribute('save_url', dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=save&id=".$contact->get('id'));
			$contact->setLightAttribute('back_url', dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$contact->get('id'));
		}
		$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/edit_contact.tpl.php');
		break;
	case 'show':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$adr = dims_load_securvalue('adr', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$contact = new contact();
		if($id != '' && $id > 0){
			$contact->open($id);
			if($contact->isNew() || $contact->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$contact = new contact();
				$contact->init_description();
			}
		}else
			$contact->init_description();
		if($contact->isNew()){
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=new");
		}else{
			$contact->setLightAttribute('adr',$adr); // permet l'ajout d'une adresse à un tiers nouvellement créé
			$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/display_contact.tpl.php');
		}
		break;
	case 'delete':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ct = contact::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($ct)){
			$ct->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&force_desktop=1&mode=default");
		break;
	case 'view_edit':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$contact = new contact();
		if($id != '' && $id > 0){
			$contact->open($id);
			$contact->setLightAttribute('function','dims_nan');
			if($contact->isNew() || $contact->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$contact = new tiers();
				$contact->init_description();
			}else{
				$is_parent = dims_load_securvalue('is_parent', dims_const::_DIMS_NUM_INPUT, true, true,true);
				if($id_tiers != '' && $id_tiers > 0 && $type == tiers::MY_GLOBALOBJECT_CODE){
					$tiers = new tiers();
					$tiers->open($id_tiers);
					if(!$tiers->isNew() && $tiers->get('id_workspace') == $_SESSION['dims']['workspaceid']){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$lk = tiersct::find_by(array('id_tiers'=>$tiers->get('id'),'id_contact'=>$contact->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
						if(!empty($lk)){
							$tiers->setLightAttribute('function',(($lk->get('function')!='')?$lk->get('function'):'dims_nan'));
						}
					}
				}
			}
		}else
			$contact->init_description();
		$contact->setLightAttribute('id_tiers',$id_tiers);
		$contact->setLightAttribute('type',$type);
		if($contact->isNew()){
			$label = explode(" ", trim(dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, true, true,true)));
			if(count($label) > 1){
				$contact->set('firstname',$label[0]);
				$contact->set('lastname',$label[1]);
			}else
				$contact->set('firstname',$label[0]);
		}
		$contact->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=save&id=$id");
		$contact->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_tiers);
		$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/edit_contact.tpl.php');
		die();
		break;
	case 'save':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$contact = new contact();
		if($id != '' && $id > 0){
			$contact->open($id);
			if($contact->isNew() || $contact->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$contact = new contact();
				$contact->init_description();
			}
		}else
			$contact->init_description();
		$contact->setvalues($_POST, 'ct_');
		$contact->setvalues($_POST, 'dyn_');
		$contact->set('id_user', $_SESSION['dims']['userid']);
		$isNew = $contact->isNew();
		if($contact->save()){
			$id_contact = $contact->getId();
			require_once(DIMS_APP_PATH.'modules/system/crm_contact_add_photo.php');

			require_once(DIMS_APP_PATH.'modules/system/class_tag.php');
			require_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');

			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true);
			if(empty($tags)) $tags = array();
			$myTags = $contact->getMyTags();
			foreach($myTags as $t){
				if(in_array($t->get('id'), $tags)){
					unset($tags[array_search($t->get('id'), $tags)]);
				}else{
					$lk = new tag_globalobject();
					$lk->openWithCouple($t->get('id'),$contact->get('id_globalobject'));
					if(!$lk->isNew())
						$lk->delete();
				}
			}
			if(!empty($tags)){
				foreach($tags as $t){
					$lk = new tag_globalobject();
					$lk->init_description();
					$lk->set('id_tag',$t);
					$lk->set('id_globalobject',$contact->get('id_globalobject'));
					$lk->set('timestp_modify',dims_createtimestamp());
					$lk->save();
				}
			}

			$addresses = dims_load_securvalue('addresses',dims_const::_DIMS_NUM_INPUT, true, true,true);
			$address = new address();
			if($addresses != '' && $addresses > 0){
				$address->open($addresses);
				if(!$address->isNew() && $address->get('id_workspace') == $_SESSION['dims']['workspaceid'] && is_null($address->getLinkCt($contact->get('id_globalobject'))))
					$address->addLink($contact->get('id_globalobject'));
			}

			// Account
			$login = trim(dims_load_securvalue('u_login',dims_const::_DIMS_CHAR_INPUT,true,true));
			$pwd = dims_load_securvalue('pwd',dims_const::_DIMS_CHAR_INPUT,true,true);
			$pwd_confirm = dims_load_securvalue('pwd_confirm',dims_const::_DIMS_CHAR_INPUT,true,true);
			$u_front = dims_load_securvalue('u_front',dims_const::_DIMS_NUM_INPUT,true,true);
			$u_back = dims_load_securvalue('u_back',dims_const::_DIMS_NUM_INPUT,true,true);
			$hasdimsuser = dims_load_securvalue('has_dims_user',dims_const::_DIMS_NUM_INPUT,true,true);

			if($hasdimsuser) {
				if($pwd == $pwd_confirm){
					$user = user::find_by(array('id'=>$contact->get('account_id'), 'id_contact'=>$contact->get('id')),null,1);
					if(empty($user)){
						if($login != '' && $pwd != ''){
							$user = new user();
							$user->init_description();
							$user->set('firstname',$contact->get('firstname'));
							$user->set('lastname',$contact->get('lastname'));
							$user->set('login',$login);
							$user->set('email',$contact->get('email'));
							$user->set('phone',$contact->get('phone'));
							$user->set('comments',$contact->get('comments'));
							$user->set('id_contact',$contact->get('id'));
							dims::getInstance()->getPasswordHash($pwd,$user->fields['password'],$user->fields['salt']);
							$user->set('date_creation',dims_createtimestamp());
							$user->set('defaultworkspace',$_SESSION['dims']['workspaceid']);
							$user->set('lang',$_SESSION['dims']['user']['lang']);
							$user->set('id_skin',$_SESSION['dims']['user']['id_skin']);
							$user->set('status',($u_front||$u_back));
							$user->save();
							$contact->set('account_id',$user->get('id'));
							$contact->save();

							$wu = workspace_user::find_by(array('id_user'=>$user->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
							if(empty($wu)){
								$wu = new workspace_user();
								$wu->init_description();
								$wu->set('id_user',$user->get('id'));
								$wu->set('id_workspace',$_SESSION['dims']['workspaceid']);
							}
							$wu->set('activefront',$u_front);
							$wu->set('activeback',$u_back);
							$wu->set('activeplanning',1);
							if($u_front || $u_back){
								$wu->set('adminlevel',_DIMS_ID_LEVEL_USER);
							}else{
								$wu->set('adminlevel',0);
							}
							$wu->save();
						}
					}else{
						$user->set('firstname',$contact->get('firstname'));
						$user->set('lastname',$contact->get('lastname'));
						$user->set('email',$contact->get('email'));
						$user->set('phone',$contact->get('phone'));
						$user->set('comments',$contact->get('comments'));
						$user->set('defaultworkspace',$_SESSION['dims']['workspaceid']);
						$user->set('lang',$_SESSION['dims']['user']['lang']);
						$user->set('id_skin',$_SESSION['dims']['user']['id_skin']);
						$user->set('status',($u_front||$u_back));
						if($pwd != '')
							dims::getInstance()->getPasswordHash($pwd,$user->fields['password'],$user->fields['salt']);

						$user->save();

						$contact->set('account_id',$user->get('id'));
						$contact->save();

						$wu = workspace_user::find_by(array('id_user'=>$user->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
						if(empty($wu)){
							$wu = new workspace_user();
							$wu->init_description();
							$wu->set('id_user',$user->get('id'));
							$wu->set('id_workspace',$_SESSION['dims']['workspaceid']);
						}
						$wu->set('activefront',$u_front);
						$wu->set('activeback',$u_back);
						$wu->set('activeplanning',1);
						if($u_front || $u_back){
							$wu->set('adminlevel',_DIMS_ID_LEVEL_USER);
						}else{
							$wu->set('adminlevel',0);
						}
						$wu->save();
					}
				}
			} else {
				$user = user::find_by(array('id'=>$contact->get('account_id'), 'id_contact'=>$contact->get('id')),null,1);
				if(!empty($user)) {
					$wu = workspace_user::find_by(array('id_user' => $user->get('id'), 'id_workspace' => $_SESSION['dims']['workspaceid']), null, 1);

					if (empty($wu)) {
						$wu = new workspace_user();
						$wu->fields['id_user']      = $user->get('id');
						$wu->fields['id_workspace'] = $user->get('workspaceid');
					}

					$wu->set('activefront', 0);
					$wu->set('activeback', 0);
					$wu->save();
				}
			}

			// groupes
			require_once DIMS_APP_PATH."modules/system/class_ct_group.php";
			$groups = ct_group::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']),' ORDER BY label ');
			$LstGr = $lstGrUsed = array();
			foreach($groups as $gr){
				$LstGr[$gr->get('id')] = $gr->get('id');
			}
			require_once DIMS_APP_PATH."modules/system/class_ct_group_link.php";
			$groupsUsed = ct_group_link::find_by(array('id_globalobject'=>$contact->get('id_globalobject'),'type_contact'=>contact::MY_GLOBALOBJECT_CODE));
			foreach($groupsUsed as $gr){
				$lstGrUsed[$gr->get('id_group_ct')] = $gr;
			}
			$groups = dims_load_securvalue('groups',dims_const::_DIMS_NUM_INPUT,true,true,true);
			foreach ($groups as $g) {
				if(in_array($g, $LstGr)){
					if(isset($lstGrUsed[$g])){
						unset($lstGrUsed[$g]);
					}else{
						$lk = new ct_group_link();
						$lk->init_description();
						$lk->set('id_globalobject',$contact->get('id_globalobject'));
						$lk->set('type_contact',contact::MY_GLOBALOBJECT_CODE);
						$lk->set('id_group_ct',$g);
						$lk->set('date_create',dims_createtimestamp());
						$lk->save();
					}
				}
			}
			foreach($lstGrUsed as $l){
				$l->delete();
			}

			$id_tiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT, true, true,true);
			if($id_tiers != '' && $id_tiers > 0){
				require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
				$lk = tiersct::find_by(array('id_tiers'=>$id_tiers,'id_contact'=>$contact->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(empty($lk)){
					$lk = new tiersct();
					$lk->init_description();
					$lk->set('id_tiers', $id_tiers);
					$lk->set('id_contact', $contact->get('id'));
					$lk->set('link_level', 2);
					$lk->set('date_deb', dims_createtimestamp());
					$lk->set('type_lien', $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']);
				}
				$lk->set('date_fin', 0);
				$lk->set('function',dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT, true, true,true));
				$lk->save();

				$tiers = new tiers();
				$tiers->open($id_tiers);
				if(is_null($address->getLinkCt($tiers->get('id_globalobject'))))
					$address->addLink($tiers->get('id_globalobject'));

				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_tiers);
			}else{
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$contact->get('id'));
			}
		}else{
			// TODO : gérer le ré-import des data dans le form
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=new");
		}
		break;
	case 'add_link_ct_tiers':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0 && $id_tiers != '' && $id_tiers > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id_tiers,'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(empty($lk)){
				$lk = new tiersct();
				$lk->init_description();
				$lk->set('id_tiers', $id_tiers);
				$lk->set('id_contact', $id_ct);
				$lk->set('link_level', 2);
				$lk->set('date_deb', dims_createtimestamp());
				$lk->set('type_lien', $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']);
			}
			$lk->set('date_fin', 0);

			$lk->set('function',trim(dims_load_securvalue('bis_function',dims_const::_DIMS_CHAR_INPUT, true, true,true)));
			$f = $lk->get('function');
			if(empty($f))
				$lk->set('function',trim(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT, true, true,true)));
			if($lk->get('function') == 'dims_nan')
				$lk->set('function',"");
			$lk->save();
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=$id_ct");
		}else
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=new");
		break;
	case 'remove_file':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0){
			$ct = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($ct)){
				require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$doc = docfile::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				$foldPar = docfolder::find_by(array('id'=>$doc->get('id_folder'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				$foldCt = docfolder::find_by(array('id'=>$ct->get('id_folder'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if($foldPar->get('id') == $foldCt->get('id') || in_array($foldCt->get('id'), explode(',', $foldPar->get('parents')))){
					$doc->delete();
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct."#doc");
		break;
	case 'add_file':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0){
			$ct = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($ct)){
				$lstFiles = dims_load_securvalue('file_name', dims_const::_DIMS_CHAR_INPUT, true, true,true);
				$descriptions = dims_load_securvalue('doc_description', dims_const::_DIMS_CHAR_INPUT, true, true,true);
				$directory = dims_load_securvalue('directory', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$tags = dims_load_securvalue('tags', dims_const::_DIMS_CHAR_INPUT, true, true,true);
				$id_folder = dims_load_securvalue('id_folder', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$tmp_path = DIMS_ROOT_PATH.'www/data/uploads/'.session_id();
				if(!empty($lstFiles) && file_exists($tmp_path)){
					$dir = scandir($tmp_path);
					require_once(DIMS_APP_PATH.'modules/system/class_tag.php');
					require_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');
					require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
					foreach($lstFiles as $key => $name){
						if(in_array($name, $dir)){
							$doc = new docfile();
							$doc->init_description();
							$doc->setugm();
							$doc->set('name',$name);
							$doc->set('size',filesize($tmp_path."/".$name));
							$doc->set('description',$descriptions[$key]);
							$doc->set('id_folder',(($directory[$key] != '' && $directory[$key] > 0)?$directory[$key]:$id_folder));
							$doc->tmpuploadedfile = $tmp_path."/".$name;
							$doc->save();

							// Lien matrice
							$matrice = new matrix();
							$matrice->fields['id_doc'] = $doc->fields['id_globalobject'];
							$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
							$matrice->fields['year'] = substr($doc->fields['timestp_create'],0,4);
							$matrice->fields['month'] = substr($doc->fields['timestp_create'],4,2);
							$matrice->fields['timestp_modify'] = dims_createtimestamp();
							$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$matrice->save();

							if(isset($tags[$key]) && !empty($tags[$key])){
								if(strrpos($tags[$key],',') !== false)
									$tags[$key] = explode(',', $tags[$key]);
								if(is_array($tags[$key])){
									foreach($tags[$key] as $t){
										$lk = new tag_globalobject();
										$lk->init_description();
										$lk->set('id_tag',$t);
										$lk->set('id_globalobject',$doc->get('id_globalobject'));
										$lk->set('timestp_modify',dims_createtimestamp());
										$lk->save();
									}
								}else{
									$lk = new tag_globalobject();
									$lk->init_description();
									$lk->set('id_tag',$tags[$key]);
									$lk->set('id_globalobject',$doc->get('id_globalobject'));
									$lk->set('timestp_modify',dims_createtimestamp());
									$lk->save();
								}
							}
						}
					}
					dims_deletedir($tmp_path);
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct."#doc");
		break;
	case 'edit_todo':
		ob_clean();
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		require_once DIMS_APP_PATH.'include/class_todo.php';
		if($id != '' && $id > 0){
			$todo = todo::find_by(array('id_globalobject'=>$id, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(empty($todo)){
				$todo = new todo();
				$todo->init_description();
			}
		}else{
			$todo = new todo();
			$todo->init_description();
		}
		$todo->setLightAttribute('id_ct',$id_ct);
		$todo->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=add_todo&id_ct=".$id_ct);
		$todo->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct);
		$todo->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/todo/edit_todo.tpl.php');
		die();
		break;
	case 'delete_todo':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_todo = dims_load_securvalue('id_todo', dims_const::_DIMS_NUM_INPUT, true, true,true);
		require_once DIMS_APP_PATH.'include/class_todo.php';
		$todo = todo::find_by(array('id'=>$id_todo,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($todo)){
			$todo->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct."#todo");
		break;
	case 'add_todo':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0){
			$ct = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($ct)){
				require_once DIMS_APP_PATH.'include/class_todo.php';
				$todo = new todo();
				$id_todo = dims_load_securvalue('id_todo', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$create = dims_createtimestamp();
				if($id_todo != '' && $id_todo > 0){
					$todo->open($id_todo);
					if($todo->isNew() || $todo->get('id_workspace') != $_SESSION['dims']['workspaceid']){
						$todo = new todo();
						$todo->init_description();
						$todo->setugm();
						$todo->set('timestp_create',$create);
						$todo->set('user_from',$_SESSION['dims']['userid']);
						$todo->set('id_globalobject_ref',$ct->get('id_globalobject'));
					}
				}else{
					$todo->init_description();
					$todo->setugm();
					$todo->set('timestp_create',$create);
					$todo->set('user_from',$_SESSION['dims']['userid']);
					$todo->set('id_globalobject_ref',$ct->get('id_globalobject'));
				}
				$todo->setvalues($_POST, 'todo_');
				$date = dims_load_securvalue('todo_date',dims_const::_DIMS_CHAR_INPUT,true,true,true);
				$dd = explode('/',$date);
				if(count($dd) == 3){
					$todo->set('date',$dd[2]."-".$dd[1]."-".$dd[0]." 00:00:00");
				}
				$todo->set('timestp_modify',$create);
				$todo->save();

				$todo->initDestinataires();
				$lstDest = $todo->getListDestinataires();

				$user_id = dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT, true, true,true);
				if(!in_array($_SESSION['dims']['workspaceid'], $user_id)){
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
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct."#todo");
		break;
	case 'edit_link':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_tiers != '' && $id_tiers > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id_tiers,'id_contact'=>$id, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$lk->setLightAttribute('mode','contact');
				$lk->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/edit_link_ct_tiers.tpl.php');
			}
		}
		die();
		break;
	case 'save_link':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0 && $id_tiers != '' && $id_tiers > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id_tiers,'id_contact'=>$id_ct, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$lk->setvalues($_POST,'lk_');
				$date_deb = dims_load_securvalue('date_deb', dims_const::_DIMS_CHAR_INPUT, true, true,true);
				$date_deb = explode('/', $date_deb);
				if(count($date_deb) == 3){
					$lk->set('date_deb',$date_deb[2].$date_deb[1].$date_deb[0]."000000");
				}else{
					$lk->set('date_deb',date('Ymd000000'));
				}
				$date_fin = dims_load_securvalue('date_fin', dims_const::_DIMS_CHAR_INPUT, true, true,true);
				$date_fin = explode('/', $date_fin);
				if(count($date_fin) == 3){
					$lk->set('date_fin',$date_fin[2].$date_fin[1].$date_fin[0]."000000");
				}else{
					$lk->set('date_fin',"0");
				}
				$lk->save();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct);
		break;
	case 'detach_tiers':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_tiers != '' && $id_tiers > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id_tiers,'id_contact'=>$id, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$date_fin = dims_load_securvalue('date_fin',dims_const::_DIMS_CHAR_INPUT,true,true,true);
				$lk->set('date_fin',dims_createtimestamp());
				$dd = explode('/',$date_fin);
				if(count($dd) == 3){
					$lk->set('date_fin',$dd[2].$dd[1].$dd[0]."000000");
				}
				$lk->save();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id);
		break;
	case 'remove_link':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_tiers != '' && $id_tiers > 0){
			$ct = contact::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			$tiers = tiers::find_by(array('id'=>$id_tiers,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($ct) && !empty($tiers)){
				require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
				$matrices = matrix::find_by(array(	'id_tiers'=>$ct->get('id_globalobject'),
													'id_contact'=>$tiers->get('id_globalobject'),
													'id_action'=>0,
													'id_opportunity'=>0,
													'id_activity'=>0,
													'id_appointment_offer'=>0,
													'id_tiers2'=>0,
													'id_contact2'=>0,
													'id_doc'=>0,
													'id_case'=>0,
													'id_suivi'=>0,
													'id_share'=>0
												));
				foreach($matrices as $m){
					$m->delete();
				}
			}
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id_tiers,'id_contact'=>$id, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$lk->delete();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id);
		break;
	case 'search_contact':
		ob_clean();
		$label = trim(dims_load_securvalue('label_search_contact',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($label != ''){
			require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
			$dimsearch = new search(dims::getInstance());
			$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, contact::MY_GLOBALOBJECT_CODE,$_SESSION['cste']['_DIMS_LABEL_CONTACT']);
			$dimsearch->initSearchObject();

			$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
			$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

			$dimsearch->executeSearch2($label, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

			$ids = array();
			foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
				foreach($tab_objects as $idobjet => $tab_ids){
					foreach($tab_ids as $kid => $id){
						$ids[$id['id_go']] = $id['id_go'];
					}
				}
			}
			if(count($ids)){
				$db = dims::getInstance()->getDb();
				$params = array();

				$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true);
				$text_attach = "";
				if($id_tiers != '' && $id_tiers > 0){
					$tiers = tiers::find_by(array('id'=>$id_tiers,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($tiers)){
						$text_attach = $tiers->get('intitule');
						$lstLink = $tiers->getAllContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
						$lstAlready = array();
						foreach($lstLink as $lk){
							unset($ids[$lk->get('id_globalobject')]);
						}
					}
				}
				if(count($ids)){
					$sel = "SELECT		*
							FROM		".contact::TABLE_NAME."
							WHERE		id_globalobject IN (".$db->getParamsFromArray($ids, 'idtiers', $params).")
							ORDER BY 	firstname, lastname ASC";
					$res = $db->query($sel, $params);
					?>
					<div class="similar-address" style="padding-top: 5px;">
						<?php
						while ($r = $db->fetchrow($res)){
							$c = new contact();
							$c->openWithFields($r);
							$c->setLightAttribute('text_attach',$text_attach);
							$c->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/result_contact.tpl.php');
						}
						?>
						<div style="margin-top:10px;">
							<input type="button" value="<?= $_SESSION['cste']['CREATE_NEW_CONTACT']; ?>" class="add_contact submit" />
							<?= $_SESSION['cste']['_DIMS_OR']; ?>
							<a href="javascript:void(0);" class="undo">
								<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
							</a>
						</div>
					</div>
					<script type="text/javascript">
						$(document).ready(function(){
							$('div#add_contact a.addContactFromSearch').click(function(){
								if($(this).attr('dims-data-value') != undefined){
									var elem = $(this);
									$.ajax({
										type: "POST",
										url: '<?= dims::getInstance()->getScriptenv(); ?>',
										data: {
											'submenu': '1',
								            'mode': 'company',
								            'action' : 'get_form_lk_ct',
								            'id_ct' : elem.attr('dims-data-value'),
								            'id': '<?= $id_tiers; ?>',
										},
										dataType: 'html',
										success: function(data){
											elem.parents('td:first').html(data);
										},
									});
								}
							});
						});
					</script>
					<?php
				}else{
					?>
					<div class="similar-address" style="padding-top: 5px;">
						<div class="infos" style="margin-bottom:10px;">
							<?= str_replace('{DIMS_TEXT}', '<b>'.$label.'</b>', $_SESSION['cste']['_NO_CONTACT_MATCHING_WAS_FOUND']); ?>
						</div>
						<input type="button" value="<?= $_SESSION['cste']['CREATE_NEW_CONTACT']; ?>" class="add_contact submit" />
						<?= $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="javascript:void(0);" class="undo">
							<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
					<?php
				}
			}else{
				?>
				<div class="similar-address" style="padding-top: 5px;">
					<div class="infos" style="margin-bottom:10px;">
						<?= str_replace('{DIMS_TEXT}', '<b>'.$label.'</b>', $_SESSION['cste']['_NO_CONTACT_MATCHING_WAS_FOUND']); ?>
					</div>
					<input type="button" value="<?= $_SESSION['cste']['CREATE_NEW_CONTACT']; ?>" class="add_contact submit" />
					<?= $_SESSION['cste']['_DIMS_OR']; ?>
					<a href="javascript:void(0);" class="undo">
						<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
					</a>
				</div>
				<?php
			}
		}
		die();
		break;
	case 'add_tmp_tag':
		include_once(DIMS_APP_PATH.'modules/system/class_tag.php');
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ct = contact::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		$tag = tag::find_by(array('id'=>$id_tag,'id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION),null,1);
		if(!empty($ct) && !empty($tag)){
			$year = dims_load_securvalue('year',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$month = dims_load_securvalue('month',dims_const::_DIMS_NUM_INPUT,true,true,true);
			include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			$m = matrix::find_by(array(
				'id_contact'=>$ct->get('id_globalobject'),
				'id_tag'=>$tag->get('id'),
				'year'=>$year,
				'id_workspace'=>$_SESSION['dims']['workspaceid'],
			),null,1);
			if(empty($m)){
				$m = new matrix();
				$m->init_description();
				$m->set('id_workspace',$_SESSION['dims']['workspaceid']);
				$m->set('id_contact',$ct->get('id_globalobject'));
				$m->set('id_tag',$tag->get('id'));
				$m->set('year',$year);
				$m->set('month',$month);
				if($year < date('Y')-1 || ($year == date('Y')-1 && $month <= date('m'))){
					$m->set('timestp_end',$year.$month."01000000");
				}else{
					include_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');
					$lk = tag_globalobject::find_by(array('id_tag'=>$tag->get('id'),'id_globalobject'=>$ct->get('id_globalobject')),null,1);
					if(empty($lk)){
						$lk = new tag_globalobject();
						$lk->init_description();
						$lk->set('id_tag',$tag->get('id'));
						$lk->set('id_globalobject',$ct->get('id_globalobject'));
						$lk->set('timestp_modify',dims_createtimestamp());
						$lk->save();
					}
				}
				$m->save();
			}elseif($year >= date('Y') && $m->get('timestp_end') == 0){
				// On peux l'éditer si le lien n'est pas passé et non fermé
				$m->set('year',$year);
				$m->set('month',$month);
				$m->save();
			}
			unset($_SESSION['dims']['advanced_search']['available_years']);
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id);
		break;
	case 'del_tmp_tag':
		include_once(DIMS_APP_PATH.'modules/system/class_tag.php');
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ct = contact::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		$tag = tag::find_by(array('id'=>$id_tag,'id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION),null,1);
		if(!empty($ct) && !empty($tag)){
			$year = dims_load_securvalue('year',dims_const::_DIMS_NUM_INPUT,true,true,true);
			include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			$m = matrix::find_by(array(
				'id_contact'=>$ct->get('id_globalobject'),
				'id_tag'=>$tag->get('id'),
				'year'=>$year,
				'id_workspace'=>$_SESSION['dims']['workspaceid'],
			),null,1);
			if(!empty($m)){
				$m->delete();
			}
			$m = matrix::find_by(array(
				'id_contact'=>$ct->get('id_globalobject'),
				'id_tag'=>$tag->get('id'),
				'id_workspace'=>$_SESSION['dims']['workspaceid'],
			),null,1);
			if(empty($m)){
				include_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');
				$lk = tag_globalobject::find_by(array('id_tag'=>$tag->get('id'),'id_globalobject'=>$ct->get('id_globalobject')),null,1);
				if(!empty($lk)){
					$lk->delete();
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id);
		break;
}
