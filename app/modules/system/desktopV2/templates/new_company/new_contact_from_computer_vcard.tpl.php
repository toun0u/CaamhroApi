<?
global $desktop;
$tmpPhotoComp = DIMS_TMP_PATH . '/uploadsjquery/'.session_id();
?>
<script type="text/javascript">
function openDisplayVcardFromComputer(path){
	var id_popup = dims_openOverlayedPopup(700,400);
	dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=displayInfoFromVcf&path='+path+'&id_popup='+id_popup,'','p'+id_popup);
}
</script>
<input type="file" name="opp_vcard" class="vcard" id="opp_vcard" />
