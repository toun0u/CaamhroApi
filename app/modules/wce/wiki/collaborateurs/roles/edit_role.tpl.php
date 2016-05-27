<?php
$module = new module_wiki();
$module->open($_SESSION['dims']['moduleid']);
$lstActions = $module->getActionsDispo();
$valideActions = $this->getactions();

$title = $_SESSION['cste']['_OEUVRE_CREATION_OF_A_ROLE'];
if($this->fields['id'] != '' && $this->fields['id'] > 0)
	$title = $_SESSION['cste']['_OEUVRE_EDITION_OF_A_ROLE']." : ".$this->fields['label'];
?>
<h4><? echo $title; ?></h4>
<div style="margin-top:10px;">
	<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R."&action=".module_wiki::_ACTION_SAVE_ROLES); ?>">
		<input type="hidden" name="id_role" value="<? echo $this->fields['id']; ?>" />
		<table cellpadding="0" cellspacing="0" class="form_user">
			<tr>
				<td class="label" style="height: 20px;">
					<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>&nbsp;:
				</td>
				<td>
					<input type="text" name="role_label" value="<? echo $this->fields['label']; ?>" />
				</td>
				<td rowspan="3">
					<?
					foreach($lstActions as $action){
						$sel = isset($valideActions[$action['id_action']]) ? 'checked=true' : '';
						?>
						<div class="action">
							<input name="actions[]" <? echo $sel; ?> type="checkbox" value="<? echo $action['id_action']; ?>" />
							<label>
								<? echo (isset($_SESSION['cste'][$action['label']])) ? $_SESSION['cste'][$action['label']] : $action['label']; ?>
							</label>
						</div>
						<?
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="label" style="height: 20px;vertical-align: top;">
					<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>&nbsp;:
				</td>
				<td>
					<textarea name="role_description"><? echo $this->fields['description']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" class="button_form">
					<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
					<input onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R); ?>';" type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
				</td>
			</tr>
		</table>
	</form>
</div>