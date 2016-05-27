<link href="/common/css/bootstrap.min.css?<?= module_wce::STATIC_FILES_VERSION; ?>" rel="stylesheet" type="text/css">
<div class="title_h2">
	<img src="<?= module_wce::getTemplateWebPath('gfx/icon_gest_article.png'); ?>">
	<h2><?= $_SESSION['cste']['_LIST_OF_ARTICLES']; ?></h2>
</div>
<div style="margin-left:20px;margin-right:20px;">
	<div class="cadre_article">
		<?
		$init = dims_load_securvalue('init',dims_const::_DIMS_NUM_INPUT,true,true);
		if($init)
			unset($_SESSION['wce']['lst_article']);

		//récupération des filtres
		if(!isset($_SESSION['wce']['lst_article']['filters']['status']) ) $_SESSION['wce']['lst_article']['filters']['status'] = -1;
		$f_status = dims_load_securvalue('status', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['status'], -1);

		if(!isset($_SESSION['wce']['lst_article']['filters']['creator']) ) $_SESSION['wce']['lst_article']['filters']['creator'] = -1;
		$f_creator = dims_load_securvalue('creator', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['creator'], -1);

		if(!isset($_SESSION['wce']['lst_article']['filters']['date_from']) ) $_SESSION['wce']['lst_article']['filters']['date_from'] = -1;
		$f_date_from = dims_load_securvalue('date_from', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['date_from'], -1, true);

		if(!isset($_SESSION['wce']['lst_article']['filters']['date_to']) ) $_SESSION['wce']['lst_article']['filters']['date_to'] = -1;
		$f_date_to = dims_load_securvalue('date_to', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['date_to'], -1, true);

		if(!isset($_SESSION['wce']['lst_article']['filters']['select_lang']) || ! isset($_POST['select_lang']) ) $_SESSION['wce']['lst_article']['filters']['select_lang'] = -1;
		$f_not_in_lang = dims_load_securvalue('select_lang', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['select_lang'], -1, true);

		if(!isset($_SESSION['wce']['lst_article']['filters']['keywords']) ) $_SESSION['wce']['lst_article']['filters']['keywords'] = -1;
		$f_keywords = dims_load_securvalue('keywords', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['keywords'], -1, true);

		if(!isset($_SESSION['wce']['lst_article']['filters']['include_content']) || ! isset($_POST['include_content']) ) $_SESSION['wce']['lst_article']['filters']['include_content'] = -1;
		$f_include_content = dims_load_securvalue('include_content', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['wce']['lst_article']['filters']['include_content'], -1, true);

		//gestion des tags
		require_once DIMS_APP_PATH.'modules/system/class_tag.php';
		if(!isset($_SESSION['wce']['lst_article']['filters']['tags']) || ! isset($_POST['tags']) ) $_SESSION['wce']['lst_article']['filters']['tags'] = $f_tags = array();
		if(!empty($_POST['tags'])){
			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$f_tags = $_SESSION['wce']['lst_article']['filters']['tags'] = tag::getNamesFor($tags);
		}

		//include module_wce::getTemplatePath('/list_articles/filtre_categorie.tpl.php');
		?>
		<form name="form_filter_articles" id="form_filter_articles" method="POST" action="<?= module_wce::get_url(module_wce::_SUB_LIST); ?>">
		<?php
		include module_wce::getTemplatePath('/list_articles/filtre_filtres.tpl.php');
		include module_wce::getTemplatePath('/list_articles/filtre_tags.tpl.php');
		?>
		</form>
		<?php
		//$heading = module_wiki::getRootHeading();

		//die();
		//traitement particulier sur les dates
		if(!empty($f_date_from) && $f_date_from != -1){
			$f_date_from = substr($f_date_from, 6) . substr($f_date_from, 3, 2) . substr($f_date_from, 0, 2) . '000000';
		}

		if( !empty($f_date_to) && $f_date_to != -1){
			$f_date_to = substr($f_date_to, 6) . substr($f_date_to, 3, 2) . substr($f_date_to, 0, 2) . '000000';
		}
		$lstArt = wce_article::getArticles($f_status, $f_creator, $f_date_from, $f_date_to, $f_not_in_lang, $f_keywords, $f_include_content, $f_tags);
		//$lstArt = $heading->getArticles($_SESSION['wiki']['lst_article']['categ'], $f_status, $f_creator, $f_date_from, $f_date_to, $f_not_in_lang, $f_keywords, $f_include_content, $f_tags);

		$lstCateg = $lstLang = array();
		$lstHeadings =  wce_heading::constructArianeHeadingsWCE();
		?>
	</div>

	<?php
	if( ! empty($lstArt) ){
		?>
	<div class="table_article">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="table_article_title">
						<? echo $_SESSION['cste']['_ARTICLE']; ?>
					</th>
					<th class="table_article_title" style="min-width:50px;width:50px;">
						<? echo $_SESSION['cste']['_VERSIONS']; ?>
					</th>
					<th class="table_article_title">
						<? echo $_SESSION['cste']['_RSS_LABEL_CATEGORY']; ?>
					</th>
					<th class="table_article_title">
						<? echo $_SESSION['cste']['_AUTHOR']; ?>
					</th>
					<th class="table_article_title">
						<? echo $_SESSION['cste']['_MODIFIED_AT_MASC']; ?>
					</th>
					<th class="table_article_title" style="width:50px;">
						<? echo $_SESSION['cste']['_STATE']; ?>
					</th>
					<th class="table_article_title" style="width:75px;">
						<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?
				foreach($lstArt as $article){
					?>
					<tr>
						<td class="table_article_text">
							<a class="lien_bleu" href="<?= module_wce::get_url(module_wce::_SUB_SITE); ?>&sub=<?= module_wce::_SITE_PREVIEW; ?>&headingid=<?= $article->fields['id_heading']; ?>&articleid=<?= $article->fields['id']; ?>"><?= $article->fields['title']; ?></a>
						</td>
						<td class="table_article_text" style="text-align:center;">
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
							<?= isset($lstHeadings[$article->get('id_heading')])?$lstHeadings[$article->get('id_heading')]:$_SESSION['cste']['_NEWS_LABEL_UNKNOWN']; ?>
							<?
							// TODO : ici les headings
							/*$lst = $article->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
							if (count($lst) > 0){
								$currGo = current($lst);
								if (!isset($lstCateg[$currGo])){
									$par = new category();
									$par->openWithGB($currGo);
									$lstCateg[$currGo] = $par;
								}
								echo $lstCateg[$currGo]->fields['label'];
							}else
								echo $_SESSION['cste']['_NEWS_LABEL_UNKNOWN'];*/
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
						<td class="table_article_text center" style="text-align:center;">
							<?
							if ( ! $article->isUptodate()) {
								echo '<img src="'.module_wce::getTemplateWebPath('/gfx/puce_orange.png').'" title="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" alt="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" />';
							}
							else {
								echo '<img src="'.module_wce::getTemplateWebPath('/gfx/puce_verte.png').'" title="'.$_SESSION['cste']['UP_TO_DATE'].'" alt="'.$_SESSION['cste']['UP_TO_DATE'].'" />';
							}
							?>
						</td>
						<td class="table_article_text center" style="text-align:center;">
							<a href="<?= module_wce::get_url(module_wce::_SUB_SITE); ?>&sub=<?= module_wce::_SITE_PREVIEW; ?>&headingid=<?= $article->fields['id_heading']; ?>&articleid=<?= $article->fields['id']; ?>"><img src="<?= module_wce::getTemplateWebPath('gfx/icone_ouvrir.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_OPEN']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_OPEN']; ?>" /></a>
							<a href="<?= module_wce::get_url(module_wce::_SUB_SITE); ?>&sub=<?= module_wce::_SITE_PREVIEW; ?>&action=edit_art&headingid=<?= $article->fields['id_heading']; ?>&articleid=<?= $article->fields['id']; ?>"><img src="<?= module_wce::getTemplateWebPath('gfx/icon_mini_modif.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" /></a>
							<?
							/*if ($article->fields['id']>0 && (dims_isactionallowed(0) || $article->fields['id_user']==$_SESSION['dims']['userid'])){
							?>
								<img onclick="<? echo "javascript:dims_confirmlink('/admin.php?dims_op=wiki&op_wiki=articlewiki_delete&id_article=".$article->fields['id']."','Etes-vous certain de vouloir supprimer l\'article &laquo; ".addslashes($article->fields['title'])." &raquo; ?')"; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_suppression.png'); ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" alt="<? echo $_SESSION['cste']['_DELETE']; ?>" />
							<?
							}*/
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
</div>
