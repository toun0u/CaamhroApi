<?php

// recherche du template
$lstwcemods = $dims->getWceModules();
$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

$headings = wce_getheadings($wce_module_id);
$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

$site_template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
$smartyobject->assign('site_template_name', $site_template_name);
// FIN - recherche du template

include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud_element.php';

$cloud = new cloud();
$cloud->open($obj['object_id']);

$cloud_tab['id']            = $cloud->fields['id'];
$cloud_tab['nom']           = $cloud->fields['nom'];
$cloud_tab['description']   = $cloud->fields['description'];
$cloud_tab['mode']          = $cloud->fields['mode'];

foreach($cloud->getElements() as $cloud_elem) {
    if(!$cloud_elem->fields['connected_only'] || $_SESSION['dims']['connected']) {
        $cloud_tab['elem'][$cloud_elem->fields['id']]['id']     = $cloud_elem->fields['id'];
        $cloud_tab['elem'][$cloud_elem->fields['id']]['titre']  = $cloud_elem->fields['titre'];
        $cloud_tab['elem'][$cloud_elem->fields['id']]['lien']   = $cloud_elem->fields['lien'];
        $cloud_tab['elem'][$cloud_elem->fields['id']]['niveau'] = $cloud_elem->fields['niveau'];
        $cloud_tab['elem'][$cloud_elem->fields['id']]['couleur']= $cloud_elem->fields['couleur'];
    }
}

if($cloud->fields['mode'] == 2) {
    if(!function_exists('cata_cmp_cloudelem')) {
        function cata_cmp_cloudelem($a, $b) {
            $return_value = 0;

            //Tri descendant !
            if($a['niveau'] == $b['niveau']) $return_value      = 0;
            elseif($a['niveau'] < $b['niveau']) $return_value   = 1;
            elseif($a['niveau'] > $b['niveau']) $return_value   = -1;

            return $return_value;
        }
    }

    usort($cloud_tab['elem'], cata_cmp_cloudelem);
}
else
    shuffle($cloud_tab['elem']);

$template_name='cloud';

if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
    $_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
}
$smartypath=$_SESSION['dims']['smarty_path'];

$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = "./templates/frontoffice/".$site_template_name."/catalogue/clouds/";

$smartyobject->assign('cloud',$cloud_tab);

if (file_exists($smartyobject->template_dir.'/'.$template_name.'.tpl')) {
    if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
    $smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

    $smartyobject->display($template_name.'.tpl');
}
else echo 'ERREUR : '.$template_name.'.tpl manquant !';

?>
