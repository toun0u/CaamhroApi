<!--<div class="subheader" id="menudesktopcollab">-->
<?
	require_once(DIMS_APP_PATH . "/modules/system/desktop_collaborative_menu.php");
?>
<!--</div>-->
<div id="content_onglet">
	<div id="menu_content_onglet">
	<?
		echo $skin->create_onglet($desktop_collab,$action,1,'0',"onglet");
	?>
	</div>
	<table cellpadding="10" cellspacing="0" style="width:100%;background:#FCFCFC">
		<tr>
			<td>
				<div class="contentdesktop" style="width:100%;" id="contentdesktopcollab">
				<?php
				$sumvalidate=0;
				$cur_moduleid=0;
				unset($_SESSION['dims']['current_ticket']);
				$array_modules=$dims->getModules($_SESSION['dims']['workspaceid']);

				switch($_SESSION['dims']['desktop_collab']) {
					case dims_const::_DIMS_CSTE_FAVORITE:
					case dims_const::_DIMS_CSTE_SURVEY:
						unset($_SESSION['dims']['current_ticket']);
						unset($_SESSION['dims']['current_object']);
						require_once(DIMS_APP_PATH . '/modules/system/desktop_collaborative_activefile.php');
						break;
					case dims_const::_DIMS_CSTE_TONEWS:
						unset($_SESSION['dims']['current_ticket']);
						//unset($_SESSION['dims']['current_object']);
						require_once(DIMS_APP_PATH . '/modules/system/desktop_collaborative_tonews.php');
						break;
				}
				?>
				</div>
			</td>
		</tr>
	</table>
</div>
