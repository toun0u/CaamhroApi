<?php

//Interface telephony générique à tout les opérateurs téléphoniques
interface Telephony
{
  //méthodes génériques
  public function makeCall($callee, $calleeName);
  public function sendSMS($callee, $text);
}
?>