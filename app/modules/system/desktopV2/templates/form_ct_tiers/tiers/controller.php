<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_address.php';

switch($action){
	case 'show':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$adr = dims_load_securvalue('adr', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$tiers = new tiers();
		if($id != '' && $id > 0){
			$tiers->open($id);
			if($tiers->isNew() || $tiers->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$tiers = new tiers();
				$tiers->init_description();
			}
		}else
			$tiers->init_description();
		if($tiers->isNew()){
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=new");
		}else{
			$tiers->setLightAttribute('adr',$adr); // permet l'ajout d'une adresse à un tiers nouvellement créé
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/display_tiers.tpl.php');
		}
		break;
	default :
	case 'new':
	case 'edit':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ajax = dims_load_securvalue('ajax', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($ajax) ob_clean();
		$tiers = new tiers();
		if($id != '' && $id > 0){
			$tiers = tiers::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(empty($tiers)){
				$tiers = new tiers();
				$tiers->init_description();
			}else{
				$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
				$is_parent = dims_load_securvalue('is_parent', dims_const::_DIMS_NUM_INPUT, true, true,true);
				if($id_ct != '' && $id_ct > 0){
					require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$lk = tiersct::find_by(array('id_tiers'=>$tiers->get('id'),'id_contact'=>$id_ct, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if((!empty($lk)) && (($df = $lk->get('date_fin')) == 0 || empty($df) || $df > dims_createtimestamp())){
						$tiers->setLightAttribute('function',(($lk->get('function')!='')?$lk->get('function'):'undefined'));
						$tiers->setLightAttribute('id_ct',$id_ct);
					}elseif($is_parent){
						$tiers->setLightAttribute('function','dims_nan_for_parent');
					}
				}elseif($is_parent){
					$tiers->setLightAttribute('function','dims_nan_for_parent');
				}
				if($tiers->get('id_tiers') != '' && $tiers->get('id_tiers') > 0){
					$parent = new tiers();
					$parent->open($tiers->get('id_tiers'));
					$tiers->setLightAttribute('parent',$parent->get('intitule'));
				}
			}
		}else
			$tiers->init_description();
		$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/edit_tiers.tpl.php');
		if($ajax) die();
		break;
	case 'delete':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ct = tiers::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($ct)){
			$ct->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&force_desktop=1&mode=default");
		break;
	case 'view_edit':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$tiers = new tiers();
		if($id != '' && $id > 0){
			$tiers->open($id);
			$tiers->setLightAttribute('function','dims_nan');
			if($tiers->isNew() || $tiers->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$tiers = new tiers();
				$tiers->init_description();
			}else{
				$is_parent = dims_load_securvalue('is_parent', dims_const::_DIMS_NUM_INPUT, true, true,true);
				if($id_ct != '' && $id_ct > 0 && $type == contact::MY_GLOBALOBJECT_CODE){
					$contact = new contact();
					$contact->open($id_ct);
					if(!$contact->isNew() && $contact->get('id_workspace') == $_SESSION['dims']['workspaceid']){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$lk = tiersct::find_by(array('id_tiers'=>$tiers->get('id'),'id_contact'=>$contact->get('id'), 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
						if(!empty($lk)){
							$tiers->setLightAttribute('function',(($lk->get('function')!='')?$lk->get('function'):'dims_nan'));
						}
					}
				}
			}
		}else
			$tiers->init_description();
		$tiers->setLightAttribute('id_ct',$id_ct);
		$tiers->setLightAttribute('type',$type);
		if($tiers->isNew()){
			$tiers->set('intitule',dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, true, true,true));
		}
		$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/edit_tiers.tpl.php');
		die();
		break;
	case 'detach_contact': // TODO : vérifier les id_workspaces
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$tiers = new tiers();
		$tiers->open($id);
		if($id != '' && $id > 0 && $id_ct != "" && $id_ct > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id,'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$lk->set('date_fin',dims_createtimestamp());
				$lk->save();
			}
		}
		/*$is_parent = dims_load_securvalue('is_parent', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($is_parent){
			$tiers->setLightAttribute('function','dims_nan_for_parent');
			$tiers->setLightAttribute('id_ct',$id_ct);
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/display_tiers.tpl.php');
		}*/
		die();
		break;
	case 'save':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$tiers = new tiers();
		$TestIsNew = false;
		$oldIdTiers = null;
		if($id != '' && $id > 0){
			$tiers->open($id);
			if($tiers->isNew() || $tiers->get('id_workspace') != $_SESSION['dims']['workspaceid']){
				$tiers = new tiers();
				$tiers->init_description();
				$tiers->setugm();
				$TestIsNew = true;
			}else{
				$oldIdTiers = $tiers->get('id_tiers');
			}
		}else{
			$tiers->init_description();
			$tiers->setugm();
			$TestIsNew = true;
		}
		$tiers->setvalues($_POST, 'tiers_');
		$tiers->setvalues($_POST, 'dyn_');
		if($tiers->save()){
			$id_ent = $tiers->getId();
			require_once(DIMS_APP_PATH.'modules/system/crm_public_ent_add_photo.php');

			require_once(DIMS_APP_PATH.'modules/system/class_tag.php');
			require_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');

			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true);
			if(empty($tags)) $tags = array();
			$myTags = $tiers->getMyTags();
			foreach($myTags as $t){
				if(in_array($t->get('id'), $tags)){
					unset($tags[array_search($t->get('id'), $tags)]);
				}else{
					$lk = new tag_globalobject();
					$lk->openWithCouple($t->get('id'),$tiers->get('id_globalobject'));
					if(!$lk->isNew())
						$lk->delete();
				}
			}
			if(!empty($tags)){
				foreach($tags as $t){
					$lk = new tag_globalobject();
					$lk->init_description();
					$lk->set('id_tag',$t);
					$lk->set('id_globalobject',$tiers->get('id_globalobject'));
					$lk->set('timestp_modify',dims_createtimestamp());
					$lk->save();
				}
			}

			$addresses = dims_load_securvalue('addresses',dims_const::_DIMS_NUM_INPUT, true, true,true);
			$address = new address();
			if($addresses != '' && $addresses > 0){
				$address->open($addresses);
				if(!$address->isNew() && $address->get('id_workspace') == $_SESSION['dims']['workspaceid'] && is_null($address->getLinkCt($tiers->get('id_globalobject'))))
					$address->addLink($tiers->get('id_globalobject'));
			}

			$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_NUM_INPUT, true, true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT, true, true,true);
			if($id_ct != '' && $id_ct > 0){
				switch ($type) {
					case contact::MY_GLOBALOBJECT_CODE:
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$lk = tiersct::find_by(array('id_tiers'=>$tiers->get('id'),'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
						if(empty($lk)){
							$lk = new tiersct();
							$lk->init_description();
							$lk->set('id_tiers', $tiers->get('id'));
							$lk->set('id_contact', $id_ct);
							$lk->set('link_level', 2);
							$lk->set('date_deb', dims_createtimestamp());
							$lk->set('type_lien', $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']);
						}
						$lk->set('date_fin', 0);
						$lk->set('function',dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT, true, true,true));
						$lk->save();
						$tiers->setLightAttribute('function',(($lk->get('function')!='')?$lk->get('function'):'undefined'));
						$tiers->setLightAttribute('id_ct',$id_ct);

						$ct = new contact();
						$ct->open($id_ct);
						if(is_null($address->getLinkCt($ct->get('id_globalobject'))))
							$address->addLink($ct->get('id_globalobject'));

						$supp = "";
						if(empty($id_ct) && isset($_POST['id_ct'])){
							$supp = "&adr=".$tiers->get('id');
						}
						dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$id_ct.$supp);
						break;
					case tiers::MY_GLOBALOBJECT_CODE:
						dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_ct);
						break;
				}
			} else {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$tiers->get('id'));
			}

			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/display_tiers.tpl.php');
		} else {
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=new");
		}
		break;
	case 'search_tiers':
		ob_clean();
		$label = trim(dims_load_securvalue('label_search_tiers',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($label != ''){
			require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
			$dimsearch = new search(dims::getInstance());
			$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_TIERS,$_SESSION['cste']['_DIMS_LABEL_ENTERPRISES']);
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

				$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true);
				$text_attach = "";
				if($id_ct != '' && $id_ct > 0){
					$ct = contact::find_by(array('id'=>$id_ct, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(!empty($ct)){
						$text_attach = $ct->get('firstname')." ".$ct->get('lastname');
						$lstLink = $ct->getAllCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR');
						$lstAlready = array();
						foreach($lstLink as $lk){
							unset($ids[$lk->get('id_globalobject')]);
						}
					}
				}
				if(count($ids)){
					$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true);
					$whereTiers = "";
					if($id_tiers != '' && $id_tiers > 0){
						$tiers = tiers::find_by(array('id'=>$id_tiers,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
						if(!empty($tiers)){
							$whereTiers = " AND 	id_tiers != :idtiers ";
							$params[':idtiers'] = array('value'=>$id_tiers, 'type'=>PDO::PARAM_INT);

							$sel = "SELECT 		DISTINCT id_tiers
									FROM 		".tiers::TABLE_NAME."
									WHERE 		id_tiers > 0
									AND 		id_workspace = :idw
									GROUP BY 	id_tiers";
							$lstNot = array($id_tiers=>$id_tiers);
							$res = $db->query($sel,array(':idw'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
							while($r = $db->fetchrow($res)){
								$lstNot[$r['id_tiers']] = $r['id_tiers'];
							}
							$whereTiers .= " AND id NOT IN (".$db->getParamsFromArray($lstNot,'idtiers2',$params).") ";

							$text_attach = $tiers->get('intitule');
						}
					}
					$sel = "SELECT		*
							FROM		".tiers::TABLE_NAME."
							WHERE		id_globalobject IN (".$db->getParamsFromArray($ids, 'idtiers', $params).")
							AND			inactif = 0
							$whereTiers
							AND 		id_workspace = :idw
							ORDER BY 	intitule ASC";
					$params[':idw'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);
					$res = $db->query($sel, $params);
					?>
					<div class="similar-address" style="padding-top: 5px;">
						<?php
						while ($r = $db->fetchrow($res)){
							$t = new tiers();
							$t->openWithFields($r);
							$t->setLightAttribute('text_attach',$text_attach);
							$t->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/result_tiers.tpl.php');
						}
						?>
						<div style="margin-top:10px;">
							<input type="button" value="<?= $_SESSION['cste']['_CREATE_NEW_COMPANY']; ?>" class="add_tiers submit" />
							<?= $_SESSION['cste']['_DIMS_OR']; ?>
							<a href="javascript:void(0);" class="undo">
								<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
							</a>
						</div>
					</div>
					<script type="text/javascript">
						$(document).ready(function(){
							$('div#add_tiers a.addCompanyFromSearch').click(function(){
								if($(this).attr('dims-data-value') != undefined){
									var elem = $(this);
									$.ajax({
										type: "POST",
										url: '<?= dims::getInstance()->getScriptenv(); ?>',
										data: {
											'submenu': '1',
											'mode': 'company',
											'action' : 'get_form_lk_ct',
											'id' : elem.attr('dims-data-value'),
											'id_ct': '<?= $id_ct; ?>',
											'id_tiers': '<?= $id_tiers; ?>',
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
							<?= str_replace('{DIMS_TEXT}', '<b>'.$label.'</b>', $_SESSION['cste']['_NO_COMPANY_MATCHING_WAS_FOUND']); ?>
						</div>
						<input type="button" value="<?= $_SESSION['cste']['_CREATE_NEW_COMPANY']; ?>" class="add_tiers submit" />
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
						<?= str_replace('{DIMS_TEXT}', '<b>'.$label.'</b>', $_SESSION['cste']['_NO_COMPANY_MATCHING_WAS_FOUND']); ?>
					</div>
					<input type="button" value="<?= $_SESSION['cste']['_CREATE_NEW_COMPANY']; ?>" class="add_tiers submit" />
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
	case 'get_form_lk_ct':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true);
		if($id != '' && $id > 0){
			if($id_ct != '' && $id_ct > 0){
				require_once DIMS_APP_PATH.'modules/system/desktopV2/templates/form_ct_tiers/tiers/construct_form_link_ct.tpl.php';
			}elseif($id_tiers != '' && $id_tiers > 0){
				// on fait directement le lien avec le tiers
				$tiers = tiers::find_by(array('id'=>$id, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				$parent = tiers::find_by(array('id'=>$id_tiers, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(!empty($tiers) && !empty($parent)){
					$tiers->set('id_tiers',$parent->get('id'));
					$tiers->save();
				}
				?>
				<script type="text/javascript">
				document.location.href='<?= dims::getInstance()->getScriptenv()."?submenu=1&mode=company&action=show&id=".$id_tiers; ?>';
				</script>
				<?php
			}
		}
		die();
		break;
	case 'detach_tiers':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_ct != '' && $id_ct > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id,'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id);
		break;
	case 'edit_link':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_ct != '' && $id_ct > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id,'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$lk->setLightAttribute('mode','company');
				$lk->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/edit_link_ct_tiers.tpl.php');
			}
		}
		die();
		break;
	case 'save_link':
		$id = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_ct != '' && $id_ct > 0){
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			$lk = tiersct::find_by(array('id_tiers'=>$id,'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id);
		break;
	case 'remove_link':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_ct != '' && $id_ct > 0){
			$ct = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			$tiers = tiers::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
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
			$lk = tiersct::find_by(array('id_tiers'=>$id,'id_contact'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($lk)){
				$lk->delete();
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id);
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
			$lk->set('function',trim(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT, true, true,true)));
			if($lk->get('function') == "dims_nan"){
				$lk->set('function',trim(dims_load_securvalue('bis_function',dims_const::_DIMS_CHAR_INPUT, true, true,true)));
			}
			$lk->save();
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=$id_tiers");
		}else
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=new");
		break;
	case 'remove_file':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0){
			$ct = tiers::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_ct."#doc");
		break;
	case 'add_file':

		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_tiers != '' && $id_tiers > 0){
			$tiers = tiers::find_by(array('id'=>$id_tiers,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($tiers)){
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
							$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_tiers."#doc");
		break;

	case 'edit_todo':
		ob_clean();
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		require_once DIMS_APP_PATH.'include/class_todo.php';
		if($id != '' && $id > 0){
			$todo = todo::find_by(array('id_globalobject'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(empty($todo)){
				$todo = new todo();
				$todo->init_description();
			}
		}else{
			$todo = new todo();
			$todo->init_description();
		}
		$todo->setLightAttribute('id_ct',$id_ct);
		$todo->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=add_todo&id_ct=".$id_ct);
		$todo->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_ct);
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_ct."#todo");
		break;
	case 'add_todo':
		$id_ct = dims_load_securvalue('id_ct', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id_ct != '' && $id_ct > 0){
			$ct = tiers::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id_ct."#todo");
		break;
	case 'remove_service':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true,true);
		if($id != '' && $id > 0 && $id_tiers != '' && $id_tiers > 0){
			$tiers = tiers::find_by(array('id'=>$id_tiers,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			$parent = tiers::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($tiers) && !empty($parent) && $parent->get('id') == $tiers->get('id_tiers')){
				$tiers->set('id_tiers',0);
				$tiers->save();
				require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
				$matrices = matrix::find_by(array(	'id_tiers'=>$tiers->get('id_globalobject'),
													'id_tiers2'=>$parent->get('id_globalobject'),
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
				$matrices = matrix::find_by(array(	'id_tiers2'=>$tiers->get('id_globalobject'),
													'id_tiers'=>$parent->get('id_globalobject'),
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
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id);
		break;

	case 'add_tmp_tag':
		include_once(DIMS_APP_PATH.'modules/system/class_tag.php');
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ct = tiers::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		$tag = tag::find_by(array('id'=>$id_tag,'id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION),null,1);
		if(!empty($ct) && !empty($tag)){
			$year = dims_load_securvalue('year',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$month = dims_load_securvalue('month',dims_const::_DIMS_NUM_INPUT,true,true,true);
			include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			$m = matrix::find_by(array(
				'id_tiers'=>$ct->get('id_globalobject'),
				'id_tag'=>$tag->get('id'),
				'year'=>$year,
				'id_workspace'=>$_SESSION['dims']['workspaceid'],
			),null,1);
			if(empty($m)){
				$m = new matrix();
				$m->init_description();
				$m->set('id_workspace',$_SESSION['dims']['workspaceid']);
				$m->set('id_tiers',$ct->get('id_globalobject'));
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id);
		break;
	case 'del_tmp_tag':
		include_once(DIMS_APP_PATH.'modules/system/class_tag.php');
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true,true);
		$ct = tiers::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		$tag = tag::find_by(array('id'=>$id_tag,'id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION),null,1);
		if(!empty($ct) && !empty($tag)){
			$year = dims_load_securvalue('year',dims_const::_DIMS_NUM_INPUT,true,true,true);
			include_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			$m = matrix::find_by(array(
				'id_tiers'=>$ct->get('id_globalobject'),
				'id_tag'=>$tag->get('id'),
				'year'=>$year,
				'id_workspace'=>$_SESSION['dims']['workspaceid'],
			),null,1);
			if(!empty($m)){
				$m->delete();
			}
			$m = matrix::find_by(array(
				'id_tiers'=>$ct->get('id_globalobject'),
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
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$id);
		break;
}
