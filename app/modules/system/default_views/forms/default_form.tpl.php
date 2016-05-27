<div class="container_admin global_content_record">
	<div class="form_object_block">
		<?php
			global $dims;
		?>
		<?= $this->get_header(); ?>
				<?php
					$object = $this->getObject();
					if(isset($object) && $object instanceof dims_data_object){
						$glob_message = $object->getLightAttribute("global_error");
						if(empty($glob_message)){
							?>
							<div class="global_message error_message" style="display: none;"></div>
							<?php
						}
						else{
							if(is_array($glob_message)) {
								?>
								<ul class="global_message error_message">
									<?php
									foreach($glob_message as $error) {
										?>
										<li>
											<?php echo $glob_message;?>
										</li>
										<?php
									}
									?>
								</ul>
								<?php
							}
							else {
								?>
								<div class="global_message error_message"><?php echo $glob_message;?></div>
								<?php
							}
						}

						//Gestion des light attributes et de l'éventuel globalobject
						foreach($object->getAllLightAttributes() as $attr => $val){
							if(!is_array($val)){
								?>
								<input type="hidden" name="<?php echo $attr; ?>" value="<?php echo $val;?>" />
								<?php
							}
						}
						if(!$object->isNew() && isset($object->fields['id_globalobject'])){
							?>
							<input type="hidden" name="id_globalobject" value="<?php echo $object->fields['id_globalobject'];?>" />
							<?php
						}
					}elseif($this->isValidationEnabled()){
						?>
						<div class="global_message error_message" style="display: none;"></div>
						<?php
					}

					$blocks = $this->getBlocks();
					foreach($blocks as $block){
						if($block->getId() != 'default' || count($block->getFields())) //on affiche pas le bloc par défaut s'il n'a aucun
							$block->show();
					}
				if( $this->includesActionsBlock() ){
					$this->displayActionsBlock();
				}
				?>
		<?= $this->close_form(); ?>
	</div>
</div>
