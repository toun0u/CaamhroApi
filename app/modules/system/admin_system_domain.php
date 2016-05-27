<?php

$search_domain= dims_load_securvalue('search_domain', dims_const::_DIMS_CHAR_INPUT, true,true,true);

echo $skin->open_simplebloc($_DIMS['cste']['_SYSTEM_DOMAINSLIST']);

if(isset($_POST['savedomainworkspace'])) {
	$id_domain=dims_load_securvalue('iddomain',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$typeaccess=dims_load_securvalue('typeaccess',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$sql="SELECT * FROM dims_workspace_domain WHERE id_domain= :iddomain ";

	$lstworkspace=array();
	$lstworkspacecurrent=array();

	$res=$db->query($sql, array(':iddomain' => $id_domain) );

 	while ($f=$db->fetchrow($res)) {
		$lstworkspacecurrent[]=$f['id_workspace'];
	}

	$selwork = dims_load_securvalue('selwork', dims_const::_DIMS_NUM_INPUT, true, true, true);
	foreach ($selwork as $wid) {
		$lstworkspace[]=$wid;
	}

	// on met a jour ceux deja rattaches par l'autre type d'acces
	if (sizeof($lstworkspace)==0) {
		$lstworkspace[]=0;
	}
	if ($typeaccess==0 ) {
		$res=$db->query("UPDATE dims_workspace_domain set access=2 where id_domain= :iddomain and access=1 and id_workspace in (".implode(',',$lstworkspace).")", array(':iddomain' => $id_domain) );
		$res=$db->query("UPDATE dims_workspace_domain set access=1 where id_domain= :iddomain and access=2 and id_workspace not in (".implode(',',$lstworkspace).")", array(':iddomain' => $id_domain) );
	}
	else {
		$res=$db->query("UPDATE dims_workspace_domain set access=2 where id_domain= :iddomain and access=0 and id_workspace in (".implode(',',$lstworkspace).")", array(':iddomain' => $id_domain) );
		$res=$db->query("UPDATE dims_workspace_domain set access=0 where id_domain= :iddomain and access=2 and id_workspace not in (".implode(',',$lstworkspace).")", array(':iddomain' => $id_domain) );
	}

	//on supprime les supprimes
	$res=$db->query("DELETE from dims_workspace_domain where id_domain= :iddomain and access= :typeaccess and id_workspace not in (".implode(',',$lstworkspace).")",
					array(':iddomain' => $id_domain, ':typeaccess' => $typeaccess) );

		// on ajoute maintenant les nouveaux domaines avec le type existant
	// on extrait les nouveaux
	$array_diff=array_diff($lstworkspace,$lstworkspacecurrent);


	if (sizeof($array_diff)>0) {
		foreach ($array_diff as $wid) {
			$res=$db->query("INSERT into dims_workspace_domain set id_domain= :iddomain , access= :typeaccess, id_workspace= :idworkspace ",
					array(':iddomain' => $id_domain, ':typeaccess' => $typeaccess, ':idworkspace' => $wid) );
		}
	}
	// redirection pour rafraichissement
	dims_redirect("/admin.php");
}

$columns = array();
$values = array();
$c = 0;

$columns['auto'][0] = array('label' => $_DIMS['cste']['_DIMS_LABEL_DOMAIN']);
$columns['right'][6] = array('label' => $_DIMS['cste']['_MODIFY']."/".$_DIMS['cste']['_DELETE'], 'width' => '130');
$columns['right'][2] = array('label' => $_DIMS['cste']['_DIMS_LABEL_SSLACCESS'], 'width' => '120');
$columns['right'][3] = array('label' => "Mobile", 'width' => '50');
$columns['right'][4] = array('label' => $_DIMS['cste']['_DIMS_LABEL_WEBDOMAIN'], 'width' => '150');
$columns['right'][5] = array('label' => $_DIMS['cste']['_DIMS_LABEL_ADMINDOMAIN'], 'width' => '150');
$columns['right'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_ACTIVATED_HTTPEMAIL_KEY'], 'width' => '210');

echo '<form name="form_filter" action="" method="post">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("search_domain");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '<div style="margin:10px;">
		<input type="text" id="search_domain" name="search_domain" value="'.$search_domain.'">
		<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" onclick="javascript:document.form_filter.submit();">
			<span class="ui-icon ui-icon-search"></span>
			<span class="ui-button-text">Search</span>
		</button>
		<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-secondary" href="'.$scriptenv.'?op=add_domain" style="float:right;">
			<span class="ui-button-text">'.$_DIMS['cste']['_DIMS_LABEL_DOMAIN_ADD'].'</span>
			<span class="ui-button-icon-secondary ui-icon ui-icon-plus"></span>
		</a>
	</div>';

echo '<div style="clear:both;">';
// suppression des correspondances des workspaces
$sql = "DELETE from dims_workspace_domain where id_workspace not in (SELECT id from dims_workspace);";

$result = $db->query($sql);

// get all modules installed in a table
$param = array();
$sql="	SELECT 		d.*,
					count(distinct( wd1.id_workspace)) as cpte1,
					count(distinct( wd2.id_workspace)) as cpte2 ,
					count(distinct(w1.id)) as cpte1actif,
					count(distinct(w2.id)) as cpte2actif
		from 		dims_domain as d
		left join	dims_workspace_domain as wd1
		on			d.id=wd1.id_domain and (wd1.access=0 or wd1.access=2)
		left join	dims_workspace as w1 on w1.id=wd1.id_workspace and w1.admin=1
		left join	dims_workspace_domain as wd2
		on			d.id=wd2.id_domain and (wd2.access=1 or wd2.access=2)
		left join	dims_workspace as w2 on w2.id=wd2.id_workspace and w2.web=1";

if ($search_domain!='') {
	$sql.=" WHERE d.domain like :searchdomain ";
	$param[':searchdomain'] = "%$search_domain%";
}

$sql.=  "
		group by	d.id";

$result = $db->query($sql, $param);

while ($fields = $db->fetchrow($result)) {
	if ($fields['cpte1']>0 && $fields['cpte1actif']>0) $has_adminaccess = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\"> ";
	else $has_adminaccess = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$has_adminaccess.=$fields['cpte1']." ".$_DIMS['cste']['_WORKSPACE']."(s)";
	$div_adminaccess="<span id=\"admin_".$fields['id']."\" onmouseover=\"javascript:this.style.cursor='pointer';\" onclick=\"displayDomainInfo(event,0,".$fields['id'].");\">$has_adminaccess</span>";

	if ($fields['cpte2']==1 && $fields['cpte2actif']==1) $has_webaccess = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\"> ";
	else $has_webaccess = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$has_webaccess.=$fields['cpte2']." ".$_DIMS['cste']['_WORKSPACE']."(s)";
	$div_webaccess="<span id=\"admin_".$fields['id']."\" onmouseover=\"javascript:this.style.cursor='pointer';\" onclick=\"displayDomainInfo(event,1,".$fields['id'].");\">$has_webaccess</span>";

	//$nbtpl=(isset($tabemplate[$fields['id_workspace']])) ? sizeof($tabemplate[$fields['id_workspace']]) : 0;

	if ($fields['ssl']) $has_ssl = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";
	else $has_ssl = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$open = "$scriptenv?op=modify&id_domain={$fields['id']}";
	$delete = "$scriptenv?op=delete&id_domain={$fields['id']}";

	if ($fields['mobile']) $div_mobile = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";
	else $div_mobile = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$has_ssl="<a href=\"".dims_urlencode("/admin.php?op=switch_ssl&domain_id=".$fields['id'])."\">".$has_ssl."</a>";
	$div_mobile="<a href=\"".dims_urlencode("/admin.php?op=switch_mobile&domain_id=".$fields['id'])."\">".$div_mobile."</a>";

	$cmd='
	<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$open.'" title="'.$_DIMS['cste']['_MODIFY'].'">
		<span class="ui-button-icon ui-icon ui-icon-wrench"></span>
		<span class="ui-button-text">'.$_DIMS['cste']['_MODIFY'].'</span>
	</a>
	<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="javascript:dims_confirmlink(\''.$delete.'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')" title="'.$_DIMS['cste']['_DELETE'].'">
		<span class="ui-button-icon ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">delete</span>
	</a>';

	$values[$c]['values'][0] = array('label' => $fields['domain'], 'style' => '');
	$values[$c]['values'][1] = array('label' => $fields['webmail_http_code'], 'style' => 'text-align:center');
	$values[$c]['values'][2] = array('label' => $has_ssl, 'style' => 'text-align:center');
	$values[$c]['values'][3] = array('label' => $div_mobile, 'style' => 'text-align:center');
	$values[$c]['values'][4] = array('label' => $div_webaccess, 'style' => 'text-align:center');
	$values[$c]['values'][5] = array('label' => $div_adminaccess, 'style' => 'text-align:center');
	$values[$c]['values'][6] = array('label' => $cmd, 'style' => 'text-align:center');
	$values[$c]['link'] = '';
	$values[$c]['style'] = '';
	$c++;

}

$skin->display_array($columns, $values);
echo '</div></form>';
echo $skin->close_simplebloc();
?>
