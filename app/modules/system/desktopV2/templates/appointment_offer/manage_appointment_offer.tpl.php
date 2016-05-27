<?php
$dims = dims::getInstance();
$db = $dims->getDb();

$init = dims_load_securvalue('init',dims_const::_DIMS_NUM_INPUT,true);
if ($init)
	$_SESSION['desktopv2']['appointment']['filters']['status'] = 0;
// filtres
$_SESSION['desktopv2']['appointment']['filters']['status'] = dims_load_securvalue('status', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['appointment']['filters']['status']);

?>

<div>
	<div class="title_leads">
		<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/calendar32.png"/>
		<h2><?= $_SESSION['cste']['_ORGANISE_MEETINGS']; ?></h2>
	</div>
	<div>
		<form id="filter_form" name="filter_form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("submenu",	_DESKTOP_V2_DESKTOP);
				$token->field("mode",		"appointment_offer");
				$token->field("action",		"manage");
				$token->field("status");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="submenu" value="<?php echo _DESKTOP_V2_DESKTOP; ?>" />
			<input type="hidden" name="mode" value="appointment_offer" />
			<input type="hidden" name="action" value="manage" />

			<div class="filters">
				<table>
					<tr>
						<td>
							<label class="status" for="status"><?php echo $_SESSION['cste']['STATUS']; ?></label>
							<select id="status" name="status" style="width:200px;">
								<option value="-1"><?= $_SESSION['cste']['_DIMS_ALLS']; ?></option>
								<option value="<?php echo dims_appointment_offer::STATUS_NOT_VALIDATED; ?>" <?php if ($_SESSION['desktopv2']['appointment']['filters']['status'] == dims_appointment_offer::STATUS_NOT_VALIDATED) echo 'selected="selected"'; ?>><?= $_SESSION['cste']['_DIMS_LABEL_OPENED_INSCR_EVT']; ?></option>
								<option value="<?php echo dims_appointment_offer::STATUS_VALIDATED; ?>" <?php if ($_SESSION['desktopv2']['appointment']['filters']['status'] == dims_appointment_offer::STATUS_VALIDATED) echo 'selected="selected"'; ?>><?= $_SESSION['cste']['_DIMS_LABEL_CLOSED_INSCR_EVT']; ?></option>
							</select>
						</td>
					</tr>
				</table>
				<div class="sub_form">
					<div class="form_buttons">
						<div>
							<a href="<?php echo $dims->getScriptEnv()."?submenu="._DESKTOP_V2_DESKTOP."&mode=appointment_offer&action=manage&init=1"; ?>">
								<? echo $_SESSION['cste']['_DIMS_RESET']; ?>
							</a>
						</div>
						<div>
							<span><? echo $_SESSION['cste']['_DIMS_OR']; ?></span>
						</div>
						<div>
							<input type="submit" value="<?php echo $_SESSION['cste']['_FORMS_FILTER']; ?>"/>
						</div>
					</div>
				</div>
			</div>
		</form>

		<br/>
		<div class="opportunities_table">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th style="width:25px;"></th>
					<th><?= $_SESSION['cste']['_DIMS_LABEL']; ?></th>
					<th><?= $_SESSION['cste']['DATES']; ?></th>
					<th><?= $_SESSION['cste']['_LOCATION']; ?></th>
					<th><?= $_SESSION['cste']['_FORMS_RESPONSE']; ?></th>
					<th style="width:75px;"><?= $_SESSION['cste']['_DIMS_LABEL_LINK_GEN']; ?></th>
					<th><?= $_SESSION['cste']['_DIMS_ACTIONS']; ?></th>
				</tr>
				<?php
				$lst = dims_appointment_offer::getAppointmentsTable($_SESSION['desktopv2']['appointment']['filters']['status']);
				if(count($lst)){
					foreach($lst as $a){
						?>
						<tr>
							<td style="text-align:center;"><?= $a[0]; ?></td>
							<td><?= $a[1]; ?></td>
							<td><?= $a[2]; ?></td>
							<td><?= $a[3]; ?></td>
							<td><?= $a[4]; ?></td>
							<td style="text-align:center;"><?= $a[5]; ?></td>
							<td><?= $a[6]; ?></td>
						</tr>
						<?php
					}
				}else{
					?>
					<tr>
						<td class="no-elements" colspan="7">No results</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
	</div>
</div>

<div id="planning_popup"></div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#status').chosen({no_results_text: "No results matched"});
		var changed = $();

		$("#filter_form div.filters table td select").change(function() {
			changed.add($(this));
		});
	});
</script>
