<?php
if (
        ( $_SESSION['desktopv2']['concepts']['rech_type'] == dims_const::_SYSTEM_OBJECT_TIERS || $_SESSION['desktopv2']['concepts']['rech_type'] == 0 )
        && ( $_SESSION['desktopv2']['concepts']['contact_search'] == '' || stristr($this->fields['intitule'], $_SESSION['desktopv2']['concepts']['contact_search']) )
) {
        $this->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/tiers.tpl.php');
        $_SESSION['desktopv2']['concepts']['contacts']++;
}

?>
