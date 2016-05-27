<?php

if (
        ( $_SESSION['desktopv2']['concepts']['rech_type'] == dims_const::_SYSTEM_OBJECT_CONTACT || $_SESSION['desktopv2']['concepts']['rech_type'] == 0 )
        && ( $_SESSION['desktopv2']['concepts']['contact_search'] == '' || stristr($this->fields['firstname'].' '.$this->fields['lastname'], $_SESSION['desktopv2']['concepts']['contact_search']) )
) {
        $this->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/contact.tpl.php');
        $_SESSION['desktopv2']['concepts']['contacts']++;
}

?>
