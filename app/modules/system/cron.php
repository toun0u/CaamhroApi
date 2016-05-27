<?php
//dims_init_module('system');

$cronfiles[] = "./common/modules/system/index_run.php";
$cronfiles[] = './common/modules/system/crm_cron_webmail.php';

foreach($cronfiles as $cronfile) {
    if (file_exists($cronfile)) {
            echo '\nexecution de '.$cronfile.'\n';
            include $cronfile;
    }
}

?>
