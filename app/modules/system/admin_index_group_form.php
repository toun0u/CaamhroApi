<FORM NAME="form_modify_group" ACTION="<? echo $scriptenv ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",			"save_group");
	$token->field("group_id",	$org_id);
	$token->field("workspacegroup_adminlevel");
	$token->field("workspacegroup_id_profile");
	$token->field("workspacegroup_activesearch");
	$token->field("workspacegroup_activeticket");
	$token->field("workspacegroup_activeprofil");
	$token->field("workspacegroup_activeannot");
	$token->field("workspacegroup_activecontact");
	$token->field("workspacegroup_activeproject");
	$token->field("workspacegroup_activeplanning");
	$token->field("workspacegroup_activenewsletter");
	$token->field("workspacegroup_activeevent");
	$token->field("workspacegroup_activeeventemail");
	$token->field("workspacegroup_activeeventstep");
	$token->field("workspacegroup_activeswitchuser");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_group">
<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<? echo $org_id; ?>">
<div class="dims_form" style="float:left;width:100%;">
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?>:&nbsp;</label>
		<SELECT class="select" NAME="workspacegroup_adminlevel">
		<?php

		foreach ($dims_system_levels as $id => $label)
		{

			if ($id <= $_SESSION['dims']['adminlevel'])
			{

				$sel = ($workspace_group->fields['adminlevel'] == $id) ? 'selected' : '';
				echo "<option $sel value=\"$id\">$label</option>";
			}
		}
		?>
		</SELECT>
	</p>
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_USER_PROFILE']; ?>:&nbsp;</label>
			<SELECT class="select" NAME="workspacegroup_id_profile">
			<?
			$ownprofiles = $workspace->getprofiles();

			echo "<option value=\"-1\" >".$_DIMS['cste']['_DIMS_LABEL_UNDEFINED']."</option>";

			foreach ($ownprofiles as $id => $profile)
			{
				$sel = ($workspace_group->fields['id_profile'] == $profile['id']) ? 'selected' : '';
				echo "<option $sel value=\"{$id}\">{$profile['label']}</option>";
			}

			?>
		</SELECT>
	</p>
	<p>
		<label><? echo $_DIMS['cste']['_SEARCH']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activesearch" <? echo ($workspace_group->fields['activesearch']) ? "checked" : ""; ?>>
	</p>
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_TICKET']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeticket" <? echo ($workspace_group->fields['activeticket']) ? "checked" : ""; ?>>
	</p>
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_PROFIL']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeprofil" <? echo ($workspace_group->fields['activeprofil']) ? "checked" : ""; ?>>
	</p>
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_ANNOT']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeannot" <? echo ($workspace_group->fields['activeannot']) ? "checked" : ""; ?>>
	</p>
	<p>
		<label><?php echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activecontact" <?php echo ($workspace_group->fields['activecontact']) ? "checked" : ""; ?>>
	</p>
	<p>
		<label><?php echo $_DIMS['cste']['_DIMS_LABEL_PROJECT']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeproject" <?php echo ($workspace_group->fields['activeproject']) ? "checked" : ""; ?>>
	</p>
	<p>
		<label><?php echo $_DIMS['cste']['_PLANNING']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeplanning" <?php echo ($workspace_group->fields['activeplanning']) ? "checked" : ""; ?>>
	</p>
        <p>
		<label><?php echo $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activenewsletter" <?php echo ($workspace_group->fields['activenewsletter']) ? "checked" : ""; ?>>
	</p>
	 <p>
		<label><?php echo "Admin. ".$_DIMS['cste']['_DIMS_LABEL_EVENTS']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeevent" <?php echo ($workspace_group->fields['activeevent']) ? "checked" : ""; ?>>
	</p>
    <p>
        <label><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL_REGISTRATION']; ?>:</label>
        <input type="checkbox" name="workspacegroup_activeeventemail" <?php echo ($workspace_group->fields['activeeventemail']) ? "checked" : ""; ?>>
   </p>
   <p>
		<label><?php echo $_DIMS['cste']['_DIMS_EVT_ALLOW_FO']; ?>:</label>
		<input type="checkbox" name="workspacegroup_activeeventstep" <?php echo ($workspace_group->fields['activeeventstep']) ? "checked" : ""; ?>>
	</p>
	<p>
    <p>
            <label><?php echo $_DIMS['cste']['_CONNECT_WITH_OTHERS_ACCOUNTS']; ?></label>
            <input style="width:16px;" type="checkbox" name="workspacegroup_activeswitchuser" <?php if($workspace_group->fields['activeswitchuser'] == 1) echo "checked"; ?>>
    </p>
    <?php
        echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:forms.form_modify_group.submit();");
    ?>
	</p>
</div>
</FORM>
