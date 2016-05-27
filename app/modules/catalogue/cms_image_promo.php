<?php
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_promotion.php';

foreach (cata_promotion::allActives('date_fin') as $promo) {
	$image = $promo->getImage();
	if ($image != null) {
		?>
		<a class="image_promo" href="<?php echo $dims->getScriptEnv().'?op=promotions&id_promo='.$promo->get('id'); ?>" title="<?php echo $promo->getLibelle(); ?>">
			<img src="<?php echo $image->getwebpath(); ?>" alt="<?php echo $promo->getLibelle(); ?>" />
		</a>
		<?php
	}
}
