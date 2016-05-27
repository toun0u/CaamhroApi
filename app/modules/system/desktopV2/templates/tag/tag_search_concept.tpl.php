<?php
$dims = dims::getInstance();
if (!isset($_SESSION['desktop']['display']['tags']['search']) || $_SESSION['desktop']['display']['tags']['search'] <= 0)
	$_SESSION['desktop']['display']['tags']['search'] = 1;

switch($type_fiche) {
	case dims_const::_SYSTEM_OBJECT_CONTACT:
		$fiche_concept = new contact();
		break;
	case dims_const::_SYSTEM_OBJECT_TIERS:
		$fiche_concept = new tiers();
		break;
}

$fiche_concept->open($id_fiche);

$list_current_tags = array();
foreach($fiche_concept->getMyTags() as $current_tag) {
	$list_current_tags[] = $current_tag->fields['id'];
}

$nbSearchTags = count($desktop->getSearchTags($_SESSION['tags']['search'], 0, 0, $list_current_tags));
$lstTags = $desktop->getSearchTags($_SESSION['tags']['search'],0,_DESKTOP_V2_LIMIT_TAGS*$_SESSION['desktop']['display']['tags']['search'], $list_current_tags);
?>
<div class="generic">
	<span><?php echo $_SESSION['cste']['_DIMS_SEARCH_RESULT'];?></span>
</div>
<div class="zone_generic">
	<?
	if ($nbSearchTags > 0){
		foreach ($lstTags as $tag){
			$tag->setLightAttribute('from_fiche', true);
			$tag->setLightAttribute('id_fiche', $id_fiche);
			$tag->setLightAttribute('type_fiche', $type_fiche);
			$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
		}
	}
	?>
	<div class="zone_generic_see_more">
		<div class="new_tag">
			<form method="POST" action="<? echo $dims->getScriptenv(); ?>">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("dims_op",	"desktopv2");
					$token->field("action",		"save_new_tag_concept");
					$token->field("tag_tag",	$_SESSION['tags']['search']);
					$token->field("id_fiche",	$id_fiche);
					$token->field("type_fiche",	$type_fiche);
					$token->field("tag_private");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<input type="hidden" name="dims_op" value="desktopv2" />
				<input type="hidden" name="action" value="save_new_tag_concept" />
				<input type="hidden" name="tag_tag" value="<? echo $_SESSION['tags']['search']; ?>" />
				<input type="hidden" name="id_fiche" value="<? echo $id_fiche; ?>" />
				<input type="hidden" name="type_fiche" value="<? echo $type_fiche; ?>" />
				<span>
					<? echo $_SESSION['cste']['_PRIVATE']; ?> :
				</span>
				<input type="checkbox" value="1" name="tag_private" />
				<input type="submit" value="Ajouter le nouveau tag" />
			</form>
		</div>
	</div>
</div>
