<?php
/*
*	Basé sur un docfolder
*/

// on vide le dossier de dl de l'user
$tmp_path = DIMS_ROOT_PATH.'www/data/uploads/'.session_id();
if(file_exists($tmp_path)){
	dims_deletedir($tmp_path);
}

$formId = uniqid();

$lstUsed = array();

?>

<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-ui.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-process.js"></script>
<script type="text/javascript" src="./common/js/dims_autocomplete.js"></script><!-- contient la lib tmpl -->

<form method="POST" action="<?= $this->getLightAttribute('save_url'); ?>" id="<?= $formId; ?>" style="">
	<input type="hidden" name="id_folder" value="<?= $this->get('id'); ?>" />
	<input style="margin-top:10px;" id="fileupload" type="file" name="files" data-url="<?= dims::getInstance()->getScriptEnv(); ?>?dims_op=jquery_upload_file" multiple directory webkitdirectory mozdirectory>
	<div id="added_files">
		<table style="width:100%;" cellpadding="0px" cellspacing="0px"></table>
		<div class="label-drag-drop-file-area">
			<table style="width:100%;">
				<tr>
					<td rowspan="2"><img src="/common/modules/doc/img/drop_here.png" /></td>
					<td style="font-size: 22px;font-weight: bold;vertical-align:bottom;"><?= $_SESSION['cste']['_DRAG_DROP_FILE_IN_THIS_BOX']; ?></td>
				</tr>
				<tr>
					<td style="font-size:18px;vertical-align:top;"><?= $_SESSION['cste']['_YOU_CAN_SELECT_MULTIPLE_AT_ONCE']; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="actions" style="float: none;">
		<input type="submit" class="green submit" value="<?= $_SESSION['cste']['_SAVE_DOCUMENTS']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= $this->getLightAttribute('back_url'); ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		var folderOptions = '',
			tagOptions = '',
			nbFiles = 0,
			nbFiles2 = 0;
		$('#<?= $formId; ?>').delegate('.valid-info-dl','click',function(){
			var el = $(this).parents('tr:first'),
				el2 = el.prev('tr:first').removeClass('open-info')
				idFold = $('select:first',el).val(),
				tags = $('select:last',el).val(),
				desc = jQuery.trim($('textarea',el).val());

			$('input[name^="tags"]',el2).val(tags);
			$('input[name^="doc_description"]',el2).val(desc);

			$('div.description-img',el2).remove();
			$('span.folder-img',el2).remove();
			$('div.tags-img',el2).remove();

			if(desc != ''){
				$('div.upload-progress',el2).before('<div class="description-img">'+desc+'</div>');
			}

			if(tags != null && tags.length){
				var lst = '';
				$(tagOptions).find('option').each(function(){
					if(jQuery.inArray($(this).val(),tags) >= 0){
						lst = lst+'<span class="tag" dims-data-value="'+$(this).val()+'">'+jQuery.trim($(this).text())+'</span>';
					}
				});
				if(lst != ''){
					$('div.upload-progress',el2).before('<div style="margin-top:5px;margin-bottom:5px;" class="tags-img">'+lst+'<p style="clear:both;"></p></div>');
				}
			}
			el.remove();
		}).delegate('.undo-info-dl','click',function(){
			var el = $(this).parents('tr:first'),
				el2 = el.prev('tr:first').removeClass('open-info');
			el.remove();
		});
	    $('#<?= $formId; ?> #fileupload').fileupload({
			dataType: 'json',
			formAcceptCharset: 'utf-8',
			dropZone: $('#<?= $formId; ?> div#added_files'),
			autoUpload: true,
			previewThumbnail: true,
			sequentialUploads:true,
			previewMaxWidth: 40,
			filesContainer: $('#<?= $formId; ?> div#added_files table:first'),
			uploadTemplateId: null,
			uploadTemplate: function (o) {
				var rows = $();
				$.each(o.files, function (index, file) {

					if(file.error != undefined){
						var row = $('<tr class="template-upload error-dl">\
										<td class="upload-thumbnail" colspan="2">\
											<?= $_SESSION['cste']['_DOC_LABEL_ERROR_MAXFILESIZE']; ?>\
										</td>\
									</tr>');
					}else{
						var row = $('<tr class="template-upload process-dl">\
										<td style="width:60px;text-align:center;" class="upload-thumbnail">\
											<img style="max-width:40px;" src="/common/modules/doc/img/doc40.png" />\
											<input type="hidden" name="file_name[]" value="" />\
											<input type="hidden" name="directory[]" value="<?= $this->get('id'); ?>" />\
											<input type="hidden" name="tags['+nbFiles+']" value="" />\
											<input type="hidden" name="doc_description[]" value="" />\
										</td>\
										<td>\
											<span class="name-file"></span><br />\
											<?= $_SESSION['cste']['_WEIGHT']; ?> : <span class="upload-size"></span> ko<br />\
											<div class="upload-progress"><div class="bar" style="width: 0%;"></div></div>\
										</td>\
									</tr>');
						row.find('.name-file').text(file.name);
						row.find('.upload-thumbnail input[name^="file_name"]').val(file.name);
						row.find('.upload-size').html(Math.round((file.size/1024)*100)/100);
						if(file.thumbnailUrl != undefined){
							row.find('.upload-thumbnail img').attr('src',file.thumbnailUrl);
						}
						row.find('.upload-actions .delete').attr({'data-type': file.deleteType,'data-url': file.deleteUrl});
						/*row.find('.tags').val('');
						row.find('select').chosen();*/
						nbFiles++;
						$('#<?= $formId; ?> div.label-drag-drop-file-area').css({"margin-top": "20px"});
					}
					rows = rows.add(row);
				});
				return rows;
			},
			progress: function(e, data){
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('#<?= $formId; ?> table:first tr.process-dl:first div.upload-progress .bar').width(progress+"%");
			},
			send: function(e, data){
				$('#<?= $formId; ?> table:first tr.process-dl:first div.upload-progress .bar').width("0%");
		    },
		    done: function(e, data){
		    	var progress = parseInt(data.loaded / data.total * 100, 10);
		    	var divFile = $('#<?= $formId; ?> table:first tr.process-dl:first');
		    	var file = data.result.files[0];
		    	$('div.upload-progress .bar',divFile).width(progress+"%");
		    	if(file.thumbnailUrl != undefined){
		    		$('.upload-thumbnail img',divFile).attr('src',file.thumbnailUrl);
		    	}
		    	$('td:last',divFile).append('<a href="javascript:void(0);" class="edit"><img src="/common/modules/doc/img/info2.png" /></a>\
				<a href="javascript:void(0);" class="delete"><img src="/common/modules/doc/img/remove_black.png" /></a>');
		    	$('.delete',divFile).attr({'data-type': file.deleteType,'data-url': file.deleteUrl});
		    	$(divFile).removeClass('process-dl');
				nbFiles2++;
			},
	    });
		$('#<?= $formId; ?> table:first').delegate('a.delete','click',function(){
			var el = $(this).parents('tr:first'),
				el2 = el.next('tr:first');
			if(el.hasClass('open-info')){
				el2.remove();
			}
			el.remove();
			$('#<?= $formId; ?> table:first tr.template-upload').each(function(index,elem){
				$('input[name^="tags"]',elem).attr('name',"tags["+index+"]");
			});
			nbFiles2--;
			if(nbFiles2 == 0)
				$('#<?= $formId; ?> div.label-drag-drop-file-area').css({"margin-top": "0px"});
		}).delegate('a.edit','click',function(){
			var el = $(this).parents('tr:first');
			if(!el.hasClass('open-info')){
				var elem = $('<tr class="add_info"><td colspan="2" style="padding-top:8px;">\
								<div>\
									<select multiple="multiple" style="width:25%;" class="tags" data-placeholder="<?= $_SESSION['cste']['_CHOOSE_ONE_OR_MORE_TAGS']; ?>">\
										'+tagOptions+'\
									</select>\
									<a href="javascript:void(0);" class="add-tag"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" /></a>\
								</div>\
								<div>\
									<textarea placeholder="<?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>" style="width:24.6%;height:80px;resize: none;"></textarea>\
								</div>\
								<div class="actions" style="float:none;">\
									<input type="button" class="submit valid-info-dl" value="<?= $_SESSION['cste']['_DIMS_VALID']; ?>" />\
									<?= $_SESSION['cste']['_DIMS_OR']; ?>\
									<a href="javascript:void(0);" class="undo-info-dl">\
										<?= $_SESSION['cste']['_DIMS_CLOSE']; ?>\
									</a>\
								</div>\
							</td></tr>');
				//elem.find('select').chosen({width:"25%"});
				el.addClass('open-info').after(elem);
				var el2 = el.next('tr:first');
				$('select:first',el2).val($('input[name^="directory"]',el).val());
				$('select:last',el2).val($('input[name^="tags"]',el).val().split(','));
				$('textarea',el2).val($('input[name^="doc_description"]',el).val());
				$('select:first',el2).chosen({width:"25%",allow_single_deselect: true});
				$('select:last',el2).chosen({width:"25%"});
			}
		});
	});
</script>
