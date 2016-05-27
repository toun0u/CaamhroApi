<script type="text/javascript" src="/common/js/upload/javascript/uploader.js"></script>
<h4>
	<img src="<?= module_wiki::getTemplateWebPath('/gfx/icone_article.png');?>"  /> <?= $_SESSION['cste']['REFERENCES']; ?>
</h4>
<div class="container_admin global_content_record todo_form">
	<div class="form_object_block">
	<form name="form_properties" id="form_properties" action="<?php echo $this->getLightAttribute('action_path'); ?>" method="POST">
		<input type="hidden" name="id_reference" id="id_reference" value="<?php echo (!isset($this->fields["id"]))? '0':$this->fields["id"];?>">
		<input type="hidden" name="id_oldposition" id="id_oldposition" value="<?php echo (!isset($this->fields["position"]))? '1':$this->fields["position"];?>">
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
		$id_docfileselected =dims_load_securvalue('id_docfileselected',dims_const::_DIMS_NUM_INPUT,true,true);


		if (isset($id_docfileselected) && $id_docfileselected>0 ) {
			$this->fields["id_doc_link"]=$id_docfileselected;
			$this->fields['typelink']=1;
		}

		if (isset($this->fields["id_doc_link"]) && $this->fields["id_doc_link"]>0) {
			$doc= new docfile();
			$doc->open($this->fields["id_doc_link"]);
			if (!isset($this->fields["label"]) || $this->fields["label"]=='') $this->fields["label"]=$doc->fields['name'];
		}
		?>

		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<div id="contentAddReference" style="clear:both;">
					<table>
						<tr>
							<td class="label_field w30">
								<label for="reference_labelle"><?php echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field" colspan="3">
								<input type="text" name="reference_label" tabindex="1" id="reference_label" rel="requis" value="<?php echo (!isset($this->fields["label"]))?'':$this->fields["label"];?>" />
							</td>
						</tr>
						<tr>
							<td class="label_field w30">
								<label for="reference_typelink"><?php echo $_SESSION['cste']['_DIMS_SELECT_TYPE']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field" colspan="3">
								<?php


								echo "<select onchange=\"javascript:changeDivTypeLink();\" id=\"reference_typelink\" name=\"reference_typelink\" tabindex=\"2\">";


								if ($this->fields['typelink']=="") $this->fields['typelink']=0;

								$elems[0]= ucfirst($_SESSION['cste']['_DIMS_LABEL_WEB_ADDRESS']);
								$elems[1]=ucfirst($_SESSION['cste']['DOCUMENT']);

								foreach ($elems as $indi => $elem) {
									if ($this->fields['typelink']==$indi) $selected="selected";
									else $selected="";

									echo "<option ".$selected." value=\"".$indi."\">".$elem."</option>";
								}

								for ($indi=1;$indi<=$maxi;$indi++) {

								}

								echo "</select>";
								?>
							</td>
						</tr>
						<tr>
							<td class="label_field w30">
								<label for="reference_position"><?php echo $_SESSION['cste']['_POSITION']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field" colspan="3">
							<?php

							echo "<select name=\"reference_position\" tabindex=\"3\">";

							$maxi=$this->getMaxPosition();

							if (!($this->fields["id"] > 0)) $maxi ++;

							if ($this->fields['position']=="" || $this->fields['position']==0) $this->fields['position']=$maxi;

							for ($indi=1;$indi<=$maxi;$indi++) {
								if ($this->fields['position']==$indi) $selected="selected";
								else $selected="";

								echo "<option ".$selected." value=\"".$indi."\">".$indi."</option>";
							}

							echo "</select>";
							?>
							</td>
						</tr>
						<?php

						$displayurlbloc='display:bloc;visibility:visible';
						$displaydocbloc='display:bloc;visibility:visible';
						$value_iddoc=0;

						if ($this->fields['typelink']==0) $displaydocbloc='display:none;visibility:hidden';
						else {
							$displayurlbloc='display:none;visibility:hidden';
							$value_iddoc=$this->fields["id_doc_link"];
						}

						?>
						<tr>
							<td class="label_field w30">
								<label for="reference_link"><?php echo $_SESSION['cste']['_DIMS_LABEL_URL']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field" colspan="3">
								<div id="switchUrl" style="<?php echo $displayurlbloc; ?>">
									<input type="text" name="reference_link" id="reference_link" value="<?php echo (!isset($this->fields["link"]))?'':$this->fields["link"];?>" />
								</div>
								<div id="switchDoc" style="<?php echo $displaydocbloc; ?>">
									<input type="hidden" id="reference_id_doc_link" name="reference_id_doc_link" value="<? echo $value_iddoc; ?>">
									<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
									<div id="descFileRef">
										<?

										if (isset($this->fields["id_doc_link"]) && $this->fields["id_doc_link"]>0) {

											echo $doc->fields['name']." <a href=\"javascript:void(0);\" onclick=\"javascript:initDocLink();\"><img src=\"./common/img/delete.png\"></a>";
										}
										?>
									</div>
									<input style="width:150px" type="button" onclick="javascript:wikiSelectDoc();" value="Choisir">
								</div>
							</td>
						</tr>
						<tr>
							<td class="label_field w30">
								<label for="reference_id_lang"><?php echo $_SESSION['cste']['_DIMS_LABEL_LANG']; ?></label>
							</td>
							<td class="value_field" colspan="3">
								<select name="reference_id_lang" id="reference_id_lang">
									<?
									global $article;
									foreach($article->getListArticleLangVersion() as $lang){
										?>
										<option <? echo ($lang->fields['id'] == $this->fields['id_lang'])?'selected=true':''; ?> value="<? echo $lang->fields['id']; ?>">
											<? echo $lang->fields['label']; ?>
										</option>
										<?
									}
									?>
								</select>
							</td>
						</tr>
					</table>
					<div class="sub_form">
						<div class="form_buttons">
							<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
							<div><input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></div>
							<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<?php echo module_wiki::getScriptEnv('')."&params_op=references&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; ?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</form>
	</div>
</div>
<?php
// on supprime ce qu'il peut y avoir en temporary
$sid = sha1(uniqid(""). MD5(microtime()));
$temp_dir = DIMS_TMP_PATH;
$session_dir = $temp_dir."/".$sid;

if (file_exists($session_dir)) dims_deletedir($session_dir);
dims_makedir($session_dir);

$upload_dir = _DIMS_PATHDATA."/uploads/".$sid."/";
if (!is_dir($upload_dir)) dims_makedir ($upload_dir);

$_SESSION['dims']['uploaded_sid']=$sid;

$upload_size_file = $session_dir."/upload_size";
$upload_finished_file = $session_dir."/upload_finished";

if (file_exists($upload_size_file)) unlink($upload_size_file);
if (file_exists($upload_finished_file)) unlink($upload_finished_file);
?>
<script type="text/javascript">
	var uploads = new Array();
	var upload_cell, file_name;
	var count=0;
	var checkCount = 0;
	var check_file_extentions = true;
	var sid = '<? echo $_SESSION['dims']['uploaded_sid'] ; ?>';
	var page_elements = ["toolbar","page_status_bar"];
	var img_path = "../common/img/";
	var path = "";
	var bg_color = false;
	var status;
	var debug = false;
	$(document).ready(function () {

		$("#reference_label").focus();

			var messages = new Array();
			$("#form_properties").dims_validForm({messages: {},
				displayMessages: true,
				refId: 'def',
				globalId: 'global_message'});
});

</script>
