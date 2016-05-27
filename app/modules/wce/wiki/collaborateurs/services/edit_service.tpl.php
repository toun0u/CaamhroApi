<?php
$module = new module_wiki();
$module->open($_SESSION['dims']['moduleid']);
$lstRolesGlobal = $module->getroles(array($_SESSION['dims']['workspaceid']));
$lstRoles = $this->getRoles(array($_SESSION['dims']['workspaceid']));

$title = $_SESSION['cste']['_OEUVRE_CREATION_OF_A_DEPARTMENT'];
if($this->fields['id'] != '' && $this->fields['id'] > 0)
	$title = $_SESSION['cste']['_OEUVRE_EDITION_OF_A_DEPARTMENT']." : ".$this->fields['label'];
?>
<h4><? echo $title; ?></h4>
<div style="margin-top:10px;">
	<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_S."&action=".module_wiki::_ACTION_SAVE_SERVICE); ?>">
		<input type="hidden" name="id_service" value="<? echo $this->fields['id']; ?>" />
		<table cellpadding="0" cellspacing="0" class="form_user">
			<tr>
				<td class="label">
					<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>&nbsp;:
				</td>
				<td>
					<input type="text" name="service_label" value="<? echo $this->fields['label']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="label" style="vertical-align: top;">
					<? echo $_SESSION['cste']['_DIMS_LABEL_ROLE_LIST']; ?>&nbsp;:
				</td>
				<td>
					<?
					foreach($lstRolesGlobal as $role){
						$sel = isset($lstRoles[$role['id']]) ? 'checked=true' : '';
						?>
						<div class="action">
							<input name="roles[]" <? echo $sel; ?> type="checkbox" value="<? echo $role['id']; ?>" />
							<label>
								<? echo $role['label']; ?>
							</label>
						</div>
						<?
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="button_form">
					<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
					<input onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R); ?>';" type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
				</td>
			</tr>
		</table>
	</form>
</div>