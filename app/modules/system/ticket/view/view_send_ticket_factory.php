<?php

/**
 * Description of view_send_ticket_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class view_send_ticket_factory {
    public static function buildWriteTicket($id_dest){
        global $_DIMS;
        self::buildBeginningOfPopup($_SESSION['cste']['_DIMS_LABEL_TICKET_WRITING_TICKET']);

        self::buildHead($id_dest);

        self::buildBody();

        self::buildActions();

        self::buildEndingOfPopup();
    }

    public static function buildBeginningOfPopup($title){
		global $dims;
        ?>
        <div id="popup_user">
            <div class="actions">
                <h2 style="margin-left:20px;float:left;margin-top:7px;"><? echo $title ; ?></h2>
                <img style="float:right; margin-right: 2px; margin-top:2px;cursor:pointer;" onclick="javascript:dims_hidepopup();" src="./common/modules/assurance/img/icon_close.gif" />
            </div>
            <div style="float:right;">
                <form id="docfile_add" name="formCreateTicket" action="<? echo $dims->getScriptEnv().'?dims_op=ticket_manager';?>" method="POST" enctype="multipart/form-data">
                	<?
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("ticket_op",_OP_TICKET_SEND);
						$token->field("draft_save", "0");
						$token->field("fck_label_corps_message");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
                	?>
                    <input type="hidden" name="ticket_op" value="<? echo _OP_TICKET_SEND; ?>"/>
                    <input type="hidden" name="draft_save" value="0"/>
        <?
    }

    public static function buildEndingOfPopup(){
            ?>
                </form>
            </div>
        </div><?
    }

	public static function buildActions(){
		global $_DIMS;
		?>
		<div class="actionsTicket2">
			<span class="suivi">
				<ul>
					<li style="cursor: pointer;"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_ACCUSE_RECEPTION']?></li>
                    <li style="cursor: pointer;"><?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_FOLLOW_TICKET']; ?></li>
				</ul>
			</span>
			<span class="actions">
				<span>
					<input type="button" class="flatbutton" style="width:200px;" name="addfile" onclick="javascript:createFileInput();" value="<? echo $_DIMS['cste']['_DOC_LABEL_ADD_OTHER_FILE']; ?>">
					<div id="ScrollBox" style="overflow:auto;">
						<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
						<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;height:10px;" src=""></iframe>
					</div>
				</span>
			</span>
			<span class="send">
				<input type="reset" onclick="javascript:dims_hidepopup();" value="<?php echo $_SESSION['cste']['_DIMS_LABEL_CANCEL'];?>" />
				<input type="button" onclick="javascript:draftSave();upload();" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
				<input id="btn_upload" type="button" onclick="javascript:upload();" value="<?php echo $_SESSION['cste']['_DIMS_SEND']; ?>" />
			</span>
                     <script type="text/javascript">
                    window['draftSave'] = function draftSave(){
                        $("input:hidden[name='draft_save']").val(1);
                    }

                    </script>
		</div>
		<?
	}

	public static function buildBody(){
		global $_DIMS;
		?>
		<div class="bodyTicket">
			<span id="subject"><? echo $_DIMS['cste']['_SUBJECT']; ?> : <input type="text" name="label_objet" /></span>
			<span id="body">
				<?
				dims_fckeditor('label_corps_message','',460,400,true);
				?>
			</span>
		</div>
		<?
	}

	public static function buildHead($id_dest){
		global $_DIMS;
		$db = dims::getInstance()->getDb();
		?>
		<div class="headTicket">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:60px;">
						<? echo $_DIMS['cste']['_DIMS_LABEL_TICKET_EMETTEUR']; ?> :
					</td>
					<td style="text-align:left;">
						<?
						$sender = new user();
						$sender->open($_SESSION['dims']['userid']);
						echo $sender->fields['lastname'].' '.$sender->fields['firstname']
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left;">
                        <?php echo $_SESSION['cste']['_DIMS_LABEL_TICKET_DESTINATAIRES']." : "; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:left;">
						<?
						// sélection des utilisateurs
						$select ="	SELECT	id, firstname, lastname
									FROM	dims_user";
						$firstUser = '';
						$params = array();
						if ($id_dest > 0){
							$select .= " WHERE id != :iddest ";
							$params[':iddest'] = $id_dest;
							$user = new user();
							$user->open($id_dest);
							$firstUser = '{ id : '.$user->fields['id'].', label : "'.$user->fields['lastname'].' '.$user->fields['firstname'].'", type : '.ticket::TYPE_SENDER_USER.'}';
						}
						$res = $db->query($select, $params);
						$lstUser = array();
						while($r = $db->fetchrow($res))
							$lstUser[] = '{ id : '.$r['id'].', label : "'.$r['lastname'].' '.$r['firstname'].'", type : '.ticket::TYPE_SENDER_USER.'}';

						// TODO : gérer la sélection des groupes
						$select ="	SELECT	id, label
									FROM	dims_group
									WHERE	id_workspace IN (0, :moduleid )";
						$res = $db->query($select, array(
							':moduleid' => dims_viewworkspaces($_SESSION['dims']['moduleid'])
						));
						while($r = $db->fetchrow($res))
							$lstUser[] = '{ id : '.$r['id'].', label : "'.$r['label'].' (groupe)", type : '.ticket::TYPE_SENDER_GROUP.'}';

						?>
						<ul id="destTicket">
							<li> Pour : <input type="text" id="destSearch"/><input type="hidden" id="destLst" name="liste_destinataire_for" value="" /></li>
							<li> CC : <input type="text" id="ccSearch"/><input type="hidden" id="ccLst" name="liste_destinataire_cc" value="" /></div></li>
							<li> CCi : <input type="text" id="cciSearch"/><input type="hidden" id="cciLst" name="liste_destinataire_cci" value="" /></div></li>
						</ul>
						<script type="text/javascript">
							var availableUser = [ <? echo implode(', ',$lstUser); ?> ];
							var firstUSerDest = [ <? echo $firstUser; ?> ];

							$(function(){
								autocompletion('destSearch','destLst',availableUser,firstUSerDest);
								autocompletion('ccSearch','ccLst',availableUser);
								autocompletion('cciSearch','cciLst',availableUser);
							});
						</script>
					</td>
				</tr>
			</table>
		</div>
		<?
	}
}

?>
