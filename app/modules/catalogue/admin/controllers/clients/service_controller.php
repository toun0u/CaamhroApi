<?php
$view = view::getInstance();
$sub_action = $view->get('sa');

$view->setLayout('layouts/clients_service_layout.tpl.php');

switch($sub_action){
	default:
	case 'show':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		$parent = $client->getService();
		$elem = $parent;
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);
		}
		$view->assign('parent',$parent);
		$view->assign('current',$elem);
		$view->assign('users',$elem->getusers(true));

		$view->assign('addresses',$client->getDepots());

		$servValid = new cata_param();
		$servValid->getByName('services_validation');
		if($servValid->isNew()) $servValid->setValue(0);
		$view->assign('active_serv',$servValid);

		$view->render('clients/show/services.tpl.php');
		break;
	case 'edit':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);

			$parent = $client->getService();
			if($parent->get('id') == $elem->get('id') || in_array($parent->get('id'),explode(';',$elem->fields['parents']))){
				$view->assign('parent',$parent);
				$view->assign('current',$elem);

				$view->assign('addresses',$client->getDepots());

				$view->assign('group',$elem);

				$view->render('clients/show/services_edit.tpl.php');
			}else
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}else
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
	case 'save':
		include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
		$elem = new cata_group();
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			$elem->open($grid);
		}else
			$elem->init_description();
		$elem->setvalues($_POST,'gr_');
		$idParent = dims_load_securvalue('gr_id_group',dims_const::_DIMS_NUM_INPUT,true,true);
		if($idParent != '' && $idParent > 0){
			$par = new cata_group();
			$par->open($idParent);
			$elem->fields['parents'] = $par->fields['parents'].";".$par->get('id');
		}
		$elem->save();
		$elem->addAdr(dims_load_securvalue('id_adr',dims_const::_DIMS_NUM_INPUT,true,true,true));
		$client = $view->get('client');
		dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $elem->get('id'))));
		break;
	case 'add':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			$parent = $client->getService();

			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);

			$view->assign('parent',$parent);
			$view->assign('current',$elem);

			$view->assign('addresses',$client->getDepots());

			$edit = new cata_group();
			$edit->init_description();
			$edit->fields['id_group'] = $elem->get('id');

			$view->assign('group',$edit);

			$view->render('clients/show/services_edit.tpl.php');
		}else{
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}
		break;
	case 'attach':
		$client = $view->get('client');
		$parent = $client->getService();
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0 && $parent->get('id') != $grid){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);

			if(in_array($parent->get('id'),explode(';',$elem->fields['parents']))){
				$view->assign('parent',$parent);
				$view->assign('current',$elem);

				$view->assign('users',$parent->getusers(true));
				$view->assign('already',$elem->getIdUsers());

				$view->render('clients/show/services_attach_user.tpl.php');
			}else
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}else
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
	case 'attachsave':
		$client = $view->get('client');
		$parent = $client->getService();
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0 && $parent->get('id') != $grid){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);

			if(in_array($parent->get('id'),explode(';',$elem->fields['parents']))){

				$lstAlready = $elem->getIdUsers();
				$lstNew = dims_load_securvalue('users_attach',dims_const::_DIMS_NUM_INPUT,true,true);
				if (!empty($lstNew)){
					foreach($lstNew as $idUser){
						if(isset($lstAlready[$idUser]))
							unset($lstAlready[$idUser]);
						else
							$elem->addUser($idUser);
					}
				}
				foreach($lstAlready as $idUser){
					$elem->delUser($idUser);
				}
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $grid)));
			}
		}
		dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
	break;
	case 'edituser':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		$parent = $client->getService();
		$elem = $parent;
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);
		}
		$view->assign('parent',$parent);
		$view->assign('current',$elem);

		$user = new user();
		$uid = dims_load_securvalue('uid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($uid != '' && $uid > 0)
			$user->open($uid);// TODO : ajouter contrôle pour vérifier que l'user appartient bien à l'entreprise
		else
			$user->init_description();

		$view->assign('editedUser',$user);

		$servValid = new cata_param();
		$servValid->getByName('services_validation');
		if($servValid->isNew()) $servValid->setValue(0);
		$view->assign('active_serv',$servValid);

		$default_lvl = "";
		if($user->isNew()){
			$default_lvl_registration = new cata_param();
			$default_lvl_registration->getByName('default_lvl_registration');
			if($default_lvl_registration->isNew()){
				$default_lvl_registration->init_description();
				$default_lvl_registration->setValue('user_without_valid');
				$default_lvl_registration->fields['name'] = 'default_lvl_registration';
				$default_lvl_registration->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$default_lvl_registration->save();
			}
			$default_lvl = $default_lvl_registration->getValue();
		}else{
			$group_user = new group_user();
			$group_user->open($parent->get('id'),$user->get('id'));
			$default_lvl = cata_param::GetLabelCorresp($group_user->fields['adminlevel']);
		}
		$view->assign('default_lvl',$default_lvl);

		$view->render('clients/show/services_add_user.tpl.php');
		break;
	case 'saveuser':
		$client = $view->get('client');
		$parent = $client->getService();
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);

			if($parent->get('id') == $elem->get('id') || in_array($parent->get('id'),explode(';',$elem->fields['parents']))){
				$lstAlready = $parent->getIdUsers();
				$user = new user();
				$uid = dims_load_securvalue('uid',dims_const::_DIMS_NUM_INPUT,true,true);
				if($uid != '' && $uid > 0){
					if(in_array($uid,$lstAlready)){
						$user->open($uid);
					}else
						dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $grid)));
				}else{
					$user->init_description();
					$user->fields['date_creation'] = dims_createtimestamp();
					$user->fields['status'] = 1;
				}
				$user->setvalues($_POST,'user_');
				$pwd = dims_load_securvalue('pwd',dims_const::_DIMS_CHAR_INPUT,true,true,true);
				$conf_pwd = dims_load_securvalue('conf_pwd',dims_const::_DIMS_CHAR_INPUT,true,true,true);
				if($user->isNew()){
					if($pwd == $conf_pwd)
						$user->fields['password'] = dims_getPasswordHash($pwd);
					else
						dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $grid)));
				}elseif($pwd == $conf_pwd){
					if($pwd != '')
						$user->fields['password'] = dims_getPasswordHash($pwd);
				}else
					dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $grid)));
				$isNew = $user->isNew();
				$user->save();

				$idLvl = cata_param::GetIdCorresp(dims_load_securvalue('level',dims_const::_DIMS_CHAR_INPUT,true,true,true));
				if ($isNew) {
					$parent->addUser($user->get('id'),$idLvl); // on rattache au parent dans tous les cas
					if($parent->get('id') != $elem->get('id'))
						$elem->addUser($user->get('id'));
				}else{
					$group_user = new group_user();
					$group_user->open($parent->get('id'),$user->get('id'));
					$group_user->fields['adminlevel'] = $idLvl;
					$group_user->save();
				}

				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $grid)));
			}
		}
		dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
	case 'addadr':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			$parent = $client->getService();
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$elem = new cata_group();
			$elem->open($grid);

			if($parent->get('id') == $elem->get('id') || in_array($parent->get('id'),explode(';',$elem->fields['parents']))){
				$view->assign('parent',$parent);
				$view->assign('current',$elem);

				include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
				$elem2 = new cata_depot();
				$elem2->init_description();
				$a_countries = country::getAllCountries();
				$a_countries_list = array(0=>"");
				foreach ($a_countries as $country) {
					$a_countries_list[$country->get('id')] = $country->getLabel();
				}
				$view->assign('pays',$a_countries_list);
				$view->assign('adr',$elem2);

				$view->assign('back_path',get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $elem->get('id'))));
				$view->assign('action_path',get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'saveadr', 'grid' => $elem->get('id'))));

				$view->render('clients/show/address_liv_edit.tpl.php');
			}else
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}else
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
	case 'saveadr':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$parent = $client->getService();
			$elem = new cata_group();
			$elem->open($grid);

			if($parent->get('id') == $elem->get('id') || in_array($parent->get('id'),explode(';',$elem->fields['parents']))){

				include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
				$elem2 = new cata_depot();
				$elem2->init_description();
				$elem2->fields['client'] = $client->fields['code_client'];
				$elem2->setvalues($_POST,'adr_');
				$elem2->save();
				$elem->addAdr($elem2->get('id'));
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $elem->get('id'))));
			}else
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}else
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
	case 'detachuser':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$parent = $client->getService();
			$elem = new cata_group();
			$elem->open($grid);

			$uid = dims_load_securvalue('uid',dims_const::_DIMS_NUM_INPUT,true,true);
			if(in_array($parent->get('id'),explode(';',$elem->fields['parents'])) && $uid != '' && $uid > 0){

				$elem->delUser($uid);
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $elem->get('id'))));
			}else
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}else
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
	case 'switchuser':
		$client = $view->get('client');
		$grid = dims_load_securvalue('grid',dims_const::_DIMS_NUM_INPUT,true,true);
		if($grid != '' && $grid > 0){
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_cata_group.php';
			$parent = $client->getService();
			$elem = new cata_group();
			$elem->open($grid);

			$uid = dims_load_securvalue('uid',dims_const::_DIMS_NUM_INPUT,true,true);
			if(($parent->get('id') == $elem->get('id') || in_array($parent->get('id'),explode(';',$elem->fields['parents']))) && $uid != '' && $uid > 0 && in_array($uid,$elem->getIdUsers())){
				$user = new user();
				$user->open($uid);
				$user->fields['status'] = !$user->fields['status'];
				$user->save();
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show', 'grid' => $elem->get('id'))));
			}else
				dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		}else
			dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'sa' => 'show')));
		break;
}
