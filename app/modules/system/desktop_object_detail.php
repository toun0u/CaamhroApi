<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo $skin->open_simplebloc("Profile", 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','26px', '26px', '-15px', '-5px', '', '', '');

if (!isset($detailobject_description) || $detailobject_description=="") {
?>
<table style="width:100%">
    <tr>
        <td><? echo $_DIMS['cste']['_DIMS_LABEL']; ?></td>
        <td><? echo $_SESSION['dims']['current_object']['label']; ?>
    </tr>
    <tr>
        <td><? echo $_DIMS['cste']['_AUTHOR']; ?></td>
        <td>
        <?
                $usr= new user();
                $usr->open($_SESSION['dims']['current_object']['id_user']);
                echo $usr->fields['firstname']." ".$usr->fields['lastname'];
        ?>
    </tr>
    <tr>
        <td><? echo $_DIMS['cste']['_DIMS_DATE_MODIFY']; ?></td>
        <td>
        <?
        $datvar=dims_timestamp2local($_SESSION['dims']['current_object']['timestp_modify']);
        $chdate=$datvar['date'];
        echo $chdate;
        ?>
        </td>
    </tr>
</table>

<?

}
else {
    echo $detailobject_description;
}

echo "<div style='clear:both;width:100%;float:left;margin:10px;'>";
//dims_print_r($_SESSION['dims']['current_object']['cmd']);die();
//echo "<table width=\"100%\" align=\"right\"><tr>";
// construction de la liste des commandes possibles � faire
foreach ($_SESSION['dims']['current_object']['cmd'] as $elem) {
	if (!isset($elem['script'])) $elem['script']="";
	if (!isset($elem['link']) || $elem['link']=="") $elem['link']="javascript:void(0);";
	//if (!isset($elem['width']) || $elem['width']=="") $elem['width']="width:70px;";
	echo dims_create_button($elem['name'],$elem['src'],$elem['script'],'','',$elem['link'],'','');
	//$desktopObject[$idDesktopObject++]=$elem;
}
echo "</div>";
echo $skin->close_simplebloc();

?>
