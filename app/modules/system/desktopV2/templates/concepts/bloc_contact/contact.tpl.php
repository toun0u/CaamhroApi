
<table cellspacing="5" cellpadding="0">
	<tbody>
		<tr>
			<td style="width:30px;">
				<div class="selection_form">
					<input type="checkbox" name="selection[]" value="<?php echo $this->fields['id_globalobject']; ?>" />
				</div>
			</td>
			<td class="picture_contact">
				<?
				$photopath=$this->getPhotoWebPath(40);
				if ($photopath != '' && file_exists($photopath))
					echo '<img class="image_address_book" src="'.$photopath.'" border="0" style="float:left;" />';
				else
					echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/human40.png" border="0" style="float:left;" />';
				?>
			</td>
			<td style="float: left; width: 75%;">
				<div class="puce_title_contact">
					<span onclick="Javascript: openContactBloc(<?php echo $this->getId(); ?>, <?php echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>, 'cadre_bloc_contact');" style="cursor:pointer;"><? echo $this->fields['firstname']." ".$this->fields['lastname']; ?></span>
				</div>
				<div class="desc_contact">
					<?
					if (isset($this->fields['employers']) && is_array($this->fields['employers']) && !empty($this->fields['employers'])) {

						if (isset($this->fields['date_fin']) && $this->fields['date_fin']!='' && $this->fields['date_fin']>0) {
							echo ",";
						}
						echo " ";
					}
					global $planning_mois;
					// on regarde si on a une date de depart
					if (isset($this->fields['date_fin']) && $this->fields['date_fin']!='' && $this->fields['date_fin']>0) {
						$date_fin_y = substr($this->fields['date_fin'], 0, 4);
						//$date_fin_m = substr($this->fields['date_fin'], 4, 2);
						//$date_fin_d = substr($this->fields['date_fin'], 6, 2);
						echo '<a href="javascript:void(0);" onclick="javascript:openLinkBloc('.$this->getId().','.dims_const::_SYSTEM_OBJECT_CONTACT.','.$this->fields['idlink'].',\'cadre_bloc_contact\');">'.$_SESSION['cste']['_NOT_SINCE']." ".$planning_mois[date('n',$this->fields['date_fin'])]." ".$date_fin_y."</a>";
					}

					/*if (isset($this->fields['employers']) && is_array($this->fields['employers']) && !empty($this->fields['employers'])) {

						$employeur=current($this->fields['employers']);
						//$employeur = current($this->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR'));
						echo $employeur['intitule'];
					}*/
					?>
				</div>
			</td>
			<td class="filter" style="float:none;vertical-align:middle;">
				<img class="perform_cube" onclick="javascript:document.location.href='/admin.php?action=add_filter&filter_type=contact&filter_value=<?php echo $this->fields['id_globalobject']; ?>';" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube_add.png" style="float:left;cursor:pointer;" />
			</td>
			<td class="filter" style="float:none;vertical-align:middle;">
			<?php
				$focus = "?submenu="._DESKTOP_V2_CONCEPTS."&id=".$this->getId()."&type=".dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1';
				?>
				<img class="perform_cube" onclick="javascript: document.location.href='<? echo $focus; ?>';" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" style="float:left;cursor:pointer;" />
			</td>
			<?php
			if (isset($this->fields['idlink'])) {
			?>

			<td class="filter" style="float:none;vertical-align:middle;">
				<img class="perform_cube" onclick="javascript:openLinkBloc(<?php echo $this->getId(); ?>, <?php echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>,<?php echo $this->fields['idlink']; ?>, 'cadre_bloc_contact');" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/infini.png" style="float:left;cursor:pointer;" />
			</td>

			<?php
			}

			if (true) { //$this->getLightAttribute('concept_not_event')) {
				?>
				<td class="filter" style="float:none;vertical-align:middle;">
					<a title="<?php echo $_SESSION['cste']['DETACH_THIS_CONTACT']; ?>" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?action=del_concepts_link&link_type=<?php echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>&id=<?php echo $this->fields['id_globalobject']; ?>', '<?php echo $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DETACH_THIS_CONTACT']; ?>');">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" />
					</a>
				</td>
				<?php
			}
			?>
		</tr>
	</tbody>
</table>
