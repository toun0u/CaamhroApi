<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_SITE); ?>" name="listart" style="clear: both;">
	<input type="hidden" name="sub" value="<? echo module_wce::_SITE_LIST; ?>" />
	<input type="hidden" name="action" id="action" value="" />
	<input type="hidden" name="headingid" value="<? echo $this->fields['id']; ?>" />
	<input type="hidden" name="headingid2" id="iddestheading" value="" />
	<div style="background: url('/common/img/arrow_ltr2.png') no-repeat 10px 5px; padding-left: 30px;margin-bottom:5px;">
		<select class="actions">
			<option value=""></option>
			<option value="<? echo module_wce::_LIST_ART_MOVE; ?>"><? echo $_DIMS['cste']['_DOC_LABEL_MOVE'];?></option>
			<option value="<? echo module_wce::_LIST_ART_DEL; ?>"><? echo $_DIMS['cste']['_DELETE'];?></option>
		</select>
	</div>
	<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;">
		<?php
		$this->setLightAttribute('pos',0);
		$this->setLightAttribute('class','');
		$this->display(module_wce::getTemplatePath("gestion_site/list_articles/list_heading.tpl.php"));
		?>
	</table>
	<div style="background: url('/common/img/arrow_ltr.png') no-repeat 10px 0px; padding-left: 30px;margin-top:5px;">
		<select class="actions">
			<option value=""></option>
			<option value="<? echo module_wce::_LIST_ART_MOVE; ?>"><? echo $_DIMS['cste']['_DOC_LABEL_MOVE'];?></option>
			<option value="<? echo module_wce::_LIST_ART_DEL; ?>"><? echo $_DIMS['cste']['_DELETE'];?></option>
		</select>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('select.actions').change(function(){
			if ($('input.input_art:checked').length > 0){
				$('select.actions').val($(this).val());
				$('input#action').val($(this).val());
				if ($(this).val() == '<? echo module_wce::_LIST_ART_DEL; ?>'){
					if(!dims_confirmform(document.listart,"<? echo str_replace('"','\"',$_SESSION['cste']['_CONFIRM_DELETE_ARTICLE']); ?>")){
						$('select.actions').val("");
						$('input#action').val("");
					}
				}else if ($(this).val() == '<? echo module_wce::_LIST_ART_MOVE; ?>'){
					dims_showcenteredpopup("",600,150,"dims_popup");
					dims_xmlhttprequest_todiv('admin-light.php','dims_op=choice_heading&currentheading=0&selectheadings=',false,'dims_popup');
				}
			}else{
				$('select.actions').val("");
				$('input#action').val("");
				alert("<? echo str_replace('"','\"',$_SESSION['cste']['_ALERT_NO_ARTICLE_SELECTED']); ?>");
			}
		});
		$('div#dims_popup input.flatbutton').live('click',function(){
			$('select.actions').val("");
			$('input#action').val("");
		});
	});
</script>
