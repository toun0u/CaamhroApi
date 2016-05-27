<?
// recuperation des articles a mettre à jour

// recuperation des articles
$lstart=module_wiki::getLastUpdatedArticles(5);

?>
<div class="cadre cadre_article_gauche cadre_fixed_height">
	<h2>Derniers articles mis à jour</h2>
        <?
		foreach ($lstart as $art) {
			// on boucle sur les artiles
			?>
			<div class="cadre_zone_article">
				<div class="zone_article_title">
					<span><img src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_mini_wiki.png'); ?>" title="wiki" alt="wiki" />
					<a style="cursor: pointer;" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=".$art->fields['id'].'&wce_mode=edit'); ?>" class="lien_bleu"><? echo $art->fields['title']; ?></a></span>
					<span>
						<?
						if ( ! $art->isUptodate()) {
							echo '<img src="'.module_wiki::getTemplateWebPath('/gfx/puce_orange.png').'" title="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" alt="'.$_SESSION['cste']['NOT_UP_TO_DATE'].'" />';
						}
						else {
							echo '<img src="'.module_wiki::getTemplateWebPath('/gfx/puce_verte.png').'" title="'.$_SESSION['cste']['UP_TO_DATE'].'" alt="'.$_SESSION['cste']['UP_TO_DATE'].'" />';
						}
						?>
					</span>
				</div>
				<div class="zone_picture_date">
					<div class="picture">
						<?
						// traitement de la photo
						$photo='';

						$updater = new contact();
						$updater->open($art->fields['updated_by']);
						if( ! $updater->isNew()){
							$file = $updater->getPhotoPath(40);//real_path
							if(file_exists($file)){
								$default_icon = false;
								?>
								<img class="picture" src="<?php echo $updater->getPhotoWebPath(40); ?>">
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
								<img src="<?php echo module_wiki::getTemplateWebPath('/gfx/human40.png'); ?>" width="40px" height="40px">
							<?php
						}
						?>
					</div>
					<div class="date_depose">
						<p class="par"><? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?> <span class="maj_par"><? echo $art->fields['firstname']." ".$art->fields['lastname'];?></span></p>
						<p>
						<?
						$date_modify = dims_timestamp2local($art->fields['timestp_modify']);
						echo $date_modify['date'].' '.$_SESSION['cste']['_DIMS_LABEL_A'].' '.$date_modify['time'];
						?>
						</p>
					</div>
				</div>
			</div>
			<?
		}
	?>
</div>