<?php

require_once DIMS_APP_PATH.'modules/system/desktopV2/include/class_module_desktopv2.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// calcul de la largeur et hauteur
if (is_numeric($this->getLargeurColumn())) {
    $largeurcol1=($this->getLargeurColumn()+12)."px";
    $largeurcol2= ($this->getWidthForm() - $this->getLargeurColumn() -16)."px";
}

$cour=0;
// on calcule sur le nbre d'images
if ($this->nbImages>0) {
	// on recupere l'image selectionne
	if ($this->selectedImage!= '') {
		// on va se placer sur l'image courante
	}
	else {
		$this->selectedImage=0;
	}
	?>
	<div class="selector">
		<? echo $this->getSelectChoosePage(module_desktopv2::getTemplateWebPath('admin/gfx/external/previous_page.png'),
										   module_desktopv2::getTemplateWebPath('admin/gfx/external/next_page.png'),
										   false,
										   false,
										   module_desktopv2::getTemplateWebPath('admin/gfx/external/loupe_plus20.png'),
										   module_desktopv2::getTemplateWebPath('admin/gfx/external/loupe_moins20.png')); ?>
	</div>
	<div style="width:<? echo $largeurcol1; ?>;" class="thumbnail">
	<?
	// on regarde maintenant pour generer les images en miniatures pour les images affichees
	$paththumb=_DIMS_PATHDATA.$this->pathData."/thumbs";

	if (!is_dir($paththumb) ) {
		dims_makedir($paththumb);
	}

	// on peut ecrire dans le dossier pour les miniatures
	if (is_dir($paththumb)) {
		echo "<ul>";

		for ($i=$cour;$i<$cour+$this->nbImages;$i++) {
			echo "<li onclick=\"javascript:refreshPreviewDocument(".$i.");\">";

			// on affiche l'image réduite
			$sel = ($i == $this->selectedImage) ? 'selected' : '' ;
			echo '<img class="'.$sel.'" style="width:'.$this->largeurcolumn.'px;" src="'.$this->webPathData.'/'.$this->listImages[$i].'">';
			echo '<div class="num_page '.$sel.'">'.($i+1).'</div>';
			echo "</li>";
		}
		echo "</ul>";
	}
	?>
	</div>
	<?
}
?>
<div id="<? echo $this->namepreview;?>refresh" style="width:<? echo $largeurcol2; ?>;" class="content_visu">

	<?
	// on doit construire l'image courante qui va s'intégrer dans le contenu
	echo $this->zoomImage(true,false,false);

	?>
</div>
