<div class="container_admin global_content_record todo_form">
	<div class="form_object_block">
		<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId()); ?>" name="update_internals">
            <input type="hidden" name="id_replace" id="id_replace" value="" />
			<h4>
				<img src="<?= module_wiki::getTemplateWebPath('/gfx/icon_categ.png');?>"  />
				<? echo $_SESSION['cste']['_INTERNAL_LINKS']; ?>
			</h4>
			<div class="sub_bloc">
				<div class="sub_bloc_form">
					<div style="background: url('/common/img/arrow_ltr2.png') no-repeat 10px 5px; padding-left: 30px;margin-bottom:5px;">
						<select class="actions internal" name="params_op">
							<option value=""></option>
							<option value="<? echo module_wiki::_DELETE_LINKS; ?>">
								<? echo $_SESSION['cste']['_BUSINESS_LEGEND_CUT']; ?>
							</option>
							<option value="<? echo module_wiki::_REPLACE_LINKS; ?>">
								<? echo $_SESSION['cste']['_REPLACE_THE_LINK']; ?>
							</option>
							<option value="sel_all">
								<? echo $_SESSION['cste']['_SMILE_SELECT_EVERYTHING']; ?>
							</option>
							<option value="unsel_all">
								<? echo $_SESSION['cste']['_SMILE_UNSELECT_EVERYTHING']; ?>
							</option>
						</select>
					</div>
					<table>
						<?
						foreach($this->getInternalLinks() as $id){
							$art = new wce_article();
							$art->open($id);
							?>
							<tr>
								<td style="width:20px;">
									<input type="checkbox" class="internalcheck" name="internals[]" value="<? echo $art->fields['id']; ?>" />
								</td>
								<td>
									<a style="cursor: pointer;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=".$art->fields['id'].'&wce_mode=edit'); ?>" class="lien_bleu">
										<? echo (isset($art->fields['title']))?$art->fields['title']:$_SESSION['cste']['_DIMS_LABEL_UNDEFINED']." ".$art->fields['id']; ?>
									</a>
								</td>
							</tr>
							<?
						}
						?>
					</table>
					<div style="background: url('/common/img/arrow_ltr.png') no-repeat 10px 1px; padding-left: 30px;margin-bottom:5px;">
						<select class="actions internal" name="params_op">
							<option value=""></option>
							<option value="<? echo module_wiki::_DELETE_LINKS; ?>">
								<? echo $_SESSION['cste']['_BUSINESS_LEGEND_CUT']; ?>
							</option>
							<option value="<? echo module_wiki::_REPLACE_LINKS; ?>">
								<? echo $_SESSION['cste']['_REPLACE_THE_LINK']; ?>
							</option>
							<option value="sel_all">
								<? echo $_SESSION['cste']['_SMILE_SELECT_EVERYTHING']; ?>
							</option>
							<option value="unsel_all">
								<? echo $_SESSION['cste']['_SMILE_UNSELECT_EVERYTHING']; ?>
							</option>
						</select>
					</div>
				</div>
			</div>
		</form>
		<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId()); ?>" name="update_externals">
            <input type="hidden" name="id_replace2" id="id_replace2" value="" />
			<h4>
				<img src="<?= module_wiki::getTemplateWebPath('/gfx/icon_categ.png');?>"  />
				<? echo $_SESSION['cste']['_EXTERNAL_LINKS']; ?>
			</h4>
			<div class="sub_bloc">
				<div class="sub_bloc_form">
					<div style="background: url('/common/img/arrow_ltr2.png') no-repeat 10px 5px; padding-left: 30px;margin-bottom:5px;">
						<select class="actions external" name="params_op">
							<option value=""></option>
							<option value="<? echo module_wiki::_DELETE_LINKS; ?>">
								<? echo $_SESSION['cste']['_BUSINESS_LEGEND_CUT']; ?>
							</option>
							<option value="<? echo module_wiki::_REPLACE_LINKS; ?>">
								<? echo $_SESSION['cste']['_REPLACE_THE_LINK']; ?>
							</option>
							<option value="sel_all">
								<? echo $_SESSION['cste']['_SMILE_SELECT_EVERYTHING']; ?>
							</option>
							<option value="unsel_all">
								<? echo $_SESSION['cste']['_SMILE_UNSELECT_EVERYTHING']; ?>
							</option>
						</select>
					</div>
					<table>
						<?
						foreach($this->getExternalLinks() as $art){
							?>
							<tr>
								<td style="width:20px;">
									<input type="checkbox" class="externalcheck" name="externals[]" value="<? echo $art->fields['id']; ?>" />
								</td>
								<td>
									<a style="cursor: pointer;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=".$art->fields['id'].'&wce_mode=edit'); ?>" class="lien_bleu">
										<? echo $art->fields['title']; ?>
									</a>
								</td>
							</tr>
							<?
						}
						?>
					</table>
					<div style="background: url('/common/img/arrow_ltr.png') no-repeat 10px 1px; padding-left: 30px;margin-bottom:5px;">
						<select class="actions external" name="params_op">
							<option value=""></option>
							<option value="<? echo module_wiki::_DELETE_LINKS; ?>">
								<? echo $_SESSION['cste']['_BUSINESS_LEGEND_CUT']; ?>
							</option>
							<option value="<? echo module_wiki::_REPLACE_LINKS; ?>">
								<? echo $_SESSION['cste']['_REPLACE_THE_LINK']; ?>
							</option>
							<option value="sel_all">
								<? echo $_SESSION['cste']['_SMILE_SELECT_EVERYTHING']; ?>
							</option>
							<option value="unsel_all">
								<? echo $_SESSION['cste']['_SMILE_UNSELECT_EVERYTHING']; ?>
							</option>
						</select>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		// liens internes
		$('select.actions.internal').change(function(){
			$('select.actions.internal').val($(this).val());
			if ($(this).val() == '<? echo module_wiki::_DELETE_LINKS; ?>'){
				if($('input.internalcheck:checked').length > 0){
					if(!dims_confirmform(document.update_internals,"<? echo str_replace('"','\"',$_SESSION['cste']['_CONFIRM_DELETE_LINK']); ?>"))
						$('select.actions.internal').val("");
				}else{
					$('select.actions.internal').val("");
					alert("<? echo str_replace('"','\"',$_SESSION['cste']['_NO_LINK_SELECTED']); ?>");
				}
			}else if ($(this).val() == '<? echo module_wiki::_REPLACE_LINKS; ?>'){
				if($('input.internalcheck:checked').length > 0){
					dims_showcenteredpopup(500,500,'dims_popup');
					dims_xmlhttprequest_todiv('/admin.php','dims_op=wiki&op_wiki=choose_article&input_id=id_replace',false,'dims_popup');
				}else{
					$('select.actions.internal').val("");
					alert("<? echo str_replace('"','\"',$_SESSION['cste']['_NO_LINK_SELECTED']); ?>");
				}
			}else if ($(this).val() == 'sel_all'){
				$('input.internalcheck').attr('checked',true);
				$('select.actions.internal').val("");
			}else if ($(this).val() == 'unsel_all'){
				$('input.internalcheck').removeAttr('checked');
				$('select.actions.internal').val("");
			}else{
				$('select.actions.internal').val("");
			}
		});
        $('input#id_replace').change(function(){
            if ($(this).val() != '')
                document.update_internals.submit();
			else
				$('select.actions.internal').val("");
        });

		// liens externes
		$('select.actions.external').change(function(){
			$('select.actions.external').val($(this).val());
			if ($(this).val() == '<? echo module_wiki::_DELETE_LINKS; ?>'){
				if($('input.externalcheck:checked').length > 0){
					if(!dims_confirmform(document.update_externals,"<? echo str_replace('"','\"',$_SESSION['cste']['_CONFIRM_DELETE_LINK']); ?>"))
						$('select.actions.external').val("");
				}else{
					$('select.actions.external').val("");
					alert("<? echo str_replace('"','\"',$_SESSION['cste']['_NO_LINK_SELECTED']); ?>");
				}
			}else if ($(this).val() == '<? echo module_wiki::_REPLACE_LINKS; ?>'){
				if($('input.externalcheck:checked').length > 0){
					dims_showcenteredpopup(500,500,'dims_popup');
					dims_xmlhttprequest_todiv('/admin.php','dims_op=wiki&op_wiki=choose_article&input_id=id_replace2',false,'dims_popup');
				}else{
					$('select.actions.external').val("");
					alert("<? echo str_replace('"','\"',$_SESSION['cste']['_NO_LINK_SELECTED']); ?>");
				}
			}else if ($(this).val() == 'sel_all'){
				$('input.externalcheck').attr('checked',true);
				$('select.actions.external').val("");
			}else if ($(this).val() == 'unsel_all'){
				$('input.externalcheck').removeAttr('checked');
				$('select.actions.external').val("");
			}else{
				$('select.actions.external').val("");
			}
		});
        $('input#id_replace2').change(function(){
            if ($(this).val() != '')
                document.update_externals.submit();
			else
				$('select.actions.external').val("");
        });
	});
</script>
