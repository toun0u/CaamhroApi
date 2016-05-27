<?php

$list_insc = dims_load_securvalue('list_insc', dims_const::_DIMS_CHAR_INPUT, true, false);

?>
<table width="100%" cellpadding="0" cellspacing="0" style="border:#3B567E 1px solid;">
	<tr style="height:35px;">
		<td style="border-bottom:#3B567E 1px solid;padding-left:10px;background-color:#FFFFFF;">
			<?php
				$style_a = "";
				$style_b = "";
				switch($list_insc) {
					default:
					case 'list_dmd':
					case 'accept_attach':
						$style_a = "text-decoration:underline;";
						$style_b = "text-decoration:none;";
						break;
					case 'list_inscr':
						$style_b = "text-decoration:underline;";
						$style_a = "text-decoration:none;";
						break;
				}

				//on compte le nombre d'inscrits
				//1 : issus des mailing lists
				$nb_mail = array();
				$nb_insc = 0;

				$sql_ml = ' SELECT		COUNT(ct.id) as nb_ct
							FROM		dims_mod_newsletter_mailing_list ml
							INNER JOIN	dims_mod_newsletter_mailing_news mn
							ON			mn.id_mailing = ml.id
							AND			mn.id_newsletter = :idnewsletter
							INNER JOIN	dims_mod_newsletter_mailing_ct ct
							ON			ct.id_mailing = ml.id
							AND			ct.actif = 1';
				$res_ml = $db->query($sql_ml, array(
					':idnewsletter' => $id_news
				));
				if($db->numrows($res_ml)>0) {
					while($tab_res = $db->fetchrow($res_ml)) {
						$nb_mail['mailing'] = $tab_res['nb_ct'];
					}
				}
				else {
					$nb_mail['mailing'] = 0;
				}

				//2: issus des inscriptions
				$sql_ct = '    SELECT		   COUNT(ns.id_contact) as nb_ct_mail
							FROM			dims_mod_newsletter_subscribed ns
							INNER JOIN		dims_mod_business_contact c
							ON				c.id = ns.id_contact
							WHERE			ns.id_newsletter = :idnewsletter
							AND				ns.etat = 1';
				$res_ct = $db->query($sql_ct, array(
					':idnewsletter' => $id_news
				));
				if($db->numrows($res_ct)>0) {
					while($tab_res = $db->fetchrow($res_ct)) {
						$nb_mail['contact'] = $tab_res['nb_ct_mail'];
					}
				}
				else {
					$nb_mail['contact'] = 0;
				}

				$nb_insc = $nb_mail['contact'] + $nb_mail['mailing'];

			?>
			<a style="<?php echo $style_a; ?>" href="<?php echo $scriptenv; ?>?subaction=<?php echo _DIMS_NEWSLETTER_INSCR; ?>&list_insc=list_dmd"><?php echo $_DIMS['cste']['_DIMS_NEWSLETTER_DMDINSC'].' ('.count($tab_news[$id_news]['nb_dmd']).')'; //$tab_news vient de bloc_menu ?></a>&nbsp;|
			<a style="<?php echo $style_b; ?>" href="<?php echo $scriptenv; ?>?subaction=<?php echo _DIMS_NEWSLETTER_INSCR; ?>&list_insc=list_inscr"><?php echo $_DIMS['cste']['_DIMS_NEWSLETTER_LIST_INSC'].' ('.$nb_insc.')'; ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<?php

				switch($list_insc) {
					default:
					case 'list_dmd':
						require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_dmd_list.php';
						break;
					case 'accept_attach':
						require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_dmd_attach.php';
						break;
					case 'delete_attach':
						$id_dmd = dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true, true);

						if($id_dmd != 0) {
							$inscription = new newsletter_inscription();

							$inscription->open($id_dmd);

							$inscription->delete();
						}

						dims_redirect($scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd');
						break;
					case 'attach_contact':
						require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');

						if($convmeta == '' || !isset($_SESSION['dims']['contact_fields_mode'])) {
						//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
							$sql =	"
										SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
													mb.protected,mb.name as namefield,mb.label as titlefield
										FROM		dims_mod_business_meta_field as mf
										INNER JOIN	dims_mb_field as mb
										ON			mb.id=mf.id_mbfield
										RIGHT JOIN	dims_mod_business_meta_categ as mc
										ON			mf.id_metacateg=mc.id
										WHERE		  mf.id_object = :idobject
										AND			mc.admin=1
										AND			mf.used=1
										ORDER BY	mc.position, mf.position
										";
							$rs_fields=$db->query($sql, array(
								':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
							));

							$rubgen=array();
							$convmeta = array();

							while ($fields = $db->fetchrow($rs_fields)) {
								if (!isset($rubgen[$fields['id_cat']]))  {
									$rubgen[$fields['id_cat']]=array();
									$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
									$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
									if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
								}

								// on ajoute maintenant les champs dans la liste
								$fields['use']=0;// par defaut non utilise
								$fields['enabled']=array();
								if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

								$_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

								// enregistrement de la conversion
								$convmeta[$fields['namefield']]=$fields['id'];
							}
						}
						if(isset($_POST['id_dmd']) && !empty($_POST['id_dmd']) &&
						   isset($_POST['id_contact']) && !empty($_POST['id_contact'])) {
							$inscription = new newsletter_inscription();
							$inscription->open(dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true, true));

							if(!$inscription->new) {
								$contact = new contact();
								$ct_layer = new contact_layer();
								if($_POST['id_contact'] != -1) {
									$maj_ct = 0;
									$maj_ly = 0;
									$contact->open(dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true, true));

									// recherche si layer pour workspace
									$res=$db->query("SELECT id,type_layer,id_layer
													FROM dims_mod_business_contact_layer
													WHERE id= :id
													AND type_layer=1
													AND id_layer= :idlayer ", array(
											':id'		=> $contact->fields['id'],
											':idlayer'	=> $_SESSION['dims']['workspaceid']
									));

									if($db->numrows($res) > 0 ) {
										//echo "select id,type_layer,id_layer from dims_mod_business_contact_layer where id=".$contact->fields['id']." and type_layer=1 and id_layer=".$_SESSION['dims']['workspaceid']; die();
										$sel_layer = $db->fetchrow($res);
										//on charge le layer
										$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);
									}
									else {
										//on cree un layer
										$ct_layer->init_description();
										$ct_layer->fields['id'] = $contact->fields['id'];
										$ct_layer->fields['type_layer'] = 1;
										$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
									}

									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(empty($contact->fields['address']   ) && !empty($inscription->fields['adresse'])) {
												$contact->fields['address'] = $inscription->fields['adresse'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(empty($ct_layer->fields['address']	) && !empty($inscription->fields['adresse'])) {
												$ct_layer->fields['address'] = $inscription->fields['adresse'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(empty($contact->fields['postalcode']) && !empty($inscription->fields['cp'])) {
												$contact->fields['postalcode'] = $inscription->fields['cp'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(empty($ct_layer->fields['postalcode']) && !empty($inscription->fields['cp'])) {
												$ct_layer->fields['postalcode'] = $inscription->fields['cp'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(empty($contact->fields['city']	   ) && !empty($inscription->fields['ville'])) {
												$contact->fields['city'] = $inscription->fields['ville'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(empty($ct_layer->fields['city']		) && !empty($inscription->fields['ville'])) {
												$ct_layer->fields['city'] = $inscription->fields['ville'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(empty($contact->fields['country']   ) && !empty($inscription->fields['pays'])) {
												$contact->fields['country'] = $inscription->fields['pays'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(empty($ct_layer->fields['country']	) && !empty($inscription->fields['pays'])) {
												$ct_layer->fields['country'] = $inscription->fields['pays'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(empty($contact->fields['phone']	   ) && !empty($inscription->fields['tel'])) {
												$contact->fields['phone'] = $inscription->fields['tel'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(empty($ct_layer->fields['phone']		) && !empty($inscription->fields['tel'])) {
												$ct_layer->fields['phone'] = $inscription->fields['tel'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(empty($contact->fields['email']	   ) && !empty($inscription->fields['email'])) {
												$contact->fields['email'] = $inscription->fields['email'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(empty($ct_layer->fields['email']		) && !empty($inscription->fields['email'])) {
												$ct_layer->fields['email'] = $inscription->fields['email'];
												$maj_ly = 1;
											}
										}
									}

									if($maj_ly == 1) {
										$ct_layer->save();
									}
								}
								else {

									//on cree un layer
									$ct_layer->init_description();
									$ct_layer->fields['type_layer'] = 1;
									$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

									//cas particulier : nom et prenom toujours dans la table contact
									$contact->fields['lastname']	= (!empty($inscription->fields['nom'])) ? $inscription->fields['nom'] : '';
									$contact->fields['firstname']	= (!empty($inscription->fields['prenom'])) ? $inscription->fields['prenom'] : '';

									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(!empty($inscription->fields['adresse'])) {
												$contact->fields['address'] = $inscription->fields['adresse'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(!empty($inscription->fields['adresse'])) {
												$ct_layer->fields['address'] = $inscription->fields['adresse'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(!empty($inscription->fields['cp'])) {
												$contact->fields['postalcode'] = $inscription->fields['cp'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(!empty($inscription->fields['cp'])) {
												$ct_layer->fields['postalcode'] = $inscription->fields['cp'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(!empty($inscription->fields['ville'])) {
												$contact->fields['city'] = $inscription->fields['ville'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(!empty($inscription->fields['ville'])) {
												$ct_layer->fields['city'] = $inscription->fields['ville'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(!empty($inscription->fields['pays'])) {
												$contact->fields['country'] = $inscription->fields['pays'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(!empty($inscription->fields['pays'])) {
												$ct_layer->fields['country'] = $inscription->fields['pays'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(!empty($inscription->fields['tel'])) {
												$contact->fields['phone'] = $inscription->fields['tel'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(!empty($inscription->fields['tel'])) {
												$ct_layer->fields['phone'] = $inscription->fields['tel'];
												$maj_ly = 1;
											}
										}
									}
									if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
										if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
											//c'est un champ generique -> on enregistre dans contact
											if(!empty($inscription->fields['email'])) {
												$contact->fields['email'] = $inscription->fields['email'];
												$maj_ct = 1;
											}
										}
										else {
											//c'est un champ metier -> on enregistre dans un layer
											if(!empty($inscription->fields['email'])) {
												$ct_layer->fields['email'] = $inscription->fields['email'];
												$maj_ly = 1;
											}
										}
									}
								}
								$id_ct = $contact->save();
								if($maj_ly == 1) {
									$ct_layer->fields['id'] = $contact->fields['id'];
									$ct_layer->save();
								}

								$to[0]['name']	   = $contact->fields['lastname'].' '.$contact->fields['firstname'];
								$to[0]['address']  = $contact->fields['email'];

								$subscribed = new news_subscribed();

								$subscribed->fields['id_newsletter']		= $id_news;
								$subscribed->fields['id_contact']			= $id_ct;
								$subscribed->fields['date_inscription']		= date('YmdHis');
								$subscribed->fields['date_desinscription']	= '';
								$subscribed->fields['etat']					= 1;

								$subscribed->save();
								$workspace = new workspace();
								$workspace->open($_SESSION['dims']['workspaceid']);
								$email = $workspace->fields['newsletter_sender_email'];
								if ($email=="") $email=_DIMS_ADMINMAIL;

								$from[0]['name']   = 'luxembourgforbusiness';
								$from[0]['address']= $email;

								$inf_news = new newsletter();
								$inf_news->open($id_news);

								$inscription->delete();
							}
						}

						dims_redirect($scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd');
						break;
					case 'search_ct': //ATTENTION : ce case n'a pas de break car il va avec le case 'list_inscr'
						$search = dims_load_securvalue('ct_search',dims_const::_DIMS_CHAR_INPUT,true,true);

						$view = '';
						if(isset($search) && $search != '') {
							$sql_sch = 'SELECT * FROM dims_mod_business_contact
										WHERE inactif = 0
										AND (lastname LIKE :search OR firstname LIKE :search )
										ORDER BY lastname, firstname';
							$res_sch = $db->query($sql_sch, array(
								':search'	=> $search.'%'
							));

							$sql_mail = 'SELECT * FROM dims_mod_newsletter_mailing_list WHERE label LIKE :search AND id_user_create =  :userid ';
							$res_mail = $db->query($sql_mail, array(
								':search'	=> $search,
								':userid'	=> $_SESSION['dims']['userid']
							));

							if($db->numrows($res_sch) > 0) {
								$clas = 'trl1';
								$view .= '	<tr>
												<td width="100%">
													<form method="POST" id="valid_insc" name="valid_insc" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_INSC_FROMBACK.'&id_news='.$id_news.'">';
								// Sécurisation du formulaire par token
								require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
								$token = new FormToken\TokenField;
								$token->field("id_contact");
								$tokenHTML = $token->generate();
								$view .= $tokenHTML;
								$view .= 			'<table width="100%" cellpadding="0" cellspacing="0">
														<tr class="trl1" style="font-size:14px;">
															<td>'.$_DIMS['cste']['_DIMS_LABEL_CONTACTS'].'</td>
															<td>'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
															<td></td>
														</tr>';
								while($tab_sch = $db->fetchrow($res_sch)) {
									if($clas == 'trl1') $clas = 'trl2';
									else $clas = 'trl1';

									$nom = '';
									$nom .= '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$tab_sch['id'].'">'.strtoupper($tab_sch['lastname']).' '.$tab_sch['firstname'].'</a>';

									$view .= '			<tr class="'.$clas.'">
															<td>'.$nom.'</td>
															<td>'.$tab_sch['email'].'</td>
															<td><input type="radio" name="id_contact" id="id_contact" value="'.$tab_sch['id'].'"/></td>
														</tr>';

								}
								$view .= '				<tr>
															<td colspan="3" align="center">
															'.dims_create_button($_DIMS['cste']['_DIMS_VALID'], './common/img/publish.png', 'javascript:document.valid_insc.submit();', '', 'float:left;').'
															</td>
														</tr>
													</table>
													</form>
												</td>
											</tr>';
							}
							else {
								$view .= '<tr><td style="font-size:13px;color:#ff0000;">'.substr($_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR'], 0, -43).'.</td></tr>';
							}
							//gestion de la recherche des listes rattachables
							if($db->numrows($res_mail) > 0) {
								$clas = 'trl1';
								$view .= '	<tr>
												<td width="100%">
													<form method="POST" id="valid_mail" name="valid_mail" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_INSC_FROMBACK.'&id_news='.$id_news.'">';
								// Sécurisation du formulaire par token
								require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
								$token = new FormToken\TokenField;
								$token->field("mailing", "1");
								$token->field("id_mailing");
								$tokenHTML = $token->generate();
								$view .= $tokenHTML;
								$view .= 			'<input type="hidden" name="mailing" value="1"/>
													<table width="100%" cellpadding="0" cellspacing="0">
														<tr class="trl1" style="font-size:14px;">
															<td>'.$_DIMS['cste']['_DIMS_LIST'].'</td>
															<td>'.$_DIMS['cste']['_DIMS_COMMENTS'].'</td>
															<td></td>
														</tr>';
								while($tab_sch = $db->fetchrow($res_mail)) {
									if($clas == 'trl1') $clas = 'trl2';
									else $clas = 'trl1';

									$nom .= '<a href="">'.strtoupper($tab_sch['label']).'</a>';
									$comment = substr($tab_sch['comment'], 0, 50);
									$view .= '			<tr class="'.$clas.'">
															<td>'.$nom.'</td>
															<td>'.$comment.'</td>
															<td><input type="radio" name="id_mailing" id="id_mailing" value="'.$tab_sch['id'].'"/></td>
														</tr>';

								}
								$view .= '				<tr>
															<td colspan="3" align="center">
															'.dims_create_button($_DIMS['cste']['_DIMS_VALID'], './common/img/publish.png', 'javascript:document.valid_mail.submit();', '', 'float:left;').'
															</td>
														</tr>
													</table>
													</form>
												</td>
											</tr>';
							}
							else {
								//$view .= '<tr><td style="font-size:13px;color:#ff0000;">'.substr($_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR'], 0, -43).'.</td></tr>';
							}
						}
					case 'list_inscr':
						require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter_list_subscribed.php');
						break;
				}

			?>
		</td>
	</tr>
</table>
