<?php
$data = $browser->getData();

$doc = new docfile();

if($data['key'] != 'GHOST'){
	$is_image = false;
?>
<div class="browser_leaf">
	<div class="pad_interne">
		<div class="entete">
			<div class="icon_extension">
				<?php

					if($data['type'] == 'folder'){
						?>
						<img src="<? echo _DESKTOP_TPL_LOCAL_PATH.'/concepts/gfx/common/open_record.png'; ?>"/>
						<?php
					}
					else{
						$doc->open(substr($data['key'],5));
						echo '<img src="'.$doc->getFileIcon(32).'"/>';
						switch(strtolower($data['extension'])){
							case 'png':
							case 'jpg':
							case 'jpeg':
							case 'tiff':
							case 'bmp':
							case 'gif':
								//echo '<img src="'._DESKTOP_TPL_LOCAL_PATH.'/finder/gfx/image48.png').'"/>';
								$is_image = true;
							break;
							case 'pdf':
								//echo '<img src="'._DESKTOP_TPL_LOCAL_PATH.'/finder/gfx/pdf48.png').'"/>';
							break;
							default:
								//echo '<img src="'._DESKTOP_TPL_LOCAL_PATH.'/finder/gfx/fichier48.png').'"/>';
							break;
						}
					}
				?>

			</div>
			<div class="title">
				<h3><?php echo $data['libelle']; ?></h3>
			</div>
		</div>
		<div class="button_actions">
			<?php
			if($data['type']=='file'){
				global $dims;
				$delete_action="javascript:dims_confirmlink('".dims_urlencode($dims->getScriptEnv()."?action=file_delete&docfile_id=".substr($data['key'],5))."','".$_SESSION['cste']['ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?']."');";

				$extract_action="javascript:dims_confirmlink('".dims_urlencode($dims->getScriptEnv()."?action=".dims_const_desktopv2::_DOCUMENTS_EXTRACT_FILE."&docfile_id=".substr($data['key'],5))."','".$_SESSION['cste']['ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?']."');";
				?>
				<div class="button">
					<a href="<? echo dims::getInstance()->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id={$data['md5id']}"; ?>">
						<img style="float:left;" src="<? echo _DESKTOP_TPL_PATH.'/gfx/common/download.png'; ?>" />
						<span style="float:left; line-height: 21px;text-decoration:none;"><? echo $_SESSION['cste']['_DIMS_DOWNLOAD']; ?></span>
					</a>
				</div>
				<div class="button">
					<?
					//$go = new dims_globalobject();
					//$go->open($_SESSION['smile']['admin']['concepts']['go_contact']);

					switch($_SESSION['desktopv2']['concepts']['sel_type']) {
						case dims_const::_SYSTEM_OBJECT_TIERS:
							$menu_op = "concept_tiers_op";
							break;
						case dims_const::_SYSTEM_OBJECT_CONTACT:
							$menu_op = "concept_contact_op";
							break;
						case dims_const::_SYSTEM_OBJECT_PROJECT:
							$menu_op = "project_op";
							break;
					}
					$previsu = dims::getInstance()->getScriptEnv()."?action=".dims_const_desktopv2::_CONCEPTS_DOCUMENTS_VIEW."&id_file=".substr($data['key'],5);
					/*if (isset($_SESSION['oeuvre']['op'])){
						switch($_SESSION['oeuvre']['op']){
							case dims_const_oeuvre::_OEUVRE_FICHE_OEUVRE:
								$previsu = dims::getInstance()->getScriptEnv().'?sub='.dims_const_oeuvre::_OEUVRE_FICHE_DOCUMENT_VISU.'&id_oeuvre='.dims_load_securvalue('id_oeuvre',dims_const::_DIMS_NUM_INPUT,true,true,true)."&id_doc=".substr($data['key'],5);
								break;
							case dims_const_oeuvre::_OEUVRE_FICHE_CONTACT:
								$previsu = dims::getInstance()->getScriptEnv().'?sub='.dims_const_oeuvre::_OEUVRE_FICHE_CT_DOC_VISU."&id_doc=".substr($data['key'],5);
								break;
						}
					}*/
					?>
					<a href="<? echo $previsu; ?>">
						<img style="float:left;" src="<? echo _DESKTOP_TPL_PATH.'/gfx/common/visu_picto.png'; ?>" />
						<span style="float:left; line-height: 21px;text-decoration:none;"><? echo $_SESSION['cste']['_PREVIEW']; ?></span>
					</a>
				</div>
				<div class="button">
					<a href="<?php echo $delete_action;?>">
						<img style="float:left;padding-top: 2px;" src="<? echo _DESKTOP_TPL_PATH.'/gfx/common/delete16.png'; ?>" />
						<span style="float:left; line-height: 21px;text-decoration:none;"><? echo $_SESSION['cste']['_DELETE']; ?></span>
					</a>
				</div>
				<?php
				$extension=strtolower($data['extension']);
				if ($extension=="zip" || $extension=="tgz" || $extension == "tar.gz") {
				?>
				<div class="button">
					<a href="<?php echo $extract_action;?>">
						<img style="float:left;padding-top: 2px;" src="<? echo _DESKTOP_TPL_PATH.'/gfx/common/delete16.png'; ?>" />
						<span style="float:left; line-height: 21px;text-decoration:none;"><? echo $_SESSION['cste']['_DOC_LABEL_UNCOMPRESS']; ?></span>
					</a>
				</div>

				<?php
				}
			}
			?>
		</div>
		<table class="data_infos">
			<tr>
			<td class="infos">
				<div><?php if(isset($data['auteur'])) ?><label><? echo $_SESSION['cste']['_AUTHOR']; ?> : </label><span class="value"><?php echo $data['auteur']; ?></span></div>
				<div><?php if(isset($data['version'])) ?><label><? echo $_SESSION['cste']['_DIMS_LABEL_VERSION']; ?> : </label><span class="value"><?php echo $data['version']; ?></span></div>
				<div><?php if(isset($data['creation'])) ?><label><? echo $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?> : </label><span class="value"><?php echo $data['creation']; ?></span></div>
				<div><?php if(isset($data['modification'])) ?><label><? echo $_SESSION['cste']['_DIMS_LABEL_MODIF_ON_FEM']; ?> : </label><span class="value"><?php echo $data['modification']; ?></span></div>
				<div><?php if(isset($data['taille'])) ?><label><?php echo ($data['type']=='folder')?'Nb. éléments : ':$_SESSION['cste']['_SIZE'].' : '; ?></label><span class="value"><?php echo $data['taille'];echo ($data['type']=='folder')?'':' ko';  ?></span></div>
			</td>
			<?php
			$colspan=1;
			$thumbnail = $doc->getThumbnail(80);
			if($thumbnail != '' && isset($data['file_path']) && strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') == ''){
				$colspan++;
				?>
			<td class="preview">
				<div>
					<img style="display:none;" src="<?php echo $thumbnail; ?>" />
				</div>
			</td>
			<script type="text/javascript">
				//pour corriger un souci de compatbilité FF, IE) il faut preloader les images
				var preload = [$('td.preview img').attr('src')];
				$.each(preload,function(e) {
					$(new Image()).load(function() {
								var img = $('td.preview img');
								var h = img.height();
								var w = img.width();

								if(img.width() > img.height() && img.width() > 70 ){
									w = 70;
									h = img.height() * 70 / img.width();
								}
								else if(img.width() < img.height() && img.height() > 70){
									h = 70;
									w = img.width() * 70 / img.height();
								}
								img.css('width', w+'px');
								img.css('height', h+'px');
								img.css('display','inline');
						}).attr('src', this);
				});
			</script>
			<?php
			}
			?>
			</tr>
			<?php
			if($data['type']=='file'){
			?>
			<tr>
				<td class="infos" colspan="<? echo $colspan;?>"><div><label> Contenu :</label></div>
					<div id="dims_sourcecontent">
						<?php
						$fileindex=$doc->getFileIndexPath();
						if (file_exists($fileindex)) {
							echo file_get_contents($fileindex);
						}
						else {
							if (isset($doc->fields['status']) && $doc->fields['status']!=4) {
								?>
								<script type="text/javascript">
									var timerdisplayresult;
									timerdisplayresult = setTimeout("refreshProgressStatus()", 2000);
								</script>
								<?php
							}
						}
						?>
					</div>

					<script type="text/javascript">
						function refreshProgressContent() {
							$.ajax({
								type: "GET",
								url: '/admin.php?action=file_process_getcontent&id_doc=<? echo $doc->fields['id'];?>&id_process=<? echo $doc->fields['id_process'];?>',
								async: false,
								dataType: "text",
								success: function(data){
									clearTimeout(timerdisplayresult);
									document.getElementById('dims_sourcecontent').innerHTML=data;
									//timerdisplayresult = setTimeout("refreshProgressContent()", 2000);
								}
							});
						}

						function refreshProgressStatus(){


							$.ajax({
								type: "GET",
								url: '/admin.php?action=file_process&id_doc=<? echo $doc->fields['id'];?>&id_process=<? echo $doc->fields['id_process'];?>',
								async: false,
								dataType: "text",
								success: function(data){
									var code = parseInt(data);

									if(code >-1 ) {
										if (code==4) {
											clearTimeout(timerdisplayresult);
											refreshProgressContent();

										}
										else {


											var text="";
											if (code<=0) text="<img src=\"./common/img/loading.gif\">&nbsp;Attente de traitement";
											if (code>0 && code <4) text="<img src=\"./common/img/loading.gif\">&nbsp;Traitement en cours";

											//$("div#dims_sourcecontent").value=text;
											document.getElementById('dims_sourcecontent').innerHTML=text;
											clearTimeout(timerdisplayresult);
											timerdisplayresult = setTimeout("refreshProgressStatus()", 2000);
										}
									}
								}
							});
						}
					</script>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<div class="space_footer"></div>
	</div>
</div>
<?php
}
?>
