<?php
if($this->isNew())
	$title = $_SESSION['cste']['_NEW_CATEGORY'];
else
	$title = $_SESSION['cste']['_SMILE_EDITION_CATEGORY']." : ".$this->fields['label'];
?>
<h4><? echo $title; ?></h4>
<div class="table_article">
	<div style="margin-top:10px;">
		<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES."&action=".module_wiki::_ACTION_SAVE_CATEG); ?>" name="save_categ" id="save_categ">
			<input type="hidden" name="id_categ" value="<? echo $this->fields['id']; ?>" />
			<table style="border:0px">
				<tr>
					<td class="label" style="width:20%;">
						<?php echo $_SESSION['cste']['_CATEGORIES']; ?>&nbsp;:&nbsp;
					</td>
					<td>
						<?
						$root = module_wiki::getCategRoot();
						$load = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if ($load != '' && $load > 0){
							$par = new category();
							$par->open($load);
							$root->setLightAttribute('parent',$par);
						}elseif ($this->fields['id_parent'] != '' && $this->fields['id_parent'] > 0){
							$par = new category();
							$par->open($this->fields['id_parent']);
							$root->setLightAttribute('parent',$par);
						}else
							$root->setLightAttribute('parent',null);
						$root->display(module_wiki::getTemplatePath('/categories/categ_browser.tpl.php'));
						?>
					</td>
				</tr>
				<tr>
					<td class="label">
						<?php echo $_SESSION['cste']['_DIMS_LABEL']; ?>&nbsp;:&nbsp;
					</td>
					<td style="text-align: left;">
						<input type="text" name="categ_label" id="categ_label" value="<?php echo $this->fields["label"]; ?>" rel="requis"/>
					</td>
					<tr><td></td><td><div class="mess_error" id="def_categ_label"></div></td></tr>
				</tr>
				<tr>
					<td></td>
					<td class="button_form" style="text-align: left;">
						<span class="display_errors" id="champs_obligatoires"></span>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
						<input onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES); ?>';" type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript" src="/common/js/dims_validForm.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("form#save_categ").dims_validForm({messages: {defaultError: '<? echo $_SESSION['cste']['_OEUVRE_THIS_FIELD_IS_COMPULSORY']; ?>',
														globalMessage: '<? echo $_SESSION['cste']['_OEUVRE_ERROR_FIELDS_SEIZED']; ?>'
														},
										   displayMessages: true,
										   refId: 'def',
										   globalId: 'champs_obligatoires',
										   classInput: 'dims_error_input'});
	});
</script>
