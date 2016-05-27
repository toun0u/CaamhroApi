<?
if (!isset($_SESSION['desktop']['display']['tags']['recently']) || $_SESSION['desktop']['display']['tags']['recently'] <= 0)
	$_SESSION['desktop']['display']['tags']['recently'] = 1;

$nbRecentlyTags = count($desktop->getRecentlyTags(0,0,0));
$lstRTags = $desktop->getRecentlyTags(0,0,0);//_DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['recently'],0);

if ($nbRecentlyTags > 0){
	?>
	<div class="recently_used">
		<h3 class="h3_recently_used"><?php echo ucfirst($_SESSION['cste']['_DIMS_LABEL_LFB_GEN']); ?></h3>
		<?php


		?>
                <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" border="0" onclick="javascript:$('div.zone_recently_used').slideToggle('fast',flip_flop($('div.zone_recently_used'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
	</div>
	<div class="zone_recently_used">
		<?

		foreach ($lstRTags as $tag){
			$tag->setLightAttribute('from_desktop', true);
			$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
		}
		?>
		<div class="zone_generic_see_more">
		<?
		if ($_SESSION['desktop']['display']['tags']['recently'] > 1){
			?>
			<span class="zone_recently_used_see_more">
				<a onclick="javascript:seeLessRecentlyTags();" href="javascript:void(0);"><?php echo $_SESSION['cste']['SEE_LESS']; ?> ...</a>
			</span>
			<?
		}
		if ($_SESSION['desktop']['display']['tags']['recently'] > 1 && $nbRecentlyTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['recently'])
			echo '<span>&nbsp;/&nbsp;</span>';
		if ($nbRecentlyTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['recently']){
			?>
			<span class="zone_recently_used_see_more">
				<a onclick="javascript:seeMoreRecentlyTags();" href="javascript:void(0);"><?php echo $_SESSION['cste']['SEE_MORE']; ?>...</a>
			</span>
			<?
		}
		?>
		</div>
	</div>
	<?
}


$nbRecentlyTags = count($desktop->getRecentlyTags(0,0,2));
$lstRTags = $desktop->getRecentlyTags(0,0,0);//_DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['recently'],2);
if ($nbRecentlyTags > 0){
	?>
	<div class="recently_used">
		<h3 class="h3_recently_used"><?php echo ucfirst($_SESSION['cste']['_DIMS_LABEL_INDUSTRY']); ?></h3>
		<?php
		/*
		* Hack demandé par André Hansen, on supprime les tags générique donc plus besoin d'avoir un repli sur ce bloc
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" border="0" onclick="javascript:$('div.zone_recently_used').slideToggle('fast',flip_flop($('div.zone_recently_used'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
		*/
		?>
                <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" border="0" onclick="javascript:$('div.zone_recently_used').slideToggle('fast',flip_flop($('div.zone_recently_used'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
	</div>
	<div class="zone_recently_used">
		<?
		foreach ($lstRTags as $tag){
			$tag->setLightAttribute('from_desktop', true);
			$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
		}
		?>
		<div class="zone_generic_see_more">
		<?
		if ($_SESSION['desktop']['display']['tags']['recently'] > 1){
			?>
			<span class="zone_recently_used_see_more">
				<a onclick="javascript:seeLessRecentlyTags();" href="javascript:void(0);"><?php echo $_SESSION['cste']['SEE_LESS']; ?> ...</a>
			</span>
			<?
		}
		if ($_SESSION['desktop']['display']['tags']['recently'] > 1 && $nbRecentlyTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['recently'])
			echo '<span>&nbsp;/&nbsp;</span>';
		if ($nbRecentlyTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['recently']){
			?>
			<span class="zone_recently_used_see_more">
				<a onclick="javascript:seeMoreRecentlyTags();" href="javascript:void(0);"><?php echo $_SESSION['cste']['SEE_MORE']; ?>...</a>
			</span>
			<?
		}
		?>
		</div>
	</div>
	<?
}
?>
