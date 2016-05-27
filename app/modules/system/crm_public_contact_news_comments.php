<?php

//require_once(DIMS_APP_PATH . '/modules/system/class_dims.php');

$tab_com = array();

$sql_com = "SELECT		*
			FROM		dims_mod_business_commentaire
			WHERE		id_contact = :idcontact
			AND			id_object = :idobject
			ORDER BY	date_create DESC";

$res_com = $db->query($sql_com, array(
	':idcontact'	=> $contact->fields['id'],
	':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
));
//dims_print_r($_SESSION['dims']);

$tab_work = $dims->getAdminWorkspaces();

//dims_print_r($tab_work);

while($tab_res = $res_com->fetch()) {
	if($tab_res['com_level'] == 1) {
		if(!isset($tab_com['generique'])) {
			$tab_com['generique'] = array();
			$tab_com['generique']['current'] = $tab_res;
		}
		else {
			if(!isset($tab_com['generique']['historique'])) $tab_com['generique']['historique'] = array();
			$tab_com['generique']['historique'][] = $tab_res;
		}
	}
	if($tab_res['com_level'] == 2 && $tab_res['id_workspace'] == $_SESSION['dims']['workspaceid']) {
		if(!isset($tab_com['metier'])) {
			$tab_com['metier'] = array();
			$tab_com['metier']['current'] = $tab_res;
		}
		else {
			if(!isset($tab_com['metier']['historique'])) $tab_com['metier']['historique'] = array();
			$tab_com['metier']['historique'][] = $tab_res;
		}
	}
	if($tab_res['com_level'] == 3 && $tab_res['id_user'] == $_SESSION['dims']['userid']) {
		if(!isset($tab_com['perso'])) {
			$tab_com['perso'] = array();
			$tab_com['perso']['current'] = $tab_res;
		}
		else {
			if(!isset($tab_com['perso']['historique'])) $tab_com['perso']['historique'] = array();
			$tab_com['perso']['historique'][] = $tab_res;
		}
	}
}

//dims_print_r($tab_com);

?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100%" align="center">
		<?php echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_COMMENTS'], "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('last_comm');", ""); ?>
		<div id="last_comm" style="display:block;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center">
						<table style="width:40%;margin-top:5px;" cellpadding="0" cellspacing="0">
							<tr onclick="javascript:affiche_div('affiche_com_1');">
								<td class="bgb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20" style="font-size:13px;">
									<table width="100%" cellpadding="0" cellspacing="0" style="color:#fff;font-size:13px;">
										<tr>
											<td align="right" width="30%"><img src="./common/img/workspace.png"/>
											</td>
											<td style="padding-bottom:4px;padding-left:4px;" align="left">
												<?php
													echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_GEN'];
												?>
											</td>
										</tr>
									</table>
								</td>
								<td class="bdb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3">
									<div id="affiche_com_1" style="display:block;width:100%;height:160px;overflow:auto;">
										<table cellspacing="0" cellpadding="0" width="100%" style="margin-top:10px;margin-bottom:10px;">
											<tbody>

												<tr>
													<td width="90%" style="border:#738CAD 1px solid;padding:3px;">
														<?php
															if(!empty($tab_com['generique']['current'])) {
																echo nl2br($tab_com['generique']['current']['commentaire']);
															}
															else {
																echo $_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'];
															}
														?>
													</td>
													<td align="center">
														<a href="javascript:void(0);" onclick="javascript:modComment('<?php if(isset($tab_com['generique']['current']['id'])) echo $tab_com['generique']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['generique']['current']['id'])) echo '1'; else echo ''; ?>');">
															<img src="./common/img/add.gif" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_ADD_COMM']?>"/>
														</a>
														<?php if(isset($tab_com['generique']['current']['id_user']) && $_SESSION['dims']['userid'] == $tab_com['generique']['current']['id_user']) { ?>
														<a href="javascript:void(0);" onclick="javascript:modCommentbyAuthor('<?php if(isset($tab_com['generique']['current']['id'])) echo $tab_com['generique']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['generique']['current']['id'])) echo '1'; else echo ''; ?>');">
															<img src="./common/img/edit.gif" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_MODIFY_COMMENTAIRE']?>"/>
														</a>
														<?php } ?>
													</td>
												</tr>
												<tr>
													<td align="right">
													<?php
														if(!empty($tab_com['generique']['current'])) {
															$writer = new contact();
															$writer->open($tab_com['generique']['current']['id_user_ct']);

															echo $_DIMS['cste']['_DIMS_LABEL_FROM'].' : ';
															echo '<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$writer->fields['id'].'">'.$writer->fields['firstname'].' '.$writer->fields['lastname'].'</a>';

															$date_writed = dims_timestamp2local($tab_com['generique']['current']['date_create']);
															echo ' - '.$date_writed['date'];
														}
													?>
													</td>
													<td></td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table style="width:40%;margin-top:5px;" cellpadding="0" cellspacing="0">
							<tr onclick="javascript:affiche_div('affiche_com_2');">
								<td class="bgb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20" style="font-size:13px;">
									<table width="100%" cellpadding="0" cellspacing="0" style="color:#fff;font-size:13px;">
										<tr>
											<td align="right" width="30%"><img src="./common/img/users.png"/>
											</td>
											<td style="padding-bottom:4px;padding-left:4px;" align="left">
												<?php echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_MET'] ?>
											</td>
										</tr>
									</table>
								</td>
								<td class="bdb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3">
									<div id="affiche_com_2" style="display:block;width:100%;height:160px;overflow:auto;">
										<table cellspacing="0" cellpadding="0" width="100%" style="margin-top:10px;margin-bottom:10px;">
											<tbody>
												<tr>
													<td width="90%" style="border:#738CAD 1px solid;padding:3px;">
														<?php
															if(!empty($tab_com['metier']['current'])) {
																echo nl2br($tab_com['metier']['current']['commentaire']);
															}
															else {
																echo $_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'];
															}
														?>
													</td>
													<td align="center">
														<a href="javascript:void(0);" onclick="javascript:modComment('<? if(isset($tab_com['metier']['current']['id'])) echo $tab_com['metier']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['metier']['current']['id'])) echo '2'; else echo ''; ?>');">
															<img src="./common/img/add.gif" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_ADD_COMM']?>"/>
														</a>
														<?php if(isset($tab_com['metier']['current']['id_user']) && $_SESSION['dims']['userid'] == $tab_com['metier']['current']['id_user']) { ?>
														<a href="javascript:void(0);" onclick="javascript:modCommentbyAuthor('<? if(isset($tab_com['metier']['current']['id'])) echo $tab_com['metier']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['metier']['current']['id'])) echo '2'; else echo ''; ?>');">
															<img src="./common/img/edit.gif" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_MODIFY_COMMENTAIRE']?>"/>
														</a>
														<?php } ?>
													</td>
												</tr>
												<tr>
													<td align="right">
													<?php
														if(!empty($tab_com['metier']['current'])) {
															$writer = new contact();
															$writer->open($tab_com['metier']['current']['id_user_ct']);

															echo $_DIMS['cste']['_DIMS_COMMENTS_BY'].' : ';
															echo '<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$writer->fields['id'].'">'.$writer->fields['firstname'].' '.$writer->fields['lastname'].'</a>';
															$date_writed = dims_timestamp2local($tab_com['metier']['current']['date_create']);
															echo ' - '.$date_writed['date'];
														}
													?>
													</td>
													<td></td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table style="width:40%;margin-top:5px;" cellpadding="0" cellspacing="0">
							<tr onclick="javascript:affiche_div('affiche_com_3');">
								<td class="bgb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20" style="font-size:13px;">
								<table width="100%" cellpadding="0" cellspacing="0" style="color:#fff;font-size:13px;">
										<tr>
											<td align="right" width="30%"><img src="./common/img/fav1.png"/>
											</td>
											<td style="padding-bottom:4px;padding-left:4px;" align="left">
												<?php
													echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_PERS'];
												?>
											</td>
										</tr>
									</table>
								<?php echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_PERS'] ?>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3">
									<div id="affiche_com_3" style="display:none;width:100%;height:160px;overflow:auto;">
										<table cellspacing="0" cellpadding="0" width="100%" style="margin-top:10px;margin-bottom:10px;">
											<tbody>
												<?php
													if(!empty($tab_com['perso']['current'])) {
														$date_writed = dims_timestamp2local($tab_com['perso']['current']['date_create']);
														echo '<tr><td colspan="2">Date : '.$date_writed['date'].'</td></tr>';
													}
												?>
												<tr>
													<td width="90%" style="border:#738CAD 1px solid;padding:3px;">
														<?php
															if(!empty($tab_com['perso']['current'])) {
																echo stripslashes($tab_com['perso']['current']['commentaire']);
															}
															else {
																echo $_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'];
															}
														?>
													</td>
													<td align="center">
														<a href="javascript:void(0);" onclick="javascript:modComment('<? if(isset($tab_com['perso']['current']['id'])) echo $tab_com['perso']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['perso']['current']['id'])) echo '3'; else echo ''; ?>');">
															<img src="./common/img/add.gif" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_ADD_COMM']?>"/>
														</a>
														<?php if(isset($tab_com['perso']['current']['id_user']) && $_SESSION['dims']['userid'] == $tab_com['perso']['current']['id_user']) { ?>
														<a href="javascript:void(0);" onclick="javascript:modCommentbyAuthor('<? if(isset($tab_com['perso']['current']['id'])) echo $tab_com['perso']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['perso']['current']['id'])) echo '3'; else echo ''; ?>');">
															<img src="./common/img/edit.gif" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_MODIFY_COMMENTAIRE']?>"/>
														</a>
														<?php } ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php echo $skin->close_widgetbloc(); ?>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
		<?php echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_COMMENT_HIST'], "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('hist_comm');", ""); ?>
		<div id="hist_comm" style="display:block;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center">
						<table style="width:40%;margin-top:5px;" cellpadding="0" cellspacing="0">
							<tr onclick="javascript:affiche_div('affiche_hcom_1');">
								<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20" style="font-size:13px;">
									<table width="100%" cellpadding="0" cellspacing="0" style="color:#fff;font-size:13px;">
										<tr>
											<td align="right" width="30%"><img src="./common/img/workspace.png"/>
											</td>
											<td style="padding-bottom:4px;padding-left:4px;" align="left">
												<?php
													$count = 0;
													if(!empty($tab_com['generique']['historique'])) $count = count($tab_com['generique']['historique']);
													echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_GEN']." (".$count.")";
												?>
											</td>
										</tr>
									</table>
								</td>
								<td class="bdb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3">
									<div id="affiche_hcom_1" style="display:block;width:100%;height:160px;overflow:auto;">
										<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tbody>
												<?php
													if(!empty($tab_com['generique']['historique'])) {
												?>
													<tr class="trl1" style="font-size:12px;">
														<td style="width: 1%;"/>
														<td style="width: 15%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
														<td style="width: 63%;"><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?></td>
														<td style="width: 17%;"><? echo $_DIMS['cste']['_AUTHOR'] ?></td>
														<td style="width: 4%;"></td>
													</tr>
												<?php
														$class_col = 'trl1';
														foreach($tab_com['generique']['historique'] as $key => $inf_com) {
															if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
															$date_on = dims_timestamp2local($inf_com['date_create']);

															$ct_creat = new contact();
															$ct_creat->open($inf_com['id_user_ct']);
															if (isset($ct_creat->fields['lastname']) && $ct_creat->fields['firstname']) {
																echo '	<tr class="'.$class_col.'">
																			<td></td>
																			<td>'.$date_on['date'].'</td>
																			<td>'.substr($inf_com['commentaire'], 0,180).'... </td>
																			<td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$ct_creat->fields['id'].'">'.$ct_creat->fields['firstname'].' '.$ct_creat->fields['lastname'].'</a></td>
																			<td align="center"><a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_hgen_'.$key.'\');"><img src="./common/img/view.png" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"></a>';
																if($inf_com['id_user_ct'] == $_SESSION['dims']['user']['id_contact'])	echo '<a href="javascript:void(0);" onclick="javascript:supr_comm(\''.$inf_com['id'].'\');"><img src="./common/modules/system/img/del.png"></a>';

																echo		'</td>
																		</tr>';
															}
															echo '
																	<tr class="'.$class_col.'">
																		<td colspan="5">
																			<div id="div_hgen_'.$key.'" style="display:none;">
																				<table width="100%">
																					<tr>
																						<td align="left" style="padding-left:25px;">'.nl2br($inf_com['commentaire']).'
																						</td>
																					</tr>
																				</table>
																			</div>
																		</td>
																	</tr>
																	';
														}
													}
													else {
														echo '	<tr>
																	<td>
																		'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'
																	</td>
																</tr>';
													}
												?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table style="width:40%;margin-top:5px;" cellpadding="0" cellspacing="0">
							<tr onclick="javascript:affiche_div('affiche_hcom_2');">
								<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20" style="font-size:13px;">
									<table width="100%" cellpadding="0" cellspacing="0" style="color:#fff;font-size:13px;">
										<tr>
											<td align="right" width="30%"><img src="./common/img/users.png"/>
											</td>
											<td style="padding-bottom:4px;padding-left:4px;" align="left">
												<?php
													$count = 0;
													if(!empty($tab_com['metier']['historique'])) $count = count($tab_com['metier']['historique']);
													echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_MET']." (".$count.")";
												?>
											</td>
										</tr>
									</table>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3">
									<div id="affiche_hcom_2" style="display:block;width:100%;height:160px;overflow:auto;">
										<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tbody>
												<?php
													if(!empty($tab_com['metier']['historique'])) {
												?>
													<tr class="trl1" style="font-size:12px;">
														<td style="width: 1%;"/>
														<td style="width: 15%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
														<td style="width: 63%;"><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?></td>
														<td style="width: 17%;"><? echo $_DIMS['cste']['_AUTHOR'] ?></td>
														<td style="width: 4%;"></td>
													</tr>
												<?php
														$class_col = 'trl1';
														foreach($tab_com['metier']['historique'] as $key => $inf_com) {
															if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
															$date_on = dims_timestamp2local($inf_com['date_create']);

															$ct_creat = new contact();
															$ct_creat->open($inf_com['id_user_ct']);

															echo '	<tr class="'.$class_col.'">
																		<td></td>
																		<td>'.$date_on['date'].'</td>
																		<td>'.substr($inf_com['commentaire'], 0,180).'... </td>
																		<td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$ct_creat->fields['id'].'">'.$ct_creat->fields['firstname'].' '.$ct_creat->fields['lastname'].'</a></td>
																		<td align="center">
																			<a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_hmet_'.$key.'\');"><img src="./common/img/view.png" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"></a>';
															if($inf_com['id_user_ct'] == $_SESSION['dims']['user']['id_contact'])	echo '<a href="javascript:void(0);" onclick="javascript:supr_comm(\''.$inf_com['id'].'\');"><img src="./common/modules/system/img/del.png" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"></a>';

															echo		'</td>
																	</tr>
																	<tr class="'.$class_col.'">
																		<td colspan="5">
																			<div id="div_hmet_'.$key.'" style="display:none;">
																				<table width="100%">
																					<tr>
																						<td align="left" style="padding-left:25px;">'.nl2br($inf_com['commentaire']).'
																						</td>
																					</tr>
																				</table>
																			</div>
																		</td>
																	</tr>
																	';
														}
													}
													else {
														echo '	<tr>
																	<td>
																		'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'
																	</td>
																</tr>';
													}
												?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table style="width:40%;margin-top:5px;" cellpadding="0" cellspacing="0">
							<tr onclick="javascript:affiche_div('affiche_hcom_3');">
								<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20" style="font-size:13px;">
									<table width="100%" cellpadding="0" cellspacing="0" style="color:#fff;font-size:13px;">
										<tr>
											<td align="right" width="30%"><img src="./common/img/fav1.png"/>
											</td>
											<td style="padding-bottom:4px;padding-left:4px;" align="left">
												<?php
													$count = 0;
													if(!empty($tab_com['perso']['historique'])) $count = count($tab_com['perso']['historique']);
													echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_PERS']." (".$count.")";
												?>
											</td>
										</tr>
									</table>
								<?php echo $_DIMS['cste']['_DIMS_LABEL_COMMENT_PERS'] ?>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3">
									<div id="affiche_hcom_3" style="display:none;width:100%;height:160px;overflow:auto;">
										<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
											<tbody>
												<?php
													if(!empty($tab_com['perso']['historique'])) {
												?>
													<tr class="trl1" style="font-size:12px;">
														<td style="width: 1%;"/>
														<td style="width: 15%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
														<td style="width: 70%;"><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?></td>
														<td style="width: 4%;"></td>
													</tr>
												<?php
														$class_col = 'trl1';
														foreach($tab_com['perso']['historique'] as $key => $inf_com) {
															if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
															$date_on = dims_timestamp2local($inf_com['date_create']);

															$ct_creat = new contact();
															$ct_creat->open($inf_com['id_user_ct']);

															echo '	<tr class="'.$class_col.'">
																		<td></td>
																		<td>'.$date_on['date'].'</td>
																		<td>'.substr($inf_com['commentaire'], 0,65).'... </td>
																		<td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$ct_creat->fields['id'].'">'.$ct_creat->fields['firstname'].' '.$ct_creat->fields['lastname'].'</a></td>
																		<td align="center"><a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_hpers_'.$key.'\');"><img src="./common/img/view.png" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"></a>';
															if($inf_com['id_user_ct'] == $_SESSION['dims']['user']['id_contact'])	echo '<a href="javascript:void(0);" onclick="javascript:supr_comm(\''.$inf_com['id'].'\');"><img src="./common/modules/system/img/del.png" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"></a>';

															echo		'</td>
																	</tr>
																	<tr class="'.$class_col.'">
																		<td colspan="5">
																			<div id="div_hpers_'.$key.'" style="display:none;">
																				<table width="100%">
																					<tr>
																						<td align="left" style="padding-left:25px;">'.nl2br($inf_com['commentaire']).'
																						</td>
																					</tr>
																				</table>
																			</div>
																		</td>
																	</tr>
																	';
														}
													}
													else {
														echo '	<tr>
																	<td>
																		'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'
																	</td>
																</tr>';
													}
												?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php echo $skin->close_widgetbloc(); ?>
		</td>
	</tr>
</table>
