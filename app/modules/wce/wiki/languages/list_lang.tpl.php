<h4><? echo $_SESSION['cste']['_LIST_LANGUAGES']; ?></h4>
<?
$lstLang = wce_lang::getInstance()->getAll();
?>
<!--<div class="cadre_article">
	<a style="float: right;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU."&action=".module_wiki::_ACTION_EDIT_LANG); ?>">
		<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_add.png'); ?>" alt="<? echo $_SESSION['cste']['_CREATION_LANGUAGE']; ?>" title="<? echo $_SESSION['cste']['_CREATION_LANGUAGE']; ?>" />
		<? echo $_SESSION['cste']['_CREATION_LANGUAGE']; ?>
	</a>
</div>-->
<div class="table_article">
	<table cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th>
				<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_FLAG']; ?>
			</th>
			<th>
				<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
			</th>
		</tr>
		<?
		foreach(lang::getAllLanguageActiv() as $lang){
			tag::addTradLang($lang->fields['ref']);
			?>
			<tr>
				<td>
					<? echo $lang->getLabel(); ?>
				</td>
				<td>
					<?
					if(!is_null($flag = $lang->getFlag())){
						?>
						<img src="<? echo $flag; ?>" />
						<?
					}else{
						echo $_SESSION['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
					}
					?>
				</td>
				<td>
					<img onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU."&action=".module_wiki::_ACTION_EDIT_LANG."&id=".$lang->fields['id']); ?>';" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_edit.png'); ?>" />
					<?
					$action = module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU."&action=".module_wiki::_ACTION_SWITCH_ACTIVE."&id=".$lang->fields['id']);
					if (wce_lang::isActive($lang->fields['id'])){
					?>
					<img onclick="javascript:document.location.href='<? echo $action; ?>';" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_DISABLED']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_DISABLED']; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/deverouiller16.png'); ?>" />
					<?
					}else{
					?>
					<img onclick="javascript:document.location.href='<? echo $action; ?>';" alt="<? echo $_SESSION['cste']['_DIMS_ENABLED']; ?>" title="<? echo $_SESSION['cste']['_DIMS_ENABLED']; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/verouiller16.png'); ?>" />
					<?
					}
					?>
				</td>
			</tr>
			<?
		}
		?>
	</table>
</div>
<h4><? echo $_SESSION['cste']['_SMILE_LIST_TAGS']; ?></h4>
<div class="table_article">
	<table cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th>
				<? echo $_SESSION['cste']['_BUSINESS_FIELD_DEFAULTVALUE']; ?>
			</th>
			<?
			foreach($lstLang as $lang){
				?>
				<th>
					<? echo $lang->getLabel(); ?>
				</th>
				<?
			}
			?>
			<th>
				<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
			</th>
		</tr>
		<?
		foreach(tag::getAllTags(true, '', ' WHERE id_workspace = '.$_SESSION['dims']['workspaceid'], 'order by t.tag') as $tag){
			?>
			<tr>
				<td>
					<? echo $tag->fields['tag']; ?>
				</td>
				<?
				foreach($lstLang as $lang){
					if (isset($tag->fields['tag_'.$lang->fields['ref']]) && $tag->fields['tag_'.$lang->fields['ref']] != ''){
						?>
						<td>
						<?
						echo $tag->fields['tag_'.$lang->fields['ref']];
					}else{
						?>
						<td style="text-align: center;">
						<?
						echo '-';
					}
					?>
					</td>
					<?
				}
				?>
				<td>
					<img onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU."&action=".module_wiki::_ACTION_EDIT_TAG."&id=".$tag->fields['id']); ?>';" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_edit.png'); ?>" />
				</td>
			</tr>
			<?
		}
		?>
	</table>
</div>