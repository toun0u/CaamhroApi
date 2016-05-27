var issearching=false;
var etype;
var keytype;
var word_timer;
var resultsearch="";
var sizeresult=65;
var current_elem_id=0;

function searchNewWord2() {
	var word = document.getElementById('searchBar_obj_bar').value;
	if (word=="") {
		dims_xmlhttprequest_todiv('admin-light.php','dims_op=searchnews','','search_content');
	}
	else {
		dims_xmlhttprequest_todivpost('admin-light.php','dims_op=search2&word='+word,'','search_content');
	}
	resizeHome();
	searchNewTag();
	refreshSelectedWord();
}

function searchNewTag() {
	clearTimeout(word_timer);
	word_timer = setTimeout("searchNewTagExec()", 100);
}

function searchNewTagExec() {
	var word = document.getElementById('searchBar_obj_bar').value;
	if (word.length>=2)
	dims_xmlhttprequest_tofunction('index-light.php','dims_op=tag_presearch&word='+word,dims_tag_search_display);
}

function updateDimsSearch(k,idmodule,idobj,idmetafield,sens) {
	var word = document.getElementById('searchBar_obj_bar').value;
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=search2&word='+word+'&kword='+k+'&idmodule='+idmodule+'&idobj='+idobj+'&idmetafield='+idmetafield+"&sens="+sens,'','search_content');
}

function dims_word_keyupExec2(e) {
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field

	switch(e.keyCode) {
		case 13: searchNewWord2();
		default:
			searchNewTag();
			break;
		break;
	}
}

function emptySearch (inputName) {
	//if(document.getElementById(inputName).value == 'Recherche' || document.getElementById(inputName).value == 'Search'){
	document.getElementById(inputName).value = '';
	/*if (current_elem_id!=7) {
		refreshDesktop(0,7);
	}*/
	//}

	// on verifie l'onglet selectionne'
	//refreshDesktop(0,7);
}

function completeSearch (inputName) {
	/*if(document.getElementById(inputName).value == ''){
		document.getElementById(inputName).value = 'Recherche';
	}*/
	$('searchBar_result_obj').style.display	="none";
}

function dimsStartSearch() {
	var word = document.getElementById('searchBar_obj_bar').value;
	addSelectedWord(word);
	prepareWord();
}

function dimsSearch(e) {
	if (!issearching) {
		clearTimeout(word_timer);
		etype=e.type;
		keytype=e.keyCode;
		word_timer = setTimeout("dimsFindStart()", 400);
	}
}

function dimsFindStart(){
	var dataSet, data, len;
	var result = '';
	var searchBar = document.getElementById('searchBar_obj');

	var word = document.getElementById('searchBar_obj_bar').value;
	word = word.replace("'", "?");

	switch(etype){
		case 'keyup':
			if(word != '') { //e.keyCode == 13 &&
				if (keytype==13) {
					dimsStartSearch();
				}
				else {
					//document.getElementById('search_content').innerHTML = '';
					searchBar.style.height = "65px";
					if ($('searchBar_result_obj').style.display	== 'none') {
						//$('searchBar_result_obj').innerHTML = '<li><img src="./common/img/loading.gif" alt="loader"></li>';
						//new Effect.BlindDown('searchBar_result_obj', { duration: 0.6 });
						$('searchBar_result_obj').style.display	="block";
					}
					dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word,dims_word_display);
				}
			}
		break;
		case 'click':
			if(word != '') {
				//document.getElementById('search_content').innerHTML = '';

				searchBar.style.height = "65px";
				$('searchBar_result_obj').style.display	= 'none';
				$('searchBar_result_obj').innerHTML = '<li><img src="./common/img/loading.gif" alt="loader"></li>';
				dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word,dims_word_display);
				//new Effect.BlindDown('searchBar_result_obj', { duration: 0.6 });
			}
		break;
	}
}

function resize(){
	var searchBar = document.getElementById('searchBar_obj');
	height=searchBar.style.height;

	if(height < (sizeresult+5)) {
		if(height > (sizeresult-5)) {
			height += 1;
		} else {
			height += 4;
		}

		searchBar.style.height = height + "px";
		var timeOut = setTimeout("resize()", 10);
	} else {
		clearTimeout(timeOut);

		resultsearch = unescape(resultsearch);
		document.getElementById('searchBar_result_obj').innerHTML = resultsearch;
	}
}

function dims_word_display(result,ticket) {
	var last_char;
	var first_char;
	if (result != '') {
		debch="";
		if (word_deb!="") debch="\""+word_deb;

		word_results = new Array();
		var nbwords=0;

		splited_result = result.split('|');
		tagstoprint = '';

		for (i=0;i<splited_result.length;i++) {

			if (i==0) {
				// on compte le nbre d'elements
				nbelemtmp=splited_result[i].split(' ').length;
			}
			//if (word_deb!="") detail=debch+" "+splited_result[i]+"\"";
			detail = splited_result[i];
			nbwords=detail.split(' ').length;

			first_char=detail.charAt(0);
			last_char=detail.charAt(detail.length-1);

			if (tagstoprint != '') tagstoprint += ' ';
			//if (nbwords==1) detailtext=detail;
			//else detailtext="\""+detail+"\"";
			detailtext=detail;
			if (first_char=='"' && last_char!='"') {
				detailtext=detailtext+"\"";
			}
			word_results[i] = detailtext;
			detail = detail.replace(/\"/g,'');
			tagstoprint += '<a href="javascript:addSelectedWord(\''+detail+'\');prepareWord();">'+detailtext+'</a>&nbsp;';
			//word_results[i] = detail;
		}

		resultsearch=tagstoprint;
		resize();
	}
	else {
		resultsearch=";"
		resize();
	}
	issearching=false;

	if (ancienimgsearch!="") document.getElementById("imgsearch").src=ancienimgsearch;

	dims_getelem('searchBar_obj_bar').selectionStart=dims_getelem('searchBar_obj_bar').value.length;
	dims_getelem('searchBar_obj_bar').selectionEnd=dims_getelem('searchBar_obj_bar').value.length;
	// test si on a pas ecrit qq chose en attendant
	if (dims_getelem('searchBar_obj_bar').value!=word_search) dims_word_search();

}

function useCampaign(campaignid) {
	currentcampaign=campaignid;
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=useCampaign&campaignid='+campaignid,useCampaignSuite);
}

function useCampaignSuite(result) {
	//dims_getelem('dims_getelem('resultselectedwords').innerHTML=result;').innerHTML=result;
	searchWordSuite("",0);
}

function addCampaign(event) {
 	dims_showpopup('Campaign',300, event);
	timerportalrefresh = setTimeout("execCampaign()", 100);
}

function execCampaign() {
	clearTimeout(timerportalrefresh);
	dims_xmlhttprequest_todiv("admin-light.php",'dims_op=add_campain','',"dims_popup");
}

function dims_init_refresh_campaign() {
	timerportalrefreshcampaign = setTimeout("execRefreshCampaign()", 120000);
}

function execRefreshCampaign() {
	clearTimeout(timerportalrefreshcampaign);
	dims_xmlhttprequest_todiv("admin-light.php",'dims_op=execRefreshCampaign','||',"infocampaign","listcampaign");
	dims_init_refresh_campaign();
}

function deleteCampaign(val) {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=deleteCampaign&campaignid='+val,'||',"infocampaign","listcampaign");
}

function updateCampaign(val) {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=updateCampaign&campaignid='+val,'||',"infocampaign","listcampaign");
}

function updateOperator(key,val) {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=word_updateoperatorsearch&wordid='+key+"&val="+val,'','resultselectedwords');
}

function addParenthese() {
	var elemdiv;
	var d1=0;
	var d2=0;
	var f1=0;
	var f2=0;
	var begin="";
	var last="";

	if (document.getSelection) sel = document.getSelection();
	else if (document.selection) sel = document.selection.createRange().text;

	if (sel.length>0) {
		// on supprime les parenthses ( et )
		sel=sel.replace(/[(]/g,"");
		sel=sel.replace(/[)]/g,"");
		sel=sel.replace(/&nbsp;/g,"");

		// recuperation du premier et dernier mot
		d1=sel.indexOf(" & ");
		d2=sel.indexOf(" U ");
		f1=sel.lastIndexOf(" & ");
		f2=sel.lastIndexOf(" U ");
		if ((d1>0 || d2>0) && (f1>0 || f2>0)) {
			// extraction du premier et dernier mots
			if (d1>0 && d2>0) {
				if (d1<d2) begin=sel.substring(0,d1);
				else begin=sel.substring(0,d2);
			}
			else {
				if (d1>0) begin=sel.substring(0,d1);
				else begin=sel.substring(0,d2);
			}

			if (f1>0 && f2>0) {
				if (f1>f2) last=sel.substring(f1+3,sel.length);
				else last=sel.substring(f2+3,sel.length);
			}
			else {
				if (f1>0) last=sel.substring(f1+3,sel.length);
				else last=sel.substring(f2+3,sel.length);
			}

			if (begin.length>0 && last.length>0)
				dims_xmlhttprequest_todiv('admin-light.php','dims_op=word_addparenthese&begin='+begin+'&last='+last,'','resultselectedwords');
		}
	}
	else alert("Vous devez slectionner au moins deux mots cles");
}

function addSelectedWord(word) {
	$('searchBar_obj_bar').value="";
	$('resultsearchbloc').style.visibility="visible";
	$('resultsearchbloc').style.display="block";
	/*issearching=true;
	if (document.getElementById("imgsearch")!=null) {
		if (ancienimgsearch=="") ancienimgsearch=document.getElementById("imgsearch").src;
		document.getElementById("imgsearch").src="./common/img/loading.gif";
	}*/
	//deleteSelected();
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=word_addsearch&word='+word,addSelectedWordSuite,word);
}

function addSelectedWordSuite(result,word) {
	dims_getelem('resultselectedwords').innerHTML=result;
	/* on appelle la mise en cache de la s�lection*/
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=word_addsearchcache&word='+word,addSelectedWordEnd);
}

function addSelectedWordEnd(result) {
	issearching=false;
	if (ancienimgsearch!="") document.getElementById("imgsearch").src=ancienimgsearch;
	document.getElementById("searchBar_obj_bar").focus();
	searchNewWord();
}

function deleteSelectedWord(wordid) {
	/* hide dims_popup*/
	dims_getelem('dims_popup').style.visibility='hidden';
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=word_deletesearch&wordid='+wordid,'','resultselectedwords');
}

function refreshSelectedWord() {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=word_refreshselectedword','','resultselectedwords');
}

function deleteSelected() {
	/* hide dims_popup*/
	dims_getelem('dims_popup').style.visibility='hidden';
	currentcampaign=0;
	window.location.href='/admin.php?dims_op=reset_selection';
	//dims_xmlhttprequest_tofunction('admin-light.php','dims_op=word_deleteselected',deleteSelectedExec);
	//var word = document.getElementById('searchBar_obj_bar');
	//word.value="";

}

function searchNews() {
	var elemdiv;

	for(i=0;i<listmodules.length;i++) {
		elemdiv=document.getElementById('state'+listmodules[i]);
		if (elemdiv.innerHTML=="1")
			document.getElementById('content'+listmodules[i]).innerHTML="<table width=\"99%\" height=\"99%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
	}

	for(i=0;i<listmodules.length;i++) {
		elemdiv=document.getElementById('state'+listmodules[i]);
		if (elemdiv.innerHTML=="1")
			dims_xmlhttprequest_todiv('admin-light.php','dims_op=searchnews&moduleid='+listmodules[i],'||','ressearch'+listmodules[i],'content'+listmodules[i]);
	}
	statesearch="searchnews";
}

function searchRecursiveNews() {
	i=0;
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=searchnews&moduleid='+listmodules[i],searchRecursiveNewsSuite,i);
	statesearch="searchnews";
}

function searchRecursiveNewsSuite(result,i) {
	if (result!=null) tabxmlvalue=result.split("||");
	var elem=dims_getelem('ressearch'+listmodules[i]);
	var elemcontent=dims_getelem('content'+listmodules[i]);

	if (elem!=null) elem.innerHTML= "<a href=\"javascript:void(0);\" onclick=\"javascript:switchModuleDisplay("+listmodules[i]+");\">"+tabxmlvalue[0]+"</a>";
	if (elemcontent!=null) elemcontent.innerHTML= tabxmlvalue[1];

	i++;

	if (i<listmodules.length) {
		/*elem=dims_getelem('ressearch'+listmodules[i]);
		elemcontent=dims_getelem('content'+listmodules[i]);
		if (elem!=null) elem.innerHTML="<img src=\"./common/img/loading.gif\" alt=\"\">";
		if (elemcontent!=null) elemcontent.innerHTML="<table width=\"100%\" height=\"100%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
		*/
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=searchnews&moduleid='+listmodules[i],searchRecursiveNewsSuite,i);
	}
}

function searchRecursiveFavorites(type) {
	i=0;
	typefavorite=type;
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=searchfavorites&type='+typefavorite+'&moduleid='+listmodules[i],searchRecursiveFavoritesSuite,i);
	statesearch="searchnfavorites";
}

function searchRecursiveFavoritesSuite(result,i) {
	tabxmlvalue=result.split("||");
	var elem=dims_getelem('ressearch'+listmodules[i]);
	var elemcontent=dims_getelem('content'+listmodules[i]);

	if (elem!=null) elem.innerHTML= "<a href=\"javascript:void(0);\" onclick=\"javascript:switchModuleDisplay("+listmodules[i]+");\">"+tabxmlvalue[0]+"</a>";
	if (elemcontent!=null) elemcontent.innerHTML= tabxmlvalue[1];

	i++;
	if (i<listmodules.length) {
		elem=dims_getelem('ressearch'+listmodules[i]);
		elemcontent=dims_getelem('content'+listmodules[i]);
		if (elem!=null) elem.innerHTML="<img src=\"./common/img/loading.gif\" alt=\"\">";
		if (elemcontent!=null) elemcontent.innerHTML="<table width=\"100%\" height=\"100%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";

		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=searchfavorites&type='+typefavorite+'&moduleid='+listmodules[i],searchRecursiveFavoritesSuite,i);
	}
}

function searchEmpty() {

	var elemdiv;

	for(i=0;i<listmodules.length;i++) {
		elemdiv=document.getElementById('state'+listmodules[i]);
		if (elemdiv.innerHTML=="1") {
			document.getElementById('content'+listmodules[i]).innerHTML="";
		}
	}
	arraysearch = new Array();
	document.getElementById("searchBar_obj_bar").focus();
}

function searchUniqueWord(idmodule) {
	word=document.getElementById("searchBar_obj_bar").value;
	document.getElementById('content'+idmodule).innerHTML="<table width=\"100%\" height=\"100%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=searchunique&moduleid='+idmodule+'&word='+word,'||','ressearch'+idmodule,'content'+idmodule);
}

function searchWordtmp() {
	i=0;
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=search&moduleid='+listmodules[i]+'&word='+wordsearch,searchSuite,i);
}

function searchSuite(result,i) {
	dims_getelem('content'+listmodules[i]).innerHTML=result;
	i++;
	if (i<listmodules.length) {
		document.getElementById('content'+listmodules[i]).innerHTML="<table width=\"100%\" height=\"100%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=search&moduleid='+listmodules[i]+'&word='+wordsearch,searchSuite,i);
	}
	else document.getElementById("searchBar_obj_bar").focus();
}

function searchWord() {
	/* test for unique search or not */
	//listmod=document.getElementById('listemodulesearch');
	//selectedelement=listmod.selectedIndex;
	moduleid=0;
	res="";
	for(i=0;i<listmodules.length;i++) {
		res=res+listmodules[i]+" ";
	}
	/*init de mouseover sur l'objet */
	idrecord_over=0;
	idobj_over=0;
	idmod_over=0;
	isearch=0;
	statesearch="search";
	/* preparation et chargement des �l�ments en m�moire */
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=executesearch',searchWordSuite);
}

function searchNewWord() {
	/* run unique search */
	$('searchBar_result_obj').style.display = "none";
	var searchBar = document.getElementById('searchBar_obj');
	searchBar.style.height = "30px";
	//dims_getelem('search_content').innerHTML= "<table width=\"100%\" height=\"100%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";;
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=executesearch',searchNewWordSuite);
}

function searchNewWordSuite() {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=newsearch','','search_content');
}

function searchNewWordRefresh() {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=newsearch&refresh=1','','search_content');
}

function searchRecursiveWordSuite(result,isearch) {
	tabxmlvalue=result.split("||");
	dims_getelem('ressearch'+listmodules[isearch]).innerHTML= "<a href=\"javascript:void(0);\" onclick=\"javascript:switchModuleDisplay("+listmodules[isearch]+");\">"+tabxmlvalue[0]+"</a>";
	dims_getelem('content'+listmodules[isearch]).innerHTML= tabxmlvalue[1];

	isearch++;
	if (isearch<listmodules.length) {
		document.getElementById('ressearch'+listmodules[isearch]).innerHTML="<img src=\"./common/img/loading.gif\" alt=\"\">";
		document.getElementById('content'+listmodules[isearch]).innerHTML="<table width=\"100%\" height=\"100%\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";

		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=search&moduleid='+listmodules[isearch],searchRecursiveWordSuite,isearch);
	}
}

function searchWordSuite(result) {
	isearch=0;
	if (listmodules[isearch]!=null) {
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=search&moduleid='+listmodules[isearch],searchRecursiveWordSuite,isearch);
	}
	else {
		/*recherche unique */
		sm=dims_getelem('dims_searchmenu');
		sm.style.visibility="visible";
		sm.style.display="block";
		dims_xmlhttprequest_todiv('admin-light.php','dims_op=search','||','dims_ressearch','dims_searchcontent');
	}
/*
	if (moduleid==0 && currentcampaign<0) {
		listmod=document.getElementById('listemodulesearch');
		selectedelement=listmod.selectedIndex;
		moduleid=listmod.options[selectedelement].value;
	}

	if (moduleid>0 ) {
		dims_xmlhttprequest_todiv('admin-light.php','dims_op=search&moduleid='+moduleid+"&campaignid="+currentcampaign,'||','ressearch'+moduleid,'content'+moduleid);
	}
	else {
		for(i=0;i<listmodules.length;i++) {
			dims_xmlhttprequest_todiv('admin-light.php','dims_op=search&moduleid='+listmodules[i]+"&campaignid="+currentcampaign,'||','ressearch'+listmodules[i],'content'+listmodules[i]);
		}

		if (currentcampaign>0)
		dims_xmlhttprequest_todiv("admin-light.php","dims_op=execRefreshCacheCampaign&campaignid="+currentcampaign,"","resultselectedwords");
	}

	document.getElementById("wordsearch").focus();
*/
}

function prepareWord() {
	document.getElementById("searchBar_obj_bar").focus();
	document.getElementById("searchBar_obj_bar").select();
}

var word_timer;
var word_search;
var word_deb;
var lastword;
var word_results = new Array();
var tag_results = new Array();

var arraysearch = new Array();
var word_new_array = new Array();
var tag_last_array = new Array();
var tag_new_array = new Array();

var word_lastedit = '';
var word_modified = -1
var issearching=false;
var ancienimgsearch ="";
var nbelemcourant=0;
var nbelemtmp=0;

function dims_word_init() {
//	dims_getelem('wordsearch').onkeyup = dims_word_keyup;
//	dims_getelem('wordsearch').onkeypress = dims_word_keypress;
	$('searchBar_result_obj').style.display	= 'none';
}

function dims_word_init_semanticsearch() {
	dims_xmlhttprequest("admin-light.php","dims_op=init_semanticsearch");
}

function dims_word_search() {
	if (!issearching) {
		clearTimeout(word_timer);
		word_search = dims_getelem('searchBar_obj_bar').value;
		word_timer = setTimeout("dims_word_searchtimeout()", 600);
	}
}

function dims_word_searchtimeout() {
	issearching=true;

	word_search=word_search.replace('(','');
	word_search=word_search.replace(')','');
	word_search=word_search.replace('AND','');
	word_search=word_search.replace('OR','');
	list_words = word_search.split(' ');

	if (list_words.length>0) {
		nbelemcourant=list_words.length;
		dims_word_searchlastword(list_words[list_words.length-1]);
	}
	else {
		dims_getelem('searchBar_result_obj').innerHTML ="";
		//dims_getelem('resulttags').innerHTML = "";
		issearching=false;
	}

}

function dims_word_searchrecursword(result,ind) {
	if (ind<list_words.length) {
		word=list_words[ind];
		arraysearch[arraysearch.length]=word;

		// recursive call for analyse next word
		if (ind==list_words.length-1)
			dims_word_searchlastword(list_words[ind]);
		else
			dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_addsearchcache&word='+word,dims_word_searchrecursword,ind+1);
	}
}

function dims_word_searchlastword(ch) {
	if (ch.length>=2) {
		issearching=true;
		if (document.getElementById("imgsearch")!=null) {
			if (ancienimgsearch=="") ancienimgsearch=document.getElementById("imgsearch").src;
			document.getElementById("imgsearch").src="./common/img/loading.gif";
		}

		dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word_search,dims_word_display);
		//if (document.getElementById("checktagsearch").checked)
		//	dims_xmlhttprequest_tofunction('index-light.php','dims_op=tag_presearch&word='+ch,dims_tag_search_display);
	}
	else {
		issearching=false;
		word_search="";
	}
}

function dims_tag_search_display(result,ticket) {
	if (result != '') {
		tag_results = new Array();

		splited_result = result.split('|');
		tagstoprint = '';

		for (i=0;i<splited_result.length;i++) {
			detail = splited_result[i];
			subdetail=detail.split(',');

			if (tagstoprint != '') tagstoprint += ' ';
			if (i==0) tagstoprint += '<b>';
			tagstoprint += '<a href="javascript:addSelectedTag(\''+subdetail[0]+'\');prepareWord();">'+subdetail[1]+'</a>&nbsp;|&nbsp; ';
			if (i==0) tagstoprint += '</b>';
			tag_results[i] = subdetail[0];
		}
		dims_getelem('resulttags').innerHTML = tagstoprint;
	}
	else {
		dims_getelem('resulttags').innerHTML = '';
		tags_results = new Array();
	}
}

function dims_word_prevent(e) {
	if (window.event) window.event.returnValue = false;
	else e.preventDefault();
}

function dims_word_keypress(e) {
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target;

	switch(e.keyCode) {

		case 38: case 40:
			//prevent(e);
		break;
		case 9:
			dims_word_prevent(e);
		break;
		case 13:
			dims_word_prevent(e);
		break;

		default:
			word_lastedit = dims_getelem(src.id).value;
		break;
	}
}

function dims_word_keyupExec(e) {
	clearTimeout(word_timer);
	etype=e;
	keytype=e.keyCode;
	word_timer = setTimeout("dims_word_keyup()", 400);
}

function dims_word_keyup() {
	//e=e||window.event;
	e=etype;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field
	var searchBar = document.getElementById('searchBar_obj');

	var word = document.getElementById('searchBar_obj_bar').value;
	word = word.replace("'", "?");

	switch(e.keyCode) {
		case 8:
			if (dims_getelem('searchBar_obj_bar').value.length==0) {
				dims_getelem('searchBar_obj_bar').value="";
				dims_getelem('searchBar_result_obj').innerHTML="";
				$('searchBar_result_obj').style.display	="none";
				word_results = new Array();
			}
			else dims_word_search();
			break;

		case 13:
			//dims_word_complete();
			//dims_word_prevent(e);
			//searchWord();

			word=dims_getelem('searchBar_obj_bar').value;

			first_char=word.charAt(0);
			if (first_char=='"' && word_results.length>0) {
				word=word_results[0];
			}
			if (word.length>0) {
				addSelectedWord(word);
				dims_getelem('searchBar_obj_bar').value="";
				dims_getelem('searchBar_result_obj').innerHTML="";
				//dims_getelem('resulttags').innerHTML="";
				word_results = new Array();
			}
		break;
		case 35: //end
		case 36: //home
			dims_word_prevent(e);
			break;
		case 39: //right
		case 37: //left
		case 38 : //up
		case 40: //down
			if (word_results.length>0) {

				dims_word_prevent(e);
				dims_getelem('searchBar_obj_bar').selectionStart=dims_getelem('searchBar_obj_bar').value.length;
				dims_getelem('searchBar_obj_bar').selectionEnd=dims_getelem('searchBar_obj_bar').value.length;
				// verify if result so turn up
				var savefirstvalue="";
				if (e.keyCode==38 || e.keyCode==37) {
					for(i=(word_results.length-2);i>=0;i--) {
						if (i==(word_results.length-2)) savefirstvalue=word_results[i+1];
						word_results[i+1]=word_results[i];
					}
					word_results[0]=savefirstvalue;

				}
				else {
					for(i=0;i<word_results.length;i++) {
						if (i==0) savefirstvalue=word_results[i];
						word_results[i]=word_results[i+1];
					}
					word_results[i-1]=savefirstvalue;
				}
				var nbwords=dims_getelem('searchBar_obj_bar').value.split(' ').length;

				tagstoprint = '';

				for (i=0;i<word_results.length;i++) {
					detail = word_results[i];

					if (tagstoprint != '') tagstoprint += ' ';
					if (nbwords==1) detailtext=detail;
					else detailtext="\""+detail+"\"";

					tagstoprint += '<a href="javascript:addSelectedWord(\''+detail+'\');prepareWord();">'+detailtext+'</a>&nbsp;|&nbsp; ';
				}
				dims_getelem('searchBar_result_obj').innerHTML = tagstoprint;
			}

			break;
		case 9:
		case 32: //space
			dims_word_prevent(e);

			if (word_results.length==0) dims_word_prevent(e);
			else {
				// on regarde le mot courant
				// on split le tableau et ensuite on prend le dernier en cours
				var word_search = dims_getelem('searchBar_obj_bar').value;
				// premier test si dernier caract�re saisi est un espace

				var list_wordstmp = word_search.split(' ');

				if (list_wordstmp.length>0) {
					// on a bien un mot tap� : on regarde si on a des �l�ments dans la liste
					//if (word_results.length>=1) {
						if (e.keyCode==9 || e.keyCode==32) {

							// on regarde si on a une expression ou non
							word=list_wordstmp[list_wordstmp.length-2];

							first_char=word_search.charAt(0);
							last_char=word_search.charAt(word_search.length-1);

							if (e.keyCode==32) {
								if (first_char=='"' && last_char!='"') {
									issearching=true;
									dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word+"%20",dims_word_display);
								}
								else {
									//word=list_wordstmp[list_wordstmp.length-2];

									//dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word,dims_word_display);
								}
							}
							else {
								if (e.keyCode==9 && word_results.length>0) {
									word=word_results[0];
									addSelectedWord(word);
									prepareWord();
									dims_getelem('searchBar_obj_bar').value="";
									dims_getelem('searchBar_obj_bar').focus();
									$('searchBar_result_obj').style.display	="none";
								}


							}
							//alert(list_wordstmp.length+" "+list_wordstmp[0]);
							//word=word_results[0];

							/*
							// on ajoute le nouveau mot ds la liste courant
							arraysearch[arraysearch.length]=word;
							dims_getelem('searchBar_obj_bar').value=word+" ";

							dims_getelem('searchBar_result_obj').innerHTML="";
							//dims_getelem('resulttags').innerHTML="";

							issearching=true;
							if (document.getElementById("imgsearch")!=null) {
								if (ancienimgsearch=="") ancienimgsearch=document.getElementById("imgsearch").src;
								document.getElementById("imgsearch").src="./common/img/loading.gif";
							}
							dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word+"%20",dims_word_display);
							*/
						}
					//}
				}
			}
			break;
		case 222:

			break;
		case 8:
		default:
			if (lastword!=dims_getelem('searchBar_obj_bar').value) {
				//dims_word_search();

				//document.getElementById('search_content').innerHTML = '';
				searchBar.style.height = "65px";
				if ($('searchBar_result_obj').style.display	== 'none') {
					$('searchBar_result_obj').innerHTML = '<li><img src="./common/img/loading.gif" alt="loader"></li>';
					new Effect.BlindDown('searchBar_result_obj', { duration: 0.6 });
				}
				var word_search = dims_getelem('searchBar_obj_bar').value;
				// premier test si dernier caract�re saisi est un espace

				var list_wordstmp = word_search.split(' ');
				if (list_wordstmp.length>1) {
					word=list_wordstmp[list_wordstmp.length-1];
				}
				dims_xmlhttprequest_tofunction('index-light.php','dims_op=word_presearch&word='+word,dims_word_display);
			}

		break;
	}
}

function dims_word_keyupunique(e) {
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field

	switch(e.keyCode) {
		case 38: case 40:
			prevent(e);
		break
		case 9:
			//dims_word_complete();
			//dims_word_prevent(e);
		break
		case 13:
			//dims_word_complete();
			dims_word_prevent(e);
			// on recupere le numro de module
			var ch=src.id;
			idmodule=ch.substring(10,ch.length);
			searchUniqueWord(idmodule);
		break;
	}
}

function dims_word_complete(idword) {
	/*
	if (!(idword>=0)) idword = 0;

	if (word_results[idword])
	{
		word_new_array[word_modified] = word_results[idword];

		taglist = '';
		for (i=0;i<word_new_array.length;i++)
		{
			if (taglist != '') taglist += ' ';
			taglist += word_new_array[i]
		}

		dims_getelem('searchBar_obj_bar').value = taglist.replace('/(^\s*)|(\s*$)/g','')+" ";
		//dims_getelem('resultwords').innerHTML = '';
	}

	word_results = new Array();
	*/
}
