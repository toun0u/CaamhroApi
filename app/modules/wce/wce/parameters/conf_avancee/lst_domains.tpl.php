<div class="title_h3">
	<h3><? echo $_SESSION['cste']['_DOMAIN_NAMES']; ?></h3>
</div>
<div class="lien_modification">
	<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_EDIT_DOMAIN; ?>">
		<? echo $_SESSION['cste']['_DIMS_LABEL_DOMAIN_ADD']; ?>
		<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_ajout.png'); ?>" />
	</a>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;margin-bottom: 30px;">
	<tr>
		<td class="title_table_accueil">
			<? echo $_SESSION['cste']['_DIMS_LABEL_DOMAIN']; ?>
		</td>
		<td class="title_table_mobile">
			Version mobile
		</td>
		<td class="title_table_mobile">
			<? echo $_SESSION['cste']['_DIMS_LABEL_SSLACCESS']; ?>
		</td>
		<td class="title_table">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$db = dims::getInstance()->db;
	// Modification pour filtre sur les espaces que l'on peut administrer
	$params = array(':id_workspace1'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
					':id_workspace2'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT));
	$sql="	SELECT 		d.*,
						COUNT(distinct( wd1.id_workspace)) as cpte1,
						COUNT(distinct( wd2.id_workspace)) as cpte2 ,
						COUNT(distinct(w1.id)) as cpte1actif,
						COUNT(distinct(w2.id)) as cpte2actif
			FROM 		dims_domain as d
			LEFT JOIN	dims_workspace_domain as wd1
			ON			d.id = wd1.id_domain
			AND 		(wd1.access=0 OR wd1.access=2)
			INNER JOIN	dims_workspace as w1
			ON 			w1.id=wd1.id_workspace
			AND			w1.admin=1 AND wd1.id_workspace=:id_workspace1
			LEFT JOIN	dims_workspace_domain as wd2
			ON			d.id=wd2.id_domain
			AND			(wd2.access=1 OR wd2.access=2)
			INNER JOIN	dims_workspace as w2
			ON			w2.id=wd2.id_workspace
			AND			w2.web=1 AND wd2.id_workspace=:id_workspace2
			";

	if (isset($search_domain) && $search_domain!='') {
		$sql.=" WHERE d.domain like :dom ";
		$params[':dom'] = array('value'=>"%".$search_domain."%",'type'=>PDO::PARAM_STR);
	}

	$sql.=  " GROUP BY	d.id";

	$result = $db->query($sql,$params);
	$trl = 'class="table_ligne1"';
	while($r = $db->fetchrow($result)){
		?>
		<tr <? echo $trl; ?>>
			<td>
				<? echo $r['domain']; ?>
			</td>
			<td class="puce">
				<?
				if ($r['mobile']){
					?>
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/puce_verte.png'); ?>" />
					<?
				}else{
					?>
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/puce_rouge.png'); ?>" />
					<?
				}
				?>
			</td>
			<td class="puce">
				<?
				if ($r['ssl']){
					?>
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/puce_verte.png'); ?>" />
					<?
				}else{
					?>
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/puce_rouge.png'); ?>" />
					<?
				}
				?>
			</td>
			<td class="actions">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_EDIT_DOMAIN."&id=".$r['id']; ?>">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_modif.png'); ?>" />
				</a>
				<a onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_CONF_DEL_DOMAIN."&id=".$r['id']; ?>','<? echo $_SESSION['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']; ?>');" href="javascript:void(0);">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_supp.png'); ?>" />
				</a>
			</td>
		</tr>
		<?
		$trl = ($trl == '')?'class="table_ligne1"':'';
	}
	?>
</table>