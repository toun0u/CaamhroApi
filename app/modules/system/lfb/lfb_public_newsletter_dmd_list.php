<?php
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);

//recuperation des donnees
$sql = 'SELECT      *
        FROM        dims_mod_newsletter_inscription
        WHERE       id_newsletter = :idnewsletter ';

if(isset($upname) && $upname == 1 ) {
    $sql .= " ORDER BY		nom DESC, prenom DESC";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
    $sql .= " ORDER BY		nom ASC, prenom ASC";
    $opt_trip = 1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
    $sql .= " ORDER BY		email DESC ";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
    $sql .= " ORDER BY		email ASC ";
    $opt_trip = -1;
    $opt_trit = 2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
    $sql .= " ORDER BY		date_inscription DESC ";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
    $sql .= " ORDER BY		date_inscription ASC ";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = 3;
}
else {
    $sql .= " ORDER BY    date_inscription DESC";
    $opt_trip = -1;
    $opt_trit = -2;
    $opt_tric = -3;
}


$res = $db->query($sql, array(
    ':idnewsletter' => $id_news
));

$tab_dmd = array();
while($tab_res = $db->fetchrow($res)){
    $tab_dmd[$tab_res['id']] = $tab_res;
}

?>
<table width="100%" cellpadding="0" cellspacing="0" style="clear:both;">
<?php
if(count($tab_dmd) > 0) {
    $class = "trl1";
    ?>
    <tr style="background-color:#FFFFFF;font-size:11px;font-weight:bold;height:20px;">
        <td>&nbsp;</td>
        <td>
            <?php echo '<a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd&upname='.$opt_trip.'">'.$_SESSION['cste']['_DIMS_LABEL_NAME'].'</a>'; ?>
        </td>
        <td>
            <?php echo '<a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd&upname='.$opt_trit.'">'.$_SESSION['cste']['_DIMS_LABEL_EMAIL'].'</a>'; ?>
        </td>
        <td>
            <?php echo '<a href="'.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd&upname='.$opt_tric.'">'.$_SESSION['cste']['_DIMS_LABEL_DATE_REGISTRATION'].'</a>'; ?>
        </td>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
        </td>
    </tr>
    <?php
    foreach($tab_dmd as $id_dmd => $inscription) {
        $class = ($class == "trl1") ? 'trl2' : 'trl1';
        $date_inscr = dims_timestamp2local($inscription['date_inscription']);
    ?>
    <tr class="<?php echo $class; ?>">
        <td></td>
        <td>
            <?php echo strtoupper($inscription['nom'])." ".$inscription['prenom'] ; ?>
        </td>
        <td>
            <?php echo $inscription['email']; ?>
        </td>
        <td>
            <?php echo $date_inscr['date']; ?>
        </td>
        <td>
            <a href="<?php echo $scriptenv; ?>?subaction=<?php echo _DIMS_NEWSLETTER_INSCR; ?>&list_insc=accept_attach&id_dmd=<?php echo $id_dmd; ?>">
                <img alt="<?php echo $_SESSION['cste']['_DIMS_VALID']; ?>" src="./common/img/checkdo.png" />
            </a>
            &nbsp;&nbsp;
            <a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?subaction=<?php echo _DIMS_NEWSLETTER_INSCR; ?>&list_insc=delete_attach&id_dmd=<?php echo $id_dmd; ?>','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');">
                <img alt="<?php echo $_SESSION['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']; ?>" src="./common/img/delete.png" />
            </a>
        </td>
    </tr>
    <?php
    }
}
else {
    ?>
    <tr>
        <td align="center" style="font-weight:bold;font-size:11px;background-color:#FFFFFF;padding:20px;">
            <?php echo $_SESSION['cste']['_DIMS_LABEL_NO_REGISTRATION']; ?>
        </td>
    </tr>
    <?php
}
?>
</table>
