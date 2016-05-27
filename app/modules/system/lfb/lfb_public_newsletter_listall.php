<?php
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);

$in = '0';

$sql_in = "	SELECT 	id_to
                        FROM 	dims_workspace_share
                        WHERE 	id_from = :idfrom
                        AND 	active = 1
                        AND 	id_object = :idobject ";
$res_in = $db->query($sql_in, array(
    ':idfrom'   => $_SESSION['dims']['workspaceid'],
    ':idobject' => dims_const::_SYSTEM_OBJECT_NEWSLETTER
));

if($db->numrows($res_in) >= 1) {
        while($tabw = $db->fetchrow($res_in)) {
                $in .= ", ".$tabw['id_to'];
        }
        $in .= ", ".$_SESSION['dims']['workspaceid']; //on ajoute le workspace courant sinon il sera exclu des recherches
}
else {
        $in = $_SESSION['dims']['workspaceid'];
}

//recuperation des donnees
$sql = 'SELECT      n.*,
                    c.id as id_article,
                    s.id_contact as id_inscr,
                    i.id as id_dmd
        FROM        dims_mod_newsletter n
        LEFT JOIN   dims_mod_newsletter_content c
        ON          c.id_newsletter = n.id
        LEFT JOIN   dims_mod_newsletter_subscribed s
        ON          s.id_newsletter = n.id
        AND         s.etat = 1
        LEFT JOIN   dims_mod_newsletter_inscription i
        ON          i.id_newsletter = n.id
        WHERE       n.id_workspace in ( :idworkspace )';

/*.'
        AND         (n.id_user_responsible = '.$_SESSION['dims']['userid'].'
        OR          n.id_user_create = '.$_SESSION['dims']['userid'].')'*/
if(isset($upname) && $upname == 1 ) {
    $sql .= " ORDER BY		n.label DESC";
    $opt_trip = -1;
    $opt_trit = -2;
}
elseif(isset($upname) && $upname == -1) {
    $sql .= " ORDER BY		n.label ASC";
    $opt_trip = 1;
    $opt_trit = -2;
}
elseif(isset($upname) && $upname == 2) {
    $sql .= " ORDER BY		n.timestp_create DESC ";
    $opt_trip = -1;
    $opt_trit = -2;
}
elseif(isset($upname) && $upname == -2) {
    $sql .= " ORDER BY		n.timestp_create ASC ";
    $opt_trip = -1;
    $opt_trit = 2;
}
else {
    $sql .= " ORDER BY    n.timestp_create DESC";
    $opt_trip = -1;
    $opt_trit = -2;
}

$res = $db->query($sql, array(
    ':idworkspace' => $in
));

$tab_news = array();
while($tab_res = $db->fetchrow($res)){
    //on compte le nombre d'inscrits issus des mailing lists backoffice
    $tab_insc_from_back = array();
    if(!isset($tab_news[$tab_res['id']]['nb_insc_from_back'])) {
        $tab_news[$tab_res['id']]['nb_insc_from_back'] = array();

        $sqlct = '  SELECT      ct.*
                    FROM        dims_mod_newsletter_mailing_ct ct
                    INNER JOIN  dims_mod_newsletter_mailing_news mn
                    ON          mn.id_mailing = ct.id_mailing
                    AND         mn.id_newsletter = :idnewsletter
                    WHERE       ct.actif =1';

		$resct = $db->query($sqlct, array(
            ':idnewsletter' => $tab_res['id']
        ));
        if($db->numrows($resct) > 0) {
            while($tab_plus = $db->fetchrow($resct)) {
                $tab_insc_from_back[$tab_plus['id']] = $tab_plus;
            }
        }

        $tab_news[$tab_res['id']]['nb_insc_from_back'] = $tab_insc_from_back;
    }

    if(!isset($tab_news[$tab_res['id']]['nb_insc'])) {
        $tab_news[$tab_res['id']]['nb_insc'] = array();
    }
    if(!isset($tab_news[$tab_res['id']]['nb_dmd'])) {
        $tab_news[$tab_res['id']]['nb_dmd'] = array();
    }
    if(!isset($tab_news[$tab_res['id']]['nb_article'])) {
        $tab_news[$tab_res['id']]['nb_article'] = array();
    }
    //nombre d'inscrits issus du front office
    if(isset($tab_res['id_inscr']) && $tab_res['id_inscr'] != '') {
        $tab_news[$tab_res['id']]['nb_insc'][$tab_res['id_inscr']] = $tab_res['id_inscr'];
    }

    if(isset($tab_res['id_dmd']) && $tab_res['id_dmd'] != '') {
        $tab_news[$tab_res['id']]['nb_dmd'][$tab_res['id_dmd']] = $tab_res['id_dmd'];
    }

    if(isset($tab_res['id_article']) && $tab_res['id_article'] != '') {
        $tab_news[$tab_res['id']]['nb_article'][$tab_res['id_article']] = $tab_res['id_article'];
    }

    $tab_news[$tab_res['id']]['news'] = $tab_res;
}

//dims_print_r($tab_news);

$class = "trl1";
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_LIST']);
echo '<table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>';
                    echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADD_NEWSLETTER'], './common/img/add.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'\'');
echo '          </td>
            </tr>';
if(count($tab_news) > 0) {
    //on construit le tableau des newsletter
    echo '  <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:#cccccc 1px solid;">
                        <tr class="trl1" style="font-size:13px;">
                            <td>
                            </td>
                            <td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action=change_view&upname='.$opt_trip.'">'.$_DIMS['cste']['_DIMS_LABEL_TITLE'].'</a>
                            </td>
                            <td>'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTIF'].'
                            </td>
                            <td>
                            </td>
                            <td>'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NB'].'
                            </td>
                            <td>'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NBINSC'].'
                            </td>
                            <td>'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NBDMDINSC'].'
                            </td>
                            <td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action=change_view&upname='.$opt_trit.'">'.$_DIMS['cste']['_DIMS_LABEL_ENT_DATEC'].'</a>
                            </td>
                            <td>'.$_DIMS['cste']['_DIMS_ACTIONS'].'
                            </td>
                        </tr>';
    foreach($tab_news as $id_news => $news) {

        if($class == "trl1") $class = "trl2";
        else $class = "trl1";

        $date_cre = dims_timestamp2local($news['news']['timestp_create']);

        $desc = '';
        if($news['news']['descriptif'] != '') $desc = substr(strip_tags($news['news']['descriptif']), 0, 50);
        //calcul du nombre d'inscrits
        $tot_nb_insc = count($news['nb_insc']) + count($news['nb_insc_from_back']);
        echo '           <tr class="'.$class.'">
                            <td align="center">';
        if($news['news']['etat'] == 1) {
            echo '              <img src="./common/modules/system/img/ico_point_green.gif" title="'.$_DIMS['cste']['_DIMS_NEWSLETTER_ACTIVE'].'"/>';
        }
        else {
            echo '              <img src="./common/modules/system/img/ico_point_red.gif" title="'.$_DIMS['cste']['_DIMS_NEWSLETTER_INACTIVE'].'"/>';
        }
        echo '              </td>
                            <td align="left" width="15%"><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'">'.$news['news']['label'].'</a>
                            </td>
                            <td align="left">'.$desc.'</td>
                            <td align="left"><a href="http://newsletters.luxembourgforbusiness.lu/index.php?id_nl='.$id_news.'" target="blank"><img src="./common/img/view.png"/></a></td>
                            <td align="center"><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'">'.count($news['nb_article']).'</a>
                            </td>
                            <td align="center"><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_INSC.'&id_news='.$id_news.'">'.$tot_nb_insc.'</a>
                            </td>
                            <td align="center"><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_DMDINSC.'&id_news='.$id_news.'">'.count($news['nb_dmd']).'</a>
                            </td>
                            <td align="center">'.$date_cre['date'].'
                            </td>
                            <td align="center">
                                <a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'"><img src="./common/modules/system/img/crayon.gif" title="'.$_DIMS['cste']['_DIMS_MODIFY'].'"/></a>&nbsp;
                                <a href="javascript:void(0);" onclick="dims_confirmlink(\''.dims_urlencode("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public&action="._NEWSLETTER_ACTION_DELETE."&id_news=".$id_news).'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/modules/system/img/ico_delete.gif" title="'.$_DIMS['cste']['_DELETE'].'"/></a>
                            </td>
                         </tr>';
    }
    echo '          </table>
                </td>
            </tr>';
}
else {
    echo '  <tr>
                <td>';
                    echo $_DIMS['cste']['_DIMS_LABEL_NO_NEWSLETTER'];
    echo '      </td>
            </tr>';
}
echo '</table>';
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADD_NEWSLETTER'], './common/img/add.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'\'');

echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_LIST_EMAIL.'\'">'.$_DIMS['cste']['_DIMS_NEWSLETTER_GESTION_MAILING'].'</a>';
//echo '<input type="button" value="'.$_DIMS['cste']['_DIMS_LABEL_ADD_NEWSLETTER'].'" onclick="javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'\'"/>';
echo $skin->close_simplebloc();
?>
