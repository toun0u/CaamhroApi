<div>
	<a href="<?php echo dims_urlencode(dims::getInstance()->getScriptEnv().'?dims_op=doc_file_download&docfile_md5id='.$this->fields['md5id']); ?>" title="<?php echo $_SESSION['cste']['_DIMS_DOWNLOAD']; ?>">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/download.png" />
		<span><?php echo $_SESSION['cste']['_DIMS_DOWNLOAD']; ?></span>
	</a>
</div>
<div>
	<a href="javascript:void(0);" onclick="javascript:preview_docfile('<?php echo $this->fields['md5id']; ?>');" title="<?php echo $_SESSION['cste']['_PREVIEW']; ?>">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/previsu.png" />
		<span><?php echo $_SESSION['cste']['_PREVIEW']; ?></span>
	</a>
</div>
<div>
	<a href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo addslashes($_SESSION['cste']['SURE_DELETE_DOCUMENT']);?>')) document.location.href = '?dims_op=desktopv2&action=delete_concept&type=document&go=<?php echo $this->fields['id_globalobject']; ?>&from=concept&desktop=1';">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" border="0" />
		<span><?php echo $_SESSION['cste']['DELETE_DOCUMENT']; ?></span>
	</a>
</div>
