<div style="width:100%;float:left;clear:both">
	<table cellpadding="0" cellspacing="0" style="width:100%;background:#FFFFFF;">
	<?
	// construction de la recherche des champs sur le type d'objet
	$categcour=0;
	$sql =	"
				SELECT		mf.*,mc.label as categlabel
				FROM		dims_mod_business_meta_field as mf
				INNER JOIN	dims_mod_business_meta_categ as mc
				ON			mf.id_metacateg=mc.id
				AND			mf.id_object = :idobject
				ORDER BY	mc.position, mf.position
				";

	$rs_fields=$db->query($sql, array(
		':idobject' => $object_id
	));
	$color="";
	if(!empty($skin->values['bgline1']) && !empty($skin->values['bgline2'])){
		$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
	}else{
		$color = "";
	}
	while ($fields = $db->fetchrow($rs_fields)) {
		if ($categcour!=$fields['id_metacateg']) {
			echo "<tr><td colspan=\"2\" style=\"font-weight:bold;\">".$fields['categlabel']."</td></tr>";
			$categcour=$fields['id_metacateg'];
		}
		echo "<tr>";
		if ($fields['option_needed']) $oblig=" *";
		else $oblig="";
		echo "<td width=\"25%\" valign=\"top\" align=\"right\" style=\"padding:4px;padding-top:".$fields['interline']."px;font-size:1em;\">".$fields['name'].$oblig."&nbsp;</td>";
		echo "<td width=\"75%\" style=\"padding:4px;padding-top:".$fields['interline']."px;\">";

		include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';
		?>
		</td>
	</tr>
	<?
	}
	?>
	<tr>
	<td colspan="2" align="center">
	<?
		echo "<a href=\"$scriptenv?op="._BUSINESS_CAT_ADMIN."\"><img border=\"0\" src=\"./common/img/undo.gif\"/>".$_DIMS['cste']['_DIMS_BACK']."</a>";
	?>
	</td></tr>
	</table>
</div>
