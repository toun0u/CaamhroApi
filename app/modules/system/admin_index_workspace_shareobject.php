<?
$id_workspace=dims_load_securvalue('id_workspace',dims_const::_DIMS_NUM_INPUT,true,false,false);
$id_object=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,false);

$workspaces = system_getworkspaces();

require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");

if(isset($_GET['deleteshareobjectworkspace'])) {
	$id_object=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$id_to=dims_load_securvalue('id_to',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$res=$db->query("DELETE FROM dims_workspace_share WHERE id_from= :idfrom AND id_object= :idobject AND id_to= :idto ",
					array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );
}

if(isset($_GET['saveshareobjectworkspace'])) {
	$id_object=dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$value=dims_load_securvalue('value',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$id_to=dims_load_securvalue('id_to',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$reverse=dims_load_securvalue('reverse',dims_const::_DIMS_NUM_INPUT,true,true,false);

	if ($value==1) {
		$sql="SELECT * FROM dims_workspace_share WHERE id_from= :idfrom and id_object= :idobject and id_to= :idto";

		$res=$db->query($sql, array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );

		if ($db->numrows($res)==1) {
			$res=$db->query("UPDATE dims_workspace_share SET active=1 WHERE id_from= :idfrom AND id_object= :idobject AND id_to= :idto",
							array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );
		}
		else {
			$res=$db->query("INSERT INTO dims_workspace_share set id_from= :idfrom , id_object= :idobject , id_to= :idto , ctive=1",
							array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );
		}
	}
	else {
		// verifions si le statut du active_to est aussi � 0, alors on supprime
		$sql="SELECT * from dims_workspace_share where id_from= :idfrom and id_object= :idobject and id_to= :idto and active=1";

		$resu=$db->query($sql, array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );

		if ($db->numrows($resu)==1) {
			$res=$db->query("DELETE from dims_workspace_share where id_from= :idfrom and id_object= :idobject and id_to= :idto ",
							array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );
		}
		else {
			$res=$db->query("UPDATE dims_workspace_share set active=0 where id_from= :idfrom and id_object= :idobject and id_to= :idto ",
							array(':idfrom' => $id_workspace, ':idobject' => $id_object, ':idto' => $id_to) );
		}
	}

	if ($reverse) {
		$id_workspace=$id_to;
	}
}

$work=new workspace();
$work->open($id_workspace);

$user= new user();
$user->open($_SESSION['dims']['userid']);
$lstworks=$user->getworkspaces();

//$skin=new skin();
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_SHARE']." <b>".$work->fields['label']."</b>");

echo "<div style=\"width:100%;text-align:center;\">";
// ajout du bouton d'ajout
//echo "<div style=\"display:block;width:100%;text-align:right;margin-right:10px;\"><a href=\"$scriptenv?op=add_shareobject\">".$_DIMS['cste']['_DIMS_ADD']."&nbsp;<img src=\"./common/img/icon_add.gif\" border=\"0\" alt=\"\"></a>&nbsp;</div>";

echo "<table style=\"width:100%;\"><tr>";
	echo "<td>".$_DIMS['cste']['_WORKSPACE']."</td>";
	echo "<td>".$_DIMS['cste']['_DIMS_LABEL_ACTIVE']."</td>";
	echo "<td>".$_DIMS['cste']['_WORKSPACE']."</td>";
	echo "<td>".$_DIMS['cste']['_DIMS_LABEL_ACTIVE']."</td>";
	echo "<td>".$_DIMS['cste']['_DELETE']."</td></tr>";

// init tableau
$tabshare=array();
$selectedworkspaces=array();
$needworkspaces=array();
$notneedworkspaces=array();

$res=$db->query("select * from dims_workspace where contact=0");
// parcours des correspondances trouvees
while ($fields = $db->fetchrow($res)) {
	$notneedworkspaces[$fields['id']]=$fields['id'];
}

// get all modules installed in a table
$sql="	select 		ws.*,
					w1.label as labelfrom,
					w2.label as labelto
		from 		dims_workspace_share as ws
		left join	dims_workspace as w1
		on			w1.id=id_from and w1.contact=1
		left join	dims_workspace as w2
		on			w2.id=id_to and w2.contact=1
		where		ws.id_from= :idfrom and id_object= :idobject ";

$result = $db->query($sql, array(':idfrom' => $id_workspace, ':idobject' => $id_object) );

// parcours des correspondances trouvees
while ($fields = $db->fetchrow($result)) {
	$key=$fields['id_from']."_".$fields['id_to'];
	if (!isset($tabshare[$key])) {
		$elem=array();
		$elem['id_from']=$fields['id_from'];
		$elem['id_to']=$fields['id_to'];
		$elem['active_from']=0;
		$elem['active_to']=0;
		$elem['labelfrom']=$fields['labelfrom'];
		$elem['labelto']=$fields['labelto'];
		$tabshare[$key]=$elem;
	}

	if ($fields['id_from']==$id_workspace) {
		if (!isset($selectedworkspaces[$fields['id_to']]) && $fields['active']) $selectedworkspaces[$fields['id_to']]=$fields['id_to'];
		// partage cr�� depuis cet espace
		$tabshare[$key]['active_from']=$fields['active'];
	}
}

$sql="	select 		ws.*,
					w1.label as labelfrom,
					w2.label as labelto
		from 		dims_workspace_share as ws
		left join	dims_workspace as w1
		on			w1.id=id_from and w1.contact=1
		left join	dims_workspace as w2
		on			w2.id=id_to and w2.contact=1
		where		ws.id_to= :idto and id_object= :idobject ";


$result = $db->query($sql, array(':idto' => $id_workspace, ':idobject' => $id_object) );

// parcours des correspondances trouvees
while ($fields = $db->fetchrow($result)) {
	if ($fields['labelfrom']!="") {
		$key=$fields['id_to']."_".$fields['id_from'];
		$keyinv=$fields['id_from']."_".$fields['id_to'];
		if (isset($tabshare[$key])) {
			$tabshare[$key]['active_to']=$fields['active'];

			if ($tabshare[$key]['active_from']==0) $needworkspaces[$fields['id_from']]=$fields['id_from'];
		}
		elseif (!isset($tabshare[$keyinv])) {
			$elem=array();
			$elem['id_from']=$fields['id_from'];
			$elem['id_to']=$fields['id_to'];
			$elem['active_from']=$fields['active'];
			$elem['active_to']=0;
			$elem['labelfrom']=$fields['labelfrom'];
			$elem['labelto']=$fields['labelto'];
			$tabshare[$keyinv]=$elem;

			if (!isset($needworkspaces[$fields['id_from']]) || (!$fields['active'])) $needworkspaces[$fields['id_from']]=$fields['id_from'];
		}
	}
	else {
		$notneedworkspaces[$fields['id_from']]=$fields['id_from'];
	}
}

foreach ($tabshare as $k=>$field) {
	if ($field['active_from']) $active_from = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\"> ";
	else $active_from = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$link="";
	$linkend="";

	//updateWorkspaceShareObject(id_workspace,id_object,id_to,value)
	if ($field['id_from']==$id_workspace) {
		if (isset($lstworks[$field['id_to']])) {
			// on peut le parametrer
			if ($field['active_to']) $value=0;
			else $value=1;
			$link="<a href=\"javascript:void(0);\" onclick=\"javascript:updateWorkspaceShareObject(".$field['id_to'].",".$id_object.",".$id_workspace.",".$value.",1);\">";
			$linkend="</a>";
		}
	}
	elseif ($field['id_to']==$id_workspace) {
			if ($field['active_to']) $value=0;
			else $value=1;

			$link="<a href=\"javascript:void(0);\" onclick=\"javascript:updateWorkspaceShareObject(".$field['id_to'].",".$id_object.",".$field['id_from'].",".$value.",0);\">";
			$linkend="</a>";
	}
	if ($field['active_to']) $active_to = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\"> ";
	else $active_to = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

	$active_to=$link.$active_to.$linkend;
	if ($field['id_from']==$id_workspace) {
		$cmd="<a href=\"javascript:void(0);\" onclick=\"deleteWorkspaceShareObject(".$id_workspace.",".$id_object.",".$field['id_to'].");\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/ico_delete.gif\"></a>";
	}
	else {
		$cmd="";
	}

	echo "<tr><td>".$field['labelfrom']."</td><td>".$active_from."</td><td>".$field['labelto']."</td><td>".$active_to."</td><td>".$cmd."</td></tr>";
}

echo "</table>";
echo "<form action=\"\" method=\"post\"><input type=\"hidden\" name=\"saveshareobjectworkspace\">";

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("id_workspace");
$token->field("id_object");
$tokenHTML = $token->generate();
echo $tokenHTML;

echo	"<input type=\"hidden\" name=\"id_workspace\" value=\"".$work->fields['id']."\">
		<input type=\"hidden\" name=\"id_object\" value=\"".$id_object."\">
		<div style=\"width:500px;height:300px;overflow:auto;\">";

$notneedworkspaces[$work->fields['id']]=$work->fields['id'];

echo system_build_tree_workspace_share($workspaces,$selectedworkspaces,$needworkspaces,$notneedworkspaces,$work->fields['id'],$id_object);
echo "</div>";

echo "
<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/>
</div></form>";
echo $skin->close_simplebloc();
?>
