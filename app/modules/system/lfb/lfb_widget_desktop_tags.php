<?php
$sql_sup="";

$params = array();
switch ($action) {
	case "contact_modify":
		$sql_sup="	INNER JOIN		dims_mod_business_contact u
					ON				u.id = ti.id_record
					AND				u.timestp_modify >= :datesince2
					AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_CONTACT;
		$params[':datesince2'] = $date_since2."000000";
		break;
	case "contact_new":
		$sql_sup="	INNER JOIN		dims_mod_business_contact u
				ON				u.id = ti.id_record
				AND				u.date_create >= :datesince2
				AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_CONTACT;
		$params[':datesince2'] = $date_since2."000000";
		break;
	case "ent_modify":
		$sql_sup="	INNER JOIN		dims_mod_business_tiers u
					ON				u.id = ti.id_record
					AND				u.timestp_modify >= :datesince2
					AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_TIERS;
		$params[':datesince2'] = $date_since2."000000";
		break;
	case "ent_new":
		$sql_sup="	INNER JOIN		dims_mod_business_tiers u
				ON				u.id = ti.id_record
				AND				u.date_create >= :datesince2
				AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_TIERS;
		$params[':datesince2'] = $date_since2."000000";
		break;
}

$sql= "SELECT count(ti.id) as cpte,t.id,t.tag FROM `dims_tag` as t
		inner join dims_tag_index as ti on ti.id_tag=t.id";

$tabtags="";

if ($sql_sup) {
	$params[':idobject'] = $idobject;
	$sql.=" and ti.id_object= :idobject and id_module=1".$sql_sup;
}
	$sql.="	where t.private = 0 and t.id_workspace= :workspaceid
			group by tag order by cpte desc";
	$params[':workspaceid'] = $_SESSION['dims']['workspaceid'];

$res=$db->query($sql, $params);
$listags='';
$st='';
if ($db->numrows($res)>0) {
	$i=0;
	while ($f=$db->fetchrow($res)) {
		if ($i<=4) {
			$size=24-$i*4;
		}
		else $size=8;
		/*if ($sql_sup=="") {
			$listags.="<a href='".$dims->getUrlPath().urlencode("?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES."&action=tags")."' style='".$size."' color='0xff0000' hicolor='0xCC0066'>".$f['tag']."</a>";
		}
		else {*/
		$listags.="<a href='".$dims->getUrlPath()."?tagfilter=".urlencode($f['id'])."' style='".$size."' color='0xff0000' hicolor='0xCC0066'>".$f['tag']."</a>";
		//}
		$st= ($st=="trl1") ? "trl2" : "trl1";
		$tabtags.="<tr class=\"".$st."\"><td>".$f['tag']."</td></tr>";
	}

}
?>
<div style="display:block;width:100%;float:left;background-color: #FFFFFF;">
	<span style="float:left;width:40%;">
	<?

	if ($tabtags!="") {
		echo "<table style=\"width:100%;\"><tr><td style=\"width:60%\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td></tr>";
		echo $tabtags;
		echo "</table>";
	}
	?>
	</span>
	<span style="float:left;width:60%;display:block;text-align:center;" id="flashcontent">
	</span>
	<script type="text/javascript">
		var so = new SWFObject("/scripts/tagcloud.swf", "tagcloud", "350", "150", "7", "");
		// uncomment next line to enable transparency
		so.addParam("wmode", "transparent");
		so.addVariable("tcolor", "0x333333");
		so.addVariable("mode", "tags");
		so.addVariable("distr", "true");
		so.addVariable("tspeed", "100");
		so.addVariable("tagcloud", "<tags><? echo $listags; ?></tags>");//<a href='http://www.roytanck.com' style='6' color='0xff0000' hicolor='0x00cc00'>WordPress</a><a href='http://www.roytanck.com' style='12'>Flash</a><a href='http://www.roytanck.com' style='16'>Plugin</a><a href='http://www.roytanck.com' style='14'>WP-Cumulus</a><a href='http://www.roytanck.com' style='12'>3D</a><a href='http://www.roytanck.com' style='12'>Tag cloud</a><a href='http://www.roytanck.com' style='9'>Roy Tanck</a><a href='http://www.roytanck.com' style='10'>SWFObject</a><a href='http://www.roytanck.com' style='10'>Example</a><a href='http://www.roytanck.com' style='12'>Click</a><a href='http://www.roytanck.com' style='12'>Animation</a></tags>");
		so.write("flashcontent");
	</script>
</div>
<?
//echo "<span style=\"width:100%;text-align:right;\"><a href='".$dims->getUrlPath()."?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES."&action=tags'>Voir l'ensemble des tags</a></span>";
?>
