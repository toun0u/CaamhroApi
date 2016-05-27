<script type="text/javascript">
	function selectAll(){
			var i = 0;
			while(dims_getelem(i) != false){
					var e = dims_getelem('ent_imp_'+i);
					e.checked = "checked";
					i++;
			}
	}

	function unselectAll(){
			var i = 0;
			while(dims_getelem(i) != false){
					var e = dims_getelem('ent_imp_'+i);
					e.checked = "";
					i++;
			}
	}
</script>
<?php
require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
require_once(DIMS_APP_PATH . "/modules/system/class_tiers_import.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact_import.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");
require_once(DIMS_APP_PATH . "/modules/system/class_tiers_contact.php");
require_once(DIMS_APP_PATH . "/modules/system/class_action.php");

if(!isset($_SESSION['dims']['IMPORT_MISSION']['id_evt'])) $_SESSION['dims']['IMPORT_MISSION']['id_evt'] = '';

$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true, true, false, $_SESSION['dims']['IMPORT_MISSION']['id_evt']);
$_SESSION['dims']['IMPORT_MISSION']['id_evt'] = $id_evt;

$part = dims_load_securvalue('part', dims_const::_DIMS_CHAR_INPUT, true, false, true);
$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true,false);
if($op == false)
	$op = 1;


$event = new action();
$event->open($id_evt);

//echo $op;
//Afin d'empecher le script de s'arreter on enleve les restrictions d'apache
ini_set('max_execution_time',-1);
ini_set('memory_limit','512M');
require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_contact_import_missions_switch.php');

echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT']." ".$_DIMS['cste']['_DIMS_PARTICIP']." : ".$event->fields['libelle'],'width:100%;float:left;clear:none;','','');
?>
<table style="text-align:center;">
	<tr>
		<?php if($op == 1 && (!isset($_FILES['srcfilect']) || empty($_FILES['srcfilect']['name']))) {
			?>
			<td>
				<span style="font-weight:bold;font-size:16px;">1</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?></span>
			</td>
			<?php
		}else{?>
			<td>
				<span style="font-weight:bold;font-size:16px;">1</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_DOWNLOAD_FILE']; ?>
			</td>
		<?php }

		if($op == 1 && isset($_FILES['srcfilect']) && !empty($_FILES['srcfilect']['name'])) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_KNOWN_CONTACTS'];?></span>
			</td>
			<?php
		}elseif($op==1) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_KNOWN_CONTACTS'];?>
			</td>
			<?php
		}else {
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">2</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_KNOWN_CONTACTS'];?>
			</td>
			<?php
		}

		if($op == 2) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'];?></span>
			</td>
			<?php
		}elseif($op > 3) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'];?>
			</td>
			<?php
		}else {
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">3</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'];?>
			</td>
			<?php
		}

		if($op == 4) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?></span>
			</td>
			<?php
		}elseif($op > 4) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?>
			</td>
			<?php
		}else {
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">4</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_UNKNOWN_CONTACTS'];?>
			</td>
			<?php
		}

		if($op == 5) {
			?>
			<td><img src="./common/img/import_fleche.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">5</span><br/>
				<span style="font-weight:bold;"><?php echo $_DIMS['cste']['_IMPORT_SUMMARY'];?></span>
			</td>
			<?php
		}else {
			?>
			<td><img src="./common/img/import_fleche_grise.png" height="40px" width="40px" alt="=>"/></td>
			<td>
				<span style="font-weight:bold;font-size:16px;">5</span><br/>
				<?php echo $_DIMS['cste']['_IMPORT_SUMMARY'];?>
			</td>
			<?php
		}
		?>
	</tr>
</table>
<?php
echo $skin->close_simplebloc();

if($op != "")
	$step = "STEP".$op;
else
	$step = "NOSTEP";


echo $skin->open_simplebloc($_DIMS['cste']['_LABEL_IMPORT_MISSION_'.$step],'width:100%;float:left;clear:none;','','');

echo $content_contact_import;

echo $skin->close_simplebloc();
?>
