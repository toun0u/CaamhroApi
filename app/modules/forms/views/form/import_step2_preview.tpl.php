<?php
$form = $this->get('form');
$import = $this->get('import');
global $field_types;
?>
<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="text-center col-md-2">
					Format
				</th>
				<th class="text-center col-md-2">
					Type
				</th>
				<th>
					Label
				</th>
				<th class="text-center col-md-2">
					Actions
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($import['formatcol'] as $li => $f){
			?>
			<tr>
				<td class="text-center">
					<?php
					switch($f) {
						case "int":
							$format=$_SESSION['cste']['_DIMS_LABEL_INT'];
							break;
						case "float":
							$format=$_SESSION['cste']['_DIMS_LABEL_FLOAT'];
							break;
						case "date":
							$format=$_SESSION['cste']['_DIMS_DATE'];
							break;
						case "string":
						default:
							$format=$_SESSION['cste']['_DIMS_LABEL_STRING'];
							break;

					}
					echo $format
					?>
				</td>
				<td class="text-center">
					<?= $field_types[$import['typecol'][$li]]; ?>
				</td>
				<td>
					<?= $import['titlecol'][$li]; ?>
				</td>
				<td class="text-center">
					<a href="javascript:void(0);" type="button" class="btn btn-default btn-sm edit-label" dims-data-value="<?= $li; ?>"><span class="glyphicon glyphicon-pencil"></a>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
</div>
