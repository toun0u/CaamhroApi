<?php defined('AUTHORIZED_ENTRY_POINT') or exit;
dims_init_module('system');
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once(DIMS_APP_PATH . '/modules/system/include/business.php');
//require_once(DIMS_APP_PATH . '/modules/system/intervention/controller_op_intervention.php');

$dims_op=dims_load_securvalue('dims_op',dims_const::_DIMS_CHAR_INPUT,true,true);

if ($dims_op!="") {
	switch($dims_op) {
		case 'verif_similar_pers':
			ob_start();
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_verif_similar.php');
			ob_end_flush();
			die();
			break;
		case 'verif_similar_ent' :
			ob_start();
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_verif_similar.php');
			ob_end_flush();
			die();
			break;
		case 'printBarcode':
			require_once(DIMS_APP_PATH . "/modules/system/crm_desktop_users_etiquettes.php");
			die();
			break;

		case 'import_excel' :
			echo '<img height="25" width="25" src="./common/modules/system/img/loading.gif" >';
			break ;

		case 'chat_actions':
			$haut=dims_load_securvalue('haut',dims_const::_DIMS_NUM_INPUT,true,true);
			$bas=dims_load_securvalue('bas',dims_const::_DIMS_NUM_INPUT,true,true);
			$margin=dims_load_securvalue('margin',dims_const::_DIMS_NUM_INPUT,true,true);
			$send=dims_load_securvalue('send',dims_const::_DIMS_NUM_INPUT,true,true);
			if (!empty($send) && $send > 0)
				$msg = trim(dims_load_securvalue('msg_send_'.$send,dims_const::_DIMS_CHAR_INPUT,true,true));
			$refresh=dims_load_securvalue('refresh',dims_const::_DIMS_NUM_INPUT,true,true);
			$read=dims_load_securvalue('read',dims_const::_DIMS_NUM_INPUT,true,true);
			$msgchat=dims_load_securvalue('msgchat',dims_const::_DIMS_NUM_INPUT,true,true);
			$ferme=dims_load_securvalue('ferme',dims_const::_DIMS_NUM_INPUT,true,true);
			include(DIMS_APP_PATH . '/modules/system/desktop_display_chat_actions.php');

			break ;

		case 'display_bookmarks' :
			ob_clean();
			echo $skin->open_simplebloc('<img src="./common/img/fav1.png" style="border:0px">&nbsp;'.$_DIMS['cste']['_ADDTO_FAVORITES'].'<div style="float:right;"><img src="./common/img/close.png" onclick="javascript:dims_hidepopup();" /></div>','width:100%;','','','','','','','','','');
			$sql = "SELECT	*
				FROM	dims_favorite
				WHERE	id_user = :userid
				AND		type = 2
				AND		id_workspace = :workspaceid ";

			$res = $db->query($sql, array(
				':userid'		=> $_SESSION['dims']['userid'],
				':workspaceid'	=> $_SESSION['dims']['workspaceid']
			));

			$contacts = '';
			$tiers = '';
			$events = '';
			$doc = '';
			$other = '';

			while ($book = $db->fetchrow($res)){
				switch ($book['id_object']) {
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						// on recherche la personne en fonction de l'id du contact
						//$id_user=$user->getUserFromContact($book['id_record']);
						$ct = new contact();
						$ct->open($book['id_record']);

						if ($ct->fields['id']>0) {
							$contacts .= '<div style="float:left;margin-right:5px;"><a href="javascript:void(0)" onclick="javascript:viewPropertiesObject('.dims_const::_SYSTEM_OBJECT_CONTACT.','.$ct->fields['id'].',1,1);dims_hidepopup(\'dims_popup\');">'.$ct->fields['firstname'].' '.$ct->fields['lastname'].'</a></div>';
						}
						break ;
					case dims_const::_SYSTEM_OBJECT_TIERS :
						// on recherche la personne en fonction de l'id du contact
						//$id_user=$user->getUserFromContact($book['id_record']);
						$t = new tiers();
						$t->open($book['id_record']);

						if ($t->fields['id']>0) {
							$tiers .= '<div style="float:left;margin-right:5px;"><a href="javascript:void(0)" onclick="javascript:viewPropertiesObject('.dims_const::_SYSTEM_OBJECT_TIERS.','.$t->fields['id'].',1,1);dims_hidepopup(\'dims_popup\');">'.$t->fields['intitule'].'</a></div>';
						}
						break ;
					case dims_const::_SYSTEM_OBJECT_DOCFILE :
						require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
						$docfile = new docfile();
						$docfile->open($book['id_record']);
						$doc .= '<div style="float:left;margin-right:5px;"><a href="javascript:void(0)" onclick="javascript:viewPropertiesObject('._DOC_OBJECT_FILE.','.$docfile->fields['id'].','.$docfile->fields['id_module'].',1);dims_hidepopup(\'dims_popup\');">'.$docfile->fields['name'].'</a></div>';

						break ;
					case dims_const::_SYSTEM_OBJECT_EVENT :
						require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
						$event = new action();
						$event->open($book['id_record']);
						$events .= '<div style="float:left;margin-right:5px;"><a href="javascript:void(0)" onclick="javascript:viewPropertiesObject('.dims_const::_SYSTEM_OBJECT_EVENT.','.$event->fields['id'].',305,'.$event->fields['id_module'].');dims_hidepopup(\'dims_popup\');'.$event->fields['id'].'">'.$event->fields['libelle'].'</a></div>';
						break ;
				default :
				// gérer les others
						break ;
				}
			}

			if ($contacts == '')
					$contacts = $_DIMS['cste']['_NO_BOOKMARK'];
			if ($tiers == '')
					$tiers = $_DIMS['cste']['_NO_BOOKMARK'];
			if ($events == '')
					$events = $_DIMS['cste']['_NO_BOOKMARK'];;
			if ($doc == '')
					$doc = $_DIMS['cste']['_NO_BOOKMARK'];;
			if ($other == '')
					$other = $_DIMS['cste']['_NO_BOOKMARK'];;

			echo "	<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
							<tr>
									<td width=\"125px;\" valign=\"top\" style=\"font-weight:bold;\">
											".$_DIMS['cste']['_DIMS_LABEL_CONTACTS']." :
									</td>
									<td>
											<div style=\"float:left;width:100%;margin-bottom:10px;\">
													$contacts
											</div>
									</td>
							</tr>
							<tr>
									<td width=\"125px;\" valign=\"top\" style=\"font-weight:bold;\">
											".$_DIMS['cste']['_DIMS_LABEL_ENTERPRISES']." :
									</td>
									<td>
											<div style=\"float:left;width:100%;margin-bottom:10px;\">
													$tiers
											</div>
									</td>
							</tr>
							<tr>
									<td valign=\"top\" style=\"font-weight:bold;\">
											".$_DIMS['cste']['_DIMS_LABEL_EVENTS']." :
									</td>
									<td>
											<div style=\"float:left;width:100%;margin-bottom:10px;\">
													$events
											</div>
									</td>
							</tr>
							<tr>
									<td valign=\"top\" style=\"font-weight:bold;\">
											".$_DIMS['cste']['_DOCS']." :
									</td>
									<td>
											<div style=\"float:left;width:100%;margin-bottom:10px;\">
													$doc
											</div>
									</td>
							</tr>
							<tr>
									<td valign=\"top\" style=\"font-weight:bold;\">
											".$_DIMS['cste']['_DIMS_LABEL_OTHER']."
									</td>
									<td>
											<div style=\"float:left;width:100%;margin-bottom:10px;\">
													$other
											</div>
									</td>
							</tr>
					</table>";

			echo $skin->close_simplebloc();
			die();
			break ;

		case 'adminChangeNewChamp';
			$id_champ = dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true);
			$row = dims_load_securvalue('row',dims_const::_DIMS_CHAR_INPUT,true,true);
			if ($id_champ > 0){
				echo	'<img src="./common/img/clipboard/close.png"/>';
			}else{
				echo '<input type="checkbox" name="create_'.$row.'"/>';
			}

			die();
			break;

		case 'adminChangeType' :
			$id_champ = dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true);
			$row = dims_load_securvalue('row',dims_const::_DIMS_CHAR_INPUT,true,true);

			require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_fct.php');

			if ($id_champ > 0){

				foreach ($rubgen as $label => $list) {
					if (isset($list['list'][$id_champ])){
						echo $field_types[$list['list'][$id_champ]['type']];
					}
				}
			}else{
				echo '<select name="typefield_'.$row.'" id="typefield_'.$row.'">';
				foreach ($field_types as $t => $v){
					if ($t == $_SESSION['dims']['importform']['typecol'][$row])
					echo '<option value="'.$t.'" selected>'.$v.'</option>';
					else
					echo '<option value="'.$t.'">'.$v.'</option>';
				}
				echo '</select>';
			}

			die() ;
			break ;

		case 'adminChangeFormat' :
			$id_champ = dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true);
			$row = dims_load_securvalue('row',dims_const::_DIMS_CHAR_INPUT,true,true);

			require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_fct.php');

			if ($id_champ > 0){
				switch ($label['list'][$id_champ]['format']){
					case 'int' :
						$format = $_DIMS['cste']['_DIMS_LABEL_INT'];
						break ;
					case 'float' :
						$format = $_DIMS['cste']['_DIMS_LABEL_FLOAT'];
						break ;
					case 'date' :
						$format = $_DIMS['cste']['_DIMS_LABEL_DATE'];
						break ;
					case 'string' :
						default :
						$format = $_DIMS['cste']['_DIMS_LABEL_STRING'];
						break ;
				}
				echo $format;
			}else{
				echo		'<select name="formatfield_'.$row.'" id="formatfield_'.$row.'">';
				$sel = array(0=>"",1=>"",2=>"",3=>"");
				switch ($_SESSION['dims']['importform']['formatcol'][$row]){
					case 'int' :
					$format = $_DIMS['cste']['_DIMS_LABEL_INT'];
					$sel[0] = " selected";
					break ;
					case 'float' :
					$format = $_DIMS['cste']['_DIMS_LABEL_FLOAT'];
					$sel[1] = " selected";
					break ;
					case 'date' :
					$format = $_DIMS['cste']['_DIMS_DATE'];
					$sel[2] = " selected";
					break ;
					case 'string' :
					default :
					$format = $_DIMS['cste']['_DIMS_LABEL_STRING'];
					$sel[3] = " selected";
					break ;
				}

				echo			'<option value="int"'.$sel[0].'>'.$_DIMS['cste']['_DIMS_LABEL_INT'].'</option>';
				echo			'<option value="float"'.$sel[1].'>'.$_DIMS['cste']['_DIMS_LABEL_FLOAT'].'</option>';
				echo			'<option value="date"'.$sel[2].'>'.$_DIMS['cste']['_DIMS_DATE'].'</option>';
				echo			'<option value="string"'.$sel[3].'>'.$_DIMS['cste']['_DIMS_LABEL_STRING'].'</option>';
				echo		'</select>';
			}

			die() ;
			break ;

		case 'adminChangeCateg' :
			$id_champ = dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true);
			$row = dims_load_securvalue('row',dims_const::_DIMS_CHAR_INPUT,true,true);

			require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_import_fct.php');

			if ($id_champ > 0){
				foreach ($rubgen as $label => $list) {
					if (isset($list['list'][$id_champ])){
						$_SESSION['dims']['importform']['label'][$row] = $label;
						$_SESSION['dims']['importform']['generic'][$row] = $id_champ;
						echo	$list['label'];
					}
				}

			}
			else{
				unset($_SESSION['dims']['importform']['label'][$row]);
				unset($_SESSION['dims']['importform']['generic'][$row]);

				echo		'<select name="catfield_'.$row.'" id="catfield_'.$row.'">';
				foreach ($rubgen as $label => $v){
					echo		'<option value="'.$label.'">'.$v['label'].'</option>';
				}
				echo		'</select>';
			}
			die();
			break ;

		case 'syncemails':

			require_once(DIMS_APP_PATH . "/modules/system/op_syncemails.php");
			die();
			break;

		case 'object_edit_tag' :
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true,true,true);

			if (!empty($id) && $id > 0){

				require_once(DIMS_APP_PATH . "/modules/system/class_tag.php");
				$objtag = new tag();
				$objtag->open($id);
				echo $skin->open_simplebloc($objtag->fields['tag']);
				echo '<form action="./admin.php" method="POST" name="create_tag">';
				echo '<input type="hidden" name="dims_op" value="object_edit_tag">';
				echo '<input type="hidden" name="id_tag" value="'.$id.'">';
				echo '<table cellspacing="2" cellpadding="0" width="100%">';
				echo '	<tr>
								<td>
										'.$_DIMS['cste']['_DIMS_LABEL'].'
						</td><td>
							&nbsp;<input type="text" id="label_tag" name="label_tag" value="'.$objtag->fields['tag'].'"  style="width:220px">
								<td>
								</td>
						</tr>
				<tr>
								<td>
										'.$_DIMS['cste']['_TYPE'].'
						</td><td>';

						$tabtype[0]=$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'];
						$tabtype[1]=$_DIMS['cste']['_DIMS_LABEL_CONTACT_GOUPS'];
						$tabtype[2]=$_DIMS['cste']['_DIMS_LABEL_ENT_SECTACT'];

						echo '<select name="typetag">';
						foreach ($tabtype as $idt =>$lab) {
							$selected = ($objtag->fields['type']==$idt) ? 'selected' : '';
							echo '<option '.$selected.' value="'.$idt.'">'.$lab.'</option>';
						}
								echo '</select><td>
								</td>
						</tr>
						<tr>
								<td>
										'.$_DIMS['cste']['_PRIVATE'].'
								</td>
								<td>';

				if (!$objtag->fields['private'])
					echo '<input type="checkbox" name="private_tag">';
				else
					echo '<input type="checkbox" name="private_tag" checked="checked">';
				echo		'</td>
						</tr>';
				echo '</table>';
				echo '</form>';
				echo '<form action="./admin.php" method="POST" name="delete_tag">
						<input type="hidden" name="id_tag" value="'.$id.'">
						<input type="hidden" name="dims_op" value="object_delete_tag">

					  </form>'; //<input type="hidden" name="typetag" value="'.$objtag->fields['type'].'">
				echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:document.create_tag.submit();",'','');
				echo dims_create_button($_DIMS['cste']['_DELETE'],"trash","javascript:dims_hidepopup();javascript:document.delete_tag.submit();");
				echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"closethick","javascript:dims_hidepopup();");

				//echo '<input class="flatbutton" type="submit" onclick="javascript:document.create_tag.submit();" value="'.$_DIMS['cste']['_DIMS_SAVE'].'" >';
				//echo '<input class="flatbutton" type="button" onclick="javascript:dims_hidepopup();" value="'.$_DIMS['cste']['_DIMS_CLOSE'].'" >';
				echo "<script language=\"JavaScript\" type=\"text/JavaScript\">$('#label_tag').focus();</script>";
				//echo '<input class="flatbutton" type="submit" onclick="javascript:document.edit_tag.submit();" value="'.$_DIMS['cste']['_DIMS_SAVE'].'" >';
				//echo '<input class="flatbutton" type="button" onclick="javascript:dims_hidepopup();" value="'.$_DIMS['cste']['_DIMS_CLOSE'].'" >';
				//echo '<input class="flatbutton" type="button" onclick="javascript:dims_hidepopup();javascript:document.delete_tag.submit();" value="'.$_DIMS['cste']['_DELETE'].'" >';
				echo $skin->close_simplebloc();
				die();
			}else{
				$label = dims_load_securvalue('label_tag', dims_const::_DIMS_CHAR_INPUT, true,true,true);
				$id = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true,true,true);
				$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true,true,true);
				if (trim($label) != ''){
						require_once(DIMS_APP_PATH . "/modules/system/class_tag.php");
						$objtag = new tag();
						$objtag->open($id);
						$objtag->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$objtag->fields['id_user'] = $_SESSION['dims']['userid'];
						$objtag->fields['tag']=trim($label);
						$objtag->fields['type']=$typetag;
						$private = dims_load_securvalue('private_tag', dims_const::_DIMS_CHAR_INPUT, true,true,true);
						if ($private == 'on')
								$objtag->fields['private']=1;
						$objtag->save();
						$_SESSION['dims']['save_typetag'] = $objtag->fields['type'];
				}
			}
			break;
		case 'object_delete_tag' :
			$id = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true,true,true);
			$type = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true,true,true);
			if (!empty($id) && $id > 0){
				$sql = "DELETE FROM	dims_tag WHERE id= :id ";
				$db->query($sql, array(
					':id' => $id
				));
			}
			$_SESSION['dims']['save_typetag'] = $type;
			break ;
		case 'object_create_tag':
				ob_clean();
				$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true,true,true);
				if ($type != ''){
						echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_TAG']);
						echo '<form action="./admin.php" method="POST" name="create_tag">';

						echo '<input type="hidden" name="dims_op" value="object_create_tag">';
						echo '<br><table cellspacing="2" cellpadding="0" width="100%">';
						echo '	<tr>
										<td>
												'.$_DIMS['cste']['_DIMS_LABEL'].'
										</td>

										<td>
								&nbsp;<input type="text" name="label_tag" id="label_tag"  style="width:220px">
										</td>
								</tr>
								<tr>
										<td>
										'.$_DIMS['cste']['_TYPE'].'
						</td><td>';

						$tabtype[0]=$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'];
						$tabtype[1]=$_DIMS['cste']['_DIMS_LABEL_CONTACT_GOUPS'];
						$tabtype[2]=$_DIMS['cste']['_DIMS_LABEL_ENT_SECTACT'];

						echo '<select name="typetag">';
						foreach ($tabtype as $idt =>$lab) {
							$selected = ($type==$idt) ? 'selected' : '';
							echo '<option '.$selected.' value="'.$idt.'">'.$lab.'</option>';
						}
								echo '</select><td>
								</td>
						</tr>
								<tr>
										<td>
												'.$_DIMS['cste']['_PRIVATE'].'
										</td>
										<td>
												<input type="checkbox" name="private_tag">
										</td>
								</tr>';
						echo '</table>';
						echo '</form>';
						echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:document.create_tag.submit();",'','');
						echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"closethick","javascript:dims_hidepopup();");

						//echo '<input class="flatbutton" type="submit" onclick="javascript:document.create_tag.submit();" value="'.$_DIMS['cste']['_DIMS_SAVE'].'" >';
						//echo '<input class="flatbutton" type="button" onclick="javascript:dims_hidepopup();" value="'.$_DIMS['cste']['_DIMS_CLOSE'].'" >';
						echo "<script language=\"JavaScript\" type=\"text/JavaScript\">$('#label_tag').focus();</script>";
						echo $skin->close_simplebloc();
						die();
				}else{

						$label = dims_load_securvalue('label_tag', dims_const::_DIMS_CHAR_INPUT, true,true,true);
						$type = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true,true,true);
						if (trim($label) != ''){

								require_once(DIMS_APP_PATH . "/modules/system/class_tag.php");
								$objtag = new tag();
								$search=array();
								$search['tag']=trim($label);
								$search['type']=$type;
								$search['id_workspace']=$_SESSION['dims']['workspaceid'];
								$resultat=$objtag->search($search);

								if ($resultat['count']==0) {
								$objtag->fields['type']= $type;
								$objtag->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
								$objtag->fields['id_user'] = $_SESSION['dims']['userid'];
								$objtag->fields['tag']=trim($label);
								$private = dims_load_securvalue('private_tag', dims_const::_DIMS_CHAR_INPUT, true,true,true);
								if ($private == 'on')
										$objtag->fields['private']=1;
								$objtag->save();

						}
								else {
									$idt=0;
									foreach ($resultat as $k =>$val) {
										if ($idt==0) $idt=$val;
									}

									$objtag->open($val);
								}

									require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
									$action = new dims_action(/*$db*/);
									$action->fields['id_parent']=0;
									$action->fields['timestp_modify']= dims_createtimestamp();
									$action->fields['id_parent']=0;
								$action->setModule(1);
									$action->setWorkspace($_SESSION['dims']['workspaceid']);
									$action->setUser($_SESSION['dims']['userid']);
									$action->fields['comment']= '_DIMS_LABEL_TAG_CREATED';
									$action->fields['type'] = dims_const::_ACTION_TAG; // link


									$link_title=$label;
									//if (isset($_SESSION['dims']['current_object']['label']) && $_SESSION['dims']['current_object']['label']!='') {
									//		$link_title=$_SESSION['dims']['current_object']['label'];
									//}
									$action->addObject(0, 1, dims_const::_SYSTEM_OBJECT_TAG, $objtag->fields['id'],$link_title);

									// ajout des tags
									$action->setTags(array($new_tag));
									$action->save();

								require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
								$ctgrl = new tag_index();
								$ctgrl->init_description();
								$ctgrl->fields['id_record'] = $objtag->fields['id'];
								$ctgrl->fields['id_object'] = dims_const::_SYSTEM_OBJECT_TAG;
								$ctgrl->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctgrl->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctgrl->fields['id_tag'] = $objtag->fields['id'];
								$ctgrl->fields['id_module'] = 1;
								$ctgrl->fields['id_module_type'] = 1;
								$ctgrl->save();
									//dims_print_r($action);die();

						}
						$_SESSION['dims']['save_typetag'] = $type;
						dims_redirect('/admin.php');
				}
				break ;

		case 'preview':
		case dims_const::_DIMS_SUBMENU_PREVIEW:
			require_once(DIMS_APP_PATH . '/modules/system/desktop_detail_event.php');
			break;
		case 'add_tag':
			require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');
			$id_record=dims_load_securvalue('id_record',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$id_module=dims_load_securvalue('id_module',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$id_object=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$typetag=dims_load_securvalue('typetag',dims_const::_DIMS_NUM_INPUT,true,true,false);

			require_once(DIMS_APP_PATH.'modules/system/class_module.php');
			$currentmod= new module();
			$currentmod->open($id_module);

			$old_list=array();
			$new_tag=array();
			$res=$db->query("SELECT id_tag
							FROM dims_tag_index
							WHERE id_record= :idrecord
							AND id_module= :idmodule
							AND id_object= :idobject
							AND id_tag IN (
											SELECT id
											FROM dims_tag
											WHERE type= :type
											)", array(
					':idrecord'	=> $id_record,
					':idmodule'	=> $id_module,
					':idobject'	=> $id_object,
					':type'		=> $typetag
			));

			while ($t=$db->fetchrow($res)) {
				$old_list[$t['id_tag']]=$t['id_tag'];
			}

			$res=$db->query("DELETE from dims_tag_index
							WHERE id_record= :idrecord
							AND id_module= :idmodule
							AND id_object= :idobject
							AND id_tag in (
											SELECT id
											FROM dims_tag
											WHERE type= :type
											)", array(
					':idrecord'	=> $id_record,
					':idmodule'	=> $id_module,
					':idobject'	=> $id_object,
					':type'		=> $typetag
			));

			if (isset($_POST['cttoadd'])) $arraytag= dims_load_securvalue('cttoadd', dims_const::_DIMS_NUM_INPUT, true, true, true);
			else $arraytag=array();

			foreach($arraytag as $k=>$idrec) {
				$ctgrl = new tag_index();
				$ctgrl->init_description();
				$ctgrl->fields['id_record'] = $id_record;
				$ctgrl->fields['id_object'] = $id_object;
				$ctgrl->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$ctgrl->fields['id_user'] = $_SESSION['dims']['userid'];
				$ctgrl->fields['id_tag'] = $idrec;
				$ctgrl->fields['id_module'] = $id_module;
				$ctgrl->fields['id_module_type'] = $currentmod->fields['id_module_type'];
				$ctgrl->save();

				if (!isset($old_list[$idrec])) {
					$new_tag[$idrec]=$idrec;
				}
			}

			unset($_SESSION['dims']['temp_tag']);
			if (!empty($new_tag)) {

				// on met à jour les actions qui sont sur le mur
				// si on a rien sur la personne on crée une action de tag
				//$user = new user();
				//$user->open($_SESSION['dims']['userid']);
				//$user->updateActionbyTag($id_object,$id_record,$id_module,$arraytag);
				require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
				$action = new dims_action(/*$db*/);
				$action->fields['id_parent']=0;
				$action->fields['timestp_modify']= dims_createtimestamp();
				$action->fields['id_parent']=0;
				$action->setModule($id_module);
				$action->setWorkspace($_SESSION['dims']['workspaceid']);
				$action->setUser($_SESSION['dims']['userid']);
				$action->fields['comment']= '_DIMS_LABEL_TAG_CREATED';
				$action->fields['type'] = dims_const::_ACTION_TAG; // link

				$link_title='';
				if (isset($_SESSION['dims']['current_object']['label']) && $_SESSION['dims']['current_object']['label']!='') {
						$link_title=$_SESSION['dims']['current_object']['label'];
				}
				$action->addObject(0, $id_module, $id_object, $id_record,$link_title);

				// ajout des tags
				$action->setTags($new_tag);
				$action->save();
			}

			$url=$_SERVER['QUERY_STRING'];
			if ($url=='' && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!='') {
				$url=$_SERVER['HTTP_REFERER'];
			}

			$_SESSION['dims']['save_typetag'] = $typetag;
			if (!strpos($url,'refreshDesktop')) dims_redirect("/admin.php?".$url);
			else dims_redirect("/admin.php");
		break;
		case 'socialbrowser_one':
			require_once(DIMS_APP_PATH . "/modules/system/crm_public_contact_graph_xml_old.php");
			break;
		case 'socialbrowser':
			$xml_id=dims_load_securvalue('xml_id', dims_const::_DIMS_CHAR_INPUT, true);

			$explorecontact=dims_load_securvalue('explorercontact', dims_const::_DIMS_NUM_INPUT, true);

			if (substr($xml_id,0,3)=="ct_" || substr($xml_id,0,4)==")ct_") {
				//$xml_id=str_replace(")","",$xml_id);
				$iscontact=true;
				$contact_id=str_replace("ct_","",$xml_id);
			}
			if ($_SESSION['dims']['userid']==$xml_id || $explorecontact) {
				require_once(DIMS_APP_PATH . "/modules/system/crm_public_contact_graph_xml_old.php");
			}
			else {
				require_once(DIMS_APP_PATH . "/modules/system/crm_public_contact_graph_xml.php");
			}
			//require_once(DIMS_APP_PATH . "/modules/system/crm_public_contact_graph_xml_old2.php");
			break;
		/*
		case 'events_xsd':
		case 'events':
			require_once(DIMS_APP_PATH . "/modules/system/public_events.php");
			break;
		 */
		case 'newsletter_xsd':
		case 'newsletter':
			require_once(DIMS_APP_PATH . "/modules/system/public_newsletter.php");
			break;
		case 'xml_desktop_lstplanning':
			require_once(DIMS_APP_PATH . "/modules/system/desktop_planning.php");
			die();
			break;

			case 'xml_desktop_planning_month':
			require_once(DIMS_APP_PATH . "/modules/system/desktop_planning_month.php");
			die();
			break;
		case 'reset_currentobject':
			if (isset($_SESSION['dims']['current_object'])) {
				unset($_SESSION['dims']['current_object']);
			}
			break;
		case 'object_detail_properties':
			// generation du contenu par unoconv
			switch($idobject) {
				case dims_const::_SYSTEM_OBJECT_ACTION:
					$obj=new action();
					$obj->open($idrecord);
					break;
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$obj=new contact();
					$obj->open($idrecord);
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$obj=new tiers();
					$obj->open($idrecord);
					break;
			}
		break;
		case 'object':
		case 'object_properties':
		case 'refreshDesktop':

			$moduleid=$_SESSION['dims']['current_object']['id_module'];
			$objectid=$_SESSION['dims']['current_object']['id_object'];
			$recordid=$_SESSION['dims']['current_object']['id_record'];

			//ob_start();
			switch($objectid) {
				case dims_const::_SYSTEM_OBJECT_ACTION:
				case dims_const::_SYSTEM_OBJECT_EVENT:
					$obj=new action();
					$obj->open($recordid);

					$_SESSION['dims']['current_object']['label']=$obj->fields['libelle'];
					$_SESSION['dims']['current_object']['id_workspace']=$obj->fields['id_workspace'];
					$_SESSION['dims']['current_object']['id_user']=$obj->fields['id_user'];
					$_SESSION['dims']['current_object']['timestp_modify']=$obj->fields['timestp_modify'];

					$_SESSION['dims']['current_object']['cmd']=array();

					// calcul de diff?rence de jour
					$annee = substr($obj->fields['datejour'], 0, 4); // on r?cup?re le jour
					$mois = substr($obj->fields['datejour'], 5, 2); // puis le mois
					$jour = substr($obj->fields['datejour'], 8, 2);

					if (DIMS_DATEFORMAT==dims_const::DIMS_DATEFORMAT_FR)
						$datecumul=$jour."/".$mois."/".$annee;
					else
						$datecumul=$annee."/".$mois."/".$jour;

					$timestamp = mktime(0, 0, 0, $mois, $jour, $annee);
					$maintenant=time();
					$ecart_secondes = $timestamp-$maintenant;
					$ecart=floor($ecart_secondes / (60*60*24));

					$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
					$elem['src']="./common/img/view.png";
					$elem['link']= dims_urlencode("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_desktop=block&dims_action=public&cat=-1&dayadd=".$ecart."&actionid=".$recordid);
					$_SESSION['dims']['current_object']['cmd'][]=$elem;

					// check responsable
					if ($obj->fields['type']==2 && ($obj->fields['id_responsible']==$dims_user->fields['id_contact']
							|| $obj->fields['id_organizer']==$dims_user->fields['id_contact'])) {
						$elem['name']=$_DIMS['cste']['_DIMS_LABEL_EDIT'];
						$elem['src']="./common/img/configure.png";
						$elem['link']= dims_urlencode("/admin.php?dims_mainmenu=0&dims_action=public&submenu=8&action=adm_evt&id_evt=".$recordid);
						$_SESSION['dims']['current_object']['cmd'][]=$elem;
					}
					break;
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$contact=new contact();
					$contact->open($recordid);
					$obj=$contact;

					$_SESSION['dims']['current_object']['label']=$contact->fields['lastname']." ".$contact->fields['firstname'];
					$_SESSION['dims']['current_object']['id_workspace']=$contact->fields['id_workspace'];
					$_SESSION['dims']['current_object']['id_user']=$contact->fields['id_user'];
					$_SESSION['dims']['current_object']['timestp_modify']=$contact->fields['timestp_modify'];

					$_SESSION['dims']['current_object']['cmd']=array();

					$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
					$elem['src']="./common/img/view.png";
					$elem['link']= dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$recordid);
					$_SESSION['dims']['current_object']['cmd'][]=$elem;

					$_SESSION['business']['contact_id']=$recordid;
					$disabledbloc=true;
					require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
					unset($disabledbloc);
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$obj=new tiers();
					$obj->open($recordid);
					$_SESSION['dims']['current_object']['label']=$obj->fields['intitule'];
					$_SESSION['dims']['current_object']['id_workspace']=$obj->fields['id_workspace'];
					$_SESSION['dims']['current_object']['id_user']=$obj->fields['id_user'];
					$_SESSION['dims']['current_object']['timestp_modify']=$obj->fields['timestp_modify'];

					$_SESSION['dims']['current_object']['cmd']=array();

					$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
					$elem['src']="./common/img/view.png";
					//$elem['link']= dims_urlencode("admin.php?dims_moduleid={$moduleid}&dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT."&cat="._BUSINESS_CAT_TIERS."&op=tiersr_ouvrir&tiers_id={$recordid}&dims_moduletabid="._BUSINESS_TAB_TIERSINFORMATIONS);
					$elem['link']= dims_urlencode("admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action=401&id_ent=".$recordid);

										$disabledbloc=true;
					require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_profil.php');
					$_SESSION['dims']['current_object']['cmd'][]=$elem;
					break;
				case dims_const::_SYSTEM_OBJECT_MAIL:
						require_once(DIMS_APP_PATH . '/modules/system/class_webmail_email.php');
						$mail = new webmail_email();
						$mail->open($recordid);
						//dims_print_r($mail->fields);
						$_SESSION['dims']['current_object']['label']= $mail->fields['subject'];
						$_SESSION['dims']['current_object']['id_workspace']=$mail->fields['id_workspace'];
						$_SESSION['dims']['current_object']['id_user']=$mail->fields['id_user'];
						if ($mail->fields['timestp_modify'] == 0)
								$_SESSION['dims']['current_object']['timestp_modify']=$mail->fields['date'];
						else	$_SESSION['dims']['current_object']['timestp_modify']=$mail->fields['timestp_modify'];

						break ;
				// pour les doc ...
				/*case dims_const::_SYSTEM_OBJECT_GROUP:
						$doc = new docfile();
						$doc->open($recordid);
						//dims_print_r($doc->fields);
						$_SESSION['dims']['current_object']['label']= $doc->fields['name'];
						$_SESSION['dims']['current_object']['id_workspace']=$doc->fields['id_workspace'];
						$_SESSION['dims']['current_object']['id_user']=$doc->fields['id_user'];
						$_SESSION['dims']['current_object']['timestp_modify']=$doc->fields['timestp_modify'];
						break ;*/
			}
			$detailobject_description = ob_get_contents();
			ob_end_clean();
		break;

		// case concernant la classe dims_browser
		case 'updateBrowserSelected' :
			ob_clean();
			$lvl = dims_load_securvalue('lvl',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if (isset($_SESSION['dims']['dims_browser'][$name]['selected_id'][$lvl-1])){
				foreach ($_SESSION['dims']['dims_browser'][$name]['selected_id'] as $clef => $val)
					if ($clef > $lvl-1)
						unset($_SESSION['dims']['dims_browser'][$name]['selected_id'][$clef]);
			}
			$_SESSION['dims']['dims_browser'][$name]['selected_lvl'] = $lvl-1;
			$_SESSION['dims']['dims_browser'][$name]['selected_id'][$lvl-1] = $id;
			die();
			break;

		// case concernant l'administration des catégories
		case 'refreshObjects' :
			ob_clean();
			$idModTyp = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$_SESSION['dims']['categFiltre']['module'] = $idModTyp;
			$lstObjects = array();
			$lstObjects[] = "<option value=\"0\">-- Choisissez un objet Dims --</option>";
			if ($idModTyp != '' && $idModTyp > 0){
				$sel = "SELECT	id, label
						FROM	dims_mb_object
						WHERE	id_module_type = :idmoduletype ";
				$res = $db->query($sel, array(
					':idmoduletype' => $idModTyp
				));
				while ($r = $db->fetchrow($res))
					if (isset($_SESSION['dims']['categFiltre']['obj']) && $_SESSION['dims']['categFiltre']['obj'] == $r['id'])
						$lstObjects[] = '<option selected=true value="'.$r['id'].'">'.$r['label'].'</option>';
					else
						$lstObjects[] = '<option value="'.$r['id'].'">'.$r['label'].'</option>';
			}
			$isInModule = dims_load_securvalue('isInModule',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($isInModule == 'modifInModule'){
				?>
				<select onchange="javascript:refreshChangeObject(<? echo $idModTyp; ?>, this.options[this.selectedIndex].value);" name="objects">
				<?
			}else{
				?>
				<select onchange="javascript:refreshChangeObject(document.getElementById('moduleType').options[document.getElementById('moduleType').selectedIndex].value, this.options[this.selectedIndex].value);" name="objects">
				<?
			}
			echo implode('',$lstObjects);
			?>
			</select>
			<?
			if (isset($_SESSION['dims']['categFiltre']['obj']) && $_SESSION['dims']['categFiltre']['obj'] > 0){
				$tmp = $_SESSION['dims']['categFiltre']['obj'];
				$_SESSION['dims']['categFiltre']['obj'] = 0;
				?>
					<script type="text/javascript">
						refreshChangeObject(<? echo $_SESSION['dims']['categFiltre']['module']; ?>, <? echo $tmp; ?>);
					</script>
				<?
			}
			die();
			break;
		case 'refreshCategories' :
			ob_clean();
			$idModTyp = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$lvl = 0;
			$lstCateg = array();
			$_SESSION['dims']['categFiltre']['module'] = $idModTyp;
			if ($idModTyp != '' && $idModTyp > 0){
				require_once DIMS_APP_PATH.'modules/system/class_category.php';
				$sel = "SELECT	id_category
						FROM	dims_category_module_type
						WHERE	id_module_type = :idmoduletype ";
				$res = $db->query($sel, array(
					':idmoduletype' => $idModTyp
				));
				if ($db->numrows($res) > 0){
					while($r = $db->fetchrow($res)){
						$categ = new category();
						$categ->open($r['id_category']);
						$categ->initDescendance();
						$lstCateg[$r['id_category']] = $categ->getArboForEdit();
						if($lstCateg[$r['id_category']]['nbLvl'] > $lvl)
							$lvl = $lstCateg[$r['id_category']]['nbLvl'];
					}
				}
			}
			$lstCateg[0] = array('data' => '<span onclick="javascript:addCateg(0);">Ajouter une cat&eacute;gorie</span>', 'child' => array());
			require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');
			$browser = new dims_browser($lvl+2,$lstCateg,'listeCateg');
			$browser->displayBrowser(DIMS_APP_PATH.'modules/system/class_category.tpl.php');
			die();
			break;
		case 'refreshCategories2' :
			ob_clean();
			$idModTyp = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idObject = dims_load_securvalue('obj',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$_SESSION['dims']['categFiltre']['module'] = $idModTyp;
			$_SESSION['dims']['categFiltre']['obj'] = $idObject;
			$lstCateg = array();
			$lvl = 0;
			if ($idModTyp != '' && $idModTyp > 0 && $idObject != '' && $idObject > 0){
				require_once DIMS_APP_PATH.'modules/system/class_category.php';
				$sel = "SELECT	id_category
						FROM	dims_category_object
						WHERE	object_id_module_type = :idmoduletype
						AND		id_object = :idobject ";
				$res = $db->query($sel, array(
					':idmoduletype' => $idModTyp,
					':idobject'		=> $idObject
				));
				if ($db->numrows($res) > 0){
					while($r = $db->fetchrow($res)){
						$categ = new category();
						$categ->open($r['id_category']);
						$categ->initDescendance();
						$lstCateg[$r['id_category']] = $categ->getArboForEdit();
						if($lstCateg[$r['id_category']]['nbLvl'] > $lvl)
							$lvl = $lstCateg[$r['id_category']]['nbLvl'];
					}
					$lvl++;
				}
			}
			$lstCateg[-1] = array('data' => '<span onclick="javascript:addCateg(0);">Ajouter une cat&eacute;gorie</span>', 'child' => array());
			require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');
						$browser = new dims_browser($lvl+1,$lstCateg,'listeCateg');
			$browser->displayBrowser(DIMS_APP_PATH.'modules/system/class_category.tpl.php');
			die();
			break;
		case 'editCateg' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_category.php';
				$cat = new category();
				$cat->open($id);
			?>
			<div style="margin-left:20px;margin-top:10px;margin-bottom:10px;">&Eacute;dition d'une cat&eacute;gorie<img style="cursor:pointer;float:right;margin-right:10px;" onclick="javascript:dims_hidepopup();" src="./common/img/close.png" /></div>
			<div style="margin-left:10px;margin-right:10px;margin-bottom:10px;">
				<form name="newCateg" method="POST" action="<? echo $dims->getScriptEnv().'?op=saveEdit'; ?>">
					<input type="hidden" name="id" value="<? echo $id; ?>" />
					<? echo $_DIMS['cste']['_DIMS_LABEL']; ?> : <input type="text" name="label" value="<? echo $cat->getLabel(); ?>" /><br />
					<? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?> : <select name="level">
																			<option <? if ($cat->getLevel() == 0) echo 'selected=true'; ?> value="0"><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC']; ?></option>
																			<option <? if ($cat->getLevel() == 1) echo 'selected=true'; ?> value="1"><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE']; ?></option>
																			<!--<option value="2">Universel></option>-->
																		 </select>
					<button type="submit"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></button>
				</form>
			</div>
			<?
			}else{
				?>
				<script type="text/javascript">
					dims_hidepopup();
				</script>
				<?
			}
			die();
			break;
		case 'addSubCateg':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<div style="margin-left:20px;margin-top:10px;margin-bottom:10px;">Ajout d'une cat&eacute;gorie<img style="cursor:pointer;float:right;margin-right:10px;" onclick="javascript:dims_hidepopup();" src="./common/img/close.png" /></div>
			<div style="margin-left:10px;margin-right:10px;margin-bottom:10px;">
				<form name="newCateg" method="POST" action="<? echo $dims->getScriptEnv().'?op=saveNew'; ?>">
					<input type="hidden" name="id" value="<? echo $id; ?>" />
					<? echo $_DIMS['cste']['_DIMS_LABEL']; ?> : <input type="text" name="label" value="" /><br />
					<? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?> : <select name="level">
																			<option value="0"><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC']; ?></option>
																			<option value="1"><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE']; ?></option>
																			<!--<option value="2">Universel></option>-->
																		 </select>
					<button type="submit"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></button>
				</form>
			</div>
			<?
			die();
		case 'downCateg' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_category.php';
				$cat = new category();
				$cat->open($id);
				$cat->setPosition($cat->getPosition()+1,true);
				$cat->save();
			}
			die();
			break;
		case 'upCateg' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_category.php';
				$cat = new category();
				$cat->open($id);
				$cat->setPosition($cat->getPosition()-1,true);
				$cat->save();
			}
			die();
			break;

		// case pour les dims_case
		case 'case_manager' :
			ob_clean();
			require_once DIMS_APP_PATH.'modules/system/case/controller_op_case.php';
			die();
			break;

		// case pour les dims_faq
		case 'dims_faq_manager' :
			ob_clean();
			require_once DIMS_APP_PATH.'modules/system/faq/controller_op_faq.php';
			die();
			break;
		// case pour les dims_glossaire
		case 'dims_glossaire_manager' :
			ob_clean();
			require_once DIMS_APP_PATH.'modules/system/faq/controller_op_glossaire.php';
			die();
			break;
		// case pour la réouverture des accordéons
		case "accordionReopen":
			ob_clean();
			$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$open = dims_load_securvalue('open',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$_SESSION['dims']['accordion'][$name] = $open/2;
			die();
			break;

		case 'intervention':
			ob_end_clean();
			include DIMS_APP_PATH.'modules/system/intervention/op.php';
			die();
			break;

		case 'addToFavorite' :
			ob_clean();
			require_once DIMS_APP_PATH.'modules/system/class_favorite.php';
			$id_user = dims_load_securvalue('id_user',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_gb = dims_load_securvalue('id_gb',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$status = dims_load_securvalue('status',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$note = dims_load_securvalue('note',dims_const::_DIMS_NUM_INPUT,true,true,true);

			if ($id_gb > 0 && $id_gb != ''){
				$fav = new favorite();
				$fav->open($id_gb,$id_user);
				$fav->changeStatus($status,$note);
				$fav->save();
			}
			die();
			break;

		case 'desktopv2':
			require_once DIMS_APP_PATH.'modules/system/desktopV2/op.php';
			break;

		case 'activity':
			require_once DIMS_APP_PATH.'modules/system/activity/op.php';
			break;
	}
}
?>
