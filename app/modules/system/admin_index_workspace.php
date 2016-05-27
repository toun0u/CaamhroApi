<script language="javascript">
function confirmDelete() {
	if (confirm('<?php echo addslashes($_DIMS['cste']['_DIMS_CONFIRM']);?>')) {
		document.getElementById("workspace_background").value="";
		document.getElementById("div_background").innerHTML="";
	}
}

function deleteWorkspaceShareObject(id_workspace,id_object,id_to) {
	if (confirm('<?php echo addslashes($_DIMS['cste']['_DIMS_CONFIRM']);?>')) {
		dims_xmlhttprequest_todiv('admin.php','dims_op=shareobject_delete&id_workspace='+id_workspace+'&id_to='+id_to+'&id_object='+id_object+"&deleteshareobjectworkspace=1",'','dims_popup');
	}
}

function updateWorkspaceShareObject(id_workspace,id_object,id_to,value,reverse) {
	dims_xmlhttprequest_todiv('admin.php','dims_op=shareobject_view&id_workspace='+id_workspace+'&id_to='+id_to+'&id_object='+id_object+'&value='+value+"&saveshareobjectworkspace=1&reverse="+reverse,'','dims_popup');
}

function displayNewsletterDetails(event,id_workspace) {
	dims_showcenteredpopup("",700,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=details_newsletter&id_workspace='+id_workspace,'','dims_popup');
}

function displayEventsDetails(event,id_workspace) {
	dims_showcenteredpopup("",700,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=details_events&id_workspace='+id_workspace,'','dims_popup');
}

</script>
<?php
echo $skin->open_simplebloc('','width:100%');

if (isset($_POST['tplworkspace'])) {

	// collecte des existants
	$arraytpl=array();
	$tplexists=array();
	$lsttpl="";

	// collecte des nouveaux coches
	if (isset($_POST['seltpl'])) {
		$seltpl = dims_load_securvalue('seltpl', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		foreach ($seltpl as $tpl) {
			if ($lsttpl==="") $lsttpl="\"".addslashes($tpl)."\"";
			else $lsttpl.=",\"".addslashes($tpl)."\"";

			$arraytpl[]=$tpl;
		}
	}

	if ($lsttpl!="") {
		// construction des existants
		$res=$db->query("select template from dims_workspace_template where id_workspace= :idworkspace ", array(':idworkspace' => $workspaceid) );

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$tplexists[$f['template']]=$f['template'];
			}
		}

		// on supprime les templates d�coches
		$res=$db->query("delete from dims_workspace_template where id_workspace= :idworkspace and template not in ($lsttpl)", array(':idworkspace' => $workspaceid) );
	}
	else $db->query("delete from dims_workspace_template where id_workspace= :idworkspace ", array(':idworkspace' => $workspaceid) );

	// on ajoute le tpl courant
	foreach ($arraytpl as $tpl) {
		if (!isset($tplexists[$tpl]))
			$res=$db->query("insert into dims_workspace_template set id_workspace= :idworkspace , template= :template ", array(':idworkspace' => $workspaceid, ':template' => addslashes($tpl)) );
	}

}

?>

<div style="overflow:auto;float: left;width:75%;">
	<form id="form_workspace" name="form_workspace" action="<?php echo $scriptenv; ?>" method="POST" enctype="multipart/form-data" onsubmit="javascript:return system_workspace_validate(this);">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",				"save_workspace");
		$token->field("workspace_id",	$workspace->fields['id']);
		$token->field("workspace_title");
		$token->field("workspace_meta_description");
		$token->field("workspace_meta_keywords");
		$token->field("workspace_meta_author");
		$token->field("workspace_meta_copyright");
		$token->field("workspace_meta_robots");
		$token->field("workspace_ssl");
		$token->field("workspace_iprules");
		$token->field("workspace_macrules");
		$token->field("workspace_mustdefinerule");
		$token->field("workspace_email_appointment");
		$token->field("workspace_title_appointment");
		$token->field("fck_workspace_content_appointment");
		$token->field("workspace_title_appointment");
		$token->field("fck_workspace_validation_appointment");
		$token->field("workspace_title_appointment_rep");
		$token->field("fck_workspace_content_appointment_rep");
		$token->field("workspace_title_appointment_remind");
		$token->field("fck_workspace_content_appointment_remind");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<input type="hidden" name="op" value="save_workspace">
	<input type="hidden" name="workspace_id" value="<?php echo $workspace->fields['id']; ?>">

		<div id="accordion" class="ui-accordion ui-widget ui-helper-reset ui-accordion-icons">
				<?
				require_once (DIMS_APP_PATH . '/modules/system/admin_index_workspace_detail.php');

				if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN) {
				?>

			<h3><a href="#">
			<?php echo $workspace->fields['label']; ?> &raquo; <?php echo $_DIMS['cste']['_DIMS_LABEL_META']; ?></a></h3>
			<div>
				<div class="dims_form" id="system_meta" >
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE'];?></label>
						<input type="text" class="text" name="workspace_title" value="<?php echo $workspace->fields['title']; ?>">
					</p>
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'];?></label>
						<input type="text" class="text" name="workspace_meta_description" value="<?php echo $workspace->fields['meta_description']; ?>">
					</p>
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_KEYWORDS'];?></label>
						<input type="text" class="text" name="workspace_meta_keywords" value="<?php echo $workspace->fields['meta_keywords']; ?>">
					</p>
					<p>
						<label><?php echo $_DIMS['cste']['_AUTHOR'];?></label>
						<input type="text" class="text" name="workspace_meta_author" value="<?php echo $workspace->fields['meta_author']; ?>">
					</p>
					<p>
						<label>Copyright</label>
						<input type="text" class="text" name="workspace_meta_copyright" value="<?php echo $workspace->fields['meta_copyright']; ?>">
					</p>
					<p>
						<label>Robots</label>
						<input type="text" class="text" name="workspace_meta_robots" value="<?php echo $workspace->fields['meta_robots']; ?>">
					</p>
				</div>
			</div>
			<h3>
				<a href="#" tabindex="-1"><? echo $workspace->fields['label']; ?> &raquo; <?php echo $_DIMS['cste']['_DIMS_LABEL_FILTERING']; ?></a>
			</h3>
			<div>
				<div class="dims_form" id="system_filtering">
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_SSL']; ?></label>
						<input style="width:16px;" type="checkbox" name="workspace_ssl" <?php if($workspace->fields['ssl']) echo "checked"; ?> value="1">
					</p>
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ALLOWEDIP']; ?>:</label>
						<input type="text" class="text" name="workspace_iprules"  value="<?php echo $workspace->fields['iprules']; ?>">
					</p>
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ALLOWEDMAC']; ?>:</label>
						<input type="text" class="text" name="workspace_macrules"  value="<?php echo $workspace->fields['macrules']; ?>">
					</p>
					<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_MUSTDEFINERULE']; ?>:</label>
						<input type="checkbox" name="workspace_mustdefinerule" <?php if($workspace->fields['mustdefinerule']) echo "checked"; ?> value="1">
					</p>
				</div>
			</div>
			<h3><a href="#">
			<?php echo $workspace->fields['label']; ?> &raquo; <?php echo $_DIMS['cste']['_BUSINESS_ACTION']; ?></a></h3>
			<div>
				<div class="dims_form" id="system_appointment" >
					<p>
						<label><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL'];?></label>
						<input type="text" class="text" name="workspace_email_appointment" value="<?php echo $workspace->fields['email_appointment']; ?>">
					</p>
					<p>
						Tags disponibles : {FIRSTNAME} {LASTNAME} {EMAIL} {URL} {RDV}
					</p>
					<p>
						<? if (trim($workspace->fields['title_appointment']) == '') $workspace->fields['title_appointment'] = '{FIRSTNAME} {LASTNAME} vous invite choisir la date de l\'événement : {RDV}'; ?>
						<label><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE'];?></label>
						<input type="text" class="text" name="workspace_title_appointment" value="<?php echo $workspace->fields['title_appointment']; ?>">
					</p>
					<p>
						<? if (trim($workspace->fields['content_appointment']) == '') $workspace->fields['content_appointment'] = 'Bonjour,<br />
<br />
{FIRSTNAME} {LASTNAME} ({EMAIL}) souhaite d&eacute;finir une date pour l&#39;&eacute;v&eacute;nement &quot;{RDV}&quot;.<br />
Pour plus d&#39;informations et soumettre vos disponibilit&eacute;s veuillez suivre le lien suivant : {URL}.<br />
<br />
Cordialement.<br />
<div style="text-align: right;">
	{FIRSTNAME} {LASTNAME}</div>'; ?>
						<label><?php echo $_SESSION['cste']['_CONTENT']; ?> : invitation</label>
						<textarea id="fck_workspace_content_appointment" name="fck_workspace_content_appointment"><?php echo $workspace->fields['content_appointment']; ?></textarea>
					</p>
					<p>
						Tags disponibles : {FIRSTNAME} {LASTNAME} {EMAIL} {URL} {RDV} {DATE}
					</p><p>
						<? if (trim($workspace->fields['title_appointment_val']) == '') $workspace->fields['title_appointment_val'] = '{FIRSTNAME} {LASTNAME} a validé la date de l\'événement : {RDV} au {DATE}'; ?>
						<label><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE'];?></label>
						<input type="text" class="text" name="workspace_title_appointment_val" value="<?php echo $workspace->fields['title_appointment_val']; ?>">
					</p>
					<p>
						<? if (trim($workspace->fields['validation_appointment']) == '') $workspace->fields['validation_appointment'] = 'Bonjour,<br />
<br />
{FIRSTNAME} {LASTNAME} ({EMAIL}) a fix&eacute; la date de l&#39;&eacute;v&eacute;nement &quot;{RDV}&quot; au {DATE}.<br />
Vous retrouverez toutes les informations en suivant le lien suivant : {URL}.<br />
<br />
Cordialement.<br />
<div style="text-align: right;">
	{FIRSTNAME} {LASTNAME}</div>'; ?>
						<label><?php echo $_SESSION['cste']['_CONTENT']; ?> : validation</label>
						<textarea id="fck_workspace_validation_appointment" name="fck_workspace_validation_appointment"><?php echo $workspace->fields['validation_appointment']; ?></textarea>
					</p>
					<p>
						Tags disponibles : {NAME} {URL} {RDV} {DATES}
					</p><p>
						<? if (trim($workspace->fields['title_appointment_rep']) == '') $workspace->fields['title_appointment_rep'] = '{NAME} a répondu à l\'événement : {RDV}'; ?>
						<label><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE'];?></label>
						<input type="text" class="text" name="workspace_title_appointment_rep" value="<?php echo $workspace->fields['title_appointment_rep']; ?>">
					</p>
					<p>
						<? if (trim($workspace->fields['content_appointment_rep']) == '') $workspace->fields['content_appointment_rep'] = 'Bonjour,<br />
<br />
{NAME} a r&eacute;pondu &agrave; l&#39;&eacute;v&eacute;nement &quot;{RDV}&quot; :<br />
{DATES}<br />
Vous retrouverez toutes les informations en suivant le lien suivant : {URL}.<br />
<br />
Cordialement.<br />
<div style="text-align: right;">
	{NAME}</div>'; ?>
						<label><?php echo $_SESSION['cste']['_CONTENT']; ?> : validation</label>
						<textarea id="fck_workspace_content_appointment_rep" name="fck_workspace_content_appointment_rep"><?php echo $workspace->fields['content_appointment_rep']; ?></textarea>
					</p>
					<p>
						Tags disponibles : {NAME} {URL} {RDV} {DATES}
					</p><p>
						<? if (trim($workspace->fields['title_appointment_remind']) == '') $workspace->fields['title_appointment_remind'] = 'Vous n\'avez pas répondu à l\'événement : {RDV}'; ?>
						<label><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE'];?></label>
						<input type="text" class="text" name="workspace_title_appointment_remind" value="<?php echo $workspace->fields['title_appointment_remind']; ?>">
					</p>
					<p>
						<? if (trim($workspace->fields['content_appointment_remind']) == '') $workspace->fields['content_appointment_remind'] = 'Bonjour,<br />
<br />
Vous n\'avez pas r&eacute;pondu &agrave; l&#39;&eacute;v&eacute;nement &quot;{RDV}&quot; :<br />
{DATES}<br />
Vous retrouverez toutes les informations en suivant le lien suivant : {URL}.<br />
<br />
Cordialement.<br />
<div style="text-align: right;">
	{FIRSTNAME} {LASTNAME}</div>'; ?>
						<label><?php echo $_SESSION['cste']['_CONTENT']; ?> : validation</label>
						<textarea id="fck_workspace_content_appointment_remind" name="fck_workspace_content_appointment_remind"><?php echo $workspace->fields['content_appointment_remind']; ?></textarea>
					</p>
					<script type="text/javascript" src="/common/js/ckeditor/ckeditor.js"></script>
					<script type="text/javascript">
						$(document).ready(function(){
							var instance=CKEDITOR.replace('fck_workspace_content_appointment',
								{
									customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
									stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
									contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
								});
							var instance2=CKEDITOR.replace('fck_workspace_validation_appointment',
								{
									customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
									stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
									contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
								});
							var instance3=CKEDITOR.replace('fck_workspace_content_appointment_rep',
								{
									customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
									stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
									contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
								});
							var instance4=CKEDITOR.replace('fck_workspace_content_appointment_remind',
								{
									customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
									stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
									contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
								});
						});
					</script>
				</div>
			</div>
		</div>
				<?php
		}
			?>

		<div style="clear:both;float:right;padding:4px;">
			<? echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","form_workspace.submit()"); ?>
		</div>

		<div style="clear:both;float:right;padding:4px;">
		</div>
	</form>
</div>

<div style="float:right:width:18%;">
		<div class="">
			<?php
			if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN) {
			$toolbar_workspace[] = array(
									'title'		=> str_replace('<LABEL>','<br /><b>'.$childworkspace.'</b>', $_DIMS['cste']['_DIMS_LABEL_CREATE_CHILD_WORKSPACE']),
									'url'		=> "$scriptenv?op=child&gworkspaceid=$workspaceid",
									'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_workspace_child.png",
								);
			}

			$toolbar_workspace[] = array(
									'title'		=> $_DIMS['cste']['_DIMS_LABEL_CREATE_GROUP'],
									'url'		=> "$scriptenv?op=groupchild&workspaceid=$workspaceid",
									'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group_child.png",
								);

			$sizeof_workspaces = sizeof($workspace->getworkspacechildrenlite());
			$sizeof_users = $workspace->getNbUsers();

			if ($_SESSION['dims']['adminlevel'] == dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
				// delete button if group not protected and no children
				if (!$workspace->fields['protected'] && !$sizeof_workspaces && !$sizeof_users) {
					$toolbar_workspace[] = array(
											'title'		=> str_replace('<LABEL>','<br /><b>'.$currentworkspace.'</b>', $_DIMS['cste']['_DIMS_LABEL_DELETE_WORKSPACE']),
											'url'		=> "$scriptenv?op=delete&workspaceid=$workspaceid",
											'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_workspace_delete.png",
										);
				}
				else {
					if ($sizeof_workspaces || $sizeof_users) {
						$msg = '';
						if ($sizeof_workspaces) $msg = $_DIMS['cste']['_SYSTEM_MSG_INFODELETE_GROUPS'];
						elseif ($sizeof_users) $msg = $_DIMS['cste']['_SYSTEM_MSG_INFODELETE_USERS'];

						$toolbar_workspace[] = array(
												'title'		=> str_replace('<LABEL>','<br /><b>'.$currentworkspace.'</b>', $_DIMS['cste']['_DIMS_LABEL_DELETE_WORKSPACE']),
												'url'		=> $scriptenv,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_workspace_delete_gray.png",
												'confirm'	=> $msg
											);

					}
				}
			}

			echo $skin->create_menu($toolbar_workspace, $x, false, true);
			?>
		</div>
	</div>

<?php
echo $skin->close_simplebloc();

echo $skin->open_simplebloc('', 'width:100%');
require_once DIMS_APP_PATH.'include/functions/annotations.php';
dims_annotation(dims_const::_SYSTEM_OBJECT_WORKSPACE, $workspace->fields['id'], $workspace->fields['label']);
echo $skin->close_simplebloc();
?>
<script language="javascript"> $("#accordion").accordion({ autoHeight: false }); </script>
