<?php
echo $skin->open_widgetbloc($_SESSION['cste']['_DIMS_LABEL_ANNOTATION'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','26px', '26px', '-12px', '-5px', '', '', '');
require_once DIMS_APP_PATH.'include/functions/annotations.php';
dims_annotation($objectid, $recordid, "",-1,-1,-1,true);
echo $skin->close_widgetbloc();
?>