<script language="javascript">
$( function(){
  dims_xmlhttprequest_todiv('admin.php','dims_op=view_skins','','content');

  $("#trie_skin button").click( function() {
    dims_xmlhttprequest_todiv('admin.php','dims_op=view_skins&sql='+$(this).val()+'','','content');
    $("#trie_skin [class*='ui-state']").removeClass('ui-state-active');
    $(this).addClass('ui-state-active');
  });

  $("#new_skin").click( function (){
    dims_xmlhttprequest_todiv('admin.php','dims_op=add_skin_form','','content');
    $("#trie_skin button").removeClass('ui-state-active');
    $(this).addClass('ui-state-active');
  });

});
function change_skin(skin){
dims_xmlhttprequest('admin.php','dims_op=update_skin&skin='+skin);
 location.reload(true);
}
function delete_skin(skin){
	dims_xmlhttprequest('admin.php','dims_op=delete_skin&skin='+skin);
	location.reload(true);
}
</script>
<div id="trie_skin" class="ui-buttonset">
  <button class="ui-button ui-widget ui-state-default ui-corner-left ui-button-text-only ui-state-active" value="1=1">
    <span class="ui-button-text">Tous</span>
  </button>
  <button class="ui-button ui-widget ui-state-default ui-button-text-only" value="`id_user`=0">
    <span class="ui-button-text">Thèmes Jquery UI</span>
  </button>
  <button class="ui-button ui-widget ui-state-default ui-button-text-only" value="`id_user`!=0">
    <span class="ui-button-text">Thèmes personnalisés</span>
  </button>
  <button class="ui-button ui-widget ui-state-default ui-corner-right ui-button-text-only" value="`id_user`=<? echo $_SESSION['dims']['userid']; ?>">
    <span class="ui-button-text">Vos créations</span>
  </button>
  <? echo dims_create_button("Nouveau","plus","","new_skin","float:right;",""); ?>
</div>
<?
?>

<div id="content"></div>
