<?php
$title = $_SESSION['cste']['_SMILE_CREATE_TAG'];
if($this->fields['id'] != '' && $this->fields['id'] > 0){
	$title = $_SESSION['cste']['_DIMS_LABEL_TAG']." : ".$this->fields['tag'];
}
?>
<div class="container_admin global_content_record todo_form">
	<div class="form_object_block">
		<h4><? echo $title; ?></h4>
		<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU."&action=".module_wiki::_ACTION_SAVE_TAG); ?>" name="save_user" id="save_user">
			<input type="hidden" name="id_tag" value="<? echo $this->fields['id']; ?>" />
			<div class="sub_bloc">
				<div class="sub_bloc_form">
					<table cellpadding="0" cellspacing="0" class="form_user">
						<tr>
							<td class="label_field">
								<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>&nbsp;:&nbsp;
							</td>
							<td class="value_field">
								<input type="text" name="tag_tag" value="<? echo $this->fields['tag']; ?>" />
							</td>
						</tr>
						<?

						foreach(wce_lang::getInstance()->getAll() as $lang){
							?>
							<tr>
								<td class="label_field">
									<? echo $lang->fields['label']; ?>&nbsp;:&nbsp;
								</td>
								<td class="value_field">
									<input type="text" name="tag_tag_<? echo $lang->fields['ref']; ?>" value="<? echo $this->fields['tag_'.$lang->fields['ref']]?>" />
								</td>
							</tr>
							<?
						}
						?>
					</table>
				</div>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
					<div><input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></div>
					<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU); ?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
				</div>
			</div>
		</form>
	</div>
</div>