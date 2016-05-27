<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');

//on regarde d'abord quel droit de partage on a
$cur_wksp = new workspace();
$cur_wksp->open($_SESSION['dims']['workspaceid']);
$sqltagfilter='';
$in = '0';

$sql_in = "	SELECT	id_to
						FROM	dims_workspace_share
						WHERE	id_from = :idfrom
						AND		active = 1
						AND		id_object = :idobject ";
$res_in = $db->query($sql_in, array(
	':idfrom' 	=> $_SESSION['dims']['workspaceid'],
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));

if($db->numrows($res_in) >= 1) {
		while($tabw = $db->fetchrow($res_in)) {
				$in .= ", ".$tabw['id_to'];
		}
		$in .= ", ".$_SESSION['dims']['workspaceid']; //on ajoute le workspace courant sinon il sera exclu des recherches
}
else {
		$in = $_SESSION['dims']['workspaceid'];
}

$_SESSION['business']['search_ct']	= array();
$_SESSION['business']['search_ent']	= array();
$_SESSION['business']['search_lkent'] = array();
$_SESSION['business']['search_ct_sql'] = array();

//on recupere les infos du formulaire
//les infos generiques
$inf_ct = new contact();
$inf_ct->init_description(false);
$inf_ct->setvalues($_POST, "ct_");

foreach($inf_ct->fields as	$g=>$fi) {
	$inf_ct->fields[$g]=trim($fi);
}

$_SESSION['business']['search_ct']=$inf_ct->fields;

//les infos entreprise
$inf_e = new tiers();
$inf_e->init_description(false);
$inf_e->setvalues($_POST, "ent_");

$_SESSION['business']['search_ent']=$inf_e->fields;
if($_SESSION['business']['search_ent']['partenaire'] == -1)  $_SESSION['business']['search_ent']['partenaire'] = "";

//les infos du lien pers->ent
$inf_cte = new tiersct();
$inf_cte->init_description(false);
$inf_cte->setvalues($_POST, "lke_");

$_SESSION['business']['search_lkent']=$inf_cte->fields;

//traitement des conditions propres a la personne
$where = '';
$wherelayer='';
foreach ($_SESSION['business']['search_ct'] as $lbl_f => $val_f) {
		if($val_f != "") {
		$where .= " lcase(c.$lbl_f) LIKE lcase('%$val_f%') AND ";
		if ($wherelayer!='') $wherelayer.='AND';
		$wherelayer .= " lcase(cl.$lbl_f) LIKE lcase('%$val_f%')";
	}
}

if ($wherelayer!='') {
	$wherelayer=' OR ('.$wherelayer.') ';
}

$sql_grpct='';

if (isset($_POST['id_groupct']) && $_POST['id_groupct']!='') {
		$id_groupct = dims_load_securvalue('id_groupct', dims_const::_DIMS_NUM_INPUT, true, true);
		$sql_grpct = " INNER JOIN dims_mod_business_contact_group_link as gl on gl.id_contact=c.id and gl.id_group_ct=".$id_groupct;
}

if (isset($_POST['id_tag']) && $_POST['id_tag']>0) {
		// construction de la liste
		$idtag = dims_load_securvalue('id_tag', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$sqltagfilter=" inner join dims_tag_index as ti on
				ti.id_tag=".$idtag." and ti.id_record=c.id and ti.id_object=".dims_const::_SYSTEM_OBJECT_CONTACT." and ti.id_module=1
				inner join dims_tag as t on t.id=ti.id_tag and t.id_workspace=".$_SESSION['dims']['workspaceid'];
}

if (isset($_POST['id_user_from']) && $_POST['id_user_from']!='0' && $_POST['id_user_from']!='') {
		$id_user_from = dims_load_securvalue('id_user_from', dims_const::_DIMS_NUM_INPUT, true, true);

		$_SESSION['business']['search_ct']['id_user_from']=$id_user_from;

		// on ajoute les liens metiers et eventuellement perso si
		$sql_grpct.=" inner join dims_mod_business_ct_link as l on (l.id_contact2=c.id and l.id_contact1=".$id_user_from;
		$sql_grpct.=") or (l.id_contact1=c.id and l.id_contact2=".$id_user_from." and link_level<=2)";
}

if (isset($_POST['id_workspace_from']) && $_POST['id_workspace_from']!='0' && $_POST['id_workspace_from']!='') {
		$id_workspace_from = dims_load_securvalue('id_workspace_from', dims_const::_DIMS_NUM_INPUT, true, true);

		$_SESSION['business']['search_ct']['id_workspace_from']=$id_workspace_from;

		// on ajoute les liens layer
		$sql_grpct.=" inner join dims_mod_business_contact_layer as cl on cl.id=c.id and cl.type_layer=1 and cl.id_layer=".$id_workspace_from;

}
else  {
	$sql_grpct.=" left join dims_mod_business_contact_layer as cl on cl.id=c.id and cl.type_layer=1 and cl.id_layer=".$_SESSION['dims']['workspaceid'];
}
//traitement des conditions propres a l'entreprise
$where_ent = '';
foreach ($_SESSION['business']['search_ent'] as $lbl_f => $val_f) {
		if($val_f != "" || $val_f != 0) $where_ent .= " AND lcase(t.$lbl_f) LIKE lcase('$val_f%') ";
}

//traitement des conditions propres au lien avec l'entreprise
$where_lkent = '';
foreach ($_SESSION['business']['search_lkent'] as $lbl_f => $val_f) {
		if($val_f != "") $where_lkent .= " AND tc.$lbl_f LIKE '$val_f%' ";
}

//elements concernant l'entreprise a selectionner
if($where_ent != '' || $where_lkent != '') $sel_ent = ', t.id as id_ent, t.intitule, t.date_creation, tc.type_lien,tc.function';
else $sel_ent ='';
//traitement des conditions du lien avec entreprise
$opt_ent = '';

if($where_ent != '' || $where_lkent != '') {
		$opt_ent = '	INNER JOIN	dims_mod_business_tiers_contact tc
										ON		tc.id_contact = c.id ';
		$opt_ent .= $where_lkent;
		$opt_ent .='	INNER JOIN	dims_mod_business_tiers t
										ON		t.id = tc.id_tiers ';
		$opt_ent .= $where_ent;
}

//if($where != '' || $where_ent!='') {
$sql_s = "";
$sql_s .= "SELECT distinct c.id as id_ct, c.lastname, c.firstname, c.timestp_modify, c.email, c.phone, c.address, c.postalcode, c.city, c.country, c.inactif ";
$sql_s .= $sel_ent;
$sql_s .= " FROM dims_mod_business_contact c ";
$sql_s .= $opt_ent.$sql_grpct.$sqltagfilter	;
if ($where!="") {
	$sql_s .= " WHERE (".substr($where, 0, -4)." AND c.lastname!='' AND c.id_workspace IN (".$in."))";
}
else {
	$sql_s .= " WHERE (c.lastname!='' AND c.id_workspace IN (".$in."))";
}

if ($wherelayer!='')
	$sql_s .=$wherelayer;

$sql_s .= " ORDER BY c.lastname, c.firstname";

//$sql_s .= " LIMIT 0,200 ";
//echo $sql_s;
$res_s = $db->query($sql_s);

// construction de la requete d'export
$sql_s = "SELECT distinct c.id as id_ct";

$sql =	"
				SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
										mb.protected,mb.name as namefield,mb.label as titlefield
				FROM		dims_mod_business_meta_field as mf
				INNER JOIN	dims_mb_field as mb
				ON			mb.id=mf.id_mbfield
				RIGHT JOIN	dims_mod_business_meta_categ as mc
				ON			mf.id_metacateg=mc.id
				WHERE		  mf.id_object = :idobject
				AND			mf.used=1
				AND			mf.option_exportview=1
				ORDER BY	mc.position, mf.position
				";
$rs_fields=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));
$_SESSION['business']['exportdata']=array();
while ($fields = $db->fetchrow($rs_fields)) {
		$sql_s .= ",c.".$fields['namefield'];

		if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
		else $namevalue=$fields['name'];
		$elem=array();
		$elem['title']=$namevalue;
		$elem['namefield']=$fields['namefield'];

		$_SESSION['business']['exportdata'][]=$elem;
}

$sql_s .= ", c.inactif ";
$sql_s .= $sel_ent;
$sql_s .= " FROM dims_mod_business_contact c ";

$sql_s .= $opt_ent.$sql_grpct.$sqltagfilter	;
if ($where!="") $sql_s .= " WHERE ".substr($where, 0, -4)." ";
$sql_s .= " ORDER BY c.lastname, c.firstname";

//mise en session de la requete pour l'export (lfb_public_contact_searchexport.php)
$_SESSION['business']['search_ct_sql'] = $sql_s;
//}

?>
