<?php

$v = view::getInstance();

?>
<!--<script language="JavaScript" type="text/JavaScript" src="./common/js/chosen/chosen.jquery.js"></script>-->
<script language="JavaScript" type="text/JavaScript" src="<?php echo _DESKTOP_TPL_PATH; ?>/functions.js?v=224"></script>
<!--<script language="JavaScript" type="text/JavaScript" src="./common/js/portal_v5.js"></script>-->
<script language="Javascript" type="text/JavaScript">
	function toggle_menu(id_menu, image) {
	$('#'+id_menu).toggle();

	if(image.attr('src').contains('replier'))
		image.attr('src', '<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png');
	else
		image.attr('src', '<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png');
	}
</script>

<div id="inet_v2_content">
	<?php include _DESKTOP_TPL_LOCAL_PATH.'/desktop_content_content.tpl.php'; ?>
	<?php
	if (isset($mode) && !in_array($mode, array('newsletters','contact','company','address')))
		include _DESKTOP_TPL_LOCAL_PATH.'/desktop_content_droite.tpl.php';
	?>
</div>
