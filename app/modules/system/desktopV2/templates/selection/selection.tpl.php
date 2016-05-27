<?php
$content = "";
foreach(selection_categ::getCategories() as $categ){
	if (count($categ->getElements()) > 0){
		$content .= "<li>
						<h3>
							".((isset($_SESSION['cste'][$categ->fields['label']]))?$_SESSION['cste'][$categ->fields['label']]:$categ->fields['label']);

							if(!$categ->fields['is_default']) {
								$content .= '
								<a href="Javascript: void(0);" onclick="if(confirm(\''.addslashes($_SESSION['cste']['_DIMS_CONFIRM']).'\')) document.location.href = \'?dims_op=desktopv2&action=delete_selection_categ&id_selcateg='.$categ->getId().'&from=concept&desktop=1\';">
									<img src="'._DESKTOP_TPL_PATH.'/gfx/common/close.png" />
								</a>';
							}
							$content .= '
							<a href="Javascript: void(0);" onclick="Javascript: document.location.href = \'?dims_op=desktopv2&action=selectionVcard&id_selcateg='.$categ->getId().'\';">
								<img src="'._DESKTOP_TPL_PATH.'/gfx/common/export_vcard.png" />
							</a>
							<a href="Javascript: void(0);" onclick="Javascript: document.location.href = \'?dims_op=desktopv2&action=exportSelectionExcel&id_selcateg='.$categ->getId().'\';">
								<img src="'._DESKTOP_TPL_PATH.'/gfx/common/export_excel.png" />
							</a>

							<img src="'._DESKTOP_TPL_PATH.'/gfx/common/'.((isset($_SESSION['desktopV2']['content_droite']['list_selection_'.$categ->getId()]) && $_SESSION['desktopV2']['content_droite']['list_selection_'.$categ->getId()] == 0) ? 'deplier_menu.png' : 'replier_menu.png').'" border="0" onclick="javascript:$(\'.list_selection_'.$categ->getId().'\').slideToggle(\'fast\',flip_flop($(\'.list_selection_'.$categ->getId().'\'),$(this),\''._DESKTOP_TPL_PATH.'\'));" />
						</h3>
						<ul class="list_selection_'.$categ->getId().'" '.((isset($_SESSION['desktopV2']['content_droite']['list_selection_'.$categ->getId()]) && $_SESSION['desktopV2']['content_droite']['list_selection_'.$categ->getId()] == 0) ? 'style="display:none;"' : '').'>';
                                                        /*
                                                        <a href="Javascript: void(0);" onclick="Javascript: document.location.href = \'?dims_op=desktopv2&action=tagSelection&id_selcateg='.$categ->getId().'\';">
								<img src="'._DESKTOP_TPL_PATH.'/gfx/common/tag_vide.png" />
							</a>*/
		foreach($categ->getElements() as $sel){
			$content .= '<li>';
			switch($sel->fields['type_object']){
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$obj = new contact();
					$obj->openWithGB($sel->fields['id_globalobject']);
					$content .= "<a href=\"/admin.php?submenu="._DESKTOP_V2_CONCEPTS."&id=".$obj->fields['id']."&type=".dims_const::_SYSTEM_OBJECT_CONTACT."&init_filters=1&from=address_book\">".$obj->fields['firstname']." ".$obj->fields['lastname']."</a>";
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$obj = new tiers();
					$obj->openWithGB($sel->fields['id_globalobject']);
					$content .= "<a href=\"/admin.php?submenu="._DESKTOP_V2_CONCEPTS."&id=".$obj->fields['id']."&type=".dims_const::_SYSTEM_OBJECT_TIERS."&init_filters=1&from=address_book\">".$obj->fields['intitule']."</a>";
					break;
			}
			$content .= '<a class="actions" href="Javascript: void(0);" onclick="if(confirm(\''.addslashes($_SESSION['cste']['_DIMS_CONFIRM']).'\')) { dims_xmlhttprequest(\''.$dims->getScriptEnv().'\', \'dims_op=desktopv2&action=delete_selection&id_selcateg='.$categ->getId().'&idgo_elem='.$sel->fields['id_globalobject'].'&from=concept&desktop=1&ajax=1\'); $(this).closest(\'li\').remove()}"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close.png"/></a></li>';
		}
		$content .= '</ul>
				</li>';
	}
}
if ($content != ''){
	?>
	<div class="title_shortcuts">
		<h2 class="shortcuts_h2" style="float:left"><?php echo $_SESSION['cste']['_FORM_SELECTION']; ?></h2>
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_selections']) && $_SESSION['desktopV2']['content_droite']['zone_selections'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_selections').slideToggle('fast',flip_flop($('div.zone_selections'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
	</div>
	<div class="zone_selections" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_selections']) && $_SESSION['desktopV2']['content_droite']['zone_selections'] == 0) echo 'style="display:none;"'; ?>>
		<ul>
		<?
		echo $content;
		?>
		</ul>
	</div>
	<?
}
?>
