<?php
/*
 *      Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

class mailinglist_mail extends dims_data_object {
    function mailinglist_mail() {
        parent::dims_data_object('dims_mailinglist_mail');
    }

    public static function openByMail($mail_string) {
        global $dims;

		$params=array();
		$params[':mail_string']=$mail_string;
        $sql = 'SELECT id FROM dims_mailinglist_mail WHERE mail = :mail_string';

        $res_mail = $dims->db->query($sql,$params);

        $mail = new mailinglist_mail();

        if($dims->db->numrows($res_mail) == 1) {
            $info = $dims->db->fetchrow($res_mail);
            $mail->open($info['id']);
        }
        else {
            $mail->init_description();
            $mail->setugm();
            $mail->fields['mail'] = $mail_string;
            $mail->save();
        }

        return $mail;
    }
}
