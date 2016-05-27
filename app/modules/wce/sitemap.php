<?php
dims_init_module("wce");

include_once(DIMS_APP_PATH . "modules/wce/include/classes/class_wce_site.php");

//if (!isset($_SESSION['dims']['moduleid'])) $_SESSION['dims']['moduleid']=$moduleid;
if ($moduleid == -1 && isset($_SESSION['dims']['moduleid']) ) $moduleid = $_SESSION['dims']['moduleid'];
elseif(!isset($_SESSION['dims']['moduleid'])) $_SESSION['dims']['moduleid']=$moduleid;

if(!isset($_SESSION['dims']['wce_default_lg'])){
    if (!isset($site))
        $site = new wce_site(dims::getInstance()->getDb(),$moduleid);
    $_SESSION['dims']['wce_default_lg'] = $site->getDefaultLanguage();
}

if (!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$_SESSION['dims']['wce_default_lg'];

echo wce_getSiteMap($db,$moduleid);
?>
