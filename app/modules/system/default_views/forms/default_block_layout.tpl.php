<div class="sub_bloc <?= $this->getClasses(); ?>" id="<?= $this->getId(); ?>">
	<?php
	$title = $this->getTitle();
	if(isset($title) && !empty($title) ){
	?>
		<h<?= $this->getHeaderLevel(); ?>><? echo $title; ?></h<?= $this->getHeaderLevel(); ?>>
	<?php
	}
	?>
	<div class="sub_bloc_form">
		<?php
		$fields = $this->getFields();
		$grid = $this->getGrid();
		$hiddens = array();
		if(count($fields)){
			?>
			<table>
				<?php

				//Mise en place du système de grille.
				//On calcule le max de colonnes
				$max_r = 1;
				$max_c = 1;
				foreach($grid as $row => $cols){
					if($row > $max_r){
						$max_r = $row;
					}
					foreach($cols as $col => $name){
						if($col > $max_c){
							$max_c = $col;
						}
					}
				}
				$widthLabel = 20;
				$widthField = 80;
				if($max_c > 1){
					$pFields = 100/$max_c;
					$widthLabel = $pFields*$this->getLabelWidth();
					$widthField = $pFields-$widthLabel;
				}
				for($i=1; $i <= $max_r; $i++){
					?>
					<tr>
					<?php
					$rel_rev_fields = array();
					for($j=1; $j <= $max_c; $j++){
						if( isset($grid[$i][$j]) ){
							$field = $fields[$grid[$i][$j]['name']][$grid[$i][$j]['idx']];
							switch($field['type']){
								default:
									?>
									<td class="label_field <?php if($field['type'] == 'textarea') echo 'label_top'; ?>" style="width:<?= $widthLabel; ?>%;">
										<label for="<?= $field['id'];?>"><?= $field['label']; ?></label><?php if( $field['mandatory'] ) echo  '<span class="required">*</span>'; ?>
									</td>
									<td class="value_field" style="width:<?= $widthField; ?>%;">
										<?= $field['html']; ?>
									</td>
									<?php
									if($field['mandatory'] || (!is_null($field['revision']) && !empty($field['revision']))) {
										$rel_rev_fields[$j] = $field['id'];
									}
									break;

								case 'checkbox':
								case 'radio':
									?>
									<td class="label_field" style="width:<?= $widthLabel; ?>%;">
										<?= $field['html']; ?>
									</td>
									<td class="value_field" style="width:<?= $widthField; ?>%;">
										<label for="<?= $field['id'];?>"><?= $field['label']; ?></label>
									</td>
									<?php
									break;
								case 'hidden':
									$hiddens[] = $field['html']; //on intègre simplement le champ
									break;
							}
						}
						else{
							?>
							<td></td><td></td>
							<?php
						}
					}
					?>
					</tr>
					<?php
					if( ! empty($rel_rev_fields) ){//C'est que sur la row courante il y en a qui sont mandatories ou rev
						?>
						<tr>
						<?php
						for($k = 1; $k <= $max_c ; $k++){
							if( ! empty($rel_rev_fields[$k]) ){
								?>
								<td></td><td><div class="mess_error" id="def_<?=$rel_rev_fields[$k];?>"></div></td>
								<?php
							}
							else{
								?>
								<td></td><td></td>
								<?php
							}
						}
						?>
						</tr>
						<?php
					}
				}
				?>
			</table>
			<?php
			//on sort les champs hidden de la table, c'est plus cohérent d'un point de vue strcture SQL
			foreach($hiddens as $hidden){
				echo $hidden;
			}
		}
		$form = $this->getForm();
		if($this->isActionnable() && isset($form)){
			$form->displayActionsBlock();
		}
		?>
	</div>
</div>