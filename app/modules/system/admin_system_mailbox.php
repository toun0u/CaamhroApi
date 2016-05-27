<?php
echo $skin->open_simplebloc($_DIMS['cste']['_SYSTEM_LABELICON_MAILBOX']);

if(isset($_GET['mailRetrieve']))
    $mailRetrieve = dims_load_securvalue('mailRetrieve', dims_const::_DIMS_NUM_INPUT, true, true, false);

if(isset($mailRetrieve))
{
    echo '<div style="float: left;">'.$_DIMS['cste']['_DIMS_LABEL_NB_MAIL_RETRIEVE'].' : '.$mailRetrieve.'</div>';
}

//bouton d'ajout de boite mail
echo '<div style="margin:10px;text-align:right;">
            <a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-secondary" href="'.$scriptenv.'?op=add_mailbox">
                    <span class="ui-button-text">'.$_DIMS['cste']['_DIMS_LABEL_MAILBOX_ADD'].'</span>
                    <span class="ui-button-icon-secondary ui-icon ui-icon-plus"></span>
            </a>
	</div>';
echo '<div style="clear:both;">';
$sql = 'SELECT
            id,
            server,
            login,
            protocol,
            port,
            crypto,
            label
        FROM
            dims_mod_webmail_inbox';
//echo $sql;
$ressource = $db->query($sql);

if($db->numrows($ressource) > 0)
{
    $columns = array();
    $columns['auto'][0] = array('label' => '#');
    $columns['right'][7] = array('label' => '&nbsp;', 'width' => '120');
    $columns['right'][6] = array('label' => $_DIMS['cste']['_DIMS_LABEL_PROTOCOL'], 'width' => '150');
    $columns['right'][5] = array('label' => $_DIMS['cste']['_DIMS_LABEL_CRYPTO'], 'width' => '150');
    $columns['right'][4] = array('label' => $_DIMS['cste']['_DIMS_LABEL_PORT'], 'width' => '150');
    $columns['right'][3] = array('label' => $_DIMS['cste']['_LOGIN'], 'width' => '150');
    $columns['right'][2] = array('label' => $_DIMS['cste']['_SERVER'], 'width' => '150');
    $columns['right'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_LABEL'], 'width' => '150');

    $c = 0;
    $values = array();

    while($result = $db->fetchrow($ressource))
    {

        $open   = $scriptenv.'?op=modify_mailbox&id_mailbox='.$result['id'];
        $delete = $scriptenv.'?op=delete_mailbox&id_mailbox='.$result['id'];
        $check  = $scriptenv.'?op=check_mailbox&id_mailbox='.$result['id'];

        $action = '
        <a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$open.'" title="'.$_DIMS['cste']['_MODIFY'].'">
            <span class="ui-button-icon ui-icon ui-icon-wrench"></span>
            <span class="ui-button-text">'.$_DIMS['cste']['_MODIFY'].'</span>
        </a>
        <a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="javascript:dims_confirmlink(\''.$delete.'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')" title="'.$_DIMS['cste']['_DELETE'].'">
            <span class="ui-button-icon ui-icon ui-icon-trash"></span>
            <span class="ui-button-text">'.$_DIMS['cste']['_DELETE'].'</span>
        </a>
        <a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$check.'" title="'.$_DIMS['cste']['_DIMS_WEBMAIL_CHECK_MANUALLY'].'">
            <span class="ui-button-icon ui-icon ui-icon-mail-closed"></span>
            <span class="ui-button-text">'.$_DIMS['cste']['_DIMS_WEBMAIL_CHECK_MANUALLY'].'</span>
        </a>';

        $values[$c]['values'][0] = array('label' => $result['id'], 'style' => '');
        $values[$c]['values'][1] = array('label' => $result['label'], 'style' => 'text-align:center;');
        $values[$c]['values'][2] = array('label' => $result['server'], 'style' => 'text-align:center');
        $values[$c]['values'][3] = array('label' => $result['login'], 'style' => 'text-align:center');
        $values[$c]['values'][4] = array('label' => $result['port'], 'style' => 'text-align:center');
        $values[$c]['values'][5] = array('label' => $result['crypto'], 'style' => 'text-align:center');
        $values[$c]['values'][6] = array('label' => $result['protocol'], 'style' => 'text-align:center');
        $values[$c]['values'][7] = array('label' => $action, 'style' => 'text-align:center');

        $values[$c]['link'] = '';
        $values[$c]['style']= '';

        $c++;
    }

    $skin->display_array($columns, $values);
}
echo '</div>';
echo $skin->close_simplebloc();
?>
