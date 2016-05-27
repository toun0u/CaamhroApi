<?php
$view = view::getInstance();
$form = $view->get('form');
$fields = $form->getAllFields();

global $field_types;

$sa = $view->get('sa');
$disabled = "";
if($sa == 'edit'){
	$new = $view->get('edit');
	$disabled = 'disabled="disabled"';
}
?>
<h1>
	<?= $_SESSION['cste']['_DIMS_LABEL_FORM']." : ".$form->get('label'); ?>
	<a href="<?= form\get_path(array('c'=>'index')); ?>" class="btn btn-default btn-sm pull-right">
		<span class="glyphicon glyphicon-chevron-left"></span>
		<?= $_SESSION['cste']['_DIMS_LINK_BACK_LIST']; ?>
	</a>
</h1>
<table class="table table-responsive table-bordered">
	<tr>
		<td colspan="7" class="text-right">
			<div class="btn-group">
				<button type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" <?= $disabled; ?>>
					<?= $_SESSION['cste']['_DIMS_LABEL_ADDFIELD']; ?>
					<span class="glyphicon glyphicon-chevron-down"></span>
				</button>
				<ul class="dropdown-menu pull-right" role="menu">
					<?php foreach($field_types as $k => $v): ?>
						<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'),'sa'=>'edit','t'=>$k,'p'=>1)); ?>"><?= $v; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
	<theader>
		<tr>
			<th class="col-md-1">Position</th>
			<th class="col-md-3">Intitul&eacute;</th>
			<th class="col-md-2">Type</th>
			<th class="col-md-3">Description</th>
			<th class="col-md-1">Req.</th>
			<th class="col-md-1">Visible export</th>
			<th class="col-md-1">Actions</th>
		</tr>
	</theader>
	<tbody>
		<?php
		$nbFields = count($fields);
		if(isset($new) && $new->isNew() && $new->get('position') == 1){
			echo $new->display(DIMS_APP_PATH."modules/forms/views/field/_default_form.tpl.php");
		}elseif($nbFields <= 0){
			?>
			<tr>
				<td colspan="7" class="col-md-12">
					Aucun champ pour ce formulaire
				</td>
			</tr>
			<?php
		}
		foreach($fields as $f){
			if(isset($new) && !$new->isNew() && $new->get('id') == $f->get('id')){
				echo $f->display(DIMS_APP_PATH."modules/forms/views/field/_default_form.tpl.php");
			}else{
				$f->setLightAttribute('nbElem',$nbFields);
				$f->setLightAttribute('disabled',$disabled);
				echo $f->display(DIMS_APP_PATH."modules/forms/views/field/_display.tpl.php");
			}
		}
		if(isset($new) && $new->isNew() && $new->get('position') == $nbFields+1 &&  $new->get('position') > 1){
			echo $new->display(DIMS_APP_PATH."modules/forms/views/field/_default_form.tpl.php");
		}
		?>
	</tbody>
	<tfooter>
		<tr>
			<th class="col-md-1">Position</th>
			<th class="col-md-3">Intitul&eacute;</th>
			<th class="col-md-2">Type</th>
			<th class="col-md-3">Description</th>
			<th class="col-md-1">Req.</th>
			<th class="col-md-1">Visible export</th>
			<th class="col-md-1">Actions</th>
		</tr>
	</tfooter>
	<tr>
		<td colspan="7" class="text-right">
			<div class="btn-group dropup">
				<button type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" <?= $disabled; ?>>
					<?= $_SESSION['cste']['_DIMS_LABEL_ADDFIELD']; ?>
					<span class="glyphicon glyphicon-chevron-up"></span>
				</button>
				<ul class="dropdown-menu pull-right" role="menu">
					<?php foreach($field_types as $k => $v): ?>
						<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'),'sa'=>'edit','t'=>$k,'p'=>$nbFields+1)); ?>"><?= $v; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
</table>
