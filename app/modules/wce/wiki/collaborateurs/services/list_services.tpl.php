<?php
$lstGr = module_wiki::getGrDispo();
?>
<h4><? echo $_SESSION['cste']['LIST_OF_SERVICES']; ?></h4>
<?
if ($actionR = dims_isactionallowed(module_wiki::_ACTION_ADMIN_SERVICES)){
?>
<div class="cadre_article">
	<a style="float: right;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_S."&action=".module_wiki::_ACTION_EDIT_SERVICE); ?>">
		<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_add.png'); ?>" />
		<? echo $_SESSION['cste']['_OEUVRE_ADD_A_DEPARTMENT']; ?>
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
				<? echo $_SESSION['cste']['_OEUVRE_NUMBER_OF_ASSOCIATED_ACCOUNTS']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_OEUVRE_ASSOCIATED_ROLES']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
			</th>
		</tr>
	<?
	foreach($lstGr as $gr){
		?>
		<tr>
			<td>
				<? echo $gr->fields['label']; ?>
			</td>
			<td style="text-align: center;">
				<? echo $gr->getNbUsers(); ?>
			</td>
			<td>
				<ul>
					<?
					$lstRoles = $gr->getRoles(array($_SESSION['dims']['workspaceid']));
					foreach($lstRoles as $role){
						?>
						<li><? echo $role->fields['label']; ?></li>
						<?
					}
					?>
				</ul>
			</td>
			<td class="actions">
				<img onclick="document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_S."&action=".module_wiki::_ACTION_EDIT_SERVICE."&id=".$gr->fields['id']); ?>';" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_edit.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
			</td>
		</tr>
		<?
	}
	?>
	</table>
</div>