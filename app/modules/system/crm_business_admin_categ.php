<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo $skin->open_simplebloc($_DIMS['cste']['_CATEGORIES'],'width:100%');
	?>
	<form name='filterform' action='<? echo $scriptenv; ?>' method='Post'>
	<?
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("op");
    ?>
    <input type='Hidden' name='op' VALUE='savecategposition'>

	<table cellpadding="2" cellspacing="1" style="width:100%;background:#FFFFFF;">
         <tr>
			<td valign="top" colspan="3">
                <?
                echo "<a href=\"$scriptenv?op=add_metacateg\"><img border=\"0\" src=\"./common/img/add.gif\"/>".$_DIMS['cste']['_DIMS_ADD']." ".strtolower($_DIMS['cste']['_RSS_LABEL_CATEGORY'])."</a>";
                ?>
            </td>
            <td>
            <?
                echo "<a href=\"$scriptenv?op="._BUSINESS_CAT_ADMIN."\"><img border=\"0\" src=\"./common/img/undo.gif\"/>".$_DIMS['cste']['_DIMS_BACK']."</a>";
                ?>
            </td>
        </tr>
		<tr>
			<td valign="top">
				<? echo $_DIMS['cste']['_DIMS_LABEL']; ?>
			</td>
			<td>
				<? echo $_DIMS['cste']['_POSITION']; ?>
			</td>
			<td><? echo $_DIMS['cste']['_DIMS_LOCKED']; ?>
			</td>
			<td>
				<? echo $_DIMS['cste']['_MODIFY'].' / '.$_DIMS['cste']['_DELETE']; ?>
			</td>
		</tr>
<?php
$res=$db->query("select * from dims_mod_business_meta_categ order by position");
$cpteused=$db->numrows($res);

if ($cpteused>0) {
    $color="#FFFFFF";

    while ($value=$db->fetchrow($res)) {
        if ($color=="#FFFFFF") $color="#FFFFDE";
        else $color="#FFFFFF";

        echo "<tr bgcolor=\"$color\"><td>".$value['label']."</td>";
        echo "<input type=\"hidden\" name=\"use".$value['id']."\" value=\"".$value['id']."\">";
        $token->field("use".$value['id']);
        // on affiche la position
        echo "<td><select name=\"position".$value['id']."\" onchange=\"document.filterform.submit();\">";
        $token->field("position".$value['id']);
        for ($j=1;$j<=$cpteused;$j++) {
            if ($value['position']==$j) $selected="selected";
            else $selected ="";

            echo "<option $selected value=\"".$j."\">".$j."</option>";
        }
        echo "</select></td>";

		if ($value['admin']==1) {
			$delete="";
			echo "<td>".$_DIMS['cste']['_DIMS_YES']."</td>";
		}
		else  {
			$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete_metacateg&id_metacateg=".$value['id']."','".$_DIMS['cste']['_DIMS_CONFIRM']."')\"><img src=\"./common/img/delete.png\" align=\"middle\" border=\"0\"></a>";
			echo "<td>".$_DIMS['cste']['_DIMS_NO']."</td>";
		}

		$modify =  "<a href=\"$scriptenv?op=admin_categ&id_metacateg=".$value['id']."\"><img src=\"./common/img/edit.gif\" align=\"middle\" border=\"0\"></a>" ;


        echo "<TD ALIGN=\"CENTER\">$modify&nbsp;&nbsp;$delete</TD>";
        echo "</tr>";
    }
}

?>
    </table>
    <?
        $tokenHTML = $token->generate();
        echo $tokenHTML;
    ?>
    </form>
<?php
echo $skin->close_simplebloc();

if ($id_metacateg>0 || $op=="add_metacateg") {
    $meta_categ = new business_metacateg();
    if ($id_metacateg>0) $meta_categ->open($id_metacateg);
    else $meta_categ->init_description();
    if ( $op=="add_metacateg") echo $skin->open_simplebloc("Ajout d'une cat&eacute;gorie ",'50%');
    else echo $skin->open_simplebloc("Modification de la cat&eacute;gorie ".$meta_categ->fields['label'],'50%');
   ?>
    <form name='filterform' action='<? echo $scriptenv; ?>' method='Post'>
    <?
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("op");
        $token->field("id_metacateg");
        $token->field("metacateg_label");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
    ?>
	<input type='Hidden' name='op' VALUE='savecateg'>
    <input type='Hidden' name='id_metacateg' VALUE='<? echo $meta_categ->fields['id'];?>'>
	<table cellpadding="2" cellspacing="1" width="50%">
		<tr>
			<td valign="top">
				<?=$_SESSION['cste']['CATEGORY_NAME']?>
			</td>
			<td >
				<input type="text" name="metacateg_label" value="<? echo $meta_categ->fields['label'];?>">
			</td>
		</tr>
        <tr>
            <TD ALIGN="RIGHT" COLSPAN="2">
				<INPUT TYPE="Submit" class="flatbutton" VALUE="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
			</TD>
        </tr>
      </table>
     <?php
    echo $skin->close_simplebloc();
}
?>
