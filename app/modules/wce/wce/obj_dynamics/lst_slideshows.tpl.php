<div class="title_h2">
	<img style="height: 39px;" src="<? echo module_wce::getTemplateWebPath('/gfx/slideshow_48.png'); ?>">
	<h2>
		Slideshows
	</h2>
	<div class="actions">
		<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_EDIT; ?>">
			<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_add_obj_dynamique.png'); ?>" alt="<? echo $_SESSION['cste']['_ADD_OBJECT']; ?>" title="<? echo $_SESSION['cste']['_ADD_OBJECT']; ?>" />
		</a>
	</div>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;margin-bottom: 30px;">
	<tr>
		<td class="title_table_accueil">
			Nom du slideshow
		</td>
		<td class="title_table">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$a_slideshows=$wce_site->getSlideShows();
	if (empty($a_slideshows)){
		?>
		<tr>
			<td colspan="2">
				Aucun slideshow présent
			</td>
		</tr>
		<?
	}else{
		foreach($a_slideshows as $slideshow){
			?>
			<tr>
				<td>
					<? echo $slideshow->fields['nom']; ?>
				</td>
				<td class="actions">
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_EDIT."&id=".$slideshow->fields['id']; ?>">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_modif.png'); ?>" />
					</a>
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_VIEW."&id=".$slideshow->fields['id']; ?>">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_voir.png'); ?>" />
					</a>
					<a onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_DEL."&id=".$slideshow->fields['id']; ?>','<? echo $_SESSION['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']; ?>');" href="javascript:void(0);">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_suppr.png'); ?>" />
					</a>
				</td>
			</tr>
			<?
		}
	}
	?>
</table>