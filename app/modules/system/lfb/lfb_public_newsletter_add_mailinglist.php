<?php

//en cas de modif on recupere une id_mailing
$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
$search_val = dims_load_securvalue('search_val', dims_const::_DIMS_CHAR_INPUT, true,true);

$label = '';
$comment = '';

if(isset($id_mail) && $id_mail != '') {
///////// DEBUT MODIF MAILINGLIST //////
	$list = new mailing();
	$list->open($id_mail);

	//informations utilisees pour le formulaire
	$label = $list->fields['label'];
	$comment = $list->fields['comment'];

	//on insere un formulaire pour (1) le rattachement aux news ou (2) l'import Outlook ou (3) un rattachement direct sur un email
	//(1)
	$opt_news = '';
	$form_news = '';
	$tab_news = '';
	$params =array();
	$sql = 'SELECT      n.id,
						n.label,
						mn.id as id_link,
						mn.id_newsletter
			FROM        dims_mod_newsletter n
			LEFT JOIN 	dims_mod_newsletter_mailing_news mn
			ON 			mn.id_newsletter = n.id
			AND 		mn.id_mailing = :idmailing
			WHERE       n.id_workspace in ('.$db->getParamsFromArray($listworkspace_nl, 'idworkspace', $params).')
			ORDER BY    n.timestp_create DESC';
	$params[':idmailing'] = $id_mail;
	// AND         (n.id_user_responsible = '.$_SESSION['dims']['userid'].'
	//		OR          n.id_user_create = '.$_SESSION['dims']['userid'].')

	$res = $db->query($sql, $params);

	if($db->numrows($res) > 0) {
		//construction du tableau de donnees
		while($tab_res = $db->fetchrow($res)) {
			$tab_news[$tab_res['id']] = $tab_res;
		}

		//mise en forme des donnees
		$view_news = '';
		$class = 'class="trl1"';
		$tab_rattach = '';
		foreach($tab_news as $id_n => $news) {

			$opt_news .= '<option value="'.$news['id'].'">'.$news['label'].'</option>';
			//si $news['id_newsletter'] != '', c'est un rattachement
			if($news['id_link'] != '') {
				if($class == 'class="trl1"') $class = 'class="trl2"';
				else $class = 'class="trl1"';

				$tab_rattach .= '<tr '.$class.'>
									<td>'.$news['label'].'</td>
									<td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_SUPP_LIST.'&id_link='.$news['id_link'].'&id_mail='.$id_mail.'"><img src="./common/img/delete.png"/></a></td>
								</tr>';
				$view_news = 1;
			}
		}
		//on insere $form_news (1) uniquement si l'utilisateur a deja des newsletters
		$form_news = '<tr>
						<td>
							<form id="link_news" name="link_news" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SAVE_RATTACH_NEWS.'" method="POST">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_mail",$id_mail);
		$token->field("news_linked");
		$tokenHTML = $token->generate();
		$form_news .= $tokenHTML;
		$form_news .= '		<input type="hidden" name="id_mail" value="'.$id_mail.'"/>
							<table>
								<tr>
									<td colspan="2" style="font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_LINK_NEWSLETTER_MAILING'].'</td>
								</tr>
								<tr>
									<td>
										<select id="news_linked" name="news_linked">
											<option value="">--</option>
											'.$opt_news.'
										</select>
									</td>
									<td>';
		$form_news .=  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.link_news.submit();');
		$form_news .= '				</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>';
		if($view_news != '') {
			//on affiche le tableau des news rattachees a la liste
			$form_news .= '<tr>
						<td>
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr class="trl1">
									<td colspan="2">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_LIST_LINKEDTO'].'</td>
								</tr>
								'.$tab_rattach.'
							</table>
						</td>
					</tr>';
		}
	}

	$form_modif = 	'<td>
						<table>
							'.$form_news.'
							<tr>
								<td style="border-bottom:#000000 1px dotted;padding-bottom:10px;">&nbsp;</td>
							</tr>
							<tr>
								<td width="100%">
									<form id="add_email" name="add_email" method="POST" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SAVE_RATTACH_EMAIL.'&id_mail='.$id_mail.'">';
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("add_mail");
	$tokenHTML = $token->generate();
	$form_modif .= $tokenHTML;
	$form_modif .= '						<table width="100%">
										<tr>
											<td style="font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_MAILING_ADD_EMAIL'].'</td>
											<td><input type="text" name="add_mail" id="add_mail" value=""/></td>
											<td>';
	$form_modif .= dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.add_email.submit();');
	$form_modif .= 					'		</td>
										</tr>
									</table>
									</form>
								</td>
							</tr>
							<tr>
								<td style="border-bottom:#000000 1px dotted;padding-bottom:10px;">&nbsp;</td>
							</tr>
							<tr>
								<td>
									<form id="import_email" name="import_email" method="POST" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_IMPORT_EMAIL.'&id_mail='.$id_mail.'" enctype="multipart/form-data">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("doc_mail");
		$tokenHTML = $token->generate();
		$form_modif .= $tokenHTML;
		$form_modif .= '				<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td style="font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT'].'</td>
										</tr>
										<tr>
											<td>
												<img src="./common/img/important_small.png"/>
												'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT_DANGER'].'
											</td>
										</tr>
										<tr>
											<td align="center">
												<div style="float:left">
													<a style="texte-decoration:none;" onclick="javascript:displayImportExample(event,\'ct\');">
														Example
													</a>
												</div>
												<input type="file" id="doc_mail" name="doc_mail"/>
											</td>
										</tr>
										<tr>
											<td colspan="2">';
	$form_modif .= dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.import_email.submit();');
	$form_modif .= 					'		</td>
										</tr>';
    if(isset($error_imp) && $error_imp != '') {
        $form_modif .=                  '<tr>
                                            <td align="center" style="color:#ff0000;font-weight;bold;font-size:13px;">'.$error_imp.'</td>
                                        </tr>';
    }
    if(isset($nb_imp) && $nb_imp != 0) {
        $form_modif .=                  '<tr>
                                            <td align="center">'.$nb_imp.' '.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT_OK'].'</td>
                                        </tr>';
    }
	$form_modif .=                  '</table>
									</form>
								</td>
							</tr>
						</table>
					</td>';

	//on passe l'id dans le post pour la sauvegarde des modifs
	$input_hidden = '<input type="hidden" name="id_mail" value="'.$id_mail.'"/>';

	//largeur des colonnes
	$td_width = 'width="50%"';
	$colspan = 'colspan="2"';

	$title = $_DIMS['cste']['_DIMS_LABEL_MODIF_MAILINGLIST'].' : '.$label;

/////// FIN MODIF MAILINGLIST ////////
}
else {
	$title = $_DIMS['cste']['_DIMS_NEWSLETTER_ADD_LIST_MAILING'];
	$input_hidden = '';
	$form_modif = '';
	$td_width = 'width="100%"';
	$colspan = '';
}

echo $skin->open_simplebloc($title);
echo '<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td '.$colspan.'>';
				//echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'\'');
echo 		'</td>
		</tr>
		<tr>
			<td '.$td_width.'>
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<form action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SAVE_LIST_EMAIL.'" method="post" id="add_list" name="add_list">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("id_mail");
$token->field("list_label");
$token->field("list_comment");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo $input_hidden.'
							<table>
								<tr>
									<td>'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</td>
									<td>
										<input type="text" id="list_label" name="list_label" value="'.$label.'"/>
									</td>
								</tr>
								<tr>
									<td>'.$_DIMS['cste']['_DIMS_COMMENTS'].'</td>
									<td>
										<textarea id="list_comment" name="list_comment">'.$comment.'</textarea>
									</td>
								</tr>
								<tr>
									<td align="center" colspan="2">';
										echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.add_list.submit();');
echo 								'</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>';

echo 			'</table>
			</td>
			'.$form_modif.'
		</tr>
		<tr>
			<td '.$colspan.'>';
				//echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'\'');
echo 		'</td>
		</tr>
	</table>';
echo $skin->close_simplebloc();

if(isset($id_mail) && $id_mail != '') {
	echo $skin->open_simplebloc($_SESSION['cste']['_DIMS_LABEL_MAILING_EMAIL_LIST']);

	$params = array();
	$sql = 'SELECT 	*
			FROM 	dims_mod_newsletter_mailing_ct
			WHERE 	id_mailing = :idmailing ';
	$params[':idmailing'] = $id_mail;
	if($search_val != '') {
		$sql .= ' AND email LIKE :email ';
		$params[':email'] = "%".$search_val."%";
	}
	$sql .= ' ORDER by email';
	$res = $db->query($sql, $params);

	if (!isset($_SESSION['dims']['viewduplicateemails'])) $_SESSION['dims']['viewduplicateemails']=0;


	$viewduplicateemails = dims_load_securvalue('viewduplicateemails', dims_const::_DIMS_CHAR_INPUT, true, true,false);
	if ($viewduplicateemails=='on') {
		$_SESSION['dims']['viewduplicateemails']=1;
	}
	else {
		$_SESSION['dims']['viewduplicateemails']=0;
	}

	$duplicateemails= array();
	while($tab_mail = $db->fetchrow($res)) {
		if ($tab_mail['actif'] == 1) {
			if (!isset($duplicateemails[$tab_mail['email']])) {
				$duplicateemails[$tab_mail['email']]=1;
			}
			else {
				// on a deja
				$duplicateemails[$tab_mail['email']]++;
			}
		}
	}
	$res = $db->query($sql, $params);

		echo 	'<div style="width:100%;height:250px;overflow:auto;">
					<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
						<tr>
							<td style="font-size:11px;padding:10px;" align="right" colspan="3">
								<form id="search_email" name="search_email" method="POST" action="'.$scriptenv.'?action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.'">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_mail");
		$token->field("viewduplicateemails");
		$token->field("search_val");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo $input_hidden.'		<input type="hidden" name="id_mail" value="'.$id_mail.'"/>
									<span style="float:left;">'.$_DIMS['cste']['_SEE_DUPLICATE_EMAIL'].' :
									<input type="checkbox" name="viewduplicateemails"';

									if ($_SESSION['dims']['viewduplicateemails']) {
										echo "checked";
									}
									echo '"></span>

									'.dims_create_button($_DIMS['cste']['_DIMS_FILTER'],'./common/img/search.png', 'javascript:document.search_email.submit();', '', 'float:left;').'&nbsp;
									<label>'.$_DIMS['cste']['_DIMS_LABEL_SEARCH_FOR_CT'].' : </label>
									<input type="text" name="search_val" id="search_val" value="'.$search_val.'"/>
									'.dims_create_button($_DIMS['cste']['_SEARCH'], './common/img/search.png', 'javascript:document.search_email.submit();', '', 'float:right;').'
								</form>
							</td>
						</tr>
						<tr class="trl1">
							<td style="width:2%"></td>
							<td align="left" width="70%" style="padding-left:10px;">'.$_SESSION['cste']['_DIMS_LABEL_EMAIL'].'s ('.$db->numrows($res).')</td>
							<td>'.$_SESSION['cste']['DUPLICATE_EMAIL'].'</td>
							<td></td>
						</tr>';
		$class = "trl1";
	if($db->numrows($res) > 0) {
		//echo $_SESSION['dims']['viewduplicateemails'];
		while($tab_mail = $db->fetchrow($res)) {
			if ($_SESSION['dims']['viewduplicateemails']==0 || ($_SESSION['dims']['viewduplicateemails']==1 && $duplicateemails[$tab_mail['email']]>1)) {
				if($class == 'trl1') $class = 'trl2';
				else $class = 'trl1';

				$etat = '';
				if($tab_mail['actif'] == 1) {
					$etat = '<img src="./common/modules/system/img/ico_point_green.gif" title="a newsletter will be send to this email, clic here to change state (without deleting)"/>';
				}
				else {
					$etat = '<img src="./common/modules/system/img/ico_point_red.gif" title="a newsletter will not be send to this email, clic here to change state."/>';
				}

				echo 		'<tr class="'.$class.'">
								<td>
									<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_CHG_EMAIL_STATE.'&id_state_mail='.$tab_mail['id'].'&id_mail='.$id_mail.'">
										'.$etat.'
									</a>
								</td>
								<td style="padding-left:10px;">'.$tab_mail['email'].'</td><td>';

				if (isset($duplicateemails[$tab_mail['email']]) && $duplicateemails[$tab_mail['email']]>1) {
					echo '<img src="/common/modules/system/img/ico_point_orange.gif">';
				}
				else {
					echo '&nbsp;';
				}

				echo '
								</td><td>
									<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_SUPP_EMAIL.'&id_supp_mail='.$tab_mail['id'].'&id_mail='.$id_mail.'&search_val='.$search_val.'&viewduplicateemails='.$viewduplicateemails.'">
										<img src="./common/img/delete.png"/>
									</a>
								</td>
							</tr>';
			}
		}
		unset($duplicateemails);
		echo 		'</table>
				</div>';
	}
	else {
		echo '<table width="100%"><tr><td align="center" width="100%">'.$_SESSION['cste']['_DIMS_LABEL_MAILING_NO_EMAIL'].'</td></tr></table>';
	}
	echo $skin->close_simplebloc();
}
?>
