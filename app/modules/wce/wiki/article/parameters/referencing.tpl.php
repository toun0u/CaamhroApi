<div class="container_admin global_content_record todo_form">
	<div class="form_object_block">
		<?php
			global $dims;
		?>
		<form name="form_referencing" id="form_referencing" action="<?php echo $this->getLightAttribute('action_path'); ?>" method="POST">
				<?php
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
				<h4>
					<img src="<?= module_wiki::getTemplateWebPath('/gfx/icone_article.png');?>"  /> <?= $_SESSION['cste']['REFERENCING_PARAMETERS']; ?>
				</h4>
				<div class="sub_bloc">
					<div class="sub_bloc_form">
						<?php
						foreach($this->getAllLightAttributes() as $attr => $val){
							?>
							<input type="hidden" name="<?php echo $attr; ?>" value="<?php echo $val;?>" />
							<?php
						}
						if(!$this->isNew()){
							?>
							<input type="hidden" name="id_globalobject" value="<?php echo $this->fields['id_globalobject'];?>" />
							<input type="hidden" name="lang" value="<?php echo $this->fields['id_lang'];?>" />
							<?php
						}

						//récupération du premier des noms de domaines
						$domainlist=$this->getDomainList();
						$first_domain = (isset($domainlist[0]['domain'])) ? 'http://'.$domainlist[0]['domain'].'/ ' : '';
						?>
						<table>
							<tr>
								<td class="label_field w30">
									<label for="article_urlrewrite"><?php echo $_SESSION['cste']['_WCE_URLREWRITE']; ?></label>
								</td>
								<td class="value_field legend" colspan="3" >
									<span><?= $first_domain ?></span><input type="text" class="fixe200" name="article_urlrewrite" id="article_urlrewrite" value="<?php echo (!isset($this->fields["urlrewrite"]))?'':$this->fields["urlrewrite"];?>" /><span>&nbsp;.html</span>
								</td>
							</tr>
							<tr>
								<td class="label_field w30">
									<label for="article_urlrewriteold"><?php echo $_SESSION['cste']['_WCE_URLREWRITE_OLD']; ?></label>
								</td>
								<td class="value_field legend" colspan="3">
									<span><?= $first_domain ?></span><input type="text" class="fixe200" name="article_urlrewriteold" id="article_urlrewriteold" value="<?php echo (!isset($this->fields["urlrewriteold"]))?'':$this->fields["urlrewriteold"];?>" /><span>&nbsp;.html</span>
								</td>
							</tr>
							<tr>
								<td class="label_field w30 label_top">
									<label for="article_meta_description"><?php echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION_META']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<textarea name="article_meta_description" id="article_meta_description"><?php echo (!isset($this->fields["meta_description"]))?'':$this->fields["meta_description"];?></textarea>
								</td>
							</tr>
							<tr>
								<td class="label_field w30 label_top">
									<label for="article_meta_keywords"><?php echo $_SESSION['cste']['_WCE_KEYWORDS_META']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<textarea name="article_meta_keywords" id="article_meta_keywords"><?php echo (!isset($this->fields["meta_keywords"]))?'':$this->fields["meta_keywords"];?></textarea>
								</td>
							</tr>
							<tr>
								<td class="label_field w30 label_top">
									<label for="article_script_bottom"><?php echo $_SESSION['cste']['_WCE_SCRIPT_BOTTOM']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<textarea name="article_script_bottom" id="article_script_bottom"><?php echo (!isset($this->fields["script_bottom"]))?'':$this->fields["script_bottom"];?></textarea>
								</td>
							</tr>




						</table>
					</div>
				</div>

				<div class="sub_form">
					<div class="form_buttons">
						<div><input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></div>
						<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<?php echo module_wiki::getScriptEnv('');?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
					</div>
				</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		$("#form_properties").dims_validForm({messages: {	defaultError: 	'<? echo addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']); ?>',
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
			}
			else{
				$('#dest_id').attr('disabled', 'disabled');
			}
		});

        $("#article_title").focus();
    });
</script>
