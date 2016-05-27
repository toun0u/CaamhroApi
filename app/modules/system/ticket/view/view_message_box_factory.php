<?php

/**
 * Description of view_message_box_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_message_box_factory {

    public static function buildViewMessageBox(array $liste_messages = null) {
        require_once DIMS_APP_PATH.'/include/class_skin_common.php';
        $skin = skin_common::getInstance();
        $data = array();
        $elements = array();

        $data['headers'][0] = $_SESSION['cste']['_DIMS_LABEL_TICKET_AUTHOR'];
        $data['headers'][1] = $_SESSION['cste']['_DIMS_LABEL_TICKET_SUBJECT'];
        $data['headers'][2] = $_SESSION['cste']['_DIMS_LABEL_TICKET_ATTACHMENTS_SHORT'];
        $data['headers'][3] = $_SESSION['cste']['_DIMS_DATE'];
        $data['headers'][4] = $_SESSION['cste']['_LABEL_ACTION'];
        if (!empty($liste_messages)) {
            foreach ($liste_messages as $message_status) {
                if ($message_status instanceof ticket_status) {
                    $elements[] = self::buildLigneMessageBox($message_status->getTicket(), $message_status->getStatus());
                } else if ($message_status instanceof ticket) {
                    $elements[] = self::buildLigneMessageBox($message_status, $message_status->getStatus());
                }
            }
        }
        $data['data']['elements'] = $elements;
        echo '<div>' . $skin->displayArray($data) . '</div>';
    }

    public static function buildViewMessageBoxFront(array $liste_messages = null) {
        echo '<script type="text/javascript" src="/common/modules/system/ticket/ticket_script.js"></script>';
        require_once DIMS_APP_PATH.'/include/class_skin_common.php';
        $skin = skin_common::getInstance();
        $data = array();
        $elements = array();

        $data['headers'][0] = $_SESSION['cste']['_DIMS_LABEL_TICKET_AUTHOR'];
        $data['headers'][1] = $_SESSION['cste']['_DIMS_LABEL_TICKET_SUBJECT'];
        $data['headers'][2] = $_SESSION['cste']['_DIMS_LABEL_TICKET_ATTACHMENTS_SHORT'];
        $data['headers'][3] = $_SESSION['cste']['_DIMS_DATE'];
        $data['headers'][4] = $_SESSION['cste']['_LABEL_ACTION'];
        if (!empty($liste_messages)) {
            foreach ($liste_messages as $message_status) {
                if ($message_status instanceof ticket_status) {
                    $elements[] = self::buildLigneMessageBox($message_status->getTicket(), $message_status->getStatus());
                } else if ($message_status instanceof ticket) {
                    $elements[] = self::buildLigneMessageBox($message_status, $message_status->getStatus());
                }
            }
        }
        $data['data']['elements'] = $elements;
		require_once DIMS_APP_PATH.'/include/class_skin_common.php';
		$skin = new skin_common();
        echo '<div>' . $skin->displayArray($data) . '</div>';
    }

    private static function buildLigneMessageBox(ticket $ticket, $statut) {
		$dims = dims::getInstance();
        $elems = array();

        $sender = $ticket->getSender();
        if ($sender instanceof group) {
            $elems[0] = $sender->getLabel();
        } else if ($sender instanceof user) {
            $elems[0] = $sender->getLastname() . " " . $sender->getFirstname();
        }

        $elems[1] = $ticket->getTitle();

        if ($ticket->hasObjetJoint()) {//todo a transfromer en if(hasPiceJointe());
            $elems[2] = "<img src=\"./common/img/attachment.png\" title=\"".$_SESSION['cste']['_DIMS_LABEL_TICKET_ATTACHMENTS']."\" alt=\"".$_SESSION['cste']['_DIMS_LABEL_TICKET_ATTACHMENTS']."\" />";
        } else {
            $elems[2] = $_SESSION['cste']['_DIMS_LABEL_TICKET_NO_ATTACHEMENTS'];
        }
        $elems[3] = dims_nicetime($ticket->getTimestp());

        $elems[4] = '<a style="cursor:pointer;" onclick="javascript:showTicket(' . $ticket->getId() . ', ' . _OP_TICKET_VIEW_TICKET . ',\''.$dims->getScriptEnv().'\');">'.$_SESSION['cste']['_DIMS_LABEL_TICKET_SEE'].'</a>
					';

        return $elems;
    }
}

?>
