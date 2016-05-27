<?
echo $skin->open_simplebloc("", '', '', '');
?>
<div style="width:100%;text-align:right">
	<form name="form_view_date" action="" method="POST">
	<?php
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("desktop_view_date");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		if (!isset($_SESSION['dims']['desktop_view_date'])) $_SESSION['dims']['desktop_view_date']=0;
		$desktop_view_date=dims_load_securvalue('desktop_view_date',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_view_date'],0);
		// mode de vue cette semaine
		// 15 jours, 1 mois, 3 mois

		$tab=array();
		$tab[]=$_DIMS['cste']['_DIMS_LABEL_THIS_WEEK'];
		$tab[]=$_DIMS['cste']['_DIMS_LABEL_15_DAYS'];
		$tab[]=$_DIMS['cste']['_DIMS_LABEL_THIS_MONTH'];
		$tab[]=$_DIMS['cste']['_DIMS_LABEL_3_MONTHS'];

		foreach($tab as $i=>$choiceview) {
			if ($i==$_SESSION['dims']['desktop_view_date']) $select="checked=\"checked\"";
			else $select="";

			echo "<input type=\"radio\" onclick=\"javascript:document.form_view_date.submit();\" name=\"desktop_view_date\" value=\"$i\" ".$select.">".$choiceview."&nbsp;";
		}
	?>
	</form>
</div>
<?php

switch ($_SESSION['dims']['desktop_view_date']) {
	case 0:
		$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
		$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
		break;
	case 1:
		$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-15, date("Y")));
		$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-15, date("Y")));
		break;
	case 2:
		$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));
		$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));
		break;
	case 3:
		$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
		$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
		break;
}

$action = '';
$type	= '';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true, false);
$type	= dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true, false);

echo $skin->open_simplebloc("", '', '', '');
?>
<table width="100%">
	<tr>
		<?php
		switch($action) {
			default:
				?>
				<td width="50%" style="vertical-align: top">
					<?php require_once(DIMS_APP_PATH . "/modules/system/lfb_desktop_contact_user.php"); ?>
				</td>
				<td width="50%" style="vertical-align: top">
					<?php require_once(DIMS_APP_PATH . "/modules/system/lfb_desktop_contact_enterprise.php"); ?>
				</td>
				<?php
				break;
			case 'see_ct':
				?>
				<td width="100%" style="vertical-align: top">
					<?php require_once(DIMS_APP_PATH . "/modules/system/lfb_desktop_contact_user_total.php"); ?>
				</td>
				<?php
				break;
			case 'see_ent':
				?>
				<td width="100%" style="vertical-align: top">
					<?php require_once(DIMS_APP_PATH . "/modules/system/lfb_desktop_contact_enterprise_total.php"); ?>
				</td>
				<?php
				break;
		}
		?>
		<?php
		?>
	</tr>
</table>
<?
echo $skin->close_simplebloc();
?>
