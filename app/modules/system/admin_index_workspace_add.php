<?
echo $skin->open_simplebloc();

$token = new FormToken\TokenField;
$token->field("op",				"save_workspace");
$token->field("workspace_id",	"");
$token->field("parent_id",		$workspace->fields['id']);
$token->field("parent_parents", $workspace->fields['parents']);
$token->field("workspace_label");
$token->field("workspace_code");
$token->field("workspace_admin");
$token->field("workspace_admin_template");
$token->field("workspace_web");
$token->field("workspace_web_template");
$token->field("workspace_title");
$token->field("workspace_meta_description");
$token->field("workspace_meta_keywords");
$token->field("workspace_meta_author");
$token->field("workspace_meta_copyright");
$token->field("workspace_meta_robots");
$token->field("workspace_iprules");
$token->field("workspace_macrules");
$token->field("workspace_mustdefinerule");
$token->field("heritedmodule");

?>

<form name="form_workspace_add" action="<? echo $scriptenv; ?>" method="POST" onsubmit="javascript:return system_workspace_validate(this);">
<input type="hidden" name="op" value="save_workspace">
<?php echo $token->generate(); ?>
<input type="hidden" name="workspace_id" value="">
<input type="hidden" name="parent_id" value="<? echo $workspace->fields['id']; ?>">
<input type="hidden" name="parent_parents" value="<? echo $workspace->fields['parents']; ?>">

<div class="dims_form_title">
	<? echo $workspace->fields['label']; ?> &raquo;
	<?
	 echo $_DIMS['cste']['_DIMS_LABEL_WORKSPACE_ADD'];
	?>
</div>
<div class="dims_form" style="clear:both;padding:2px">
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:</label>
		<input type="text" class="text" name="workspace_label"  value="fils de <? echo $workspace->fields['label']; ?>">
	</p>
	<?
		$templatelist_back = dims_getavailabletemplates('backoffice');
		$templatelist_front = dims_getavailabletemplates('frontoffice');

		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
			?>
			<p>
				<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_CODE']; ?>:</label>
				<input type="text" class="text" name="workspace_code"  value="<? echo $workspace->fields['code']; ?>">
			</p>
			<?
		}
	?>
	<div class="dims_form_title">
		<? echo $workspace->fields['label']; ?> &raquo; <? echo $_DIMS['cste']['_DIMS_LABEL_ACCESS']; ?>
	</div>
	<div class="dims_form" style="clear:both;padding:2px">
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ADMIN']; ?>:</label>
			<input type="checkbox" name="workspace_admin" <? if($workspace->fields['admin']) echo "checked"; ?> value="1">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_SKIN']; ?>:</label>
			<select class="select" name="workspace_admin_template">
				<option value=""><? echo $_DIMS['cste']['_DIMS_NOE']; ?></option>
				<?
				foreach($templatelist_back as $index => $tpl_name)
				{
					$sel = ($tpl_name == $workspace->fields['admin_template']) ? 'selected' : '';
					echo "<option $sel>$tpl_name</option>";
				}
				?>
			</select>
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_WEB']; ?>:</label>
			<input type="checkbox" name="workspace_web" <? if($workspace->fields['web']) echo "checked"; ?> value="1">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_SKIN']; ?>:</label>
			<select class="select" name="workspace_web_template">
				<option value="">--</option>
				<?
				foreach($templatelist_front as $index => $tpl_name)
				{
					$sel = ($tpl_name == $workspace->fields['web_template']) ? 'selected' : '';
					echo "<option $sel>$tpl_name</option>";
				}
				?>
			</select>
		</p>
		<?
		if ($workspace->fields['web']) {
			// check if cms used in this workgroup
			$cmsready = false;
			if(isset($_SESSION['dims']['currentworkspace']['modules'])) {
				foreach($_SESSION['dims']['currentworkspace']['modules'] as $idm) {
					$mod=$dims->getModule($idm);
					if (isset($mod['label']) && $mod['label'] == 'cms') {$cmsready = true;}
				}
			}

			if (!$cmsready)
			{
				?>
				<div style="clear:both;float:right;margin:4px;padding:2px 4px;border:1px solid #c0c0c0;background-color:#f0f0f0;">
				<p class="dims_va">
					<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/attention.png">
					<span>Attention, pour pouvoir activer l'acces Frontoffice, vous devez creer une instance du module <a href="">WCE</a></span>
				</p>
				</div>
				<?
			}
		}
		?>
	</div>

	<a class="dims_form_title" href="javascript:dims_switchdisplay('system_meta');">
		<? echo $workspace->fields['label']; ?> &raquo; <? echo $_DIMS['cste']['_DIMS_LABEL_META']; ?>
	</a>
	<div class="dims_form" id="system_meta" style="clear:both;padding:2px;display:none;">
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?></label>
			<input type="text" class="text" name="workspace_title" value="<? echo $workspace->fields['title']; ?>">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']; ?></label>
			<input type="text" class="text" name="workspace_meta_description" value="<? echo $workspace->fields['meta_description']; ?>">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_WCE_KEYWORDS_META']; ?></label>
			<input type="text" class="text" name="workspace_meta_keywords" value="<? echo $workspace->fields['meta_keywords']; ?>">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_AUTHOR']; ?></label>
			<input type="text" class="text" name="workspace_meta_author" value="<? echo $workspace->fields['meta_author']; ?>">
		</p>
		<p>
			<label>Copyright:</label>
			<input type="text" class="text" name="workspace_meta_copyright" value="<? echo $workspace->fields['meta_copyright']; ?>">
		</p>
		<p>
			<label>Robots:</label>
			<input type="text" class="text" name="workspace_meta_robots" value="<? echo $workspace->fields['meta_robots']; ?>">
		</p>
	</div>

	<a class="dims_form_title" href="javascript:dims_switchdisplay('system_filtering');">
		<? echo $workspace->fields['label']; ?> &raquo; <? echo $_DIMS['cste']['_DIMS_LABEL_FILTERING']; ?>
	</a>
	<div class="dims_form" id="system_filtering" style="clear:both;padding:2px;display:none;">
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ALLOWEDIP']; ?>:</label>
			<input type="text" class="text" name="workspace_iprules"  value="<? echo $workspace->fields['iprules']; ?>">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ALLOWEDMAC']; ?>:</label>
			<input type="text" class="text" name="workspace_macrules"  value="<? echo $workspace->fields['macrules']; ?>">
		</p>
		<p>
			<label><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_MUSTDEFINERULE']; ?>:</label>
			<input type="checkbox" name="workspace_mustdefinerule" <? if($workspace->fields['mustdefinerule']) echo "checked"; ?> value="1">
		</p>
		<?
	?>
	</div>
	<div class="dims_form_title">
		<? echo $workspace->fields['label']; ?> &raquo; <? echo $_DIMS['cste']['_DIMS_LABEL_USEDMODULES']; ?>
	</div>
	<div class="dims_form" id="system_filtering" style="clear:both;padding:2px;">
		<?
		$child = new workspace();
		$child->fields['parents'] = $workspace->fields['parents'].';'.$workspace->fields['id'];
		$sharedmodules = $child->getsharedmodules(false);
		$heritedmodules = $child->getsharedmodules(true);
		$installedmodules = system_getinstalledmodules();
		?>
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<?
		echo 	"
			<TR CLASS=\"title\">
				<TD ALIGN=\"CENTER\" width=\"20\"></TD>
				<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
				<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
			</TR>
			";



		  foreach ($sharedmodules AS $instanceid => $instance)
		  {

			$checked = (isset($heritedmodules[$instanceid])) ? 'checked' : '';

			echo 	"
					<TR>
						<TD ALIGN=\"CENTER\"><input type=\"checkbox\" name=\"heritedmodule[]\" value=\"SHARED,$instanceid\" $checked></TD>
						<TD ALIGN=\"CENTER\">$instance[label]</TD>
						<TD ALIGN=\"CENTER\">$instance[description]</TD>
					</TR>
					";
			//echo "<option value=\"SHARED,$groupID,$instanceId\" class=\"listParentItem\">$instanceName</option>";
		  }

		  foreach ($installedmodules AS $index => $moduletype)
		  {

			echo 	"
				<TR>
					<TD ALIGN=\"CENTER\"><input type=\"checkbox\" name=\"heritedmodule[]\" value=\"NEW,{$moduletype['id']}\"></TD>
					<TD ALIGN=\"CENTER\">$moduletype[label]</TD>
					<TD ALIGN=\"CENTER\">$moduletype[description]</TD>
				</TR>
				";
			// Objet temporaire
			// $obj = NEW DIMS_MODULE($db->connection_id,$moduletype['instanceid']);
			// $moduleLabel = $obj->adminGetProperty('moduleLabel');
			//echo "<option value=\"NEW,$groupID,{$moduletype['id']}\">{$moduletype['label']}</option>";
		  }
		?>
		</TABLE>
	</div>
</div>


<div style="clear:both;float:right;padding:4px;">
    <?php
    echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'disk',"Javascript: forms.form_workspace_add.submit();");
    ?>
</div>
</form>
<? echo $skin->close_simplebloc(); ?>
