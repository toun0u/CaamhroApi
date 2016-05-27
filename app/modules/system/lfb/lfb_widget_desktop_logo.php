<table width="100%" cellpadding="2" cellspacing="0">
	<tr>
		<td align="center">
			<img src="./common/img/logo/LFB.jpg"/>
		</td>
	</tr>
	<tr>
		<td align="center">
			<?php
				switch($_SESSION['dims']['currentworkspace']['label']) {
					case 'Board of Economic Developement':
						echo '<img src="./common/img/logo/bed.jpg"/>';
						break;
					case 'CASES':
						echo '<img src="./common/img/logo/cases.jpg"/>';
						break;
					case 'Chambre de Commerce':
						echo '<img src="./common/img/logo/cc.jpg"/>';
						break;
				}
			?>
		</td>
	</tr>
</table>