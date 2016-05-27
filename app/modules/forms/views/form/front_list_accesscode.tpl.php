<script type="text/javascript">
if($.fn.dims_validForm === undefined){
	jQuery.ajax({
		dataType: 'script',
		cache: true,
		url: "/assets/javascripts/common/dims_validForm.js",
		async: false
	});
}
</script>
<?php
$view = view::getInstance();
$form = $view->get('form');
?>
<div class="bg-warning">
	Un code d'accès est nécessaire pour accèder à cette liste.
</div>
<?php
$fo = new Dims\form(array(
	'name' 						=> 'form_access_'.$form->get('id'),
	'action'					=> $view->get('urlSend'),
	'additionnal_attributes'	=> 'role="form"',
));
echo $fo->get_header();
?>
<div class="row">
	<div class="form-group row">
		<label for="ac" class="col-sm-2 control-label">Code d'accès<span class="mandatory">*</span></label>
		<div class="col-sm-10">
			<?= $fo->password_field(array(
				'name'						=> 'ac',
				'id'						=> 'ac',
				'classes'					=> 'form-control',
				'additionnal_attributes' 	=> 'placeholder="Code d\'accès"',
				'value'						=> "",
				'mandatory'					=> true,
			)); ?>
		</div>
	</div>
</div>
<div class="text-right">
	<button type="submit" class="btn btn-success btn-sm"><?= $_SESSION['cste']['_SUBMIT']; ?></button>
</div>
<?php
echo $fo->close_form();
