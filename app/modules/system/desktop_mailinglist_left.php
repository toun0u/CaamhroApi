<?php

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch($action) {
    case 'add_mailinglist':
            $id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_CHAR_INPUT, true, true);
            break ;
    case 'add_sending';
            $id_env = dims_load_securvalue('id_env', dims_const::_DIMS_CHAR_INPUT, true, true);
            break ;
    default :
            break ;
}
//recherche des listes de diffusion
$sql_ml =   "SELECT     l.label,
                        l.id
                         FROM       dims_mailing_list l
                         WHERE      l.id_workspace = :workspaceid
                         AND        l.id_user = :userid ";

$res_ml = $db->query($sql_ml, array(
    ':workspaceid'  => $_SESSION['dims']['workspaceid'],
    ':userid'       => $_SESSION['dims']['userid']
));

$list_diffusion = '';
if($db->numrows($res_ml) > 0) {
        $list_diffusion = '<table width="100%" cellpadding="4" cellspacing="0">
                                                        <tr class="trl1">
                                                                <th align="left">'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</th>
                                                                <th align="left" width="40%">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NBINSC'].'</th>
                                                                <th align="left" width="50px"></th>
                                                        </tr>';
        $class = "trl1";
        while($tab_c = $db->fetchrow($res_ml)) {

                $class = ($class == "trl1") ? 'trl2' : 'trl1';

                //on recherche le nbr d'inscrit pour la liste courante
                $res_nb = $db->query("SELECT 	id
                                      FROM 		dims_mailing_email
                                      WHERE 	id_list = :idlist ", array(
                            ':idlist' => $tab_c['id']
                ));
                $nb_insc = $db->numrows($res_nb);

                if (!empty($id_mail) && $id_mail == $tab_c['id'])
                        $list_diffusion .= '<tr style="background-color: #B0B0C3;">';
                else $list_diffusion .= '<tr class="'.$class.'">';

                $list_diffusion .=		'<td onClick="javascript:location.href=\''.$scriptenv.'?action=add_mailinglist&id_mail='.$tab_c['id'].'\'">'.$tab_c['label'].'</td>
                                                                <td onClick="javascript:location.href=\''.$scriptenv.'?action=add_mailinglist&id_mail='.$tab_c['id'].'\'">'.$nb_insc.'</td>
                                                                <td align="right">
                                                                        <a href="'.$scriptenv.'?action=add_mailinglist&id_mail='.$tab_c['id'].'"><img src="./common/img/edit.gif" title="Modifier"/></a> /
                                                                        <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$scriptenv.'?action=delete_listdiff&id_list='.$tab_c['id'].'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/delete.png"/></a>
                                                                </td>
                                                        </tr>';
        }
        $list_diffusion .= '</table>';
}
else {
        $list_diffusion = "---";
}
// affichage mailing list
?>
<div style="width:30%;float:left;">

                <?
                echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_NEWSLETTER_YOUR_MAILING_LIST']);
                ?>
                <table width="100%" cellpadding="0" cellspacing="2">
                        <tr>
                                <td width="100%" align="right">
                                        <a href="<? echo $scriptenv ?>?action=add_mailinglist">
                                                <img src="./common/img/add_user.png" border="0"/><? echo $_DIMS['cste']['_DIMS_NEWSLETTER_ADD_LIST_MAILING']; ?>
                                        </a>
                                </td>

                        </tr>
                        <tr>
                                <td align="center" style="vertical-align:top;">
                                        <?php echo $list_diffusion; ?>
                                </td>

                        </tr>
                </table>
                <?
                echo $skin->close_simplebloc();
                ?>

        <?
                $sql_e =    "SELECT     c.subject,
                                                                c.id,
                                                                c.date_envoi

                                         FROM       dims_mailing_content c

                                         WHERE      c.id_workspace = :workspaceid
                                                AND     c.id_user = :userid

                                         ORDER BY   c.date_create DESC";

                $res_e = $db->query($sql_e, array(
                    ':workspaceid'  => $_SESSION['dims']['workspaceid'],
                    ':userid'       => $_SESSION['dims']['userid']
                ));
                $sendings = '';
                if($db->numrows($res_e) > 0) {
                        $sendings = '<table width="100%" cellpadding="4" cellspacing="0">
                                                                <tr class="trl1">
                                                                        <th align="left">'.$_DIMS['cste']['_SUBJECT'].'</th>
                                                                        <th align="left" width="130px">'.$_DIMS['cste']['_DIMS_LABEL_SEND_DATE'].'</th>
                                                                        <th align="left" width="70px"></th>
                                                                </tr>';
                        $tab_env = array();
                        $class = "trl1";
                        while($tab_c = $db->fetchrow($res_e)) {
                                //on verifie si l'envoi est bien lié à au moins une liste
                                $res_v = $db->query("SELECT id_list FROM dims_mailing_content_list WHERE id_content = :idcontent LIMIT 0,1", array(
                                    ':idcontent' => $tab_c['id']
                                ));
                                $link_env = '';
                                if($db->numrows($res_v) == 1) $link_env = '/ <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$scriptenv.'?action=send_mail&id_env='.$tab_c['id'].'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/mail_sent.png" title="Envoyer le message"/></a>';

                                $class = ($class == "trl1") ? 'trl2' : 'trl1';
                                $date_e = ($tab_c['date_envoi'] != '') ? dims_timestamp2local($tab_c['date_envoi']) : '';

                                if (!empty($id_env) && $id_env == $tab_c['id'])
                                        $sendings .= '<tr style="background-color: #B0B0C3;">';
                                else $sendings .= '<tr class="'.$class.'">';
                                $sendings .= 		'<td onClick="javascript:location.href=\''.$scriptenv.'?action=add_sending&id_env='.$tab_c['id'].'\'">'.$tab_c['subject'].'</td>
                                                                        <td onClick="javascript:location.href=\''.$scriptenv.'?action=add_sending&id_env='.$tab_c['id'].'\'">'.$date_e['date'].'</td>
                                                                        <td>
                                                                                <a href="'.$scriptenv.'?action=add_sending&id_env='.$tab_c['id'].'">
                                                                                        <img src="./common/img/edit.gif" title="Modifier"/>
                                                                                </a> /
                                                                                <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$scriptenv.'?action=delete_envoi&id_env='.$tab_c['id'].'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">
                                                                                        <img src="./common/img/delete.png"/>
                                                                                </a>
                                                                                '.$link_env.'
                                                                        </td>
                                                                </tr>';
                        }
                        $sendings .= '</table>';
                }
                else {
                        $sendings = $_DIMS['cste']['_DIMS_MAILING_NO_SENDING'];
                }
        // affichage mails
?>

                <?
                echo $skin->open_simplebloc($_DIMS['cste']['_FAQ_SEND_MESSAGE']);
                ?>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="100%" align="right">
                            <a href="<? echo $scriptenv ?>?action=add_sending">
                                                            <img src="./common/img/mail_create.png" border="0"/>&nbsp;<? echo $_DIMS['cste']['_DIRECTORY_LEGEND_EMAIL'];?>
                                                    </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="vertical-align:top;">
                            <?php echo $sendings; ?>
                        </td>
                    </tr>
                </table>
                <?
                        echo $skin->close_simplebloc();
                ?>

</div>
