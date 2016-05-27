<?
$dims = dims::getInstance();
if (!isset($_SESSION['desktop']['display']['tags']['search']) || $_SESSION['desktop']['display']['tags']['search'] <= 0)
	$_SESSION['desktop']['display']['tags']['search'] = 1;
$nbSearchTags = count($desktop->getSearchTags($_SESSION['tags']['search']));
$lstTags = $desktop->getSearchTags($_SESSION['tags']['search'],0,_DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['search']);
?>
<div class="generic">
	<span><?php echo $_SESSION['cste']['_DIMS_SEARCH_RESULT'];?></span>
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" border="0" onclick="javascript:$('div.zone_generic').slideToggle('fast',flip_flop($('div.zone_generic'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
</div>
<div class="zone_generic">
	<?
	if ($nbSearchTags > 0){
		foreach ($lstTags as $tag){
			$tag->setLightAttribute('from_desktop', true);
			$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
		}
		?>
		<div class="zone_generic_see_more">
		<?
		if ($_SESSION['desktop']['display']['tags']['search'] > 1){
			?>
			<span>
				<a onclick="javascript:seeLessSearchTags();" href="javascript:void(0);">See less ...</a>
			</span>
			<?
		}
		if ($_SESSION['desktop']['display']['tags']['search'] > 1 && $nbSearchTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['search'])
			echo '<span>&nbsp;/&nbsp;</span>';
		if ($nbSearchTags > _DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['search']){
			?>
			<span>
				<a onclick="javascript:seeMoreSearchTags();" href="javascript:void(0);">See more ...</a>
			</span>
			<?
		}
	}
	?>
		<div class="new_tag">
			<form method="POST" action="<? echo $dims->getScriptenv(); ?>">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("action",	"save_new_tag");
					$token->field("tag_tag",$_SESSION['tags']['search']);
					$token->field("tag_private");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<input type="hidden" name="action" value="save_new_tag" />
				<input type="hidden" name="tag_tag" value="<? echo $_SESSION['tags']['search']; ?>" />
				<span>
					<? echo $_SESSION['cste']['_PRIVATE']; ?> :
				</span>
				<input type="checkbox" value="1" name="tag_private" />
				<input type="submit" value="Ajouter le nouveau tag" />
			</form>
		</div>
	</div>
</div>
