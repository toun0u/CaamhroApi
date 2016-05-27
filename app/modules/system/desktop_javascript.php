<script type="text/javascript">
		function checkAllSearch(nbsearch) {
			for (i = 0; i < nbsearch; i++)
				document.getElementById("selsearch"+i).checked = true;
		}

		function uncheckAllSearch(nbsearch) {
			for (i = 0; i < nbsearch; i++)
				document.getElementById("selsearch"+i).checked = false;
		}

		function validCommand(event) {
			var elem=document.getElementById('op').selectedIndex;
			if (elem>0) {
				switch(elem) {
					case 1:
						dims_showcenteredpopup("",700,500,'dims_popup');
						dims_xmlhttprequest_todiv('admin.php','dims_op=add_globaltag','','dims_popup');
						break;
				}
			}

		}

		function desktopViewDetailContact(action) {
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=activities&action='+action+'&elem_id=0&desktopobjectheight='+desktopheightobject,"||",'object_onglet','object_content');
		}

		function desktopViewDetail(type,elem_id) {
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type='+type+'&elem_id='+elem_id+'&desktopobjectheight='+desktopheightobject,"||",'object_onglet','object_content');
		}

		function desktopViewPreview() {
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=preview&elem_id=0&desktopobjectheight='+desktopheightobject,"||",'object_onglet','object_content');
		}
		function desktopVcard() {
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=vcard&elem_id=0&desktopobjectheight='+desktopheightobject,"||",'object_onglet','object_content');
		}
		function desktopViewComment() {
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=comments&elem_id=0&desktopobjectheight='+desktopheightobject,"||",'object_onglet','object_content');
		}

		function desktopViewTag(ctsearch) {
			if (ctsearch==null) ctsearch='';
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=viewtag&elem_id=0&desktopobjectheight='+desktopheightobject+'&ct_filter='+ctsearch,"||",'object_onglet','object_content');
		}

		function desktopViewDoc(ctsearch) {
			if (ctsearch==null) ctsearch='';
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=viewdoc&elem_id=0&desktopobjectheight='+desktopheightobject+'&ct_filter='+ctsearch,"||",'object_onglet','object_content');
		}

		function desktopViewBiens(ctsearch) {
			if (ctsearch==null) ctsearch='';
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=viewimmo&elem_id=0&desktopobjectheight='+desktopheightobject+'&ct_filter='+ctsearch,"||",'object_onglet','object_content');
		}

		function desktopViewEventContact(ctsearch) {
			if (ctsearch==null) ctsearch='';
			dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=refreshDesktop&block_id=3&type=vieweventcontact&elem_id=0&desktopobjectheight='+desktopheightobject+'&ct_filter='+ctsearch,"||",'object_onglet','object_content');
		}
		var desktopheightobject=0;

		function resizeHome () {
			if (window.innerHeight > 0) {
				var height 	= (window.innerHeight-130);
			} else {
				var height 	= (document.documentElement.clientHeight-130);
			}

			var height2 	= ((height/2)-19)+100;
			var height2bis 	= ((height/2)-19)-100;

			if (height < 440) {
				height 		= 438;
			}

			if (height2 < 200) {
				height2 	= 200;
			}

			//$('search_content').style.height 	= height2+'px';
			//if ($('searchcontentresult')!=null)
			//	$('searchcontentresult').style.height 	= (height2-145)+'px';
			//$('monitors_content').style.height 	= height2bis+'px';
			//$('object_content').style.height 	= height+'px';
			desktopheightobject=height;
		}



	//loader.add(loadDesktop);

	//window.onresize 											= function () {
	//	resizeHome();
	//};

	$(document).ready(function(){
	 //  resizeHome();

		//$('search_content').innerHTML 	= "<table width=\"100%\" height=\"400\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
		//$('monitors_content').innerHTML 	= "<table width=\"100%\" height=\"400\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
		//$('object_content').innerHTML 	= "<table width=\"100%\" height=\"400\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
		if ($('searchBar_obj_bar')!=null) {
                        var elem=document.getElementById('searchBar_obj_bar');

			elem.focus();

                        //elem.selectionStart=elem.length;
                        //elem.selLength=0;
		}
		//initializeDesktop(0);
		<?
		//if (isset($_SESSION['dims']['submenumain']) && ($_SESSION['dims']['submenumain']==dims_const::_DIMS_SUBMENU_SEARCH || $_SESSION['dims']['submenumain']==dims_const::_DIMS_SUBMENU_NEWS)) {

		//}
		?>
	 });

	function initializeDesktop(i) {
		switch(i) {
			case 0:
				dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=initDesktop&block_id='+i,"||",'search_onglet','search_content');
				initializeDesktop(i+2);
				break;
			case 1:
				dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=initDesktop&block_id='+i,"||",'monitors_onglet','monitors_content');
				initializeDesktop(i+1);
				break;
			case 2:
				dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=initDesktop&block_id='+i,"||",'object_onglet','object_content');
				initializeDesktop(i+1);
				break;
		}
	}

/*
	function refreshDesktop(block_id,elem_id,variable) {
		var ch="";

		if (variable!=null) {
			ch=variable;
		}

		switch(block_id) {
			case 0:
				//$('resultsearchbloc').style.visibility="hidden";
				//$('resultsearchbloc').style.display="none";
				if (elem_id==7) {
					if ($('searchBar_obj_bar')!=null) {
						$('searchBar_obj_bar').focus();
					}
				}
				dims_xmlhttprequest_todiv('admin-light.php','dims_op=refreshDesktop&block_id='+block_id+'&submenumain='+elem_id+ch,"||",'search_onglet','search_content');
				break;
			case 1:
				dims_xmlhttprequest_todiv('admin-light.php','dims_op=refreshDesktop&block_id='+block_id+'&submenumonitor='+elem_id+ch,"||",'monitors_onglet','monitors_content');
				break;
			case 2:
				dims_xmlhttprequest_todiv('admin-light.php','dims_op=refreshDesktop&block_id='+block_id+'&submenuobject='+elem_id+ch,"||",'object_onglet','object_content');
				break;
		}
		current_elem_id=elem_id;
	}*/

	function validDesktop(result) {
		$('object_content').innerHTML=result;
	}
</script>
