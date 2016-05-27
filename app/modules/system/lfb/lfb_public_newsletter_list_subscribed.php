<?php
//$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);
$search_name = dims_load_securvalue('search_name', dims_const::_DIMS_CHAR_INPUT, false,true);
//$view='';
$params = array();
$sql = '    SELECT          ns.*,
                            c.*
            FROM            dims_mod_newsletter_subscribed ns
            INNER JOIN      dims_mod_business_contact c
            ON              c.id = ns.id_contact
            WHERE           id_newsletter = :idnewsletter ';
$params[':idnewsletter'] = $id_news;

if($search_name != '') {
    $sql .= ' AND (c.lastname LIKE :searchname OR c.firstname LIKE :searchname ) ';
    $params[':searchname'] = $search_name.'%';
}
if(isset($upname) && $upname == 1 ) {
    $sql .= " ORDER BY		c.lastname DESC, c.firstname DESC, ns.etat DESC";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
    $sql .= " ORDER BY		c.lastname ASC, c.firstname ASC, ns.etat DESC";
    $opt_trip = 1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
    $sql .= " ORDER BY		ns.date_inscription DESC, c.lastname ASC, c.firstname ASC, ns.etat DESC ";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
    $sql .= " ORDER BY		ns.date_inscription ASC, c.lastname ASC, c.firstname ASC, ns.etat DESC ";
    $opt_trip = -1;
    $opt_trit = 2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
    $sql .= " ORDER BY		ns.date_desinscription DESC, c.lastname ASC, c.firstname ASC, ns.etat DESC ";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
    $sql .= " ORDER BY		ns.date_desinscription ASC, c.lastname ASC, c.firstname ASC, ns.etat DESC ";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = 3;
}
else {
    $sql .= " ORDER BY    c.lastname ASC, c.firstname ASC, ns.etat DESC";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
$res = $db->query($sql, $params);



$nltt = new newsletter();
$nltt->open($id_news);

//listing des mailing lists rattachees
$sql_ml = ' SELECT      ml.*,
                        ct.id as id_nb_ct,
                        mn.id as id_link
            FROM        dims_mod_newsletter_mailing_list ml
            INNER JOIN  dims_mod_newsletter_mailing_news mn
            ON          mn.id_mailing = ml.id
            AND         mn.id_newsletter = :idnewsletter
            INNER JOIN  dims_mod_newsletter_mailing_ct ct
            ON          ct.id_mailing = ml.id
            AND         ct.actif = 1';

$res_ml = $db->query($sql_ml, array(
    ':idnewsletter' => $id_news
));
$mailing_list = '';
$sel_list = '';
$tab_mailing = '';
if($db->numrows($res_ml) > 0) {
    $class = 'trl1';

    $mailing_list .= '<tr class="trl1" style="font-size:13px;">
                            <td width="25%">Label</td>
                            <td>Comment</td>
                            <td>Nb mail</td>
                            <td></td>
                        </tr>';
    $cpt_insc = 0;
    while($tab_ml = $db->fetchrow($res_ml)) {
        $tab_mailing[$tab_ml['id']] = $tab_ml;
        $sql_nb_ct = 'SELECT id FROM dims_mod_newsletter_mailing_ct WHERE id_mailing = :idmailing AND actif = 1';
        $res_nb_ct = $db->query($sql_nb_ct, array(
            ':idmailing' => $tab_ml['id']
        ));

        $tab_mailing[$tab_ml['id']]['nb_ct'] = $db->numrows($res_nb_ct);
    }

    foreach($tab_mailing as $id_m => $inf_m) {
        if($class == 'trl1') $class = 'trl2';
        else $class = 'trl1';


        $comment = substr($inf_m['comment'], 0, 50).'...';

        $mailing_list .=    '<tr class="'.$class.'">
                                <td>'.$inf_m['label'].'</td>
                                <td>'.$comment.'</td>
                                <td>'.$inf_m['nb_ct'].'</td>
                                <td><a href="'.$scriptenv.'?action='._NEWSLETTER_ACTION_SUPP_LIST.'&id_link='.$inf_m['id_link'].'&from=to_insc" title="Delete this link"><img src="./common/img/delete.png"/></a></td>
                            </tr>';
    }
}

//recherche des mailing listes disponibles pour le rattachement
$sql_mr = 'SELECT * FROM dims_mod_newsletter_mailing_list WHERE id_user_create = :userid ';
$res_mr = $db->query($sql_mr, array(
    ':userid' => $_SESSION['dims']['userid']
));
while($tab_mr = $db->fetchrow($res_mr)) {
    $sel_list .= '<option value="'.$tab_mr['id'].'">'.$tab_mr['label'].'</option>';
}

$title = $_DIMS['cste']['_DIMS_NEWSLETTER_INSCRITS'].' : "'.$nltt->fields['label'].'"';

//echo $skin->open_simplebloc($title);

echo '  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:white;">
            <tr>
                <td>';
//echo  dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'\';', '', 'float:right;');
echo '          </td>
            </tr>
            <tr>
                <td width="100%">
                    <form id="search_ct" name="search_ct" method="POST" action="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=search_ct">';
                    // Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("ct_search");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '              <table width="100%">
                        <tr>
                            <td align="right" width="35%">'.$_DIMS['cste']['_ADD_CT'].' :&nbsp;
                            </td>
                            <td align="left" width="20%"><input type="text" name="ct_search" id="ct_search" value="';
if(isset($search) && $search != '') echo $search;
                        echo '"/>
                            </td>
                            <td>';
echo dims_create_button($_DIMS['cste']['_SEARCH'], './common/img/search.png', 'javascript:document.search_ct.submit();', '', 'float:left;');
echo '                      </td>
                        </tr>
                    </table>
                    </form>
                </td>
            </tr>';
//creer des rattachement avec des mailings lists
if($db->numrows($res_mr) > 0) {
echo        '<tr>
                <td width="100%">
                    <form id="add_mailing" name="add_mailing" method="POST" action="'.$scriptenv.'?action='._NEWSLETTER_SAVE_RATTACH_NEWS.'&news_linked='.$id_news.'&from=to_insc">';
                    // Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("id_mail");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '              <table width="100%">
                        <tr>
                            <td align="right" width="35%">'.$_DIMS['cste']['_DIMS_LABEL_MAILING_TO_ATTACH'].' :&nbsp;
                            </td>
                            <td align="left" width="20%">
                                <select id="id_mail" name="id_mail" style="width:130px;">
                                    <option value="">--</option>
                                    '.$sel_list.'
                                </select>
                            </td>
                            <td align="left">';
/////// ATTENTION : $news est defini dans lfb_public_newsletter case '_NEWSLETTER_SEARCH_CT'
echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.add_mailing.submit();', '', 'float:left;');
echo '                      </td>
                        </tr>
                    </table>
                    </form>
                </td>
            </tr>';
}

if (!isset($view)) $view='';
/////// ATTENTION : $view est defini dans lfb_public_newsletter case '_NEWSLETTER_SEARCH_CT'
/////// permet d'afficher les resultats de la recherche pour l'ajout d'un ct
echo        $view;

//affichage des mailings lists rattachees
if($db->numrows($res_ml) > 0) {
echo        '<tr>
                <td style="border-bottom:#222222 1px dotted;">&nbsp;
                </td>
            </tr>
            <tr>
                <td>
                    <div style="width:100%;height:150px;overflow:auto;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="font-size:13px;padding:10px;" colspan="4">'.$_DIMS['cste']['_DIMS_LABEL_LIST_MAILING_LINKED'].' :</td>
                        </tr>
                        '.$mailing_list.'
                    </table>
                    </div>
                </td>
            </tr>';
}

echo        '<tr>
                <td style="border-bottom:#222222 1px dotted;">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-size:13px;padding:10px;">
                '.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_CT_LINKED'].' :
                </td>
            </tr>
            <tr>
                <td style="font-size:11px;padding:10px;" align="right">
                <form id="search_contact" name="search_contact" method="POST" action="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr">';
                    // Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("search_name");
$tokenHTML = $token->generate();
echo $tokenHTML;
echo '              <label>'.$_DIMS['cste']['_DIMS_LABEL_SEARCH_FOR_CT'].' : </label>
                    <input type="text" name="search_name" id="search_name" value="'.$search_name.'"/>
                    '.dims_create_button($_DIMS['cste']['_SEARCH'], './common/img/search.png', 'javascript:document.search_contact.submit();', '', 'float:right;').'
                </form>
                </td>
            </tr>
            <tr>
                <td width="100%">';
                    if($db->numrows($res) > 0) {
                        echo '<div style="height:250px;overflow:auto;width:100%;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr class="trl1" style="font-size:14px;">
                                        <td></td>
                                        <td><a href="'.$scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr&upname='.$opt_trip.'">'.$_DIMS['cste']['_DIMS_LABEL_PERSONNE'].'</a></td>
                                        <td><a href="'.$scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr&upname='.$opt_trit.'">'.$_DIMS['cste']['_DIMS_LABEL_DATE_REGISTRATION'].'</a></td>
                                        <td><a href="'.$scriptenv.'?action='._NEWSLETTER_VIEW_INSC.'&subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_inscr&upname='.$opt_tric.'">'.$_DIMS['cste']['_DIMS_LABEL_DATE_UNREGISTRATION'].'</a></td>
                                        <td>'.$_DIMS['cste']['_DIMS_ACTIONS'].'</td>
                                    </tr>';
                        $class = "trl1";
                        while($tab_insc = $db->fetchrow($res)) {
                            if($class == "trl1") $class = "trl2";
                            else $class = "trl1";

                            $etat = '';
                            $action = '';
                            if($tab_insc['etat'] == 1) {
                                $etat .= '<img src="./common/modules/system/img/ico_point_green.gif" title="'.$_DIMS['cste']['_DIMS_NEWSLETTER_ACTIVE'].'"/>';
                                $action .= '<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$scriptenv.'?action='._NEWSLETTER_DELETE_INSC.'&id_contact='.$tab_insc['id_contact'].'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].' '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION'].'"/></a>';
                            }
                            else {
                                $etat .= '<img src="./common/modules/system/img/ico_point_red.gif" title="'.$_DIMS['cste']['_DIMS_NEWSLETTER_INACTIVE'].'"/>';
                                $action .= '<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$scriptenv.'?action='._NEWSLETTER_RECREATE_INSC.'&id_contact='.$tab_insc['id_contact'].'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/add.gif" title="'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_RESUBSCRIBE'].'"/></a>';
                            }

                            $nom = '';
                            $nom .= '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$tab_insc['id_contact'].'">'.$tab_insc['firstname'].' '.strtoupper($tab_insc['lastname']).'</a>';

                            $date_desinsc = '';
                            $date_insc = dims_timestamp2local($tab_insc['date_inscription']);
                            if($tab_insc['date_desinscription'] != '') $date_desinsc = dims_timestamp2local($tab_insc['date_desinscription']);
                            else $date_desinsc['date'] = '';

                            echo '  <tr class="'.$class.'">
                                        <td>'.$etat.'</td>
                                        <td>'.$nom.'</td>
                                        <td>'.$date_insc['date'].'</td>
                                        <td>'.$date_desinsc['date'].'</td>
                                        <td>'.$action.'</td>
                                    </tr>';
                        }
                        echo '   </table></div>';
                    }
                    else {
                        echo $_DIMS['cste']['_DIMS_NEWSLETTER_NO_INSCRITS'];
                    }
echo '          </td>
            </tr>
            <tr>
                <td>';
//echo  dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'\';', '', 'float:right;');
echo '          </td>
            </tr>
        </table>';

//echo $skin->close_simplebloc();
?>
