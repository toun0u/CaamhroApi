<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_NEWSLETTER_LIST_MODELS']);

//recuperation des donnees
$params = array();
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
        WHERE       n.id_workspace in ('.$db->getParamsFromArray($listworkspace_nl, 'idworkspace', $params).')
        ORDER BY		n.label ASC';


$res = $db->query($sql, $params);

$tab_news = array();
while($tab_res = $db->fetchrow($res)){
    //on compte le nombre d'inscrits issus des mailing lists backoffice

    if(isset($tab_res['id_dmd']) && $tab_res['id_dmd'] != '') {
        $tab_news[$tab_res['id']]['nb_dmd'][$tab_res['id_dmd']] = $tab_res['id_dmd'];
    }

    $tab_news[$tab_res['id']]['news'] = $tab_res;
}

echo '<table width="100%" cellpadding="0" cellspacing="0">';
echo    '<tr>
            <td colspan="4" style="padding:5px 0;">';
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADD_MODEL'], './common/img/add.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'\'');
echo        '</td>
        </tr>';
if(count($tab_news) > 0) {
    $news_default = 0;
	$class='';
    foreach($tab_news as $id_nw => $news) {

        if(isset($_SESSION['dims']['default_newsletter']) && $_SESSION['dims']['default_newsletter'] == 0) {
            $_SESSION['dims']['default_newsletter'] = $id_nw;
            $_SESSION['dims']['current_newsletter'] = $id_nw;
            $id_news = $id_nw;
        }

        if($class == "trl1") $class = "trl2";
        else $class = "trl1";

        if($id_nw == $_SESSION['dims']['current_newsletter']) $style = 'style="background-color:#BCBCBC;"';
        else $style = "";

		if (!isset($news['nb_dmd'])) $news['nb_dmd']=array();
        echo '<tr class="'.$class.'" '.$style.'>';
        echo    '<td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&id_news='.$id_nw.'">'.dims_strcut($news['news']['label'],28).'</a></td>';
        echo    '<td>'.count($news['nb_dmd']).' '.$_DIMS['cste']['_DIMS_NEWSLETTER_INSC_REQUEST'].'</td>';
        echo    '<td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&id_news='.$id_nw.'"><img src="./common/img/edit.gif"/></a></td>';
        echo    '<td><a href="javascript:void(0);" onclick="dims_confirmlink(\''.dims_urlencode("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_NEWSLETTER."&cat=0&dims_desktop=block&dims_action=public&action="._NEWSLETTER_ACTION_DELETE."&id_news=".$id_nw).'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/delete.gif"/></a></td>';
        echo '</tr>';
    }
}
else {
    echo        '<td>'.$_DIMS['cste']['_DIMS_LABEL_NO_NEWSLETTER'].'</td>';
}
echo '</table>';

echo $skin->close_simplebloc();
?>
