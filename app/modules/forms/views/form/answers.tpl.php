<?php
$view = view::getInstance();
$form = $view->get('form');
$fields = $view->get('fields');
$answers = $view->get('answers');
$s = $view->get('search');
$nbFields = 1;
?>
<h1>
	<?= $_SESSION['cste']['_DIMS_LABEL_FORM']." : ".$form->get('label'); ?>
	<a href="<?= form\get_path(array('c'=>'index')); ?>" class="btn btn-default btn-sm pull-right">
		<span class="glyphicon glyphicon-chevron-left"></span>
		<?= $_SESSION['cste']['_DIMS_LINK_BACK_LIST']; ?>
	</a>
</h1>
<div class="text-right" style="margin-bottom:10px;">
	<a href="<?= form\get_path(array('c'=>'answer','a'=>'new', 'id'=>$form->get('id'))); ?>" class="btn btn-success btn-sm">
		<span class="glyphicon glyphicon-plus"></span>
		<?= $_SESSION['cste']['_DIMS_ADD']; ?>
	</a>
</div>
<?php
$f = new Dims\form(array(
	'name' 			=> 'form_search',
	'action'		=> dims::getInstance()->getScriptEnv(),
	'submit_value' 	=> $_SESSION['cste']['_SEARCH'],
	'method'		=> 'GET',
	'back_name'		=> $_SESSION['cste']['_DIMS_RESET'],
	'back_url'		=> form\get_path(array('c'=>'form','a'=>"show",'id'=>$form->get('id'))),
));
$f->add_hidden_field(array(
	'name' 		=> 'c',
	'value'		=> 'form',
));
$f->add_hidden_field(array(
	'name' 		=> 'a',
	'value'		=> 'show',
));
$f->add_hidden_field(array(
	'name' 		=> 'id',
	'value'		=> $form->get('id'),
));
$f->add_text_field(array(
	'name' 		=> 's',
	'label'		=> $_SESSION['cste']['_FORMS_FILTER'],
	'value'		=> $s,
));
$f->build();
//TODO : add pagination
?>
<div class="table-responsive" style="margin-top:10px;">
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
				<!-- TODO : d'autres options sont dispo -->
				<?php if($form->get('option_displaydate')){
					$nbFields++;
					?>
					<th>
						<?= $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?>
					</th>
				<?php } ?>
				<?php if($form->get('option_displayip')){
					$nbFields++;
					?>
					<th>
						IP
					</th>
				<?php } ?>
				<th class="text-center">
					<?= $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
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
						<!-- TODO : d'autres options sont dispo -->
						<?php if($form->get('option_displaydate')){
							?>
							<td>
								<?php
								$dd = dims_timestamp2local($a->get('date_validation'));
								echo $dd['date']." ".$dd['time'];
								?>
							</td>
						<?php } ?>
						<?php if($form->get('option_displayip')){
							?>
							<td class="col-md-1">
								<?= $a->get('ip'); ?>
							</td>
						<?php } ?>
						<td class="text-center col-md-1">
							<a href="<?= form\get_path(array('c'=>'answer','a'=>'edit','id'=>$a->get('id'))); ?>" class="btn btn-default btn-sm" alt="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
							<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= form\get_path(array('c'=>'answer','a'=>'delete','id'=>$a->get('id'))); ?>','<?= $_SESSION['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']; ?>');" class="btn btn-danger btn-sm" alt="<?= $_SESSION['cste']['_DIMS_LABEL_DELETE_GROUP']; ?>" title="<?= $_SESSION['cste']['_DIMS_LABEL_DELETE_GROUP']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
						</td>
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
	<div class="text-right" style="margin-bottom:10px;">
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
?>
<div class="text-right">
	<a href="<?= form\get_path(array('c'=>'answer','a'=>'new', 'id'=>$form->get('id'))); ?>" class="btn btn-success btn-sm">
		<span class="glyphicon glyphicon-plus"></span>
		<?= $_SESSION['cste']['_DIMS_ADD']; ?>
	</a>
</div>
