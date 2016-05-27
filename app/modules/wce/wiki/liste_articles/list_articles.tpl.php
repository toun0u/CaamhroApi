<h4><? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?></h4>
<div class="cadre_article">
	<?
	$init = dims_load_securvalue('init',dims_const::_DIMS_NUM_INPUT,true,true);
	if($init)
		unset($_SESSION['wiki']['lst_article']);

	//récupération des filtres
	if(!isset($_SESSION['wiki']['lst_article']['filters']['status']) ) $_SESSION['wiki']['lst_article']['filters']['status'] = -1;
	$f_status = dims_load_securvalue('status', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['status'], -1);

	if(!isset($_SESSION['wiki']['lst_article']['filters']['creator']) ) $_SESSION['wiki']['lst_article']['filters']['creator'] = -1;
	$f_creator = dims_load_securvalue('creator', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['creator'], -1);

	if(!isset($_SESSION['wiki']['lst_article']['filters']['date_from']) ) $_SESSION['wiki']['lst_article']['filters']['date_from'] = -1;
	$f_date_from = dims_load_securvalue('date_from', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['date_from'], -1, true);

	if(!isset($_SESSION['wiki']['lst_article']['filters']['date_to']) ) $_SESSION['wiki']['lst_article']['filters']['date_to'] = -1;
	$f_date_to = dims_load_securvalue('date_to', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['date_to'], -1, true);

	if(!isset($_SESSION['wiki']['lst_article']['filters']['select_lang']) || ! isset($_GET['select_lang']) ) $_SESSION['wiki']['lst_article']['filters']['select_lang'] = -1;
	$f_not_in_lang = dims_load_securvalue('select_lang', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['select_lang'], -1, true);

	if(!isset($_SESSION['wiki']['lst_article']['filters']['keywords']) ) $_SESSION['wiki']['lst_article']['filters']['keywords'] = -1;
	$f_keywords = dims_load_securvalue('keywords', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['keywords'], -1, true);

	if(!isset($_SESSION['wiki']['lst_article']['filters']['include_content']) || ! isset($_GET['include_content']) ) $_SESSION['wiki']['lst_article']['filters']['include_content'] = -1;
	$f_include_content = dims_load_securvalue('include_content', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wiki']['lst_article']['filters']['include_content'], -1, true);

	//gestion des tags
	if(!isset($_SESSION['wiki']['lst_article']['filters']['tags']) || ! isset($_GET['tags']) ) $_SESSION['wiki']['lst_article']['filters']['tags'] = $f_tags = array();
	if(!empty($_GET['tags'])){
		$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$f_tags = $_SESSION['wiki']['lst_article']['filters']['tags'] = tag::getNamesFor($tags);
	}


	include module_wiki::getTemplatePath('/liste_articles/filtre_categorie.tpl.php');
	?>
	<form name="form_filter_articles" id="form_filter_articles" method="get" action="<?php echo dims::getInstance()->getScriptEnv(); ?>">
	<?php
	include module_wiki::getTemplatePath('/liste_articles/filtre_filtres.tpl.php');
	include module_wiki::getTemplatePath('/liste_articles/filtre_tags.tpl.php');
	?>
	</form>
	<?php
	$heading = module_wiki::getRootHeading();

	//die();
	//traitement particulier sur les dates
	if(!empty($f_date_from) && $f_date_from != -1){
		$f_date_from = substr($f_date_from, 6) . substr($f_date_from, 3, 2) . substr($f_date_from, 0, 2) . '000000';
	}

	if( !empty($f_date_to) && $f_date_to != -1){
		$f_date_to = substr($f_date_to, 6) . substr($f_date_to, 3, 2) . substr($f_date_to, 0, 2) . '000000';
	}
	$lstArt = $heading->getArticles($_SESSION['wiki']['lst_article']['categ'], $f_status, $f_creator, $f_date_from, $f_date_to, $f_not_in_lang, $f_keywords, $f_include_content, $f_tags);

	$lstCateg = $lstLang = array();
	?>
</div>

<?php
if( ! empty($lstArt) ){
	?>
<div class="table_article">
	<table cellpadding="0" cellspacing="0" border="1">
		<tbody>
			<tr>
				<td class="table_article_title">
					<? echo $_SESSION['cste']['_ARTICLE']; ?>
				</td>
				<td class="table_article_title">
					<? echo $_SESSION['cste']['_VERSIONS']; ?>
				</td>
				<td class="table_article_title">
					<? echo $_SESSION['cste']['_RSS_LABEL_CATEGORY']; ?>
				</td>
				<td class="table_article_title">
					<? echo $_SESSION['cste']['_AUTHOR']; ?>
				</td>
				<td class="table_article_title">
					<? echo $_SESSION['cste']['_MODIFIED_AT_MASC']; ?>
				</td>
				<td class="table_article_title">
					<? echo $_SESSION['cste']['_STATE']; ?>
				</td>
				<td class="table_article_title" colspan="2">
					<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</td>
			</tr>
			<?
			foreach($lstArt as $article){
				?>
				<tr>
					<td class="table_article_text">
						<a class="lien_bleu" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=".$article->fields['id']."&wce_mode=edit"); ?>"><? echo $article->fields['title']; ?></a>
					</td>
					<td class="table_article_text">
						<?
						foreach($article->getListArticleLangVersion() as $lang){
							$url = $lang->getFlag();
							if (!is_null($url)){
								?>
								<img src="<? echo $url; ?>" title="<? echo $lang->getLabel(); ?>" alt="<? echo $lang->getLabel(); ?>" />
								<?
							}
						}
						?>
					</td>
					<td class="table_article_text">
						<a class="lien_bleu" href="#"></a>
						<?
						$lst = $article->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
						if (count($lst) > 0){
							$currGo = current($lst);
							if (!isset($lstCateg[$currGo])){
								$par = new category();
								$par->openWithGB($currGo);
								$lstCateg[$currGo] = $par;
							}
							echo $lstCateg[$currGo]->fields['label'];
						}else
							echo $_SESSION['cste']['_NEWS_LABEL_UNKNOWN'];
						?>
					</td>
					<td class="table_article_text">
						<span class="maj_par" href="#">
							<?
							$user = new user();
							$user->open($article->fields['id_user']);
							echo $user->fields['firstname']." ".$user->fields['lastname'];
							?>
						</a>
					</td>
					<td class="table_article_text">
						<?
						$dd = dims_timestamp2local($article->fields['timestp_modify']);
						echo $dd['date'];
						?>
					</td>
					<td class="table_article_text center">
						<?
						if ( ! $article->isUptodate()) {
							echo '<img src="'.module_wiki::getTemplateWebPath('/gfx/puce_orange.png').'" title="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" alt="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" />';
						}
						else {
							echo '<img src="'.module_wiki::getTemplateWebPath('/gfx/puce_verte.png').'" title="'.$_SESSION['cste']['UP_TO_DATE'].'" alt="'.$_SESSION['cste']['UP_TO_DATE'].'" />';
						}
						?>
					</td>
					<td class="table_article_text center">
						<img onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=".$article->fields['id']."&wce_mode=edit"); ?>';" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_ouvrir.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_OPEN']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_OPEN']; ?>" />
						<?
						if ($article->fields['id']>0 && (dims_isactionallowed(0) || $article->fields['id_user']==$_SESSION['dims']['userid']))
						?>
							<img onclick="<? echo "javascript:dims_confirmlink('/admin.php?dims_op=wiki&op_wiki=articlewiki_delete&id_article=".$article->fields['id']."','Etes-vous certain de vouloir supprimer l\'article &laquo; ".addslashes($article->fields['title'])." &raquo; ?')"; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_suppression.png'); ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" alt="<? echo $_SESSION['cste']['_DELETE']; ?>" />
						<?
						?>
					</td>
				</tr>
				<?
			}
			?>
		</tbody>
	</table>
</div>
<?php
}
else{
	?>
	<div class="div_no_elem">
		<?= $_SESSION['cste']['NO_ELEM_WITH_CRITERIA']; ?>
	</div>
	<?php
}
?>

