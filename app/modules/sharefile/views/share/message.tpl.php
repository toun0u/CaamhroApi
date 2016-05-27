<div class="dims_form" style="float:left; width:50%;padding-top:20px;">
	<div style="padding:2px;">
			<span style="width:10%;display:block;float:left;">
					<img src="/common/modules/sharefile/img/btn_access_bg.gif">
			</span>
			<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
				<?= dims_constant::getVal('ACCESS_ERROR'); ?>
			</span>
	</div>
	<div style="padding:2px;clear:both;float:left;width:100%;font-size:14px;">
			<p>
				<label>&nbsp;</label>
				<img src="./common/img/warning.png">
				<font style="color:#FF0000">
					<?= $this->get('message'); ?>
				</font>
			</p>
	</div>
</div>
