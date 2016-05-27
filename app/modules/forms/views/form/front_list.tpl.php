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
$fields = $view->get('fields');
$answers = $view->get('answers');
$search = $view->get('search');
$urlSend = $view->get('urlSend');

$nbFields = 1;
?>
<h1>
	<?= $_SESSION['cste']['_DIMS_LABEL_FORM']." : ".$form->get('label'); ?>
</h1>
<?php
$f = new Dims\form(array(
	'name' 			=> 'form_search',
	'action'		=> $urlSend,
	'submit_value' 	=> $_SESSION['cste']['_SEARCH'],
	'method'		=> 'GET',
	'back_name'		=> $_SESSION['cste']['_DIMS_RESET'],
	'back_url'		=> $urlSend,
));
echo $f->get_header();
?>
<div class="row mt2 mb1">
	<div class="form-group row">
		<label for="ac" class="col-sm-2 control-label"><?= $_SESSION['cste']['_FORMS_FILTER']; ?></label>
		<div class="col-sm-10">
			<?= $f->text_field(array(
				'name' 						=> 's',
				'value'						=> $search,
				'classes'					=> 'form-control',
				'additionnal_attributes' 	=> 'placeholder="'.$_SESSION['cste']['_FORMS_FILTER'].'"',
			)); ?>
		</div>
	</div>
</div>
<div class="text-right mb2">
	<a href="<?= $urlSend."?".http_build_query(array('a'=>'export','s'=>$search)); ?>" class="btn btn-link btn-sm left"><?= $_SESSION['cste']['_FORMS_DATA_EXPORT']; ?></a>
	<a href="<?= $urlSend; ?>" class="btn btn-link btn-sm "><?= $_SESSION['cste']['_DIMS_RESET']; ?></a>
	<span><?= $_SESSION['cste']['_DIMS_OR']; ?></span>
	<button type="submit" class="btn btn-success btn-sm"><?= $_SESSION['cste']['_SUBMIT']; ?></button>
</div>
<?= $f->close_form(); ?>
<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<?php foreach($fields as $f){
					if($f->get('option_arrayview')){
						$nbFields++;
						?>
						<th>
							<?= $f->get('name'); ?>
						</th>
					<?php } ?>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php
			if(empty($answers)){
				?>
				<tr>
					<td colspan="<?= $nbFields; ?>" class="text-center">
						<?= $_SESSION['cste']['NO_RESULT']; ?>
					</td>
				</tr>
				<?php
			}else{
				foreach($answers as $a){
					$Afields = $a->getFields();
					?>
					<tr>
						<?php foreach($fields as $f){
							if($f->get('option_arrayview')){
								if($f->get('type') == 'file'){
									?>
									<td>
										<?php
										$path = _DIMS_PATHDATA.'forms-'.$form->get('id_module')._DIMS_SEP.$form->get('id')._DIMS_SEP.$a->get('id')._DIMS_SEP;
										if(isset($Afields[$f->get('id')]) && file_exists($path.$Afields[$f->get('id')]->get('value'))){
											?>
											<a href="/data/forms-<?= $form->get('id_module')._DIMS_SEP.$form->get('id')._DIMS_SEP.$a->get('id')._DIMS_SEP.$Afields[$f->get('id')]->get('value'); ?>"><?= $Afields[$f->get('id')]->get('value'); ?></a>
											<?php
										}
										?>
									</td>
									<?php
								}else{
									?>
									<td>
										<?= isset($Afields[$f->get('id')])?$Afields[$f->get('id')]->get('value'):""; ?>
									</td>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</table>
<?php
$pagin = $form->getPagination();
if(!empty($pagin)){
	?>
	<div class="text-right">
		<?php
		foreach($pagin as $p){
			if(empty($p['url'])){
				?>
				<button type="button" disabled="disabled" class="btn btn-default btn-xs"><?= $p['label']; ?></button>
				<?php
			}else{
				?>
				<a href="<?= $p['url']; ?>" class="btn btn-default btn-xs" alt="<?= $p['title']; ?>" title="<?= $p['title']; ?>"><?= $p['label']; ?></a>
				<?php
			}
		}
		?>
	</div>
	<?php
}
