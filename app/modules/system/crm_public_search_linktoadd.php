<?
$cont_search = dims_load_securvalue('search_name', dims_const::_DIMS_CHAR_INPUT, true, true);
$type = dims_load_securvalue('type_search', dims_const::_DIMS_CHAR_INPUT, true, true);

$criteria = '';
switch($type) {
    case "pers" :
        $sql_pers = "SELECT id, lastname, firstname FROM dims_mod_business_contact WHERE inactif != 1 AND (lastname LIKE '%".$cont_search."%' OR firstname LIKE '%".$cont_search."%') ORDER BY lastname, firstname";
        //echo $sql_pers;
		$cont = $db->query($sql_pers);
        $nb_rep = $db->numrows($cont);

        $link_level ='	<option value="1">'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
                        <option value="2" selected>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>
                        <option value="3">'.$_DIMS['cste']['_DIMS_LABEL_PERSO'].'</option>';
		$type_de_lien = '	<option value="'.$_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS'].'">'.$_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_RESEAU'].'">'.$_DIMS['cste']['_DIMS_LABEL_RESEAU'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_FAMILLE_AMI'].'">'.$_DIMS['cste']['_DIMS_LABEL_FAMILLE_AMI'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</option>';
		$other_options ='';

		$from_id_cte = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true);
		$criteria = '&id_from='.$from_id_cte.'&type_from=cte&type_to=cte&case=1';
        break;
    case "tiers" :
        $sql_tiers = "SELECT id, intitule FROM dims_mod_business_tiers WHERE intitule LIKE '%".$cont_search."%' AND inactif != 1 ORDER BY intitule";
        //echo $sql_tiers;
		$cont = $db->query($sql_tiers);
        $nb_rep = $db->numrows($cont);

        $link_level = '	<option value="1">'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
                        <option value="2" selected>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>';
		$type_de_lien = '	<option value="'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'">'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'">'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'</option>
							<option value="'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'">'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</option>';
		$other_options = '	<tr>
								<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'&nbsp;</td>
								<td width="20%" align="left">
									<input type="text" id="fonction" name="fonction" style="width:200px;" value=""/>
								</td>
								<td></td>
							</tr>
							<tr>
								<td width="30%" align="right">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])).'&nbsp;</td>
								<td width="20%" align="left">
									<input type="text" class="departement"  id="departement" name="departement" style="width:200px;" value=""/>
								</td>
								<td></td>
							</tr>';

		$from_id_cte = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true);
		$criteria = '&id_from='.$from_id_cte.'&type_from=cte&type_to=ent&case=2';
        break;
	case "ent_pers" :
		$sql_pers = "SELECT id, lastname, firstname FROM dims_mod_business_contact WHERE inactif != 1 AND (lastname LIKE '%".$cont_search."%' OR firstname LIKE '%".$cont_search."%') ORDER BY lastname, firstname";
        //echo $sql_pers;
		$cont = $db->query($sql_pers);
        $nb_rep = $db->numrows($cont);

        $link_level ='	<option value="1">'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
                        <option value="2" selected>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>';
		$type_de_lien = '	<option value="'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'">'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'">'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'</option>
							<option value="'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'">'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</option>';
		$other_options = '	<tr>
								<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'&nbsp;</td>
								<td width="20%" align="left">
									<input type="text" id="fonction" name="fonction" style="width:200px;" value=""/>
								</td>
								<td></td>
							</tr>
							<tr>
								<td width="30%" align="right">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])).'&nbsp;</td>
								<td width="20%" align="left">
									<input type="text" id="departement" name="departement" style="width:200px;" value=""/>
								</td>
								<td></td>
							</tr>';

		$from_id_ent = dims_load_securvalue('id_ent', dims_const::_DIMS_NUM_INPUT, true);
		$criteria = '&id_from='.$from_id_ent.'&type_from=ent&type_to=cte&case=1';
		break;
	case "ent_ent" :
		$sql_tiers = "SELECT id, intitule FROM dims_mod_business_tiers WHERE intitule LIKE '%".$cont_search."%' AND inactif != 1 ORDER BY intitule";
        //echo $sql_tiers;
		$cont = $db->query($sql_tiers);
        $nb_rep = $db->numrows($cont);

        $link_level = '	<option value="1">'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
                        <option value="2" selected>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>';

		$type_de_lien = '	<option value="'.$_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS'].'">'.$_DIMS['cste']['_DIMS_MOD_LABEL_BUSINESS'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_RESEAU'].'">'.$_DIMS['cste']['_DIMS_LABEL_RESEAU'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_FAMILLE_AMI'].'">'.$_DIMS['cste']['_DIMS_LABEL_FAMILLE_AMI'].'</option>
							<option value="'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</option>';
		$other_options ='';

		$from_id_ent = dims_load_securvalue('id_ent', dims_const::_DIMS_NUM_INPUT, true);

		$criteria = '&id_from='.$from_id_ent.'&type_from=ent&type_to=ent&case=2';
		break;
}

$retour = '<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>';

if($nb_rep>0) {
    $retour .= '<table width="100%">
                    <tr>
                        <td colspan="3" align="center">
                            <select id="'.$type.'_id" name="'.$type.'_id" size="10" style="width:236px;">';
    while($list_cont = $db->fetchrow($cont)) {
        if($type=="tiers" || $type=="ent_ent") {
            $option = '<option value="'.$list_cont['id'].'">'.$list_cont['intitule'].'</option>';
        }
        else {
            $option = '<option value="'.$list_cont['id'].'">'.$list_cont['lastname'].' '.$list_cont['firstname'].'</option>';
        }
        $retour .= $option;
    }
    $retour .= '			</select>
                        </td>
						<td width="50%">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'].'&nbsp;</td>
									<td width="20%" align="left">
										<select id="'.$type.'_type_link" name="'.$type.'_type_link" style="width:205px;">
											'.$type_de_lien.'
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_LEVEL_LINK'].'&nbsp;</td>
									<td width="20%" align="left">
										<select id="'.$type.'_link_level" name="'.$type.'_link_level" style="width:205px;">
											'.$link_level.'
										</select>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_BEGIN'].'&nbsp;</td>
									<td width="20%" align="left">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input id="date_deb_day" name="date_deb_day" maxlenght="2" value="'.date("d").'" style="width:30px;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_deb_month" name="date_deb_month" maxlenght="2" value="'.date("m").'" style="width:30px;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_deb_year" name="date_deb_year" maxlenght="4" value="'.date("Y").'" style="width:30px;"/>
												</td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_END'].'&nbsp;</td>
									<td width="20%" align="left">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input id="date_fin_day" name="date_fin_day" maxlenght="2" value="jj" style="width:30px;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_fin_month" name="date_fin_month" maxlenght="2" value="mm" style="width:30px;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_fin_year" name="date_fin_year" maxlenght="4" value="aaaa" style="width:30px;"/>
												</td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>'.$other_options.'
								<tr>
									<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_COMMENTS'].'&nbsp;</td>
									<td width="20%" align="left">
										<textarea id="commentaire" name="commentaire" style="width:200px;"></textarea>
									</td>
									<td></td>
								</tr>
							</table>
						</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" style="padding-top:15px;">';
					if ($type=="ent_pers" || $type=="pers") {
						$retour .= dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_inscript_link_pers.submit();","","");
					}
					else {
						$retour .=  dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_inscript_link.submit();","","");
					}
                    echo '</td>
                    </tr>
                </table>';
}
else {
    $retour .= '<p style="font-size:14px;">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</p>
                <p align="center"><input type="button" value="'.$_DIMS['cste']['_ADD_CT'].'" onclick="javascript:document.location.href=\'admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACTSTIERS.$criteria.'\';"/>';
}
$retour .= '</td></tr></table>';

echo $retour;

?>
