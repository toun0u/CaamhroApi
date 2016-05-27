<div class="title_h3">
	<h3>Templates disponibles pour le site</h3>
</div>
<div class="lien_modification">
	<a onclick="javascript:displayTemplateInfoWce(event);" href="javascript:void(0);<? //echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_EDIT_TEMPL; ?>">
		Nouveau template
		<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_ajout.png'); ?>" />
	</a>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;margin-bottom: 30px;">
	<tr>
		<td class="title_table_accueil">
			<?=$_SESSION['cste']['_TEMPLATE_NAME']; ?>
		</td>
		<td class="title_table_defaut">
			<?= $_SESSION['cste']['_BUSINESS_FIELD_DEFAULTVALUE']; ?>
		</td>
		<?
		if(defined('_DISPLAY_WIKI') && _DISPLAY_WIKI){
		?>
		<td class="title_table_accueil">
			<?
			echo $_SESSION['cste']['_DEFAULT_TEMPLATE_WIKI'];
			require_once(DIMS_APP_PATH."modules/wce/wiki/include/class_module_wiki.php");
			$rootWiki = module_wiki::getRootHeading();
			?>
		</td>
		<?
		}
		?>
		<td class="title_table">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$availabletpl = dims_getavailabletemplates();
	$sql = "SELECT 	*
			FROM 	dims_workspace_template
			WHERE 	id_workspace = :id_workspace";
	$db = dims::getInstance()->db;
	$res = $db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
	$trl = 'class="table_ligne1"';
	while($r = $db->fetchrow($res)){
		if (in_array($r['template'], $availabletpl)) {
			?>
			<tr <? echo $trl; ?>>
				<td>
					<? echo $r['template']; ?>
				</td>
				<td class="puce">
					<?
					if($r['is_default']){
						?>
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/puce_verte.png'); ?>" />
						<?
					}else{
						?>
						<img onclick="javascript:document.location.href='<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_TEMPL_DEFAULT."&name=".$r['template']; ?>';" style="cursor:pointer;" src="<? echo module_wce::getTemplateWebPath('/gfx/puce_rouge.png'); ?>" />
						<?
					}
					?>
				<?
				if(defined('_DISPLAY_WIKI') && _DISPLAY_WIKI){
				?>
				<td class="puce">
					<?
					if($rootWiki->fields['template'] == $r['template']){
						?>
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/puce_verte.png'); ?>" />
						<?
					}else{
						?>
						<img onclick="javascript:document.location.href='<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_TEMPL_WIKI."&name=".$r['template']; ?>';" style="cursor:pointer;" src="<? echo module_wce::getTemplateWebPath('/gfx/puce_rouge.png'); ?>" />
						<?
					}
					?>
				</td>
				<?
				}
				?>
				<td class="actions">
					<a onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_DEL_TEMPL."&name=".$r['template']; ?>','<? echo $_SESSION['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']; ?>');" href="javascript:void(0);">
						<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_supp.png'); ?>" />
					</a>
				</td>
			</tr>
			<?
			$trl = ($trl == '')?'class="table_ligne1"':'';
		}
	}
	?>
</table>

<script type="text/javascript">
	function displayTemplateInfoWce(event){
		dims_showcenteredpopup("",500,500,'dims_popup');
		dims_xmlhttprequest_todiv('admin.php','dims_op=templates_view_wce','','dims_popup');
	}
</script>
