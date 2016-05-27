<?
if (!isset($_SESSION['desktop']['display']['tags']['generic']) || $_SESSION['desktop']['display']['tags']['generic'] <= 0)
	$_SESSION['desktop']['display']['tags']['generic'] = 1;
$nbGenericTags = count($desktop->getGenericTags());
$lstTags = $desktop->getGenericTags(0,_DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['generic']);
?>
<div class="generic">
	<h3 class="h3_generic"><?php echo $_SESSION['cste']['_DIMS_LABEL_LFB_GEN']; ?></h3>
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" border="0" onclick="javascript:$('div.zone_generic').slideToggle('fast',flip_flop($('div.zone_generic'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
</div>
<div class="zone_generic">
	<?
	foreach ($lstTags as $tag){
		$tag->setLightAttribute('from_desktop', true);
		$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
	}
	?>
	<div class="zone_generic_see_more">
	<?
	if ($_SESSION['desktop']['display']['tags']['generic'] > 1){
		?>
		<span>
			<a onclick="javascript:seeLessGenericTags();" href="javascript:void(0);"><?php echo $_SESSION['cste']['SEE_LESS']; ?> ...</a>
		</span>
		<?
	}
	if ($_SESSION['desktop']['display']['tags']['generic'] > 1 && $nbGenericTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['generic'])
		echo '<span>&nbsp;/&nbsp;</span>';
	if ($nbGenericTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['generic']){
		?>
		<span>
			<a onclick="javascript:seeMoreGenericTags();" href="javascript:void(0);"><?php echo $_SESSION['cste']['SEE_MORE']; ?>...</a>
		</span>
		<?
	}
	?>
	</div>
</div>
