<?php
$import = $this->get('import');
$k = $this->get('k'); // clef
$formats = array(
	'int' => $_SESSION['cste']['_DIMS_LABEL_INT'],
	'float' => $_SESSION['cste']['_DIMS_LABEL_FLOAT'],
	'date' => $_SESSION['cste']['_DIMS_DATE'],
	'string' => $_SESSION['cste']['_DIMS_LABEL_STRING'],
);
global $field_types;
$f = new Dims\form();
?>

<td class="text-center">
	<?= $f->select_field(array(
		'name' 		=> 'format',
		'value'		=> $import['formatcol'][$k],
		'options'	=> $formats,
	)); ?>
</td>
<td class="text-center">
	<?= $f->select_field(array(
		'name' 		=> 'type',
		'value'		=> $import['typecol'][$k],
		'options'	=> $field_types,
	)); ?>
</td>
<td>
	<?= $f->text_field(array(
		'name' 		=> 'label',
		'value'		=> $import['titlecol'][$k],
	)); ?>
</td>
<td class="text-center">
	<a href="javascript:void(0);" type="button" class="btn btn-default btn-sm save-label" dims-data-value="<?= $k; ?>"><span class="glyphicon glyphicon-save"></a>
	<a href="javascript:void(0);" type="button" class="btn btn-link btn-sm undo-label" dims-data-value="<?= $k; ?>">Annuler</a>
</td>