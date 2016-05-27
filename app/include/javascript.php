defaultStatus = '<?
if (isset($_SESSION['dims']['workspaceid']) && isset($_SESSION['dims']['currentworkspace']['title']))
echo dims_sql_filter($_SESSION['dims']['currentworkspace']['title']);
?>
';

var lstmsg = new Array();
lstmsg[0] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_EMAIL_ERROR_1'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[1] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_EMAIL_ERROR_2'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[2] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_EMAIL_ERROR_3'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[3] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_EMAIL_ERROR_4'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[4] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_STRING_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[5] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_INT_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[6] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_FLOAT_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[7] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_DATE_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[8] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_TIME_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[9] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_CHECK_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";
lstmsg[10] = "<? echo html_entity_decode($_DIMS['cste']['_DIMS_JS_COLOR_ERROR'],ENT_COMPAT,_DIMS_ENCODING) ?>";

var error_bgcolor = "<? echo (isset($skin->values['colerror'])) ? $skin->values['colerror'] : "#FFAAAA"; ?>";

// delay to display help box
var timerdelay = 1;

