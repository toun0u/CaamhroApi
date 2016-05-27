<?php
global $skin;

$data =array();
$elements=array();

$data['headers'][] = $_SESSION['cste']['_DIMS_DATE'];
$data['headers'][] = $_SESSION['cste']['_AUTHOR'];
$data['headers'][] = $_SESSION['cste']['_DIMS_LABEL_PERSONNE_CONTACTED'];
$data['headers'][] = $_SESSION['cste']['_DIMS_LABEL_MODE_COMMUNICATION'];
$data['headers'][] = substr($_SESSION['cste']['_DIMS_COMMENTS'],0,7).".";
$data['headers'][] = $_SESSION['cste']['_DIMS_ACTIONS'];
$data['data']['aasorting']['num'] = 1;
$data['data']['aasorting']['order'] = 'desc';
$data['data']['bSortable'][5] = false;

?>
<div class="displayCase" style="border-bottom:1px solid #D1D1D1;width:100%;">
	<?php
	if(!empty($idCurrentCase)) {
		?>
		<div class="actions">
			<a href="Javascript: void(0);" onclick="Javascript: displayAddInterventionPopup(<? echo dims_const_interv::_OP_EDIT_INTERVENTION; ?>, <? echo $this->fields['id_globalobject']; ?>);">
				<?php echo $_SESSION['cste']['_DIMS_TITLE_ADD_INTERVENTION']; ?>
			</a>
		</div>
		<?php
	}
	?>
	<span style="font-weight:bold;margin-left:10px;margin-top:10px;width:auto; margin-bottom:10px;">
		<img src="img/tags-icon.png" style="margin-right:5px;" />
		<?php echo $_SESSION['cste']['_DIMS_TITLE_INTERV']; ?> :
	</span>

	<div style="width:99%;">
		<?php echo $skin->displayArray($data,'',dims::getInstance()->getScriptEnv().'?dims_op=intervention&intervention_op='.dims_const_interv::_OP_LST_INTERVENTIONS.'&id='.$this->fields['id_globalobject']); ?>
	</div>
</div>