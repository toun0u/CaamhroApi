<?php

// recherche du template
$lstwcemods = $dims->getWceModules();
$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

$headings = wce_getheadings($wce_module_id);
$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

$site_template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
$smartyobject->assign('site_template_name', $site_template_name);
// FIN - recherche du template

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow_element.php';

$slideshow = new slideshow();
$slideshow->open($obj['object_id']);



$slide_tab['id']            = $slideshow->fields['id'];
$slide_tab['nom']           = $slideshow->fields['nom'];
$slide_tab['description']   = $slideshow->fields['description'];
$slide_tab['template']      = $slideshow->fields['template'];

foreach($slideshow->getElements() as $slide) {
    if(!$slide->fields['connected_only'] || $_SESSION['dims']['connected']) {
        $slide_tab['slide'][$slide->fields['id']]['id']             = $slide->fields['id'];
        $slide_tab['slide'][$slide->fields['id']]['connected_only'] = $slide->fields['connected_only'];
        $slide_tab['slide'][$slide->fields['id']]['titre']          = $slide->fields['titre'];
        $slide_tab['slide'][$slide->fields['id']]['descr_courte']   = $slide->fields['descr_courte'];
        $slide_tab['slide'][$slide->fields['id']]['descr_longue']   = $slide->fields['descr_longue'];
        $slide_tab['slide'][$slide->fields['id']]['descr_position'] = $slide->fields['descr_position'];
        $slide_tab['slide'][$slide->fields['id']]['position']       = $slide->fields['position'];

        // on ajoute le lien que si il est valide
        $url = parse_url($slide->fields['lien']);
        // si c'est un lien relatif, on rajoute le protocole et le host
        if ( !isset($url['scheme']) || ($url['scheme'] != 'http' && $url['scheme'] != 'https' && $url['scheme'] != 'mailto' && $url['scheme'] != 'news' && $url['scheme'] != 'file') ) {
			$lien = $dims->getProtocol().$_SERVER['HTTP_HOST'];
			if (isset($slide->fields['lien'][0]) && $slide->fields['lien'][0] != '/') $lien .= '/';
			$lien .= $slide->fields['lien'];
		}
		else {
			$lien = $slide->fields['lien'];
		}
        if (filter_var($lien, FILTER_VALIDATE_URL)) {
			$slide_tab['slide'][$slide->fields['id']]['lien'] = $lien;
		}

        if($slide->fields['image'] > 0) {
            $image = new docfile();
            $image->open($slide->fields['image']);

            $slide_tab['slide'][$slide->fields['id']]['image'] = $image->getwebpath();
        }

        if($slide->fields['miniature'] > 0) {
            $miniature = new docfile();
            $miniature->open($slide->fields['miniature']);

            $slide_tab['slide'][$slide->fields['id']]['miniature'] = $miniature->getwebpath();
        }
    }
}

$template_name=$slideshow->fields['template'];

if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
    $_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
}
$smartypath=$_SESSION['dims']['smarty_path'];

$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = "./templates/frontoffice/".$site_template_name."/catalogue/slideshows/";

$smartyobject->assign('slideshow',$slide_tab);

if (file_exists($smartyobject->template_dir.'/'.$template_name.'.tpl')) {
    if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
    $smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

    $smartyobject->display($template_name.'.tpl');
}
else echo 'ERREUR : '.$template_name.'.tpl manquant !';
