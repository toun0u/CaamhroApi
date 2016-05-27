<?php
$view = view::getInstance();
$form = $view->get('form');
$import = $view->get('import');
?>
<h1><?= "&Eacute;tape 2 : S&eacute;lection des champs (".$form->get('label').")"; ?></h1>
<?php
$f = new Dims\form(array(
	'name' 			=> 'form_import_2',
	'action'		=> form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>3)),
	'submit_value' 	=> $_SESSION['cste']['_SYSTEM_LABELTAB_USERIMPORT'],
	'back_url'		=> form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>0)),
));
echo $f->get_header();

$options = array(
	0 => 'Pas de ligne de nom',
);
for ($i=1;$i<=$import['nbrow'];$i++){
	$options[$i] = "Ligne $i";
}
?>
<div class="row">
	<div class="col-md-6">
		<div class="row">
			<div class="form-group row">
				<label for="ac" class="col-sm-5 control-label text-right">Ligne contenant les noms des champs</label>
				<div class="col-sm-5">
					<?= $f->select_field(array(
						'name' 		=> 'firstdataline',
						'value'		=> $import['firstdataline'],
						'options'	=> $options,
					)); ?>
				</div>
			</div>
			<div class="form-group row">
				<label for="ac" class="col-sm-5 control-label text-right">Nombre total de lignes</label>
				<div class="col-sm-5">
					<?= $import['nbrow']; ?>
				</div>
			</div>
		</div>
		<div class="text-right">
			<a href="<?= form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>0)); ?>" class="btn btn-link btn-sm "><?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?></a>
			<span><?= $_SESSION['cste']['_DIMS_OR']; ?></span>
			<button type="submit" class="btn btn-success btn-sm"><?= $_SESSION['cste']['_SUBMIT']; ?></button>
		</div>
	</div>
	<div class="col-md-6 form-preview">
		<?php $view->partial($view->getTemplatePath('form/import_step2_preview.tpl.php')); ?>
	</div>
</div>
<?= $f->close_form(); ?>
<script type="text/javascript">
$(document).ready(function(){
$('.form-preview').delegate('.edit-label','click',function(){
	var k = $(this).attr('dims-data-value'),
		tr = $(this).parents('tr:first');
	$.ajax({
		type: "POST",
		url: '<?= form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>4)); ?>',
		data: {
			field: k,
		},
		dataType: 'html',
		success: function(data){
			tr.html(data);
			$('.form-preview .edit-label').attr('disabled','disabled');
		},
	});
}).delegate('.save-label','click',function(){
	var k = $(this).attr('dims-data-value'),
		tr = $(this).parents('tr:first');
	$.ajax({
		type: "POST",
		url: '<?= form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>6)); ?>',
		data: {
			field: k,
			format:$('#format',tr).val(),
			type:$('#type',tr).val(),
			title:$('#label',tr).val()
		},
		dataType: 'html',
		success: function(data){
			$('.form-preview').html(data);
		},
	});
}).delegate('.undo-label','click',function(){
	$.ajax({
		type: "POST",
		url: '<?= form\get_path(array('c'=>'form','a'=>"import",'id'=>$form->get('id'),'step'=>5)); ?>',
		dataType: 'html',
		success: function(data){
			$('.form-preview').html(data);
		},
	});
});
});
</script>
