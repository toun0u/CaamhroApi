<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form method="post" action="<? echo "admin.php?dims_mainmenu=events&action=import_zip_files&id_event=".$_SESSION['dims']['currentaction']; ?>" enctype="multipart/form-data">
<input type="file" name="importfile">
<input type="submit" value="<? echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?>">
</form>