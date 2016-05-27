<?php

$cat = dims_load_securvalue('cat',dims_const::_DIMS_NUM_INPUT,true,true);
//$dims_mainmenu = dims_load_securvalue('dims_mainmenu',dims_const::_DIMS_NUM_INPUT,true,true);
$tabscriptenv = "admin.php?cat=".$cat; //dims_mainmenu=".$dims_mainmenu."&

$part = dims_load_securvalue('part',dims_const::_DIMS_NUM_INPUT,true,true);
if(empty($part)) $part= _BUSINESS_TAB_CONTACT_IDENTITE;

$contact_id = dims_load_securvalue('contact_id',dims_const::_DIMS_NUM_INPUT,true,true);
if($contact_id == "") $contact_id = $_SESSION['business']['contact_id'];
////echo "contact_id : ".$contact_id;
$contact= new contact();
if ($contact_id>0) {
	$contact->open($contact_id);
	$_SESSION['business']['contact_id']=$contact_id;
}

$id_mbfield = dims_load_securvalue('id_mbfield',dims_const::_DIMS_NUM_INPUT,true,true);
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="width:25%;vertical-align:top;">
			<?
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_news.php');
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_docs.php');
			?>
		</td>
		<td align="center" style="vertical-align:top;padding-left:5px;">
			<? echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_HISTORY'],'font-weight:bold;width:100%','','');
			echo "<div style=\"width:100%\">";
			echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],'./common/img/undo.gif','','','display',"$tabscriptenv&action=".$action."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id);
			echo "</div>";
			// construction de la liste distincte des champs qui ont change
			$sql =" select distinct cmf.id_mbfield, mf.label from dims_mod_business_contact_mbfield as cmf
					inner join dims_mb_field as mf on
					mf.id=cmf.id_mbfield and cmf.id_contact= :idcontact
					group by cmf.id_mbfield";
			$res=$db->query($sql, array(
				':idcontact' => $contact_id
			));

			$color=$skin->values['bgline2'];

			if ($db->numrows($res)>0) {
				echo "<table cellpadding=\"2\" cellspacing=\"1\" style=\"width:100%;\">";

				echo "<form name=\"metafilter\" action=\"".$dims->getScriptEnv()."\" method=\"get\">";
				echo "<tr bgcolor=\"".$color."\"><td colspan=\"3\" style=\"text-align:center;\">
				<span style=\"float:left;\">";
				echo  $_DIMS['cste']['_FORMS_FILTER']."<select name=\"id_mbfield\" onchange=\"javascript:document.metafilter.submit();\">";

				if ($id_mbfield==0) $sel="selected";
				else $sel="";

				echo "<option value=\"0\">-</option>";
				// construction de la liste
				while ($f=$db->fetchrow($res)) {
					if ($id_mbfield==$f['id_mbfield']) $sel="selected";
					else $sel="";

					if (isset($_DIMS['cste'][$f['label']])) $name=$_DIMS['cste'][$f['label']];
					else $name=$f['label'];

					echo "<option $sel value=\"".$f['id_mbfield']."\">".$name."</option>";
				}

				echo "</select></span<span style=\"float:left;\">";
				echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],'./common/img/search.png','javascript:document.metafilter.submit();','display','javascript:void(0);')."</span></td>";



				// construction de la liste des champs d'historique
				$param = array();
				$sql =" select cmf.*, mf.label,u.lastname,u.firstname from dims_mod_business_contact_mbfield as cmf
						inner join dims_mb_field as mf on
						mf.id=cmf.id_mbfield  and cmf.id_contact= :idcontact ";
				$param[':idcontact'] = $contact_id;

				if ($id_mbfield>0) {
					$sql.=" and cmf.id_mbfield= :mbfield ";
					$param[':mbfield'] = $id_mbfield;
				}
				$sql.=" left join dims_user as u
						on u.id=cmf.id_user
						order by cmf.timestp_modify desc";

				$res=$db->query($sql, $param );

				echo "
					<tr bgcolor=\"".$color."\">
					<td width=\"25%\">".$_DIMS['cste']['_BUSINESS_FIELD_NAME']."</td>
					<td width=\"45%\">".$_DIMS['cste']['_DIMS_LABEL_RULEVALUE']."</td>
					<td width=\"10%\">". $_DIMS['cste']['_DIMS_DATE']."</td>
					<td width=\"20%\">". $_DIMS['cste']['_DIMS_LABEL_USER']."</td>
				</tr>";

				while ($f=$db->fetchrow($res)) {
					if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
					else $color=$skin->values['bgline2'];

					echo "
						<tr bgcolor=\"".$color."\">";

					if (isset($_DIMS['cste'][$f['label']])) $name=$_DIMS['cste'][$f['label']];
					else $name=$f['label'];

					echo "
						<td>".$name."</td>
						<td>".dims_strcut($f['value'],200)."</td>";

					$datecreate=dims_timestamp2local($f["timestp_modify"]);
					echo "
						<td>".$datecreate['date']."</td>
						<td>".$f['firstname']." ".$f['lastname']."</td>
					</tr>";
				}
				echo "</table>";
			}
				echo $skin->close_widgetbloc();
			?>
		</td>

	</tr>
</table>
