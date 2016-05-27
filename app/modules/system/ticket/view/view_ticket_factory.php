<?php

/**
 * Description of view_ticket_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_ticket_factory {
    public static function buildViewTicket(ticket $ticket) {
		global $_DIMS;
        self::buildBeginningOfPopup($_SESSION['cste']['_DIMS_LABEL_TICKET_CONSULT_TICKET']);

        //todo affichage de mon ticket
        //dims_print_r($ticket->fields);
		self::buildHead($ticket->getTimestp(),$ticket->getSender(),$ticket->getListDestinataire(),$ticket->fields['id_user'] == $_SESSION['dims']['userid']);

		self::buildBody($ticket);

        self::buildActions($ticket);

        self::buildEndingOfPopup();
    }

    public static function buildBeginningOfPopup($title){
        ?>
        <div id="popup_user">
			<div class="actions">
				<h2 style="margin-left:20px;float:left;margin-top:7px;"><? echo $title ; ?></h2>
				<img style="float:right; margin-right: 2px; margin-top:2px;cursor:pointer;" onclick="javascript:dims_hidepopup();" src="./common/modules/assurance/img/icon_close.gif" />
			</div>
			<div>
        <?
    }

    public static function buildEndingOfPopup(){
            ?>
            </div>
        </div><?
    }

	public static function buildActions($ticket){
		global $_DIMS;
		?>
		<div class="actionsTicket">
			<span class="suivi">
                            <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_FOLLOWED']; ?>
			</span>
			<span class="actions">
				<ul>
					<li style="cursor: pointer;">Marquer comme non lu</li>
                                        <li style="cursor: pointer;" onclick="javascript:changeStatusTicket(<? echo $ticket->getId(); ?>, <? echo ticket_status::ARCHIVED ; ?>,<? echo _OP_TICKET_CHANGE_STATUT_TICKET; ?>);"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_TO_ARCHIVE']; ?></li>
					<li style="cursor: pointer;" onclick="javascript:changeStatusTicket(<? echo $ticket->getId(); ?>, <? echo ticket_status::ARCHIVED ; ?>,<? echo _OP_TICKET_CHANGE_STATUT_TICKET; ?>);"><? echo $_DIMS['cste']['_DELETE']; ?></li>
                                        <li style="cursor: pointer;" onclick="javascript:forwardTicket(<? echo $ticket->getId(); ?>, <? echo _OP_TICKET_FORWARD_TICKET; ?>);"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_TO_TRANSFER']; ?></li>
					<li style="cursor: pointer;"><? echo $_DIMS['cste']['_DIMS_REPLY']; ?></li>
				</ul>
			</span>
		</div>
		<?
	}

	public static function buildBody($ticket){
		global $_DIMS;
		?>
		<div class="bodyTicket">
			<span id="subject"><? echo $_DIMS['cste']['_SUBJECT']; ?> : <? echo $ticket->fields['title']; ?></span>
			<span id="body">
				<span>
					<? echo $ticket->fields['message']; ?>
				</span>
			</span>
			<div id="linkedDoc">
				<?
					$nbPieces = $ticket->getCountObjetsJoints();
				?>
				<a style="cursor: pointer;" onclick="javascript:showLinkedObject(<? echo $ticket->getId(); ?>, <? echo _OP_TICKET_SHOW_LINKED_OBJECT; ?>, 'linkedDoc');">
					Voir les pi&egrave;ces jointes (<? echo $nbPieces; ?>)
				</a>
			</div>
		</div>
		<?
	}

	public static function buildHead($date,$sender,$dest,$ccCci){
		global $_DIMS;
		?>
		<div class="headTicket">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:55px;">
						<? echo $_DIMS['cste']['_DIMS_LABEL_TICKET_EMETTEUR']; ?> :
					</td>
					<td style="text-align:left;">
						<?
						if ($sender instanceof group)
							echo $_SESSION['cste']['_DIMS_LABEL_TICKET_THE_GROUP'].$sender->fields['label'];
						elseif($sender instanceof user)
							echo $sender->fields['lastname'].' '.$sender->fields['firstname']
						?>
					</td>
					<td style="width:100px;">
                        <?php echo $_SESSION['cste']['_AT']; ?> <? $d = dims_timestamp2local($date); echo $d['date']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:left;">
                        <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_DESTINATAIRES']." : "; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:left;">
						<?
						$lstDest = array();
						$lstCC = array();
						$lstCCi = array();
						foreach ($dest as $de){
							$u = $de->getDest();
							$label = '';
							if ($u instanceof group)
								$label = $u->fields['label'].' ('.$_SESSION['cste']['_GROUP'].')';
							if ($u instanceof user)
								$label = $u->fields['lastname'].' '.$u->fields['firstname'];
							if ($label != '')
								switch($de->getTypeLienDestinataire()){
									case $de::TYPE_DESTINATAIRE_FOR :
										$lstDest[] = $label;
										break;
									case $de::TYPE_DESTINATAIRE_CC :
										$lstCC[] = $label;
										break;
									case $de::TYPE_DESTINATAIRE_CCI :
										$lstCCi[] = $label;
										break;
								}
						}
						?>
						<ul id="destTicket">
                            <li><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_DEST_CC_SHORT']." : "; ?><? echo implode(', ',$lstDest); ?></li>
							<?
								if ($ccCci && count($lstCC) > 0)
									echo '<li> '.$_SESSION['cste']['_DIMS_LABEL_TICKET_DEST_CC_SHORT'].' : '.implode(', ',$lstCC).'</li>';
								if ($ccCci && count($lstCCi) > 0)
									echo '<li> '.$_SESSION['cste']['_DIMS_LABEL_TICKET_DEST_CCI_SHORT'].' : '.implode(', ',$lstCCi).'</li>';
							?>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<?
	}
}

?>
