<?php
	$class_trie = "ordered";
	$type = $this->getType();
?>
<tr>
	<td <?php if(isset($this->orders[0])) echo "class=".$class_trie ?>>
		<?php
		if($type == suivi::TYPE_FACTURE) {
		?>
		<img <?php echo 'alt="Acceptée" src="'._DESKTOP_TPL_PATH.'/gfx/common/dollar16.png"' ?>/>
		<?php
		} else {
			if($type == suivi::TYPE_DEVIS && !$this->getValide()) {
		?>
			<a onclick="javascript:dims_confirmlink('admin.php?valider_devis=<?php echo $this->fields['id_suivi'] ?>', '<?php echo $_SESSION['cste']['_DIMS_CONFIRM_VALIDATE_ESTIMATE']; ?>');" href="javascript:void(0)"><img <?php echo ($this->getValide()) ? 'alt="Accepté" src="'._DESKTOP_TPL_PATH.'/gfx/common/actif16.png"' : 'alt="Refusé" src="'._DESKTOP_TPL_PATH.'/gfx/common/inactif16.png"' ?>/></a>
		<?php
			} else {
		?>
				<img <?php echo ($this->getValide()) ? 'alt="Accepté" src="'._DESKTOP_TPL_PATH.'/gfx/common/actif16.png"' : 'alt="Refusé" src="'._DESKTOP_TPL_PATH.'/gfx/common/inactif16.png"' ?>/>
		<?php
			}
		}
		?>
	</td>
	<?php
		$dec_point = ($_SESSION['dims']['user']['lang'] == 2) ? "." : ",";
		$thousands_sep = ($_SESSION['dims']['user']['lang'] == 2) ? "," : " ";
	?>
	<td <?php if(isset($this->orders[1])) echo "class=".$class_trie ?>>
		<?php
		switch($type){
			case suivi::TYPE_DEVIS:
				echo $_SESSION['cste']['QUOTATION'];
				break;
			case suivi::TYPE_FACTURE:
				echo $_SESSION['cste']['INVOICE'];
				break;
			case suivi::TYPE_AVOIR:
				echo $_SESSION['cste']['ASSET'];
				break;
		}
		?>
	</td>
	<td <?php if(isset($this->orders[2])) echo "class=".$class_trie ?>><?php echo $this->getNumero(); ?></td>
	<td <?php if(isset($this->orders[3])) echo "class=".$class_trie ?>>
		<span class="title_fiche_bloc_document"><a href="javascript:void(0);" onclick="javascript:openSuivi(<?php echo $this->getIdSuivi(); ?>);"><?php echo $this->fields['libelle']; ?></a></span>
		<?php /*<span class="text_fiche_bloc_document">Added the <?php echo $this->getDateJour(); ?></span>*/ ?>
	</td>
	<td <?php if(isset($this->orders[4])) echo "class=".$class_trie ?>><?php echo $this->getExercice(); ?></td>
	<td <?php if(isset($this->orders[5])) echo "class=".$class_trie ?>><?php echo $this->getDateJour(); ?></td>
	<?php

	?>
	<td <?php if(isset($this->orders[6])) echo "class=".$class_trie ?>><?php echo number_format($this->getMontantHT()/(1-($this->getRemise()/100)), 2, $dec_point, $thousands_sep); ?></td>
	<td <?php if(isset($this->orders[7])) echo "class=".$class_trie ?>><?php echo $this->getRemise(); ?> %</td>
	<td <?php if(isset($this->orders[8])) echo "class=".$class_trie ?>><?php echo number_format($this->getMontantHT(), 2, $dec_point, $thousands_sep); ?></td>
	<td <?php if(isset($this->orders[9])) echo "class=".$class_trie ?>><?php echo number_format($this->getMontantTTC(), 2, $dec_point, $thousands_sep); ?></td>
	<td <?php if(isset($this->orders[10])) echo "class=".$class_trie ?>>
		<?php
		if($type != suivi::TYPE_DEVIS) {
			if($this->getSoldeTTC() > 0) {
				echo "<a onclick=\"javascript:dims_confirmlink('admin.php?dims_op=desktopv2&action=solder_suivi&id_suivi=".$this->fields['id_suivi']."', '".$_SESSION['cste']['_DIMS_CONFIRM']."');\" href=\"javascript:void(0)\"><img title='Non soldé' src='"._DESKTOP_TPL_PATH."/gfx/common/inactif16.png'/></a>";
			} else {
				echo "<img title='Soldé' src='"._DESKTOP_TPL_PATH."/gfx/common/actif16.png'/>";
			}
		}
		?>
	</td>
	<td class="filter" style="float:none;vertical-align:middle;">
		<a class="progressive previsu" href="javascript:void(0);" onclick="javascript:openSuivi(<?php echo $this->getIdSuivi(); ?>);" title="<?php echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>"></a>
		<a class="progressive close" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?dims_op=desktopv2&action=supprimer_suivi&id_suivi=<?php echo $this->getIdSuivi(); ?>', '<?php echo $_SESSION['cste']['_DIMS_CONFIRM_DELETE_MONITORING']; ?>');" title="<?php echo $_SESSION['cste']['_DELETE']; ?>"></a>
	</td>
</tr>
