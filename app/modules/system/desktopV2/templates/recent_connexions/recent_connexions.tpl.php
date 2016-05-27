<?php
$i = 0;
foreach ($lstConn as $connection){
	if ($i == _DESKTOP_V2_LIMIT_CONNEXION){
		?>
		<div class="more_connexions">
		<?
	}
	$i++;
	$connection->display(_DESKTOP_TPL_LOCAL_PATH.'/recent_connexions/recent_connexion_ligne.tpl.php');
}
if ($i > _DESKTOP_V2_LIMIT_CONNEXION){
	?>
		</div>
		<div class="recent_connexions_ligne_see_more">
			<span>
				<a onclick="javascript:flipFlopConnexion();" href="javascript:void(0);">
					See more ...
				</a>
			</span>
		</div>
	<?
}
?>
