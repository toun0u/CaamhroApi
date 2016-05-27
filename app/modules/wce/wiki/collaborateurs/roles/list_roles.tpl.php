<?php
$module = new module();
$module->open($_SESSION['dims']['moduleid']);
$lstRoles = $module->getroles(array($_SESSION['dims']['workspaceid']));
?>
<h4><? echo $_SESSION['cste']['_LIST_OF_ROLES']; ?></h4>
<?
if ($actionR = dims_isactionallowed(module_wiki::_ACTION_ADMIN_ROLES)){
?>
<div class="cadre_article">
	<a style="float: right;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R."&action=".module_wiki::_ACTION_EDIT_ROLES); ?>">
		<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_add.png'); ?>" />
		<? echo $_SESSION['cste']['_DIMS_LABEL_ROLEADD']; ?>
	</a>
</div>
<? } ?>
<div class="table_article">
	<table cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th>
				<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
			</th>
		</tr>
	<?
	foreach($lstRoles as $role){
		?>
		<tr>
			<td>
				<? echo $role['label']; ?>
			</td>
			<td>
				<? echo $role['description']; ?>
			</td>
			<td class="actions">
				<? if($actionR){ ?>
				<img onclick="document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R."&action=".module_wiki::_ACTION_EDIT_ROLES."&id=".$role['id']); ?>';" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_edit.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
				<? } ?>
			</td>
		</tr>
		<?
	}
	?>
	</table>
</div>