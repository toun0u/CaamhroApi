<style type="text/css">
    <!--
      div#contentsearch {
      	width: 100%;
      	height: 400px;
      	overflow: auto;
      	border: 0px;
      }
    -->
    </style>
<?
$groupid=$_SESSION['dims']['workspaceid'];
$moduleid = $_SESSION['dims']['moduleid'];

?>
<script language="javascript">

function detail_workspace_group(idgroup,qte)
{
	var ts=document.getElementById('txtsearch'+idgroup);
	var val=ts.value;

	//if (qte>100 && val=="")
	//{
	//	alert("Veuillez précisez un nom dans la zone de recherche");
	//	ts.focus();
	//}
	//else
	dims_xmlhttprequest_todiv('<? echo $scriptenv ?>','dims_op=search_list_group&text='+val.replace("'","\'")+'&idgroup='+idgroup,'','contentsearch'+idgroup);

	//dims_getxmlhttp('<? echo $scriptenv ?>','search_list_group','&text='+val.replace("'","\'")+'&idgroup='+idgroup,0,'contentsearch'+idgroup);
}

function maskdiv(element)
{
	var elem = document.getElementById(element);
	elem.innerHTML="";
}

</script>


<?
/*
if (isset($urlredirect) && $urlredirect!="")
{
	echo $skin->open_simplebloc(_DIMS_MANAGESHARE,'80%');
}
else
{

	echo $skin->open_simplebloc(_DIMS_MANAGESHARE,'100%');

}
*/
?>

<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td align="center" colspan="3">
	</td>
</tr>

<tr>
	<td>
		<div id="contentsearch">
		<?
		dims_share_searchlist();
		?>
		</div>
	</td>
</tr>
</table>

<?
//echo $skin->close_simplebloc();

/*
if (isset($urlredirect) && $urlredirect!="")
	echo $skin->open_simplebloc(_DIMS_AUTHORIZEDUSERS,'80%');
else

echo $skin->open_simplebloc(_DIMS_AUTHORIZEDUSERS,'100%');
*/
?>
<table>
	<tr>
		<td align="center">
			<div id="contentattach">
			<?
			dims_share_getelements();
			?>
			</div>
		</td>
	</tr>
</table>
<?
//echo $skin->close_simplebloc();

if (isset($urlredirect) && $urlredirect!="")
	echo "<TABLE WIDTH=\"80%\" CELLPADDING=\"0\" CELLSPACING=\"0\" valign=\"top\">";
else
	echo "<TABLE WIDTH=\"100%\" CELLPADDING=\"0\" CELLSPACING=\"0\" valign=\"top\">";

?>
<table>
	<tr>
		<td align="center">
		<?
		if (isset($urlredirect) && $urlredirect!="")
			echo "<table width=\"100%\"><tr><td align=center  valign=\"top\"><a href=\"".str_replace(";","&",$urlredirect)."\">".$_DIMS['cste']['_DIMS_SHARE_VALID']."&nbsp;<img border=\"0\" src=\"./common/modules/system/img/ico_yes.gif\"></a></td></tr></table>";
		else
		{
			echo "<input type=\"button\" class=\"flatbutton\" value=\"Fermer\" onclick=\"javascript:dims_hidepopup();\">";
		}
		?>
		</td>
	</tr>
</table>

<?
die();
?>