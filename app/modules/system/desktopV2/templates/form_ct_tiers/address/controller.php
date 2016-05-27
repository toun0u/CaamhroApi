<?php
require_once DIMS_APP_PATH.'modules/system/class_address.php';
require_once DIMS_APP_PATH.'modules/system/class_address_type.php';
switch($action){
	default :
	case 'new_address':
	case 'edit_address':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ajax = dims_load_securvalue('ajax', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($ajax) ob_clean();
		$address = new address();
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address->setLightAttribute('id_ct',$id_ct);
		if($id != '' && $id > 0){
			$address->open($id);
			if($address->isNew() || $address->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$address = new address();
				$address->init_description();
			}
		}else
			$address->init_description();

		$address->setLightAttribute('mode','');
		$address->setLightAttribute('go_parent',0);
		switch ($type) {
			case contact::MY_GLOBALOBJECT_CODE:
				$contact = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(!empty($contact)){
					$address->setLightAttribute('mode','contact');
					$address->setLightAttribute('go_parent',$contact->get('id_globalobject'));
				}
				break;
			case tiers::MY_GLOBALOBJECT_CODE:
				$tiers = tiers::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(!empty($tiers)){
					$address->setLightAttribute('mode','company');
					$address->setLightAttribute('go_parent',$tiers->get('id_globalobject'));
				}
				break;
		}
		$address->setLightAttribute('id_ct',$id_ct);
		if(!$address->isNew()){
			$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/display_mini_address.tpl.php');
		}else{
			$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/edit_address.tpl.php');
		}
		if($ajax) die();
		break;
	case 'get_addresses':
		ob_clean();
		$go = dims_load_securvalue('go',dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if(!empty($go)){
			$lst = address::getAddressesFromGo($go);
			foreach($lst as $address){
				if($address->get("id_workspace") == $_SESSION['dims']['workspaceid']){
					$address->setLightAttribute('id_ct',$id_ct);
					$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/display_address.tpl.php');
				}
			}
		}
		die();
		break;
	case 'view_edit':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address = new address();
		if($id != '' && $id > 0){
			$address->open($id);
			if($address->isNew() || $address->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$address = new address();
				$address->init_description();
				if(defined('_DIMS_DEFAULT_COUNTRY'))
					$address->set('id_country',_DIMS_DEFAULT_COUNTRY);
			}else{
			}
		}else{
			$address->init_description();
			if(defined('_DIMS_DEFAULT_COUNTRY'))
				$address->set('id_country',_DIMS_DEFAULT_COUNTRY);
		}
		$address->setLightAttribute('id_ct',$id_ct);
		$address->setLightAttribute('type',$type);
		$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/edit_address.tpl.php');
		die();
		break;
	case 'save_address':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address = new address();
		if($id != '' && $id > 0){
			$address->open($id);
			if($address->isNew() || $address->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$address = new address();
				$address->init_description();
				$address->setugm();
			}
		}else{
			$address->init_description();
			$address->setugm();
		}
		$address->setvalues($_POST,'adr_');

		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address->setLightAttribute('id_ct',$id_ct);
		$typeObj = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$goCurr = 0;
		$obj_to_link = "";
		$urlRedirect = dims::getInstance()->getScriptEnv()."?submenu=1";
		if($id_ct != '' && $id_ct > 0){
			switch ($typeObj) {
				case contact::MY_GLOBALOBJECT_CODE:
					$contact = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($contact)){
						$goCurr = $contact->get('id_globalobject');
						$obj_to_link = $contact->get('firstname')." ".$contact->get('lastname');
						$urlRedirect .= "&mode=contact&action=show&id=".$contact->get('id');
					}
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$tiers = tiers::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($tiers)){
						$goCurr = $tiers->get('id_globalobject');
						$obj_to_link = $tiers->get('intitule');
						$urlRedirect .= "&mode=company&action=show&id=".$tiers->get('id');
					}
					break;
			}
		}

		$link_to_contacts = dims_load_securvalue('link_to_contacts', dims_const::_DIMS_NUM_INPUT, true, true,true);

		$leven = $address->searchSimilar();
		if(($address->isNew() && !empty($leven)) || (!$address->isNew() && count($leven) > 1)){
			ob_clean();
			$type = dims_load_securvalue('address_type', dims_const::_DIMS_NUM_INPUT, true, true,true);
			$lk_phone = dims_load_securvalue('lk_phone', dims_const::_DIMS_CHAR_INPUT, true, true,true);
			$lk_email = dims_load_securvalue('lk_email', dims_const::_DIMS_CHAR_INPUT, true, true,true);
			$lk_fax = dims_load_securvalue('lk_fax', dims_const::_DIMS_CHAR_INPUT, true, true,true);
			$go_tiers = dims_load_securvalue('go_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);

			$address->setLightAttribute('leven',$leven);
			$address->setLightAttribute('obj_to_link',$obj_to_link);
			$address->setLightAttribute('id_ct',$id_ct);
			$address->setLightAttribute('type',$typeObj);
			$address->setLightAttribute('address_type',$type);
			$address->setLightAttribute('lk_phone',$lk_phone);
			$address->setLightAttribute('lk_email',$lk_email);
			$address->setLightAttribute('lk_fax',$lk_fax);
			$address->setLightAttribute('go_tiers',$go_tiers);
			$address->setLightAttribute('link_to_contacts',$link_to_contacts);

			$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/leven_similar_addresses.tpl.php');
			die();
		}

		if($address->save()){
			if($goCurr > 0){
				$type = dims_load_securvalue('address_type', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$lk = $address->getLinkCt($goCurr);
				if(!empty($lk)){
					$lk->set('id_type',$type);
					$lk->save();
				}else
					$lk = $address->addLink($goCurr,$type);
				$lk->setvalues($_POST,'lk_');
				$lk->save();

				$go_tiers = dims_load_securvalue('go_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
				if($go_tiers != '' && $go_tiers > 0){
					$lk = $address->getLinkCt($go_tiers);
					if(!empty($lk)){
						$lk->set('id_type',$type);
						$lk->save();
					}else
						$lk = $address->addLink($go_tiers,$type);
					//$lk->setvalues($_POST,'lk_');
					//$lk->save();
				}
				if($link_to_contacts){
					$tiers = new tiers();
					$tiers->openWithGB($goCurr);
					if(!$tiers->isNew()){
						$lstCt = $tiers->getAllContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
						foreach($lstCt as $ct){
							$lk = $address->getLinkCt($ct->get('id_globalobject'));
							if(empty($lk)){
								$lk = $address->addLink($ct->get('id_globalobject'),$type);
							}
						}
					}
				}
			}
			?>
			<script type="text/javascript">
				window.location.href='<?= $urlRedirect; ?>';
			</script>
			<?php
		}else{
			$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/edit_address.tpl.php');
		}
		die();
		break;
	case 'save_merge':
		$id = dims_load_securvalue('chose_action', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address = new address();
		if($id != '' && $id > 0){
			$address->open($id);
			if($address->isNew() || $address->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$address = new address();
				$address->init_description();
				$address->setugm();
				$address->setvalues($_POST,'adr_');
			}else{
				$old_adr = dims_load_securvalue('old_adr', dims_const::_DIMS_NUM_INPUT, true, true,true);
				if($old_adr != '' && $old_adr > 0 && $old_adr != $address->get('id')){
					$old_adr = address::find_by(array('id'=>$old_adr,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($old_adr)){
						$lks = address_link::find_by(array('id_goaddress'=>$old_adr->get('id_globalobject')));
						foreach($lks as $lk){
							$lk->set('id_goaddress',$address->get('id_globalobject'));
						}
						$old_adr->delete();
					}
				}
			}
		}else{
			$address->init_description();
			$address->setugm();
			$address->setvalues($_POST,'adr_');
		}

		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address->setLightAttribute('id_ct',$id_ct);
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);

		$goCurr = 0;
		$obj_to_link = "";
		$urlRedirect = dims::getInstance()->getScriptEnv()."?submenu=1";
		if($id_ct != '' && $id_ct > 0){
			switch ($type) {
				case contact::MY_GLOBALOBJECT_CODE:
					$contact = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($contact)){
						$goCurr = $contact->get('id_globalobject');
						$obj_to_link = $contact->get('firstname')." ".$contact->get('lastname');
						$urlRedirect .= "&mode=contact&action=show&id=".$contact->get('id');
					}
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$tiers = tiers::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($tiers)){
						$goCurr = $tiers->get('id_globalobject');
						$obj_to_link = $tiers->get('intitule');
						$urlRedirect .= "&mode=company&action=show&id=".$tiers->get('id');
					}
					break;
			}
		}

		if($address->save()){
			if($goCurr > 0){
				$type = dims_load_securvalue('address_type', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$lk = $address->getLinkCt($goCurr);
				if(!empty($lk)){
					$lk->set('id_type',$type);
					$lk->save();
				}else
					$lk = $address->addLink($goCurr,$type);
				$lk->setvalues($_POST,'lk_');
				$lk->save();
			}
			$go_tiers = dims_load_securvalue('go_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
			if($go_tiers != '' && $go_tiers > 0){
				$lk = $address->getLinkCt($go_tiers);
				if(!empty($lk)){
					$lk->set('id_type',$type);
					$lk->save();
				}else
					$lk = $address->addLink($go_tiers,$type);
			}
			$link_to_contacts = dims_load_securvalue('link_to_contacts', dims_const::_DIMS_NUM_INPUT, true, true,true);
			if($link_to_contacts){
				$tiers = new tiers();
				$tiers->openWithGB($goCurr);
				if(!$tiers->isNew()){
					$lstCt = $tiers->getAllContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
					foreach($lstCt as $ct){
						$lk = $address->getLinkCt($ct->get('id_globalobject'));
						if(empty($lk)){
							$lk = $address->addLink($ct->get('id_globalobject'),$type);
						}
					}
				}
			}
		}
		dims_redirect($urlRedirect);
		break;
	case 'del_link':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$urlRedirect = dims::getInstance()->getScriptEnv()."?submenu=1";
		$goCurr = 0;
		if($id_ct != '' && $id_ct > 0){
			switch ($type) {
				case contact::MY_GLOBALOBJECT_CODE:
					$contact = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($contact)){
						$goCurr = $contact->get('id_globalobject');
						$urlRedirect .= "&mode=contact&action=show&id=".$contact->get('id');
					}
					break;
				case tiers::MY_GLOBALOBJECT_CODE:
					$tiers = tiers::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($tiers)){
						$goCurr = $tiers->get('id_globalobject');
						$urlRedirect .= "&mode=company&action=show&id=".$tiers->get('id');
					}
					break;
			}
		}

		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$address = address::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($address) && $goCurr > 0){
			$address->delLink($goCurr);
		}
		dims_redirect($urlRedirect);
		break;
}
