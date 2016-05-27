<?

$params = array();
$sel = "SELECT		DISTINCT dg.id_record, da.comment, da.type
					FROM		dims_action da
					INNER JOIN	dims_action_matrix dam
					ON			da.id = dam.id_action
					INNER JOIN	dims_globalobject dg
					ON			dg.id = dam.id_globalobject
					WHERE		da.id_user = :iduser
					AND			da.comment != ''
					AND			da.type not in (".
												$this->db->getParamsFromArray(explode(',', dims_const::_ACTION_LINK), 'actionlink', $params)
												.",".
												$this->db->getParamsFromArray(explode(',', dims_const::_ACTION_UPDATE_LINK), 'actionupdatelink', $params)
												.")
					ORDER BY	da.timestp_modify DESC
					LIMIT		1";
$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
$rAction = $this->db->query($sel, $params);
$lastAction = array();
if ($action = $this->db->fetchrow($rAction))
	$lastAction = $action;

?>
<table class="cadre_zone_recent_connexions" cellspacing="10" cellpadding="0" style="width:100%;">
	<tr>
		<td>
			<div class="ligne_photo">
				<?
				if ($ct->getPhotoPath(40) != '' && file_exists($ct->getPhotoPath(40)))
					echo '<img src="'.$ct->getPhotoWebPath(40).'" />';
				else
					echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/human40.png" />';
				?>
			</div>
			<table cellspacing="0" cellpadding="0" style="float: left; margin-left:10px">
				<tr>
					<td>
						<? if ($this->fields['diff'] > 300){ ?><!-- deconnecté -->
						<div class="ligne_puce"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/sleep.png"></div>
						<? }else{ ?>
						<div class="ligne_puce"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png"></div>
						<? } ?>
						<div class="ligne_zone_texte">
							<div>
								<a href="Javascript: void(0);" onclick="javascript:document.location.href='/admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action=<? echo _BUSINESS_TAB_CONTACT_FORM."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$this->fields['id_contact']; ?>';" class="ligne_zone_contact">
									<? echo $this->fields['firstname']." ".$this->fields['lastname']; ?>
								</a>
							</div>
						</div>
						<div class="commentaire">
							<p>
								<?
								if (count($lastAction) > 0){
									echo $_SESSION['cste'][$lastAction['comment']];
									switch($lastAction['type']){
										case dims_const::_ACTION_CREATE_CONTACT:
										case dims_const::_ACTION_UPDATE_CONTACT:
											$ct = new contact();
											if ($ct->open($lastAction['id_record']))
												echo " : ".$ct->fields['firstname']."&nbsp;".$ct->fields['lastname'];
											break;
										case dims_const::_ACTION_CREATE_TIERS:
										case dims_const::_ACTION_UPDATE_TIERS:
											$tiers = new tiers();
											if($tiers->open($lastAction['id_record']))
												echo " : ".$tiers->fields['intitule'];
											break;
										case dims_const::_ACTION_CREATE_EVENT:
										case dims_const::_ACTION_MODIFY_EVENT:
											$event = new action();
											if($event->open($lastAction['id_record']))
												echo " : ".$event->fields['libelle'];
											break;
										case dims_const::_ACTION_DELETE_EVENT:
											break;
										case dims_const::_ACTION_CREATE_DOC:
										case dims_const::_ACTION_UPDATE_DOC:
											$doc = new docfile();
											if($doc->open($lastAction['id_record']))
												echo " : ".$doc->fields['name'];
											break;
									}
								}
								?>
							</p>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

