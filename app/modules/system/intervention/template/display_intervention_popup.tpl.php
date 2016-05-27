<?php
$id_popup = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, true);
$labelCible = '';

if ($this->fields['id'] > 0 && $this->fields['id'] != '')
	$id_globalobject = $this->fields['id_globalobject_ref'];
else
	$id_globalobject = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true, true,true);
$globalObject = new dims_globalobject();
$globalObject->open($id_globalobject);

switch($globalObject->fields['id_object']) {
	case dims_const::_SYSTEM_OBJECT_CONTACT:
		$contact = new contact();
		$contact->open($globalObject->fields['id_record']);

		$labelCible = $contact->getFirstname().' '.$contact->getLastname();
		break;
	case dims_const::_SYSTEM_OBJECT_TIERS:
		$tiers = new tiers();
		$tiers->open($globalObject->fields['id_record']);

		$labelCible = $tiers->getIntitule();
		break;
}

?>
<div id="intervention_add">
	<div class="actions">
		<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
			<img src="img/close.png" />
		</a>
	</div>
	<h2>
		<?php echo $_SESSION['cste']['_DIMS_TITLE_ADD_INTERVENTION']; ?>
	</h2>
	<div style="width:100%;">
		<form method="post" action="" enctype="multipart/form-data" name="save_intervention">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("dims_op",		"intervention");
				$token->field("intervention_op",dims_const_interv::_OP_SAVE_INTERVENTION);
				$token->field("interv_id_globalobject_ref",$globalObject->fields['id']);
				$token->field("id_int",			$this->fields['id']);
				$token->field("interv_id_case");
				$token->field("interv_id_type_intervention");
				$token->field("interv_inout");
				$token->field("interv_comment");
				$token->field("intervention_file");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="dims_op" value="intervention" />
			<input type="hidden" name="intervention_op" value="<?php echo dims_const_interv::_OP_SAVE_INTERVENTION; ?>" />
			<input type="hidden" name="interv_id_globalobject_ref" value="<?php echo $globalObject->fields['id']; ?>" />
			<input type="hidden" name="id_int" value="<? echo $this->fields['id']; ?>" />
			<?php
			if(!dims_isEmpty($globalObject->searchLink(dims_const::_SYSTEM_OBJECT_CASE))) {
				?>
				<div class="elem">
					<span class="label">
						<?php echo $_SESSION['cste']['_DIMS_DOSSIERS']; ?> :
					</span>
					<span class="value">
						<select name="interv_id_case">
							<option value="0" selected="selected">
								-- --
							</option>
							<?php
							foreach($globalObject->searchLink(dims_const::_SYSTEM_OBJECT_CASE) as $go_case) {
								$case = new dims_case();
								$case->openWithGB($go_case);
								if ($case->fields['id'] > 0){

								$sel = ($case->getId() == $idCaseSelected) ? 'selected="selected"' : '';
								?>
								<option value="<?php echo $case->getId(); ?>" <?php echo $sel; ?>>
									<?php  echo $case->getLabel(); ?>
								</option>
								<?php
								}
							}
							?>
						</select>
					</span>
				</div>
				<?php
			}
			?>
			<div class="elem">
				<span class="label">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_MODE_COMMUNICATION']; ?> :
				</span>
				<span class="value">
					<select name="interv_id_type_intervention">
						<?
						$lstInt = dims_intervention_type::getListType();
						foreach($lstInt as $int){
							?>
							<option value="<?php echo $int->fields['id']; ?>" <?php echo ($this->fields['id_type_intervention'] == $int->fields['id']) ? 'selected="selected"' : ''; ?>>
								<?php echo $int->getText(); ?>
							</option>
							<?
						}
						?>
					</select>
				</span>
			</div>
			<div class="elem">
				<span class="label">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_PERSONNE_CONTACTED']; ?> :
				</span>
				<span class="value">
					<input type="text" readonly value="<?php echo $labelCible; ?>" />
				</span>
			</div>
			<div class="elem">
				<span class="label">
					<?php echo $_SESSION['cste']['_TYPE']; ?> :
				</span>
				<span class="value">
					<select name="interv_inout">
						<option value="<? echo dims_intervention::_INTERVENTION_IN; ?>" <? if($this->fields['inout'] == dims_intervention::_INTERVENTION_IN) echo "selected=true"; ?>><? echo $_SESSION['cste']['_TYPE_IN']; ?></option>
						<option value="<? echo dims_intervention::_INTERVENTION_OUT; ?>" <? if($this->fields['inout'] == dims_intervention::_INTERVENTION_OUT) echo "selected=true"; ?>><? echo $_SESSION['cste']['_TYPE_OUT']; ?></option>
					</select>
				</span>
			</div>
			<div class="elem">
				<span class="label">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_FICHIER_ATTACHED']; ?> :
				</span>
				<span class="value">
					<input type="file" name="intervention_file" />
				</span>
			</div>
			<div class="elem">
				<span class="label" style="vertical-align:top;">
					<?php echo $_SESSION['cste']['_DIMS_COMMENTS']; ?> :
				</span>
				<span class="value">
					<textarea style="height: 140px;width:500px;" name="interv_comment"><? echo $this->fields['comment']; ?></textarea>
				</span>
			</div>
			<div>
				<?
					echo dims_create_button($_SESSION['cste']['_DIMS_CLOSE'],'close','Javascript:dims_closeOverlayedPopup('.$id_popup.');','','float:right;margin-right:20px;');
					echo dims_create_button($_SESSION['cste']['_DIMS_SAVE'],'save','javascript:document.save_intervention.submit();','','float:right;');
				?>
			</div>
		</form>
	</div>
</div>