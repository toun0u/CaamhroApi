<?php


//affichage du formulaire de recherche
//$include = './common/modules/system/crm_public_contact_search.php';

//affichage des resultats

if(isset($res_s) && $db->numrows($res_s) > 0) {
	echo '
						<table width="100%">
							<tr>
								<td align="center">
								'.$skin->open_widgetbloc($_DIMS['cste']['_DIMS_SEARCH_RESULT'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '').'
									<div style="width:100%;height:300px;overflow:auto;margin-top:10px;">';
$nb_res = $db->numrows($res_s);
if($nb_res > 100) {
    echo '<table width="100%" cellpadding="0" cellspacing="0" style="color:#ff0000;">
                <tr>
                    <td>'.$_DIMS['cste']['_DIMS_LABEL_TOO_MUCH_RES_SEARCH'].'
                    </td>
                </tr>
           </table>';
}
	echo							'   <table width="100%" cellpadding="0" cellspacing="0" style="border:#536485 1px solid">
										<tr class="trl1" style="font-size:13px;">
											<td>'.$_DIMS['cste']['_DIMS_LABEL_ENT_NAME'].'</td>
											<td>'.$_DIMS['cste']['_DIMS_DATE_MODIFY'].'</td>';

	if($where_ct != '' || $where_lkct != '') {
		echo '<td>'.$_DIMS['cste']['_TYPE'].'</td>
				<td>'.$_DIMS['cste']['_DIMS_LABEL_PERSONNE'].'</td>';
	}

	echo '<td></td>
		</tr>';

		$class_col = 'trl1';
        $i = 1;
		while($tab_res = $db->fetchrow($res_s)) {
            if($i <= 100) {
                if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
                $date_mod = dims_timestamp2local($tab_res['timestp_modify']);
                echo '<tr class="'.$class_col.'">
                        <td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ENT_FORM.'&part='._BUSINESS_TAB_ENT_IDENTITE.'&id_ent='.$tab_res['id_ent'].'">'.$tab_res['intitule'].'</a></td>
                        <td>'.$date_mod['date'].'</td>';

                if($where_ct != '' || $where_lkct != '') {
                    $name = $tab_res['firstname']." ".strtoupper($tab_res['lastname']);
                    echo '<td>'.$tab_res['type_lien'].'</td><td><a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$tab_res['id_ct'].'">'.$name.'</a></td>';
                }

                echo '<td style="padding-top:5px;">';
                if($tab_res['inactif'] == 1) echo '<img src="./common/img/important_small.png" title="'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'"/>';

                echo '</td></tr>';
            }
            $i++;
		}

		echo '</table></div>'.$skin->close_widgetbloc().'</td></tr>
				<tr><td align="center">';

		echo dims_create_button($_DIMS['cste']['_FORMS_ADR_EXPORT'],'./common/img/export.png','javascript:document.location.href=\'admin.php?op=exportsearchent_adr\';','','');
		echo dims_create_button($_DIMS['cste']['_FORMS_DATA_EXPORT'],'./common/img/export.png','javascript:location.href=\'admin.php?op=exportsearchent\';','','');

		echo '</td></tr></table>'; //on ajoute un </table> ici car au moment de l'affichage des resultats la balise n'est plus presente dans lfb_public_contact_search.php //exportSearchPers()
}
else {
	echo '<table width="100%"><tr><td align="center">'.$skin->open_widgetbloc($_DIMS['cste']['_DIMS_SEARCH_RESULT'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '').'
		<table width="33%"><tr class="trl1"><td>'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr></table>'.$skin->close_widgetbloc().'</td></tr></table>';
}
?>
