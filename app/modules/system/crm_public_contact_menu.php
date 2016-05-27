<?php
$part = dims_load_securvalue('part',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['businness']['part'],"");

$tabscriptenv = "$scriptenv?cat="._BUSINESS_CAT_CONTACT;

//gestion de l'affichage de l'onglet personne
$id_contact = dims_load_securvalue('contact_id',dims_const::_DIMS_CHAR_INPUT,true,true,false);
if ($id_contact==0 && !empty($_SESSION['business']['contact_id'])) $id_contact = $_SESSION['business']['contact_id'];

// test si positionnement sur une fiche contact, alors on supprime de la sesssion l'info entreprise

// if($id_contact>0 ) {
// 	$tabs[_BUSINESS_TAB_CONTACT_IDENTITE]['title'] = $_DIMS['cste']['_DIMS_LABEL_CT_FICHE'];
// 	$tabs[_BUSINESS_TAB_CONTACT_IDENTITE]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$id_contact."&part="._BUSINESS_TAB_CONTACT_IDENTITE;
// 	$tabs[_BUSINESS_TAB_CONTACT_IDENTITE]['icon'] = "./common/img/user.png";
// 	$tabs[_BUSINESS_TAB_CONTACT_IDENTITE]['width'] = 130;
// 	$tabs[_BUSINESS_TAB_CONTACT_IDENTITE]['position'] = 'left';
// 	//if (!isset($_GET['tiers_id']) && $part==_BUSINESS_TAB_CONTACT_IDENTITE) $_SESSION['dims']['businness']['part']=_BUSINESS_TAB_CONTACT_IDENTITE;
// 	//unset($_SESSION['business']['ent_id']);
// }

// //gestion de l'affichage de l'onglet entreprise
// $id_ent = dims_load_securvalue('id_ent',dims_const::_DIMS_CHAR_INPUT,true,true,false);
// if ($id_ent==0 && !empty($_SESSION['business']['ent_id'])) $id_ent = $_SESSION['business']['ent_id'];

// // test si positionnement sur une fiche contact, alors on supprime de la sesssion l'info entreprise
// if($id_ent>0) {
// 	$tabs[_BUSINESS_TAB_ENT_IDENTITE]['title'] = $_DIMS['cste']['_DIMS_LABEL_ENT_FICHE'];
// 	$tabs[_BUSINESS_TAB_ENT_IDENTITE]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_ENT_FORM."&id_ent=".$id_ent."&part="._BUSINESS_TAB_ENT_IDENTITE;
// 	$tabs[_BUSINESS_TAB_ENT_IDENTITE]['icon'] = "./common/img/factory.gif";
// 	$tabs[_BUSINESS_TAB_ENT_IDENTITE]['width'] = 155;
// 	$tabs[_BUSINESS_TAB_ENT_IDENTITE]['position'] = 'left';
// 	//unset($_SESSION['business']['contact_id']);
// }

// $tabs[_BUSINESS_TAB_CONTACTSSEEK]['title'] = $_DIMS['cste']['_SEARCH'];
// $tabs[_BUSINESS_TAB_CONTACTSSEEK]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACTSSEEK."&part="._BUSINESS_TAB_CONTACTSSEEK."&view=1";
// $tabs[_BUSINESS_TAB_CONTACTSSEEK]['icon'] = _BUSINESS_ICO_RECHERCHE;
// $tabs[_BUSINESS_TAB_CONTACTSSEEK]['width'] = 110;
// $tabs[_BUSINESS_TAB_CONTACTSSEEK]['position'] = 'left';

// // on regarde si on a le choix ou non
// if ($workspace->fields['contact_activeent']==1) {
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['title'] = $_DIMS['cste']['_DIMS_ADD'];
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACTSTIERS."&part="._BUSINESS_TAB_CONTACTSTIERS;
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['icon'] = "./common/modules/system/img/contact_add.png";
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['width'] = 95;
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['position'] = 'left';
// }
// else {
// 	// lien direct vers contact
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['title'] = $_DIMS['cste']['_DIMS_ADD'];
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACTSTIERS."&part="._BUSINESS_TAB_CONTACTSTIERS."&case=1";
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['icon'] = "./common/modules/system/img/contact_add.png";
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['width'] = 95;
// 	$tabs[_BUSINESS_TAB_CONTACTSTIERS]['position'] = 'left';
// }
/*
$tabs[_BUSINESS_TAB_CONTACTSADD]['title'] = $_DIMS['cste']['_DIMS_LABEL_VEILLE'];
$tabs[_BUSINESS_TAB_CONTACTSADD]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACTSADD."&part="._BUSINESS_TAB_CONTACTSADD;
$tabs[_BUSINESS_TAB_CONTACTSADD]['icon'] = "./common/img/view.png";
$tabs[_BUSINESS_TAB_CONTACTSADD]['width'] = 100;
$tabs[_BUSINESS_TAB_CONTACTSADD]['position'] = 'left';
*/
if (dims_isadmin()) {
	$tabs[_BUSINESS_TAB_ADMIN]['title'] = $_DIMS['cste']['_DIMS_LABEL_ADMIN'];
	$tabs[_BUSINESS_TAB_ADMIN]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_ADMIN."&part="._BUSINESS_TAB_ADMIN;
	$tabs[_BUSINESS_TAB_ADMIN]['icon'] = "./common/img/configure.png";
	$tabs[_BUSINESS_TAB_ADMIN]['width'] = 140;
	$tabs[_BUSINESS_TAB_ADMIN]['position'] = 'left';
}

// if ($workspace->fields['contact_outlook']==1) {
// 	$tabs[_BUSINESS_TAB_IMPORT_OUTLOOK]['title'] = $_DIMS['cste']['_LABEL_IMPORT_OUTLOOK'];
// 	$tabs[_BUSINESS_TAB_IMPORT_OUTLOOK]['url'] = "$tabscriptenv&dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT."&cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK;
// 	$tabs[_BUSINESS_TAB_IMPORT_OUTLOOK]['icon'] = "./common/img/configure.png";
// 	$tabs[_BUSINESS_TAB_IMPORT_OUTLOOK]['width'] = 145;
// 	$tabs[_BUSINESS_TAB_IMPORT_OUTLOOK]['position'] = 'left';
// }

// if ($workspace->fields['contact_activeent']==1) {
// 	$tabs[_BUSINESS_TAB_IMPORT_ENTREPRISES]['title'] = $_DIMS['cste']['_LABEL_IMPORT_ENTREPRISE'];
// 	$tabs[_BUSINESS_TAB_IMPORT_ENTREPRISES]['url'] = "$tabscriptenv&dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT."&cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES;
// 	$tabs[_BUSINESS_TAB_IMPORT_ENTREPRISES]['icon'] = "./common/img/configure.png";
// 	$tabs[_BUSINESS_TAB_IMPORT_ENTREPRISES]['width'] = 155;
// 	$tabs[_BUSINESS_TAB_IMPORT_ENTREPRISES]['position'] = 'left';
// }
// if (dims_isadmin()) {
// 	$tabs[_BUSINESS_TAB_MANAGE_DOUBLONS]['title'] = $_DIMS['cste']['_MANAGE_DOUBLONS'];
// 	$tabs[_BUSINESS_TAB_MANAGE_DOUBLONS]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_MANAGE_DOUBLONS."&part="._BUSINESS_TAB_MANAGE_DOUBLONS;
// 	$tabs[_BUSINESS_TAB_MANAGE_DOUBLONS]['icon'] = "./common/img/users.png";
// 	$tabs[_BUSINESS_TAB_MANAGE_DOUBLONS]['width'] = 190;
// 	$tabs[_BUSINESS_TAB_MANAGE_DOUBLONS]['position'] = 'left';
// }
// if (dims_isadmin()) {
// 	$tabs[_BUSINESS_TAB_INCOMPLETE_RECORDS]['title'] = $_DIMS['cste']['_INCOMPLETE_RECORDS'];
// 	$tabs[_BUSINESS_TAB_INCOMPLETE_RECORDS]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_INCOMPLETE_RECORDS."&part="._BUSINESS_TAB_INCOMPLETE_RECORDS;
// 	$tabs[_BUSINESS_TAB_INCOMPLETE_RECORDS]['icon'] = "./common/img/users_disabled.png";
// 	$tabs[_BUSINESS_TAB_INCOMPLETE_RECORDS]['width'] = 160;
// 	$tabs[_BUSINESS_TAB_INCOMPLETE_RECORDS]['position'] = 'left';
// }

//$tabs[_BUSINESS_TAB_IMPORT_MISSIONS]['title'] = $_DIMS['cste']['_LABEL_IMPORT_MISSIONS'];
//$tabs[_BUSINESS_TAB_IMPORT_MISSIONS]['url'] = "$tabscriptenv&dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT."&cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_MISSIONS."&part="._BUSINESS_TAB_IMPORT_MISSIONS;
//$tabs[_BUSINESS_TAB_IMPORT_MISSIONS]['icon'] = "./common/img/configure.png";
//$tabs[_BUSINESS_TAB_IMPORT_MISSIONS]['width'] = 160;
//$tabs[_BUSINESS_TAB_IMPORT_MISSIONS]['position'] = 'left';

?>
