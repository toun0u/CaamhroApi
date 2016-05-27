<?php
$view = view::getInstance();
$workflows = $view->get('workflows');
?>
<h1>Workflows</h1>
<div class="txtright">
	<a href="<?= Gescom\get_path(array('c'=>'admin','a'=>'add_workflow')); ?>" class="btn"><span class="icon-plus-alt"></span>&nbsp;Ajouter un workflow</a>
</div>

<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="w20">Label</th>
				<th class="">Description</th>
				<th class="w5">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			if(empty($workflows)){
				?>
				<tr>
					<td colspan="3" class="txtcenter">
						Aucun workflow défini
					</td>
				</tr>
				<?php
			}else{
				foreach($workflows as $w){
					?>
					<tr>
						<td>
							<?= $w->get('label'); ?>
						</td>
						<td>
							<?= nl2br($w->get('description')); ?>
						</td>
						<td class="txtcenter">
							<a href="<?= Gescom\get_path(array('c'=>'admin','a'=>'show_workflow','id'=>$w->get('id'))); ?>" alt="Visualiser" title="Visualiser" style="text-decoration:none;"><span class="icon-tree"></span></a>&nbsp;
							<a href="<?= Gescom\get_path(array('c'=>'admin','a'=>'add_workflow','id'=>$w->get('id'))); ?>" alt="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" style="text-decoration:none;"><span class="icon-pencil"></span></a>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</div>