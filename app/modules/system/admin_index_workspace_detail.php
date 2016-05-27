<?

if ($father = $workspace->getfather())
{
		$parentlabel = $father->fields['label'];
		$parentid = $father->fields['id'];
}
else
{
		$parentlabel = 'Racine';
		$parentid = '';
}

$users = $workspace->getusers();
$nbusers = sizeof($users);

$workspaces = $workspace->getworkspacechildren(1);

$workspacelist = '';
foreach ($workspaces as $childid => $fields)
{
		if ($workspacelist!='') $workspacelist .= ' &#149; ';
		$workspacelist .= $fields['label'];
}

$templatelist_back = dims_getavailabletemplates('backoffice');
$templatelist_front = dims_getavailabletemplates('frontoffice');

$workspaces_parents = system_getallworkspaces($workspaceid);

$token = new FormToken\TokenField;
$token->field("op");
$token->field("workspace_id");
$token->field("workspace_label");
$token->field("workspace_code");
$token->field("workspace_id_lang");
$token->field("workspace_admin");
$token->field("workspace_admin_template");
$token->field("background");
$token->field("workspace_web");
$token->field("workspace_tickets");
$token->field("workspace_project");
$token->field("workspace_planning");
$token->field("workspace_share_info");
$token->field("workspace_contact");
$token->field("workspace_switchuser");
$token->field("workspace_id_tiers");
$token->field("workspace_contact_intel");
$token->field("workspace_contact_docs");
$token->field("workspace_contact_tags");
$token->field("workspace_contact_comments");
$token->field("workspace_contact_activeent");
$token->field("workspace_contact_outlook");
$token->field("workspace_newsletter");
$token->field("workspace_email");
$token->field("workspace_email_noreply");
$token->field("workspace_signature");
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
$token->field("workspace_title_appointment_val");
$token->field("workspace_title_appointment_rep");
$token->field("fck_workspace_validation_appointment");
$token->field("fck_workspace_content_appointment_rep");
$token->field("workspace_title_appointment_remind");
$token->field("fck_workspace_content_appointment_remind");
$token->field("workspace_id_workspace_feedback");
$token->field("workspace_background");
$tokenHTML = $token->generate();


?>
 <h3>
		<a href="#" tabindex="-1"><? echo $workspace->fields['label']; ?> &raquo; <?php  echo $_DIMS['cste']['_PROFIL']; ?></a>
</h3>
<div>

<div class="dims_form">
	<p>
			<?php echo $tokenHTML; ?>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?></label>
			<input type="text" class="text" name="workspace_label"  value="<?php echo $workspace->fields['label']; ?>">
	</p>
	<?php
		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
				?>
				<p>
						<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_CODE']; ?></label>
						<input type="text" class="text" name="workspace_code"  value="<?php echo $workspace->fields['code']; ?>">
				</p>
				<?php
				/*
				if (sizeof($workspaces_parents))
				{
						?>
						<p>
								<label><?php echo $_DIMS['cste']['_GROUP_FATHER']; ?>:</label>
								<select class="select" name="workspace_id_workspace">
										<option value="<?php echo dims_const::_DIMS_SYSTEMGROUP; ?>"></option>
										<?php
										foreach($workspaces_parents as $index => $fields)
										{
												if ($fields['id'] == $workspace->fields['id_workspace']) {$sel = 'selected';}
												else {$sel = '';}
												echo "<option $sel value=\"$fields[id]\">$fields[fullpath]</option>";
										}
										?>
						</p>
						<?php
				}
				*/
		}
		//gestion de la langue pour le workspace
		echo '<p>
						<label>'.$_DIMS['cste']['_DIMS_LABEL_LANG'].'</label>
						<select name="workspace_id_lang">';
		//on selectionne les langues
		$res_lg = $db->query("SELECT id, label FROM dims_lang");
		while($tab_l = $db->fetchrow($res_lg)) {
				if(isset($workspace->fields['id_lang']) && $workspace->fields['id_lang'] == $tab_l['id']) $sel = "selected=\"selected\"";
				else $sel = "";
				echo '<option value="'.$tab_l['id'].'" '.$sel.'>'.$tab_l['label'].'</option>';
		}

		echo '	</select>
				  </p>';
		?>
</div>
<?php
if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN) {
?>

	<div class="dims_form">
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ADMIN']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_admin" <?php if($workspace->fields['admin']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_SKIN']; ?></label>
				<select class="select" name="workspace_admin_template">
						<option value=""><?php echo $_DIMS['cste']['_DIMS_LABEL_UNDEFINED']; ?></option>
						<?php
						foreach($templatelist_back as $index => $tpl_name)
						{
								$sel = ($tpl_name == $workspace->fields['admin_template']) ? 'selected' : '';
								echo "<option $sel>$tpl_name</option>";
						}
						?>
				</select>
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_BACKGROUNDIMAGE']; ?>
				<?php
				if ($workspace->fields['background']!="") {
						echo "<span style=\"width:10%\" id=\"div_background\"><a href=\"javascript:void(0);\" onclick=\"confirmDelete();\"><img src=\"./common/img/delete.png\" alt=\"\"></a></span>";
						echo "<input type=\"hidden\" id=\"workspace_background\" name=\"workspace_background\" value=\"".$workspace->fields['background']."\">";
				}
				?>
				</label>
				<input type="file" class="text" name="background" id="background" />
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ADMINDOMAINLIST']; ?>:</label>
				<?php
				$res=$db->query("SELECT dims_domain.*
								FROM dims_domain
								INNER JOIN dims_workspace_domain
								ON dims_workspace_domain.id_domain=dims_domain.id
								AND id_workspace= :workspaceid
								AND (dims_workspace_domain.access=0 OR dims_workspace_domain.access=2)",
								array(':workspaceid' => $workspace->fields['id'] ));

				if ($db->numrows($res)>0) {
						echo "<span style=\"float:left;width:55%;font-weight:bold;\">";
						while ($dom=$db->fetchrow($res)) {
								if ($dom['domain']!="") echo $dom['domain']."<br>";
						}
						echo "</span>";
				}
				?>
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_WEB']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_web" <?php if($workspace->fields['web']) echo "checked"; ?> value="1">
		</p>
		<!--p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_SKIN']; ?></label>
				<select class="select" name="workspace_web_template">
						<option value=""><?php
						//echo $_DIMS['cste']['_DIMS_NONE'];
						?></option>
						<?php
						foreach($templatelist_front as $index => $tpl_name) {
								$sel = ($tpl_name == $workspace->fields['web_template']) ? 'selected' : '';
								echo "<option $sel>$tpl_name</option>";
						}
						?>
				</select>
		</p-->
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_GROUP_WEBDOMAINLIST']; ?></label>
				<?php
				$res=$db->query("SELECT dims_domain.*
								FROM dims_domain
								INNER JOIN dims_workspace_domain
								ON dims_workspace_domain.id_domain=dims_domain.id
								AND id_workspace= :workspaceid
								AND (dims_workspace_domain.access=1 OR dims_workspace_domain.access=2)",
								array(':workspaceid' => $workspace->fields['id']));

				if ($db->numrows($res)>0) {
						echo "<span style=\"float:left;width:55%;font-weight:bold;\">";
						while ($dom=$db->fetchrow($res)) {
								if ($dom['domain']!="") echo $dom['domain']."<br>";
						}
						echo "</span>";
				}
				?>

		<?php
		if ($workspace->fields['web']) {
			// check if wce used in this workgroup
			$wceready = false;
			$res=$db->query("SELECT dims_module.id
									from 		dims_module
									inner join 	dims_module_type
									on			dims_module_type.id=dims_module.id_module_type
									and			dims_module_type.label='wce'
									and			dims_module.id_workspace= :workspaceid ",
								array(':workspaceid' => $workspace->fields['id']));

			if ($db->numrows($res)>=1) $wceready =true;

			if (!$wceready) {
				?>
				<div style="clear:both;float:right;margin:4px;padding:2px 4px;border:1px solid #c0c0c0;background-color:#f0f0f0;">
				<p class="dims_va">
						<img src="<?php echo $_SESSION['dims']['template_path']; ?>/img/system/attention.png">
						<span><?php echo $_DIMS['cste']['_SYSTEM_LABEL_ALERT_WCE']; ?></span>
				</p>
				</div>
				<?php
			}
		}
		?>
		</p>
		<p>
		<?
		$sql=   "SELECT *
				FROM dims_workspace_template
				WHERE id_workspace= :workspaceid ";

		$res=$db->query($sql, array(':workspaceid' => $workspace->fields['id']));

		// collecte la liste des templates disponibles
		$availabletpl = dims_getavailabletemplates();

		echo "<div style=\"width:40%;text-align:right;display:block;float:left\">
				".$_DIMS['cste']['_DIMS_LABEL_TEMPLATEWORKSPACE_LIST'].":<br>";

		if (dims_isadmin() || dims_ismanager())  {
				echo "<a href=\"javascript:void(0);\" onclick=\"displayTemplateInfo(event,".$workspace->fields['id'].")\"><b>".$_DIMS['cste']['_MODIFY']."</b></a>";
		}

		echo "</div><div style=\"width:50%;text-align:left;display:block;float:right\">";

		while ($f=$db->fetchrow($res)) {
				if (in_array($f['template'],$availabletpl)) {
						echo "<b>".$f['template']."</b><br>";
				}
		}
		echo "</div>";
		?>
		</p>
	</div>
</div>
<h3>
		<a href="#" tabindex="-1" ><? echo $workspace->fields['label']; ?> &raquo; <?php echo $_DIMS['cste']['_DIMS_OPTIONS']; ?></a>
</h3>
<div>
	<div class="dims_form">
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_ACTIVE_TICKETS']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_tickets" <?php if($workspace->fields['tickets']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_PROJECT']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_project" <?php if($workspace->fields['project']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_PLANNING']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_planning" <?php if($workspace->fields['planning']) echo "checked"; ?> value="1">
				<?php
				if (dims_isadmin() && $workspace->fields['newsletter']) {
					echo "<a href=\"javascript:void(0);\" onclick=\"displayEventsDetails(event,".$workspace->fields['id'].")\"><b>".$_DIMS['cste']['_MODIFY']."</b></a>";
				}
				?>
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_SHARE_PLANNING']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_share_info" <?php if($workspace->fields['share_info'] == 1 || !isset($workspace->fields['share_info'])) echo "checked"; ?> value="1">
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_contact" <?php if($workspace->fields['contact']) echo "checked"; ?> value="1" onclick="javascript:document.form_workspace.submit();">
				<?php
				if (dims_isadmin() && $workspace->fields['contact']) {
					echo "<a href=\"javascript:void(0);\" onclick=\"displayShareObject(event,".$workspace->fields['id'].",".dims_const::_SYSTEM_OBJECT_CONTACT.")\"><b>".$_DIMS['cste']['_SHARE']."</b></a>";
				}
				?>
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_CONNECT_WITH_OTHERS_ACCOUNTS']; ?></label>
				<input style="width:16px;" type="checkbox" name="workspace_switchuser" <?php if($workspace->fields['switchuser'] == 1) echo "checked"; ?> value="1">
		</p>
		<?php
		if (dims_isadmin() && $workspace->fields['contact']) {
		?>
		<p>
			<label></label>
			<?php echo $_DIMS['cste']['_DIMS_LABEL_ENT_FICHE'];

					// on ajoute l'association de l'espace de travail avec l'entite
					echo "<select name=\"workspace_id_tiers\" style=\"width:250px;\">";

					$res=$db->query("SELECT id,intitule FROM dims_mod_business_tiers ORDER BY intitule");
					if ($db->numrows($res)>0) {
							while ($tiers=$db->fetchrow($res)) {
									if ($tiers['id']==$workspace->fields['id_tiers']) {
											$sel="selected";
									}
									else {
											$sel="";
									}
									echo "<option $sel value=\"".$tiers['id']."\">".dims_strcut($tiers['intitule'],45)."</option>";
							}
					}

					echo "</select>";
			?>
		</p>
		<p>
				<label></label>
				<?php echo $_DIMS['cste']['_DIMS_OPTIONS']." ".$_DIMS['cste']['_DIMS_LABEL_LINKS']; ?>
				<input style="width:16px;" type="checkbox" name="workspace_contact_intel" <?php if($workspace->fields['contact_intel']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label></label>
				<?php echo $_DIMS['cste']['_DIMS_OPTIONS']." ".$_DIMS['cste']['_DOCS']; ?>
				<input style="width:16px;" type="checkbox" name="workspace_contact_docs" <?php if($workspace->fields['contact_docs']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label></label>
				<?php echo $_DIMS['cste']['_DIMS_OPTIONS']." ".$_DIMS['cste']['_DIMS_LABEL_TAGS']; ?>
				<input style="width:16px;" type="checkbox" name="workspace_contact_tags" <?php if($workspace->fields['contact_tags']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label></label>
				<?php echo $_DIMS['cste']['_DIMS_OPTIONS']." ".$_DIMS['cste']['_DIMS_COMMENTS']; ?>
				<input style="width:16px;" type="checkbox" name="workspace_contact_comments" <?php if($workspace->fields['contact_comments']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label></label>
				<?php echo $_DIMS['cste']['_DIMS_OPTIONS']." ".$_DIMS['cste']['_DIMS_LABEL_COMPANY']; ?>
				<input style="width:16px;" type="checkbox" name="workspace_contact_activeent" <?php if($workspace->fields['contact_activeent']) echo "checked"; ?> value="1">
		</p>
		<p>
				<label></label>
				<?php echo $_DIMS['cste']['_DIMS_OPTIONS']." ".$_DIMS['cste']['_LABEL_IMPORT_OUTLOOK']; ?>
				<input style="width:16px;" type="checkbox" name="workspace_contact_outlook" <?php if($workspace->fields['contact_outlook']) echo "checked"; ?> value="1">
		</p>
		<?php
		}
		?>
		<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']; ?></label>
			<input style="width:16px;" type="checkbox" name="workspace_newsletter" <?php if($workspace->fields['newsletter']) echo "checked"; ?> value="1">
			<?php
					if (dims_isadmin() && $workspace->fields['newsletter']) {
							echo "<a href=\"javascript:void(0);\" onclick=\"displayNewsletterDetails(event,".$workspace->fields['id'].")\"><b>".$_DIMS['cste']['_MODIFY']."</b></a>";

							echo "&nbsp;/&nbsp;";
							echo "<a href=\"javascript:void(0);\" onclick=\"displayShareObject(event,".$workspace->fields['id'].",".dims_const::_SYSTEM_OBJECT_NEWSLETTER.")\"><b>".$_DIMS['cste']['_SHARE']."</b></a>";



					}
			?>
		</p>
		<?

		?>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?></label>
				<input type="text" class="text" name="workspace_email"  value="<?php echo $workspace->fields['email']; ?>">
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL_NOREPLY']; ?></label>
				<input type="text" class="text" name="workspace_email_noreply"  value="<?php echo $workspace->fields['email_noreply']; ?>">
		</p>
		<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_SIGNATURE']; ?></label>
				<textarea type="text" class="text" name="workspace_signature"><?php echo $workspace->fields['signature']; ?></textarea>
		</p>

		<p>
			<label>FeedBack</label>
			<select class="select" name="workspace_id_workspace_feedback">
					<option value=""><?php echo $_DIMS['cste']['_DIMS_LABEL_UNDEFINED']; ?></option>
					<?php
					if ($dims->isAdmin()) $arrayw=$dims->getAllWorkspaces();
					else $arrayw=$dims->getAdminWorkspaces();

					foreach($arrayw as $key => $value) {
							$sel = ($key == $workspace->fields['id_workspace_feedback']) ? 'selected' : '';
							echo "<option $sel value='".$key."'>".$value['label']."</option>";
					}
					?>
			</select>
		</p>
	</div>
</div>
<?
}
else {
	echo "</div>";
}
?>
