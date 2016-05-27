<link type="text/css" rel="stylesheet" href="./common/js/chosen/chosen.css" media="screen" />
<script language="JavaScript" type="text/JavaScript" src="./common/js/chosen/chosen.jquery.js"></script>
<?
// recuperation des deux champs name et firstname
$sql=	"SELECT dims_mod_business_meta_field.id,mb.label
		FROM dims_mod_business_meta_field
		INNER JOIN dims_mb_field as mb
		ON mb.id=dims_mod_business_meta_field.id_mbfield
		AND dims_mod_business_meta_field.id_object= :idobject
		AND (mb.name='lastname' or mb.name='firstname')
		ORDER BY mb.name desc";

$tabfiltrefield=array();
$res=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		if (isset($_DIMS['cste'][$f['label']])) $f['label']=$_DIMS['cste'][$f['label']];
		$tabfiltrefield[]=$f;
	}
}

// ajout des champs devant ï¿œtre renseignï¿œs pour l'ajout
$arrayfield_control=array();

// liste users courant
$arrayUsers = array();

// update unique pour migration des champs NACE, a ne pas refaire !!!

echo "<script type=\"text/javascript\" src=\"./common/modules/system/include/calendar.js\"></script>";
echo "<script type=\"text/javascript\" src=\"./common/modules/system/include/script_booking.js\"></script>";
?>
<script type="text/javascript">
	var timersearch;
	var nbelem;
	var id_field;
	var id_categ;
	var value;

		window['validateFormCRM'] = function validateFormCRM() {
			$("#form_ct").submit();
		}

	window['updateLine'] = function updateLine(id,id_categ,id_field) {
		var elem = dims_getelem('trline'+id);
		elem.className="trctsel";
		//if (arrayModif[id_categ][id_field][1]==0) {
		if (document.getElementById("cmdshare"+id)!=null) {
			dims_xmlhttprequest_todiv("admin.php", "op=refresh_viewfieldshare&id="+id, "", "cmdshare"+id);
			arrayModif[id_categ][id_field][1]=1;
			checkUpdateCateg(id_categ);
		}
	}

	window['checkUpdateCateg'] = function checkUpdateCateg(id_cat) {
		id_categ=id_cat;
		var elem=document.form_ct['ch_categ_'+id_categ];
		if (elem!=null) {
			value=-1;

			for (i=0;i<=2;i++) {
				if (elem[i].checked) {
					value=i;
				}
			}
			/*parcours des elements pour trouver si chgt */
			if (value>=0) {

				nbelem=arrayModif[id_categ].length;
				id_field=0;
				/*for (id_field=0;id_field<nbelem;id_field++) {
					if (arrayModif[id_categ][id_field][1]==1) {
						updateValueShare(arrayModif[id_categ][id_field][0],value);
					}
				}*/

				updateValueShareRecursive('',id_field);
			}
		}
	}

	window['updateValueShare'] = function updateValueShare(id,value) {
		var elem = dims_getelem('trline'+id);
		elem.className="trctsel";
		dims_xmlhttprequest_todiv("admin.php", "op=update_viewfieldshare&id="+id+"&value="+value, "", "cmdshare"+id);
	}

	window['updateValueShareRecursive'] = function updateValueShareRecursive(result,id_field) {

		if (id_field<=nbelem) {
			if (result!='' && id_field>0) {
				var id=arrayModif[id_categ][(id_field-1)][0];
				document.getElementById("cmdshare"+id).innerHTML=result;
			}

			if (id_field<nbelem) {
				if (arrayModif[id_categ][id_field][1]==1) {
					var id=arrayModif[id_categ][id_field][0];
					var elem = dims_getelem('trline'+id);
					elem.className="trctsel";
					dims_xmlhttprequest_tofunction("admin-light.php", "op=update_viewfieldshare&id="+id+"&value="+value,updateValueShareRecursive,(id_field+1));
				}
				else {
					updateValueShareRecursive('',(id_field+1));
				}
			}
		}
	}

	window['upKeysearch'] = function upKeysearch() {
		clearTimeout(timersearch);
		timersearch = setTimeout('execSearch()', 800);
	}

	window['execSearch'] = function execSearch() {
		clearTimeout(timersearch);
		var nomsearch = dims_getelem('search_ent').value;
		var divtoaffich = dims_getelem('dispres_searchent');

		if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "op=search_enttoadd&action=<?php echo _BUSINESS_TAB_CONTACTSTIERS;?>&ent_name="+nomsearch, "", "dispres_searchent");
			divtoaffich.style.display = "block";
		}
	}

	window['affiche_div'] = function affiche_div(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}

	window['sendRequestInfo'] = function sendRequestInfo(id_record, type, rubcour) {
		var retour = dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<?php echo _BUSINESS_CAT_CONTACT; ?>&action=<?php echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=add_ticket&id_record="+id_record+"&type="+type+"&rubcour="+rubcour,"", 'dims_popup');
		dims_showpopup("popup", "450", "300", '',"dims_popup", 200, 100);
	}

		window['validate_form_ct'] = function validate_form_ct(id) {
		<?
		if (!empty($tabfiltrefield)) {
		?>

		if(validate_dyn_field()) {
			if(id == 0) {
				verif_similar(document.getElementById("field<? echo $tabfiltrefield[0]['id']; ?>").value,document.getElementById("field<? echo $tabfiltrefield[1]['id']; ?>").value);
				return false;
			}
			else {
				document.form_ct.submit();
			}
		}
		else {
			return false;
		}
		<?
		}
		else {
			echo 'document.form_ct.submit();';
		}
		?>
	}

		window['validate_form_ct_new'] = function validate_form_ct_new(id) {
		if(validate_dyn_field()) {
			if(id == 0) {
				var popup = document.getElementById("inf_similarite");
				popup.style.display="block";
				verif_similar(document.getElementById("field<? echo $tabfiltrefield[0]['id']; ?>").value,document.getElementById("field<? echo $tabfiltrefield[1]['id']; ?>").value);
				document.getElementById('inf_similarite').focus();
			}
			else {
				document.form_ct.submit();
			}
		}

	}

	window['verif_similar'] = function verif_similar(nom,prenom) {
		id_popup = dims_openOverlayedPopup(500,424);
		dims_xmlhttprequest_todiv("admin.php", "dims_op=verif_similar_pers&nom="+nom+"&prenom="+prenom+"&id_popup="+id_popup,"",'p'+id_popup);
	}
	<?
	$sql= "SELECT		c.id,c.label,count(f.id) as cpte
			FROM		dims_mod_business_meta_categ as c
			left join	dims_mod_business_meta_field as f
			on			f.id_metacateg=c.id
			and			f.id_object= :idobject
			group by	c.id
			having		count(f.id) > 0
			order by	c.position";

	$res=$db->query($sql, array(
		':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
	));

	$arrayCateg = array();
	if ($db->numrows($res)>0) {
		echo "var arrayModif = new Array(".$db->numrows($res).");";
		$i=0;
		while ($f=$db->fetchrow($res)) {
			$max=$f['cpte'];
			// on stocke le nombre de ligne / categorie
			$arrayCateg[$i]=$max;

			echo "arrayModif[".$i."]= new Array(".$max.");";
			for ($j=0;$j<$max;$j++) {
				echo "arrayModif[".$i."][".$j."]=Array(2);";
			}
			$i++;
		}

		// on remplit les valeurs dans les cases
		$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
						mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON			mb.id=mf.id_mbfield
			LEFT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object = :idobject
			ORDER BY	mc.position, mf.position
			";

		$rs_fields=$db->query($sql, array(
			':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
		));
		if ($db->numrows($rs_fields)>0) {
			$ind_categ=-1;
			$ind_field=-1;
			$categcour="";
			while ($fields = $db->fetchrow($rs_fields)) {
				// increment de l'indice du champ
				$ind_field++;
				// test si utilise ou non
				if ($categcour!=$fields['id_metacateg']) {
					$ind_categ++;
					$ind_field=0;
					$categcour=$fields['id_metacateg'];
				}
				// on ecrit la valeur du field
				echo "arrayModif[".$ind_categ."][".$ind_field."][0]=".$fields['id'].";";
				echo "arrayModif[".$ind_categ."][".$ind_field."][1]=1;";
			}
		}
	}
	else {
		echo "var arrayModif;";
	}
	?>
</script>
<?php

//dans le cas ou on est dans l'ajout (action = _BUSINESS_TAB_CONTACTSTIERS), on affiche pas les infobulles
//$ajout = 0;
$url_action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
$id_from = dims_load_securvalue('id_from', dims_const::_DIMS_NUM_INPUT, true, true);
$type_from = dims_load_securvalue('type_from', dims_const::_DIMS_CHAR_INPUT, true, true);
$type_to = dims_load_securvalue('type_to', dims_const::_DIMS_CHAR_INPUT, true, true);
//echo "url_action : ".$url_action;
if($url_action != "" || $url_action != _BUSINESS_TAB_CONTACTSTIERS) {
	$ajout = 0;
}
else {
	$ajout = 1;
}

// construction des valeurs de session du type d'enregistrement / champ
$_SESSION['dims']['contact_fields_view']=array();

//construction du tableau de conversion name => meta_id
$convmeta=array();

// construction des categories et champs dynamiques
$categcour=0;
// 1ere requete pour les champs attaches aux rubriques generiques

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
	ORDER BY	mc.position, mf.position
	";
 //		AND			mc.admin=1
//echo $sql;
$rubgen=array();
$lstworkspace=$dims->getAllWorkspaces();
$enabledworkspace = $dims->getAdminWorkspaces();
$rs_fields=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));
$color="";

$shareactive=false;
$nbsharefield=0;

while ($fields = $db->fetchrow($rs_fields)) {
	if (!isset($rubgen[$fields['id_cat']]))  {
			$rubgen[$fields['id_cat']]=array();
			$rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
			$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
			if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
	}

	// on ajoute maintenant les champs dans la liste
	$fields['use']=0;// par defaut non utilise
	$fields['enabled']=array();
	if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

	// on ajoute ce nouveau champ
	$_SESSION['dims']['contact_fields_view'][$fields['id']]=0; // public ou workspace ou user

	$_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

	// enregistrement de la conversion
	$convmeta[$fields['namefield']]=$fields['id'];
}

// construction de la liste des usages
$sql= " select		mu.*,
					mf.id_metacateg,
					w.label as labelworkspace
		from		dims_mod_business_meta_use as mu
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=mu.id_metafield
		AND			mf.used=1
		AND			mu.id_object= :idobject
		LEFT JOIN	dims_workspace as w
		ON			w.id=mu.id_workspace";

$res=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));
if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {

		if ($f['sharemode']==1) {
			// on doit verifier si le currentworkspace fait parti de la liste
			/*foreach($enabledworkspace as $id_wkspc => $inf_wkspce) {
				if ($id_wkspc == $f['id_workspace']) {
					// on est dans la selection
					$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=1;
				}
			}*/
			if ($_SESSION['dims']['workspaceid'] == $f['id_workspace']) {
					// on est dans la selection
					$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=1;
				}
			// on stocke les workspaces qui l'utilisent
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['enabled'][$f['id_workspace']]=$f['id_workspace'];
		}
		else {
			// on est a 2 on met le flag a 2
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=2;
		}
	}
}

$isuseraccount=false;
if($contact->fields['inactif'] != 1) {
	if ($contact->fields['id']>0) {
		// verification si dims_user existe ou non
		$res=$db->query("SELECT id from dims_user where id_contact= :idcontact ", array(
			':idcontact' => $contact->fields['id']
		));
		if ($db->numrows($res)>0) {
			$isuseraccount=true;
		}
	}

if (!isset($_SESSION['dims']['crm_newcontact_actionform'])) {
	$action_formsave="";
	$marginleft="250px";
}
else {
	$action_formsave = $_SESSION['dims']['crm_newcontact_actionform'];
	$marginleft="100px";
}
?>

<form action="<? echo $action_formsave;?>" method="post" enctype="multipart/form-data" id="form_ct" name="form_ct">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
?>
<div id="inf_similarite" style="position:absolute;display:none;z-index:20;margin-top:150px;margin-left:<? echo $marginleft;?>;width:450px;height:350px;"></div>
<?php
/*
 * *********************************************************************************************/
// construction des layers eventuels sur des donnees privees ou espace de travail courant
/**********************************************************************************************/
$layers=array();

// construction des deux autres dimensions
$contactworkspace = new contact_layer();
$contactworkspace->init_description();
$contactuser = new contact_layer();
$contactuser->init_description();


if (isset($contact->fields['id']) && $contact->fields['id']>0) {
	// requete selection layer
	$sql =	"	SELECT		*
				FROM		dims_mod_business_contact_layer
				WHERE		id = :idrecord
				AND			((type_layer<=1 and id_layer= :workspaceid )
				OR			(type_layer=2 and id_layer= :userid )";

	// a ajouter : dimension partage
	$sqlshare = "select		*
				from		dims_share
				where		id_module=1
				and			id_object= :idobject
				and			id_record= :idrecord
				and			(type_from=0
				and			id_from= :workspaceid
				and			level_from=0)
				OR			(type_share=1 and id_from= :workspaceid )
				OR			(type_share=2 and id_from= :userid )";

	$res=$db->query($sqlshare, array(
		':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT,
		':idrecord' 	=> $contact->fields['id'],
		':workspaceid' 	=> $_SESSION['dims']['workspaceid'],
		':userid' 		=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($res)>0) {

		while ($f=$db->fetchrow($res)) {

			// test si share actif pour l'espace courant
			if ($f['type_from']==0 && $f['id_from']==$_SESSION['dims']['workspaceid'] && $f['level_from']==0) {
				$shareactive=true;
			}
			else {
				// on peut charger les autres valeurs
				$sql .= " OR (type_share=".$f['type_share']." and id_share=".$f['id_share'].")";
			}
		}
	}

	// on termine la requete par le tri
	$sql.=")			ORDER BY	timestp_modify,type_layer";

	$rs=$db->query($sql, array(
		':idrecord' 	=> $contact->fields['id'],
		':workspaceid' 	=> $_SESSION['dims']['workspaceid'],
		':userid' 		=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($rs)>0) {

		while ($f=$db->fetchrow($rs)) {
			$layers[$f['type_layer']]=$f;
			$owner=false;

			// ouverture de l'objet concerne
			if ($f['type_layer']==1) {
				if ($f['id_layer']==$_SESSION['dims']['workspaceid']) {
					$contactworkspace->open($contact->fields['id'],1,$f['id_layer']);
					$owner=true;
				}
				else {

				}
			}
			else {
				if ($f['id_layer']==$_SESSION['dims']['userid']) {
					$contactuser->open($contact->fields['id'],2,$f['id_layer']);
					$owner=true;
				}
			}

			// on remplit les champs courants pour le layer concerne
			if ($owner) {
				foreach($f as $name=>$val) {
					if ($val!="" && isset($convmeta[$name])) {
						$idmeta=$convmeta[$name];
						if (!isset($_SESSION['dims']['contact_fields_view'][$idmeta]) || isset($_SESSION['dims']['contact_fields_view'][$idmeta]) && $_SESSION['dims']['contact_fields_view'][$idmeta]!=2) {
							$_SESSION['dims']['contact_fields_view'][$idmeta]=$f['type_layer']; // public
						}

						if ($f['type_layer']==1) {
							$contactworkspace->fields[$name]=$val;
						}
						else {
							$contactuser->fields[$name]=$val;
						}
					}
				} // end of foreach
			} // end of owner
		}
	}
}

// test si on a des valeurs partages sans acceptation

foreach($contact->fields as $name=>$val) {
	if (isset($convmeta[$name])) {
		$idmeta=$convmeta[$name];

		if ($val!="" &&  !$shareactive && $_SESSION['dims']['contact_fields_mode'][$idmeta]==1 && $contact->fields[$name]!=''
			&& $name != 'lastname' && $name != 'firstname' ) {
			//die($name." ".$idmeta);
			// on controle si workspace = courant
			if ($contact->fields['id_workspace']==$_SESSION['dims']['workspaceid']) {
				// on doit ajouter le partage pour le courant
				$share = new share();
				$share->fields['id_module']=1;
				$share->fields['id_module_type']=1;
				$share->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
				$share->fields['id_record']=$contact->fields['id'];
				$share->fields['type_from']=0;
				$share->fields['id_from']=$_SESSION['dims']['workspaceid'];
				$share->fields['level_from']=0;
				$share->save();
				$shareactive=true;
			}
			else {
				// partageable et valeur deja saisie
				$nbsharefield++;
			}

		}

		// on met en workspace
		// Voir pourquoi on doit annuler cette ligne 29/05/2011
		//if ($_SESSION['dims']['contact_fields_view'][$idmeta]==0) $_SESSION['dims']['contact_fields_view'][$idmeta]=1;
	}
}

if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_TIERS) {
	$retour = '<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="15%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'].'&nbsp;</td>
			<td width="35%" align="left">
				<select id="tiers_type_link" name="tiers_type_link" style="background-color:#ebf2ea;">';
	$token->field("tiers_type_link");
	echo			'<option value="'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'">'.$_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR'].'</option>
					<option value="'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'">'.$_DIMS['cste']['_DIMS_LABEL_ASSOCIE'].'</option>
					<option value="'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'">'.stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']).'</option>
					<option value="'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'">'.$_DIMS['cste']['_DIMS_LABEL_OTHER'].'</option>
				</select
			</td>
			<td></td>
		</tr>
		<tr>
			<td width="15%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_LEVEL_LINK'].'&nbsp;</td>
			<td width="35%" align="left">
				<select id="tiers_link_level" name="tiers_link_level" style="background-color:#ebf2ea;">';
	$token->field("tiers_link_level");
	echo			'<option value="1">'.$_DIMS['cste']['_DIMS_LABEL_LFB_GEN'].'</option>
					<option value="2" selected>'.$_DIMS['cste']['_DIMS_LABEL_LFB_MET'].'</option>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td width="15%" align="right">'.$_DIMS['cste']['_BEGIN'].'&nbsp;</td>
			<td width="35%" align="left">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<input id="date_deb_day" name="date_deb_day" maxlenght="2" value="'.date("d").'" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;';
	$token->field("date_deb_day");
	echo				'</td>
						<td>
							<input id="date_deb_month" name="date_deb_month" maxlenght="2" value="'.date("m").'" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;';
	$token->field("date_deb_month");
	echo				'</td>
						<td>
							<input id="date_deb_year" name="date_deb_year" maxlenght="4" value="'.date("Y").'" style="width:30px;background-color:#ebf2ea;"/>';
	$token->field("date_deb_year");
	echo				'</td>
					</tr>
				</table>
			</td>
			<td></td>
		</tr>
		<tr>
			<td width="15%" align="right">'.$_DIMS['cste']['_END'].'&nbsp;</td>
			<td width="35%" align="left">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<input id="date_fin_day" name="date_fin_day" maxlenght="2" value="jj" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;';
	$token->field("date_fin_day");
	echo				'</td>
						<td>
							<input id="date_fin_month" name="date_fin_month" maxlenght="2" value="mm" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;';
	$token->field("date_fin_month");
	echo				'
						</td>
						<td>
							<input id="date_fin_year" name="date_fin_year" maxlenght="4" value="aaaa" style="width:30px;background-color:#ebf2ea;"/>';
	$token->field("date_fin_year");
	echo				'
						</td>
					</tr>
				</table>
			</td>
			<td></td>
		</tr>
		<tr>
			<td width="15%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'&nbsp;</td>
			<td width="35%" align="left">
				<input type="text" id="fonction" name="fonction" style="background-color:#ebf2ea;" value=""/>';
	$token->field("fonction");
	echo				'
			</td>
			<td></td>
		</tr>
		<tr>
			<td width="15%" align="right">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])).'&nbsp;</td>
			<td width="35%" align="left">
				<input type="text" id="departement" name="departement" style="background-color:#ebf2ea;" value=""/>';
	$token->field("departement");
	echo				'
			</td>
			<td></td>
		</tr>
		<tr>
			<td width="15%" align="right">'.$_DIMS['cste']['_DIMS_COMMENTS'].'&nbsp;</td>
			<td width="35%" align="left">
				<textarea id="commentaire" name="commentaire" style="background-color:#ffffff;"></textarea>';
	$token->field("commentaire");
	echo				'
			</td>
			<td></td>
		</tr>
	</table>';
	echo $retour;

}
// on duplique les vues pour vï¿œrification des changements
$_SESSION['dims']['contact_fields_view_old']=$_SESSION['dims']['contact_fields_view'];


/**********************************************************************************************/
// construction des donnees de partage
/**********************************************************************************************/
if($id_from != 0) {
	echo '	<input type="hidden" name="id_from" value="'.$id_from.'">
			<input type="hidden" name="type_to" value="'.$type_to.'">
			<input type="hidden" name="type_from" value="'.$type_from.'">';
	}
	$token->field("id_from",	$id_from);
	$token->field("type_to",	$type_to);
	$token->field("type_from",	$type_from);
}

$readonlyfields=array();

$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
						mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON			mb.id=mf.id_mbfield
			LEFT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object = ".dims_const::_SYSTEM_OBJECT_CONTACT."
			ORDER BY	mc.position, mf.position
			";

$rs_fields=$db->query($sql);
if($db->numrows($rs_fields) > 0) {
?>
<table style="width:100%">
	<tr>
		<td width="width:100%" style="vertical-align:top;">
			<?
			/*
			// on va construire la liste des espaces de travail en collaboration
			$workspace= new workspace();
			$workspace->open($_SESSION['dims']['workspaceid']);
			$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
			$tabsworkspaces=array();
			$partw='';

			if (!empty($lstworkpaces)) {
				echo "<div style=\"width:100%;float:left;\">";

				foreach($lstworkpaces as $i=>$work) {
					if ($partw=='') $partw='tabw_'.$i;
					$tabsworkspaces['tabw_'.$i]['title'] = dims_strcut($work['label'],15);
					$tabsworkspaces['tabw_'.$i]['url'] = "";
					$tabsworkspaces['tabw_'.$i]['icon'] = "./common/img/workspace.png";
					$tabsworkspaces['tabw_'.$i]['width'] = 180;
					$tabsworkspaces['tabw_'.$i]['position'] = 'right';
				}
				echo $skin->create_onglet($tabsworkspaces,$partw,$partw,'0',"onglet");
			}
			*/
			//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CONT_DESCM'],'100%','padding-left:10px;');
			if (!isset($dims_op) || $dims_op!="add_contact")
							if (isset($skin))
				echo $skin->open_simplebloc($_DIMS['cste']['_PROFIL'],'100%', 'font-size:13px;');

			if($contact->fields['inactif'] != 1) {
			?>
			<table width="100%"  style="">
				<tr>
					<?
					if (!empty($lstworkpaces)) {
						?>
						<td align="left">
						<table style="100%;border:1px dotted">
							<tr><td colspan="4"><? echo $_DIMS['cste']['_DIMS_LEGEND'];?></td></tr>
							<tr><td style="width:10%" valign="top"><img src="./common/img/all.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC'];?></td>
							<td style="width:10%" valign="top"><img src="./common/img/share.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_SHARE'];?></td>
							<td style="width:10%" valign="top"><img src="./common/img/users.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_WORKSPACE'];?></td>
							<td style="width:10%" valign="top"><img src="./common/img/user.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_PRIVATE'];?></td></tr>
						</table>
						</td>
					<?
					}
					if (!isset($_SESSION['dims']['crm_newcontact_actionform'])) {
					?>
					<td align="right">
						<input type="submit" style="width:0px;height:0px;display:none;">
						<?
						if (!isset($_SESSION['dims']['crm_newcontact_actionform'])) {
						?>
							<input type="hidden" name="op" value="save_contact">
							<input type="hidden" name="action" value="<?php echo _BUSINESS_TAB_CONTACTSTIERS; ?>">
							<?
								$token->field("op",		"save_contact");
								$token->field("action",	_BUSINESS_TAB_CONTACTSTIERS);
							?>
					<?php
						}
						if(!empty($contact->fields['id']))
							echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:validate_form_ct('".$contact->fields['id']."');","","");
						else {
							if (!empty($contact->fields['id'])) $ctid=$contact->fields['id'];
							else $ctid=0;
							echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:validate_form_ct_new('".$ctid."');","","");
						}
						?>
					</td>
					<?
					}
					?>
				</tr>

			</table>
			<?php
			}

			echo '<div id="vertical_container2" style=\"clear:both;\">';
			// construction des champs dynamiques pour la fiche
			// 04/04/2009

			// construction de la recherche des champs sur le type d'objet
			$categcour=0;
			$replies=array();

			$color="";
			$workspaceenabled=array();
			$ind_categ=-1;
			$ind_field=-1;
			while ($fields = $db->fetchrow($rs_fields)) {
				// increment de l'indice du champ
				$ind_field++;

				// init de la structure des autres valeurs
				$arrayOtherValues=array();

				// test si utilise ou non
				if ($categcour!=$fields['id_metacateg']) {
					$ind_categ++;
					$ind_field=0;
					if ($categcour>0) {

						$workspaceenabled=array();
						echo "</table></div></div>";
					}

					$categcour=$fields['id_metacateg'];
					if(substr($fields['categlabel'], 0,3) == "IDE") {
						$categlabel=$_DIMS['cste']['_DIMS_PERS_IDENTITY'];
					}
					elseif(substr($fields['categlabel'], 0,3) == "COO") {
						$categlabel=$_DIMS['cste']['_DIMS_PERS_COORD'];
					}
					elseif(substr($fields['categlabel'], 0,3) == "INF") {
						$categlabel=$_DIMS['cste']['_DIMS_PERS_INFOG'];
					}
					else
						$categlabel=$fields['categlabel'];

					?>
					<table style="width:100%;height:20px;padding-top:0px; margin-top: 10px;" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" width="35%" valign="top" colspan="2" class="tdcategcrm">
								<?php echo $categlabel; ?>
							</td>
							<td style="width:16%;text-align:left;">
								<?
								if (!empty($lstworkpaces)) {
									?>
								<table align="left" cellspacing="0" cellpadding="0" style="text-align:left;width:100%;">
									<tr>
										<td style="width:32px;text-align:center;" valign="top"><span><img src="./common/img/all.png" title="<? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC'];?>"></span><span><img src="./common/img/share.png"  title="<? echo $_DIMS['cste']['_WORKSPACE'];?>"></span>
										</td>
										<td style="width:32px;text-align:center;" valign="top"><img src="./common/img/users.png"  title="<? echo $_DIMS['cste']['_WORKSPACE'];?>">
										</td>
										<td style="width:32px;text-align:center;" valign="top"><img src="./common/img/user.png"  title="<? echo $_DIMS['cste']['_PRIVATE'];?>">
										</td>
									</tr>
									<tr>
										<td style="text-align:center;">
											<input style="width:32px;" type="radio" id="ch_categ_<? echo $ind_categ;?>" name="ch_categ_<? echo $ind_categ;?>" onclick="javascript:checkUpdateCateg(<? echo $ind_categ;?>)">
										</td>
										<td style="text-align:center;" valign="top">
											<input style="width:32px;" type="radio" id="ch_categ_<? echo $ind_categ;?>" name="ch_categ_<? echo $ind_categ;?>" onclick="javascript:checkUpdateCateg(<? echo $ind_categ;?>)">
										</td>
										<td style="text-align:center;" valign="top">
											<input style="width:32px;" type="radio" id="ch_categ_<? echo $ind_categ;?>" name="ch_categ_<? echo $ind_categ;?>" onclick="javascript:checkUpdateCateg(<? echo $ind_categ;?>)">
										</td>
										<?
											$token->field("ch_categ_".$ind_categ);
										?>
									</tr>
								</table>
								<?
								}
								?>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>

					<div id="div_<?php echo $fields['id']; ?>" style="">
						<table cellspacing="1" style="width:100%;" cellpadding="1">
					<?php
				}

				// test si on peut utiliser le champ ou non
				if (isset($rubgen[$fields['id_metacateg']]['list'][$fields['id']]['use']) && $rubgen[$fields['id_metacateg']]['list'][$fields['id']]['use']>0) {
					if (isset($skin))
						$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
					else
						$color='';

					echo "<tr id=\"trline".$fields['id']."\" class=\"trct\">";
					if ($fields['option_needed']) {
						$oblig=" *";
						// ajout dans structure de filtre
						if (($fields['namefield']!='firstname' && $fields['namefield']!='lastname'	&& $fields['namefield']!='photo') || (($fields['namefield']=='firstname' || $fields['namefield']=='lastname' || $fields['namefield']=='photo') && $contact->fields['id']==$_SESSION['dims']['user']['id_contact'])) {
							$arrayfield_control[]=$fields;
						}
					}
					else $oblig="";

					if ($fields['protected']) {
						if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
						else $namevalue=$fields['name'];
					}
					else {
						$namevalue=$fields['name'];
					}

					echo "<td width=\"15%\" valign=\"middle\" align=\"right\" style=\"padding-top:".$fields['interline']."px;font-size:1em;\">".$namevalue.$oblig."&nbsp;</td>";
					echo "<td width=\"35%\" style=\"padding-top:".$fields['interline']."px;\">";

					// construction du reply eventuel
					$private = false;

					// test si on a un champ vide
					if ($contact->fields[$fields['namefield']]=='') {
						// if mode = partage, par defaut sur workspace au cas ou
						if ($_SESSION['dims']['contact_fields_view'][$fields['id']]==0 && $_SESSION['dims']['contact_fields_mode'][$fields['id']]>0) {
							$_SESSION['dims']['contact_fields_view'][$fields['id']]=1;
							$_SESSION['dims']['contact_fields_view_old'][$fields['id']]=1;
						}
					}

					if (isset($_SESSION['dims']['contact_fields_view'][$fields['id']])) {
						switch ($_SESSION['dims']['contact_fields_view'][$fields['id']]) {
							case 0 : // public
								$replies[$fields['id']] = explode('||',$contact->fields[$fields['namefield']]);
								break;
							case 1 : // workspace
								$replies[$fields['id']] = explode('||',$contactworkspace->fields[$fields['namefield']]);
								break;
							case 2 : // private
								$replies[$fields['id']] = explode('||',$contactuser->fields[$fields['namefield']]);
								break;
						}
					}
					else {
						$replies[$fields['id']] = explode('||',$contact->fields[$fields['namefield']]);
					}

					// verification de la valeur par defaut si nouveau contact
					if ($contact->fields['id']==0 || $contact->fields['id']=='' || ($contact->fields['id']>0 && $replies[$fields['id']][0]=='')) {
						if ($fields['defaultvalue']!='') {
							if ($fields['defaultvalue']=="day()") {
								$replies[$fields['id']][0]=date("d/m/Y");
							}
							else {
								$replies[$fields['id']][0]=$fields['defaultvalue'];
							}
						}
					}

					// verification si description = <nace ou country
					if ($fields['enum']!='') {
						$sql_sa = "SELECT * FROM dims_mod_business_enum WHERE type LIKE :type AND lang = :lang order by libelle";

						$res_sa = $db->query($sql_sa, array(
							':type'	=> dims_sql_filter($fields['enum']),
							':lang' => $_SESSION['dims']['currentlang']
						));
						while($tab = $db->fetchrow($res_sa)) {
							if ($tab['libelle']!="") {
								$code = $tab['code'];
								$elem=array();
								if ($code!="") $elem['value']=$code.' - '.dims_strcut(html_entity_decode(strtolower($tab['libelle'])),100);
								else $elem['value']=dims_strcut(($tab['libelle']),100);
								$elem['key']=$tab['id'];
								$values[]=$elem;
							}
						}
						$fields['values']=$values;
					}

					$colorfield='';
					if (isset($_SESSION['dims']['contact_fields_view'][$fields['id']])) {
						switch ($_SESSION['dims']['contact_fields_view'][$fields['id']]) {
							case 0 : // public
								$colorfield='background-color:#ffffff;';
								break;
							case 1 : // workspace
								$colorfield='background-color:#f3f4ff;';
								break;
							case 2 : // private
								$colorfield='background-color:#ffffff;';
								break;
						}
					}

					if (($fields['namefield']!='firstname' && $fields['namefield']!='lastname'	&& $fields['namefield']!='photo') || (($fields['namefield']=='firstname' || $fields['namefield']=='lastname' || $fields['namefield']=='photo') && $contact->fields['id']==$_SESSION['dims']['user']['id_contact']) || $url_action == _BUSINESS_TAB_CONTACTSTIERS || !$isuseraccount) {
						if ($fields['namefield'] == 'country'){
							?>
							<select style="width: 260px;" name="id_country" class="crm_country" id="crm_country" data-placeholder="<?php echo $_SESSION['cste']['_DIMS_SELECT_COUNTRY']; ?>">
								<? $token->field("id_country"); ?>
								<option value=""></option>
								<?php
								require_once DIMS_APP_PATH."modules/system/class_country.php";
								if ($contact->fields['id_country'] == 0 || $contact->fields['id_country'] == ''){
									$contact->updateIdCountry();
								}
								$sel_Country = null;
								foreach (country::getAllCountries() as $country) {
									$sel = '';
									if ($country->fields['id'] == $contact->fields['id_country']){
										$sel = "selected=true";
										$sel_Country = $country;
									}
									echo '<option value="'.$country->fields['id'].'"'.$sel.'>'.stripslashes($country->fields['printable_name']).'</option>';
								}
								?>
							</select>
							<?
						}else
							include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';
					}
					else {

						$optmp=$op;
						$op='display';

						// read only fields
						$readonlyfields[$fields['id']]=$fields['id'];

						if ($fields['namefield']!='photo' && $contact->fields['id']>0) {
							//on regarde si le contact est un dims_user ou non
							$sqlv = "SELECT id FROM dims_user WHERE id_contact = :idcontact ";
							$resv = $db->query($sqlv, array(
								':idcontact' => $contact->fields['id']
							));
							if($db->numrows($resv) == 0) {
								$op=$optmp;
							}
							if ($fields['namefield'] == 'country'){
								?>
								<select style="width: 260px;" name="id_country" class="crm_country" id="crm_country" data-placeholder="<?php echo $_SESSION['cste']['_DIMS_SELECT_COUNTRY']; ?>">
									<? $token->field("id_country"); ?>
									<option value=""></option>
									<?php
									require_once DIMS_APP_PATH."modules/system/class_country.php";
									if ($contact->fields['id_country'] == 0 || $contact->fields['id_country'] == ''){
										$contact->updateIdCountry();
									}
									$sel_Country = null;
									foreach (country::getAllCountries() as $country) {
										$sel = '';
										if ($country->fields['id'] == $contact->fields['id_country']){
											$sel = "selected=true";
											$sel_Country = $country;
										}
										echo '<option value="'.$country->fields['id'].'"'.$sel.'>'.stripslashes($country->fields['printable_name']).'</option>';
									}
									?>
								</select>
								<?
							}else
								include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';
						}
						$op=$optmp;
					}

					/**********************************************************/
					// construction des usages du champ
					/**********************************************************/
					echo "</td><td style=\"width:16%\">";

					if (!empty($lstworkpaces) && $fields['namefield']!='firstname' && $fields['namefield']!='lastname'	&& $fields['namefield']!='photo') {
						echo "<div id=\"cmdshare".$fields['id']."\" style=\"margin:0px;width:100%\">";
						echo "<table style=\"width:100%;\" cellpadding=\"0\" cellspacing=\"0\"><tr>";
						$share=false;

						// test si value vide, alors on met le flag a 0 pour chgt direct
						if (!is_array($replies[$fields['id']]) && $replies[$fields['id']] =='' || (is_array($replies[$fields['id']]) && $replies[$fields['id']][0]=='')) {
							echo "<script type=\"text/javascript\">arrayModif[".$ind_categ."][".$ind_field."][1]=0;</script>";
							$value_empty=true;
						}
						else {
							$value_empty=false;
						}
						// affichage des vues eventuels sur les donnees
						if (isset($_SESSION['dims']['contact_fields_view'][$fields['id']])) {
							for ($c=0;$c<=2;$c++) {
								$link="";
								$sel=($_SESSION['dims']['contact_fields_view'][$fields['id']]==$c);
								//if ($c==1 && $sel!="") $share=true;
								switch ($c) {
									case 0 : // public
										if ($sel) {
											//$link="javascript:updateValueShare(".$fields['id'].",0);";
											if ($fields['mode']==0) $src="./common/img/all.png";
											else $src="./common/img/share.png";
										}
										else {
											if (!is_array($replies[$fields['id']]) && $replies[$fields['id']] !='' || (is_array($replies[$fields['id']]) && $replies[$fields['id']][0]!='')) {
												$src="./common/img/bullet_sel.png";
												$link="javascript:updateValueShare(".$fields['id'].",0);";
											}
											else {
												$src="./common/img/bullet.png";
											}
										}
										$label=$_DIMS['cste']['_DIMS_LABEL_PUBLIC'];
										break;

									case 1 : // workspace
										$label=$_DIMS['cste']['_WORKSPACE'];
										if ($sel) {
											if ($value_empty) $src="./common/img/check.png";
											else $src="./common/img/checkdo.png";
										}
										else {
											if ($_SESSION['dims']['contact_fields_view'][$fields['id']]==0) {
												$src="./common/img/bullet.png";
											}
											else {
												if (!is_array($replies[$fields['id']]) && $replies[$fields['id']] !='' || (is_array($replies[$fields['id']]) && $replies[$fields['id']][0]!='')) {
													$src="./common/img/bullet_sel.png";
													$link="javascript:updateValueShare(".$fields['id'].",1);";
												}
												else {
													$src="./common/img/bullet.png";
												}
											}
										}
										$colorfield='background-color:#dbdeef;';
										break;

									case 2 : // private
										$label=$_DIMS['cste']['_PRIVATE'];
										if ($sel) {
											if ($value_empty) $src="./common/img/check.png";
											else $src="./common/img/checkdo.png";
										}
										else {
											if ($_SESSION['dims']['contact_fields_view'][$fields['id']]==0) {
												$src="./common/img/bullet.png";
											}
											else {
												if (!is_array($replies[$fields['id']]) && $replies[$fields['id']] !='' || (is_array($replies[$fields['id']]) && $replies[$fields['id']][0]!='')) {
													$src="./common/img/bullet_sel.png";
													$link="javascript:updateValueShare(".$fields['id'].",2);";
												}
												else {
													$src="./common/img/bullet.png";
												}
											}
										}

										$colorfield='background-color:#efdedb;';
										break;
								}

								//echo "<span style=\"float:left;width:40px;height:16px;text-align:center;margin:0px;".$sel."\">";
								echo "<td align=\"center\" width=\"33%\">";
								if ($link!='') echo "<a href=\"javascript:void(0);\" onclick=\"".$link."\">";
								echo "<img id=\"view".$fields['id']."\" title=\"".$label."\" name=\"view".$fields['id']."\" border=\"0\" src=\"".$src."\"/>";
								$token->field("view".$fields['id']);
								if ($link!='') echo "</a>";
								//echo "</span>";
								echo "</td>";

							}

						}

						echo "</tr></table>";
						echo "</div>";
					}
					else {
						if ($fields['namefield']=='lastname') {
							if ($contact->fields['id']>0) {
								$sql = "SELECT id FROM dims_user WHERE id_contact = :idcontact ";
								$res = $db->query($sql, array(
									':idcontact' => $contact->fields['id']
								));
								if($db->numrows($res) ==1)
									echo "";
							}
						}
						else {
							echo "<span style=\"float:left;width:15%;height:16px;\">&nbsp;</span>";
						}
					}

					echo "</td>";

					// on regarde si on a des donnï¿œes partagees et que l'on ne peut les voir
					if (!$shareactive && $nbsharefield>0) {
						if ($ind_field==0) {
							echo "<td style=\"background:#FFFFFF;width:34%;text-align:center;\" rowspan=\"".($arrayCateg[$ind_categ]*2)."\">";
							// affichage de la demande d'acception du partage
							echo $_DIMS['cste']['_DIMS_CONTACT_ACCEPT_SHARE'];
							echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],"./common/img/publish.png","javascript:document.location.href='$scriptenv?cat="._BUSINESS_CAT_CONTACT."&op=validpublicshare&action="._BUSINESS_TAB_CONTACT_FORM."&id_ct=".$contact->fields['id']."&part="._BUSINESS_TAB_CONTACT_IDENTITE."';","","");
							echo "</td>";
						}
					}
					else {
						echo "<td style=\"width:34%;text-align:left;\">";
						/***********************************************************/
						// test si besoin d'afficher une autre valeur
						/**********************************************************/
						switch($_SESSION['dims']['contact_fields_view'][$fields['id']]) {
							case 0:
								if ($contactworkspace->fields[$fields['namefield']]!='' && $replies[$fields['id']][0]!=$contactworkspace->fields[$fields['namefield']]) {
									$elem= array();
									$elem['icon']='users.png';
									$elem['label']=$_DIMS['cste']['_WORKSPACE'];
									$elem['value']=$contactworkspace->fields[$fields['namefield']];
									$elem['mode']=1;
									$elem['id']=$contactworkspace->fields['id_layer'];
									$arrayOtherValues[]=$elem;
								}
								elseif ($contactuser->fields[$fields['namefield']]!='' && $replies[$fields['id']][0]!=$contactuser->fields[$fields['namefield']]) {
									$elem= array();
									$elem['icon']='user.png';
									$elem['label']=$_DIMS['cste']['_DIMS_LABEL_USER'];
									$elem['value']=$contactuser->fields[$fields['namefield']];
									$elem['mode']=2;
									$elem['id']=$contactuser->fields['id_layer'];
									$arrayOtherValues[]=$elem;
								}
								break;

							case 1:

								if ($contactuser->fields[$fields['namefield']]!='' && $replies[$fields['id']][0]!=$contactuser->fields[$fields['namefield']]) {
									$elem= array();
									$elem['icon']='user.png';
									$elem['label']=$_DIMS['cste']['_DIMS_LABEL_USER'];
									$elem['value']=$contactuser->fields[$fields['namefield']];
									$elem['mode']=2;
									$elem['id']=$contactuser->fields['id_layer'];
									$arrayOtherValues[]=$elem;
								}
								elseif ($contact->fields[$fields['namefield']]!='' && $replies[$fields['id']][0]!=$contact->fields[$fields['namefield']]) {
									if ($_SESSION['dims']['contact_fields_mode'][$fields['id']]==0) {
										$icon_field='all.png';
										$label=$_DIMS['cste']['_DIMS_LABEL_PUBLIC'];
									}
									else {
										$icon_field='share.png';
										$label=$_DIMS['cste']['_WORKSPACE'];
									}

									$elem= array();
									$elem['icon']=$icon_field;
									$elem['label']=$label;
									$elem['value']=$contact->fields[$fields['namefield']];
									$elem['mode']=0;
									$elem['id']=0;
									$arrayOtherValues[]=$elem;
								}
								break;

							case 2:
								if ($contactworkspace->fields[$fields['namefield']]!='' && $replies[$fields['id']][0]!=$contactworkspace->fields[$fields['namefield']]) {
									$elem= array();
									$elem['icon']='users.png';
									$elem['label']=$_DIMS['cste']['_WORKSPACE'];
									$elem['value']=$contactworkspace->fields[$fields['namefield']];
									$elem['mode']=1;
									$elem['id']=$contactworkspace->fields['id_layer'];
									$arrayOtherValues[]=$elem;
								}
								elseif ($contact->fields[$fields['namefield']]!='' && $replies[$fields['id']][0]!=$contact->fields[$fields['namefield']]) {
									if ($_SESSION['dims']['contact_fields_mode'][$fields['id']]==0) {
										$icon_field='all.png';
										$label=$_DIMS['cste']['_DIMS_LABEL_PUBLIC'];
									}
									else {
										$icon_field='share.png';
										$label=$_DIMS['cste']['_WORKSPACE'];
									}

									$replies[$fields['id']][0]=
									$elem= array();
									$elem['icon']=$icon_field;
									$elem['label']=$label;
									$elem['value']=$contact->fields[$fields['namefield']];
									$elem['mode']=0;
									$elem['id']=0;
									$arrayOtherValues[]=$elem;
								}
								break;
						}

						// on alimente les autres partages eventuels Personne a personne
						// on affiche eventuellement
						if (!empty($arrayOtherValues)) {
							echo "<a href=\"javascript:void(0);\" onclick=\"dims_switchdisplay('share".$fields['id']."');\" style=\"border:0px;\"><img src=\"./common/img/data_view.png\" alt=\"\"></a>";
						}
					}

					// on mettra apres le mode duel
					echo "</td></tr>";

					echo "<tr><td colspan=\"4\">";
					// construction du tableau de valeurs diffï¿œrentes et/ou partagï¿œes
					if (!empty($arrayOtherValues)) {
						// ligne sup

						// on cree le div
						echo "<div style=\"width:80%;margin: 0 auto;display:none;\" id=\"share".$fields['id']."\"><table style=\"width:100%;border:#BEBEBE 1px solid;\" cellspacing=\"1\" cellpadding=\"3\">";
						$styletd=1;
						foreach ($arrayOtherValues as $kj=>$elem) {
							$styletd=($styletd==1) ? 2 : 1;
							echo "<tr>";
							// affichage du nom (espace ou personne)
							switch ($elem['mode']) {
								case 0:
										// public ou partage
										$name=$_DIMS['cste']['_WORKSPACE'];
									break;
								case 1 : //workspace
										$works = $dims->getAllWorkspaces();
										$work= $works[$elem['id']];
										$name=$work['label'];
									break;
								case 2 : //user
									if (!isset($arrayUsers[$elem['id']])) {
										$usr= new user();
										$usr->open($elem['id']);
										$arrayUsers[$elem['id']]=$usr->fields['firstname']." ".$usr->fields['lastname'];
										$name=$arrayUsers[$elem['id']];
									}
									break;
							}
							echo "<td class=\"tdl".$styletd."\" style=\"width:2%;\"><img src=\"./common/img/".$elem['icon']."\" alt=\"".$elem['label']."\"></td>";
							echo "<td class=\"tdl".$styletd."\" style=\"width:38%;\">&nbsp;".$name."</td>";
							echo "<td class=\"tdl".$styletd."\" style=\"width:60%;\">".$elem['value']."</input></td>";
						}
						echo "</table>";
						echo "</div>";
					}
					echo "</td></tr>";
				}
				else {
					/*
					if(isset($rubgen[$fields['id_metacateg']]['list'][$fields['id']]['enabled'])) {
						foreach ($rubgen[$fields['id_metacateg']]['list'][$fields['id']]['enabled'] as $workid) {
							if (!isset($workspaceenabled[$workid])) $workspaceenabled[$workid]=1;
							else $workspaceenabled[$workid]++;
						}
					}*/
				}
			}
			?>
			<tr>
				<td colspan="2"><?
					if (!empty($lstworkpaces)) {
						?>

						<table style="100%;border:1px dotted">
							<tr><td colspan="4"><? echo $_DIMS['cste']['_DIMS_LEGEND'];?></td></tr>
							<tr><td style="width:10%" valign="top"><img src="./common/img/all.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC'];?></td>
							<td style="width:10%" valign="top"><img src="./common/img/share.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_WORKSPACE'];?></td>
							<td style="width:10%" valign="top"><img src="./common/img/users.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_WORKSPACE'];?></td>
							<td style="width:10%" valign="top"><img src="./common/img/user.png"></td><td style="text-align:left;"><? echo $_DIMS['cste']['_PRIVATE'];?></td></tr>
						</table>
					<?
					}
					?></td>
				<td colspan="2" align="right">
					<?php

										if (!isset($op) || $op!="display") {
						if(!empty($contact->fields['id']))
							echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:validate_form_ct('".$contact->fields['id']."');","","");
						else {
							if (!empty($contact->fields['id'])) $ctid=$contact->fields['id'];
							else $ctid=0;
							echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:validate_form_ct_new('".$ctid."');","","");
						}
										}
					?>
				</td>
			</tr>
			<?
			// on ferme le dernier block
			if ($categcour>0) {
				// affichage des infos des champs dispos par les autres workspaces
				if (!empty($workspaceenabled)) {
					foreach ($workspaceenabled as $workid => $cpte) {
						echo "<tr><td colspan=\"2\">".$lstworkspace[$workid]['label']." ".$_DIMS['cste']['_DIMS_LABEL_LFB_POSSEDE']." ".$cpte." ".$_DIMS['cste']['_DIMS_LABEL_RULEFIELD_MIN']."</td></tr>";
					}
				}
				echo "</table></div></div></div>";
			}
			?>
			</div>
			<?php
			if (!isset($dims_op) || $dims_op!="add_contact")
							if (isset($skin))
								echo $skin->close_simplebloc();
			?>
		</td>
	</tr>
<?php
	}
	if($contact->fields['inactif'] != 1) {
?>
	<tr>
		<td colspan="2">
			<table width="100%">
				<tr>
					<td align="center" style="padding-right: 20px;">

					</td>
				</tr>
			</table>
			<?
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			</form>
		</td>
	</tr>
</table>
<?php
	}
?>

<script type="text/javascript">
	$(document).ready(function(){
		$("select.crm_country")
			.chosen({no_results_text: "No results matched"});
			/*.change(function(){
				if($(this).val() != '') {
					$('#city_activity').removeAttr('disabled');
				}
				else {
					$('#city_activity').attr('disabled','disabled');
				}
				refreshCityOfCountry($(this).val(),'city_activity');
			});*/
	});
	<?
	if (!empty($tabfiltrefield)) {
	?>
	document.getElementById('field<? echo $tabfiltrefield[0]['id']; ?>').focus();
	<?
	}
	echo "function validate_dyn_field() {";

// on genere le test
		echo "if (";

		if (!isset($readonlyfields[$tabfiltrefield[0]['id']])) {
	?>
		dims_validatefield('<? echo addslashes($tabfiltrefield[0]['label']); ?>',document.getElementById("field<? echo $tabfiltrefield[0]['id']; ?>"), 'string') &&

			dims_validatefield('<? echo addslashes($tabfiltrefield[1]['label']); ?>',document.getElementById("field<? echo $tabfiltrefield[1]['id']; ?>"), 'string')
	<?
		}
		else {
			echo "true ";
		}

		$ind=2;
		if (sizeof($arrayfield_control)>0) {

			foreach ($arrayfield_control as $ind=>$f) {

				if (isset($_DIMS['cste'][$f['titlefield']])) $name=$_DIMS['cste'][$f['titlefield']];
				else $name=$f['name'];

					if ($ind>=0) echo " && ";
					if ($f['type']=='checkbox' || $f['type']=='radio') {
						if ($f['type']=='checkbox') $f['type']='checked';
						echo "dims_validatefield('".addslashes($name)."','field".$f['id']."[]', '".$f['type']."')";
					}
					else {
						echo "dims_validatefield('".addslashes($name)."',document.getElementById('field".$f['id']."'), '".$f['type']."')";
					}
			}

			echo ") { return true ;}";
		}
		else {
			echo ") return true;";
		}
echo "}";
	?>
</script>
