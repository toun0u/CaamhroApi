<div class="cadre_naviguer">
	<div class="title_naviguer">
		<div class="title_cadre"><? echo $_SESSION['cste']['_LAST_UPDATED_ARTICLES']; ?></div>
	</div>
	<?
	$lstart=module_wce::getLastUpdatedArticles(5);
	foreach ($lstart as $art) {
		// on boucle sur les artiles
		?>
		<div class="zone_article">
			<div class="title_article">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid=".$art->fields['id_heading']."&articleid=".$art->fields['id']); ?>">
					<? echo $art->fields['title']; ?>
				</a>
				<?
				if ( ! $art->isUptodate()) {
					echo '<img style="float:right;" src="'.module_wce::getTemplateWebPath('/gfx/puce_orange.png').'" title="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" alt="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" />';
				}
				else {
					echo '<img style="float:right;" src="'.module_wce::getTemplateWebPath('/gfx/puce_verte.png').'" title="'.$_SESSION['cste']['UP_TO_DATE'].'" alt="'.$_SESSION['cste']['UP_TO_DATE'].'" />';
				}
				?>
			</div>
			<div class="title_article_picture">
				<?
				$photo='';
				$updater = new contact();
				$updater->open($art->fields['updated_by']);
				if( ! $updater->isNew()){
					$file = $updater->getPhotoPath(40);//real_path
					if(file_exists($file)){
						$default_icon = false;
						?>
						<img src="<?php echo $updater->getPhotoWebPath(40); ?>">
						<?php
					}
					else{
						$default_icon = true;
					}
				}
				else{
					$default_icon = true;
				}

				if($default_icon){
					?>
						<img src="<?php echo module_wce::getTemplateWebPath('/gfx/human40.png'); ?>">
					<?php
				}
				?>
			</div>
			<div class="autor">
				<span><? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?></span>
				<a href="javascript:void(0);">
					<? echo $art->fields['firstname']." ".$art->fields['lastname'];?>
				</a>
			</div>
			<div class="date">
				<span>
					<?
					$date_modify = dims_timestamp2local($art->fields['timestp_modify']);
					echo $date_modify['date'].' '.$_SESSION['cste']['_DIMS_LABEL_A'].' '.$date_modify['time'];
					?>
				</span>
			</div>
		</div>
		<?
	}
	?>
	<div class="title_naviguer">
		<?
		// TODO : remplacer _SUB_SITE par _SUB_ARTICLE (qd l'onglet sera actif)
		?>
		<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE); ?>">
			<? echo $_SESSION['cste']['_GO_LIST_ARTICLES']; ?>
		</a>
	</div>
</div>