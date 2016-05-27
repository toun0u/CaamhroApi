<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . '/modules/system/class_ct_link.php');
//recuperation des donn�es du formulaire
$idlink = dims_load_securvalue('id_link',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_ct = dims_load_securvalue('id_cont',dims_const::_DIMS_CHAR_INPUT,true,true);
$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true);

$type_link = dims_load_securvalue('type_link',dims_const::_DIMS_CHAR_INPUT,true,true);
$link_level = dims_load_securvalue('link_level',dims_const::_DIMS_CHAR_INPUT,true,true);
$commentaire = dims_load_securvalue('commentaire',dims_const::_DIMS_CHAR_INPUT,true,true);

$date_deb_d = dims_load_securvalue('date_deb_day', dims_const::_DIMS_NUM_INPUT, true, true);
$date_deb_m = dims_load_securvalue('date_deb_month', dims_const::_DIMS_NUM_INPUT, true, true);
$date_deb_y = dims_load_securvalue('date_deb_year', dims_const::_DIMS_NUM_INPUT, true, true);
$date_fin_d = dims_load_securvalue('date_fin_day', dims_const::_DIMS_NUM_INPUT, true, true);
$date_fin_m = dims_load_securvalue('date_fin_month', dims_const::_DIMS_NUM_INPUT, true, true);
$date_fin_y = dims_load_securvalue('date_fin_year', dims_const::_DIMS_NUM_INPUT, true, true);
if($date_deb_d != "jj" && $date_deb_m != "mm" && $date_deb_y != "aaaa") {
	$date_deb = $date_deb_y.$date_deb_m.$date_deb_d."000000";
}
else {
	$date_deb = 0;
}
if($date_fin_d != "jj" && $date_fin_m != "mm" && $date_fin_y != "aaaa") {
	$date_fin = $date_fin_y.$date_fin_m.$date_fin_d."000000";
}
else {
	$date_fin = 0;
}

$ctlk = new ctlink();
$ctlk->open($idlink);

//insertion des �l�ments
if($type_link != $ctlk->fields['type_link'])	$ctlk->fields['type_link'] = $type_link;
if($link_level != $ctlk->fields['link_level'])	$ctlk->fields['link_level'] = $link_level;
if($commentaire != $ctlk->fields['commentaire'])	$ctlk->fields['commentaire'] = $commentaire;
if($date_deb != $ctlk->fields['date_deb'])		$ctlk->fields['date_deb'] = $date_deb;
if($date_fin != $ctlk->fields['date_fin'])		$ctlk->fields['date_fin'] = $date_fin;
$ctlk->fields['id_user'] = $_SESSION['dims']['userid'];

$ctlk->save();

$c = new contact();
$c->open($ctlk->fields['id_contact1']);
$c->save();

$c = new contact();
$c->open($ctlk->fields['id_contact2']);
$c->save();

//redirection
if($ctlk->fields['id_object'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
	dims_redirect('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_INTELL.'&part='._BUSINESS_TAB_CT_INTEL_PERS.'&contact_id='.$id_ct);
}
if($ctlk->fields['id_object'] == dims_const::_SYSTEM_OBJECT_TIERS) {
	dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$id_ct);
}
?>
