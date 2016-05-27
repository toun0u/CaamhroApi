<?php
$template = $this->getLightAttribute('template');

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
<script src="/common/js/upload/javascript/uploader.js" type="text/javascript"></script>
<script type="text/javascript">
	var uploads = new Array();
	var upload_cell, file_name;
	var count=0;
	var checkCount = 0;
	var check_file_extentions = true;
	var sid = '<? echo $_SESSION['dims']['uploaded_sid'] ; ?>';
	var page_elements = ["toolbar","page_status_bar"];
	var img_path = "./common/img/";
	var path = "";
	var bg_color = false;
	var status;
	var debug = false;
	var param1=true;
	var param2=true;
</script>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" name="docfile_add" id="docfile_add" enctype="multipart/form-data">
		<input type="hidden" name="action" value="<? echo module_wce::_DYN_SLID_SAVE_ELEM; ?>" />
		<input type="hidden" name="id" value="<? echo $this->fields['id']; ?>" />
		<input type="hidden" name="obj_id_slideshow" value="<? echo $this->fields['id_slideshow']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
						</td>
						<td>
							<input type="text" name="obj_titre" value="<? echo $this->fields['titre']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
						</td>
						<td>
							<textarea name="fck_obj_descr_courte" id="obj_descr_courte"><?php echo $this->fields['descr_courte']; ?></textarea>
						</td>
					</tr>
					<?php
					/*
					if ($template != 'topCarousel' && $template != 'smallCarousel') {
						echo '<tr>
							<td class="label_field">
								<label for="slideshowelem_descr_longue"><strong>Description longue :</strong></label>
							</td>
						</tr>
						<tr>
							<td class="value_field">';
								dims_fckeditor('slideshowelem_descr_longue',$slideshow_elem->fields['descr_longue'],'590','250',true);
						echo  '</td>
						</tr>';
					} */
					?>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DOCS']; ?>
						</td>
						<!-- <td>
							<div id="ScrollBox" style="overflow:auto;float: left;">
								<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
								<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;height:10px;width:auto;" src=""></iframe>
							</div>
							<script type="text/javascript">
								createFileInputNotOpen('');
								<?php
								//if($template == 'topCarousel' || $template == 'smallCarousel' ){
									?>
									createFileInputNotOpen('');
									createFileInputNotOpen('');
									$('#frmUpload_0 input[type=file]').attr({'rev':'ext:mp4'}).before('<div style="float: right;margin-top: 15px;">*.mp4</div>').css("width","80%");
									$('#frmUpload_1 input[type=file]').attr({'rev':'ext:ogv'}).before('<div style="float: right;margin-top: 15px;">*.ogv</div>').css("width","80%");
									$('#frmUpload_2 input[type=file]').attr({'rev':'ext:webm'}).before('<div style="float: right;margin-top: 15px;">*.webm</div>').css("width","80%");
									<?php
								//}
								?>
							</script>
							<?
							//if(($file = $this->getPreview()) !== false){
								?>
								<div style="float: right;">
									<img style="height: 160px;" src="<? //echo $file; ?>" />
								</div>
								<?
							//}
							?>
						</td> -->
						<td>
							<?
							if(isset($this->fields['image']) && $this->fields['image'] > 0) {

								$doc = new docfile();
								$doc->open($this->fields['image']);

								echo '<img src="'.$doc->getThumbnail(200).'" alt="Miniature" />';
							}
							?> <br>
							<input type="file" name="document" value="" />
						</td>
					</tr>
					<?php
					if ($template != 'topCarousel' && $template != 'smallCarousel') {
						?>
						<tr>
							<td class="label">
								<? echo $_SESSION['cste']['_DIMS_LABEL_URL']; ?>
							</td>
							<td>
								<input type="text" value="<? echo $this->fields['lien']; ?>" name="obj_lien" />
							</td>
						</tr>
						<?
					}
					?>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>
						</td>
						<td>
							<input style="width:70px;" type="text" value="<? echo $this->fields['color']; ?>" name="obj_color" id="obj_color" />
							<a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('obj_color', event);">
								<img src="./common/img/colorpicker/colorpicker.png" align="top" border="0">
							</a>
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<!-- <input onclick="javascript:upload();" type="button" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" /> -->
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_DEF; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php
// récupération du template
$sql = "SELECT 	*
		FROM 	dims_workspace_template
		WHERE 	id_workspace = :id_workspace
		AND 	is_default = 1
		LIMIT 	1";
$db = dims::getInstance()->getDb();
$res = $db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
$template_name = "default";
if($r = $db->fetchrow($res))
	$template_name = $r['template'];
?>
<script type="text/javascript" src="/common/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var instance=CKEDITOR.replace('obj_descr_courte',
			{
				customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
				stylesSet:'default:/common/templates/frontoffice/<?= $template_name; ?>/ckstyles.js',
				contentsCss:'/common/templates/frontoffice/<?= $template_name; ?>/ckeditorarea.css'
			});
	});
</script>
