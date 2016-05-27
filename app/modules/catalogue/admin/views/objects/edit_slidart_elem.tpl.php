<?php
$view = view::getInstance();
$tag = $view->get('elem');
$elem = $view->get('elems');

$searchArt = dims::getInstance()->getScriptEnv()."?c=objects&a=searchArticle";

$form = new Dims\form(array(
	'name'              => 'f_slidart_elem',
	'action'            => get_path('objects', 'showart', array('id'=>$tag->get('id'), 'sa'=>'saveelemart')),
	'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
	'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
	'back_url'          => get_path('objects', 'showart', array('id'=>$tag->get('id'))),
	'validation'        => false
));
$form->addBlock('default', dims_constant::getVal('_SLIDESHOW_ARTICLES')." > ".$tag->fields['nom']);
if(!$elem->isNew()){
	$form->add_hidden_field(array(
		'name'          => 'sid',
		'value'         => $elem->get('id')
	));
}

$form->add_hidden_field(array(
	'name'          => 'elem_id_slidart',
	'value'         => $elem->fields['id_slidart']
));

$desc_block = $form->getBlock('default');
?>
<div class="form_object_block">
	<?= $form->get_header(); ?>
	<div class="sub_bloc" id="<?= $desc_block->getId(); ?>">
		<?php
		$title = $desc_block->getTitle();
		if (!empty($title)) {
			?>
			<h3><?php echo $title; ?></h3>
			<?php
		}
		echo $form->hidden_field(array(
			'name'      => 'elem_id_slidart',
			'value'         => $elem->fields['id_slidart']
		));
		if(!$elem->isNew()){
			echo $form->hidden_field(array(
				'name'          => 'sid',
				'value'         => $elem->get('id')
			));
		}
		?>
		<div class="sub_bloc_form">
			<div id="add_fields">
				<?= $form->text_field(array(
					'name'      => 'search_article',
					'classes'   => 'temp_message w300p',
					'value'     => dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'),
					'dom_extension' => '<div id="ac_references" class="ac_container" style="display:none;"><ul id="ul_ac_references"></ul></div>'
				));
				echo $form->hidden_field(array(
					'name'      => 'ref_article'
				));
				?>

			</div>
			<?php if(!$elem->isNew()){ ?>
			<table class="tableau">
				<?php
				$art = $elem->getArticle();
				$url = $art->getVignette(50);
				?>
				<tr>
					<td class="w50p">
						<?php
						if(!is_null($url)){
							?>
							<img src="<?= $url; ?>" />
							<?php
						}
						?>
					</td>
					<td>
						<?= $art->fields['reference']; ?>
					</td>
					<td>
						<?= $art->fields['label']; ?>
					</td>
				</tr>
			</table>
			<?php } ?>
		</div>
		<?= $form->displayActionsBlock(); ?>
	</div>
	<?= $form->close_form(); ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#search_article').focus(function(){
		if($(this).val() == '<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>'){

			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
		if ( event.which == 13 ) {
			event.preventDefault();
		}
	})
	.dims_autocomplete( { c : 'objects', a : 'searchArticle' }, 2, 500, '#ref_article', '#ac_references', '#ul_ac_references', '<li>${label}</li>', '<?php echo addslashes(dims_constant::getVal('NO_REFERENCE')); ?>', null );
	$("#ref_article").change(function(){
		var integer = /^\d*$/;
		var val = $(this).val()
		if(val != '' && val.match(integer))
			document.f_slidart_elem.submit();
	});
});
</script>
