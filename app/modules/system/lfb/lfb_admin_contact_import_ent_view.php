<?php

//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_DYNFIELD_ENT'],'width:50%;float:left;clear:none;','','');
//include (DIMS_APP_PATH . "/modules/system/lfb_admin_index_contact_import_form.php");
//echo $skin->close_simplebloc();
$text =  $_DIMS['cste']['_LABEL_IMPORT']." ".$_DIMS['cste']['_DIMS_LABEL_GROUP_LIST'];
echo $skin->open_simplebloc($text,'width:50%;float:left;clear:none;','','');
include (DIMS_APP_PATH . "/modules/system/lfb/lfb_admin_index_ent_import_form.php");
echo $skin->close_simplebloc();


?>
