<?php

$params = array();
$sql = 'SELECT 		*
		FROM 		dims_mod_newsletter_mailing_list
		WHERE 		id_workspace in ('.$db->getParamsFromArray($listworkspace_nl, 'idworkspace', $params).")";

$res = $db->query($sql, $params);

while ($tab_mail = $db->fetchrow($res)) {

	$tab_mailing[$tab_mail['id']] = $tab_mail;

	//on compte le nbr de mails dans la liste
	if(!isset($tab_mailing[$tab_mail['id']]['nb_mail'])) {
		$sqlct = 'SELECT 	id
				  FROM 		dims_mod_newsletter_mailing_ct
				  WHERE 	actif =1
				  AND 		id_mailing = :idmailing ';
		$resct = $db->query($sqlct, array(
			':idmailing' => $tab_mail['id']
		));
		$tab_mailing[$tab_mail['id']]['nb_mail'] = $db->numrows($resct);
	}

	//on compte le nbr de news rattache a la liste
	if(!isset($tab_mailing[$tab_mail['id']]['nb_news'])) {
		$sqlnews = 'SELECT 	id
					FROM 	dims_mod_newsletter_mailing_news
					WHERE 	id_mailing = :idmailing ';
		$resnews = $db->query($sqlnews, array(
			':idmailing' => $tab_mail['id']
		));
		$tab_mailing[$tab_mail['id']]['nb_news'] = $db->numrows($resnews);
	}
}

$class = "trl1";
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_NEWSLETTER_YOUR_MAILING_LIST']);
echo '<table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding:5px 0;">';
                    echo dims_create_button($_DIMS['cste']['_DIMS_NEWSLETTER_ADD_LIST_MAILING'], './common/img/add.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.'\'');
echo '          </td>
			</tr>';
if($db->numrows($res) > 0) {

	echo '		<tr>
					<td>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr class="trl1">
								<td>'.$_DIMS['cste']['_DIMS_LABEL'].'</td>
								<td>'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NBINSC_ACTIF'].'</td>
								<td>'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NB'].'</td>
								<td>'.$_DIMS['cste']['_DIMS_ACTIONS'].'</td>
							</tr>';
							$class = 'class="trl1"';
					foreach ($tab_mailing as $id_m => $tab_mail) {
						if($class == 'class="trl1"') $class = 'class="trl2"';
						else $class = 'class="trl1"';
						$comment = substr($tab_mail['comment'], 0, 50);
						echo 	'<tr '.$class.'>
									<td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.'&id_mail='.$tab_mail['id'].'">'.$tab_mail['label'].'</a></td>
									<td>'.$tab_mail['nb_mail'].'</td>
									<td>'.$tab_mail['nb_news'].'</td>
									<td>
										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'&submail='._NEWSLETTER_ACTION_ADD_LIST.'&id_mail='.$tab_mail['id'].'">
											<img src="./common/img/edit.gif"/>
										</a>&nbsp;&nbsp;
										<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action=delete_mailing_list&id_mail='.$tab_mail['id'].'\',\''.$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTDELETE'].'\');">
											<img src="./common/img/delete.gif"/>
										</a>
									</td>
								</tr>';

					}
	echo '				</table>
					</td>
				</tr>';
}
else {
	//$_DIMS['cste']['']
	echo '		<tr>
					<td>'.$_DIMS['cste']['_DIMS_MAILING_NO_LIST'].'</td>
				</tr>';
}
echo 			'<tr>
					<td>';
						//echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&init=1\'');
echo 				'</td>
				</tr>
			</table>';
echo $skin->close_simplebloc();

?>
