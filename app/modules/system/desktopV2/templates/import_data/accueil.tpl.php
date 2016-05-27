<?php
?>
<div>
	<h1>
		<img src="./common/img/icon_gclients.png" />
		<? echo $_SESSION['cste']['_DIMS_LABEL_IMPORT_MANAGEMENT']; ?>
	</h1>
</div>
<!-- History -->
<div style="float:left;width:32%;text-align:center;margin:2px auto;">
	<img src="<? echo $_SESSION['dims']['template_path']; ?>/media/history32.png">
	<br><input type="button" onclick="document.location.href='<? echo dims_urlencode("/admin.php?import_op="._OP_SAVE_HISTORY); ?>';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="<? echo $_SESSION['cste']['_DIMS_HISTORY']; ?>"/>
</div>
<!-- new file import -->
<div style="float:left;width:33%;text-align:center;margin:2px auto;">
	<img src="./common/img/doc_add.png">
	<br><input type="button" onclick="document.location.href='<? echo dims_urlencode("/admin.php?import_op="._OP_NEW_IMPORT); ?>';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="<? echo $_SESSION['cste']['_IMPORT_DOWNLOAD_FILE']; ?>"/>
</div>
<!-- manage model -->
<div style="float:left;width:33%;text-align:center;margin:2px auto;">
	<img src="<? echo $_SESSION['dims']['template_path']; ?>/media/add_table32.png">
	<br><input type="button" onclick="document.location.href='<? echo dims_urlencode("/admin.php?import_op="._OP_MODULE_IMPORT); ?>';" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="<? echo $_SESSION['cste']['_DIMS_LABEL_FAIRS_MODELS_MGT']; ?>"/>
</div>
