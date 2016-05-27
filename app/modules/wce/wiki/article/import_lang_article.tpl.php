<form name="form_wce_article" style="margin:0;" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_IMPORT_LANG_ART); ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="id_article" value="<? echo $this->fields['id']; ?>" />
	<table style="width:100%">
		<tr>
			<td style="width:40%;height:30px;"><? echo $_SESSION['cste']['_DIMS_LABEL_LANG']; ?></td>
			<td>
				<select name="id_lang" id="id_lang">
					<?
                    $content2 = "";
                    $listCourante = $this->getListArticleLangNotVersion();
					foreach($listCourante as $lang){
                        $content2 .= "<option value=\"".$lang->fields['id']."\">".$lang->fields['label']."</option>";
						?>
						<option value="<? echo $lang->fields['id']; ?>"><? echo $lang->fields['label']; ?></option>
						<?
					}
                    $content = "";
                    foreach(wce_lang::getInstance()->getAll(true) as $lang){
                        $content .= "<option value=\"".$lang->fields['id']."\">".$lang->fields['label']."</option>";
                    }
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="width:40%;height:30px;"><? echo $_SESSION['cste']['_DIMS_LABEL_FILE']; ?></td>
			<td>
				<input type="file" name="file" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" name="delete_content" id="delete_content" value="1" />
				<? echo $_SESSION['cste']['_DELETE_CONTENT_EXISTING_LANGUAGE']; ?>
			</td>
		</tr>
	</table>
	<div style="clear:both;text-align:right;float:left;padding:5px;height:30px;width:95%;">
		<?
		echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"","javascript:dims_hidepopup();");
		echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:document.form_wce_article.submit();",'','');
		?>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('input#delete_content').change(function(){
            var content = '<? echo $content; ?>';
            var content2 = '<? echo $content2; ?>';
            if($(this).is(':checked')){
                $('select#id_lang').html(content);
            }else{
                $('select#id_lang').html(content2);
            }
        });
	});
</script>
