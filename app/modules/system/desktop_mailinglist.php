<?php

//Recherche des derniers envois effectues
$sql_e =    "SELECT     c.subject,
                        c.id,
                        c.date_envoi,
                        l.id as id_list,
                        l.label

             FROM       dims_mailing_content c

             INNER JOIN dims_mailing_content_list cl
                ON        cl.id_content = c.id

             INNER JOIN dims_mailing_list l
                ON      l.id = cl.id_list

             WHERE      c.id_workspace = :workspaceid
                AND     c.id_user = :userid

             ORDER BY   c.date_envoi DESC
             LIMIT 0,5";
//echo $sql_e;
$res_e = $db->query($sql_e, array(
    ':workspaceid'  => $_SESSION['dims']['workspaceid'],
    ':userid'       => $_SESSION['dims']['userid']
));
$last_send = '';
if($db->numrows($res_e) > 0) {
    $last_send = '<table width="100%" cellpadding="0" cellspacing="0">
                        <tr class="trl1">
                            <th align="left" width="15%">'.$_DIMS['cste']['_DIMS_LABEL_SEND_DATE'].'</th>
                            <th align="left" width="65%">'.$_DIMS['cste']['_SUBJECT'].'</th>
                            <th align="left">'.$_DIMS['cste']['_DIMS_LABEL_MAILINGLIST'].'</th>
                        </tr>';
    $tab_env = array();
    while($tab_c = $db->fetchrow($res_e)) {
        if(!isset($tab_env[$tab_c['id']])) $tab_env[$tab_c['id']] = array();
        $tab_env[$tab_c['id']] = $tab_c;
    }
    $class = "trl1";

//dims_print_r($tab_env);

    foreach($tab_env as $id_env => $tab_e) {

        $class = ($class == "trl1") ? 'trl2' : 'trl1';
        $date_e = dims_timestamp2local($tab_e['date_envoi']);
        $list_list = '';
        //on recherche le nombre de listes attachées
        $sql_l =    "SELECT         l.id
                     FROM           dims_mailing_list l
                     INNER JOIN     dims_mailing_content_list cl
                     ON             cl.id_list = l.id
                     AND            cl.id_content = :idcontent ";

        $res_l = $db->query($sql_l, array(
            ':idcontent' =>  $id_env
        ));
        $list_list = $db->numrows($res_l);

        $last_send .= '<tr class='.$class.'>
                            <td>'.$date_e['date'].'</td>
                            <td>'.$tab_e['subject'].'</td>
                            <td>'.$list_list.'</td>
                        </tr>';
    }
    $last_send .= '</table>';
}
else {
    $last_send = $_DIMS['cste']['_DIMS_MAILING_NO_SENDING'];
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

if($db->numrows($res_ml) > 0) {
    $list_diffusion = '<font style="font-weight:normal;">';
    while($tab_l = $db->fetchrow($res_ml)) {
        $list_diffusion .= '<a href="admin.php?submenu='.dims_const::_DIMS_SUBMENU_DIFFUSION_LIST.'&action=add_mailinglist&id_mail='.$tab_l['id'].'">'.$tab_l['label']."</a>, ";
    }
    $list_diffusion = substr($list_diffusion, 0, -2);
    $list_diffusion .= "</font>";
}
else {
    $list_diffusion = "---";
}

?>
<div style="width:98%;height:225px;overflow:auto;padding:5px;">
    <table width="100%" cellpadding="0" cellspacing="2">
        <tr>
            <td align="right">
                <a href="admin.php?dims_mainmenu=<? echo dims_const::_DIMS_MENU_HOME; ?>&dims_desktop=block&dims_action=public&submenu=<? echo dims_const::_DIMS_SUBMENU_DIFFUSION_LIST; ?>"><img src="./common/img/mailinglist.png" border="0">&nbsp;<?php echo $_DIMS['cste']['_DIMS_MAILING_MANAGE_LIST'] ?></a>
            </td>
        </tr>
        <tr>
            <td align="left" style="color:#3E3E3E;font-weight:none;">
                <img src="./common/img/mailsend.png" border="0">&nbsp;
				<?php echo $_DIMS['cste']['_DIMS_MAILING_LAST_SENDING']; ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                <?php echo $last_send; ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td align="left" style="color:#3E3E3E;font-weight:none;">
				<img src="./common/img/mailing.png" border="0">&nbsp;
                <?php echo $_DIMS['cste']['_DIMS_NEWSLETTER_YOUR_MAILING_LIST']."<br>".$list_diffusion; ?>
            </td>
        </tr>
    </table>
</div>
