<?
if (!isset($_SESSION['tags']['search'])) $_SESSION['tags']['search'] = '';
?>
<div class="tags">
	<h2 class="h1_tags" style="float:left"><?php echo $_SESSION['cste']['TAGS___FILTERING_YOUR_SEARCH']; ?></h2>
	<?php
	//hack demandé par André Hansen, par défaut la pupuce rouge
	if(!isset($_SESSION['desktopV2']['content_droite']['zone_tag'])){
		$_SESSION['desktopV2']['content_droite']['zone_tag'] = 1;
	}
	?>
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_tag']) && $_SESSION['desktopV2']['content_droite']['zone_tag'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_tag').slideToggle('fast',flip_flop($('div.zone_tag'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
</div>
<div class="zone_tag" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_tag']) && $_SESSION['desktopV2']['content_droite']['zone_tag'] == 0) echo 'style="display:none;"'; ?>>
	<div id="zoneadd_tag" class="zoneadd_tag" >
		<a href="javascript:void(0)" onclick="javascript:switchCategTagEdit(0);"><?php echo $_SESSION['cste']['_AGENDA_CAT_CREATE']; ?> <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png"></a>
	</div>
	<div id="edit_categtag0" class="zoneadd_tagdetail"></div>
	<div class="searchform_tags">
		<span>
			<input type="image" class="button_search_tags" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche_petit.png" name="button_search" style="float:left">
			<input onkeyup="javascript:searchTags(this.value);" type="text" name="editbox_search" class="editbox_search_tags" id="editbox_search_tags" maxlength="80" value="<? if ($_SESSION['tags']['search'] == '') echo $_SESSION['cste']['LOOKING_FOR_A_TAG']; else echo $_SESSION['tags']['search']; ?>" onfocus="Javascript:if (this.value == '<? echo $_SESSION['cste']['LOOKING_FOR_A_TAG']; ?>') this.value='';" onblur="Javascript:if (this.value=='')this.value='<? echo $_SESSION['cste']['LOOKING_FOR_A_TAG']; ?>';">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite_petit.png" style="float:left">
		</span>
	</div>

	<div id="zone_tags">

		<?
		if ($_SESSION['tags']['search'] != '')
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_search.tpl.php';
		else{
		?>
		<div id="zone_recently">
			<?php
                        include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_recently.tpl.php';
                        ?>
		</div>
		<?php
		/*
		Demandé par André Hansen - on dégage
			<div id="zone_generic">
				<?php include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_generic.tpl.php'; ?>
			</div>
		<?
		*/
		}
		?>
	</div>
</div>
