<?php

/**
 * Description of view_message_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_message_factory {

    public static function buildViewMessage(ticket $ticket = null) {
        if(is_null($ticket)){
            ?>
            <span><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_NO_TICKET_SELECTED']; ?></span>
            <?
        }else{
            ?>

            <?
        }
    }
}

?>
