<script language="javascript">
	function selgroup(id_group, nb_elem) {
		if(nb_elem > 0 && nb_elem < 10000) {
			if(document.getElementById("gp_"+id_group).checked) {
				for(i = 1;i<=nb_elem;i++) {
					document.getElementById(id_group+"_"+i).checked = "checked";
				}
			}
			else {
				for(i = 1;i<=nb_elem;i++) {
					document.getElementById(id_group+"_"+i).checked = "";
				}
			}
		}
	}

	function verif_gp(id_group) {
		//si un des elements de la liste n'est pas selectionne, on enl�ve le checked sur le groupe
		document.getElementById("gp_"+id_group).checked = "";
	}
</script>
<?php

if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0)
	echo $skin->open_simplebloc($_DIMS['cste']['_PROPERTIES_PROJECT']." '".$project->fields['label']."'",'100%','','',false);
else
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_ADDPROJECT'],'100%','','',false);

/* Requ�te SQL : selection des utilisateurs*/
//$sql =	"SELECT		u.id,
//					concat(u.lastname, ' ', u.firstname) as name
//		FROM		dims_user u
//		INNER JOIN	dims_workspace_user wu
//		ON			wu.id_user = u.id
//		WHERE		wu.id_workspace = ".$_SESSION['dims']['workspaceid'];

/* On execute la requete */
//$rs = $db->query($sql);


require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
require_once(DIMS_APP_PATH . "/modules/system/class_workspace.php");
require_once(DIMS_APP_PATH . "/modules/system/class_group.php");

$wksp = new workspace();
$wksp->open($_SESSION['dims']['workspaceid']);
$tab_group = $wksp->getgroups();

//dims_print_r($tab_group);
foreach($tab_group as $id_group => $inf_group) {
	$group = new group();
	$group->open($id_group);
	$tab_gusers[$id_group] = $group->getusers();
}

foreach($tab_gusers as $id_group => $tab_u) {
	foreach($tab_u as $id_user => $inf_u) {
		$lstusers[$id_user] = $inf_u;
	}
}

$lstuserssel=array();
$user_content = "";
$sel="";
//if (!empty($_SESSION['project']['users'])) $lstuserssel+=$_SESSION['project']['users'];
//echo "<div style=\"width:100%;height:120px;overflow:auto;\">";
// affichage de la liste de resultat
if (!empty($lstusers)) {
	if (count($lstusers) > 0) {
		foreach($lstusers as $id_user => $f) {
			$sel = "";
			$sel2 = "";
			$sel3 = "";

			if($f['id'] == $project->fields['id_resp']) {
				$sel = 'selected="selected"';
				//variable pour indiquer si le user connecte fait parti des responsables
				if($f['id'] == $_SESSION['dims']['userid']) $allow_resp = 1;
			}
			$user_content .= "<option value=\"".$f['id']."\" ".$sel.">".$f['lastname'].". ".$f['firstname']."</option>";

			if($f['id'] == $project->fields['id_resp2']) {
				$sel2 = 'selected="selected"';
				//variable pour indiquer si le user connecte fait parti des responsables
				if($f['id'] == $_SESSION['dims']['userid']) $allow_resp = 1;
			}
			$user_content2 .= "<option value=\"".$f['id']."\" ".$sel2.">".$f['lastname'].". ".$f['firstname']."</option>";

			if($f['id'] == $project->fields['id_resp3']) {
				$sel3 = 'selected="selected"';
				//variable pour indiquer si le user connecte fait parti des responsables
				if($f['id'] == $_SESSION['dims']['userid']) $allow_resp = 1;
			}
			$user_content3 .= "<option value=\"".$f['id']."\" ".$sel3.">".$f['lastname'].". ".$f['firstname']."</option>";
		}
		echo "</select></p>";
	}
}

$type_proj = "";
$type_proj .= "<option value=\"1\"";
if($project->fields['type'] == 1) $type_proj .= ' selected="selected"';
$type_proj .= ">{$_DIMS['cste']['_DIMS_PROJECT_TYPE_FORMATION']}</option>";
$type_proj .= "<option value=\"2\"";
if($project->fields['type'] ==2) $type_proj .= ' selected="selected"';
$type_proj .= ">{$_DIMS['cste']['_DIMS_LABEL_STANDARD']}</option>";

//f($project->fields['type'] == $_DIMS['cste'][''])
/* On utilise les resultats de la requete pour le formulaire*/
//$user_content="";
//$user_crea="";
//$detaildisplay="";
//$detailhide="";
//while ($fields = $db->fetchrow($rs)) {
//	$user_content .= '<option value="'.$fields['id'].'">'.$fields['name'].'</option>';
//
//	/* Pour l'utilisateur cr�ateur (celui connect�) */
//	if ($fields['id']==$_SESSION['dims']['userid']) {
//		$user_crea .= $fields['name'];
//	}
//}

if ($project->fields['date_start']!="") {
	$var=dims_timestamp2local($project->fields['date_start']);
	$datestart=$var['date'];
}
else $datestart=dims_getdate();

if ($project->fields['date_end']!="") {
	$var=dims_timestamp2local($project->fields['date_end']);
	$dateend=$var['date'];
}
else $dateend=strftime ('%d/%m/%Y', strtotime ('+ 1 month'));

//recherche des ifos sur le createur du projet
$cre = new user();
$cre->open($project->fields['id_create']);
$creator = $cre->fields['firstname']." ".$cre->fields['lastname'];

/* Le contenu de la page (essentielement le formulaire) */
echo '<div style="width:100%;background:#FFFFFF;">
	<form name="form_project" onsubmit="return project_error(event, this);" action="'.$scriptenv.'"  method="POST">';
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("op", "project_save");
$token->field("project_label");
$token->field("project_id_resp");
$token->field("project_type");
$token->field("project_id_resp2");
$token->field("project_id_resp3");
$token->field("project_description");
$token->field("project_date_start");
$token->field("project_date_end");
$token->field("project_state");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '<input type="hidden" name="op" value="project_save">
	<table width="100%" cellpadding="5">
		<tr>
			<td>
				<span style="float:left;width:80px;">'.$_DIMS['cste']['_FORM_PROJECT_LABEL'].'</span>
				<span style="float:left;width:350px;"><input type="text" name="project_label" size="40" value="'.$project->fields['label'].'"></span>
			</td>
			<td>
				<div style="float:left;width:60px;">'.$_DIMS['cste']['_FORM_PROJECT_CREATOR'].'</div>
				<div style="float:left;width:180px;">'.$creator.'</div>
				<div style="float:left;width:60px;">'.$_DIMS['cste']['_PROJECT_MENU_PERSON_IN_CHARGE'].'</div>
				<div style="float:left;width:180px;"><select name="project_id_resp"><option value="">--</option>'.$user_content.'</select></div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="float:left;width:80px;">'.$_DIMS['cste']['_TYPE'].'</div>
				<div style="float:left;width:350px;"><select name="project_type"><option value="">--</option>'.$type_proj.'</select></div>
			</td>
			<td>
				<div style="float:left;width:60px;">&nbsp;</div>
				<div style="float:left;width:180px;">&nbsp;</div>
				<div style="float:left;width:60px;">'.$_DIMS['cste']['_PROJECT_MENU_PERSON_IN_CHARGE'].' 2</div>
				<div style="float:left;width:180px;"><select name="project_id_resp2"><option value="">--</option>'.$user_content2.'</select></div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div style="float:left;width:60px;">&nbsp;</div>
				<div style="float:left;width:180px;">&nbsp;</div>
				<div style="float:left;width:60px;">'.$_DIMS['cste']['_PROJECT_MENU_PERSON_IN_CHARGE'].' 3</div>
				<div style="float:left;width:180px;"><select name="project_id_resp3"><option value="">--</option>'.$user_content3.'</select></div>
			</td>
		</tr>';
echo '

		<tr>
			<td>
				<span style="float:left;width:80px;">'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</span>
				<span style="float:left;width:350px;"><textarea type="text" name="project_description" style="width:300px;height:90px">'.$project->fields['description'].'</textarea></span>
			</td>
			<td>

				<div style="float:left;width:80px;">'.$_DIMS['cste']['_FORM_PROJECT_START_DATE'].'</div>
				<div style="float:left;width:160px;">
					<input type="text" size="10" class="date datepicker" name="project_date_start" id="project_date_start" value="'.$datestart.'">&nbsp;
				</div>
				<div style="float:left;width:80px;">'.$_DIMS['cste']['_FORM_PROJECT_END_DATE'].'</div>
				<div style="float:left;width:120px;">
					<input style="margin:0px" type="text" class="text" class="date datepicker" size="10" name="project_date_end" id="project_date_end" value="'.$dateend.'">&nbsp;

				</div>
				<div style="float:top;width:200px;">
					<div style="float:left;width:80px;margin-top:15px;">'.$_DIMS['cste']['_DIMS_LABEL_ACTIVE'].' ? </div>
						<div style="float:left;width:120px;margin-top:15px;">
							'.$_DIMS['cste']['_DIMS_YES'].'<input type="radio" name="project_state" id="project_state" value="1" checked="checked"/>
							'.$_DIMS['cste']['_DIMS_NO'].'<input type="radio" name="project_state" id="project_state" value="0"/>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<tr><td colspan="2" >
			<div class="projects_form">';
				if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0 && ($_SESSION['dims']['userid'] == $project->fields['id_create'] || $allow_resp == 1)) { //dims_isadmin() ||
					echo '<div style="float:right;">';
					$href="javascript:document.form_project.submit();";
					if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0) {
						echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif",$href,"","");
					}
					else {
						echo dims_create_button($_DIMS['cste']['_FORM_TASK_OK'],"./common/img/save.gif",$href,"","");
					}
					echo '</div>';


					echo '<div style="float:right;">';
					$href="javascript:dims_confirmlink('".dims_urlencode("$scriptenv?op=delete_project")."','".$_DIMS['cste']['_DIMS_CONFIRM']."');";
					echo dims_create_button($_DIMS['cste']['_DELETE'],"./common/img/del.png",$href,"","");
					echo "</div>";
				}
				echo '
			</div>
			</td></tr>
		</table>
	</div>
	</form>';
echo $skin->close_simplebloc();

echo $skin->open_simplebloc($_DIMS['cste']['_SYSTEM_USER_PARTICIPATE'],'100%','','',false);

$ws= new workspace();
$ws->open($_SESSION['dims']['workspaceid']);

// construction des personnes affect�es � ce projet
$tabselusers=array();

if (isset($_SESSION['dims']['currentproject'])) {
	$project = new project();
	$project->open($_SESSION['dims']['currentproject']);
	$tabselusers=$project->getUsers();
}
//dims_print_r($tabselusers);
$possible_groups = $ws->getGroups();
//dims_print_r($possible_groups);
foreach($possible_groups as $id_group => $tab_g) {
	$gp = new group();
	$gp->open($id_group);
	$gpuser[$id_group]['name'] = $tab_g['label'];
	$gpuser[$id_group]['users'] = $gp->getusers();
	$gpuser[$id_group]['nb_users_sel'] = 0;
	//on indique si la personne est affect�e ou non
	foreach($gpuser[$id_group]['users'] as $id_u => $inf_u) {
		//dims_print_r($inf_u);
		//$gpuser[$id_group]['users'][$id_u]['selected'] =
		if(isset($tabselusers[$id_u])) {
			$gpuser[$id_group]['nb_users_sel']++;
			$gpuser[$id_group]['users'][$id_u]['selected'] = 1;
		}
		else $gpuser[$id_group]['users'][$id_u]['selected'] = 0;
	}
}
//dims_print_r($gpuser);

echo '<div style="width: 100%; float: left;overflow:auto;">';
if (sizeof($lstusers)>0) {

	echo "<form id=\"form_affect\" name=\"form_affect\" method=\"POST\">";
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op");
	$token->field("groupaffect");
	$token->field("useraffect");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
	echo "	<input type=\"hidden\" name=\"op\" value=\"affectation_save\">
				<div style=\"width: 100%; float: left;overflow:hidden;\">";

				foreach($gpuser as $id_group => $tab_g) {
					$gpsel = '';
					$nb_gusers = count($tab_g['users']);
					$nb_concerned = $tab_g['nb_users_sel'];
					if($nb_gusers == $nb_concerned) $gpsel = 'checked';

					echo	'<div style="width:100%; float:left;overflow:hidden;">
								<input name="groupaffect[]" id="gp_'.$id_group.'" value="'.$id_group.'" type="checkbox" '.$gpsel.' onclick="javascript:selgroup(\''.$id_group.'\', '.$nb_gusers.');">
								<span style="padding-left:10px;">
									<a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'group_'.$id_group.'\');">'.$tab_g['name'].' ('.$nb_concerned.'/'.$nb_gusers.')</a>
								</span>
							</div>
							<div id="group_'.$id_group.'" style="display:none;width:100%;float:left;overflow:hidden;">';
					$cpt = 0;
					foreach($tab_g['users'] as $id_user => $tab_u) {
						$cpt++;
						$select=(isset($tabselusers[$id_user])) ? "checked" : "";
						echo	'<div style="width:100%; float:left;overflow:hidden;padding-left:30px;">
									<input name="useraffect[]" id="'.$id_group.'_'.$cpt.'" value="'.$id_user.'" type="checkbox" '.$select.' onclick="javascript:if(!this.checked) verif_gp(\''.$id_group.'\');">
									<span style="padding-left:10px;">'.$tab_u['firstname'].' '.$tab_u['lastname'].'</span>
								</div>';
					}
					echo	'</div>';
				}
				echo "</div>";
	if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0 && ($_SESSION['dims']['userid']==$id_crea || $allow_resp == 1) || dims_isadmin()) { //dims_isadmin() ||
		echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:document.form_affect.submit();","","");
	}
	echo "</form>";
}
echo '</div>';

//echo '<div style="float:left; width: 60%;">';
//if (isset($_SESSION['dims']['currentproject']) && $_SESSION['dims']['currentproject']>0 && ($_SESSION['dims']['userid']==$id_crea || $allow_resp == 1)) { //dims_isadmin() ||
//	require_once DIMS_APP_PATH.'modules/system/desktop_project_detail_contact.php';
//}
//echo '</div>';

//echo $skin->close_simplebloc();


global $dims_agenda_months;
global $dims_agenda_days;

//initialisation des tableaux utilisés par le datepicker
$full_months = '[';
$full_days = '[';

$min_month = '[';
$min_days = '[';
$mega_min_days = '[';

$i=0;
foreach($dims_agenda_months as $m){
	$full_months .= "'".$m."'";
	$min_month .= "'".utf8_encode(substr(html_entity_decode(utf8_decode($m)),0,3))."'";
	if($i< 11){
		$full_months .= ',';
		$min_month .= ',';
	}
	$i++;
}

$i=0;
foreach($dims_agenda_days as $d){
	$full_days .= "'".$d."'";
	$min_days .= "'".utf8_encode(substr(utf8_decode($d),0,3))."'";
	$mega_min_days .= "'".utf8_encode(substr(utf8_decode($d),0,2))."'";
	if($i< 6){
		$full_days .= ',';
		$mega_min_days .= ',';
		$min_days .= ',';
	}
	$i++;
}

$min_month .= ']';
$min_days .= ']';
$mega_min_days .= ']';
$full_months .= ']';
$full_days .= ']';
?>

<script type="text/javascript">
	$('document').ready(function(){

		$("#project_date_start").datepicker({
			buttonImage: './common/img/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			monthNames: <?php echo $full_months;?>,
			monthNamesShort: <?php echo $min_month;?>,
			dayNames: <?php echo $full_days;?>,
			dayNamesShort: <?php echo $min_days;?>,
			dayNamesMin: <?php echo $mega_min_days;?>
		});

		$("#project_date_end").datepicker({
			buttonImage: './common/img/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			monthNames: <?php echo $full_months;?>,
			monthNamesShort: <?php echo $min_month;?>,
			dayNames: <?php echo $full_days;?>,
			dayNamesShort: <?php echo $min_days;?>,
			dayNamesMin: <?php echo $mega_min_days;?>
		});
	}
   )
</script>
