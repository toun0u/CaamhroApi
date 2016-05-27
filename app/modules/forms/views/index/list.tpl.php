<?php
$view = view::getInstance();
$lst = $view->get('forms');
?>
<h1>
	<?= $_SESSION['cste']['_FORMS_LIST']; ?>
	<?php if (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0)): ?>
		<a href="<?= form\get_path(array('c'=>'form','a'=>'new')); ?>" class="btn btn-success btn-sm pull-right">
			<span class="glyphicon glyphicon-plus"></span>
			<?= $_SESSION['cste']['_DIMS_ADD']; ?>
		</a>
	<?php endif; ?>
</h1>
<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>
					<?= $_SESSION['cste']['_DIMS_LABEL']; ?>
				</th>
				<th class="text-center col-md-1">
					<?= $_SESSION['cste']['_ANSWERS']; ?>
				</th>
				<th class="text-center col-md-1">
					<?= $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(empty($lst)){
				?>
				<tr>
					<td colspan="3" class="text-center">
						<?= $_SESSION['cste']['NO_RESULT']; ?>
					</td>
				</tr>
				<?php
			}else{
				foreach($lst as $f){
					?>
					<tr>
						<td>
							<?= $f->get('label'); ?>
						</td>
						<td class="text-center col-md-1">
							<?= $f->getLightAttribute('cpte'); ?>
						</td>
						<td class="text-center col-md-1">
							<div class="btn-group">
								<button type="button" class="btn btn-default dropdown-toggle btn-sm" id="drop-<?= $f->get('id'); ?>" data-toggle="dropdown">
									<?= $_SESSION['cste']['_DIMS_ACTIONS']; ?>
									<span class="glyphicon glyphicon-chevron-down"></span>
								</button>
								<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="drop-<?= $f->get('id'); ?>">
									<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'preview','id'=>$f->get('id'))); ?>"><span class="glyphicon glyphicon-search">&nbsp;<?= $_SESSION['cste']['_PREVIEW']; ?></a></li>
									<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'show','id'=>$f->get('id'))); ?>"><span class="glyphicon glyphicon-list-alt">&nbsp;<?= $_SESSION['cste']['_ANSWERS']; ?></a></li>
									<?php if(dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_EXPORT) || dims_isactionallowed(0)): ?>
										<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'export','id'=>$f->get('id'),'format'=>'CSV')); ?>"><span class="glyphicon glyphicon-export">&nbsp;<?= $_SESSION['cste']['_FORMS_EXPORT']." CSV"; ?></a></li>
										<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'export','id'=>$f->get('id'),'format'=>'XSL')); ?>"><span class="glyphicon glyphicon-export">&nbsp;<?= $_SESSION['cste']['_FORMS_EXPORT']." XSL"; ?></a></li>
									<?php endif; ?>
									<?php if ($f->get('nb_fields') <= 0 && (dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_ADDREPLY) || dims_isactionallowed(0))): ?>
										<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'import','id'=>$f->get('id'))); ?>"><span class="glyphicon glyphicon-import">&nbsp;Importer un fichier</a></li>
									<?php endif; ?>
									<?php if(dims_isadmin() || dims_isactionallowed(_FORMS_ACTION_CREATEFORM) || dims_isactionallowed(0)): ?>
										<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$f->get('id'))); ?>"><span class="glyphicon glyphicon-list">&nbsp;<?= $_SESSION['cste']['_FORMS_FIELDLIST']; ?></a></li>
										<li class="text-left"><a href="<?= form\get_path(array('c'=>'form','a'=>'edit','id'=>$f->get('id'))); ?>"><span class="glyphicon glyphicon-pencil">&nbsp;<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></a></li>
										<li class="text-left"><a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= form\get_path(array('c'=>'form','a'=>'delete','id'=>$f->get('id'))); ?>','<?= $_SESSION['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']; ?>');"><span class="glyphicon glyphicon-trash">&nbsp;<?= $_SESSION['cste']['_DIMS_LABEL_DELETE_GROUP']; ?></a></li>
									<?php endif; ?>
								</ul>
							</div>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</table>
