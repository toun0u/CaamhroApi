<?php
$view = view::getInstance();
?>

<div class="billet-wrapper">
	<div class="mod grid1-3 line">
		<div class="mod photo">
			<?php
			$src = '/assets/images/frontoffice/zooparis/design/logo_zoo.png';
			switch(get_class($object)){
				case 'cata_famille':
					$imgs = $object->getThumbnails(1);
					if(!empty($imgs)){
						$first = $imgs[0];
						$src = $first->getDocFile()->getwebpath();
					}
					break;
				case 'article':
					$img = $object->getOriginal();
					if( ! empty($img) ) $src = $img->getwebpath();
					break;
			}
			?>
			<img src="<?= $src; ?>" title="<?= $object->get('label'); ?>"/>
		</div>
		<div class="mod description">
			<h2><?= $object->get('label'); ?></h2>
			<div class="description">
				<?= $object->get('description'); ?>
			</div>
			<div class="actions">
				<input type="submit" class="right" value="Réserver" onclick="javascript:document.location.href='<?= get_path('billets', 'show', array('id' => $object->get('id'), 'type' => (get_class($object) == 'article') ? 'art' : 'fam'));?>';">
			</div>
		</div>
	</div>
</div>
