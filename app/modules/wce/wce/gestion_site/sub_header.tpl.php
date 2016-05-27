<?
$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
if (empty($headingid)){
	$lstHeadings = wce_heading::getAllHeadings();
	$heading = current($lstHeadings);
	$headingid = $heading->fields['id'];
}else{
	$heading = new wce_heading();
	$heading->open($headingid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
}
$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
?>
<div class="title_h2">
	<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_gest_site.png'); ?>">
	<h2>
		<? echo $_SESSION['cste']['_SITE_MANAGEMENT']; ?>&nbsp;>&nbsp;
		<?
		if($articleid != '' && $articleid > 0){
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			$lstLangDipo = $article->getListArticleLangVersionWCE();
			echo $_SESSION['cste']['_ARTICLE']." : ".$article->fields['title'];
			?>
			<img style="float:none;" src="<? echo module_wce::getTemplateWebPath('gfx/puce_'.((!$article->isUptodate())?"orange":"verte").'.png'); ?>" />
			<?
		}else{
			$lstLangDipo = $heading->getListLang();
			echo (($heading->fields['id_heading'] == 0)?$_SESSION['cste']['_DOC_ROOT']:$_SESSION['cste']['_RUBRIC'])." : ".$heading->fields['label'];
		}
		?>
	</h2>
	<div class="actions">
		<table>
			<tr>
				<?
				if($articleid != '' && $articleid > 0){
					$user = new user();
					if (isset($article->fields['id_user']) && $article->fields['id_user']>0) {
						$user->open($article->fields['id_user']);
						if(isset($user->fields['id_contact'])){
							$contactadd = new contact();
							$contactadd->open($user->fields['id_contact']);
							?>
							<td>
								<div class="zone_picture_date">
									<div class="picture">
									<?
									if (isset($contactadd) && ($contactadd->getPhotoWebPath(60) != '' && file_exists($contactadd->getPhotoPath(60))))
										echo '<img class="ab_desc_image" src="'.$contactadd->getPhotoWebPath(60).'" border="0" title="picture" alt="picture" />';
									else
										echo '<img class="ab_desc_image" src="'.module_wce::getTemplateWebPath('/gfx/human40.png').'" border="0" title="picture" alt="picture" />';
									?>
									</div>
									<div class="date_depose">
										<p>
											<?
											$dd = dims_timestamp2local($article->fields['timestp']);
											echo $_SESSION['cste']['_SYSTEM_LABEL_FICHCREATED']." ".$dd['date']." - ".$dd['time'];
											?>
										</p>
										<p class="par">
											<? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?>
											<span class="maj_par"><? echo $user->fields['firstname']." ".$user->fields['lastname']; ?></span>
										</p>
									</div>
								</div>
							</td>
							<?
						}
					}
					?>
					<td>
						<?php
						if ( ! $article->isUptodate()) {
							?>
							<div style="float: left; text-align: center; margin-right: 15px;">
								<img id="icon_valid_article" onclick="javascript:document.location.href='<?php echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_VALID_ARTICLE."&articleid=".$article->fields['id']."&headingid=".$article->fields['id_heading']."&lang=".$article->fields['id_lang']; ?>';" style="cursor:pointer;" src="<? echo module_wce::getTemplateWebPath('gfx/icon_validation.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_FAQ_NOTPUBLISHED']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_FAQ_NOTPUBLISHED']; ?>" />
								<div><? echo $_SESSION['cste']['_DIMS_FAQ_NOTPUBLISHED']; ?></div>
							</div>
							<?
						}else{
							?>
							<div style="float: left; text-align: center; margin-right: 15px;">
								<img id="icon_valid_article" style="cursor:pointer;" src="<? echo module_wce::getTemplateWebPath('gfx/icon_validation_dis.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_FAQ_PUBLISHED']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_FAQ_PUBLISHED']; ?>" />
								<div style="padding-top: 28px;"><? echo $_SESSION['cste']['_DIMS_FAQ_PUBLISHED']; ?></div>
							</div>
							<?
						}
						?>
					</td>
					<td>
						<?php
						if (dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true) == module_wce::_PREVIEW_EDIT){
							?>
							<div style="float: left; text-align: center; margin-right: 15px;">
								<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&articleid=".$article->fields['id']."&headingid=".$article->fields['id_heading']; ?>">
									<img alt="<? echo $_SESSION['cste']['_PREVIEW']; ?>" title="<? echo $_SESSION['cste']['_PREVIEW']; ?>" src="<? echo module_wce::getTemplateWebPath('gfx/icon_preview.png'); ?>" />
									<div><? echo $_SESSION['cste']['_PREVIEW']; ?></div>
								</a>
							</div>
							<?
						}else{
							?>
							<div style="float: left; text-align: center;">
								<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&articleid=".$article->fields['id']."&headingid=".$article->fields['id_heading']; ?>">
									<img alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" src="<? echo module_wce::getTemplateWebPath('gfx/icone_article.png'); ?>" />
									<div><? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></div>
								</a>
							</div>
							<?
						}
						?>
					</td>
					<?php
				}else{
					if($heading->fields['id_heading'] > 0 && $heading->fields['id_user'] != '' && $heading->fields['id_user'] > 0){
						$user = new user();
						$user->open($heading->fields['id_user']);
						if(isset($user->fields['id_contact'])){
							$contactadd = new contact();
							$contactadd->open($user->fields['id_contact']);
							?>
							<td>
								<div class="zone_picture_date">
									<div class="picture">
									<?
									if (isset($contactadd) && ($contactadd->getPhotoWebPath(60) != '' && file_exists($contactadd->getPhotoPath(60))))
										echo '<img class="ab_desc_image" src="'.$contactadd->getPhotoWebPath(60).'" border="0" title="picture" alt="picture" />';
									else
										echo '<img class="ab_desc_image" src="'.module_wce::getTemplateWebPath('/gfx/human40.png').'" border="0" title="picture" alt="picture" />';
									?>
									</div>
									<div class="date_depose">
										<p>
											<?
											$dd = dims_timestamp2local($heading->fields['timestp_modify']);
											echo $_SESSION['cste']['_SYSTEM_LABEL_FICHCREATED']." ".$dd['date']." - ".$dd['time'];
											?>
										</p>
										<p class="par">
											<? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?>
											<span class="maj_par"><? echo $user->fields['firstname']." ".$user->fields['lastname']; ?></span>
										</p>
									</div>
								</div>
							</td>
							<?
						}
					}
					?>
					<td>
						<div style="float: left; text-align: center; margin-right: 15px;">
							<a href="javascript:void(0);" onclick="javascript: propertiesArticleWce(<? echo $heading->fields['id']; ?>);">
								<img src="<? echo module_wce::getTemplateWebPath('gfx/add_article.png'); ?>" style="float: none !important" alt="<? echo $_SESSION['cste']['_NEW_ARTICLE']; ?>" title="<? echo $_SESSION['cste']['_NEW_ARTICLE']; ?>" />
								<div><? echo $_SESSION['cste']['_NEW_ARTICLE']; ?></div>
							</a>
						</div>
					</td>
					<td>
						<div style="float: left; text-align: center; margin-right: 15px;">
							<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_ADD_HEAD."&headingid=".$heading->fields['id']; ?>">
								<img style="float: none !important" src="<? echo module_wce::getTemplateWebPath('/gfx/add_heading.png'); ?>" style="float: none !important" alt="<? echo $_SESSION['cste']['_ADD_REVIEW']; ?>" title="<? echo $_SESSION['cste']['_ADD_REVIEW']; ?>" />
								<div><? echo $_SESSION['cste']['_ADD_REVIEW']; ?></div>
							</a>
						</div>
					</td>
					<?
					if($heading->fields['id_heading'] == 0){
						?>
						<td>
							<div style="float: left; text-align: center;">
								<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_ADD_ROOT; ?>">
									<img style="float: none !important" src="<? echo module_wce::getTemplateWebPath('gfx/add_root.png'); ?>" alt="<? echo $_SESSION['cste']['_WCE_ADD_ROOT']; ?>" title="<? echo $_SESSION['cste']['_WCE_ADD_ROOT']; ?>" />
									<div><? echo $_SESSION['cste']['_WCE_ADD_ROOT']; ?></div>
								</a>
							</div>
						</td>
						<?php
					}
				}
				?>
			</tr>
		</table>
	</div>
</div>
<div class="sous_rubrique" style="float:right;">
	<ul>
		<li>
			<a <? echo ($_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_PROPERTIES)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PROPERTIES."&headingid=$headingid".((!empty($articleid))?"&articleid=$articleid":""); ?>">
				<? echo mb_strtoupper(($articleid != '' && $articleid > 0)?$_SESSION['cste']['_DIMS_PROPERTIES_ARTICLE']:(($heading->fields['id_heading']==0)?$_SESSION['cste']['_DIMS_PROPERTIES']." : ".$heading->fields['label']:$_SESSION['cste']['_DIMS_PROPERTIES_HEADING']), 'UTF-8'); ?>
			</a>
		</li>
		<li>
			<a <? echo ($_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_PREVIEW)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PREVIEW."&headingid=$headingid".((!empty($articleid))?"&articleid=$articleid":""); ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_PREVIEW'], 'UTF-8'); ?>
			</a>
		</li>
		<? if(!empty($articleid)){ ?>
		<li>
			<a <? echo ($_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_REF)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_REF."&headingid=$headingid"."&articleid=$articleid"; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_WCE_PAGE_REFER'], 'UTF-8'); ?>
			</a>
		</li>
		<!-- Sous-menu Mailing List -->
		<!--<li>
			<a <? echo ($_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_DIFF)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_DIFF."&headingid=$headingid"."&articleid=$articleid"; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_DIMS_LABEL_MAILINGLIST'], 'UTF-8'); ?>
			</a>
		</li>-->
		<? }else{ ?>
		<li>
			<a <? echo ($_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_LIST)?'class="selected"':""; ?> href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_LIST."&headingid=$headingid"; ?>">
				<? echo mb_strtoupper($_SESSION['cste']['_LIST_OF_ARTICLES'], 'UTF-8'); ?>
			</a>
		</li>
		<? } ?>
	</ul>
</div>
