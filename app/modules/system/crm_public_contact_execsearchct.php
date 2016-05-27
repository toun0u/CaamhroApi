<?php

//affichage du formulaire de recherche
//$include = './common/modules/system/crm_public_contact_search.php';

//affichage des resultats
if($db->numrows($res_s) > 0) {
	$nb_res = $db->numrows($res_s);
	echo '
						<table width="100%">
							<tr>
								<td align="center">
								'.$skin->open_widgetbloc($_DIMS['cste']['_DIMS_SEARCH_RESULT']." : ".$nb_res, 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '').'
									<div style="width:100%;height:250px;overflow:auto;margin-top:10px;">';

if($nb_res > 100) {
    echo '<table width="100%" cellpadding="0" cellspacing="0" style="color:#ff0000;">
                <tr>
                    <td>'.$_DIMS['cste']['_DIMS_LABEL_TOO_MUCH_RES_SEARCH'].'
                    </td>
                </tr>
           </table>';
}
	echo 							'	<table width="100%" cellpadding="0" cellspacing="0" style="border:#536485 1px solid">
										<tr class="trl1" style="font-size:13px;">
											<td>'.$_DIMS['cste']['_DIMS_LABEL_PERSONNE'].'</td>
											<td>'.$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM'].'</td>';
	if($where_ent != '' || $where_lkent != '') {
		echo '<td>'.$_DIMS['cste']['_TYPE'].'</td>
			<td>'.$_DIMS['cste']['_DIMS_LABEL_ENT_NAME'].'</td>
			<td>'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'</td>';
	}
	echo '								<td></td>
										<td>'.$_DIMS['cste']['_INFOS_STATE'].'</td>
                                                                                    <td>'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
										</tr>';
		$class_col = 'trl1';
		$firstmail = '';
		$firstmailcc = '';
		$tab_mailcc = '';
		$checkmail=array();
		$i = 1;
		while($tab_res = $db->fetchrow($res_s)) {
            if($i <= 100) {
                if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
                $name = strtoupper($tab_res['lastname'])." ".$tab_res['firstname'];
                $date_mod = dims_timestamp2local($tab_res['timestp_modify']);

                echo '<tr class="'.$class_col.'">
                    <td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$tab_res['id_ct'].'">'.$name.'</a></td>
                    <td>'.$date_mod['date'].'</td>';

                if($where_ent != '' || $where_lkent != '') {
                    echo '<td>'.$tab_res['type_lien'].'</td>
                        <td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ENT_FORM.'&part='._BUSINESS_TAB_ENT_IDENTITE.'&id_ent='.$tab_res['id_ent'].'">'.$tab_res['intitule'].'</a></td>
                        <td>'.$tab_res['function'].'</td>';
                }
                echo '<td style="padding-top:5px;">';

                /*if($tab_res['inactif'] == 1) {
                    echo '<img src="./common/img/important_small.png" title="'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'"/>';
                }*/
				//echo '<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACTSSEEK.'&op=export_pdf&contact_id='.$tab_res['id_ct'].'"></a>';
                echo '</td><td>';

                $isdoublon=false;
                //enregistrement des adresses mails pour le mailto
                if($tab_res['inactif'] == 0) {
                    echo '<img src="/common/modules/system/img/ico_point_green.gif" alt="email">';
                    if (!isset($checkmail[$tab_res['email']])) { //test des doublons d'email
                        $checkmail[$tab_res['email']]=$tab_res['email'];
                        if($firstmail == '') $firstmail = $tab_res['email'];
                        elseif($firstmailcc == '') $firstmailcc = "?bcc=".$tab_res['email']."";
                        else $tab_mailcc .= "&bcc=".$tab_res['email']."";
                    }
                    else {
                        $isdoublon=true;
                    }
                }
                else echo '<img src="/common/modules/system/img/ico_point_red.gif" alt="email">';
                echo"</td><td>";

                if ($tab_res['email']=='') {
                    echo '<img src="/common/modules/system/img/ico_point_red.gif" alt="email">';
                }
                else {
                    if ($isdoublon) {
                        echo '<img src="/common/modules/system/img/ico_point_orange.gif" alt="email">&nbsp;<a href="mailto:'.$tab_res['email'].'">'.$tab_res['email'].'</a>';
                    }
                    else {
                        echo '<img src="/common/modules/system/img/ico_point_green.gif" alt="email">&nbsp;<a href="mailto:'.$tab_res['email'].'">'.$tab_res['email'].'</a>';
                    }
                }
                echo '</td></tr>';
            }
            $i++;
		}

		echo '</table>
			</div>'.$skin->close_widgetbloc().'
				</td>
			</tr>
			<tr>
				<td align="center">';

		echo "<div style=\"100%\">";
		if($firstmailcc != '') {
			if($tab_mailcc != '') $firstmailcc .= $tab_mailcc;
			$firstmail.=$firstmailcc;
		}
		if ($firstmail!="")
			echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_EMAIL_SEND'],'./common/img/icon_tickets.gif','javascript:document.location.href=\'mailto:'.$firstmail.'\';','','');

		echo dims_create_button($_DIMS['cste']['_FORMS_EMAIL_EXPORT'],'./common/img/export.png','javascript:document.location.href=\'admin.php?op=exportsearchpers_mail\';','','');
		echo dims_create_button($_DIMS['cste']['_FORMS_ADR_EXPORT'],'./common/img/export.png','javascript:document.location.href=\'admin.php?op=exportsearchpers_adr\';','','');
		echo dims_create_button($_DIMS['cste']['_FORMS_DATA_EXPORT'],'./common/img/export.png','javascript:document.location.href=\'admin.php?op=exportsearchpers\';','','');
		echo dims_create_button('Label','./common/img/pdf.png','javascript:document.location.href=\'admin.php?op=exportsearchpersetiquette\';','','');

/*									<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_FORMS_DATA_EXPORT'].'" onclick="javascript:document.location.href=\'admin.php?op=exportsearchpers\';"/>
									<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_FORMS_ADR_EXPORT'].'" onclick="javascript:document.location.href=\'admin.php?op=exportsearchpers_adr\';"/>
									<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_FORMS_EMAIL_EXPORT'].'" onclick="javascript:document.location.href=\'admin.php?op=exportsearchpers_mail\';"/>
									<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_EMAIL_SEND'].'" onclick="javascript:document.dlocation.href=\'mailto:'.$firstmail.'';
*/

		echo '
								</div></td>
							</tr>
						</table>
					';
}
else {
	echo '
						<table width="100%">
							<tr>
								<td align="center">
								'.$skin->open_widgetbloc($_DIMS['cste']['_DIMS_SEARCH_RESULT'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '').'
									<table width="33%">
										<tr class="trl1">
											<td>'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</td>
										</tr>
                                                                                <tr>
                                                                                    <td>';

        // on regarde maintenant ce qui ressemble :
        // autoSuggest




       echo '                                                                    </td>
                                                                                </tr>
									</table>
								'.$skin->close_widgetbloc().'
								</td>
							</tr>
						</table>';
}

?>
