<?php

/**
 * Description of view_panel_actions_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_panel_actions_factory {

    const FO_BASIC_INBOX = 100;
    const BO_BASIC_INBOX = 200;
    const BOP_BASIC_INBOX = 300;
    public static function buildViewPanelAction($typeView, $id_user) {
        switch($typeView){
            case self::FO_BASIC_INBOX:
                ?>
                    <a style="cursor:pointer;" href="javascript:void(0);" onclick="javascript:createTicket(<? echo _OP_TICKET_VIEW_WRITE_TICKET; ?>, <?php echo 0;?>,'index.php');"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_WRITE_NEW_TICKET'];?></a><br />
                <?
                break;
            case self::BO_BASIC_INBOX:
                ?>
                    <a style="cursor:pointer;" onclick="javascript:createTicket(<? echo _OP_TICKET_VIEW_WRITE_TICKET; ?>, <?php echo 0;?>);"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_WRITE_NEW_TICKET'];?></a><br />
                <?
                break;
            case self::BOP_BASIC_INBOX:
                ?>
                    <a style="cursor:pointer;" href="javascript:void(0);" onclick="javascript:createTicket(<? echo _OP_TICKET_VIEW_WRITE_TICKET; ?>, <?php echo $id_user;?>);"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_WRITE_NEW_TICKET_TO_CUSTOMER']?></a><br />
                <?
                break;
        }
    }
}

?>
