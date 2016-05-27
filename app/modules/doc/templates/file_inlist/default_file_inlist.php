<?php
global $dims;
?>
<div id="file_<?php echo $file->fields['id'];?>" class="link_file">
	<div class="link">
            <a href="<?php echo dims_urlencode($dims->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id=".$file->fields['md5id']);?>" onclick="" title="Télécharger ce document"><?php echo $file->fields['name'];?></a>
	</div>
	<div class="actions">
		<a href="javascript:preview_docfile('<?php echo $file->fields['md5id']; ?>');" title="Prévisualiser ce document" class="fil_button loupe"></a>
		<a href="<?php echo dims_urlencode($dims->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id=".$file->fields['md5id']);?>" title="Télécharger ce document" class="fil_button download"></a>
	</div>
</div>