<?php
/*
require_once DIMS_APP_PATH.'modules/system/class_action.php';
require_once DIMS_APP_PATH.'modules/system/class_action_detail.php';
require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
require_once DIMS_APP_PATH.'modules/system/class_inscription.php';
require_once DIMS_APP_PATH.'modules/system/class_contact.php';*/

if(!isset($_SESSION['dims']['IMPORT_MISSION']['id_evt'])) $_SESSION['dims']['IMPORT_MISSION']['id_evt'] = '';

$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true, true, false, $_SESSION['dims']['IMPORT_MISSION']['id_evt']);
$_SESSION['dims']['IMPORT_MISSION']['id_evt'] = $id_evt;

$event = new action();
$event->open($id_evt);

$import_op = dims_load_securvalue('import_op', dims_const::_DIMS_NUM_INPUT, true,true,false);
echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT']." ".$_DIMS['cste']['_DIMS_PARTICIP']." : ".$event->fields['libelle']);
?>

<table style="text-align:center; width: 100%;">
    <tr>
        <?php if($import_op < 1 && (!isset($_FILES['srcfilect']) || empty($_FILES['srcfilect']['name']))) {
            ?>
            <td>
                <span style="font-weight:bold;font-size:16px;">1</span><br/>
                <span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?></span>
            </td>
            <?php
        }
        else {
            ?>
            <td>
                <span style="font-weight:bold;font-size:16px;">1</span><br/>
                <?php echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?>
            </td>
            <?php
        }

        if($import_op == 1 && isset($_FILES['srcfilect']) && !empty($_FILES['srcfilect']['name'])) {
            ?>
            <td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">2</span><br/>
                <span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_CONTACTS'];?></span>
            </td>
            <?php
        }
        elseif($import_op>=1) {
            ?>
            <td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">2</span><br/>
                <?php echo $_DIMS['cste']['_IMPORT_CONTACTS'];?>
            </td>
            <?php
        }
        else {
            ?>
            <td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">2</span><br/>
                <?php echo $_DIMS['cste']['_IMPORT_CONTACTS'];?>
            </td>
            <?php
        }

        if($import_op == 2) {
            ?>
            <td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">3</span><br/>
                <span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?></span>
            </td>
            <?php
        }
        elseif($import_op > 2) {
            ?>
            <td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">3</span><br/>
                <?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?>
            </td>
            <?php
        }
        else {
            ?>
            <td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">3</span><br/>
                <?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?>
            </td>
            <?php
        }

        if($import_op == 3) {
            ?>
            <td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">4</span><br/>
                <span style="font-weight:bold;">
                    <?php
                    //echo $_DIMS['cste']['_DIMS_REGISTRATION'].' &amp;<br />'.$_DIMS['cste']['_IMPORT_SUMMARY'];
                    echo $_DIMS['cste']['_IMPORT_SUMMARY'];
                    ?>
                </span>
            </td>
            <?php
        }
        else {
            ?>
            <td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
            <td>
                <span style="font-weight:bold;font-size:16px;">4</span><br/>
                <?php
                //echo $_DIMS['cste']['_DIMS_REGISTRATION'].' &amp;<br />'.$_DIMS['cste']['_IMPORT_SUMMARY'];
                echo $_DIMS['cste']['_IMPORT_SUMMARY'];
                ?>
            </td>
            <?php
        }
        ?>

    </tr>
</table>

<?php

echo $skin->close_simplebloc();

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_MILESTONE'].' : ');

require_once DIMS_APP_PATH.'modules/events/public_events_import_inscr_switch.php';

echo $skin->close_simplebloc();
?>
