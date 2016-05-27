<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" name="save_object" enctype="multipart/form-data">
		<input type="hidden" name="action" value="save_rss" />
		<input type="hidden" name="id_obj" value="<? echo $this->fields['id']; ?>" />
		<input type="hidden" name="id" value="<? echo $this->get('id_object'); ?>" />
		<!--<input type="hidden" name="id_lang" value="<? echo $this->fields['id_lang']; ?>" />-->
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr class="url-rss">
						<td class="label_field">
							<label>
								<?= $_SESSION['cste']['_DIMS_LABEL_WEB_ADDRESS']; ?>
							</label>
						</td>
						<td>
							<input onkeydown="javascript: $('div.form_buttons input[type=\'submit\']').attr('disabled',true); return (event.keyCode!=13);" type="text" value="<?= $this->fields['url']; ?>" name="obj_url" id="obj_url" style="width:85%;" />
							<img onclick="javascript:refreshRss();" style="cursor:pointer;" src="/common/img/reload.png" alt="<?= $_SESSION['cste']['_RSS_LABELTAB_MODIFY']; ?>" title="<?= $_SESSION['cste']['_RSS_LABELTAB_MODIFY']; ?>" />
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<input disabled=true type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$this->get('id_object'); ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	function refreshRss(){
		var v = jQuery.trim($('#obj_url').val());
		if(v != ''){
			$.ajax({
				type: "POST",
				url: '<?= module_wce::get_url(module_wce::_SUB_DYN); ?>',
				data: {
					action: 'infos_rss',
					url: v,
				},
				dataType: 'json',
				success: function(data){
					$("tr.load-data-rss").remove();
					if(data['title'] != undefined){
						/*if(data['ico'] != '')
							var d = '<tr class="load-data-rss"><td class="label_field"><label><?= $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label></td><td><img src="'+data['ico']+'" /> '+data['title']+'</td></tr>';
						else*/
							var d = '<tr class="load-data-rss"><td class="label_field"><label><?= $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label></td><td>'+data['title']+'</td></tr>';
						if(data['description'] != ''){
							d += '<tr class="load-data-rss"><td class="label_field"><label><?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></label></td><td>'+data['description']+'</td></tr>';
						}
						$('tr.url-rss').after(d);
						$('div.form_buttons input[type="submit"]').attr('disabled',false);
					}else{
						$('div.form_buttons input[type="submit"]').attr('disabled',true);
					}
				},
			});
		}
	}
</script>
