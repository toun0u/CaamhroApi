var id_open = 0 ;

function displayListeConnected(tab){
	if (tab != ''){
		for (var i=0; i<tab.length; i++){
			list = '';
			while(i<tab.length && tab[i]!=','){
				list = list+tab[i];
				i++;
			}
			if (window.document.getElementById('chat_open_'+list) != null && window.document.getElementById('chat_open_'+list).style.display!='none'){
				window.document.getElementById('chat_open_'+list).style.display='none';
				dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&haut='+list+'&margin=0',true,false);
			}
		}
	}

	if (window.document.getElementById('liste_connect').style.display == 'none'){
		window.document.getElementById('liste_connect').style.display='block';
	}else{
		window.document.getElementById('liste_connect').style.display='none';
	}
	dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&haut=-1&margin=0',true,false);
}

function displayChat(id, tab,color){
	if (tab != ''){
		for (var i=0; i<tab.length; i++){
			list = '';
			while(i<tab.length && tab[i]!=','){
				list = list+tab[i];
				i++;
			}
			if(list != id && window.document.getElementById('chat_open_'+list).style.display!='none'){
				dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&haut='+list+'&margin=0',true,false);
				window.document.getElementById('chat_open_'+list).style.display='none';
			}
		}
	}
	window.document.getElementById('liste_connect').style.display='none';
	window.document.getElementById('chat_'+id).style.display='block';

	if (color != '')
		window.document.getElementById('chat_'+id).style.background=color;
	else
		window.document.getElementById('chat_'+id).style.background='url("./common/templates/backoffice/dims_lfb/img/sprite.png") repeat-x scroll left -24px #000000';
	dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&bas='+id,true,false);
	//window.document.getElementById('chat_open_'+id).style.display='';
}

function chatFocus(id){
	window.document.getElementById('chat_'+id).style.background='url("./common/templates/backoffice/dims_lfb/img/sprite.png") repeat-x scroll left -24px #000000';
	dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&read='+id,true,false);
}

function closeChat(id){
	window.document.getElementById('chat_'+id).style.display='none';
	if (window.document.getElementById('chat_open_'+id) != null)
		window.document.getElementById('chat_open_'+id).style.display='none';
	//dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&bas='+id,true,false);
	dims_xmlhttprequest_todivpost('admin.php','dims_op=chat_actions&bas='+id,'','chat_msg_'+id);
	id_open = 0 ;
}

function displayChatOpen(id, tab){
	if (tab != ''){
		for (var i=0; i<tab.length; i++){
			list = '';
			while(i<tab.length && tab[i]!=','){
				list = list+tab[i];
				i++;
			}
			if(list != id && window.document.getElementById('chat_open_'+list).style.display!='none'){
				dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&haut='+list+'&margin=0',true,false);
				window.document.getElementById('chat_open_'+list).style.display='none';
			}
		}
	}
	var posChatOpen = 0;

	if (window.document.getElementById('chat_open_'+id).style.display == 'none'){
		window.document.getElementById('chat_'+id).style.background='url("./common/templates/backoffice/dims_lfb/img/sprite.png") repeat-x scroll left -24px #000000';
		window.document.getElementById('liste_connect').style.display='none';
		window.document.getElementById('chat_open_'+id).style.display='block';
		if (window.document.getElementById('chat_'+id).offsetParent)
			var posChat = (window.document.getElementById('chat_'+id).offsetLeft + getLeft(window.document.getElementById('chat_'+id).offsetParent));
		else
			var posChat = (window.document.getElementById('chat_'+id).offsetLeft);
		//var posChat = window.document.getElementById('chat_'+id).offsetLeft ;
		var taille = screen.width;
		var posChatOpen = taille-posChat-188 ;
		id_open = id ;
		window.document.getElementById('chat_open_'+id).style.marginRight=posChatOpen+'px';
	}else{
		id_open = 0 ;
		window.document.getElementById('chat_open_'+id).style.display='none';
	}
	dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&haut='+id+'&margin='+posChatOpen,true,false);
}

function chat_submit(id){
	// msg_send_
	var champ = dims_getelem('msg_send_'+id);
	//dims_xmlhttprequest_post('admin.php','dims_op=chat_actions&send='+id+'&msg_send_'+id+'='+champ.value,true,false);
	if (champ.value != '')
		dims_xmlhttprequest_todivpost('admin.php','dims_op=chat_actions&send='+id+'&msg_send_'+id+'='+champ.value,'','chat_msg_'+id);
	champ.value = '';
}

function refreshChat(id){
	if (id_open > 0)
		dims_xmlhttprequest_todivpost('admin.php','dims_op=chat_actions&refresh=3&msgchat='+id_open,'','chat_msg_'+id_open);
	setTimeout("refreshChat()",2000);
}

refreshChat();

function chatRefreshAll(){
	setTimeout("chatRefreshAll()",15000);
	// refresh du nombre d'utilisateurs
	dims_xmlhttprequest_todivpost('admin.php','dims_op=chat_actions&refresh=1','','chat_inf');

	dims_xmlhttprequest_todivpost('admin.php','dims_op=chat_actions&refresh=2','','liste_connect');

	var t = window.document.getElementById('list_used');
	if (t != null && t.value != ''){
		var tab = t.value ;
		for (var i=0; i<tab.length; i++){
			list = '';
			while(i<tab.length && tab[i]!=','){
				list = list+tab[i];
				i++;
			}
			dims_xmlhttprequest_todivpost('admin.php','dims_op=chat_actions&refresh=3&msgchat='+list,'','chat_msg_'+list);
			var div =
			document.getElementById('chat_msg_'+list).scrollTop=document.getElementById('chat_msg_'+list).scrollHeight;
		}
	}
}



