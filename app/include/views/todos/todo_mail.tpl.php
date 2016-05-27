<?php
$subject = $_SESSION['cste']['DIMS'].' - '.$_SESSION['cste']['TASK_TO_REALIZE']. ' / ' .$title_object;
$from = $this->getLightAttribute('from');
$dest = $this->getLightAttribute('dest');
$link_to = $this->getLightAttribute('link_to');
$title_object = $this->getLightAttribute('title_object');
$on_the_record = $this->getLightAttribute('on_the_record');

$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
		    <title>'.$_SESSION['cste']['DIMS'].' - '.$_SESSION['cste']['TASK_TO_REALIZE'].' '.$_SESSION['cste']['FROM_THE_PART_OF'].' '.$from->fields['firstname'].' '.$from->fields['lastname'].'</title>
		    <meta http-equiv="content-type" content="text/html; charset=utf-8">
	        <meta name="title" content="'.$_SESSION['cste']['TASK_TO_REALIZE'].' '.$_SESSION['cste']['FROM_THE_PART_OF'].' '.$from->fields['firstname'].' '.$from->fields['lastname'].'">
			<meta name="description" content="'.$_SESSION['cste']['TASK_TO_REALIZE'].' '.$_SESSION['cste']['FROM_THE_PART_OF'].' '.$from->fields['firstname'].' '.$from->fields['lastname'].'">
		</head>
		<body>
		    <table width="100%">
		        <tr>
		            <td>
		                <!-- HEADER -->
		                <table align="center" border="0" cellpadding="0" cellspacing="0" width="770px">
		                    <tr>
		                        <td height="80px" width="770px"><img name="IC_Mail_Banner.png" src="'.dims::getInstance()->getProtocol().$_SERVER['HTTP_HOST'].'/common/img/views/todos/gfx/dims_banner.png" width="770" height="80" alt="Bannière Dims" style="display: block;" border="0"></td>
		                    </tr>
		                </table><!-- FIN HEADER -->
		                <!-- CONTENT -->
		                <table cellspacing="0" align="center" width="770" bgcolor="#F1F1F1">
		                    <tr>
		                        <td valign="top">
		                            <table cellpadding="40" cellspacing="0" align="center" width="600">
		                                <tr>
		                                    <td valign="top" align="justigy"><font face="Arial" size="4" color="#434343">
		                                    	'.$_SESSION['cste']['_WELCOME'].' '.$dest->fields['firstname'].' '.$dest->fields['lastname'].',<br/><br/>'.$from->getLightAttribute('picture').' '.
		                                    	$from->fields['firstname'].' '.$from->fields['lastname'].' '.$_SESSION['cste']['ASSIGNED_A_TASK_TO_YOU'].' '.$on_the_record.' <b>'.$title_object.'</b> : <br/><br/><span style="font-size: 0.9em; color: #555;"><span style="color: #95DE2C;">"</span>&nbsp;'.nl2br($this->fields['content']).'&nbsp;<span style="color: #95DE2C;">"</span></span><br/><br>'.
		                                    	$_SESSION['cste']['YOU_CAN_ACCESS_TO_IT_BY_CLICKING_ON'].' : <a href="'.$link_to.'" style="color: #95DE2C;">'.$title_object.'</a>.<br/><br/>


		                                    </td>
		                                </tr>
		                            </table>
		                        </td>
		                    </tr>
		                </table><!-- FIN CONTENT -->
		                <!-- FOOTER -->
		                <table cellspacing="0" align="center" width="770">
		                    <tr bgcolor="#D9DADB">
		                        <td valign="top">
		                            <table cellpadding="20" cellspacing="0" align="center" width="500">
		                                <tr>
		                                    <td valign="top" align="center"><font face="Arial" size="1" color="#5F5F5F">Dims Portal v5</font></td>
		                                </tr>
		                            </table>
		                        </td>
		                    </tr>
		                </table><!-- FIN FOOTER -->
		            </td>
		        </tr>
		    </table>
		</body>
		</html>
		';
?>
