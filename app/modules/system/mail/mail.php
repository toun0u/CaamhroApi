<?php

class simplemail {

      var $recipient;
      var $subject;
      var $hfrom;
      var $headers;
      var $hbcc;
      var $hcc;
      var $text;
      var $html;
      var $attachement;
      var $htmlattachement;
      var $error_log;

      function simplemail() {
               $this -> attachement = array();
               $this -> htmlattachement = array();
      }

      function checkaddress($address) {
               if ( preg_match('`([[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,4}))`', $address) )
               { return TRUE; }
               else
               { $this->error_log.="l'adresse $address est invalide\n"; return FALSE; }
      }

      function checkname($name) {
               if ( preg_match("`[0-9a-zA-Z\.\-_ ]*`" , $name ) )
               { return TRUE; }
               else
               { $this->error_log.=" le pseudo $name est invalide\n"; return FALSE; }
      }

      function makenameplusaddress($address,$name) {
               if ( !$this->checkaddress($address) ) return FALSE;
               if ( !$this->checkname($name) ) return FALSE;
               if ( empty($name) ) { return $address; }
               else { $tmp=$name." <".$address.">"; return $tmp; }
      }

      function addrecipient($newrecipient,$name='') {
               $tmp=$this->makenameplusaddress($newrecipient,$name);
               if ( !$tmp ) { $this->error_log.=" To: error\n"; return FALSE; }
               if ( !empty($this->recipient) ) $this->recipient.= ",";
               $this->recipient.= $tmp;
               return TRUE;
      }

      function addbcc($bcc,$name='') {
               $tmp=$this->makenameplusaddress($bcc,$name);
               if ( !$tmp ) { $this->error_log.=" Bcc: error\n"; return FALSE; }
               if ( !empty($this->hbcc)) $this->hbcc.= ",";
               $this->hbcc.= $tmp;
               return TRUE;
      }

     function addcc($cc,$name='') {
               $tmp=$this->makenameplusaddress($cc,$name);
               if ( !$tmp ) { $this->error_log.=" Cc: error\n"; return FALSE; }
               if (!empty($this->hcc)) $this->hcc.= ",";
               $this->hcc.= $tmp;
               return TRUE;
     }

      function addsubject($subject) {
               if (!empty($subject)) $this->subject= $subject;
      }

      function addfrom($from,$name='') {
               $tmp=$this->makenameplusaddress($from,$name);
               if ( !$tmp ) { $this->error_log.=" From: error\n"; return FALSE; }
               $this->hfrom = $tmp;
               return TRUE;
      }

      function addreturnpath($return) {
               $tmp=$this->makenameplusaddress($return,'');
               if ( !$tmp ) { $this->error_log.=" Return-Path: error\n"; return FALSE; }
               $this->returnpath = $return;
               return TRUE;
      }

      function addreplyto($replyto) {
               $tmp=$this->makenameplusaddress($replyto,'');
               if ( !$tmp ) { $this->error_log.=" Reply-To: error\n"; return FALSE; }
               $this->returnpath = $tmp;
               return TRUE;
      }

      // les attachements
      function addattachement($filename) {
               array_push ( $this -> attachement , array ( 'filename'=> $filename ) );
      }

      // les attachements html
      function addhtmlattachement($filename,$cid='',$contenttype='') {
               array_push ( $this -> htmlattachement ,
                                  array ( 'filename'=>$filename ,
                                          'cid'=>$cid ,
                                          'contenttype'=>$contenttype )
                          );
      }

      function writeattachement($attachement,$B2B) {
	  	$message = '';
		if ( !empty($attachement) ) {
			foreach($attachement as $AttmFile){
				$patharray = explode ("/", $AttmFile['filename']);
                                $FileName = $patharray[count($patharray)-1];

                                $message .= "\n--".$B2B."\n";

				if (!empty($AttmFile['cid'])) {
                                $message .= "Content-Type: {$AttmFile['contenttype']};\n name=\"".$FileName."\"\n";
                                $message .= "Content-Transfer-Encoding: base64\n";
                                $message .= "Content-ID: <{$AttmFile['cid']}>\n";
                                $message .= "Content-Disposition: inline;\n filename=\"".$FileName."\"\n\n";
				} else {
				$message.="Content-Type: application/octetstream;\n name=\"".$FileName."\"\n";
				$message.="Content-Transfer-Encoding: base64\n";
				$message.="Content-Disposition: attachement;\n filename=\"".$FileName."\"\n\n";
				}

                                $fd=fopen ($AttmFile['filename'], "rb");
                                $FileContent=fread($fd,filesize($AttmFile['filename']));
                                fclose ($fd);

                                $FileContent = chunk_split(base64_encode($FileContent));
                                $message .= $FileContent;
								unset($FileContent);
                                $message .= "\n\n";
			}
			$message .= "\n--".$B2B."--\n";
		}
		return $message;
	}

      function sendmail() {
			$headers = '';
			$message = '';
               if ( empty($this->recipient) ) { $this->error_log.="destinataire manquant\n"; return FALSE; }
               if ( empty($this->subject) ) { $this->error_log.="sujet manquant\n"; return FALSE; }

               if ( !empty($this->hfrom) ) $headers.= "From: ".$this->hfrom."\n";
               if ( !empty($this->returnpath) ) $headers.= "Return-Path: ".$this->returnpath."\n";
               if ( !empty($this->replyto) ) $headers.= "Reply-To: ".$this->replyto."\n";
               $headers .="MIME-Version: 1.0\n";

               if ( !$this->html && $this->text && !empty($this->attachement) ) {

                       $B1B="----=_001";
                       $headers.="Content-Type: multipart/mixed;\n\t boundary=\"".$B1B."\"\n";

                       //Messages start with text/html alternatives in OB
                       $message ="This is a multi-part message in MIME format.\n";
                       $message.="\n--".$B1B."\n";

					   if (!defined(_DIMS_ENCODING)) {
							$message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
						}
						else {
							$message.="Content-Type: text/plain; charset=\"".$_DIMS_ENCODING."\"\n";
						}

                       $message.="Content-Transfer-Encoding: quoted-printable\n\n";
                       // plaintext goes here
                       $message.=$this->text."\n\n";

		       $message.=$this->writeattachement($this->attachement,$B1B);

               }
	       elseif ( !$this->html && $this->text && empty($this->attachement) ) {

                       $headers.="Content-Type: text/plain; charset=us-ascii; format=flowed\n";
		       $headers.="Content-Transfer-Encoding: 7bit\n";
			// plaintext goes here
                       $message.=$this->text."\n\n";
	       }

 elseif ( $this->html ) {
                       $B1B="----=_001";
                       //$B2B="----=_002";
                       $B3B="----=_002";

                       if ( !$this->text ) { $this->text="HTML only!"; }
                       $headers.="Content-Type:  multipart/mixed;\n\t boundary=\"".$B1B."\"\n";

                       //Messages start with text/html alternatives in OB
                       $message ="This is a multi-part message in MIME format.\n";
                       $message.="\n--".$B1B."\n";

		       //                     $message.="Content-Type: multipart/related;\n\t boundary=\"".$B2B."\"\n\n";
			//plaintext section
                       //$message.="\n--".$B2B."\n";

                       $message.="Content-Type: multipart/alternative;\n\t boundary=\"".$B3B."\"\n\n";
                       //plaintext section
                       $message.="\n--".$B3B."\n";

                       //$message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
                       //$message.="Content-Transfer-Encoding: quoted-printable\n\n";
                       // plaintext goes here
                       //$message.=$this->text."\n\n";

                       // html section
                       //$message.="\n--".$B3B."\n";
					   if (!defined(_DIMS_ENCODING)) {
							$message.="Content-Type: text/html; charset=\"iso-8859-1\"\n";
						}
						else {
							$message.="Content-Type: text/html; charset=\"".$_DIMS_ENCODING."\"\n";
						}

                       $message.="Content-Transfer-Encoding: base64\n\n";
                       // html goes here
                       $message.=chunk_split(base64_encode($this->html))."\n\n";

                       // end of text
                       $message.="\n--".$B3B."--\n";

		       // attachments html
		       //$message.=$this->writeattachement($this->htmlattachement,$B2B);

                        $message.=$this->writeattachement($this->attachement,$B1B);

			//echo "<pre>$message</pre>";

               }
 /*               elseif ( $this->html ) {
                       $B1B="----=_001";
                       $B2B="----=_002";
                       $B3B="----=_003";

                       if ( !$this->text ) { $this->text="HTML only!"; }
                       $headers.="Content-Type:  multipart/mixed;\n\t boundary=\"".$B1B."\"\n";

                       //Messages start with text/html alternatives in OB
                       $message ="This is a multi-part message in MIME format.\n";
                       $message.="\n--".$B1B."\n";

                        $message.="Content-Type: multipart/related;\n\t boundary=\"".$B2B."\"\n\n";
                       //plaintext section
                       $message.="\n--".$B2B."\n";

                       $message.="Content-Type: multipart/alternative;\n\t boundary=\"".$B3B."\"\n\n";
                       //plaintext section
                       $message.="\n--".$B3B."\n";

                       $message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
                       $message.="Content-Transfer-Encoding: quoted-printable\n\n";
                       // plaintext goes here
                       $message.=$this->text."\n\n";

                       // html section
                       $message.="\n--".$B3B."\n";
                       $message.="Content-Type: text/html; charset=\"iso-8859-1\"\n";
                       $message.="Content-Transfer-Encoding: base64\n\n";
                       // html goes here
                       $message.=chunk_split(base64_encode($this->html))."\n\n";

                       // end of text
                       $message.="\n--".$B3B."--\n";

			// attachments html
			$message.=$this->writeattachement($this->htmlattachement,$B2B);

			$message.=$this->writeattachement($this->attachement,$B1B);

               }
 */
		if ( !empty($this->hcc) ) $headers.= "Cc: ".$this->hcc."\n";
		if ( !empty($this->hbcc) ) $headers.= "Bcc: ".$this->hbcc."\n";

               $recipient=$this->recipient;
               $subject=$this->subject;

               if ( mail($recipient, $subject, $message, $headers) ) { return TRUE; } else { return FALSE; }
      }
}
?>
