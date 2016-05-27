<?php
require_once(DIMS_APP_PATH . "/modules/system/desktop_mailinglist_left.php");

echo '<div style="width:65%;float:right;margin-right:20px;">';

//en cas de modif on recupere une id_mailing
$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
$label = '';
$comment = '';

if(isset($id_mail) && $id_mail != '' && $id_mail > 0) {
///////// DEBUT MODIF MAILINGLIST //////
	$list = new list_diff();
	$list->open($id_mail);

	//informations utilisees pour le formulaire
	$label = $list->fields['label'];
	$comment = $list->fields['comment'];

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

$token = new FormToken\TokenField;
$token->field("op");
$token->field("id_mail",$id_mail);
$token->field("fck_list_label");
$token->field("fck_list_comment");


echo $skin->open_simplebloc($title);

//séparation
echo '	<div style="float: left; width:100%; font-family: Helvetica; padding-top: 3px; padding-bottom: 3px; padding-left: 3px; font-size: 18px;
					background-color: #DADADA; font-weight: bold; font-style: italic; margin-top: 2px; border-bottom: 2px solid #999;" >
				'.$_DIMS['cste']['_INFOS_LABEL'].'
			</div>';

echo '<div style="width:100%;float:left;margin-top:20px;">';
echo '<table style="margin-bottom:20px;" width="100%" cellpadding="0" cellspacing="0">';
echo	'<tr>
			<td '.$td_width.'>
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<form action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&dims_desktop=block&dims_action=public&action=save_mailinglist" method="post" id="add_list" name="add_list">
							'.$token->generate().$input_hidden.'
							<table>
								<tr>
									<td>'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'&nbsp;*</td>
									<td>';
if (isset($id_mail) && $id_mail != ''){
	if ($id_mail > 0)
		echo							'<input type="text" id="fck_list_label" name="fck_list_label" value="'.$label.'"/>';
	else echo							'<input style="background-color: #EF3B3B;" type="text" id="fck_list_label" name="fck_list_label" value="'.$label.'"/>';
}else echo								'<input type="text" id="fck_list_label" name="fck_list_label" value="'.$label.'"/>';
echo								'</td>
								</tr>
								<tr>
									<td>'.$_DIMS['cste']['_DIMS_COMMENTS'].'</td>
									<td>
										<textarea id="fck_list_comment" name="fck_list_comment">'.$comment.'</textarea>
									</td>
								</tr>
								<tr>
									<td align="center" colspan="2">';
										echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.add_list.submit();');
echo								'</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
					</table>';
echo		'</td>
		</tr>
	</table>
	</div>';

echo '<script language="JavaScript" type="text/JavaScript">
		window.onload=function(){
	document.getElementById("fck_list_label").focus();
}
</script>';

if(isset($id_mail) && $id_mail != '' && $id_mail > 0) {
	// séparation
	echo '	<div style="float: left; width:100%; font-family: Helvetica; padding-top: 3px; padding-bottom: 3px; padding-left: 3px; font-size: 18px;
					background-color: #DADADA; font-weight: bold; font-style: italic; margin-top: 2px; border-bottom: 2px solid #999;" >
				'.$_DIMS['cste']['_SYSTEM_MANAGE_CONTACT'].'
			</div>';

	// contacts déjà ajoutés
	echo '<div style="width:100%">';
	echo '<div style="width:40%; float:left;">';
	echo $skin->open_simplebloc($_SESSION['cste']['_DIMS_LABEL_MAILING_EMAIL_LIST']);

	$sql = 'SELECT * FROM dims_mailing_email WHERE id_list = :idlist ORDER by email';
	$res = $db->query($sql, array(
		':idlist' => $id_mail
	));

	if($db->numrows($res) > 0) {
		echo	'<div style="width:100%;height:250px;overflow:auto;">
					<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
						<tr class="trl1">
							<td></td>
							<td align="left" width="100%" style="padding-left:10px;">'.$_SESSION['cste']['_DIMS_LABEL_EMAIL'].'</td>
							<td></td>
						</tr>';
		$class = "trl1";
		while($tab_mail = $db->fetchrow($res)) {
			if($class == 'trl1') $class = 'trl2';
			else $class = 'trl1';

			$etat = '';
			if($tab_mail['actif'] == 1) {
				$etat = '<img src="./common/modules/system/img/ico_point_green.gif" title="Actif"/>';
			}
			else {
				$etat = '<img src="./common/modules/system/img/ico_point_red.gif" title="Inactif"/>';
			}

			echo		'<tr class="'.$class.'">
							<td>
								<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&dims_desktop=block&dims_action=public&action=change_state_email&id_state_mail='.$tab_mail['id'].'&id_mail='.$id_mail.'">
									'.$etat.'
								</a>
							</td>
							<td style="padding-left:10px;">'.$tab_mail['email'].'</td>
							<td>
								<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&dims_desktop=block&dims_action=public&action=delete_email&id_supp_mail='.$tab_mail['id'].'&id_mail='.$id_mail.'">
									<img src="./common/img/delete.png"/>
								</a>
							</td>
						</tr>';

		}
		echo		'</table>
				</div>';
	}
	else {
		echo '<table width="100%"><tr><td align="center" width="100%">'.$_SESSION['cste']['_DIMS_LABEL_MAILING_NO_EMAIL'].'</td></tr></table>';
	}
	echo $skin->close_simplebloc();
	echo '</div>';


$token = new FormToken\TokenField;
$token->field("add_mail");

	echo '<div style="width:55%;float:right;margin-top:20px;margin-bottom:20px;">';
	echo	'<table width="100%">
				<tr>
					<td width="100%">
						<form id="add_email" name="add_email" method="POST" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&dims_desktop=block&dims_action=public&action=save_email&id_mail='.$id_mail.'">
						'.$token->generate().'
							<table width="100%">
								<tr>
									<td style="font-size:13px;width:30%;">'
										.$_DIMS['cste']['_DIMS_LABEL_MAILING_ADD_EMAIL'].
									'</td>
									<td style="width:55%;">';
	if(isset($_SESSION['dims']['mailing']['erreur_mail'])){
		echo							'<input style="background-color: #FE9292;" type="text" name="add_mail" id="add_mail" value="'.$_SESSION['dims']['mailing']['erreur_mail'].'"/>';
		unset($_SESSION['dims']['mailing']['erreur_mail']);
	}
	else echo							'<input type="text" name="add_mail" id="add_mail" value=""/>';
	echo							'</td>
									<td style="width:15%;">';
	echo								dims_create_button($_DIMS['cste']['_DIMS_ADD'], './common/img/add.gif', 'javascript:document.add_email.submit();');
	echo							'</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>';

$token = new FormToken\TokenField;
$token->field("doc_mail");

				echo '<tr>
					<td>
						<form style="margin-top:20px;" id="import_email" name="import_email" method="POST" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&dims_desktop=block&dims_action=public&action=import_email&id_mail='.$id_mail.'" enctype="multipart/form-data">'.$token->generate().'
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td style="font-size:13px;width:30%;">'
										.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT'].'
									</td>
									<td style="width:55%;">
										<input type="file" id="doc_mail" name="doc_mail"/>
									</td>
									<td style="width:15%;">';
	echo								dims_create_button($_DIMS['cste']['_DIMS_ADD'], './common/img/add.gif', 'javascript:document.import_email.submit();');
	echo							'</td>
								</tr>
								<tr>
									<td colspan="3">
										<img src="./common/img/important_small.png"/>&nbsp;'
										.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT_DANGER'].'
									</td>
								</tr>';
	if(isset($error_imp) && $error_imp != '') {
		echo					'<tr>
									<td align="center" style="color:#ff0000;font-weight;bold;font-size:13px;">'.$error_imp.'</td>
								</tr>';
	}
	if(isset($nb_imp) && $nb_imp != 0) {
		echo					'<tr>
									<td align="center">'.$nb_imp.' '.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT_OK'].'</td>
								</tr>';
	}
	echo					'</table>
						</form>
					</td>
				</tr>';

$token = new FormToken\TokenField;
$token->field("ct_search");

				echo '<tr>
					<td>
						<form style="margin-top:20px;" id="search_ct" name="search_ct" method="POST" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&dims_desktop=block&dims_action=public&action=list_search_contact&id_mail='.$id_mail.'">
							'.$token->generate().'
							<table width="100%">
								<tr>
									<td style="font-size:13px;width:30%;">'
										.$_DIMS['cste']['_ADD_CT'].' :&nbsp;
									</td>
									<td style="width:55%;">
										<input type="text" name="ct_search" id="ct_search" value="';
	if(isset($search) && $search != '')
		echo							$search;
	echo								'"/>
									</td>
									<td style="width:15%;">
										<div style="float:right;"';
	echo									dims_create_button($_DIMS['cste']['_SEARCH'], './common/img/search.png', 'javascript:document.search_ct.submit();', '', 'float:left;');
	echo								'</div>
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>';
	echo	'<table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;" >';
	echo		$view;
	echo	'</table>';
	echo '</div>';

}

echo $skin->close_simplebloc();
echo '</div>';
?>
