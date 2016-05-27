<?php
$handle = fopen($_FILES['srcfilect']['tmp_name'], "r");
                    $_CURRENT_KEY = 0;
                    $_CURRENT_LINE = 1;
                    $_PREV_LETTER = "";
                    $_PREV_PREV_LETTER = "";
                    $_PREV_PREV_PREV_LETTER = "";
                    $_INTO_KEY = false;

while ($line = fgets($handle)){
                            // Ligne de description de la structure du fichier
                            if(count($_FIELDS) == 0){
                                $content = explode(',',$line);
                                foreach($content AS $key => $value){
                                    $value = strtolower(trim(str_replace('"','',$value)));
                                    //On vérifie si on connait la clé
                                    switch($value){
                                        case "firstname":
                                        case "prenom":
                                        case "pr".utf8_decode("é")."nom":
                                        case "first name":
                                            $value = "firstname";
                                        break;

                                        case "lastname":
                                        case "nom":
                                        case "last name":
					                        $value = "lastname";
                                        break;

                                        case "middlename":
                                        case "midle name":
                                        case "deuxi".utf8_decode("è")."mepr".utf8_decode("é")."nom":
                                            $value = "middlename";
                                        break;

                                        case "email":
                                        case "email address":
                                        case "e-mail address":
                                        case "courriel":
                                        case "emailaddress":
                                        case "mail":
                                        case "adressedemessagerie":
                                            $value = "email";
                                        break;

                                        case "company":
                                        case "societe":
                                        case "soci".utf8_decode("é")."t".utf8_decode("é") :
                                        case "company name":
                                        case "companyname":
                                        case "entreprise":
                                            $value = "company";
                                        break;

                                        case "businesspostalcode":
                                        case "codepostalbureau":
                                        case "business postal code":
                                            $value = "cp";
                                        break;

                                        case "city":
                                        case "localite":
                                        case "ville":
                                        case "businesscity":
                                        case "business city":
                                        case "villebureau":
                                            $value = "ville";
                                        break;

                                        case "ruebureau":
                                        case "businessstreet":
                                        case "business street":
                                            $value = "address";
                                        break;

                                        case "ruebureau2":
                                        case "businessstreet2":
                                        case "business street 2":
                                            $value = "address2";
                                        break;

                                        case "ruebureau3":
                                        case "businessstreet3":
                                        case "business street 3":
                                            $value = "address3";
                                        break;

                                        case "paysregionbureau":
                                        case "business country/region":
                                        case "businnescountryregion":
                                        case "d".utf8_decode("é")."pr".utf8_decode("é")."gionbureau":
                                            $value = "country";
                                        break;

                                        case "civilite":
                                        case "title":
                                        case "titre":
                                            $value = "civilite";
                                        break;

                                        case "job title":
                                        case "profession":
                                            $value = "professional";
                                        break;

                                        case "mobile phone":
                                        case "mobilephone":
                                        case "telmobile":
										case "carphone":
                                            $value = "mobile";
                                        break;

                                        case "telephonebureau":
                                        case "t".utf8_decode("é")."l".utf8_decode("é")."phonebureau":
                                        case "businessphone":
                                        case "business phone":
                                            $value = "phone";
                                        break;

										case "telephonebureau2":
                                        case "t".utf8_decode("é")."l".utf8_decode("é")."phonebureau2":
                                        case "businessphone2":
                                        case "business phone2":
                                            $value = "phone2";
                                        break;

                                        case "telecopiebureau":
                                        case "t".utf8_decode("é")."l".utf8_decode("é")."copiebureau":
                                        case "businessfax":
                                        case "business fax":
                                            $value = "fax";
                                        break;

										case 'notes': //Traitement des commentaires
											$value="comment";
										break;
                                    }
                                    $_FIELDS[$key] = $value;
                                }

                                $_NB_COL = count($_FIELDS);
                            }else{

                                //Variable
                                for($i=0;$i<(strlen($line));$i++){
                                    $letter = $line[$i];
                                    if($letter != chr(13)){
                                        switch ($letter){
                                            // On rencontre une double quote
                                            case '"':
                                                if(($_PREV_LETTER == ",")&&(!$_INTO_KEY)){
                                                    $_CURRENT_KEY++;
                                                    if($_CURRENT_KEY == $_NB_COL){
                                                        $_CURRENT_KEY = 0;
                                                        $_CURRENT_LINE++;
                                                    }
                                                    $_INTO_KEY = true;
                                                }else if($_PREV_LETTER == ""){
                                                    $_CURRENT_KEY = 0;
                                                    $_INTO_KEY = true;
                                                }else if(($_PREV_LETTER == "\\")&&($_INTO_KEY)){
                                                    if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
                                                        $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
                                                    }else{
                                                        $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
                                                    }
                                                }elseif($_PREV_LETTER == chr(10) && !$_INTO_KEY){
													$_INTO_KEY = true;
													$_CURRENT_KEY = 0;
												}
                                            break;

                                            case ',':
                                                if(($_PREV_LETTER == '"') && $_INTO_KEY){
                                                    $_INTO_KEY = false;
                                                }else if($_PREV_LETTER == ',' && !$_INTO_KEY){
                                                    $_CURRENT_KEY++;
                                                    if($_CURRENT_KEY == $_NB_COL){
                                                        $_CURRENT_KEY = 0;
                                                        $_CURRENT_LINE++;

                                                    }
                                                }else if(($_PREV_LETTER == ' ') && !$_INTO_KEY){
                                                    $_CURRENT_KEY++;
                                                    if($_CURRENT_KEY == $_NB_COL){
                                                        $_CURRENT_KEY = 0;
                                                        $_CURRENT_LINE++;

                                                    }
                                                }else if($_INTO_KEY){
                                                    if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
                                                        $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
                                                    }else{
                                                        $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
                                                    }
                                                }
                                            break;

                                            case chr(10):
                                                switch($_PREV_LETTER){
                                                    case '"':
                                                        if($_PREV_PREV_LETTER == ',' && $_INTO_KEY){
                                                            if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
                                                                $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
                                                            }else{
                                                                $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
                                                            }
                                                        }else if($_PREV_PREV_LETTER != ',' && $_INTO_KEY){
                                                            $_CURRENT_LINE++;

                                                            $_INTO_KEY = false;
                                                            $_CURRENT_KEY=0;
                                                        }
                                                    break;

                                                    case ",":
                                                        if(!$_INTO_KEY){
                                                            $_CURRENT_LINE++;

                                                            $_INTO_KEY = false;
                                                            $_CURRENT_KEY=0;
                                                        }
                                                    break;

                                                    case chr(10):
                                                        if($_INTO_KEY){
                                                            if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
                                                                $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
                                                            }else{
                                                                $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
                                                            }
                                                        }
                                                    break;

                                                    default:
                                                        if($_INTO_KEY){
                                                            if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
                                                                $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
                                                            }else{
                                                                $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
                                                            }
                                                        }
                                                    break;
                                                }


                                            break;

                                            default:
                                                if($_INTO_KEY && $letter != chr(13)){
                                                    if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]])){
                                                        $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] .= $letter;
                                                    }else{
                                                        $_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE][$_FIELDS[$_CURRENT_KEY]] = $letter;
                                                    }
                                                }
                                            break;
                                        }

                                        $_PREV_PREV_LETTER = $_PREV_LETTER;
                                        $_PREV_LETTER = $letter;

                                    }

                                }

                            }
                    }
                    if(isset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE])){
                        if(count($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE])==0)
                            unset($_SESSION['dims']['IMPORT_CONTACT'][$_CURRENT_LINE]);
                    }

?>
