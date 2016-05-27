<?php
if (empty($op)) $op = 'list';

switch ($op) {
	case 'list';
		include_once './common/modules/catalogue/public_clients_users_list.php';
		break;
}
