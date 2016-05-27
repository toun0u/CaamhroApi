<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$metafield = new metafield();
$metafield->open($metafield_id);

$skin=new skin();
echo $skin->open_simplebloc($_DIMS['cste']['_BUSINESS_USE_DEFINEDFIELD']." <b>".$metafield->fields['name']."</b>");

echo "<form action=\"\" method=\"post\"><input type=\"hidden\" name=\"op\" value=\"savemetause\">";

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("sharemode");
$token->field("op");
$token->field("selwork");
$tokenHTML = $token->generate();
echo $tokenHTML;

echo	"<div style=\"overflow:auto;background-color:#FFFFFF;\">";

// lecture des partages
$arrayshare=array();
$ts=array();
for ($i=0;$i<=2;$i++) $ts[$i]="";
$tslabel=array();
$tslabel[0]=$_DIMS['cste']['_DIMS_LABEL_NOTUSED'];
$tslabel[1]=$_DIMS['cste']['_DIMS_WORKSPACE_USE'];
$tslabel[2]=$_DIMS['cste']['_DIMS_ALL_WORKSPACES'];

$res=$db->query("SELECT * from dims_mod_business_meta_use where id_metafield= :idmetafield and id_object= :idobject ", array(
	':idmetafield' 	=> $metafield->fields['id'],
	':idobject' 	=> $metafield->fields['id_object']
));
$selectedworkspaces=array();

$ts[0]="checked=\"checked\"";

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$arrayshare[$f['id']]=$f;
		if ($f['sharemode']==1) $selectedworkspaces[]=$f['id_workspace'];
		$ts[0]="";
		$ts[$f['sharemode']]="checked=\"checked\"";
	}
}

echo $_DIMS['cste']['_DIMS_USE_MODE']."<br><ul style=\"list-style: none;\">";
for ($i=0;$i<=2;$i++) {
	if ($i==1) $refresh=1;
	else $refresh=0;
	echo "<li><input type=\"radio\" onclick=\"refreshListWork(".$refresh.");\" value=\"$i\" ".$ts[$i]." name=\"sharemode\"/>".$tslabel[$i]."</input></li>";
}
echo "</ul></div>";

if (!empty($selectedworkspaces)) $style="display:block;visibility:visible;";
else $style="visibility:hidden;display:none;";
echo "<div id=\"lstsharemetaworkspace\" style=\"overflow:auto;height:200px;background-color:#FFFFFF;$style\">";
	$workspaces = system_getworkspaces();
	echo system_build_tree_domain($workspaces,$selectedworkspaces);
echo "</div>";

echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
<input type=\"button\" onclick=\"dims_hidepopup();\" value=\"Fermer\" class=\"flatbutton\"/>
<input type=\"submit\"  value=\"Valider\" class=\"flatbutton\"/></div></form>";
echo $skin->close_simplebloc();
?>
