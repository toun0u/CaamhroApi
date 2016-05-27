<?php
$title = $_SESSION['cste']['_CREATION_LANGUAGE'];
if($this->fields['id'] != '' && $this->fields['id'] > 0){
	if(!is_null($flag = $this->getFlag()))
		$myFlag = '<img style="padding-left:5px;" src="'.$flag.'" />';
	$title = $_SESSION['cste']['_EDITION_LANGUAGE']." : ".$this->fields['label']."$myFlag";
}
?>
<h4><? echo $title; ?></h4>
<div style="margin-top:10px;">
	<div style="margin-top:10px;">
		<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU."&action=".module_wiki::_ACTION_SAVE_LANG); ?>" name="save_user" id="save_user">
			<input type="hidden" name="id_lang" value="<? echo $this->fields['id']; ?>" />
			<table cellpadding="0" cellspacing="0" class="form_user">
				<tr>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>&nbsp;:&nbsp;
					</td>
					<td>
						<input type="text" name="lg_label" value="<? echo $this->fields['label']; ?>" />
					</td>
				</tr>
				<!--<tr>
					<td class="label">
						<? echo $_SESSION['cste']['_FLAG']; ?>&nbsp;:&nbsp;
					</td>
					<td>
						<?
						if ($handle = opendir(DIMS_APP_PATH.'./common/img/flag')) {
							while (false !== ($entry = readdir($handle))) {
								if ($entry != '.' && $entry != '..'){
									$ref = substr($entry,5,2);
									?>
									<div style="float:left;margin-right:5px;margin-bottom:5px;">
										<input <? if($this->fields['ref'] == $ref) echo 'checked=true'; ?> type="radio" value="<? echo $ref; ?>" name="lg_ref" />
										<img title="<? echo $ref; ?>" alt="<? echo $ref; ?>" src="./common/img/flag/<? echo $entry; ?>" />
									</div>
									<?
								}
							}
						}
						?>
					</td>
				</tr>-->
				<tr>
					<td></td>
					<td>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
						<input onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU); ?>';" type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>