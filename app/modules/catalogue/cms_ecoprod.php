<?php

// recherche du template
$lstwcemods = $dims->getWceModules();
$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

$headings = wce_getheadings($wce_module_id);
$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

$site_template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
$smartyobject->assign('site_template_name', $site_template_name);
// FIN - recherche du template

/** Eco prod : Slider page d'accueil **/
$ecoprod = array();
if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
    $sql = "
            SELECT  a.id,
                    a.reference,
                    al.label,
                    a.page AS numpage,
                    a.image,
                    a.marque,
                    a.qte,
                    a.putarif_1,
                    a.degressif,
                    s.selection,
                    af.id_famille,
                    m.libelle AS marque_label

            FROM    dims_mod_cata_article a

            INNER JOIN  dims_mod_cata_article_lang al
            ON          al.id_article_1 = a.id

            INNER JOIN  dims_mod_vpc_selection s
            ON          s.ref_article = a.reference
            AND         s.ref_client = '{$_SESSION['catalogue']['code_client']}'

            LEFT JOIN   dims_mod_cata_article_famille af
            ON          af.id_article = a.id

            LEFT JOIN   dims_mod_cata_marque m
            ON          m.id = a.marque

            WHERE art.dev_durable = 1
            GROUP BY a.reference";
}
else {
    $sql = "
            SELECT  a.id,
                    a.reference,
                    al.label,
                    a.page AS numpage,
                    a.image,
                    a.marque,
                    a.qte,
                    a.putarif_1,
                    a.degressif,
                    s.selection,
                    af.id_famille,
                    m.libelle AS marque_label

            FROM    dims_mod_cata_article a

            INNER JOIN  dims_mod_cata_article_lang al
            ON          al.id_article_1 = a.id

            LEFT JOIN   dims_mod_cata_article_famille af
            ON          af.id_article = a.id

            LEFT JOIN   dims_mod_vpc_selection s
            ON          s.ref_article = a.reference
            AND         s.ref_client = '{$_SESSION['catalogue']['code_client']}'

            LEFT JOIN   dims_mod_cata_marque m
            ON          m.id = a.marque

            WHERE art.dev_durable = 1
            GROUP BY a.reference";
}

$res = $db->query($sql);

while ($fields = $db->fetchrow($res)) {

    // si article pas epuise
    if ($fields['qte'] != 0) {
        $a_idArt[$fields['id']] = $fields['id'];
        $articles[$fields['id']] = $fields;

        // marque
        if ( !empty($fields['marque']) && !isset($catalogue['marques']['values'][$fields['marque']]) ) {
            $catalogue['marques']['values'][$fields['marque']]['label'] = $fields['marque_label'];
            if ($fields['marque'] == $marque) {
                $catalogue['marques']['selected'] = $fields['marque'];
            }
        }

        if (!is_null($fields['id_famille'])) {
            if (!isset($a_artFam[$fields['id_famille']])) $a_artFam[$fields['id_famille']] = 0;
            $a_artFam[$fields['id_famille']]++;
        }

        $articleRatt = new article();
        $articleRatt->fields = $fields;

        $prix = catalogue_getprixarticle($articleRatt);

        if(empty($fields['image']) || !file_exists('./photos/50x50/'.$fields['image']))
            $fields['image'] = 'empty.jpg';

        if ($prix > 0) {
            $prixaff = catalogue_afficherprix($prix, $a_tva[$articleRatt->fields['ctva']]);

            $prix = catalogue_formateprix($prix);
            $prixaff = catalogue_formateprix($prixaff);

            $fields['prix']     = $prix;
            $fields['prixaff'] = $prixaff;

            if (isset($_SESSION['catalogue']['promo']['unlocked']["'".$$articleRatt->fields['reference']."'"])) {
                $fields['promotion'] = true;
                $fields['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($articleRatt, 1, true));
            }


            $ecoprod[$fields['id']] = $fields;
        }

    }
}

shuffle($ecoprod);
$ecoprod = array_slice($ecoprod, 0, 10);

$template_name='ecoprod';

if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
    $_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
}
$smartypath=$_SESSION['dims']['smarty_path'];

$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = "./templates/frontoffice/".$site_template_name."/catalogue/ecoprod/";

$smartyobject->assign('ecoprod',$ecoprod);
$smartyobject->assign('nbecoprod',count($ecoprod));

if (file_exists($smartyobject->template_dir.'/'.$template_name.'.tpl')) {
    if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
    $smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

    $smartyobject->display($template_name.'.tpl');
}
else echo 'ERREUR : '.$template_name.'.tpl manquant !';

?>
