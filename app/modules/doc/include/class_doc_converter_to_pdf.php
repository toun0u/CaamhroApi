<?php
require_once 'HTTP/Request.php';

class doc_converter_to_pdf {

	var $url = "http://localhost:8080/converter/service";

	function convert($inputFile,$outputFile) {
		$inputData = file_get_contents($inputFile);
		$type = explode('.',$inputFile);
		switch($type[count($type)-1]){
			case "doc":
				$inputType = "application/msword";
				break;
			case "rtf":
				$inputType = "text/rtf";
				break;
			case "odt":
				$inputType = "application/vnd.oasis.opendocument.text";
				break;
			case "sxw":
				$inputType = "application/vnd.sun.xml.writer";
				break;
			case "odp":
				$inputType = "application/vnd.oasis.opendocument.presentation";
				break;
			case "odg":
				$inputType = "application/vnd.oasis.opendocument.graphics";
				break;
			case "ods":
				$inputType = "application/vnd.oasis.opendocument.spreadsheet";
				break;
			case "xls":
				$inputType = "application/vnd.ms-excel";
				break;
			case "ppt":
				$inputType = "application/vnd.ms-powerpoint";
				break;
			case "pps":
				$inputType = "application/vnd.ms-pps";
				break;
			case "docx":
				$inputType = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
				break;
			case "xlsx":
				$inputType = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
				break;
			case "pptx":
				$inputType = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
				break;
		}
		//die($inputType);
		$outputType = "application/pdf";
		$request = new HTTP_Request($this->url);
		$request->setMethod("POST");
		$request->addHeader("Content-Type", $inputType);
		$request->addHeader("Accept", $outputType);
		$request->setBody($inputData);
		$request->sendRequest();
		//die($request->getResponseBody());
		return file_put_contents($outputFile, $request->getResponseBody());
	}
}

?>
