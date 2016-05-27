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

require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_mailinglist_mail.php';

class mailinglist_send extends dims_data_object {
    function mailinglist_send() {
        $this->articles=array();
        $this->mails=array();

        parent::dims_data_object('dims_mailinglist_send');
    }

    public function addArticle($articleid) {
        $this->articles[$articleid] = $articleid;
    }

    public function addMail($mail) {
        $this->mails[$mail] = $mail;
    }

    public function save() {
        parent::save();

        $global_object = new dims_globalobject();
        $global_object->open($this->fields['id_globalobject']);

        $id_globalobject_article = array();
        foreach($this->articles as $idArticle) {
            $article = new wce_article();
            $article->open($idArticle);

            $id_globalobject_article[] = $article->fields['id_globalobject'];
        }

        $global_object->addLink($id_globalobject_article);

        $id_globalobject_mail = array();
        foreach($this->mails as $mails) {
            $mail = mailinglist_mail::openByMail($mails);
            $id_globalobject_mail[] = $mail->fields['id_globalobject'];
        }
        $global_object->addLink($id_globalobject_mail);

        return $this->fields['id'];
    }

    private $articles;
    private $mails;
}
