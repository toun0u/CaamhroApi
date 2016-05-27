<?php
// TODO : Utiliser des constantes de langue Dims
?>

<form class="ajaxForm" name="form_wce_block_edit_small<? echo $this->fields['id']; ?>" id="form_wce_block_edit_small<? echo $this->fields['id']; ?>" style="margin:0;" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_SAVE_BLOC_LITTLE."&id=".$this->fields['id_article']); ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="block_id" value="<? echo $this->fields['id']; ?>" />
<input type="hidden" name="section" value="<? echo $this->fields['section']; ?>" />
<input type="hidden" name="wce_block_id_article" value="<? echo $this->fields['id_article']; ?>" />
<input type="hidden" name="wce_block_id_lang" value="<? echo $this->fields['id_lang']; ?>" />
<span style="float:left;">
<label>Title</label>
<input type="text" id="wce_block_title" name="wce_block_title" value="<? echo str_replace('"','&quot;',$this->fields['title']); ?>">
</span><span style="float:left;">
<label><? echo ucfirst($_SESSION['cste']['MODEL']);?> :</label>
<select name="wce_block_id_model" id="wce_block_id_model" class="select" tabindex="4">

		<?
		/*<option <? echo ($this->fields['id_model'] == 0) ? 'selected' : ''; ?> value=""><? echo "aucun"; ?></option>
		 *
		 */
		$wce_site = new wce_site(dims::getInstance()->db,$_SESSION['dims']['moduleid']);
		$wce_site->loadBlockModels();
		foreach($wce_site->getBlockModels() as $key => $model) {
			?>
			<option <? echo ($this->fields['id_model'] == $model['id']) ? 'selected' : ''; ?> value="<? echo $model['id']; ?>"><? echo $model['label']; ?></option>
			<?
		}
		?>
		</select>
<a href="javascript:void(0)" onclick="javascript:wceSaveLittleBlock(<? echo $this->fields['id']; ?>);"><img src="./common/img/checkdo.png" style="border:0px"></a>
<?

//echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:document.form_wce_block.submit();",'','');
?>
</span>
</form>
