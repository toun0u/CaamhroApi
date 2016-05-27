<?php

/**
 * Description of view_box_selecter_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_box_selecter_factory {

    const inbox = 1;
    const outbox = 2;
    const drafts = 3;
    const archives = 4;
    const junk = 5;
    const follower = 6;

    public static function buildViewSelecter($selected, $id_user, $type_messagerie) {
        //on construit les liens
		$dims = dims::getInstance();
        switch($type_messagerie){
            case controller_ticket::TYPE_MESSAGE_BACK_OFFICE_PUBLIC_VIEW :
                $action_inbox = _OP_TICKET_VIEW_INBOX_BOPV ;
                $action_outbox = _OP_TICKET_VIEW_OUTBOX_BOPV;
                $action_drafts = _OP_TICKET_VIEW_DRAFTS_BOPV;
                $action_junk = _OP_TICKET_VIEW_JUNK_BOPV;
                $action_archives = _OP_TICKET_VIEW_ARCHIVES_BOPV;
                $action_follow = _OP_TICKET_VIEW_FOLLOW_BOPV;
                break;
            case controller_ticket::TYPE_MESSAGE_BACK_OFFICE_INTERNE :
                $action_inbox = _OP_TICKET_VIEW_INBOX_BOI ;
                $action_outbox = _OP_TICKET_VIEW_OUTBOX_BOI;
                $action_drafts = _OP_TICKET_VIEW_DRAFTS_BOI;
                $action_junk = _OP_TICKET_VIEW_JUNK_BOI;
                $action_archives = _OP_TICKET_VIEW_ARCHIVES_BOI;
                $action_follow = _OP_TICKET_VIEW_FOLLOW_BOI;
                break;
            case controller_ticket::TYPE_MESSAGE_FRONT_OFFICE :
                $action_inbox = _OP_TICKET_VIEW_INBOX ;
                $action_outbox = _OP_TICKET_VIEW_OUTBOX;
                $action_drafts = _OP_TICKET_VIEW_DRAFTS_FO;
                $action_junk = _OP_TICKET_VIEW_JUNK;
                $action_archives = _OP_TICKET_VIEW_ARCHIVES;
                $action_follow = _OP_TICKET_VIEW_FOLLOW_FO;
                break;
        }
        $attribut_inbox = "" ;
        $attribut_outbox = "" ;
        $attribut_drafts = "" ;
        $attribut_junk = "" ;
        $attribut_archives = "" ;
        $attribut_follow = "" ;

        $source = "";

        switch($selected){
            case self::inbox :
                $attribut_inbox = "disabled='false'";
                $source = "inbox";
                break;
            case self::outbox :
                $attribut_outbox = "disabled='false'";
                $source = "outbox";
                break;
            case self::drafts :
                $attribut_drafts = "disabled='false'";
                $source = "drafts";
                break;
            case self::junk :
                $attribut_junk = "disabled='false'";
                $source = "junk";
                break;
            case self::archives :
                $attribut_archives = "disabled='false'";
                $source = "archives";
                break;
            case self::follower :
                $attribut_follow = "disabled='false'";
                $source = "follow";
                break;
        }

        ?>
        <input type="hidden" id="bouton_source" value="<? echo $source; ?>"/>
        <button id="inbox" type="button" <? echo $attribut_inbox; ?> onclick="javascript:changeSelector('inbox', <? echo $id_user; ?>, <? echo $action_inbox; ?>,'<? echo $dims->getScriptEnv(); ?>')">
                    <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_INBOX']; ?>
		</button>
        <button id="outbox" type="button" <? echo $attribut_outbox; ?> onclick="javascript:changeSelector('outbox', <? echo $id_user; ?>, <? echo $action_outbox; ?>,'<? echo $dims->getScriptEnv(); ?>')">
                    <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_OUTBOX']; ?>
		</button>
        <button id="drafts" type="button" <? echo $attribut_drafts; ?> onclick="javascript:changeSelector('drafts', <? echo $id_user; ?>, <? echo $action_drafts; ?>,'<? echo $dims->getScriptEnv(); ?>')">
                    <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_DRAFTS']; ?>
		</button>
        <button id="archives" type="button" <? echo $attribut_archives; ?> onclick="javascript:changeSelector('archives', <? echo $id_user; ?>, <? echo $action_archives; ?>,'<? echo $dims->getScriptEnv(); ?>')">
                    <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_ARCHIVES']; ?>
		</button>
        <button id="junk" type="button" <? echo $attribut_junk; ?> onclick="javascript:changeSelector('junk', <? echo $id_user; ?>, <? echo $action_junk; ?>,'<? echo $dims->getScriptEnv(); ?>')">
                    <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_JUNK']; ?>
		</button>
        <button id="follow" type="button" <? echo $attribut_follow; ?> onclick="javascript:changeSelector('follow', <? echo $id_user; ?>, <? echo $action_follow; ?>,'<? echo $dims->getScriptEnv(); ?>')">
                    <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_FOLLOW']; ?>
		</button>
        <?
    }
}

?>
