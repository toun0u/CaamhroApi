<script src="/common/js/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="<?= module_wiki::getTemplateWebPath('/jquery.ui.autocomplete.custom.css');?>" rel="stylesheet" type="text/css"  />
<div class="container_admin global_content_record todo_form">
	<div class="form_object_block">
		<?php
		$dims = dims::getInstance();
		?>
		<form name="form_properties" id="form_properties" action="<?php echo $this->getLightAttribute('action_path'); ?>" method="POST">
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
					<img src="<?= module_wiki::getTemplateWebPath('/gfx/icone_article.png');?>"  /> <?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
				</h4>
				<div class="sub_bloc">
					<div class="sub_bloc_form">
						<?php
						foreach($this->getAllLightAttributes() as $attr => $val){
                            if(!is_object($val) && !is_array($val)){
                                ?>
                                <input type="hidden" name="<?php echo $attr; ?>" value="<?php echo $val;?>" />
                                <?php
                            }
						}
						if(!$this->isNew()){
							?>
							<input type="hidden" name="id_globalobject" value="<?php echo $this->fields['id_globalobject'];?>" />
							<input type="hidden" name="lang" value="<?php echo $this->fields['id_lang'];?>" />
							<?php
						}
						?>
						<table>
							<tr>
								<td class="label_field w30">
									<label for="article_title"><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label><span class="required">*</span>
								</td>
								<td class="value_field" colspan="3">
									<input type="text" name="article_title" id="article_title" rel="requis" value="<?php echo (!isset($this->fields["title"]))?'':$this->fields["title"];?>" />
								</td>
							</tr>
							<tr><td></td><td colspan="3"><div class="mess_error" id="def_article_title"></div></td></tr>

							<tr>
								<td class="label_field w30">
									<label for="article_title"><?php echo $_SESSION['cste']['CONTENT_MODEL']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<select name="article_model" id="article_model" class="select" tabindex="2">
										<option <? echo ($this->fields['model'] == "" ) ? 'selected' : ''; ?> value=""><? echo "aucun"; ?></option>
										<?
										$wce_models = wce_getmodels();
										foreach($wce_models["pages_publiques"] as $key => $model) {
											?>
											<option <? echo ($this->fields['model'] == $model ) ? 'selected' : ''; ?> value="<? echo $model; ?>"><? echo $model; ?></option>
											<?
										}
										if(is_array($wce_models["workspace"])) {
											foreach($wce_models["workspace"] as $key => $model) {
												?>
												<option <? echo ($this->fields['model'] == $model) ? 'selected' : ''; ?> value="<? echo $model; ?>"><? echo $model; ?></option>
												<?
											}
										}
										?>
									</select>
								</td>
							</tr>

							<tr>
								<td class="label_field w30 label_top">
									<label for="article_description"><?php echo $_SESSION['cste']['DESCRIPTION_COURTE']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<textarea name="article_description" id="article_description"><?php echo (!isset($this->fields["description"]))?'':$this->fields["description"];?></textarea>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<h4>
					<img src="<?= module_wiki::getTemplateWebPath('/gfx/calendar20.png');?>"  /> <?= $_SESSION['cste']['_WCE_ARTICLE_PUBLISH']; ?>
				</h4>
				<div class="sub_bloc">
					<div class="sub_bloc_form">
						<table>
							<?php
							$article_timestp_published = $article_timestp_unpublished = "";
							if(!$this->isNew()){
								$ldate = ($this->fields['timestp_published']) ? dims_timestamp2local($this->fields['timestp_published']) : array('date' => '');
								$article_timestp_published = $ldate['date'];

								$ldate = ($this->fields['timestp_unpublished']) ? dims_timestamp2local($this->fields['timestp_unpublished']) : array('date' => '');
								$article_timestp_unpublished = $ldate['date'];
							}
							?>
							<tr>
								<td class="label_field w30">
									<label for="article_timestp_published">
										<? echo $_SESSION['cste']['_POSTED_ON']; ?>
									</label>
								</td>
								<td class="value_field">
									<input class="datepicker fixe100" type="text" name="article_timestp_published" id="article_timestp_published" value="<? echo $article_timestp_published; ?>" />
								</td>
							</tr>
							<tr>
								<td class="label_field w30">
									<label for="article_timestp_unpublished">
										<? echo $_SESSION['cste']['_TAKEN_OFFLINE_THE']; ?>
									</label>
								</td>
								<td class="value_field">
									<input class="datepicker fixe100" type="text" name="article_timestp_unpublished" id="article_timestp_unpublished" value="<? echo $article_timestp_unpublished; ?>" />
								</td>
							</tr>
						</table>
					</div>
				</div>

				<h4>
					<img src="<?= module_wiki::getTemplateWebPath('/gfx/tag20.png');?>"  /> <?= $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?>
				</h4>
				<div class="sub_bloc">
					<div class="sub_bloc_form">
						<table>
							<tr>
								<td class="label_field w30">
									<label for="tags"><?php echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></label><span class="required">*</span>
								</td>
								<td class="value_field" colspan="3">
									<ul id="tags">
									</ul>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<h4>
					<img src="<?= module_wiki::getTemplateWebPath('/gfx/icone_categorie.png');?>"  /> <?= $_SESSION['cste']['CATEGORY_OF_THE_ARTICLE']; ?>
				</h4>
				<div class="sub_bloc">
					<div class="sub_bloc_form">

						<?
						$lst = $this->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
						?>
						<div>
							<?
							$root = module_wiki::getCategRoot();
							$load = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,true);
							if (count($lst) > 0){
								$par = new category();
								$par->openWithGB(current($lst));
								$root->setLightAttribute('parent',$par);
							}else
								$root->setLightAttribute('parent',null);
							$root->display(module_wiki::getTemplatePath('/categories/ajax_categ_browser.tpl.php'));
							?>
						</div>
					</div>
				</div>

				<div class="sub_form">
					<div class="form_buttons">
						<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
						<div><input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></div>
						<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<?php echo module_wiki::getScriptEnv('');?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
					</div>
				</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		var messages = new Array();
		$("#form_properties").dims_validForm({messages: {},
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
        $('.datepicker').datepicker({	dateFormat: 'dd/mm/yy',
										changeMonth: true,
										changeYear: true,
										buttonImage: '<?php echo module_wiki::getTemplateWebPath('/gfx/calendar.png'); ?>',
										showOn: 'both',
										buttonImageOnly: true,
										buttonText: '<? echo $_SESSION['cste']['_OEUVRE_SELECT_DATE']; ?>'});
    });

    //récupération des tags de l'appli
  	$.ajax({
			type: "POST",
			url: "admin.php",
			data: {
				'dims_op' : 'wiki',
				'op_wiki' : 'get_tags'
			},
			dataType: "json",
			async: false,
			success: function(data){
				$("#tags").tagit({
					availableTags: data
				});

			},
			error: function(data){
			}
		});

  	<?php
		$mytags = $this->getMyTags();
		foreach($mytags as $tag){
		?>
			$('#tags').prepend('<li class="tagit-choice"><?= str_replace("'","\'",$tag->fields["tag"]); ?><a class="close">x</a><input type="hidden" name="item[tags][]" value="<?= str_replace("'","\'",$tag->fields["tag"]);?>" style="display: none;"></li>');
		<?php
		}
  	?>

</script>
