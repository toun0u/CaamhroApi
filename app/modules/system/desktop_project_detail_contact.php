<script language="javascript">
	var timersearch;

	function upKeysearch(type) {
		clearTimeout(timersearch);
		timersearch = setTimeout("execSearchLink('"+type+"')", 500);
	}

	function execSearchLink(type) {
		clearTimeout(timersearch);

        var nomsearch = dims_getelem('search_ct').value;
        var divtoaffich = dims_getelem('searchArea');

        if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "op=search_ct&search="+nomsearch+"&type="+type, "", 'searchArea' );
            divtoaffich.style.display = "block";
        }
	}

    function askmail(idCt,type) {
        var mail;
        mail = window.prompt("<?php echo $_DIMS['cste']['_DIMS_PROJECTS_CT_NO_MAIL']; ?>","");
        if(mail!=null && mail!='') {
			document.location.href="admin.php?op=add_ct&type="+type+"&id_ct="+idCt+"&mail="+mail;
        }
    }
</script>
<?php
echo dims_create_button($_DIMS['cste']['_ADD_CT'],"./common/img/add.gif","javascript:dims_switchdisplay('attach_ct');","","");
//echo '<a href="Javascript: void(0);" onclick="Javascript: dims_switchdisplay(\'attach_ct\');">';
//echo $_DIMS['cste']['_ADD_CT'];
//echo '</a>';

echo '<div id="attach_ct" style="display: none; margin: 5px;">';
echo '<p>'.$_DIMS['cste']['_SEARCH'];
if(isset($type_rat) && $type_rat == 'phase') {
	echo '&nbsp;<input type="text" id="search_ct" name="search_ct"  onkeyup="javascript:upKeysearch(\'phase\');" />';
}
elseif(isset($type_rat) && $type_rat == 'task') {
	echo '&nbsp;<input type="text" id="search_ct" name="search_ct"  onkeyup="javascript:upKeysearch(\'task\');" />';
}
else {
	echo '&nbsp;<input type="text" id="search_ct" name="search_ct"  onkeyup="javascript:upKeysearch(\'pers\');" />';
}
echo '</p>';
echo '<div id="searchArea" style="overflow: auto; display: block; max-height: 130px;">';
echo '</div>';
echo '</div>';
?>
