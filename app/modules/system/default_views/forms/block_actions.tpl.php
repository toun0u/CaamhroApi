<?php
$object = $this->getObject();
$ddo_present = (isset($object) && $object instanceof dims_data_object);
?>
<div class="sub_form">
	<div class="form_buttons">
		<?php
		if($this->isValidationEnabled()){
			?>
			<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
			<?php
		}
		?>

		<div><input type="submit" value="<?php echo $this->getSubmitValue(); ?>"> <?php if( $this->isForceContinueEnabled() || ( ( ( $ddo_present && $object->isNew() ) || is_null($object) ) && $this->isContinueEnabled() ) ) echo $_SESSION['cste']['_DIMS_OR']; ?></div>
		<?php
		if( $this->isForceContinueEnabled() || ( ( ( $ddo_present && $object->isNew() ) || is_null($object) ) && $this->isContinueEnabled() ) ){
			?>
			<div><input type="submit" name="continue" value="<?php echo $this->getSubmitValue(). ' '.$_SESSION['cste']['AND_CONTINUE']; ?>"></div>
			<?php
		}
		$back_url = $this->getBackUrl();
		if(isset($back_url)){
			?>
			<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a class="undo" href="<?php echo $back_url;?>"><?php echo $this->getBackName(); ?></a></div>
			<?php
		}
		?>
	</div>
</div>