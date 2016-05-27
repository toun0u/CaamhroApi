<?php
$view = view::getInstance();
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');
$document = $view->get('document');
$current = $view->get('current');
$error = $view->get('error');
if( empty($error) ){
	?>
	<h3 style="color:white;"><?= $document->get('name'); ?></h3>
	<div class="line grid1-3">
		<div class="mod">
			<div class="thumb-wrapper">
				<?php
				$path = $document->getThumbnail(400);
				if(is_null($path)){
					?>
					<i class="icon-file white"></i>
					<?php
				}
				else{
					?>
					<img src="<?= $path; ?>" />
					<?php
				}
				?>
			</div>
		</div>
		<div class="mod">
			<div class="actionnable-wrapper mb10">
				<a href="<?= $document->getDownloadLink(); ?>" class="actionnable" title="<?= dims_constant::getVal('_DIMS_DOWNLOAD'); ?>">
					<i class="icon-arrow-down"></i>
				</a>
			</div>
			<?php
			//if(can_edit($view->get('connected_agent'), $objcourant)){
				?>
				<div class="actionnable-wrapper mb10">
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= get_path('show', 'show', array('id' => $objcourant->get('id'), 'cc' => 'documents', 'aa' => 'delete_doc', 'docid' => $document->get('id'))); ?>', '<?= addslashes(dims_constant::getVal('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?'));?>');" class="actionnable" title="<?= dims_constant::getVal('_DELETE'); ?>">
						<i class="icon-remove"></i>
					</a>
				</div>
				<div class="actionnable-wrapper">
					<a href="javascript:void(0);" onclick="javascript:editDoc(this);" class="actionnable" title="<?= dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'); ?>">
						<i class="icon-pencil"></i>
					</a>
				</div>
								<div class="actionnable-wrapper">
					<a href="javascript:void(0);" onclick="javascript:moveDoc(this);" class="actionnable" title="<?= dims_constant::getVal('_DIMS_LABEL_MOVE'); ?>">
						<i class="icon-arrow-down-alt1"></i>
					</a>
				</div>
				<?php
			//}
			?>
			<div class="description">
				<?php
				$description = $document->get('description');
				if(!empty($description)){
					echo nl2br($description);
				}
				else{
					echo dims_constant::getVal('NO_DESCRIPTION');
				}
				?>
			<div>
		</div>

		<script type="text/javascript">
			function editDoc(elem){
				var id = $(elem).parents('div.doc-details:first').attr('data-id');
				$('.description').load('admin.php?action=show&id=<?= $objcourant->get('id'); ?>&cc=documents&aa=docdesc&docid='+id);
			}

						function moveDoc(elem) {
							var id = $(elem).parents('div.doc-details:first').attr('data-id');
				$('.description').load('admin.php?action=show&id=<?= $objcourant->get('id'); ?>&cc=documents&aa=docmove&docid='+id);
						}
			function validDescription(elem){
				var id = $(elem).parents('div.doc-details:first').attr('data-id');
				var description = $('#description').val();
				$.ajax({
					type: "POST",
					url: "admin.php",
					async: false,
					data: {
						'action': 'show',
						'id': <?= $objcourant->get('id'); ?>,
						'cc': 'documents',
						'aa': 'savedesc',
						'docid': id,
						'description': description
					},

					dataType: "text",
					success: function(data){
						$('.description').html(nl2br(description));
					}

				});
			}

			function annulEdition(elem){
				var id = $(elem).parents('div.doc-details:first').attr('data-id');
				$('.description').load('admin.php?a=show&id=<?= $objcourant->get('id'); ?>&cc=documents&aa=getdesc&docid='+id);
			}

		</script>
	</div>
<?php
}
else{
	echo $error;
}
?>
