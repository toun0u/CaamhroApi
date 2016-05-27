<?php
$form = $this->get('form');
$id_web_ask = $this->get('id_web_ask');
?>
<h1>
	<span class="icon-archive"></span>&nbsp;Nouveau dossier<?= empty($id_web_ask)?"":(" pour la demande <span style=\"color:#444444;\">#".$id_web_ask.'</span>'); ?>
	<a class="icon-close right mt10" href="javascript:void(0);" onclick="javascript:dims_closeOverlayedPopup('<?= $this->get('id_popup');?>');"></a>
</h1>
<?php

$this->partial($this->getTemplatePath('dossier/_edit_description.tpl.php'));

$form->build();
