<?php

class sharefile_user extends dims_data_object {
	const TABLE_NAME = 'dims_mod_sharefile_user';

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public static function openByLinkContact($id_share, $id_contact) {
		$db = dims::getInstance()->getDb();

		$sql = 'SELECT *
				FROM '.self::TABLE_NAME.'
				WHERE id_share = :idshare
				AND id_contact = :idcontact';

		$res = $db->query($sql, array(':idshare' => $id_share, ':idcontact' => $id_contact));

		$share_user = new self();
		if($db->numrows($res)) {
			$data = $db->fetchrow($res);
			$share_user->openFromResultSet($data);
		}
		else {
			$share_user->init_description();
			$share_user->fields['id_share'] = $id_share;
			$share_user->fields['id_contact'] 	= $id_contact;
			$share_user->fields['view']=0;
			$share_user->fields['active']=1;
		}

		return $share_user;
	}

	public static function openByLinkUser($id_share, $id_user) {
		$db = dims::getInstance()->getDb();

		$sql = 'SELECT * FROM '.self::TABLE_NAME.' WHERE id_share = :idshare AND id_user = :iduser';

		$res = $db->query($sql, array(':idshare' => $id_share, ':iduser' => $id_user));

		$share_user = new self();
		if($db->numrows($res)) {
			$data = $db->fetchrow($res);
			$share_user->openFromResultSet($data);
		}
		else {
			$share_user->init_description();
			$share_user->fields['id_share'] = $id_share;
			$share_user->fields['id_user'] 	= $id_user;
			$share_user->fields['view']=0;
			$share_user->fields['active']=1;
		}

		return $share_user;
	}
}
