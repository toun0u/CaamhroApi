<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$idlink = dims_load_securvalue('id_link',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_ent = dims_load_securvalue('id_ent',dims_const::_DIMS_CHAR_INPUT,true,true);
//from sert a savoir si on vient d'une fiche entreprise, sinon il est vide
$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true);

$sql = "SELECT * FROM dims_mod_business_tiers_contact WHERE id = ".$idlink;

$res = $db->query($sql);
$tab_ct = $db->fetchrow($res);
if(!empty($tab_ct['date_deb'])) {
	$date_deb_y = substr($tab_ct['date_deb'], 0, 4);
	$date_deb_m = substr($tab_ct['date_deb'], 4, 2);
	$date_deb_d = substr($tab_ct['date_deb'], 6, 2);
}
else {
	$date_deb_y = date('Y');
	$date_deb_m = date('m');
	$date_deb_d = date('d');
}
if(!empty($tab_ct['date_fin'])) {
	$date_fin_y = substr($tab_ct['date_fin'], 0, 4);
	$date_fin_m = substr($tab_ct['date_fin'], 4, 2);
	$date_fin_d = substr($tab_ct['date_fin'], 6, 2);
}
else {
	$date_fin_y = "aaaa";
	$date_fin_m = "mm";
	$date_fin_d = "jj";
}

$link_level ='
				<option ';
				if($tab_ct['link_level'] == "1") $link_level .= 'selected="selected"';
				$link_level .= ' value="1">'.$_DIMS['cste']['_DIMS_LABEL_PUBLIC'].'</option>
						<option ';
				if($tab_ct['link_level'] == "2") $link_level .= 'selected="selected"';
				$link_level .= ' value="2">'.$_DIMS['cste']['_WORKSPACE'].'</option>';
				if( $_SESSION['dims']['user']['id_contact'] == $tab_ct['id_contact1']  || $_SESSION['dims']['user']['id_contact'] == $tab_ct['id_contact2']) {
					$link_level .='<option ';
					if($tab_ct['link_level'] == "3") $link_level .= 'selected="selected"';
					$link_level .= ' value="3">'.$_DIMS['cste']['_PRIVATE'].'</option>';
				}
echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_LFB_MOD_LINK'],'font-weight:bold;width:100%','','');
echo '<form method="POST" action="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACTSSEEK.'&op=savelinkentct" style="background-color:#ffffff;">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("id_link",	$idlink);
$token->field("id_ent",		$id_ent);
$token->field("from");
$token->field("type_link");
$token->field("link_level");
$token->field("fonction");
$token->field("departement");
$token->field("date_deb_day");
$token->field("date_deb_month");
$token->field("date_deb_year");
$token->field("date_fin_day");
$token->field("date_fin_month");
$token->field("date_fin_year");
$token->field("commentaire");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '<input type="hidden" name="id_link" value="'.$idlink.'"/>';
echo '<input type="hidden" name="id_ent" value="'.$id_ent.'"/>';
if(!empty($from)) echo '<input type="hidden" name="from" value="'.$from.'"/>';
echo'	<table width="100%">
			<tr>
				<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'].'&nbsp;</td>
				<td width="20%" align="left">
					<select id="type_link" name="type_link" style="width:205px;">
						<option ';
						if($tab_ct['type_lien'] == $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']) echo 'selected="selected"';
						echo ' value="'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'">'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'</option>
						<option ';
						if($tab_ct['type_lien'] == $_DIMS['cste']['_DIMS_LABEL_ASSOCIE']) echo 'selected="selected"';
						echo '  value="'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'">'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'</option>
						<option ';
						if($tab_ct['type_lien'] == stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN'])) echo 'selected="selected"';
						echo '  value="'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'">'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'</option>
						<option ';
						if($tab_ct['type_lien'] == $_DIMS['cste']['_DIMS_LABEL_OTHER']) echo 'selected="selected"';
						echo '  value="'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</option>

				</td>
				<td></td>
			</tr>
			<tr>
				<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_LEVEL_LINK'].'&nbsp;</td>
				<td width="20%" align="left">
					<select id="link_level" name="link_level" style="width:205px;">
						'.$link_level.'
					</select>
				</td>
				<td></td>
			</tr>
			<tr>
				<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'&nbsp;</td>
				<td width="20%" align="left">
					<input type="text" id="fonction" name="fonction" style="width:200px;" value="'.$tab_ct['function'].'"/>
				</td>
				<td></td>
			</tr>
			<tr>
				<td width="30%" align="right">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])).'&nbsp;</td>
				<td width="20%" align="left">
					<input type="text" id="departement" name="departement" style="width:200px;" value="'.$tab_ct['departement'].'"/>
				</td>
				<td></td>
			</tr>
			<tr>
				<td width="30%" align="right">'.$_DIMS['cste']['_BEGIN'].'&nbsp;</td>
				<td width="20%" align="left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input id="date_deb_day" name="date_deb_day" maxlenght="2" value="'.$date_deb_d.'" style="width:30px;"/>&nbsp;/&nbsp;
							</td>
							<td>
								<input id="date_deb_month" name="date_deb_month" maxlenght="2" value="'.$date_deb_m.'" style="width:30px;"/>&nbsp;/&nbsp;
							</td>
							<td>
								<input id="date_deb_year" name="date_deb_year" maxlenght="4" value="'.$date_deb_y.'" style="width:30px;"/>
							</td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td width="30%" align="right">'.$_DIMS['cste']['_END'].'&nbsp</td>
				<td width="20%" align="left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input id="date_fin_day" name="date_fin_day" maxlenght="2" value="'.$date_fin_d.'" style="width:30px;"/>&nbsp;/&nbsp;
							</td>
							<td>
								<input id="date_fin_month" name="date_fin_month" maxlenght="2" value="'.$date_fin_m.'" style="width:30px;"/>&nbsp;/&nbsp;
							</td>
							<td>
								<input id="date_fin_year" name="date_fin_year" maxlenght="4" value="'.$date_fin_y.'" style="width:30px;"/>
							</td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			<tr>
				<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_COMMENTS'].'&nbsp;</td>
				<td width="20%" align="left">
					<textarea id="commentaire" name="commentaire" style="width:200px;">'.$tab_ct['commentaire'].'</textarea>
				</td>
				<td></td>
			</tr>
			<tr>
				<td colspan="3" align="center" style="padding-top:15px;">
					<input type="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'].'"/>
				</td>
			</tr>
		</table>
	</form>
	';
echo $skin->close_widgetbloc();
?>
