<?php
	$sql = "
		SELECT 		name,parents
		FROM 		dims_mod_doc_folder
		WHERE		id = :idfolder
		";

		$result = $db->query($sql, array(':idfolder' => $currentfolder) );

		$row = $db->fetchrow($result);

		$parents = explode(",", $row['parents']);
		$parent = $parents[sizeof($parents)-1];
		$label=$row['name'];
?>
<div class="doc_fileform">
	<?php
	$docgallery = new docgallery();
	if ($op == 'folder_gallery_create') {
		?>
		<div class="doc_fileform_main">
			<div>

				<form name="docfolder_gallery" action="<? echo $scriptenv; ?>" method="post" onsubmit="javascript: return doc_gallery_validate(this);" enctype="multipart/form-data">
				<input type="hidden" name="op" value="folder_gallery_add">
				<input type="hidden" name="docgallery_id_folder" value="<? echo $currentfolder; ?>">
				<div class="dims_form" style="float:left;width:55%;">
					<div style="padding:2px;">
						<p>
							<label>Nom de la Gallerie :</label>
							<input type="text" class="text" name="docgallery_name" value="<? echo $label; ?>">
						</p>
						<p>
							<label>Largeur des Miniatures :</label>
							<input type="text" class="text" name="docgallery_small_width" style="width:40px;" maxlength='3' value='50'>
							<select name="docgallery_s_w_format" id="docgallery_s_w_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_s_h_format').selectedIndex=this.selectedIndex">
								<option value="px" selected>pixels</option>
								<option value="%">%</option>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Hauteur des Miniatures :</label>
							<input type="text" class="text" name="docgallery_small_height" style="width:40px;" maxlength='3' value='50'>
							<select name="docgallery_s_h_format" id="docgallery_s_h_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_s_w_format').selectedIndex=this.selectedIndex">
								<option value="px" selected>pixels</option>
								<option value="%">%</option>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Largeur des Images :</label>
							<input type="text" class="text" name="docgallery_big_width" style="width:40px;" maxlength='4' value='90'>
							<select name="docgallery_b_w_format" id="docgallery_b_w_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_b_h_format').selectedIndex=this.selectedIndex">
								<option value="px">pixels</option>
								<option value="%" selected>%</option>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Hauteur des Images :</label>
							<input type="text" class="text" name="docgallery_big_height" style="width:40px;" maxlength='4' value='90'>
							<select name="docgallery_b_h_format" id="docgallery_b_h_format" style="width:60px;"onchange="javascript:document.getElementById('docgallery_b_w_format').selectedIndex=this.selectedIndex">
								<option value="px">pixels</option>
								<option value="%" selected>%</option>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Affichage des Images :</label>
							<select name="docgallery_show_picture" style="width:60px;">
								<option value="yes" selected>oui</option>
								<option value="no" >non</option>
							</select>
							(.jpeg, .jpg, .gif, .png, .bmp)
						</p>
						<p>
							<label>Affichage des Fichiers Texte :</label>
							<select name="docgallery_show_textfile" style="width:60px;">
								<option value="yes">oui</option>
								<option value="no" selected>non</option>
							</select>
							(.pdf, .doc, .docx, .odt, .txt)
						</p>
						<p>
							<label>Affichage des Archives :</label>
							<select name="docgallery_show_compressfile" style="width:60px;">
								<option value="yes">oui</option>
								<option value="no" selected>non</option>
							</select>
							(.zip, .rar, .ace, .tar.bz2, .tar.gz, .tgz, .7z)
						</p>
					</div>
				</div>

				<div class="dims_form" style="float:left;width:45%;">
					<div style="padding:2px;">
						<p>
							<label>Commentaire:</label>
							<textarea class="text" name="docgallery_description"></textarea>

						</p>
						<p>
							<label>Nombre de ligne :</label>
							<input type="text" class="text" name="docgallery_nb_row" style="width:20px;"  maxlength='2' value='5'>
							( le nombre de ligne d'images par page )
						</p>
						<p>
							<label>Nombre de colonne :</label>
							<input type="text" class="text" name="docgallery_nb_column" style="width:20px;" maxlength='2' value='5'>
							( le nombre de colonne d'images par page )
						</p>
						<br>
						<p>
						<label>Galerie photo :</label>
							<select name="docgallery_show_photography" style="width:60px;">
								<option value="yes" selected>oui</option>
								<option value="no" >non</option>
							</select><br>( Le dossier ne doit contenir que des images en format JPEG )
						</p>
					</div>
				</div>

			<div style="clear:both;float:right;padding:4px;">
				<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_BACK']; ?>" onclick="javascript:document.location.href='<? echo "{$scriptenv}?op=browser&currentfolder={$parent}"; ?>';">
				<input type="submit" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
			</div>

			</form>

		</div>
		<?
	}
	else if ($op == 'folder_gallery_modify') {
		$currentgallery=dims_load_securvalue('currentgallery',dims_const::_DIMS_NUM_INPUT,true,true);
		if (isset($currentfolder)) $docgallery->open($currentgallery);
		?>
		<div class="doc_fileform_main">
			<div>

			<form name="docfolder_gallery" action="<? echo $scriptenv; ?>" method="post" onsubmit="javascript: return doc_gallery_validate(this);" enctype="multipart/form-data">
				<input type="hidden" name="op" value="folder_gallery_add">
				<input type="hidden" name="docgallery_timestp_modify" value="<? echo dims_createtimestamp(); ?>">
				<input type="hidden" name="docgallery_id" value="<? echo $currentgallery; ?>">
				<input type="hidden" name="docgallery_id_folder" value="<? echo $currentfolder; ?>">

				<div class="dims_form" style="float:left;width:58%;">
					<div style="padding:2px;">
						<p>
							<label>Nom de la Gallerie :</label>
							<input type="text" class="text" name="docgallery_name" value="<? echo ($docgallery->fields['name']); ?>">
						</p>
						<p>
							<label>Largeur des Miniatures :</label>
							<input type="text" class="text" name="docgallery_small_width" style="width:40px;" maxlength='3' value="<? echo ($docgallery->fields['small_width']); ?>">
							<select name="docgallery_s_w_format" id="docgallery_s_w_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_s_h_format').selectedIndex=this.selectedIndex">
								<?
								if ($docgallery->fields['s_w_format']=='px')
									echo '<option value="px" selected>pixels</option><option value="%">%</option>';
								else
									echo '<option value="px">pixels</option><option value="%" selected>%</option>';
								?>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Hauteur des Miniatures :</label>
							<input type="text" class="text" name="docgallery_small_height" style="width:40px;" maxlength='3' value="<? echo ($docgallery->fields['small_height']); ?>">
							<select name="docgallery_s_h_format" id="docgallery_s_h_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_s_w_format').selectedIndex=this.selectedIndex">
								<?
								if ($docgallery->fields['s_h_format']=='px')
									echo '<option value="px" selected>pixels</option><option value="%">%</option>';
								else
									echo '<option value="px">pixels</option><option value="%" selected>%</option>';
								?>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Largeur des Images :</label>
							<input type="text" class="text" name="docgallery_big_width" style="width:40px;" maxlength='4' value="<? echo ($docgallery->fields['big_width']); ?>">
							<select name="docgallery_b_w_format" id="docgallery_b_w_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_b_h_format').selectedIndex=this.selectedIndex">
								<?
								if ($docgallery->fields['b_w_format']=='px')
									echo '<option value="px" selected>pixels</option><option value="%">%</option>';
								else
									echo '<option value="px">pixels</option><option value="%" selected>%</option>';
								?>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Hauteur des Images :</label>
							<input type="text" class="text" name="docgallery_big_height" style="width:40px;" maxlength='4' value="<? echo ($docgallery->fields['big_height']); ?>">
							<select name="docgallery_b_h_format" id="docgallery_b_h_format" style="width:60px;" onchange="javascript:document.getElementById('docgallery_b_w_format').selectedIndex=this.selectedIndex">
								<?
								if ($docgallery->fields['b_h_format']=='px')
									echo '<option value="px" selected>pixels</option><option value="%">%</option>';
								else
									echo '<option value="px">pixels</option><option value="%" selected>%</option>';
								?>
							</select>
							( % : en fonction de la taille de l'image )
						</p>
						<p>
							<label>Affichage des Images :</label>
							<select name="docgallery_show_picture" style="width:60px;">
								<?
								if ($docgallery->fields['show_picture']=='yes')
									echo '<option value="yes" selected>oui</option><option value="no" >non</option>';
								else
									echo '<option value="no" selected>non</option><option value="yes" >oui</option>';
								?>
							</select>
							(.jpeg, .jpg, .gif, .png, .bmp)
						</p>
						<p>
							<label>Affichage des Fichiers Texte :</label>
							<select name="docgallery_show_textfile" style="width:60px;">
								<?
								if ($docgallery->fields['show_textfile']=='yes')
									echo '<option value="yes" selected>oui</option><option value="no" >non</option>';
								else
									echo '<option value="no" selected>non</option><option value="yes" >oui</option>';
								?>
							</select>
							(.pdf, .doc, .docx, .odt, .txt)
						</p>
						<p>
							<label>Affichage des Archives :</label>
							<select name="docgallery_show_compressfile" style="width:60px;">
								<?
								if ($docgallery->fields['show_compressfile']=='yes')
									echo '<option value="yes" selected>oui</option><option value="no" >non</option>';
								else
									echo '<option value="no" selected>non</option><option value="yes" >oui</option>';
								?>
							</select>
							(.zip, .rar, .ace, .tar.bz2, .tar.gz, .tgz, .7z)
						</p>
					</div>
				</div>

				<div class="dims_form" style="float:left;width:42%;">
					<div style="padding:2px;">
						<p>
							<label>Commentaire:</label>
							<textarea class="text" name="docgallery_description"><? echo ($docgallery->fields['description']); ?></textarea>

						</p>
						<p>
							<label>Nombre de ligne :</label>
							<input type="text" class="text" name="docgallery_nb_row" style="width:20px;"  maxlength='2' value="<? echo ($docgallery->fields['nb_row']); ?>">
							( le nombre de ligne d'images par page )
						</p>
						<p>
							<label>Nombre de colonne :</label>
							<input type="text" class="text" name="docgallery_nb_column" style="width:20px;" maxlength='2' value="<? echo ($docgallery->fields['nb_column']); ?>">
							( le nombre de colonne d'images par page )
						</p>
						<br>
						<p>
							<label>Galerie photo :</label>
							<select name="docgallery_show_photography" style="width:100px;">
								<?
								if ($docgallery->fields['show_photography']=='yes')
									echo '<option value="yes" selected>oui</option><option value="no" >non</option>';
								else
									echo '<option value="no" selected>non</option><option value="yes" >oui</option>';
								?>
							</select><br>( Le dossier ne doit contenir que des images en format JPEG )
						</p>
					</div>
				</div>

			<div style="clear:both;float:right;padding:4px;">
				<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_BACK']; ?>" onclick="javascript:document.location.href='<? echo "{$scriptenv}?op=browser&currentfolder={$parent}"; ?>';">
				<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DELETE']; ?>" onclick="javascript:dims_confirmlink('<? echo "{$scriptenv}?op=folder_gallery_delete&currentfolder={$currentfolder}&currentgallery={$currentgallery}"; ?>','Voulez-vous vraiment supprimer cette gallerie ?');">
				<input type="submit" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
			</div>

			</form>

		</div>

		<div class="doc_folderannotations">
		<?
                require_once DIMS_APP_PATH.'include/functions/annotations.php';
                dims_annotation(_DOC_OBJECT_FOLDER, $docgallery->fields['id'], $docgallery->fields['name']);
                ?>
		</div>

		<?
	}
	?>


</div>
