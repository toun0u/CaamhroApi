<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$tabversioncontent=array();
for($i=1;$i<=9;$i++) $tabversioncontent[$i]="";
// recuperation de la version courante d'affichage
if ($wce_mode!="online" && isset($adminedit) && isset($versionid) && is_numeric($versionid) && $versionid>0) {
	$db = dims::getInstance()->getDb();
	$rver=$db->query("	SELECT 	*
						FROM 	dims_mod_wce_article_version
						WHERE 	id = :id",array(':id'=>array('value'=>$versionid,'type'=>PDO::PARAM_INT)));

	if ($db->numrows($rver)>0) {
		if ($fver=$db->fetchrow($rver)) {
			for($i=1;$i<=9;$i++) $tabversioncontent[$i]=$fver['content'.$i];
		}
	}
}
else {
	for($i=1;$i<=9;$i++) {
		$tabversioncontent[$i]=$article->fields["content".$i];
	}
}

for($i = 1; $i <= 9; $i++) {

	$posstart=strpos($page,"<CONTENT$i>");
	$posend=strpos($page,"</CONTENT$i>");

	if (($posstart+strlen("<CONTENT$i>"))==$posend || $posend==0) {
		if (isset($adminedit) && $versionid) $page = str_replace("<CONTENT$i>", $tabversioncontent[$i], $page);
		else $page = str_replace("<CONTENT$i>", $article->fields["content".$i], $page);
	}
	else {
		// on  a qq chose
		$chparams=substr($page,$posstart+strlen("<CONTENT$i>"),$posend-($posstart+strlen("<CONTENT$i>")));
		// on  nettoie les params en plus + le tag de fin
		if (isset($adminedit) && $versionid) $page = str_replace("<CONTENT$i>$chparams</CONTENT$i>", $tabversioncontent[$i], $page);
		else $page = str_replace("<CONTENT$i>$chparams</CONTENT$i>",$tabversioncontent[$i] , $page);
	}
	$page = str_replace("</CONTENT$i>", "", $page);
}

?>
