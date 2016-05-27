<script type="text/javascript">
function displayMetaShareInfo(id_metafield,id_object) {
	dims_showcenteredpopup("",600,350,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','cat=<? echo _BUSINESS_CAT_CONTACT; ?>&dims_op=<? echo _BUSINESS_TAB_ADMIN; ?>&dynfield_op=editmetause&metafield_id='+id_metafield+'&object_id='+id_object,'','dims_popup');
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
require_once(DIMS_APP_PATH . "/modules/system/include/metatype.php");

$db = dims::getInstance()->getDB();

$object_id=$this->getIdObject();
$id_module_type=$this->getIdModuleType();

echo "<div style=\"width:100%;background:#FFFFFF;display:block;float:left;\">Affichage des champs";

$op = dims_load_securvalue('dynfield_op', dims_const::_DIMS_CHAR_INPUT, true, true);

// recherche de la premiere rubrique
$res=$db->query("select id from dims_mod_business_meta_categ where admin=1 order by position");
if ($db->numrows($res)>0) {
	if ($f=$db->fetchrow($res)) {
		$id_firstcateg=$f['id'];
	}
}

// structure permettant de bloquer la suppression de ces champs
$elemblock=array();


// construction de la liste des champs mbfields deja utilises
$mbfields_used = $this->getMbFieldsUsed();

// verifie si un element est s�lectionn� ou non
$find=false;
?>
<div style="clear:left;width:100%;background:#FFFFFF">
	<table cellpadding="2" cellspacing="1" style="width:100%;bgcolor:#FFFFFF;">
		<?
		$categcour=0;
		$sql =	"
				SELECT		distinct mf.*
				FROM		dims_meta_field as mf

				WHERE		 mf.id_object = :objectid
				AND			 mf.id_module_type = :moduletype
				GROUP BY	mf.id
				ORDER BY	mf.position
				";

		$rs_fields = $db->query($sql, array(
			':objectid'		=> $object_id,
			':moduletype'	=> $id_module_type
		));
		$posit=0;

		while ($fields = $db->fetchrow($rs_fields)) {

			if(!is_null($skin))
				$color =  (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
				else $color = 'white';
			if (!isset($elemblock[$fields['id_mbfield']])) {
				$delete = "admin.php?".$this->getMetierEnvList()."&dims_op=dynfield_manager&dynfield_op=deletemetafield&object_id={$object_id}&metafield_id={$fields['id']}&tablename=".$this->tablename.'&id_module_type='.$this->getIdModuleType();
			}
			else {
				$delete="";
			}
			$usecmd = "admin.php?".$this->getMetierEnvList()."&dims_op=dynfield_manager&dynfield_op=usemetafield&object_id={$object_id}&metafield_id={$fields['id']}";

			if ($op == 'modifyfield' && $metafield_id == $fields['id']) {
				$find=true;
		?>
					<tr bgcolor="<? echo $color; ?>">
						<td colspan="9">
							<table cellpadding="0" cellspacing="0" bgcolor="<? echo $skin->values['colsec']; ?>" width="100%"><tr><td height="1"></td></tr></table>
						</td>
					</tr>
					<tr>
						<td colspan="9">
							<? require_once DIMS_APP_PATH . '/modules/system/dynfield/view/dynfield_field.php'; ?>
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
					<td width="35" align="center"><a href="<? echo "admin.php?".$this->getMetierEnvList()."&dims_op=dynfield_manager&dynfield_op=moveupmetafield&object_id={$object_id}&metafield_id={$fields['id']}"; ?>"><img border="0" src="./common/modules/forms/img/ico_up.gif"></a><a href="<? echo "admin.php?".$this->getMetierEnvList()."&dims_op=dynfield_manager&dynfield_op=movedownmetafield&object_id={$object_id}&metafield_id={$fields['id']}"; ?>"><img border="0" src="./common/modules/forms/img/ico_down.gif"></a></td>
					<td width="20" align="center"><? echo $fields['position']; ?></td>
					<?
						$open = "admin.php?".$this->getMetierEnvForm()."&dims_op=dynfield_manager&dynfield_op=modifyfield&object_id={$object_id}&metafield_id={$fields['id']}";
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
					<?php
					// generation du lien pour la gestion des usages
/*
					if ($fields['cpte']==0) $textshare=$_DIMS['cste']['_DIMS_LABEL_NOTUSED'];
					else {
						$textshare=$fields['cpte']." ".$_DIMS['cste']['_DIMS_USE_MODE'];
						if ($fields['cpte']>1) $textshare.="s";
					}
*/
					if (!isset($elemblock[$fields['id_mbfield']])) {
/*
						echo "<div id=\"share_".$fields['id']."\" onmouseover=\"javascript:this.style.cursor='pointer';\" onclick=\"displayMetaShareInfo(".$fields['id'].",".$fields['id_object'].");\">$textshare</div>";
*/
					}
					else {
						echo $_DIMS['cste']['_FORMS_OBLIGATORY'];
						// verification de l'existance de l'utilisation
						$restemp= $db->query("SELECT id from dims_mod_business_meta_use where id_metafield= :idmetafield and id_object= :idobject ", array(
							':idmetafield'	=> $fields['id'],
							':idobject'		=> $fields['id_object']
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
					<td align="center">
					<?
					if ($fields['mode']==0) echo "<img src=\"./common/img/all.png\" alt=\"\">";
					else echo "<img src=\"./common/img/share.png\" alt=\"\">";
					?>
					</td>
					<td align="center">
					<?
					if ($fields['used']) echo "<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
					else echo "<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";
					?>
					</td>
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
