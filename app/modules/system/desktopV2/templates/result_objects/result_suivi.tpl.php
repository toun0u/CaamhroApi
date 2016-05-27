<div class="search_result">
	<div class="selection_form">
		<input type="checkbox" name="selection[]" value="<?php echo $this->fields['id_globalobject']; ?>" />
	</div>
	<div class="add_to_context">
			<?php
			if(!isset($_SESSION['dims']['advanced_search']['filters']['suivis'][$this->fields['id_globalobject']])){
				?>
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=suivi&val=<?php echo $this->fields['id_globalobject'];?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/fleche_result2.png">
				<?php
			}
			else{
				?>
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=del&type=suivi&val=<?php echo $this->fields['id_globalobject'];?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/already_in_search2.png">
				<?php
			}
			?>
		</a>
	</div>
	<div class="avatar">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60.png">
	</div>
	<div class="detail">
		<div class="title">
			<a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $this->fields['tiers_id']; ?>&type=<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>&init_filters=1&from=search&concepts_op=suivis">
				<?
				switch($this->fields['type']){
					case 'Devis':
						?>
						<img src="modules/system/img/ico_devis.gif" />
						<?
						break;
					case 'Facture':
						?>
						<img src="modules/system/img/ico_facture.gif" />
						<?
						break;
					case 'Avoir':
						?>
						<img src="modules/system/img/ico_avoir.gif" />
						<?
						break;
				}
				?>
				<span><?php echo $this->fields['libelle']; ?></span>
			</a>
			<div>
				<strong><? echo $_SESSION['cste']['_WCE_ARTICLE_REFERENCE']; ?> :</strong>
				<span>
					<? echo $this->getNumero(); ?>
				</span>
			</div>
		</div>
		<div class="contact_employer">
			<?php
				if($this->fields['tiers_id'] != '' && $this->fields['tiers_id'] > 0){
					$tiers = new tiers();
					$tiers->open($this->fields['tiers_id']);
					if (isset($tiers->fields['intitule'])){
						?>
						<span>
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png"/>
							<strong><? echo $_SESSION['cste']['_DIMS_LABEL_ENTERPRISES']; ?> :</strong>
							<a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $tiers->fields['id']; ?>&type=<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>&init_filters=1&from=search">
								<?php echo $tiers->fields['intitule'];?>
							</a>
						</span>
						<?php
					}
				}
			?>
		</div>
		<div class="context" style="clear: both;">
			<?php
			$field = $this->getLightAttribute('extra_field');
			$label = $this->getLightAttribute('extra_label');
			if(	isset($field)
			&& 	isset($label)
			&&  isset($this->fields[$field])
			&&  isset($_SESSION['cste'][$label])){
				$sentence = $this->getLightAttribute('sentence');
				$prewords = $this->getLightAttribute('prewords');
				if(isset($prewords['possible']))//à priori root est quoi qu'il arrive set sinon y'aurait pas de résultat
					$words = array_merge($prewords['root'], $prewords['possible']);
				else $words = $prewords['root'];
				if (strpos($this->fields[$field],$sentence) !== false)
					echo '<strong>'. ucfirst($_SESSION['cste'][$label]) .'</strong> : '.dims_getManifiedWords(strip_tags($this->fields[$field]), $words, '<span class="founded_result">', '</span>');
				else
					echo '<strong>'. ucfirst($_SESSION['cste'][$label]) .'</strong> : '.dims_getManifiedWords(strip_tags($sentence), $words, '<span class="founded_result">', '</span>');
			}
			?>
		</div>
	</div>
</div>