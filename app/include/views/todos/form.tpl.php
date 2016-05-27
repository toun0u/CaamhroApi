<script type="text/javascript" src="js/dims_validForm.js"></script>
<div class="global_content_record todo_form">
	<div class="zone_title_form">

    	<?php
    	$mode = $this->getLightAttribute('mode_todo');

    	if(isset($mode) && $mode=='answer'){
			?>
    		 <h4><?php echo $_SESSION['cste']['ANSWER_ACTION']; ?></h4>
    		<?php
    	}
    	else if(isset($mode) && $mode=='validation'){
			?>
    		 <h4 id="valid_<?=$this->getLightAttribute('todo_id_parent');?>"><?php echo $_SESSION['cste']['_DIMS_LABEL_VALIDATION']; ?></h4>
    		<?php
    	}
    	else{
	    	if($this->isNew()){
	    		?>
	    		 <h4><?php echo $_SESSION['cste']['NEW_INTERVENTION']; ?></h4>
	    		<?php
	    	}
	    	else{
	    		?>
	    		<h4>
	    			<?php echo $_SESSION['cste']['SHORT_EDITION'];?>
	    		</h4>
	    		<?php
	    	}
	    }
    	?>
	</div>

	<div class="form_object_block">
		<?php
			global $dims;
		?>
		<form name="form_todo" id="form_todo" action="<?php echo $this->getLightAttribute('action_path'); ?>" method="POST">
				<?php
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$glob_message = $this->getLightAttribute("global_error");
					if(empty($glob_message)){
						?>
						<div class="global_message error_message" style="display: none;"></div>
						<?php
					}
					else{
						if(is_array($glob_message)) {
							?>
							<ul class="global_message error_message">
								<?php
								foreach($glob_message as $error) {
									?>
									<li>
										<?php echo $glob_message;?>
									</li>
									<?php
								}
								?>
							</ul>
							<?php
						}
						else {
							?>
							<div class="global_message error_message"><?php echo $glob_message;?></div>
							<?php
						}
					}
				?>

				<div class="sub_bloc">
					<div class="sub_bloc_form">
						<?php
						foreach($this->getAllLightAttributes() as $attr => $val){
							?>
							<input type="hidden" name="<?php echo $attr; ?>" value="<?php echo $val;?>" />
							<?php
								$token->field($attr);
						}
						if(!$this->isNew()){
							?>
							<input type="hidden" name="id_globalobject" value="<?php echo $this->fields['id_globalobject'];?>" />
							<?php
								$token->field("id_globalobject", $this->fields['id_globalobject']);
						}
						?>
						<table>
							<tr>
								<td class="label_field label_top">
									<label for="todo_content"><?php echo $_SESSION['cste']['_DIMS_LABEL_MESSAGE']; ?></label><span class="required">*</span>
								</td>
								<td class="value_field" colspan="3">
									<textarea type="text" name="todo_content" id="todo_content" rel="requis"><?php echo (!isset($this->fields["content"]))?'':$this->fields["content"];?></textarea>
									<?
										$token->field("todo_content");
									?>
								</td>
							</tr>
							<tr><td></td><td colspan="3"><div class="mess_error" id="def_todo_content"></div></td></tr>
							<?php
							$users = $this->getLightAttribute('users');

							if (isset($users) ) {
							?>
								<tr>
									<td class="label_field label_top">
										<input type="checkbox" name="is_todo" id="is_todo" value="1" <?php if( !$this->isNew() && ! $this->isSimpleMessage() ) echo 'checked="checked"'; ?>/>
										<?
											$token->field("is_todo");
										?>
									</td>
									<td class="label_field label_left" colspan="3">
										<label for="is_todo"><?php echo $_SESSION['cste']['NEW_TODO']; ?></label>
										<select name="dests_id[]" id="dest_id" <?php if( $this->isNew() || $this->isSimpleMessage() ) echo 'disabled="disabled"'; ?> multiple="multiple">
											<?
												$token->field("dests_id");
											?>
											<?php
											foreach($users as $u){
												?>
												<option id="opt_<?php echo $u->getId(); ?>" value="<?php echo $u->getId(); ?>" <?php if($this->hasDestinataire($u->getId())) echo 'selected="selected"'; ?>><?php echo $u->fields['firstname'].' '.$u->fields['lastname'];?></option>
												<?php
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="label_field label_top">
										<input type="checkbox" name="is_all_validation_required" id="is_all_validation_required" value="1" <?php if( $this->isNew() ||  $this->fields['type'] != todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION ) echo 'disabled="disabled"';?> <?php if( !$this->isNew() && ! $this->fields['type'] == todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION ) echo 'checked="checked"'; ?>/>
										<?
											$token->field("is_all_validation_required");
										?>
									</td>
									<td class="label_field label_left" colspan="3">
										<label id="label_all_validation" <?php if( $this->isNew() ||  $this->fields['type'] != todo::TODO_TYPE_WITH_ALL_DEST_VALIDATION ) echo 'class="label_disabled"'; ?> for="is_all_validation_required"><?php echo $_SESSION['cste']['VALIDATION_OF_EVERY_ONE_REQUIRED']; ?></label>
								<?php
								}
								?>

						</table>
					</div>
				</div>

				<div class="sub_form">
					<div class="form_buttons">
						<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
						<div><input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></div>
						<?php
						$back_op = $this->getLightAttribute("back_path");
						if(isset($back_op)){
							?>
							<div>
							<?php
							echo " ".$_SESSION['cste']['_DIMS_OR']." ";
							$from_todo = $this->getLightAttribute('from');
							if(!isset($from_todo) || $from_todo != 'desktop'){
							?>
								<a href="<?php echo $back_op;?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
							<?php
							}
							else{
								?>
								<a style="cursor: pointer;" onclick="javascript:$('.ajax_form').remove();"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
								<?php
							}
						}
						?>
					</div>
				</div>
			<?
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
		</form>
	</div>
</div>

<script type="text/javascript">
	$("#form_todo").dims_validForm({messages: {	defaultError: 	'<? echo addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']); ?>',
													formatMail: 	'<? echo addslashes($_SESSION['cste']['WRONG_EMAIL_FORMAT']); ?>',
													globalMessage: 	'<? echo addslashes($_SESSION['cste']['PLEASE_VERIFY_FIELDS']); ?>',
													login: 			'<? echo addslashes($_SESSION['cste']['LOGIN_ALREADY_USED']); ?>',
													},
												    displayMessages: true,
												    refId: 'def',
												    globalId: 'global_message'});
	$('#is_todo').change(function(){
		if( $(this).is(':checked')){
			$('#dest_id').removeAttr('disabled');
			$('#is_all_validation_required').removeAttr('disabled');
			$('#label_all_validation').removeClass('label_disabled');
		}
		else{
			$('#dest_id').attr('disabled', 'disabled');
			$('#is_all_validation_required').attr('disabled', 'disabled');
			$('#label_all_validation').addClass('label_disabled');
		}
	});
</script>

