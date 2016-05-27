<?php
global $field_types;
global $field_formats;
$nbElem = $this->getLightAttribute('nbElem');
$disabled = $this->getLightAttribute('disabled');
?>
<tr>
	<td class="col-md-1 text-center">
		<?php if($this->get('position') == 1): ?>
			<button type="button" class="btn btn-default btn-sm" disabled="disabled"><span class="glyphicon glyphicon-chevron-up"></button>
		<?php else: ?>
			<a <?= $disabled; ?> href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'down','idf'=>$this->get('id'))); ?>" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-chevron-up"></a>
		<?php endif; ?>
		<?php if($this->get('position') >= $nbElem): ?>
			<button type="button" class="btn btn-default btn-sm" disabled="disabled"><span class="glyphicon glyphicon-chevron-down"></span></button>
		<?php else: ?>
			<a <?= $disabled; ?> href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'up','idf'=>$this->get('id'))); ?>" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-chevron-down"></span></a>
		<?php endif; ?>
	</td>
	<td class="col-md-3">
		<?= $this->get('name'); ?>
	</td>
	<td class="col-md-2">
		<?= $field_types[$this->get('type')].(($this->get('type')=='text')?' ('.$field_formats[$this->get('format')].')':''); ?>
	</td>
	<td class="col-md-3">
		<?= $this->get('description'); ?>
	</td>
	<td class="col-md-1 text-center">
		<a <?= $disabled; ?> href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'toggle_required','idf'=>$this->get('id'))); ?>" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-<?= $this->get('option_needed')?'check':'unchecked'; ?>"></a>
	</td>
	<td class="col-md-1 text-center">
		<a <?= $disabled; ?> href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'toggle_export','idf'=>$this->get('id'))); ?>" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-<?= $this->get('option_exportview')?'check':'unchecked'; ?>"></a>
	</td>
	<td class="col-md-1 text-center">
		<a <?= $disabled; ?> href="<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'edit','idf'=>$this->get('id'))); ?>" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
		<a <?= $disabled; ?> href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'delete','idf'=>$this->get('id'))); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
	</td>
</tr>
