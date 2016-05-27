<?php
require_once DIMS_APP_PATH . '/modules/system/class_project.php';

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_USER_AFFECT'],'100%','','',false);

$ws= new workspace();
$ws->open($_SESSION['dims']['workspaceid']);

$lstusers=$ws->getusers();

// construction des personnes affectées à ce projet
$tabselusers=array();

if (isset($_SESSION['dims']['currentproject'])) {
	$project = new project();
	$project->open($_SESSION['dims']['currentproject']);
	$tabselusers=$project->getUsers();
}

if (sizeof($lstusers)>0) {
	echo "<form id=\"form_affect\" name=\"form_affect\" method=\"POST\">";
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op");
	$token->field("useraffect");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
	echo "<input type=\"hidden\" name=\"op\" value=\"affectation_save\">
	<table width=\"100%\">";
	foreach ($lstusers as $id=>$user) {
		$select=(isset($tabselusers[$user['id']])) ? "checked" : "";
		echo "<tr><td width=\"5%\"><input name=\"useraffect[]\" value=\"".$user['id']."\" type=\"checkbox\" ".$select."></td><td>".strtoupper(substr($user['firstname'],0,1)).". ".$user['lastname']."</td></tr>";
	}
	echo "</table>";
	echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_affect.submit();","","");
	echo "</form>";
}
echo $skin->close_simplebloc();
?>
