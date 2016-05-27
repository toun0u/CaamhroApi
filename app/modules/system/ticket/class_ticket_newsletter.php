<?php

/**
 * Description of class_ticket_newsletter
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class ticket_newsletter extends ticket{
    const TYPE_TICKET_NEWSLETTER = _TICKET_TYPE_NEWSLETTER ;

    public function getIdDocfile() {
        return $this->getAttribut("newsletter_id_docfile", self::TYPE_ATTRIBUT_KEY);
    }

    public function setIdDocfile($id_docfile, $save = false){
        $this->setAttribut("newsletter_id_docfile", self::TYPE_ATTRIBUT_KEY, $id_docfile, $save);
    }
}
