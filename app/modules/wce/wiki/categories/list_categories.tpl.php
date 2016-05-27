<?php
if(!isset($_SESSION['wiki']['categ']['page']))
    $_SESSION['wiki']['categ']['page'] = 0;
if (!isset($_SESSION['wiki']['categ']['search_text']))
	$_SESSION['wiki']['categ']['search_text'] = "";
$page = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wiki']['categ']['page'], 0, true);
$search_text = dims_load_securvalue('search_text', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wiki']['categ']['search_text'], "", true);
$categ = module_wiki::getCategRoot();
$categ->page_courant = $page;
$categ->setPaginationParams(8, 10, false, $_SESSION['cste']['PAGINATION_FIRST'], $_SESSION['cste']['_LAST'], $_SESSION['cste']['_PREVIOUS_FEM'], $_SESSION['cste']['_NEXT_FEM']);
$categories = $categ->getAll($search_text);
$pages = $categ->getPagination();
$lstUsers = array();
?>
<h4><? echo $_SESSION['cste']['_CAT_LIST']; ?></h4>
<div class="cadre_article">
	<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES); ?>">
		<label>
			<? echo $_SESSION['cste']['_WCE_KEYWORDS_META']; ?>&nbsp;:&nbsp;
		</label>
		<input type="text" value="<? echo $search_text; ?>" name="search_text" />
		<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_FILTER']; ?>" />
		<a style="float: right;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES."&action=".module_wiki::_ACTION_EDIT_CATEG); ?>">
			<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_add.png'); ?>" alt="<? echo $_SESSION['cste']['_NEW_CATEGORY']; ?>" title="<? echo $_SESSION['cste']['_NEW_CATEGORY']; ?>" />
			<? echo $_SESSION['cste']['_NEW_CATEGORY']; ?>
		</a>
	</form>
</div>
<div class="table_article">
	<table cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th><?php echo $_SESSION['cste']['_DIMS_LABEL']; ?></th>
			<th><?php echo $_SESSION['cste']['CREATED_BY']; ?></th>
			<th><?php echo $_SESSION['cste']['_SYSTEM_LABEL_FICHCREATED']; ?></th>
			<th width="60px"><?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?></th>
		</tr>
		<?
		if (count($categories) > 0){
			foreach($categories as $categ){
				?>
				<tr>
					<td>
						<? echo $categ->getArianeNoRoot(); ?>
					</td>
					<td>
						<?
						if (!isset($lstUsers[$categ->fields['id_user']])){
							$lstUsers[$categ->fields['id_user']] = new user();
							$lstUsers[$categ->fields['id_user']]->open($categ->fields['id_user']);
						}
						echo $lstUsers[$categ->fields['id_user']]->fields['firstname']." ".$lstUsers[$categ->fields['id_user']]->fields['lastname'];
						?>
					</td>
					<td>
						<?
						$d = dims_timestamp2local($categ->fields['timestp_create']);
						echo $d['date'];
						?>
					</td>
					<td>
						<img onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES."&action=".module_wiki::_ACTION_EDIT_CATEG."&id=".$categ->fields['id']); ?>';" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_edit.png'); ?>" atl="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
						<img onclick="javascript:dims_confirmlink('<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES."&action=".module_wiki::_ACTION_DEL_CATEG."&id=".$categ->fields['id']); ?>','<? echo $_SESSION['cste']['_RSSCAT_LABEL_DELETE_CONFIRM']; ?>');" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_mini_suppr.png'); ?>" atl="<? echo $_SESSION['cste']['_DELETE']; ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" />
					</td>
				</tr>
				<?
			}
		}else{
			?>
			<tr>
				<td colspan="4">
					<? echo $_SESSION['cste']['_LIST_NO_CATEGORY_ASSET']; ?>
				</td>
			</tr>
			<?
		}
		?>
	</table>
	<? if (count($categories) > 0){ ?>
	<div id="liens_pagination">
		<span><? echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
		<?
		if(count($pages) > 1) {
			foreach($pages as $k=>$p){
				if(!empty($p['url'])) {
					echo '<a href="';
					echo $p['url'];
					echo '" title="';
					echo $p['title'];
					echo '">';
					echo $p['label'];
					echo '</a>';
				} else {
					echo '<span class="current">';
					echo $p['label'];
					echo '</span>';
				}
			}
		}else{
			echo '<span class="current">1</span>';
		}
		?>
	</div>
	<? } ?>
</div>