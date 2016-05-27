<?php
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
require_once(DIMS_APP_PATH.'modules/system/class_tiers_contact.php');

$cat = dims_load_securvalue('cat',dims_const::_DIMS_NUM_INPUT,true,true);

$tabscriptenv = "admin.php?cat=".$cat; //dims_mainmenu=".$dims_mainmenu."&

$part = dims_load_securvalue('part',dims_const::_DIMS_NUM_INPUT,true,true);
if(empty($part)) $part= _BUSINESS_TAB_CONTACT_IDENTITE;

$contact_id = dims_load_securvalue('contact_id',dims_const::_DIMS_NUM_INPUT,true,true);
$subaction = dims_load_securvalue('subaction',dims_const::_DIMS_NUM_INPUT,true,true);
if($contact_id == "") $contact_id = $_SESSION['business']['contact_id'];

$contact= new contact();
if ($contact_id>0) {
	$contact->open($contact_id);
	$_SESSION['business']['contact_id']=$contact_id;
}

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="width:25%;vertical-align:top;">
			<?php
				require_once(DIMS_APP_PATH.'modules/system/crm_public_contact_bloc_profil.php');
				require_once(DIMS_APP_PATH.'modules/system/crm_public_contact_bloc_news.php');
				require_once(DIMS_APP_PATH.'modules/system/crm_public_contact_bloc_docs.php');
				require_once(DIMS_APP_PATH.'modules/system/crm_public_contact_bloc_mail.php');
			?>
		</td>
		<td align="center" style="vertical-align:top;padding-left:5px;">
<?php

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_EMAIL'], 'width:100%');

$tab_onglet = array();

$tab_onglet[dims_const::_DIMS_MENU_MAIL_RECEIVED]['title'] = $_DIMS['cste']['_DIMS_TOVIEW'];
$tab_onglet[dims_const::_DIMS_MENU_MAIL_RECEIVED]['url'] = $tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&subaction=".dims_const::_DIMS_MENU_MAIL_RECEIVED ;
$tab_onglet[dims_const::_DIMS_MENU_MAIL_RECEIVED]['width'] = '70';
$tab_onglet[dims_const::_DIMS_MENU_MAIL_RECEIVED]['position'] = 'left';

$tab_onglet[dims_const::_DIMS_MENU_MAIL_SENT]['title'] = $_DIMS['cste']['_DIMS_MSG_SENT'];
$tab_onglet[dims_const::_DIMS_MENU_MAIL_SENT]['url'] = $tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&subaction=".dims_const::_DIMS_MENU_MAIL_SENT ;
$tab_onglet[dims_const::_DIMS_MENU_MAIL_SENT]['width'] = '70';
$tab_onglet[dims_const::_DIMS_MENU_MAIL_SENT]['position'] = 'left';

if ($subaction == dims_const::_DIMS_MENU_IMPORT_VCF){
	$tab_onglet[dims_const::_DIMS_MENU_IMPORT_VCF]['title'] = $_DIMS['cste']['_LABEL_IMPORT'];
	$tab_onglet[dims_const::_DIMS_MENU_IMPORT_VCF]['url'] = $tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF ;
	$tab_onglet[dims_const::_DIMS_MENU_IMPORT_VCF]['width'] = '70';
	$tab_onglet[dims_const::_DIMS_MENU_IMPORT_VCF]['position'] = 'left';
}

echo $skin->create_onglet($tab_onglet,$subaction,true,'0',"onglet");

switch ($subaction) {
	default :
	case dims_const::_DIMS_MENU_MAIL_RECEIVED :

	$id_delete = dims_load_securvalue('delete',dims_const::_DIMS_NUM_INPUT,true,true);
	if ($id_delete != ""){
		$sql = "SELECT	id
			FROM	dims_mod_webmail_email_adresse
			WHERE	id_mail = :idmail
			AND	type = 1";
		$res = $db->query($sql, array(
			':idmail'	=> $id_delete
		));
		$id_adr = $db->fetchrow($res);

		$sql = "DELETE FROM	dims_mod_webmail_email_adresse
			WHERE		id = :id
			AND		type = 1";
		$db->query($sql, array(
			':id'	=> $id_adr['id']
		));

		$sql = "DELETE FROM	dims_mod_webmail_email_link
			WHERE		id_mail = :idmail ";
		$db->query($sql, array(
			':idmail'	=> $id_adr['id']
		));

		dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&subaction=".dims_const::_DIMS_MENU_MAIL_RECEIVED);

	}

	$sql = '' ;
	if ($_SESSION['dims']['user']['id_contact'] == $contact_id){
		$sql = "SELECT	e.*
			FROM	dims_mod_webmail_email_adresse a
			INNER JOIN	dims_mod_webmail_email_link lk
			ON	lk.id_mail = a.id
			AND		lk.id_contact = :contactid
			INNER JOIN	dims_mod_webmail_email e
			ON	e.id = a.id_mail
			WHERE	(a.type = 2 OR a.type = 3)
			ORDER BY	e.date DESC";
	}else{
		$sql = "SELECT	e.*
			FROM	dims_mod_webmail_email_adresse a
			INNER JOIN	dims_mod_webmail_email_link lk
			ON	lk.id_mail = a.id
			AND		lk.id_contact = :contactid
			INNER JOIN	dims_mod_webmail_email e
			ON	e.id = a.id_mail
			WHERE	a.type = 1
			ORDER BY	e.date DESC";
	}

	$res = $db->query($sql, array(
		':contactid' => $contact_id
	));

	echo '<table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr class="trl1" align="left"><th width="5%"></th>
		<th width="15%">'.$_DIMS['cste']['_SENDER'].'</th>
		<th width="50%">'.$_DIMS['cste']['_SUBJECT'].'</th>
		<th width="15%">'.$_DIMS['cste']['_DIMS_DATE'].'</th>
		<th width="15%"></th></tr>';

	$tab_infmail = array();
	if($db->numrows($res) > 0) {
		$class = 'trl2';
		while($result = $db->fetchrow($res)) {
			$tab_infmail[$result['id']] = $result;
			// recherche de l'expéditeur
			$sql = 'SELECT	a.mail, COUNT(d.id) as nb_pj
				FROM	dims_mod_webmail_email_adresse a
				LEFT JOIN	dims_mod_webmail_email_docfile d
					ON	d.id_email = :idemail
				WHERE	a.id_mail = :idemail
				AND	a.type = 1';
			$from = $db->query($sql, array(
				':idemail' => $result['id']
			));
			$tabFrom = $db->fetchrow($from);

			$tab_infmail[$result['id']]['from'] = $tabFrom['mail'];

			//recherche des documents joints
			$sql_pj = 'SELECT	id_docfile
					FROM	dims_mod_webmail_email_docfile
					WHERE	id_email = :idemail ';
			$pj = $db->query($sql_pj, array(
				':idemail' => $result['id']
			));
			while($tabpj = $db->fetchrow($pj)) {
				$tab_infmail[$result['id']]['pj'][]= $tabpj['id_docfile'];
			}

			$onclick = "javascript: location.href='".$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&mail_id=".$result['id']."&subaction=".dims_const::_DIMS_MENU_MAIL_RECEIVED."'";
			echo '<tr class="'.$class.'" onclick="'.$onclick.'">';
			echo '<td>';
			if ($tabFrom['nb_pj']>0){
				if($tabFrom['nb_pj'] > 1) echo $tabFrom['nb_pj'];
				echo ' <img src="./common/img/attachment.png" />';
			}
			echo '</td>';
			echo '<td>';
			echo ($tabFrom['mail']);
			echo '</td>';
			echo '<td>';
			if(strlen($result['subject']) > 25)
				echo substr($result['subject'],0,25).'[...]';
			else
				echo $result['subject'];
			echo '</td>';
			echo '<td>';
			$dateLocal = dims_timestamp2local($result['date']);
			echo $dateLocal['date'];
			echo '</td>';
			// actions
			echo '<td>';
			echo '<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&subaction=".dims_const::_DIMS_MENU_MAIL_RECEIVED."&delete=".$result['id'].'" style="text-decoration:none;"><img src="./common/img/icon_delete.gif" alt="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'" title="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'"/></a>';
			echo '</td>';
			echo '</tr>';
			$class = ($class == 'trl1') ? 'trl2' : 'trl1';
		}
	}
	else {
		echo '<tr><td>';
		echo $_DIMS['cste']['_DIMS_LABEL_MAIL_NONE'];
		echo '</td></tr>';
	}

	echo '</table>';

	break;

	case dims_const::_DIMS_MENU_MAIL_SENT :

	$id_delete = dims_load_securvalue('delete',dims_const::_DIMS_NUM_INPUT,true,true);
	if ($id_delete != ""){
		$sql = "SELECT		a.id
			FROM		dims_mod_webmail_email_adresse a
			INNER JOIN	dims_mod_webmail_email_link lk
				ON	lk.id_contact = :idcontact
				AND	lk.id_mail = a.id
			WHERE		a.id_mail= :idmail
			AND		(a.type = 2 OR a.type = 3)";
		$res = $db->query($sql, array(
			':idcontact'	=> $contact_id,
			':idmail'		=> $id_delete
		));
		$id_adr = $db->fetchrow($res);

		$sql = "DELETE FROM	dims_mod_webmail_email_adresse
			WHERE		id = :idadr
			AND		type = 1";
		$db->query($sql, array(
			':idadr' => $id_adr['id']
		));

		$sql = "DELETE FROM	dims_mod_webmail_email_link
			WHERE		id_mail = :idadr ";
		$db->query($sql, array(
			':idadr' => $id_adr['id']
		));

		dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&subaction=".dims_const::_DIMS_MENU_MAIL_SENT);
	}

	$sql = '' ;
	if ($_SESSION['dims']['user']['id_contact'] == $contact_id){
		$sql = "SELECT	e.*
			FROM	dims_mod_webmail_email_adresse a
			INNER JOIN	dims_mod_webmail_email_link lk
			ON	lk.id_mail = a.id
			AND		lk.id_contact = :idcontact
			INNER JOIN	dims_mod_webmail_email e
			ON	e.id = a.id_mail
			WHERE	a.type = 1
			ORDER BY	e.date DESC";
	}else{
		$sql = "SELECT	e.*
			FROM	dims_mod_webmail_email_adresse a
			INNER JOIN	dims_mod_webmail_email_link lk
			ON	lk.id_mail = a.id
			AND		lk.id_contact = :idcontact
			INNER JOIN	dims_mod_webmail_email e
			ON	e.id = a.id_mail
			WHERE	(a.type = 2 OR a.type = 3)
			ORDER BY	e.date DESC";
	}

	$res = $db->query($sql, array(
		':idcontact' => $contact_id
	));

	echo '<table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr class="trl1" align="left"><th width="5%"></th>
		<th width="15%">'.$_DIMS['cste']['_DIMS_LABEL_MAIL_TO'].'</th>
		<th width="50%">'.$_DIMS['cste']['_SUBJECT'].'</th>
		<th width="15%">'.$_DIMS['cste']['_DIMS_DATE'].'</th>
		<th width="15%"></th></tr>';

	$tab_infmail = array();
	if($db->numrows($res) > 0) {
		$class = 'trl2';
		while($result = $db->fetchrow($res)) {
			$tab_infmail[$result['id']] = $result;
			// recherche du/des destinataire(s) : To
			$sql = 'SELECT	a.mail
				FROM	dims_mod_webmail_email_adresse a
				WHERE	a.id_mail = :idmail
				AND	a.type = 2';
			$res_To = $db->query($sql, array(
				':idmail' => $result['id']
			));

			$to = '<table width="100%" cellpadding="0" cellspacing="0" border="0">' ;

			while($dest = $db->fetchrow($res_To)) {
				$to .= "<tr><td>".$dest['mail']."</td></tr>";
			}
			$to .= "</table>";

			$tab_infmail[$result['id']]['to'] = $to;

			//recherche des documents joints
			$sql_pj = 'SELECT	id_docfile
					FROM	dims_mod_webmail_email_docfile
					WHERE	id_email = :idmail ';
			$pj = $db->query($sql_pj, array(
				':idmail' => $result['id']
			));
			while($tabpj = $db->fetchrow($pj)) {
				$tab_infmail[$result['id']]['pj'][]= $tabpj['id_docfile'];
			}

			$onclick = "javascript: location.href='".$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&mail_id=".$result['id']."&subaction=".dims_const::_DIMS_MENU_MAIL_SENT."'";
			echo '<tr class="'.$class.'" onclick="'.$onclick.'">';
			echo '<td>';
			$nb_pj = $db->numrows($pj) ;
			if ($nb_pj > 0){
				if($nb_pj > 1) echo $nb_pj;
				echo ' <img src="./common/img/attachment.png" />';
			}
			echo '</td>';
			echo '<td>';
			echo ($to);
			echo '</td>';
			echo '<td>';
			if(strlen($result['subject']) > 25)
				echo substr($result['subject'],0,25).'[...]';
			else
				echo $result['subject'];
			echo '</td>';
			echo '<td>';
			$dateLocal = dims_timestamp2local($result['date']);
			echo $dateLocal['date'];
			echo '</td>';
			echo '<td>';
			echo '<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&subaction=".dims_const::_DIMS_MENU_MAIL_SENT."&delete=".$result['id'].'" style="text-decoration:none;"><img src="./common/img/icon_delete.gif" alt="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'" title="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'"/></a>';
			echo '</td>';
			echo '</tr>';
			$class = ($class == 'trl1') ? 'trl2' : 'trl1';
		}
	}
	else {
		echo '<tr><td>';
		echo $_DIMS['cste']['_DIMS_LABEL_MAIL_NONE'];
		echo '</td></tr>';
	}

	echo '</table>';

	break ;
	case dims_const::_DIMS_MENU_IMPORT_VCF:
	$do = dims_load_securvalue('do', dims_const::_DIMS_CHAR_INPUT, true, true);
	switch($do) {
		case 'maj_tiers':
			$vcard = dims_load_securvalue('vcard',dims_const::_DIMS_NUM_INPUT,true,true);
			$id_doc = dims_load_securvalue('doc_id', dims_const::_DIMS_NUM_INPUT, true, true);

			$vc = new docfile();
			$vc->open($id_doc);
			$vcards = $vc->getParseVcf();

			$tiers = new tiers();
			$tiers->open(dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true, true));

			if (isset($_POST['web_page']) && $_POST['web_page'] == "unchecked"){
				if (isset($vcards[$vcard]['url'])){
					if (count($vcards[$vcard]['url']) == 1){
						$tiers->fields['site_web'] = $vcards[$vcard]['url']['0'];
					}else {
						foreach ($vcards[$vcard]['url'] as $url){
							$tiers->fields['site_web'] .= $url."; ";
						}
					}
				}
			}

			if (isset($_POST['adresse']) && $_POST['adresse'] == "unchecked"){
				if (isset($vcards[$vcard]['adr']['work']['rue'])){
					$tiers->fields['adresse'] = $vcards[$vcard]['adr']['work']['rue'];
				}
				if (isset($vcards[$vcard]['adr']['work']['city'])){
					$tiers->fields['ville'] = $vcards[$vcard]['adr']['work']['city'];
				}
				if (isset($vcards[$vcard]['adr']['work']['cp'])){
					$tiers->fields['codepostal'] = $vcards[$vcard]['adr']['work']['cp'];
				}
				if (isset($vcards[$vcard]['adr']['work']['rue'])){
					$tiers->fields['pays'] = $vcards[$vcard]['adr']['work']['pays'];
				}

			}

			$tiers->save();

			$id_profil = dims_load_securvalue('id_profil', dims_const::_DIMS_NUM_INPUT, true, true);
			$idtiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true, true);
			if ($id_profil>0){
				//creation d'un lien eventuel avec l'entreprise si le contact n'est pas déjà lié
				$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtiers AND id_contact = :idcontact ";
				$res = $db->query($sql, array(
					':idtiers'		=> $idtiers,
					':idcontact'	=> $id_profil
				));
				if($db->numrows($res) == 0) {
					//on cree un lien
					$tiers_ct = new tiersct();
					$tiers_ct->init_description();
					$tiers_ct->fields['id_tiers'] = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true, true);
					$tiers_ct->fields['id_contact'] = $id_profil;
					$tiers_ct->fields['type_lien'] = $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'];
					$tiers_ct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$tiers_ct->fields['date_create'] = date('YmdHis');
					$tiers_ct->fields['link_level'] = 2;
					$tiers_ct->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];

					$tiers_ct->save();
				}

			}

			$vcard++;

			$sql = "UPDATE	dims_mod_vcard
				SET	id_contact = :idcontact , date_modify = '".date("Ymdhis")."'
				WHERE	id_docfile = :iddocfile
				AND	num = ".$vcard;
			$db->query($sql, array(
				':idcontact'	=> $id_profil,
				':iddocfile'	=> $id_doc
			));

			if(count($vcards) > $vcard) {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&doc_id=".$id_doc."&vcard=".$vcard."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF);
			}
			else {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=msg_ok_import");
			}
			break;
		case 'maj_contact':
			$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true); // profil maj
			$tiers_id = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true);
			$vcard = dims_load_securvalue('vcard',dims_const::_DIMS_NUM_INPUT,true,true);
			$id_doc = dims_load_securvalue('doc_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$contact_id = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true); // profil courant

			$vc = new docfile();
			$vc->open($id_doc);
			$vcards = $vc->getParseVcf();

			$ct = new contact();
			$ct->open($contact_id);

			$ct_layer = new contact_layer();
			$ct_layer->open($contact_id, 1, $_SESSION['dims']['workspaceid']);

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

			$convmeta = array();
			$mode=array();
			while ($fields = $db->fetchrow($rs_fields)) {
				// on ajoute maintenant les champs dans la liste
				$fields['use']=0;// par defaut non utilise
				$fields['enabled']=array();

				$mode[$fields['id']]=$fields['mode'];

				// enregistrement de la conversion
				$convmeta[$fields['namefield']]=$fields['id'];
			}

			if($_POST['email1'] != 'unchanged') {
				if($mode[$convmeta['email']] == 0) {
					$ct->fields['email'] = dims_load_securvalue('email1', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				}
				else {
					$ct_layer->fields['email'] = dims_load_securvalue('email1', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				}
			}

			if($_POST['email2'] != 'unchanged') {
				if($mode[$convmeta['email2']] == 0) {
					$ct->fields['email2'] = dims_load_securvalue('email2', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				}
				else {
					$ct_layer->fields['email2'] = dims_load_securvalue('email2', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				}
			}

			if($_POST['email3'] != 'unchanged') {
				if($mode[$convmeta['email3']] == 0) {
					$ct->fields['email3'] = dims_load_securvalue('email3', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				}
				else {
					$ct_layer->fields['email3'] = dims_load_securvalue('email3', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				}
			}

			if($_POST['tel1'] != 'unchanged') {
				if($mode[$convmeta['phone']] == 0) {
					$ct->fields['phone'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel1', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
				else {
					$ct_layer->fields['phone'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel1', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
			}

			if($_POST['tel2'] != 'unchanged') {
				if($mode[$convmeta['pers_phone']] == 0) {
					$ct->fields['pers_phone'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel2', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
				else {
					$ct_layer->fields['pers_phone'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel2', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
			}

			if($_POST['tel3'] != 'unchanged') {
				if($mode[$convmeta['mobile']] == 0) {
					$ct->fields['mobile'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel3', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
				else {
					$ct_layer->fields['mobile'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel3', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
			}

			if($_POST['tel4'] != 'unchanged') {
				if($mode[$convmeta['phone2']] == 0) {
					$ct->fields['phone2'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel4', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
				else {
					$ct_layer->fields['phone2'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel4', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
				}
			}

			if(isset($_POST['adr_home']) && $_POST['adr_home'] == "unchecked") {
				if($vcards[$vcard]['adr']['home']['rue'] != '') {
					if($mode[$convmeta['address']] == 0) {
						$ct->fields['address'] = $vcards[$vcard]['adr']['home']['rue'];
					}
					else {
						$ct_layer->fields['address'] = $vcards[$vcard]['adr']['home']['rue'];
					}
				}

				if($vcards[$vcard]['adr']['home']['cp'] != '') {
					if($mode[$convmeta['postalcode']] == 0) {
						$ct->fields['postalcode'] = $vcards[$vcard]['adr']['home']['cp'];
					}
					else {
						$ct_layer->fields['postalcode'] = $vcards[$vcard]['adr']['home']['cp'];
					}
				}

				if($vcards[$vcard]['adr']['home']['city'] != '') {
					if($mode[$convmeta['city']] == 0) {
						$ct->fields['city'] = $vcards[$vcard]['adr']['home']['city'];
					}
					else {
						$ct_layer->fields['city'] = $vcards[$vcard]['adr']['home']['city'];
					}
				}

				if($vcards[$vcard]['adr']['home']['pays'] != '') {
					if($mode[$convmeta['country']] == 0) {
						$ct->fields['country'] = $vcards[$vcard]['adr']['home']['pays'];
					}
					else {
						$ct_layer->fields['country'] = $vcards[$vcard]['adr']['home']['pays'];
					}
				}
			}

			if(isset($_POST['image']) && $_POST['image'] == "unchecked" && isset($vcards[$vcard]['photo'])) {

				$time = time();

				$path = DIMS_WEB_PATH.'data/photo_cts/contact_'.$contact_id;
				//on verifie si le dossier existe deja, si oui, on le vide
				$files = scandir($path);
				if(!empty($files)){
					foreach($files as $file) {
						if($file != "." & $file != "..") {
							$p_todel = $path."/".$file;
							unlink($p_todel);
						}
					}
				}
				else {
					mkdir($path, 0777);
				}

				// ajouter ratio ?
				rename($vcards[$vcard]['photo'],$path."/photo60_".$time.".png");

				$ct->fields['photo'] = "_".$time;
			}

			$ct->save();
			$ct_layer->save();

			if (isset($_POST['societe']) && $_POST['societe'] == "unchecked"){
				if(((int)$tiers_id) > 0) {
					$tiers = new tiers();
					$tiers->open($tiers_id);

					$tiers_layer = new tiers_layer();
					$tiers_layer->open($tiers_id, 1, $_SESSION['dims']['workspaceid']);

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

					$convmeta = array();
					$mode=array();
					while ($fields = $db->fetchrow($rs_fields)) {
						// on ajoute maintenant les champs dans la liste
						$fields['use']=0;// par defaut non utilise
						$fields['enabled']=array();

						$mode[$fields['id']]=$fields['mode'];

						// enregistrement de la conversion
						$convmeta[$fields['namefield']]=$fields['id'];
					}

					if($_POST['tel1'] != 'unchanged') {
						if($mode[$convmeta['telephone']] == 0) {
							$tiers->fields['telephone'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel1', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
						}
						else {
							$tiers_layer->fields['telephone'] = $vcards[$vcard]['tel'][dims_load_securvalue('tel1', dims_const::_DIMS_CHAR_INPUT, true, true, true)];
						}
					}

					if(isset($_POST['adr_work']) && $_POST['adr_work'] == "unchecked") {
						if($vcards[$vcard]['adr']['work']['rue'] != '') {
							if($mode[$convmeta['adresse']] == 0) {
								$tiers->fields['adresse'] = $vcards[$vcard]['adr']['work']['rue'];
							}
							else {
								$tiers_layer->fields['adresse'] = $vcards[$vcard]['adr']['work']['rue'];
							}
						}

						if($vcards[$vcard]['adr']['work']['cp'] != '') {
							if($mode[$convmeta['codepostal']] == 0) {
								$tiers->fields['codepostal'] = $vcards[$vcard]['adr']['work']['cp'];
							}
							else {
								$tiers_layer->fields['codepostal'] = $vcards[$vcard]['adr']['work']['cp'];
							}
						}

						if($vcards[$vcard]['adr']['work']['city'] != '') {
							if($mode[$convmeta['ville']] == 0) {
								$tiers->fields['ville'] = $vcards[$vcard]['adr']['work']['city'];
							}
							else {
								$tiers_layer->fields['ville'] = $vcards[$vcard]['adr']['work']['city'];
							}
						}

						if($vcards[$vcard]['adr']['work']['pays'] != '') {
							if($mode[$convmeta['pays']] == 0) {
								$tiers->fields['pays'] = $vcards[$vcard]['adr']['work']['pays'];
							}
							else {
								$tiers_layer->fields['pays'] = $vcards[$vcard]['adr']['work']['pays'];
							}
						}
					}

					if(isset($_POST['adr_work']) && $_POST['adr_work'] == "unchecked") {
						$all_url = '';
						foreach($vcards[$vcard]['url'] as $url) {
							if($url != '') $all_url .= $url."; ";
						}
						if($mode[$convmeta['site_web']] == 0) {
							$tiers->fields['site_web'] = $all_url;
						}
						else {
							$tiers_layer->fields['site_web'] = $all_url;
						}
					}

					$tiers->save();
					$tiers_layer->save();

					//creation d'un lien eventuel avec l'entreprise si le contact n'est pas déjà lié
					$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtiers AND id_contact = :idcontact ";
					$res = $db->query($sql, array(
						':idtiers'		=> $tiers_id,
						':idcontact'	=> $id_contact
					));
					if($db->numrows($res) == 0) {
						//on cree un lien
						$tiers_ct = new tiersct();
						$tiers_ct->init_description();
						$tiers_ct->fields['id_tiers'] = $tiers_id;
						$tiers_ct->fields['id_contact'] = $contact_id;
						$tiers_ct->fields['type_lien'] = $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'];
						$tiers_ct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiers_ct->fields['date_create'] = date('YmdHis');
						$tiers_ct->fields['link_level'] = 2;
						$tiers_ct->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];

						$tiers_ct->save();
					}
				}
				elseif ($vcards[$vcard]['org'] != ''){
					//on a pas de correspondance exacte avec une entreprise, il faut faire la moulinette de recherche de similarités pour les entreprises
					//on aurait un redirect du type : dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&doc_id=".$id_doc."&vcard=".$vcard."&new_ct=".$id_new_ct."&type=tiers&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF);
					//Attention : type sert à rediriger vers le fichier crm_public_ent_compare_vcf.php inclus dans crm_public_contact_view
					dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&doc_id=".$id_doc."&vcard=".$vcard."&new_ct=".$id_new_ct."&id_profil=".$id_contact."&type=tiers&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF);
				}
			}

			//redirection
			$vcard++;

			$sql = "UPDATE	dims_mod_vcard
				SET	id_contact = :idcontact , date_modify = '".date("Ymdhis")."'
				WHERE	id_docfile = :iddocfile
				AND	num = :num ";
			$db->query($sql, array(
				':idcontact'	=> $id_contact,
				':iddocfile'	=> $id_doc,
				':num'			=> $vcard
			));

			if(count($vcards) > $vcard) {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&doc_id=".$id_doc."&vcard=".$vcard."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF);
			}
			else {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=msg_ok_import");
			}

			break;
		case 'create_ct_fromvcard':

			$id_mail = dims_load_securvalue('mail_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$id_doc = dims_load_securvalue('doc_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$contact_id = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$vcard = dims_load_securvalue('vcard',dims_const::_DIMS_NUM_INPUT,true,true);

			//creation de la fiche contact
			$new_ct = new contact();
			$new_ct->init_description();

			$ctworkspace = new contact_layer();
			$ctworkspace->init_description();

			$vc = new docfile();
			$vc->open($id_doc);
			$vcards = $vc->getParseVcf();

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
				':idobject'	=> dims_const::_SYSTEM_OBJECT_CONTACT
			));

			$convmeta = array();
			$mode=array();
			while ($fields = $db->fetchrow($rs_fields)) {
				// on ajoute maintenant les champs dans la liste
				$fields['use']=0;// par defaut non utilise
				$fields['enabled']=array();

				$mode[$fields['id']]=$fields['mode'];

				// enregistrement de la conversion
				$convmeta[$fields['namefield']]=$fields['id'];
			}
//dims_print_r($mode);
//dims_print_r($convmeta);
			$new_ct->fields['firstname'] = $vcards[$vcard]['prenom'];
			$new_ct->fields['lastname'] = $vcards[$vcard]['nom'];

			$layer = 0;
			if($vcards[$vcard]['civilite'] != '') {
				if($mode[$convmeta['civilite']] == 0) {
					$new_ct->fields['civilite'] = $vcards[$vcard]['title'];
				}
				else {
					$ctworkspace->fields['civilite'] = $vcards[$vcard]['title'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['home']['rue'] != '') {
				if($mode[$convmeta['address']] == 0) {
					$new_ct->fields['address'] = $vcards[$vcard]['adr']['home']['rue'];
				}
				else {
					$ctworkspace->fields['address'] = $vcards[$vcard]['adr']['home']['rue'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['home']['cp'] != '') {
				if($mode[$convmeta['postalcode']] == 0) {
					$new_ct->fields['postalcode'] = $vcards[$vcard]['adr']['home']['cp'];
				}
				else {
					$ctworkspace->fields['postalcode'] = $vcards[$vcard]['adr']['home']['cp'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['home']['city'] != '') {
				if($mode[$convmeta['city']] == 0) {
					$new_ct->fields['city'] = $vcards[$vcard]['adr']['home']['city'];
				}
				else {
					$ctworkspace->fields['city'] = $vcards[$vcard]['adr']['home']['city'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['home']['pays'] != '') {
				if($mode[$convmeta['country']] == 0) {
					$new_ct->fields['country'] = $vcards[$vcard]['adr']['home']['pays'];
				}
				else {
					$ctworkspace->fields['country'] = $vcards[$vcard]['adr']['home']['pays'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['tel']['work'] != '') {
				if($mode[$convmeta['phone']] == 0) {
					$new_ct->fields['phone'] = $vcards[$vcard]['tel']['work'];
				}
				else {
					$ctworkspace->fields['phone'] = $vcards[$vcard]['tel']['work'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['tel']['home'] != '') {
				if($mode[$convmeta['pers_phone']] == 0) {
					$new_ct->fields['pers_phone'] = $vcards[$vcard]['tel']['home'];
				}
				else {
					$ctworkspace->fields['pers_phone'] = $vcards[$vcard]['tel']['home'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['tel']['cell'] != '') {
				if($mode[$convmeta['mobile']] == 0) {
					$new_ct->fields['mobile'] = $vcards[$vcard]['tel']['cell'];
				}
				else {
					$ctworkspace->fields['mobile'] = $vcards[$vcard]['tel']['cell'];
					$layer = 1;
				}
			}
			$cpt = 1;
			while($cpt <= 4 && count($vcards[$vcard]['email']) >= $cpt) {
				if($vcards[$vcard]['email'][$cpt-1] != '') {
					if($cpt == 1) {
						if($mode[$convmeta['email']] == 0) {
							$new_ct->fields['email'] = $vcards[$vcard]['email'][$cpt-1];
						}
						else {
							$ctworkspace->fields['email'] = $vcards[$vcard]['email'][$cpt-1];
							$layer = 1;
						}
					}
					else {
						$lbl = 'email'.$cpt;
						if($mode[$convmeta[$lbl]] == 0) {
							$new_ct->fields[$lbl] = $vcards[$vcard]['email'][$cpt-1];
						}
						else {
							$ctworkspace->fields[$lbl] = $vcards[$vcard]['email'][$cpt-1];
							$layer = 1;
						}
					}
				}
				$cpt++;
			}

			//on enregistre
			$id_new_ct = $new_ct->save();

			if(isset($vcards[$vcard]['photo'])) {

				$time = time();

				$path = DIMS_WEB_PATH . 'data/photo_cts/contact_'.$id_new_ct;
				//on verifie si le dossier existe deja, si oui, on le vide
				$files = scandir($path);
				if(!empty($files)){
					foreach($files as $file) {
						if($file != "." & $file != "..") {
							$p_todel = $path."/".$file;
							unlink($p_todel);
						}
					}
				}
				else {
					mkdir($path, 0777);
				}

				// ajouter ratio ?
				rename($vcards[$vcard]['photo'],$path."/photo60_".$time.".png");

				$new_ct->fields['photo'] = "_".$time;
				$new_ct->save();
			}

			if($layer > 0) {
				$ctworkspace->fields['id'] = $id_new_ct;
				$ctworkspace->fields['type_layer'] = 1;
				$ctworkspace->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
				$ctworkspace->save();
			}

			//redirections
			$vcard++;

			$sql = "UPDATE	dims_mod_vcard
				SET	id_contact = :idcontact , date_modify = '".date("Ymdhis")."'
				WHERE	id_docfile = :iddocfile
				AND	num = :num ";
			$db->query($sql, array(
				':idcontact'	=> $id_new_ct,
				':iddocfile'	=> $id_doc,
				':num'			=> $vcard
			));

			if(count($vcards) > $vcard) {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&doc_id=".$id_doc."&vcard=".$vcard."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF);
			}
			else {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=msg_ok_import");
			}

			break;
		case 'create_tiers_fromvcard':

			$id_mail = dims_load_securvalue('mail_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$id_doc = dims_load_securvalue('doc_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$contact_id = dims_load_securvalue('contact_id', dims_const::_DIMS_NUM_INPUT, true, true);
			$vcard = dims_load_securvalue('vcard',dims_const::_DIMS_NUM_INPUT,true,true);
			$id_new_ct = dims_load_securvalue('new_ct',dims_const::_DIMS_NUM_INPUT,true,true);

			//creation de la fiche contact
			$new_ct = new tiers();
			$new_ct->init_description();

			$ctworkspace = new tiers_layer();
			$ctworkspace->init_description();

			$vc = new docfile();
			$vc->open($id_doc);
			$vcards = $vc->getParseVcf();

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
				':idobject'	=> dims_const::_SYSTEM_OBJECT_TIERS
			));

			$convmeta = array();
			$mode=array();
			while ($fields = $db->fetchrow($rs_fields)) {
				// on ajoute maintenant les champs dans la liste
				$fields['use']=0;// par defaut non utilise
				$fields['enabled']=array();

				$mode[$fields['id']]=$fields['mode'];

				// enregistrement de la conversion
				$convmeta[$fields['namefield']]=$fields['id'];
			}

			$new_ct->fields['intitule'] = $vcards[$vcard]['org'];

			$layer = 0;
			/*if($vcards[$vcard]['tel']['work'] != '') {
				if($mode[$convmeta['telephone']] == 0) {
					$new_ct->fields['telephone'] = $vcards[$vcard]['tel']['work'];
				}
				else {
					$ctworkspace->fields['telephone'] = $vcards[$vcard]['tel']['work'];
					$layer = 1;
				}
			}*/

			if($vcards[$vcard]['adr']['work']['rue'] != '') {
				if($mode[$convmeta['adresse']] == 0) {
					$new_ct->fields['adresse'] = $vcards[$vcard]['adr']['work']['rue'];
				}
				else {
					$ctworkspace->fields['adresse'] = $vcards[$vcard]['adr']['work']['rue'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['work']['cp'] != '') {
				if($mode[$convmeta['codepostal']] == 0) {
					$new_ct->fields['codepostal'] = $vcards[$vcard]['adr']['work']['cp'];
				}
				else {
					$ctworkspace->fields['codepostal'] = $vcards[$vcard]['adr']['work']['cp'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['work']['city'] != '') {
				if($mode[$convmeta['ville']] == 0) {
					$new_ct->fields['ville'] = $vcards[$vcard]['adr']['work']['city'];
				}
				else {
					$ctworkspace->fields['ville'] = $vcards[$vcard]['adr']['work']['city'];
					$layer = 1;
				}
			}

			if($vcards[$vcard]['adr']['work']['pays'] != '') {
				if($mode[$convmeta['pays']] == 0) {
					$new_ct->fields['pays'] = $vcards[$vcard]['adr']['work']['pays'];
				}
				else {
					$ctworkspace->fields['pays'] = $vcards[$vcard]['adr']['work']['pays'];
					$layer = 1;
				}
			}

			if(isset($vcards[$vcard]['url'])) {
				$all_url = '';
				foreach($vcards[$vcard]['url'] as $url) {
					if($url != '') $all_url .= $url."; ";
				}
				if($mode[$convmeta['site_web']] == 0) {
					$new_ct->fields['site_web'] = $all_url;
				}
				else {
					$ctworkspace->fields['site_web'] = $all_url;
					$layer = 1;
				}
			}

			//on enregistre
			$id_new_tiers = $new_ct->save();
			if($layer > 0) {
				$ctworkspace->fields['id'] = $id_new_ct;
				$ctworkspace->fields['type_layer'] = 1;
				$ctworkspace->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
				$id_ent = $ctworkspace->save();
			}

			//on cree le lien entre le contact et l'entreprise
			$tiers_ct = new tiersct();
			$tiers_ct->init_description();
			$tiers_ct->fields['id_tiers'] = $id_new_tiers;
			$tiers_ct->fields['id_contact'] = $id_new_ct;
			$tiers_ct->fields['type_lien'] = $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'];
			$tiers_ct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$tiers_ct->fields['date_create'] = date('YmdHis');
			$tiers_ct->fields['link_level'] = 2;
			$tiers_ct->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];

			$tiers_ct->save();


			//redirections
			//dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&subaction=".dims_const::_DIMS_MENU_MAIL_RECEIVED);
			$vcard++;

			$sql = "UPDATE	dims_mod_vcard
				SET	id_contact = :idcontact , date_modify = '".date("Ymdhis")."'
				WHERE	id_docfile = :iddocfile
				AND	num = :num ";
			$db->query($sql, array(
				':idcontact'	=> $id_new_ct,
				':iddocfile'	=> $id_doc,
				':num'			=> $vcard
			));

			if(count($vcards) > $vcard) {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id."&doc_id=".$id_doc."&vcard=".$vcard."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF);
			}
			else {

				dims_redirect($tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&do=msg_ok_import");
			}
		case 'msg_ok_import':
			echo '<p><b>'.$_DIMS['cste']['_DIMS_LABEL_FIN_IMPORT_VCARD'].'</b></p>';

			break;
		default:
			require_once DIMS_APP_PATH."modules/system/crm_public_contact_view_vcf.php";
		break;
	}


	break;
}

$mail_id = dims_load_securvalue('mail_id',dims_const::_DIMS_NUM_INPUT,true,true);
//dims_print_r($tab_infmail);

if(!empty($mail_id)){

	switch ($subaction){

	case dims_const::_DIMS_MENU_MAIL_RECEIVED :
		$mail = $tab_infmail[$mail_id];

		// mise à jour du mail : marqué comme lu
		if ($mail['read'] == 0){
			$sql = "UPDATE `"._DIMS_DB_DATABASE."`.`dims_mod_webmail_email`
				SET `read` = '1'
				WHERE `dims_mod_webmail_email`.`id` = :mailid ";
			$db->query($sql, array(
				':mailid'	=> $mail_id
			));
		}

		// recherche du/des destinataire(s) : To
		$sql = "SELECT mail
			FROM dims_mod_webmail_email_adresse
			WHERE id_mail = :mailid
			AND type = 2";
		$dest_To = $db->query($sql, array(
			':mailid'	=> $mail_id
		));
		$mail['to'] = '<table width="100%" cellpadding="0" cellspacing="0" border="0">' ;

		while($dest = $db->fetchrow($dest_To)) {
		$mail['to'] .= "<tr><td>".$dest['mail']."</td></tr>";
		}
		$mail['to'] .= "</table>";

		// recherche du/des destinataire(s) : CC
		$sql = "SELECT mail
			FROM dims_mod_webmail_email_adresse
			WHERE id_mail = :mailid
			AND type = 3";
		$dest_Cc = $db->query($sql, array(
			':mailid'	=> $mail_id
		));
		$nb_cc = $db->numrows($dest_Cc);
		if ($nb_cc > 0){
		$mail['cc'] = '<table width="100%" cellpadding="0" cellspacing="0" border="0">' ;

		while($dest = $db->fetchrow($dest_Cc)) {
			$mail['cc'] .= "<tr><td>".$dest['mail']."</td></tr>";
		}
		$mail['cc'] .= "</table>";
		}

		echo $skin->open_simplebloc($mail['subject']);
		echo '<table width="100%">';
			echo '<tr>';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_SUBJECT'];
				echo '</td>';
				echo '<td>';
					echo $mail['subject'];
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_AT'];
				echo '</td>';
				echo '<td>';
					$dateLocal = dims_timestamp2local($mail['date']);
					echo $dateLocal['date'];
				echo '</td>';
			echo '</tr>';
			echo '<tr style="vertical-align:top">';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_FROM'];
				echo '</td>';
				echo '<td>';
					echo $mail['from'];
				echo '</td>';
			echo '</tr>';
			echo '<tr style="vertical-align:top">';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_DIMS_LABEL_MAIL_TO'];
				echo '</td>';
				echo '<td>';
					echo $mail['to'];
				echo '</td>';
			echo '</tr>';
			if ($nb_cc > 0 ){
			echo '<tr>';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_DIMS_LABEL_MAIL_CC'];
				echo '</td>';
				echo '<td>';
					echo $mail['cc'];
				echo '</td>';
			echo '</tr>';
			}
			echo '<tr width=10%>';
				echo '<td style="vertical-align:top;">';
					echo $_DIMS['cste']['_CONTENT'];
				echo '</td>';
				echo '<td>';
					echo str_replace("\n","<br>",$mail['content']);
				echo '</td>';
			echo '</tr>';
			echo '<tr>';

			// affichage fichiers joints

			if ((isset($mail['pj'])) && (count($mail['pj']) > 0)) {
				$attachement = "<tr>";
				$attachement .= '<td width=10% style="vertical-align:top;">';
				$attachement .= $_DIMS['cste']['_DIMS_LABEL_MAIL_ATTACHMENT'];
				$attachement .= '</td>';
				$attachement .= '<td>';

				$vcf = '<tr>';
				$vcf .= '<td width=10% style="vertical-align:top;">';
				$vcf .= $_DIMS['cste']['_DIMS_LABEL_VCARD'];
				$vcf .= '</td>';
				$vcf .= '<td>';

				$id_task = 0 ;

				foreach($mail['pj'] as $doc_id) {
					$docfile = new docfile();
					$docfile->open($doc_id);

				if($docfile->fields['extension'] != 'vcf') {
					$attachement .= '<a href="'.$docfile->getwebpath().'">';
					$attachement .= $docfile->fields['name'];
					$attachement .= '</a>';
					$attachement .= '<br />';

				}else{

					$vcf .= '<a href="Javascript: void(0);" onclick="Javascript: dims_switchdisplay(\'infos_entity_'.$id_task.'\');">'.$docfile->fields['name'].'</a><br />';

					$sql = "SELECT	name_vcard, id_contact, num, date_modify
						FROM	dims_mod_vcard
						WHERE	id_docfile = :iddocfile ";
					$res = $db->query($sql, array(
						':iddocfile'	=> $doc_id
					));
					if ($db->numrows($res) == 0){
						if (file_exists($docfile->getfilepath())) {
							$num = 1 ;
							$content = fopen($docfile->getwebpath(), 'r');
							while($ligne = fgets($content)) {
								if (substr($ligne,0,5) == "BEGIN"){

									while(($ligne = fgets($content)) && (substr($ligne,0,3) != "END")){

										if (substr($ligne,0,2) == "FN"){
											$fn = substr($ligne,3);
											$tabfn = explode(" ",$fn);

											$sql2 = "INSERT INTO	dims_mod_vcard
												VALUES		( :iddocfile , :fn , '0', :num ,'')";
											$db->query($sql2, array(
												':iddocfile'	=> $docfile->fields['id'],
												':fn'			=> trim($fn),
												':num'			=> $num
											));
											$num ++ ;
										}
									}
								}
							}
							fclose($content);
						}
						$res = $db->query($sql, array(
							':iddocfile'	=> $doc_id
						));
					}

					if ($db->numrows($res) > 0){

						$vcf .= '<div id="infos_entity_'.$id_task.'" style="display:none;padding:10px;">';
						$vcf .= '<table>';

						while($etat = $db->fetchrow($res)){
							if ($etat['id_contact'] > 0){
								//echo '&nbsp;<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&contact_id=".$ct."&mail_id=".$mail_id.'&doc_id='.$docfile->fields['id'].'&vcard='.$etat['etat'].'" style="text-decoration:none;"><img src="./common/img/mailsend.png" alt="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'" title="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'"/></a>';
								// afficher les contacts liés à la vcard

								$sql = "SELECT	lastname, firstname
									FROM	dims_mod_business_contact
									WHERE	id = :id ";
								if ($res2 = $db->query($sql, array(':id' => $etat['id_contact']))){
									$contact = $db->fetchrow($res2);
									$vcf .= '<tr><td style="text-align:left; color: #333; width:130px;">';
									$vcf .= '<a href="'.$tabscriptenv.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$val.'">';
									$vcf .= $contact['firstname']." ".$contact['lastname'].'</a>';
									$vcf .= '</td>';
									$dateLocal = dims_timestamp2local($etat['date_modify']);
									$vcf .= '<td>'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_DATE'].' '.$dateLocal['date'].'</td>';
									$vcf .= '</tr>';
								}
							}else{
								$vcf .= '<tr><td style="text-align:left; color: #333; width:130px;">';
								$vcf .= $etat['name_vcard'];
								$num_vcard = $etat['num']-1;
								$vcf .= '&nbsp;<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&contact_id=".$ct."&mail_id=".$mail_id.'&doc_id='.$doc_id.'&vcard='.$num_vcard.'" style="text-decoration:none;"><img src="./common/img/mailsend.png" alt="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'" title="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'"/></a>';
								$vcf .= '</td></tr>';
							}
						}

						$vcf .= '</table>';
						$vcf .= '</div>';

					}
					$id_task ++ ;
					}

				}
				$attachement .= '</td></tr>';
				$vcf .= '</td></tr>';

				if($id_task > 0)
				echo $vcf ;
				if(count($mail['pj']) > $id_task)
				echo $attachement ;
			}
		echo '</table>';
		echo $skin->close_simplebloc();
	break ;

	case dims_const::_DIMS_MENU_MAIL_SENT :
		$mail = $tab_infmail[$mail_id];

		// "recherhe" de l'expéditeur
		$sql = "SELECT mail
			FROM dims_mod_webmail_email_adresse
			WHERE id_mail = :idmail
			AND type = 1";
		$expediteur = $db->query($sql, array(
			':idmail'	=> $mail_id
		));
		$res = $db->fetchrow($expediteur) ;
		$mail['from'] = $res['mail'];

		// recherche du/des destinataire(s) : CC
		$sql = "SELECT mail
			FROM dims_mod_webmail_email_adresse
			WHERE id_mail = :idmail
			AND type = 3";
		$dest_Cc = $db->query($sql, array(
			':idmail'	=> $mail_id
		));
		$nb_cc = $db->numrows($dest_Cc);
		if ($nb_cc > 0){
		$mail['cc'] = '<table width="100%" cellpadding="0" cellspacing="0" border="0">' ;

		while($dest = $db->fetchrow($dest_Cc)) {
			$mail['cc'] .= "<tr><td>".$dest['mail']."</td></tr>";
		}
		$mail['cc'] .= "</table>";
		}

		echo $skin->open_simplebloc($mail['subject']);
		echo '<table width="100%">';
			echo '<tr>';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_SUBJECT'];
				echo '</td>';
				echo '<td>';
					echo $mail['subject'];
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_AT'];
				echo '</td>';
				echo '<td>';
					$dateLocal = dims_timestamp2local($mail['date']);
					echo $dateLocal['date'];
				echo '</td>';
			echo '</tr>';
			echo '<tr style="vertical-align:top">';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_FROM'];
				echo '</td>';
				echo '<td>';
					echo $mail['from'];
				echo '</td>';
			echo '</tr>';
			echo '<tr style="vertical-align:top">';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_DIMS_LABEL_MAIL_TO'];
				echo '</td>';
				echo '<td>';
					echo $mail['to'];
				echo '</td>';
			echo '</tr>';
			if ($nb_cc > 0 ){
			echo '<tr>';
				echo '<td width=10%>';
					echo $_DIMS['cste']['_DIMS_LABEL_MAIL_CC'];
				echo '</td>';
				echo '<td>';
					echo $mail['cc'];
				echo '</td>';
			echo '</tr>';
			}
			echo '<tr>';
				echo '<td width=10% style="vertical-align:top;">';
					echo $_DIMS['cste']['_CONTENT'];
				echo '</td>';
				echo '<td>';
					echo str_replace("\n","<br>",$mail['content']);
				echo '</td>';
			echo '</tr>';

			// affichage fichiers joints

			if ((isset($mail['pj'])) && (count($mail['pj']) > 0)) {
				$attachement = "<tr>";
				$attachement .= '<td width=10% style="vertical-align:top;">';
				$attachement .= $_DIMS['cste']['_DIMS_LABEL_MAIL_ATTACHMENT'];
				$attachement .= '</td>';
				$attachement .= '<td>';

				$vcf = '<tr>';
				$vcf .= '<td width=10% style="vertical-align:top;">';
				$vcf .= $_DIMS['cste']['_DIMS_LABEL_VCARD'];
				$vcf .= '</td>';
				$vcf .= '<td>';

				$id_task = 0 ;

				foreach($mail['pj'] as $doc_id) {
					$docfile = new docfile();
					$docfile->open($doc_id);

				if($docfile->fields['extension'] != 'vcf') {
					$attachement .= '<a href="'.$docfile->getwebpath().'">';
					$attachement .= $docfile->fields['name'];
					$attachement .= '</a>';
					$attachement .= '<br />';

				}else{

					$vcf .= '<a href="Javascript: void(0);" onclick="Javascript: dims_switchdisplay(\'infos_entity_'.$id_task.'\');">'.$docfile->fields['name'].'</a>';

					$sql = "SELECT	name_vcard, id_contact, num, date_modify
						FROM	dims_mod_vcard
						WHERE	id_docfile = :iddocfile ";
					$res = $db->query($sql, array(
						':iddocfile'	=> $doc_id
					));
					if ($db->numrows($res) == 0){
						if (file_exists($docfile->getfilepath())) {
							$num = 1 ;
							$content = fopen($docfile->getwebpath(), 'r');
							while($ligne = fgets($content)) {
								if (substr($ligne,0,5) == "BEGIN"){

									while(($ligne = fgets($content)) && (substr($ligne,0,3) != "END")){

										if (substr($ligne,0,2) == "FN"){
											$fn = substr($ligne,3);
											$tabfn = explode(" ",$fn);

											$sql2 = "INSERT INTO	dims_mod_vcard
												VALUES		( :iddocfile , :fn , '0', :num ,'')";
											$db->query($sql2, array(
												':iddocfile'	=> $docfile->fields['id'],
												':fn'			=> trim($fn),
												':num'			=> $num
											));
											$num ++ ;
										}
									}
								}
							}
							fclose($content);
						}
						$res = $db->query($sql, array(
							':iddocfile'	=> $doc_id
						));
					}

					if ($db->numrows($res) > 0){

						$vcf .= '<div id="infos_entity_'.$id_task.'" style="display:none;">';
						$vcf .= '<table>';

						while($etat = $db->fetchrow($res)){
							if ($etat['id_contact'] > 0){
								//echo '&nbsp;<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&contact_id=".$ct."&mail_id=".$mail_id.'&doc_id='.$docfile->fields['id'].'&vcard='.$etat['etat'].'" style="text-decoration:none;"><img src="./common/img/mailsend.png" alt="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'" title="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'"/></a>';
								// afficher les contacts liés à la vcard

								$sql = "SELECT	lastname, firstname
									FROM	dims_mod_business_contact
									WHERE	id = :id ";
								if ($res2 = $db->query($sql, array( ':id' => $etat['id_contact']))){
									$contact = $db->fetchrow($res2);
									$vcf .= '<tr><td style="text-align:left; color: #333; width:130px;">';
									$vcf .= '<a href="'.$tabscriptenv.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$val.'">';
									$vcf .= $contact['firstname']." ".$contact['lastname'].'</a>';
									$vcf .= '</td>';
									$dateLocal = dims_timestamp2local($etat['date_modify']);
									$vcf .= '<td>'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_DATE'].' '.$dateLocal['date'].'</td>';
									$vcf .= '</tr>';
								}
							}else{
								$vcf .= '<tr><td style="text-align:left; color: #333; width:130px;">';
								$vcf .= $etat['name_vcard'];
								$num_vcard = $etat['num']-1;
								$vcf .= '&nbsp;<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&subaction=".dims_const::_DIMS_MENU_IMPORT_VCF."&contact_id=".$ct."&mail_id=".$mail_id.'&doc_id='.$doc_id.'&vcard='.$num_vcard.'" style="text-decoration:none;"><img src="./common/img/mailsend.png" alt="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'" title="'.$_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'].'"/></a>';
								$vcf .= '</td></tr>';
							}
						}

						$vcf .= '</table>';
						$vcf .= '</div>';

					}
					$id_task ++ ;
					}

				}
				$attachement .= '</td></tr>';
				$vcf .= '</td></tr>';

				if($id_task > 0)
				echo $vcf ;
				if(count($mail['pj']) > $id_task)
				echo $attachement ;
			}

		echo '</table>';
		echo $skin->close_simplebloc();
	break ;
	}
}

echo $skin->close_simplebloc();

?>
		</td>
	</tr>
</table>
