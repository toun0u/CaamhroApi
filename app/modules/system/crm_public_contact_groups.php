<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');

//gestion de la suppression d'un group ou d'un lien
$tagaction = dims_load_securvalue('tagaction', dims_const::_DIMS_CHAR_INPUT, true, true, true);
$id_tagtodel = dims_load_securvalue('id_tagtodel', dims_const::_DIMS_NUM_INPUT, true, true, true);
$id_linktodel = dims_load_securvalue('id_linktodel', dims_const::_DIMS_NUM_INPUT, true, true, true);

if($tagaction == 'del_tag' && $id_tagtodel != '') {
	$grlk = new tag();
	$grlk->open($id_tagtodel);
	$grlk->delete();
}
if($tagaction == 'del_tag_link' && $id_linktodel != '') {
	$grlink = new tag_index();
	$grlink->open($id_linktodel);
	$grlink->delete();
}

//gestion de la creation d'un group
if(isset($_POST)) { //
	//dims_print_r($_POST);
	$gp_lbl = dims_load_securvalue('tag_label', dims_const::_DIMS_CHAR_INPUT, false, true, true);
	$gp_private = dims_load_securvalue('tag_view', dims_const::_DIMS_NUM_INPUT, false, true, true);
	if($gp_lbl != '') {
		$tag = new tag();
		$tag->init_description();
		$tag->fields['tag'] = $gp_lbl;
		$tag->fields['private'] = !$gp_private;
		$tag->fields['view'] = 1;
		$tag->fields['id_user'] = $_SESSION['dims']['userid'];
		$tag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$tag->save();
	}
	else {
		$msg_err = $_DIMS['cste']['_DIMS_ERROR_LABEL_EMPTY'];
	}
}

//affichage des groupes et contacts rattaches
$tab_group = array();
$sqlgr =	"SELECT		t.id,
						t.tag as label,
						t.private,
						t.id_user as id_user_create,
						t.id_workspace,
						l.id as id_link,
						l.id_record,
						l.id_tag
			FROM		dims_tag as t
			LEFT JOIN	dims_tag_index as l
			ON			l.id_tag = t.id
			AND			l.id_module=1
			AND			(l.id_object= :objectcontact OR l.id_object= :objecttiers )
			WHERE		(t.id_workspace = :workspaceid
			OR			t.id_user = :userid )
			AND			t.private=1
			ORDER BY	t.tag";

$resgr = $db->query($sqlgr, array(
	':objectcontact' 	=> dims_const::_SYSTEM_OBJECT_CONTACT,
	':objecttiers' 		=> dims_const::_SYSTEM_OBJECT_TIERS,
	':workspaceid' 		=> $_SESSION['dims']['workspaceid'],
	':userid' 			=> $_SESSION['dims']['userid']
));
if($db->numrows($resgr) > 0) {
	while($tab_gr = $db->fetchrow($resgr)) {
		if(!isset($tab_group[$tab_gr['view']])) $tab_group[$tab_gr['view']] = array();

		if(($tab_gr['private'] == 0 && $tab_gr['id_workspace'] == $_SESSION['dims']['workspaceid']) || ($tab_gr['private'] == 1 && $tab_gr['id_user_create'] == $_SESSION['dims']['userid'])) {

			if(!isset($tab_group[$tab_gr['private']][$tab_gr['id']])) $tab_group[$tab_gr['private']][$tab_gr['id']] = array();

			$tab_group[$tab_gr['private']][$tab_gr['id']]['label'] = $tab_gr['label'];
			$tab_group[$tab_gr['private']][$tab_gr['id']]['id_user_create'] = $tab_gr['id_user_create'];

			if(!isset($tab_group[$tab_gr['private']][$tab_gr['id']]) && $tab_gr['id_link'] != '')
				$tab_group[$tab_gr['private']][$tab_gr['id']]['ct_linked'][$tab_gr['id_link']] = array();

			if($tab_gr['id_link'] != '') {
				$tab_group[$tab_gr['private']][$tab_gr['id']]['ct_linked'][$tab_gr['id_link']]['id_link'] = $tab_gr['id_link'];
				$tab_group[$tab_gr['private']][$tab_gr['id']]['ct_linked'][$tab_gr['id_link']]['id_record'] = $tab_gr['id_record'];
			}
		}
	}
}

//dims_print_r($tab_group);
?>
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<?php
			//Bloc d'ajout d'un groupe
			echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_ADD_CT_GROUP'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
			?>
			<form id="add_ct_group" name="add_ct_group" method="post" action="">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("tag_label");
					$token->field("tag_view");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<table width="100%" cellpadding="5" cellspacing="5">
					<tr>
						<td width="40%" align="right">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_CTGROUP_TITLE']; ?>
						</td>
						<td align="left">
							<input type="text" id="group_label" name="tag_label" value=""/>
						</td>
					</tr>
					<tr>
						<td width="40%" align="right">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_CTGROUP_VIEW']; ?>
						</td>
						<td align="left">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE']; ?><input type="radio" name="tag_view" id="tag_view" value="0" checked/>&nbsp;
							<?php echo $_DIMS['cste']['_WORKSPACE']; ?><input type="radio" name="tag_view" id="tag_view" value="1"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<?php echo dims_create_button($_DIMS['cste']['_DIMS_ADD'],'./common/img/contact.png', 'document.add_ct_group.submit();')?>
						</td>
					</tr>
				</table>
			</form>
			<?
			echo $skin->close_simplebloc();
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php
			//Bloc de consultation des groupes avec leur contacts
			echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_CT_GROUP_LIST'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
			?>
			<table width="100%" cellpadding="5" cellspacing="5">

				<tr>
					<? if(!empty($tab_group[1])) { ?>
					<td style="font-size:14px;font-weight:bold;">
						<? echo $_DIMS['cste']['_DIMS_LABEL_LIST_PRIVATE_CT']; ?>
					</td>
					<? }
						if(!empty($tab_group[0])) {
					?>
					<td style="font-size:14px;font-weight:bold;">
						<? echo $_DIMS['cste']['_DIMS_LABEL_LIST_WORKSACE_CT']; ?>
					</td>
					<? } ?>
				</tr>
				<tr>
					<? if(!empty($tab_group[1])) { ?>
					<td>
						<table width="100%">
						<?
							foreach($tab_group[1] as $id_group => $tab_g) {
								echo	'<tr>
											<td>';
									//cas ou on a des contacts rattaches au groupe
									if(isset($tab_g['ct_linked'])) {
										echo	'<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left"><a href="javascript:void(0);" onclick="javascipt:dims_switchdisplay(\'show_ct_linked'.$id_group.'\');">'.$tab_g['label'].'</a></td>
													</tr>
													<tr>
														<td align="left">
															<div style="width:100%;display:none;" id="show_ct_linked'.$id_group.'">
																<table width="100%" cellpadding="0" cellspacing="0">
																	<tr class="trl1">
																		<td>'.$_DIMS['cste']['_DIMS_LABEL_CONTACTS'].'
																		</td>
																		<td>
																		</td>
																	</tr>';
											$class= 'trl1';
											foreach($tab_g['ct_linked'] as $id_link => $tab_l) {
												if($class == 'trl1') $class = 'trl2';
												else $class = 'trl1';
												$ct = new contact();
												$ct->open($tab_l['id_record']);
												echo				'<tr class="'.$class.'">
																		<td>'.$ct->fields['firstname'].' '.$ct->fields['lastname'].'</td>
																		<td>
																			<a href="admin.php?cat=0&action='._BUSINESS_TAB_CONTACT_GROUP.'&part='._BUSINESS_TAB_CONTACT_GROUP.'&tagaction=del_group_link&id_linktodel='.$id_link.'" onclick="javascript:confirm(\''.$_DIMS['cste']['_SYSTEM_MSG_CONFIRMGROUPDELETE'].'\');" style="border:0px;">
																				<img src="./common/img/delete.png"/>
																			</a>
																		</td>
																	</tr>';
											}
										echo					'</table>
															</div>
														</td>
													</tr>
												</table>';
									}
									else {
										//pas de contact rattache
										echo	'<table width="100%">
													<tr>
														<td align="left">'.$tab_g['label'].'&nbsp;
															<a href="admin.php?cat=0&action='._BUSINESS_TAB_CONTACT_GROUP.'&part='._BUSINESS_TAB_CONTACT_GROUP.'&tagaction=del_group&id_tagtodel='.$id_group.'" onclick="javascript:confirm(\''.$_DIMS['cste']['_SYSTEM_MSG_CONFIRMGROUPDELETE'].'\');" style="border:0px;">
																<img src="./common/img/delete.png"/>
															</a>
														</td>
														<td align="left">
															'.$_DIMS['cste']['_DIMS_LABEL_NO_CT_ATTACHED'].'
														</td>
													</tr>
												</table>';
									}
								echo		'</td>
										</tr>';
							}
						?>
						</table>
					</td>
					<? }
					if(!empty($tab_group[0])) {
					?>
					<td>
						<table width="100%">
						<?
							foreach($tab_group[0] as $id_group => $tab_g) {
								echo	'<tr>
											<td valign="top">';
									//cas ou on a des contacts rattaches au groupe
									if(isset($tab_g['ct_linked'])) {
										echo	'<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td align="left"><a href="javascript:void(0);" onclick="javascipt:dims_switchdisplay(\'show_ct_linked'.$id_group.'\');">'.$tab_g['label'].'</a></td>
													</tr>
													<tr>
														<td align="left">
															<div style="width:100%;display:none;" id="show_ct_linked'.$id_group.'">
																<table width="100%" cellpadding="0" cellspacing="0">
																	<tr class="trl1">
																		<td>'.$_DIMS['cste']['_DIMS_LABEL_CONTACTS'].'
																		</td>
																		<td>
																		</td>
																	</tr>';
											$class = 'trl1';
											foreach($tab_g['ct_linked'] as $id_link => $tab_l) {
												if($class == 'trl1') $class = 'trl2';
												else $class = 'trl1';
												$ct = new contact();
												$ct->open($tab_l['id_record']);
												echo				'<tr class="'.$class.'">
																		<td>'.$ct->fields['firstname'].' '.$ct->fields['lastname'].'</td>
																		<td>';
												if($_SESSION['dims']['userid'] == $tab_g['id_user_create']) {
													echo					'<a href="admin.php?cat=0&action='._BUSINESS_TAB_CONTACT_GROUP.'&part='._BUSINESS_TAB_CONTACT_GROUP.'&tagaction=del_tag_link&id_linktodel='.$id_link.'" onclick="javascript:confirm(\''.$_DIMS['cste']['_SYSTEM_MSG_CONFIRMGROUPDELETE'].'\');" style="border:0px;">
																				<img src="./common/img/delete.png"/>
																			</a>';
												}
												echo					'</td>
																	</tr>';
											}
										echo					'</table>
															</div>
														</td>
													</tr>
												</table>';
									}
									else {
										//pas de contact rattache
										echo	'<table width="100%">
													<tr>
														<td align="left">'.$tab_g['label'];
												if($_SESSION['dims']['userid'] == $tab_g['id_user_create']) {
													echo	'<a href="admin.php?cat=0&action='._BUSINESS_TAB_CONTACT_GROUP.'&part='._BUSINESS_TAB_CONTACT_GROUP.'&tagaction=del_tag&id_tagtodel='.$id_group.'" onclick="javascript:confirm(\''.$_DIMS['cste']['_SYSTEM_MSG_CONFIRMGROUPDELETE'].'\');" style="border:0px;">
																<img src="./common/img/delete.png"/>
															</a>';
												}
												echo	'</td>
														<td align="left">
															'.$_DIMS['cste']['_DIMS_LABEL_NO_CT_ATTACHED'].'
														</td>
													</tr>
												</table>';
									}
								echo		'</td>
										</tr>';
							}
						?>
						</table>
					</td>
					<? } ?>
				</tr>

			</table>
			<?php
			echo $skin->close_simplebloc();
			?>
		</td>
	</tr>
</table>
