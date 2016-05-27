<?php
require_once DIMS_APP_PATH . "/modules/system/class_metafield.php";
require_once DIMS_APP_PATH . "/modules/system/class_business_metacateg.php";
$type = $this->getid_object();
$continue = false;
switch($type){
	case dims_const::_SYSTEM_OBJECT_TIERS:
		$continue = true;
		break;
	case dims_const::_SYSTEM_OBJECT_CONTACT:
		$continue = true;
		break;
}
if($continue){
	$sql = "SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		".metafield::TABLE_NAME." as mf
			INNER JOIN	".mb_field::TABLE_NAME." as mb
			ON			mb.id=mf.id_mbfield
			LEFT JOIN	".business_metacateg::TABLE_NAME." as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object = :idobject
			AND			mf.used=1
			ORDER BY	mc.position, mf.position";
	$params = array(
		":idobject" => $type,
	);
	$db = dims::getInstance()->getDb();
	$res = $db->query($sql,$params);
	if($db->numrows($res)){
		$buildFields = array();
		while ($r = $db->fetchrow($res)){
			$buildFields[$r['id_cat']]['label'] = $r['categlabel'];
			$buildFields[$r['id_cat']]['fields'][] = array(
				'id_mtf' => $r['id'],
				'namefield' => $r['namefield'],
				'titlefield' => $r['titlefield'],
				'name' => $r['name'],
				'type' => $r['type'],
				'format' => $r['format'],
				'values' => $r['values'],
				'maxlength' => $r['maxlength'],
				'option_needed' => $r['option_needed'],
			);
		}
		?>
		<h2 class="contact">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>gfx/common/already_in_search2.png" />
			<span>
				<?= $_SESSION['cste']['_DIMS_EVT_INFO_COMPL'] ; ?>
			</span>
		</h2>
		<div>
			<?php foreach($buildFields as $b): ?>
				<?php if(!empty($b['fields'])): ?>
					<h3><?= empty($b['label'])?"Inconnue":$b['label']; ?></h3>
					<ul>
					<?php foreach($b['fields'] as $f): ?>
						<li>
							<?php $label = isset($_SESSION['cste'][$f['titlefield']])?$_SESSION['cste'][$f['titlefield']]:$f['name']; ?>
							<label><?= $label; ?> : </label>
							<?php
							switch($f['type']) {
								case 'radio':
									if ($this->fields[$f['namefield']] == 'on')
										echo '<img src="./common/img/checkdo.png" alt="'.$label.'" title="'.$label.'" />';
									else
										echo'<img src="./common/img/check.png" alt="'.$label.'" title="'.$label.'" />';
									break;
								default:
									if($f['option_needed']){
										// TODO: gérer valeur manquante
									}
									echo $this->fields[$f['namefield']];
									break;
							}
							?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
