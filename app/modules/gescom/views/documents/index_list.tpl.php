<?php
$view = view::getInstance();
$parents = $view->get('parents');
$current = $view->get('current');
$folders = $view->get('folders');
$documents = $view->get('documents');
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');

?>
<div class="docs-actions">
	<?php
	if(true){

		?>
		<a class="mr10" href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'add_folder', 'foldid' => $current->get('id')));?>"><?= image_tag('add_folder64_2.png', array('title' => dims_constant::getVal('_ADD_FOLDER'))); ?></a>
		<?php
		if($current->get('id') != $objcourant->get('id_folder')){
			?>
			<a class="mr10" href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'edit_folder', 'foldid' => $current->get('id')));?>"><?= image_tag('edit_folder64_2.png', array('title' => dims_constant::getVal('EDIT_FOLDER'))); ?></a>
			<a class="mr10" onclick="javascript:dims_confirmlink('<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'delete_folder', 'foldid' => $current->get('id'))); ?>', '<?= addslashes(dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'));?>');" href="javascript:void(0);"><?= image_tag('remove_folder64_2.png', array('title' => dims_constant::getVal('REMOVE_CURRENT_FOLDER'))); ?></a>
			<?php
		}

		$form = new Dims\form(
			array(
				'name'		=> 'upload_form',
				'action' 	=> get_path('show', 'show', array('id' => $objcourant->get('id'),'cc' => 'documents', 'aa' => 'save_files')),
				'enctype'	=> true
			));

		echo $form->get_header();
		echo $form->hidden_field(array('name' => 'id', 'value' => $objcourant->get('id')));
		echo $form->hidden_field(array('name' => 'foldid', 'value' => $current->get('id')));
		?>
		<a id="launcher" class="ml10" href="javascript:void(0);"><?= image_tag('add_file64_2.png', array('title' => dims_constant::getVal('ADD_DOCUMENT'))); ?></a>
		<div class="hidden" id="lst_files">
			<?= $form->file_field(array(
				'name'						=> 'files',
				'additionnal_attributes'	=> 'data-url="'.dims::getInstance()->getScriptEnv().'?dims_op=jquery_upload_file"'
				));
			?>
		</div>
		</form>
		<?php
		//echo $form->close_form(); --> cyril, souci avec le token validator, l'appel ajax doit consommer le token
	}
	?>
</div>
<div id="progress">
	<div class="bar" style="width: 0%;"></div>
</div>
<?php

if($current->get('id') == $objcourant->get('id_folder')){
	?>
	<h2><?= dims_constant::getVal('CONVENTION_DOCUMENTS'); ?></h2>
	<?php
}
else{
	?>
	<h2><?php
	if ($current->get('id') == $objcourant->get('id'))
		echo "e".dims_constant::getVal('_DOC_ROOT');
	else
		echo $current->get('name') ;
	//$current->get('name');
	?></h2>
	<?php
}

//Gestion du fil d'ariane
if(!empty($parents)){

	?>
	<div class="line mb20">
		<?php
		$first = true;
		foreach($parents as $parent){
			if(!$first) echo '&nbsp;>&nbsp;';
			if($first) $first = false;
			?>
			<a class="ariane" href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'index', 'folder' => $parent->get('id'))); ?>"><?= ($parent->get('id') == $objcourant->get('id_folder') ) ? dims_constant::getVal('_DOC_ROOT') : $parent->get('name') ; ?></a>
			<?php
		}
		?>
		&nbsp;>&nbsp;<a class="ariane current" href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'index', 'folder' => $current->get('id'))); ?>"><?= ($current->get('id') == $objcourant->get('id_folder') ) ? dims_constant::getVal('_DOC_ROOT') : $current->get('name') ; ?></a>
	</div>
	<?php
}
else {
	?>
	<div class="line mb20">
		<a class="ariane current" href="<?= get_path('show', 'show', array('id' => $objcourant->get('id'),'folder' => $foldercourant->get('id'), 'cc' => 'documents', 'aa' => 'index', 'folder' => $current->get('id'))); ?>"><?= ($current->get('id') == $objcourant->get('id_folder') ) ? dims_constant::getVal('_DOC_ROOT') : $current->get('name') ; ?></a>
	</div>
	<?php
}
if(!empty($documents) || !empty($folders)){
	?>
	<div id="list-of-documents">
		<?php
		//on commence par les dossiers toujours en premier
		if( ! empty($folders) ){
			foreach($folders as $fold){
				$view->partial($view->getTemplatePath('documents/_folder.tpl.php'), $fold);
			}
		}
		if( ! empty($documents) ){
			foreach($documents as $file){
				$view->partial($view->getTemplatePath('documents/_file.tpl.php'), $file);
			}
		}
		?>
	</div>
	<?php
}
else{
	?>
	<div class="no-elem"><?= dims_constant::getVal('NO_DOCUMENT'); ?></div>
	<?php
}
?>
<script type="text/javascript" src="common/js/jQuery-File-Upload/js/jquery.fileupload.js"></script>
<script type="text/javascript">
	$('document').ready(function(){
		mouse_is_inside=false;
		indexDocs();
		$('#launcher').click(function(){
			$('input#files').click();
		});

		$('#files').fileupload({
			dataType: 'json',
			done: function (e, data) {
				$.each(data.result.files, function (index, file) {
					$('#lst_files').append('<input type="text" name="files_name[]" value="'+file.name+'" />');
				});
			   document.upload_form.submit();
			},
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('#progress .bar').css('width', progress + '%');
			}
		});

		$('.uploaded').effect( "highlight", {color: '#f5b8c5'}, 1000 );

		$('.document').click(function(){
			cleanDetails();
			$('.document[data-row='+$(this).attr('data-row')+'][data-last=true]')
			.after('<div class="doc-details" data-id="'+$(this).attr('data-id')+'"><?= image_tag('ajax-loader.gif'); ?></div>');
			$('.doc-details[data-id='+$(this).attr('data-id')+']')
				.load('admin.php?action=show&id=<?= $objcourant->get('id'); ?>&cc=documents&aa=docinfos&docid='+$(this).attr('data-id'), function(){
					//var offset = $('.doc-details[data-id='+$(this).attr('data-id')+']').offset();
					//$(document).scrollTop(offset.top + $('.doc-details[data-id='+$(this).attr('data-id')+']').height());
				})
				.hover(function(){
					mouse_is_inside=true;
				}, function(){
					mouse_is_inside=false;
				});
			});

		$(window).resize(function(){
			//cleanDetails();
			indexDocs();
		});

		$('body').mouseup(function (e){
			if(!mouse_is_inside){
				cleanDetails();
			}
		});

		function cleanDetails(){
			$('.doc-details').remove();
			mouse_is_inside=false;
		}

		/* Le but de la fonction est de déterminer sur chaque élément son n° de ligne et le last */
		function indexDocs(){
			var container_w = $('.sub-navig').width() - 22; //22 = padding de 10 de chaque côté + border de 1
			var max_elems = ( container_w - (container_w%(110)) ) / 110 ; //le elem-index fait précisément 100px mais ont un margin right de 10
			var count = 1;//count par row
			var row = 1;//nombre de rows
			var i = 1;
			var total = $('.elem-index').length;
			$('.elem-index').each(function(){
				$(this).attr('data-row', row);
				if(count < max_elems && i < total) $(this).attr('data-last', false);
				else{
					$(this).attr('data-last', true);
					row++;
					count = 0;//0 parce qu'on refait un ++ juste derrière
				}
				count++;
				i++;
			});
		}
	});
</script>
