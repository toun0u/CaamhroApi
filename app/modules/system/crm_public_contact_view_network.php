<script language="javascript">
	var timersearch;

	function upKeysearchLink(type) {
		clearTimeout(timersearch);
		timersearch = setTimeout("execSearchLink(\'"+type+"\')", 500);
	}

	function execSearchLink(type) {
		clearTimeout(timersearch);

		if(type == 'pers') {
			var nomsearch = dims_getelem('search_pers').value;
			var divtoaffich = dims_getelem('dispres_searchp');

			if(nomsearch.length>=2) {
				dims_xmlhttprequest_todiv("admin.php", "op=search_linktoadd&action=<? echo _BUSINESS_TAB_CONTACTSTIERS;?>&search_name="+nomsearch+"&type_search="+type, "", "dispres_searchp");
				divtoaffich.style.display = "block";
			}
		}
		else {
			var nomsearch = dims_getelem('search_tiers').value;
			var divtoaffich = dims_getelem('dispres_searcht');

			if(nomsearch.length>=2) {
				dims_xmlhttprequest_todiv("admin.php", "op=search_linktoadd&action=<? echo _BUSINESS_TAB_CONTACTSTIERS;?>&search_name="+nomsearch+"&type_search="+type, "", "dispres_searcht");
				divtoaffich.style.display = "block";
			}
		}
	}

</script>
<?
	$tab_link = array();

	if(!empty($ent_id))		{
		//recherche des liens entre entreprises

	}
	elseif(!empty($contact_id)) {
		//recherche des liens entre personnes
		$sql_li1 = "SELECT			c.id as id_pers, c.lastname, c.firstname,
									l.*,
									byc.lastname as by_lastname, byc.firstname as by_firstname
					FROM			dims_mod_business_contact c
					INNER JOIN		dims_mod_business_ct_link l
					ON				l.id_contact2 = c.id
					INNER JOIN		dims_mod_business_contact byc
					ON				byc.id = l.id_ct_user_create
					WHERE			l.id_contact1 = :idcontact
					AND				type_ct LIKE 'personne'
					ORDER BY		c.lastname, c.firstname";
		//echo $sql_li1;
		$res_li1 = $db->query($sql_li1, array(
			':idcontact' => $contact_id
		));

		while($tab_li1 = $db->fetchrow($res_li1)) {
			//lien de type personnel (que l'on fait � partir du bouton 'Ajouter � votre liste de contacts')
			if($tab_li1['link_level'] == 3 && $_SESSION['dims']['user']['id_contact'] == $contact_id) {
				$tab_link['between_pers']['personnel'][$tab_li1['id_contact2']] = $tab_li1;
			}
			//lien de type m�tier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
			if($tab_li1['link_level'] == 2) {
				$tab_link['between_pers']['metier'][$tab_li1['id_contact2']] = $tab_li1;
			}
			//liens de type g�n�rique (ceux que l'on fait a partir du formualire de gestion des liens dans la fiche contact)
			if($tab_li1['link_level'] == 1) {
				$tab_link['between_pers']['generique'][$tab_li1['id_contact2']] = $tab_li1;
			}
		}

		//un lien est bidirectionnel, il faut donc rechercher � partir de contact2 aussi
		$sql_li2 = "SELECT			c.id as id_pers, c.lastname, c.firstname,
									l.*,
									byc.lastname as by_lastname, byc.firstname as by_firstname
					FROM			dims_mod_business_contact c
					INNER JOIN		dims_mod_business_ct_link l
					ON				l.id_contact1 = c.id
					INNER JOIN		dims_mod_business_contact byc
					ON				byc.id = l.id_ct_user_create
					WHERE			l.id_contact2 = :idcontact
					AND				type_ct LIKE 'personne'
					ORDER BY		c.lastname, c.firstname";
		//echo $sql_li2;
		$res_li2 = $db->query($sql_li2, array(
			':idcontact' => $contact_id
		));

		while($tab_li2 = $db->fetchrow($res_li2)) {
			//lien de type personnel (que l'on fait � partir du bouton 'Ajouter � votre liste de contacts')
			if($tab_li2['link_level'] == 3 && $_SESSION['dims']['user']['id_contact'] == $contact_id) {
				if(!isset($tab_link['between_pers']['personnel'][$tab_li2['id_contact1']])) $tab_link['between_pers']['personnel'][$tab_li2['id_contact1']] = $tab_li2;
			}
			//lien de type m�tier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
			if($tab_li2['link_level'] == 2) {
				if(!isset($tab_link['between_pers']['metier'][$tab_li2['id_contact1']])) $tab_link['between_pers']['metier'][$tab_li2['id_contact1']] = $tab_li2;
			}
			//liens de type g�n�rique (ceux que l'on fait a partir du loc de gestion des liens dans la fiche contact)
			if($tab_li2['link_level'] == 1) {
				if(!isset($tab_link['between_pers']['generique'][$tab_li2['id_contact1']])) $tab_link['between_pers']['generique'][$tab_li2['id_contact1']] = $tab_li2;
			}
		}

		//recherche des liens avec une entreprise
		$sql_li_ent = "	SELECT	e.intitule, e.ville,
								le.*
						FROM	dims_mod_business_tiers e
						INNER JOIN dims_mod_business_tiers_contact le
						ON		le.id_tiers = e.id
						WHERE	le.id_contact = :idcontact ";
		//echo $sql_li_ent;
		$res_li_ent = $db->query($sql_li_ent, array(
			':idcontact' => $contact_id
		));

		while($tab_lie = $db->fetchrow($res_li_ent)) {
			if($tab_lie['link_level'] == '1') {
				$tab_link['ent_pers']['generique'][$tab_lie['id_tiers']] = $tab_lie;
			}
			if($tab_lie['link_level'] == '2') {
				$tab_link['ent_pers']['metier'][$tab_lie['id_tiers']] = $tab_lie;
			}
		}

		//dims_print_r($tab_link);
	}
?>

<td>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="50%" style="vertical-align:top;" rowspan="3">
				<table width="100%">
					<tr>
						<td>
						<?
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
						?>
						</td>
					</tr>
					<tr>
						<td>
						<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ADDLINK'],'width:100%', 'color:#cccccc;'); ?>
						<div id="vertical_container3">
							<h3 class="accordion_toggle">
								<table style="width:100%;">
									<tr>
										<td align="left" width="30%">&nbsp;</td>
										<td align="left" width="30%">
											<table style="width:100%;" cellpadding="0" cellspacing="0">
												<tr>
													<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
													<td class="midb20">
													<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_PSEARCH']; ?>
													</td>
													<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												</tr>
											</table>
										</td>
										<td  style="width:30%;text-align:right">&nbsp;</td>
									</tr>
								</table>
							</h3>
							<div class="accordion_content" style="background-color:transparent;">
								<form name="inscript_link" id="inscript_link" action="" method="post">
								<?
									// Sécurisation du formulaire par token
									require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
									$token = new FormToken\TokenField;
									$token->field("op",				"save_link_intelligence");
									$token->field("action",			_BUSINESS_TAB_CONTACTSTIERS);
									$token->field("id_pers_from",	$contact_id);
									$token->field("id_ent_from",	$ent_id);
									$token->field("type",			"pers");
									$token->field("search_pers");
									$tokenHTML = $token->generate();
									echo $tokenHTML;
								?>
								<input type="hidden" name="op" value="save_link_intelligence"/>
								<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>"/>
								<? if(!empty($contact_id)) {  ?>
								<input type="hidden" name="id_pers_from" value="<? echo $contact_id ?>"/>
								<? } elseif(!empty($ent_id)) { ?>
								<input type="hidden" name="id_ent_from" value="<? echo $ent_id ?>"/>
								<? } ?>
								<input type="hidden" name="type" value="pers"/>
									<table width="100%" border="0" cellpadding="5" cellspacing="0">
										<tr>
											<td align="right" width="40%">
												<? echo $_DIMS['cste']['_DIMS_LABEL_SEARCH_LPERS']; ?> :
											</td>
											<td align="left">
												<input type="text" value="" onkeyup="javascript:upKeysearchLink('pers');" id="search_pers" name="search_pers"/>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div id="dispres_searchp" style="display:none;width:100%">

												</div>
											</td>
										</tr>
									</table>
								</form>
							</div>
							<h3 class="accordion_toggle">
								<table style="width:100%;">
									<tr>
										<td align="left" width="30%">&nbsp;</td>
										<td align="left" width="30%">
											<table style="width:100%;" cellpadding="0" cellspacing="0">
												<tr>
													<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
													<td class="midb20">
													<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_TSEARCH']; ?>
													</td>
													<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												</tr>
											</table>
										</td>
										<td  style="width:30%;text-align:right">&nbsp;</td>
									</tr>
								</table>
							</h3>
							<div class="accordion_content" style="background-color:transparent;">
								<form name="inscript_link" id="inscript_link" action="" method="post">
								<?
									// Sécurisation du formulaire par token
									require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
									$token = new FormToken\TokenField;
									$token->field("op",				"save_link_intelligence");
									$token->field("action",			_BUSINESS_TAB_CONTACTSTIERS);
									$token->field("id_pers_from",	$contact_id);
									$token->field("id_ent_from",	$ent_id);
									$token->field("type",			"tiers");
									$token->field("search_pers");
									$tokenHTML = $token->generate();
									echo $tokenHTML;
								?>
								<input type="hidden" name="op" value="save_link_intelligence"/>
								<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>"/>
								<? if(!empty($contact_id)) {  ?>
								<input type="hidden" name="id_pers_from" value="<? echo $contact_id ?>"/>
								<? } elseif(!empty($ent_id)) { ?>
								<input type="hidden" name="id_ent_from" value="<? echo $ent_id ?>"/>
								<? } ?>
								<input type="hidden" name="type" value="tiers"/>
									<table width="100%" border="0" cellpadding="5" cellspacing="0">
										<tr>
											<td align="right" width="40%">
												<? echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?> :
											</td>
											<td align="left">
												<input type="text" value="" onkeyup="javascript:upKeysearchLink('tiers');" id="search_tiers" name="search_tiers"/>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div id="dispres_searcht" style="display:none;width:100%">

												</div>
											</td>
										</tr>
									</table>
								</form>
							</div>
						</div>
						<? echo $skin->close_simplebloc(); ?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_CONT'],'width:100%', 'color:#cccccc;'); ?>
				<div id="vertical_container">
						<h3 class="accordion_toggle">
							<table style="width:100%;">
								<tr>
									<td align="left" width="30%">&nbsp;</td>
									<td align="left" width="30%">
										<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												<td class="midb20">
												<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_GEN'] ?>
												</td>
												<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											</tr>
										</table>
									</td>
									<td  style="width:30%;text-align:right">&nbsp;</td>
								</tr>
							</table>
						</h3>
							<div class="accordion_content" style="background-color:transparent;">
								<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
									<tbody>
										<?
											if(!empty($tab_link['between_pers']['generique'])) {
										?>
											<tr class="fontgray" style="font-size:12px;">
												<td style="width: 3%;"/>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_PERSONNE']; ?></td>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE']; ?></td>
												<td style="width: 22%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></td>
											</tr>
										<?
												$class_col = 'trl1';
												foreach($tab_link['between_pers']['generique'] as $id_perstoview => $tab_pers) {
													if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
													$date_c = dims_timestamp2local($tab_pers['time_create']);
														echo '	<tr class="'.$class_col.'">
																	<td></td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'</a>
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		'.$tab_pers['type_link'].'
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		'.$date_c['date'].'
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
																		<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
																	</td>
																</tr>';
												}
											}
											else {
												echo '<tr><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
											}
										?>

								   </tbody>
							   </table>
							</div>
						<h3 class="accordion_toggle">
							<table style="width:100%;">
								<tr>
									<td align="left" width="30%">&nbsp;</td>
									<td align="left" width="30%">
										<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												<td class="midb20">
												<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_MET'] ?>
												</td>
												<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											</tr>
										</table>
									</td>
									<td  style="width:30%;text-align:right">&nbsp;</td>
								</tr>
							</table>
						</h3>
							<div class="accordion_content" style="background-color:transparent;">
								<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
										<tbody>
											<?
												if(!empty($tab_link['between_pers']['metier'])) {
											?>
												<tr class="fontgray" style="font-size:12px;">
													<td style="width: 3%;"/>
													<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_PERSONNE']; ?></td>
													<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE']; ?></td>
													<td style="width: 22%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
													<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></td>
												</tr>
											<?
													$class_col = 'trl1';
													foreach($tab_link['between_pers']['metier'] as $id_perstoview => $tab_pers) {
														if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
														$date_c = dims_timestamp2local($tab_pers['time_create']);
															echo '	<tr class="'.$class_col.'">
																		<td></td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																			<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'</a>
																		</td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																			'.$tab_pers['type_link'].'
																		</td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																			'.$date_c['date'].'
																		</td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
																			<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
																		</td>
																	</tr>';
													}
												}
												else {
													echo '<tr><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
												}
											?>
									   </tbody>
								</table>
							</div>
						<? if($_SESSION['dims']['user']['id_contact'] == $contact_id && !empty($tab_link['between_pers']['personnel'])) { ?>
						<h3 class="accordion_toggle">
							<table style="width:100%;">
								<tr>
									<td align="left" width="30%">&nbsp;</td>
									<td align="left" width="30%">
										<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												<td class="midb20">
												<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_PERSO'] ?>
												</td>
												<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											</tr>
										</table>
									</td>
									<td  style="width:30%;text-align:right">&nbsp;</td>
								</tr>
							</table>
						</h3>
							<div class="accordion_content" style="background-color:transparent;">
								<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
										<tbody>
											<?
												if(!empty($tab_link['between_pers']['personnel'])) {
											?>
												<tr class="fontgray" style="font-size:12px;">
													<td style="width: 3%;"/>
													<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_PERSONNE']; ?></td>
													<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE']; ?></td>
													<td style="width: 22%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
													<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></td>
												</tr>
											<?
													$class_col = 'trl1';
													foreach($tab_link['between_pers']['personnel'] as $id_perstoview => $tab_pers) {
														if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
														$date_c = dims_timestamp2local($tab_pers['time_create']);
															echo '	<tr class="'.$class_col.'">
																		<td></td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																			<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'</a>
																		</td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																			'.$tab_pers['type_link'].'
																		</td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																			'.$date_c['date'].'
																		</td>
																		<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
																			<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
																		</td>
																	</tr>';
													}
												}
												else {
													echo '<tr><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
												}
											?>
									   </tbody>
								</table>
							</div>
				<?
						}
					echo $skin->close_simplebloc();
				?>
			</td>
		</tr>
		<tr>
			<td width="50%" style="vertical-align:top;">
				<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_ENT'],'width:100%', 'color:#cccccc;'); ?>
				<div id="vertical_container2">
						<h3 class="accordion_toggle">
							<table style="width:100%;">
								<tr>
									<td align="left" width="30%">&nbsp;</td>
									<td align="left" width="30%">
										<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												<td class="midb20">
												<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_GEN']; ?>
												</td>
												<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											</tr>
										</table>
									</td>
									<td  style="width:30%;text-align:right">&nbsp;</td>
								</tr>
							</table>
						</h3>
						<div class="accordion_content" style="background-color:transparent;">
								<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
										<tbody>
										<?
											if(!empty($tab_link['ent_pers']['generique'])) {
										?>
											<tr class="fontgray" style="font-size:12px;">
												<td style="width: 3%;"/>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']; ?></td>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_LOCATION']; ?></td>
												<td style="width: 22%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></td>
											</tr>
										<?
												$class_col = 'trl1';
												foreach($tab_link['ent_pers']['generique'] as $id_enttoview => $tab_ent) {
													if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
													$date_c = dims_timestamp2local($tab_ent['date_create']);
														echo '	<tr class="'.$class_col.'">
																	<td></td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&ent_id='.$tab_ent['id_tiers'].'" title="Voir la fiche de ce contact.">'.$tab_ent['intitule'].'</a>
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		'.$tab_ent['ville'].'
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		'.$date_c['date'].'
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
																		<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
																	</td>
																</tr>';
												}
											}
											else {
												echo '<tr><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
											}
										?>
										</tbody>
								</table>
						</div>
						<h3 class="accordion_toggle">
							<table style="width:100%;">
								<tr>
									<td align="left" width="30%">&nbsp;</td>
									<td align="left" width="30%">
										<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												<td class="midb20">
												<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_MET']; ?>
												</td>
												<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											</tr>
										</table>
									</td>
									<td  style="width:30%;text-align:right">&nbsp;</td>
								</tr>
							</table>
						</h3>
						<div class="accordion_content" style="background-color:transparent;">
							<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
								<tbody>
									<?
											if(!empty($tab_link['ent_pers']['metier'])) {
										?>
											<tr class="fontgray" style="font-size:12px;">
												<td style="width: 3%;"/>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']; ?></td>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_LOCATION']; ?></td>
												<td style="width: 22%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
												<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></td>
											</tr>
										<?
												$class_col = 'trl1';
												foreach($tab_link['ent_pers']['metier'] as $id_enttoview => $tab_ent) {
													if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
													$date_c = dims_timestamp2local($tab_ent['date_create']);
														echo '	<tr class="'.$class_col.'">
																	<td></td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&ent_id='.$tab_ent['id_tiers'].'" title="Voir la fiche de ce contact.">'.$tab_ent['intitule'].'</a>
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		'.$tab_ent['ville'].'
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="font-weight: bold; cursor: default;" id="tickets_title_3">
																		'.$date_c['date'].'
																	</td>
																	<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
																		<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
																	</td>
																</tr>';
												}
											}
											else {
												echo '<tr><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
											}
										?>
							   </tbody>
						   </table>
						</div>
				</div>
			<? echo $skin->close_simplebloc(); ?>
			</td>
		</tr>
		<tr>
			<td width="50%" style="vertical-align:top;">
			<? echo $skin->open_simplebloc(_DIMS_LABEL_LINK_LINK,'width:100%', 'color:#cccccc;'); ?>


					<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
						<tbody>
							<tr class="fontgray" style="font-size:12px;">
								<td style="width: 3%;"/>
								<td style="width: 25%;">&Eacute;v&egrave;nement</td>
								<td style="width: 25%;">Lieu</td>
								<td style="width: 22%;">Date</td>
								<td style="width: 25%;">Organis&eacute; par</td>
							</tr>
							<tr style="background-color:#738CAD;">
								<td id="watch_notify_3" class="system_tickets_user_puce"> </td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a onclick="javascript:alert('lien vers la fiche du contact');" title="Voir la fiche de cette entreprise.">Sogertou</a></td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">Paris</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">15 F&eacute;vrier 2009</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a href="admin.php?dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=0&dims_desktop=block&dims_action=public&action=view_contact" title="Voir la fiche de ce contact.">Patrick Nourrissier</a></td>
							</tr>
							<tr style="background-color:#B5CFF1;">
								<td id="watch_notify_3" class="system_tickets_user_puce"> </td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a onclick="javascript:alert('lien vers la fiche du contact');" title="Voir la fiche de cette entreprise.">Pole de l'information</a></td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">Lille</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">13 F&eacute;vrier 2009</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a href="admin.php?dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=0&dims_desktop=block&dims_action=public&action=view_contact" title="Voir la fiche de ce contact.">Patrick Nourrissier</a></td>
							</tr>
							<tr style="background-color:#738CAD;">
								<td id="watch_notify_3" class="system_tickets_user_puce"> </td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a onclick="javascript:alert('lien vers la fiche du contact');" title="Voir la fiche de ce contact.">Rose et jardin</a></td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">Metz</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">14 F&eacute;vrier 2009</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a href="admin.php?dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=0&dims_desktop=block&dims_action=public&action=view_contact" title="Voir la fiche de ce contact.">Patrick Nourrissier</a></td>
							</tr>
							<tr style="background-color:#B5CFF1;">
								<td id="watch_notify_3" class="system_tickets_user_puce"> </td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><div id="ent1"><a onclick="javascript:alert('lien vers la fiche du contact');" title="Voir la fiche de cette entreprise.">Bricobrac</a></div></td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">Nancy</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3">11 F&eacute;vrier 2009</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;" id="tickets_title_3"><a href="admin.php?dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=0&dims_desktop=block&dims_action=public&action=view_contact" title="Voir la fiche de ce contact.">Thierry Nourrissier</a></td>
							</tr>
					   </tbody>
				   </table>

			<? echo $skin->close_simplebloc(); ?>
		</td>
	</tr>
</table>
</td>
<script type="text/javascript">
	var bottomAccordion = new accordion('vertical_container2');

	var verticalAccordions = $$('.accordion_toggle');
	verticalAccordions.each(function(accordion){
		$(accordion.next(0)).setStyle({height: '0px'});
	});
	bottomAccordion.activate($$('#vertical_container2 .accordion_toggle')[0]);

	var bottomAccordion = new accordion('vertical_container3');

	var verticalAccordions = $$('.accordion_toggle');
	verticalAccordions.each(function(accordion){
		$(accordion.next(0)).setStyle({height: '0px'});
	});
	bottomAccordion.activate($$('#vertical_container3 .accordion_toggle')[0]);

</script>
