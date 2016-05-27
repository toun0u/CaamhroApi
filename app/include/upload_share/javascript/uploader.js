/**************************************************************************************************
	Predefined Variables
***************************************************************************************************/
	var http_requests = new Array();
	var http_path="";
	var sendmsgupload="";
	var sendmsgwaiting="";
	var sendmsgcopy="";
	var sendmsgerror="";

	if (window.XMLHttpRequest) var http_request = new XMLHttpRequest();  else var http_request=false;
	//var http_request = new XMLHttpRequest();
	var loading_graphic = "<img src='./common/img/loading.gif' width='16' height='16' border='0' align='absmiddle'>&nbsp;";

/**************************************************************************************************
	Set ScrollBox Heights
***************************************************************************************************/
//** FUNCTION: Set SrollBox Height Based on Element Input Array
	function checkPage(scrollBox,el_array) {
		var adjustOffset = 0;
		if (eval(el_array)) {
			for (i=0; i< el_array.length; i++) {
				if (eval(document.getElementById(el_array[i])) && !isNaN(document.getElementById(el_array[i]).offsetHeight)) {
					adjustOffset = adjustOffset + document.getElementById(el_array[i]).offsetHeight;
				}
			}
		}
		var height = document.body.clientHeight - adjustOffset;
		document.getElementById(scrollBox).style.height=height+'px';
	}
/**************************************************************************************************
	AJAX Server Requests
***************************************************************************************************/
	function build_HttpRequest() {
		xmlhttp = dims_gethttpobject();
		return xmlhttp;
	}
//** FUNCTION: Make Request to Server passing GET variables
	function makeRequest(url,do_function) {
		http_request = build_HttpRequest();
		var time = new Date();
		if (url.indexOf('?')>0){ url = url + '&time='+time.getTime(); } else { url = url + '?time='+time.getTime();}
		http_request.onreadystatechange = do_function;
		http_request.open('GET', url, true);
		http_request.send(null);
	}

	function setVariables(hpath,supload,swaiting,scopy,serror,serrorext)
	{
		http_path=hpath;
		sendmsgupload=supload;
		sendmsgwaiting=swaiting;
		sendmsgcopy=scopy;
		sendmsgerror=serror;
		sendmsgerrorext=serrorext;
	}

/**************************************************************************************************
	Upload Form Functions
***************************************************************************************************/
//** FUNCTION: Create Upload Form
	function createFileInput(path) {
		if (count>0) {
			//copie du texte dans la zone d'info du dessous
			var elemfilename = document.getElementById('filename_'+(count-1));
			var eleminfo = document.getElementById('tdinfo_'+(count-1));
			eleminfo.innerHTML="Nom du fichier : "+elemfilename.value;
		}
		var tbody = document.getElementById('list_body').getElementsByTagName('tbody')[0];
	// Create Table Row
		var tr = document.createElement("tr")
		if (bg_color) { tr.className="on"; bg_color= false; } else { bg_color = true;}
	// Create Table Cell
		var td = document.createElement("td")
		td.id='upload_'+count;
		td.width='100%';
		var output = new Array();
		//output.push("<form id='frmUpload_"+count+"' target='uploadForm' method='post' enctype='multipart/form-data' action='./cgi-bin/upload.cgi?sid="+sid+"'>");
		output.push("<input style='margin:10px;border:1px solid #dedede;' size='87' onchange='javascript:createFileInput();' type='file' id='filename_"+count+"' name='filename' >");

		//output.push("<\/form>");
		td.innerHTML = output.join('');
		tr.appendChild(td);
		tbody.appendChild(tr);
		uploads.push(count);

		var tr = document.createElement("tr");
		if (bg_color) { tr.className="on"; bg_color= false; } else { bg_color = true;}
		var td = document.createElement("td");
		td.width='100%';
		td.id='tdinfo_'+count;
		td.innerHTML = "";
		tr.appendChild(td);
		tbody.appendChild(tr);
		if (count != 0 ) {
			//var tiggerForm = document.getElementById('frmUpload_'+count);
			//tiggerForm['filename'].click();
			//tiggerForm = null;
		}
		count++;

		if (count>1) document.getElementById('btn_upload').style.visibility="visible";
	}

	function checkFileExtentions(form){
		if(check_file_extentions == false){ return false; }
		var re = /(\.php)|(\.sh)$/i;   //Change line 126 in uber_uploader.cgi to match
		if(form['filename'].value != ""){
			if(form['filename'].value.match(re)){
				var string = form['filename'].value;
				var num_of_last_slash = string.lastIndexOf("\\");
				if(num_of_last_slash < 1){ num_of_last_slash = string.lastIndexOf("/"); }
				var file_name = string.slice(num_of_last_slash + 1, string.length);
				var file_extention = file_name.slice(file_name.indexOf(".")).toLowerCase();
				alert('Sorry, uploading a file with the extention "' + file_extention + '" is not allowed.');
				return true;
			}
		}
		return false;
	}

	function upload() {

		if (uploads.length>0) {
			//form = document.getElementById('frmUpload_'+uploads[0]);
			form = document.getElementById('frmUpload_0');
			//if (form["filename"].value == ""){
			if (false) {
				if (count==1) alert("Please Choose a file to upload.");
				else {
					form = document.getElementById('docfile_add');
					form.submit();
				}
			} else {
				var filename="";
				/*filename =	form["filename"].value;
				if (filename.lastIndexOf("\\")>0) {
					filename = filename.substring(filename.lastIndexOf("\\")+1,filename.length);
				} else if (filename.lastIndexOf("/")>0) {
					filename = filename.substring(filename.lastIndexOf("/")+1,filename.length);
				}
				//if(checkFileExtentions(form)){ return false; }
				filename=filename.replace(/&/g,"%26");
				*/
				form.submit();
				makeRequest(http_path+"/index-quick.php?dims_op=upload_progress&filename="+filename,progress);
				document.getElementById("btn_upload").innerHTML = loading_graphic+sendmsgupload;
			}
		}
		else
		{
			form = document.getElementById('docfile_add');
			form.submit();
		}
	}

	function progress() {
		var callpath=http_path+"/index-quick.php?dims_op=upload_progress";

		switch (http_request.readyState) {
			case 1 : checkCount++; break;
			case 4 :
				if (http_request.status == 200) {
					if (debug) status.innerHTML = checkCount + " - " +http_request.responseText;
					response = http_request.responseText.split('|');
					upload_cell = document.getElementById('upload_'+uploads[0]);
					switch(response[0]) {
						case "wait":
							var output = new Array();
							var ext = response[1].substring(response[1].lastIndexOf(".")+1,response[1].length);
							output.push("<table cellspacing='0' cellpadding='0' border='0' width='98%' style='margin-top:10px;'>");
							output.push("<tr>");
							output.push("<td rowspan='2' width='30' height='40' class='img_32_"+ext+"'>&nbsp;<\/td>");

							output.push("<td class='upload_filename'>"+response[1]+"<\/td>");
							output.push("<\/tr>");
							output.push("<tr><td class='upload_stats'>"+sendmsgwaiting+" ("+checkCount+")<\/td><\/tr>");
							output.push("<\/table>");
							upload_cell.innerHTML = output.join('');

							setTimeout('makeRequest("'+callpath+'",progress);',1000);
							break;
						case "started":
							makeRequest(callpath+"&sid="+sid,progress);
							break;
						case "downloading":
							var output = new Array();
							var ext = response[1].substring(response[1].lastIndexOf(".")+1,response[1].length);
							output.push("<table cellspacing='0' cellpadding='0' border='0' width='98%' style='margin-top:10px;'>");
							output.push("<tr>");
							output.push("<td rowspan='3' width='30' height='40' class='img_32_"+ext+"'>&nbsp;<\/td>");
							output.push("<td class='upload_filename'>"+response[1]+"<\/td>");
							output.push("<\/tr>");
							output.push("<tr><td><div id='upload_bar'><div style='width:"+response[3]+"%;'>&nbsp;<\/div><\/div><\/td><\/tr>");
							output.push("<tr><td class='upload_stats'>"+response[4]+" of "+response[5]+" ("+response[6]+"/s) " +response[2]+"<\/td><\/tr>");
							output.push("<\/table>");
							upload_cell.innerHTML = output.join('');
							setTimeout('makeRequest("'+callpath+'",progress);',1000);
							break;
						case "copying":
							var output = new Array();
							var ext = response[1].substring(response[1].lastIndexOf(".")+1,response[1].length);
							output.push("<table cellspacing='0' cellpadding='0' border='0' width='98%' style='margin-top:10px;'>");
							output.push("<tr>");
							output.push("<td rowspan='2' width='30' height='40' class='img_32_"+ext+"'>&nbsp;<\/td>");

							output.push("<td class='upload_filename'>"+response[1]+"<\/td>");
							output.push("<\/tr>");
							output.push("<tr><td class='upload_stats'>"+sendmsgcopy+"<\/td><\/tr>");
							output.push("<\/table>");
							upload_cell.innerHTML = output.join('');
							setTimeout('makeRequest("'+callpath+'&sid='+sid+'",progress);',1000);
							break;
						case "Success":

							uploads.splice(0,1);
							var output = new Array();
							var ext = response[1].substring(response[1].lastIndexOf(".")+1,response[1].length);
							output.push("<table cellspacing='0' cellpadding='0' border='0' width='98%' style='margin-top:10px;'>");
							output.push("<tr>");
							output.push("<td rowspan='2' width='30' height='40' class='img_32_"+ext+"'>&nbsp;<\/td>");
							output.push("<td class='upload_filename'>"+response[1]+"<\/td>");
							output.push("<\/tr>");
							output.push("<tr><td class='upload_stats'>"+response[2]+"<\/td><\/tr>");
							output.push("<\/table>");
							//upload_cell.innerHTML = output.join('');
							//document.getElementById("btn_upload").innerHTML = '<a href="javascript:upload();" class="btn img_upload">'+sendmsgupload+'<\/a>';
							checkCount = 0;
							setTimeout("upload();",800);
							//form = document.getElementById('docfile_add');
							//form = document.getElementById('frmUpload_0');
							//form.submit();

							break;
						default:
							alert(http_request.responseText);

					}
				} else { alert(sendmsgerror+"("+http_request.responseText+")"); }
				break;
		}

	}
//** FUNCTION: Cancel Upload
	function cancelUpload(msg) {
		alert(msg);
	}
