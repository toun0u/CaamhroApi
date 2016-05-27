<div class="bloc_submenu">
	<div class="submenu1">
		<div class="picture_text">
			<a href="<?= $dims->getScriptEnv().'?submenu=subscriptions';?>">
				<div style="height: 63px;margin-bottom: 10px;">
					<img src="./common/modules/events/img/icon_subscriptions.png">
				</div>
				<?= dims_constant::getVal('_DIMS_FRONT_SUBSCRIPTIONS'); ?>
			</a>
		</div>
		<div class="picture_text">
			<a href="<?= $dims->getScriptEnv().'?submenu=coming_events';?>">
				<div style="height: 63px;margin-bottom: 10px;">
					<img src="./common/modules/events/img/icon_events.png" />
				</div>
				<?= dims_constant::getVal('COMING_EVENTS'); ?>
			</a>
		</div>
		<div class="picture_text">
			<a href="<?= $dims->getScriptEnv().'?submenu=help';?>">
				<div style="height: 63px;margin-bottom: 10px;">
					<img src="./common/modules/events/img/icon_help.png" />
				</div>
				<?= dims_constant::getVal('_DIMS_EVT_HELP_TUTORIAL'); ?>
			</a>
		</div>
	</div>
	<div class="submenu2">
		<div class="picture_text">
			<a href="<?= $dims->getScriptEnv().'?submenu=my_profile';?>">
				<div style="height: 63px;margin-bottom: 10px;">
					<img src="./common/modules/events/img/icon_profile.png" />
				</div>
				<?= dims_constant::getVal('_DIMS_LABEL_MYPROFILE'); ?>
			</a>
		</div>
		<div class="picture_text">
			<a href="<?= $dims->getScriptEnv().'?submenu=feedback';?>">
				<div style="height: 63px;margin-bottom: 10px;">
					<img src="./common/modules/events/img/icon_feedback.png" />
				</div>
				<?= dims_constant::getVal('_DIMS_LABEL_FEEDBACK'); ?>
			</a>
		</div>
		<div class="picture_text">
			<a href="<?= $dims->getScriptEnv().'?dims_logout=1';?>">
				<div style="height: 63px;margin-bottom: 10px;">
					<img src="./common/modules/events/img/icon_logout.png" />
				</div>
				<?= dims_constant::getVal('_DIMS_LABEL_DISCONNECT'); ?>
			</a>
		</div>
	</div>
</div>
