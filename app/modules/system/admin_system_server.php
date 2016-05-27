<?php
echo $skin->open_simplebloc($_DIMS['cste']['_SERVER']);

//bouton d'ajout de boite mail
echo '<div style="margin:10px;text-align:right;">
			<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-secondary" href="'.$scriptenv.'?op=add_server">
					<span class="ui-button-text">'.$_DIMS['cste']['_DIMS_ADD_SERVER'].'</span>
					<span class="ui-button-icon-secondary ui-icon ui-icon-plus"></span>
			</a>
	</div>';
echo '<div style="clear:both;">';


$server = new dims_server();
$server->page_courant = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true);
$server->setPageLimited(true);
$server->setPaginationParams(10, 5, false, $_SESSION['cste']['PAGINATION_FIRST'], $_SESSION['cste']['_LAST'], $_SESSION['cste']['_PREVIOUS_FEM'], $_SESSION['cste']['_NEXT_FEM']);//10 éléments par pages, 3 pages max affichées en même temps
$serverRes = $server->getContent(false, true);
$pages = $server->getPagination();

if($db->numrows($serverRes) > 0) {
	$columns = array();
	$columns['auto'][0] = array('label' => '#');
	$columns['right'][7] = array('label' => '&nbsp;', 'width' => '120');
        $columns['right'][5] = array('label' => $_DIMS['cste']['_DIMS_SSH'], 'width' => '150');
	$columns['right'][5] = array('label' => $_DIMS['cste']['_INFOS_STATE'], 'width' => '150');
	$columns['right'][4] = array('label' => $_DIMS['cste']['_DIMS_PORT'], 'width' => '150');
	$columns['right'][3] = array('label' => $_DIMS['cste']['_LOGIN'], 'width' => '150');

	$columns['right'][2] = array('label' => $_DIMS['cste']['_DIMS_LABEL_ADDRESS'], 'width' => '150');
	$columns['right'][1] = array('label' => $_DIMS['cste']['_DIMS_LABEL_LABEL'], 'width' => '150');

	$c = 0;
	$values = array();

	?>
	<div class="liens_pagination">
		<?php
		if(count($pages) > 1){
		?>
			<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
			<?php
			foreach($pages as $k=>$p){
				if(!empty($p['url'])){
					echo '<a href="'.$p['url'].'" title="'.$p['title'].'">'.$p['label'].'</a>';
				}
				else echo '<span class="current">'.$p['label'].'</span>';
			}
		}
		else{
			?>
			<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> : 1</span>
			<?php
		}
		?>
	</div>
	<?php

	while($result = $db->fetchrow($serverRes))
	{

		$open   = $scriptenv.'?op=modify_server&id_server='.$result['id'];
		$delete = $scriptenv.'?op=delete_server&id_server='.$result['id'];
		$check  = $scriptenv.'?op=check_server&id_server='.$result['id'];

		switch($result['status']) {
			case dims_server::STATE_ACTIVE:
				$icon_server_state = '<img src="modules/system/img/ico_point_green.gif" alt="'.$_SESSION['cste']['_DIMS_SERVER_ACTIVE'].'" title="'.$_SESSION['cste']['_DIMS_SERVER_ACTIVE'].'" />';
				break;
			case dims_server::STATE_INACTIVE_SSH:
				$icon_server_state = '<img src="modules/system/img/ico_point_orange.gif" alt="'.$_SESSION['cste']['_DIMS_SERVER_SSH_ERROR'].'" title="'.$_SESSION['cste']['_DIMS_SERVER_SSH_ERROR'].'" />';
				break;
			default:
			case dims_server::STATE_INACTIVE:
				$icon_server_state = '<img src="modules/system/img/ico_point_red.gif" alt="'.$_SESSION['cste']['_DIMS_SERVER_UNREACHABLE'].'" title="'.$_SESSION['cste']['_DIMS_SERVER_UNREACHABLE'].'" />';
				break;
		}

		if($result['ssh'] == dims_server::SSH_ENABLE)
			$icon_ssh_state = '<img src="img/checkdo.png" alt="'.$_SESSION['cste']['_DIMS_SERVER_ACTIVE'].'" title="'.$_SESSION['cste']['_DIMS_SERVER_ACTIVE'].'" />';
		else
			$icon_ssh_state = '<img src="img/delete.png" alt="'.$_SESSION['cste']['_DIMS_SERVER_SSH_ERROR'].'" title="'.$_SESSION['cste']['_DIMS_SERVER_SSH_ERROR'].'" />';

		$action = '
		<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$open.'" title="'.$_DIMS['cste']['_MODIFY'].'">
			<span class="ui-button-icon ui-icon ui-icon-wrench"></span>
			<span class="ui-button-text">'.$_DIMS['cste']['_MODIFY'].'</span>
		</a>
		<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="javascript:dims_confirmlink(\''.$delete.'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')" title="'.$_DIMS['cste']['_DELETE'].'">
			<span class="ui-button-icon ui-icon ui-icon-trash"></span>
			<span class="ui-button-text">'.$_DIMS['cste']['_DELETE'].'</span>
		</a>
		 <a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" href="'.$check.'" title="'.$_DIMS['cste']['_DIMS_SERVER_CHECK'].'">
			<span class="ui-button-icon ui-icon ui-icon-mail-closed"></span>
			<span class="ui-button-text">'.$_DIMS['cste']['_DIMS_WEBMAIL_CHECK_MANUALLY'].'</span>
		</a>';

		$values[$c]['values'][0] = array('label' => $result['id'], 'style' => '');
		$values[$c]['values'][1] = array('label' => $result['label'], 'style' => 'text-align:center;');
		$values[$c]['values'][2] = array('label' => $result['address'], 'style' => 'text-align:center');
		$values[$c]['values'][3] = array('label' => $result['login'], 'style' => 'text-align:center');
                $values[$c]['values'][4] = array('label' => $result['port'], 'style' => 'text-align:center');
		$values[$c]['values'][5] = array('label' => $icon_server_state, 'style' => 'text-align:center');
		$values[$c]['values'][6] = array('label' => $icon_ssh_state, 'style' => 'text-align:center');
		$values[$c]['values'][7] = array('label' => $action, 'style' => 'text-align:center');

		$values[$c]['link'] = '';
		$values[$c]['style']= '';

		$c++;
	}

	$skin->display_array($columns, $values);

	?>
	<div class="liens_pagination">
		<?php
		if(count($pages) > 1){
		?>
			<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
			<?php
			foreach($pages as $k=>$p){
				if(!empty($p['url'])){
					echo '<a href="'.$p['url'].'" title="'.$p['title'].'">'.$p['label'].'</a>';
				}
				else echo '<span class="current">'.$p['label'].'</span>';
			}
		}
		else{
			?>
			<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> : 1</span>
			<?php
		}
		?>
	</div>
	<?php
}
echo '</div>';
echo $skin->close_simplebloc();
?>
