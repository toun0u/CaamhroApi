<?
$disp_t = dims_load_securvalue('disp', dims_const::_DIMS_CHAR_INPUT, true, false);

if ($disp_t!='' && $disp_t!='pers') $obj=dims_const::_SYSTEM_OBJECT_TIERS;
else $obj=dims_const::_SYSTEM_OBJECT_CONTACT;

$initsearch = dims_load_securvalue('initsearch', dims_const::_DIMS_NUM_INPUT, true, false);

if ($initsearch) {
	switch($obj) {
		case dims_const::_SYSTEM_OBJECT_CONTACT:
			unset($_SESSION['business']['search_ct']);
			unset($_SESSION['business']['search_ent']);
			unset($_SESSION['business']['search_lkent']);
			unset($_SESSION['business']['search_ct_sql']);

			break;
		case dims_const::_SYSTEM_OBJECT_TIERS:
			unset($_SESSION['business']['ent_search_ent']);
			unset($_SESSION['business']['ent_search_ct']);
			unset($_SESSION['business']['ent_search_lkct']);
			unset($_SESSION['business']['search_ent_sql']);
			break;
	}
}

$enabledworkspace = $dims->getAdminWorkspaces();
// construction de la liste des usages
$sql= " select 		mu.*,
					mf.id_metacateg,
					w.label as labelworkspace
		from 		dims_mod_business_meta_use as mu
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=mu.id_metafield
		AND			mf.used=1
		AND			mu.id_object= :obj
		LEFT JOIN	dims_workspace as w
		ON			w.id=mu.id_workspace";

$res=$db->query($sql, array(
	':obj' => $obj
));
if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {

		if ($f['sharemode']==1) {
			// on doit verifier si le currentworkspace fait parti de la liste
			foreach($enabledworkspace as $id_wkspc => $inf_wkspce) {
				if ($id_wkspc == $f['id_workspace']) {
					// on est dans la selection
					$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=1;
				}
			}
			// on stocke les workspaces qui l'utilisent
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['enabled'][$f['id_workspace']]=$f['id_workspace'];
		}
		else {
			// on est a 2 on met le flag a 2
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=2;
		}

		// construciton de la liste des workspaces
		//if (!isset($lstworkspace[$f['id_workspace']])) $lstworkspace[$f['id_workspace']]=$f['labelworkspace'];
	}
}

//gestion de l'affichage des blocs
$disp ='';
$disp_ent = '';
$include_top='';
$include_bot='';

if($disp_t != '') {
	switch($disp_t) {
		default:
		case "pers" :
			$disp ='block';
			$disp_ent = 'none';
			if ($workspace->fields['contact_activeent']==1) {
				$include_top = 'modules/system/crm_public_ent_search_formct.php';
			}

			$include_bot = 'modules/system/crm_public_contact_search_formct.php';
			$focus="ct_lastname";
			break;
		case "ent" :
			$disp ='none';
			$disp_ent = 'block';
			$include_top = 'modules/system/crm_public_contact_search_formct.php';
			if ($workspace->fields['contact_activeent']==1) {
				$include_bot = 'modules/system/crm_public_ent_search_formct.php';
			}
			$focus="ent_intitule";
			break;
	}
}
else {
	switch($op) {
		default:
		case "exec_search" :
			$disp ='block';
			$disp_ent = 'none';
			if ($workspace->fields['contact_activeent']==1) {
				$include_top = 'modules/system/crm_public_ent_search_formct.php';
			}
			$include_bot = 'modules/system/crm_public_contact_search_formct.php';
			$focus="ct_lastname";
			break;
		case "exec_search_ent" :
			$disp ='none';
			$disp_ent = 'block';
			$include_top = 'modules/system/crm_public_contact_search_formct.php';
			if ($workspace->fields['contact_activeent']==1) {
				$include_bot = 'modules/system/crm_public_ent_search_formct.php';
			}
			$focus="ent_intitule";
			break;
	}
}

// init control structure,
$arrayfield_control=array();
?>
<table width="100%">
    <tr>
        <td width="100%" align="center" style="vertical-align:top;">
			<?php
                        if (file_exists($include_top)) {
                            include($include_top);
                        }
                        ?>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center" style="vertical-align:top;">
			<?php
                        include($include_bot);
                        ?>
		</td>
	</tr>
</table>

<script language="JavaScript" type="text/JavaScript">
<?
echo "var timersearch;";

if ($obj==dims_const::_SYSTEM_OBJECT_CONTACT) {
	$namefctvalid="verif_ct";
	$namefctdisabled="verif_ent";
	$nameform="form_search_ct";
}
else {
	$namefctvalid="verif_ent";
	$namefctdisabled="verif_ct";
	$nameform="ent_search_ent";
}

echo "function execSearchCt() {clearTimeout(timersearch);timersearch = setTimeout(\"searchExecuteCt()\", 400);}

function searchExecuteCt() {
	document.".$nameform.".submit();}";

echo "function ".$namefctdisabled."() {} ";
echo "function ".$namefctvalid."() {";
// construction de la liste des champs � filtrer
foreach ($arrayfield_control as $f) {
	echo "var ".$f."=document.getElementById(\"ct_".$f."\").value;";
}

// ajout du test pour la recherche de personne liee a une entreprise
if ($workspace->fields['contact_activeent']==1) {
	echo "var ent_n = document.getElementById(\"ent_intitule\").value;";
}

// on genere le test
echo "if (";
foreach ($arrayfield_control as $ind=>$f) {
	if ($ind>0) echo " && ";
	echo $f." == ''";
}
if(count($arrayfield_control)) echo ' && ';
if ($workspace->fields['contact_activeent']==1) {
	echo " ent_n == ''";
}

echo ") { alert(\"".$_DIMS['cste']['_DIMS_LABEL_ERROR_SCH']."\"); return false; } else { document.".$nameform.".submit(); }";

echo "}";
?>
    function affiche_div(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}

	function exportSearchPers() {
		dims_xmlhttprequest("admin.php", "op=exportsearchpers", true,'');
	}

	function delete_search(type) {
		//dims_xmlhttprequest("admin.php", "op=delete_search_val&type="+type, true,'');
		if(type=="pers") {
			document.location.href="admin.php?cat="+<?php echo _BUSINESS_CAT_CONTACT ?>+"&action="+<?php echo _BUSINESS_TAB_CONTACTSSEEK ?>+"&part"+<?php echo _BUSINESS_TAB_CONTACTSSEEK ?>+"=&disp=pers&initsearch=1";
		}
		else if(type=="ent") {
			document.location.href="admin.php?cat="+<?php echo _BUSINESS_CAT_CONTACT ?>+"&action="+<?php echo _BUSINESS_TAB_CONTACTSSEEK ?>+"&part"+<?php echo _BUSINESS_TAB_CONTACTSSEEK ?>+"=&disp=ent&initsearch=1";
		}
	}

$('#<? echo $focus;?>').focus();
</script>
