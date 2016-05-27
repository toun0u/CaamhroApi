<script type="text/javascript">
function displayMetaShareInfo(id_metafield,id_object) {
	dims_showcenteredpopup("",600,350,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_ADMIN; ?>&op=editmetause&metafield_id='+id_metafield+'&object_id='+id_object,'','dims_popup');
}

function displayMetaShareInfoDoublon(id_metafield,id_object) {
	dims_showcenteredpopup("",900,650,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_ADMIN; ?>&op=editmetausedoublon&metafield_id='+id_metafield+'&object_id='+id_object,'','dims_popup');
}


function refreshListWork(display) {
	var elem=document.getElementById("lstsharemetaworkspace");
	if (display) {
		elem.style.visibility="visible";
		elem.style.display="block";
	}
	else {
		elem.style.visibility="hidden";
		elem.style.display="none";
	}
}
function importExcelcontact(){
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=import_excel","","import_excel_contact");
	document.import_contact.submit();
}
function importExceltiers(){
	dims_xmlhttprequest_todiv("admin-light.php","dims_op=import_excel","","import_excel_tiers");
	document.import_tiers.submit();
}

</script>
<?
require_once(DIMS_APP_PATH . "/modules/system/class_metafield.php");
require_once(DIMS_APP_PATH . "/modules/system/class_metafielduse.php");

unset($_SESSION['dims']['importform']);

echo "<div style=\"width:100%;background:#FFFFFF;display:block;float:left;\">";

// // import fichier excel
// echo	"<div style=\"width:60%;display:block;float:left;text-align:right;\">" ;
// if ($object_id==dims_const::_SYSTEM_OBJECT_CONTACT)
// 	$id_form = "contact";
// else	$id_form = "tiers";

// echo		'<form name="import_'.$id_form.'" id="import_'.$id_form.'" style="margin:0;" action="'.$scriptenv.'?op=import_'.$id_form.'2&object_id='.$object_id.'" method="post" enctype="multipart/form-data">';

// // Sécurisation du formulaire par token
// require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
// $token = new FormToken\TokenField;
// $token->field("file_import_$id_form");
// $tokenHTML = $token->generate();
// echo $tokenHTML;

// echo		'<div class="dims_form" style="float:left;width:85%;">';
// echo				'<label>'.$_DIMS['cste']['_IMPORT_DOWNLOAD_FILE'].'</label>';
// echo				'<span style="text-align:left">';
// echo					"<input type=\"file\" name=\"file_import_$id_form\" class=\"text\"	tabindex=\"17\">";
// echo				'</span>';
// echo		'</div>';
// echo		'<div class="dims_form" style="float:left;width:100%;">';
// echo			'<div style="clear:both;text-align:right;padding:0px;height:40px;padding-top:10px;" id="import_excel_'.$id_form.'">';
// echo				dims_create_button($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'],"./common/img/save.gif",'javascript:importExcel'.$id_form.'();',"","");
// echo			'</div>';
// echo		'</div>';
// echo		'</form>';
// echo	"</div>";

// echo	"<div style=\"display:block;float:left;text-align:right;\">".dims_create_button($_DIMS['cste']['_PREVIEW'],'./common/img/view.png','javascript:document.location.href=\''.$scriptenv.'?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ADMIN.'&op=display_metafield&object_id='.$object_id.'\'','display','javascript:void(0);')."</div>";
echo	"<div style=\"display:block;float:left;text-align:right;\">".dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADDFIELD'],'./common/img/add.gif','javascript:document.location.href=\''.$scriptenv.'?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ADMIN.'&op=add_metafield&object_id='.$object_id.'\'','admin','javascript:void(0);')."</div>";
// if ($object_id==dims_const::_SYSTEM_OBJECT_CONTACT)
// 		echo	"<div style=\"width:20%;display:block;float:left;text-align:right;\">".dims_create_button($_DIMS['cste']['_PROFIL'].'&nbsp;','./common/img/add.gif','javascript:document.location.href=\''.$scriptenv.'?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ADMIN.'&op=gest_profil\'','admin','javascript:void(0);')."</div>";
echo "</div>";

// recherche de la premiere rubrique
$id_firstcateg = 0;
$res=$db->query("select id from dims_mod_business_meta_categ where admin=1 order by position");
if ($db->numrows($res)>0) {
	if ($f=$db->fetchrow($res)) {
		$id_firstcateg=$f['id'];
	}
}

// structure permettant de bloquer la suppression de ces champs
$elemblock=array();

// verification de la pr�sence des champs firstname et lastname pour contact, idem pour entreprise
if ($object_id==dims_const::_SYSTEM_OBJECT_CONTACT) {
	$sql ="select		distinct id_mbfield,dims_mb_field.id,dims_mb_field.name,
						dims_mb_field.label,dims_mb_field.protected,dims_mb_field.indexed
			from		dims_mb_field
			LEFT join	dims_mod_business_meta_field
			on			dims_mb_field.id=dims_mod_business_meta_field.id_mbfield
			INNER JOIN	dims_mb_table as t on t.id = dims_mb_field.id_table
			where		t.name='dims_mod_business_contact' and (dims_mb_field.name= 'firstname' or dims_mb_field.name ='lastname' )";

	$res=$db->query($sql);

	if ($db->numrows($res)>0) {
		$pos=1;
		while ($f=$db->fetchrow($res)) {
			if ($f['id_mbfield']=="" || is_null($f['id_mbfield'])) {
				// on a pas de champ dynamique correspondant, on en cr�� un
				$metafield = new metafield();
				$metafield->fields['id_mbfield']=$f['id'];
				$metafield->fields['name']=$f['name'];
				$metafield->fields['id_object'] = $object_id;
				$metafield->fields['option_needed'] = 1;
				$metafield->fields['description'] = '';
				$metafield->fields['type'] = 'text';
				$metafield->fields['format'] = 'string';
				$metafield->fields['fieldname'] = 0;
				$metafield->fields['cols'] = 1;
				$metafield->fields['used'] = 1;
				$id_metacateg=$id_firstcateg;
				$metafield->fields['id_metacateg'] = $id_metacateg;
				$select = "SELECT max(position) as maxpos from dims_mod_business_meta_field where id_object = :idobject and id_metacateg= :idmetacateg ";

				$resu=$db->query($select, array(
					':idobject' 	=> $object_id,
					':idmetacateg' 	=> $id_metacateg
				));
				$fields = $db->fetchrow($resu);
				$maxpos = $fields['maxpos'];

				if (!is_numeric($maxpos) || $maxpos==0) $maxpos = 0;
				// on a maintenant le max
				$metafield->fields['position']=$maxpos+1;
				$metafield->save();

				// on ajout l'usage par d�faut � l'ensemble
				$metafielduse = new metafielduse();
				$metafielduse->fields['id_metafield']=$metafield->fields['id'];
				$metafielduse->fields['id_object']=$metafield->fields['id_object'];
				$metafielduse->fields['id_workspace']=0;
				$metafielduse->fields['sharemode']=2;
				$metafielduse->save();

				$pos++;
			}
			else {
				$elemblock[$f['id_mbfield']]=$f['id_mbfield'];
			}
		}
	}
}
else {
	// entreprise
	$sql ="select		distinct id_mbfield,dims_mb_field.id,dims_mb_field.name,
						dims_mb_field.label,dims_mb_field.protected,dims_mb_field.indexed
			from		dims_mb_field
			LEFT join	dims_mod_business_meta_field
			on			dims_mb_field.id=dims_mod_business_meta_field.id_mbfield
			INNER JOIN	dims_mb_table as t on t.id = dims_mb_field.id_table
			where		t.name='dims_mod_business_tiers' and dims_mb_field.name= 'intitule'";

	$res=$db->query($sql);

	if ($db->numrows($res)>0) {
		$pos=1;
		while ($f=$db->fetchrow($res)) {
			if ($f['id_mbfield']=="" || is_null($f['id_mbfield'])) {
				// on a pas de champ dynamique correspondant, on en cr�� un
				$metafield = new metafield();
				$metafield->fields['id_mbfield']=$f['id'];
				$metafield->fields['name']=$f['name'];
				$metafield->fields['id_object'] = $object_id;
				$metafield->fields['option_needed'] = 1;
				$metafield->fields['description'] = '';
				$metafield->fields['type'] = 'text';
				$metafield->fields['format'] = 'string';
				$metafield->fields['fieldname'] = 0;
				$metafield->fields['cols'] = 1;
				$metafield->fields['used'] = 1;
				$id_metacateg=$id_firstcateg;
				$metafield->fields['id_metacateg'] = $id_metacateg;
				$select = "SELECT max(position) as maxpos from dims_mod_business_meta_field where id_object = :idobject and id_metacateg= :idmetacateg ";

				$resu=$db->query($select, array(
					':idobject' 	=> $object_id,
					':idmetacateg' 	=> $id_metacateg
				));
				$fields = $db->fetchrow($resu);
				$maxpos = $fields['maxpos'];

				if (!is_numeric($maxpos) || $maxpos==0) $maxpos = 0;
				// on a maintenant le max
				$metafield->fields['position']=$maxpos+1;
				$metafield->save();

				// on ajout l'usage par d�faut � l'ensemble
				$metafielduse = new metafielduse();
				$metafielduse->fields['id_metafield']=$metafield->fields['id'];
				$metafielduse->fields['id_object']=$metafield->fields['id_object'];
				$metafielduse->fields['id_workspace']=0;
				$metafielduse->fields['sharemode']=2;
				$metafielduse->save();

				$pos++;
			}
			else {
				$elemblock[$f['id_mbfield']]=$f['id_mbfield'];
			}
		}
	}
}

// construction de la liste des champs mbfields deja utilises
$mbfields_used = array();
$sql ="select		distinct id_mbfield,
					dims_mb_field.label,dims_mb_field.protected,dims_mb_field.indexed
		from		dims_mod_business_meta_field
		inner join	dims_mb_field
		on			dims_mb_field.id=dims_mod_business_meta_field.id_mbfield
		and			dims_mod_business_meta_field.id_object = :idobject ";

//echo $sql;
$res=$db->query($sql, array(
	':idobject' => $object_id
));
if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$mbfields_used[$f['id_mbfield']]=$f;
	}
}

// verifie si un element est s�lectionn� ou non
$find=false;
?>
<div style="clear:left;width:100%;background:#FFFFFF">
	<table cellpadding="2" cellspacing="1" style="width:100%;bgcolor:#FFFFFF;">
		<?
		$categcour=0;
		$sql =	"
				SELECT		distinct mf.*,mc.label as categlabel,
							count(mu.id) as cpte
				FROM		dims_mod_business_meta_field as mf
				LEFT JOIN	dims_mod_business_meta_categ as mc
				ON			mf.id_metacateg=mc.id
				LEFT JOIN	dims_mod_business_meta_use as mu
				ON			mu.id_metafield=mf.id and mu.id_object = :idobject
				WHERE		mf.id_object = :idobject
				GROUP BY	mf.id
				ORDER BY	mc.position, mf.position
				";

		$rs_fields = $db->query($sql, array(
			':idobject' => $object_id
		));

		while ($fields = $db->fetchrow($rs_fields)) {
			if ($categcour!=$fields['id_metacateg']) {
				$posit=1;
				echo "<tr><td colspan=\"9\" style=\"font-weight:bold;\">".$fields['categlabel']."</td></tr>";
				echo "
					<tr bgcolor=\"".(isset($skin->values['bgline2'])?$skin->values['bgline2']:"")."\">
					<td class=\"title\" align=\"center\" colspan=\"2\">".$_DIMS['cste']['_POSITION']."</td>
					<td class=\"title\">".$_DIMS['cste']['_BUSINESS_FIELD_NAME']."</td>
					<td class=\"title\">".$_DIMS['cste']['_TYPE']."</td>
					<td class=\"title\">". $_DIMS['cste']['_DIMS_USE_MODE']."</td>
					<td width=\"30px\" class=\"title\">". $_DIMS['cste']['_SYSTEM_LABELICON_INDEX']."</td>
					<td width=\"30px\" class=\"title\">Gen.</td>
					<td></td>
				</tr>";
					// <td width=\"30px\" class=\"title\">Mode</td>
					// <td width=\"30px\" class=\"title\">". $_DIMS['cste']['_DIMS_LABEL_ACTIVE']."</td>
				$categcour=$fields['id_metacateg'];
			}

			if(!empty($skin->values['bgline1']) && !empty($skin->values['bgline2'])){
				$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
			}else{
				$color = "";
			}
			if (!isset($elemblock[$fields['id_mbfield']])) {
				$delete = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ADMIN."&op=deletemetafield&object_id={$object_id}&metafield_id={$fields['id']}";
			}
			else {
				$delete="";
			}
			$usecmd = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ADMIN."&op=usemetafield&object_id={$object_id}&metafield_id={$fields['id']}";

			if ($op == 'modifyfield' && $metafield_id == $fields['id']) {
				$find=true;
		?>
					<tr bgcolor="<? echo $color; ?>">
						<td colspan="9">
							<table cellpadding="0" cellspacing="0" bgcolor="<?= isset($skin->values['colsec'])?$skin->values['colsec']:""; ?>" width="100%"><tr><td height="1"></td></tr></table>
						</td>
					</tr>
					<tr>
						<td colspan="9">
							<? require_once DIMS_APP_PATH . '/modules/system/crm_business_admin_field.php'; ?>
						</td>
					</tr>
				<?
			}
			elseif ($op == 'modifyseparator' && $field_id == $fields['id']) {
				?>
					<tr bgcolor="<? echo $color; ?>">
						<td colspan="9">
							<table cellpadding="0" cellspacing="0" bgcolor="<? if (array_key_exists('colsec', $skin->values)) { echo $skin->values['colsec']; } ?>" width="100%"><tr><td height="1"></td></tr></table>
						</td>
					</tr>
					<tr>
						<td colspan="9">
							<? require_once DIMS_APP_PATH . '/modules/sytem/crm_business_admin_separator.php'; ?>
						</td>
					</tr>
				<?
			}
			else {
				if ($posit!=$fields['position']) {
					$metafield = new metafield();
					$metafield->open($fields['id']);
					$metafield->fields['position']=$posit;
					//$fields['position']=$posit;
					//$metafield->save();
				}
				$posit++;
				?>
				<tr bgcolor="<? echo $color; ?>">
					<td width="35" align="center"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ADMIN."&op=moveupmetafield&object_id={$object_id}&metafield_id={$fields['id']}"; ?>"><img border="0" src="./common/modules/forms/img/ico_up.gif"></a><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ADMIN."&op=movedownmetafield&object_id={$object_id}&metafield_id={$fields['id']}"; ?>"><img border="0" src="./common/modules/forms/img/ico_down.gif"></a></td>
					<td width="20" align="center"><? echo $fields['position']; ?></td>
					<?
						$open = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ADMIN."&op=modifyfield&object_id={$object_id}&metafield_id={$fields['id']}";
					?>
					<td><a href="<? echo $open; ?>">
					<?
					if ($fields['id_mbfield']>0 && isset($mbfields_used[$fields['id_mbfield']]) && isset($mbfields_used[$fields['id_mbfield']]['protected']) && $mbfields_used[$fields['id_mbfield']]['protected']==1) {
						if (isset($_DIMS['cste'][$mbfields_used[$fields['id_mbfield']]['label']])) echo $_DIMS['cste'][$mbfields_used[$fields['id_mbfield']]['label']];
						else echo $mbfields_used[$fields['id_mbfield']]['label'];
					}
					else {
						echo $fields['name'];
					}
					?>
					</a></td>

					<td><? echo $metafield_types[$fields['type']]; ?></td>
					<td>
					<?
					// <? if ($fields['type'] == 'text' && isset($metafield_formats[$fields['format']])) echo " ( {$metafield_formats[$fields['format']]} )";
					// generation du lien pour la gestion des usages
					if ($fields['cpte']==0) $textshare=$_DIMS['cste']['_DIMS_LABEL_NOTUSED'];
					else {
						$textshare=$fields['cpte']." ".$_DIMS['cste']['_DIMS_USE_MODE'];
						if ($fields['cpte']>1) $textshare.="s";
					}
					if (!isset($elemblock[$fields['id_mbfield']])) {
						echo "<div id=\"share_".$fields['id']."\" onmouseover=\"javascript:this.style.cursor='pointer';\" onclick=\"displayMetaShareInfo(".$fields['id'].",".$fields['id_object'].");\">$textshare</div>";
					}
					else {
						echo $_DIMS['cste']['_FORMS_OBLIGATORY'];
						// verification de l'existance de l'utilisation
						$restemp= $db->query("SELECT id from dims_mod_business_meta_use where id_metafield= :idmetafield and id_object= :idobject ", array(
							':idobject' 	=> $fields['id_object'],
							':idmetafield' 	=> $fields['id']
						));
						if ($db->numrows($restemp)==0) {
							// on a pas correpondance
							$metafielduse = new metafielduse();
							$metafielduse->fields['id_metafield']=$fields['id'];
							$metafielduse->fields['id_object']=$fields['id_object'];
							$metafielduse->fields['id_workspace']=0;
							$metafielduse->fields['sharemode']=2;
							$metafielduse->save();
						}
					}
					?>
					</td>
					<td align="center">
						<?
						if (isset($mbfields_used[$fields['id_mbfield']]['indexed']) && $mbfields_used[$fields['id_mbfield']]['indexed']) {
							echo "<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
						}
						else {
							echo "<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";
						}
						?>
					</td>
					<td align="center">
					<?
					if ($fields['id_mbfield']>0 && isset($mbfields_used[$fields['id_mbfield']]['protected']) && $mbfields_used[$fields['id_mbfield']]['protected']==1) {
						echo "<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
					}
					else echo "&nbsp;"
					?>
					</td>
<?php
/*
					<td align="center">
					<?
					if ($fields['mode']==0) echo "<img src=\"./common/img/all.png\" alt=\"\">";
					else echo "<img src=\"./common/img/share.png\" alt=\"\">";

										if ($fields['mode']!=0) {
											// on a differentes valeurs

											echo "&nbsp;<a href=\"javascript:void(0);\" onclick=\"displayMetaShareInfoDoublon(".$fields['id'].",".$fields['id_object'].");\"><img src=\"./common/img/desktop.png\" alt=\"\"></a>";
										}
					?>
					</td>
					<td align="center">
					<?
					if ($fields['used']) echo "<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
					else echo "<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";
					?>
					</td>
*/
?>
					<td width="75" align="center"><a name="link_edit_<? echo $fields['id']; ?>" id="link_edit_<? echo $fields['id']; ?>" href="<? echo $open; ?>"><img border="0" src="./common/modules/forms/img/ico_modify.gif"></a>
					<?
					if (!$fields['used']) {
						echo "<a href=\"javascript:dims_confirmlink('".$usecmd."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img border=\"0\" src=\"./common/img/go-up.png\"></a></td>";
					}
					else {
						if ($delete!="") {
							echo "<a href=\"javascript:dims_confirmlink('".$delete."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img border=\"0\" src=\"./common/modules/forms/img/ico_delete.gif\"></a></td>";
						}

					}
					?>
				</tr>
				<?
			}
		}
		?>
	</table>
</div>

<script language="JavaScript" type="text/JavaScript">
<?
	if ($op == 'modifyfield' && $metafield_id>0 && $find) {
		echo 'window.onload=function(){ $("button_save").focus();}';
	}
?>
</script>
