<?php
// TODO : Utiliser des constantes de langue Dims
?>
<form class="ajaxForm" name="form_categ_tag_edit<? echo $this->get('id'); ?>" id="form_categ_tag_edit<? echo $this->get('id'); ?>" style="margin:0;" action="admin.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="save_categ_tag" />
<input type="hidden" name="id_categ" value="<? echo $this->get('id'); ?>" />
<span style="float:left;">
<label><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label>
<input type="text" id="tag_label" name="tag_label" value="<? echo str_replace('"','&quot;',$this->get('title')); ?>">

<a href="javascript:void(0)" onclick="javascript:saveCategTag(<? echo $this->get('id'); ?>);"><img src="./common/img/checkdo.png" style="border:0px"></a>
<a href="javascript:void(0)" onclick="javascript:hideCategTag(<? echo $this->get('id'); ?>);"><img src="./common/img/delete.png" style="border:0px"></a>
</span>
</form>
<script>
$("#tag_label").focus();
</script>
