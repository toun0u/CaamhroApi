<?php
require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
require_once DIMS_APP_PATH.'modules/system/class_address_link.php';

//$tiers = tiers::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'telephone'=>'', ' ORDER BY date_creation '));
$db = dims::getInstance()->getDb();
$lst = array();

$sel = "SELECT 		DISTINCT t.*
		FROM 		".tiers::TABLE_NAME." t
		LEFT JOIN 	".address_link::TABLE_NAME." al
		ON 			al.id_goobject = t.id_globalobject
		WHERE 		(t.telephone = ''
		OR 			al.id_goobject IS NULL)
		AND 		t.id_workspace = :idw
		ORDER BY 	t.date_creation DESC
		LIMIT 		10";
$params = array(
	':idw' => array('type'=>PDO::PARAM_INT, 'value'=>$_SESSION['dims']['workspaceid']),
);
$res = $db->query($sel,$params);
$contacts = array();
while($r = $db->fetchrow($res)){
	$t = new tiers();
	$t->openFromResultSet($r);
	$lst[] = $t;
}

$sel = "SELECT 		DISTINCT c.*
		FROM 		".contact::TABLE_NAME." c
		LEFT JOIN 	".tiersct::TABLE_NAME." lk
		ON 			lk.id_contact = c.id
		LEFT JOIN 	".address_link::TABLE_NAME." al
		ON 			al.id_goobject = c.id_globalobject
		WHERE 		(c.mobile = ''
		OR 			(lk.id_contact IS NULL
		AND 		al.id_goobject IS NULL))
		AND 		c.id_workspace = :idw
		ORDER BY 	c.date_create DESC
		LIMIT 		10";
$params = array(
	':idw' => array('type'=>PDO::PARAM_INT, 'value'=>$_SESSION['dims']['workspaceid']),
);
$res = $db->query($sel,$params);
$contacts = array();
while($r = $db->fetchrow($res)){
	$c = new contact();
	$c->openFromResultSet($r);
	$lst[] = $c;
}

usort($lst,'sortCtTiersByCreate');
$lst = array_reverse($lst);

$nbElems = 0;
$sel = "SELECT 		COUNT(t.id) as nb
	FROM 		".tiers::TABLE_NAME." t
	LEFT JOIN 	".address_link::TABLE_NAME." al
	ON 			al.id_goobject = t.id_globalobject
	WHERE 		(t.telephone = ''
	OR 			al.id_goobject IS NULL)
	AND 		t.id_workspace = :idw
	ORDER BY 	t.date_creation DESC";
$params = array(
':idw' => array('type'=>PDO::PARAM_INT, 'value'=>$_SESSION['dims']['workspaceid']),
);
$res = $db->query($sel,$params);
if($r = $db->fetchrow($res)){
$nbElems += $r['nb'];
}
$sel = "SELECT 		COUNT(c.id) as nb
	FROM 		".contact::TABLE_NAME." c
	LEFT JOIN 	".tiersct::TABLE_NAME." lk
	ON 			lk.id_contact = c.id
	LEFT JOIN 	".address_link::TABLE_NAME." al
	ON 			al.id_goobject = c.id_globalobject
	WHERE 		(c.mobile = ''
	OR 			(lk.id_contact IS NULL
	AND 		al.id_goobject IS NULL))
	AND 		c.id_workspace = :idw
	ORDER BY 	c.date_create DESC";
$params = array(
':idw' => array('type'=>PDO::PARAM_INT, 'value'=>$_SESSION['dims']['workspaceid']),
);
$res = $db->query($sel,$params);
if($r = $db->fetchrow($res)){
$nbElems += $r['nb'];
}

?>
<div class="companies_recently">
<h2 class="h1_zone_companies_recently"><?= $_SESSION['cste']['_INCOMPLETE_RECORDS']." (".$nbElems.")"; ?></h2>
<div style="max-height:400px;overflow: auto;" class="incomplet-records">
	<?php
	$lst = array_slice($lst, 0, 10);
	foreach($lst as $elem){
			switch ($elem->getid_object()) {
				case tiers::MY_GLOBALOBJECT_CODE:
					//$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/missing_infos_tiers.tpl.php');
					$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/companies_recently/companies_recently_details.tpl.php');
					break;
				case contact::MY_GLOBALOBJECT_CODE:
					//$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/missing_infos_ct.tpl.php');
					$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/contacts_recently/contacts_recently_details.tpl.php');
					break;
			}
		}
		?>
	</div>
</div>
<script type="text/javascript">
var nbLoad = 10;
var load = false;
$(document).ready(function(){
	$('.incomplet-records').scrollTop(0);
	$('.incomplet-records').scroll(function(){
		if(nbLoad <= <?= $nbElems; ?>){
			if($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight-20){
				if(!load){
					load = true;
					$.ajax({
						type: 'POST',
						url: '<?= dims::getInstance()->getScriptEnv(); ?>',
						data: {
							'dims_op' : 'desktopv2',
							'action' : 'load_incomplete_records',
							'nb' : nbLoad
						},
						dataType: 'html',
						success: function(data){
							$(".incomplet-records").append(data);
							nbLoad += 10;
							load = false;
						}
					});
				}
			}
		}
	});
})
</script>
