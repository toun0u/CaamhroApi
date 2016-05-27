<!-- Page template des logs d'appels -->
<h4>Journal d'appels</h4><br/>
<p style="font-weight:bold";>Filtrer les appels :</p><br/>

<!-- list des filtres de recherche via un formulaire -->
<span id='spandi' class='cell_log'>
<form id="form_telephony" name="myform" method="post" action="<?= Gescom\get_path(array('c'=>'telephony', 'a'=>'index')); ?>">
		<div class='cell_log_field'><input type="text" name="dateStart" id="datepickerstart" placeholder="date début" value="<?php echo date("d/m/Y"); ?>"></div>
		<div class='cell_log_field'><input type="text" name="dateEnd" id="datepickerend" placeholder="date fin"></div>
		<div class='cell_log_field'><input type="text" name="nom" id="nomid" placeholder="nom"></div>
		<div class='cell_log_field'><input type="text" name="prenom" id="prenomid" placeholder="prenom"></div>
		<div class='cell_log_field'><input type="text" name="numero" id="numeroid" placeholder="numéro"></div>
		<div class='cell_log_field'>
			<select name="event" id="eventid">
	            <option value =""> Tous les appels </option>
	            <option value="NORMAL_INC_CALL">Appels entrants</option>
	            <option value="NORMAL_OUT_CALL">Appels sortants</option>
	            <option value="MISSED_CALL">Appels manqués</option>
	            <option value="FAILED_CALL">Appels echoués</option>
       		</select>
		</div>
		<div class='cell_log_field'><input name='rechercher' onclick="$('[name=offset]').attr( 'value', 0 );" type="submit" value="Rechercher" /></div>
		<!-- offset -->
		<input type="hidden"  name="offset"  value="0">
</form></span><br/>
<!-- affichage des logs via l'api keyyo -->
<?php
//recupération de l'offset de la requête pour les liens de paginations
if(isset($_POST['offset']))
	$getoff = $_POST['offset'] ;
else
	$getoff=0;

//construction de l'url pour l'api
$url="http://clib/api_keyyo.php/appels?limit=9&offset=$getoff&";
//$url="http://clib/api_keyyo.php/appels?limit=5&";
$fields=array();
$lastn="";
$firstn="";

//on récupère les champs des filtres de la précédente requête au passage
$isSameRequest=false;
foreach ($_POST as $key => $value) {
	if($value!=''){
		switch($key){

			case 'dateStart' :
				$ds = explode("/", "$value");
				$fields[$key] = urldecode("'".$ds[2].'-'.$ds[1].'-'.$ds[0]."'");
				
				echo "<script>$('[name=$key]').attr( 'value', '$value' );</script>";
			break;

			case 'dateEnd' :
				$de = explode("/", "$value");
				$fields[$key] = urldecode("'".$de[2].'-'.$de[1].'-'.$de[0]."'");
				
				echo "<script>$('[name=$key]').attr( 'value', '$value' );</script>";
			break;
			
			case 'nom' :
				$fields[$key] = urldecode("'".$value."'");
				
				echo "<script>$('[name=$key]').attr( 'value', '$value' );</script>";
			break;
			
			case 'prenom' :
				$fields[$key] = urldecode("'".$value."'");
				
				echo "<script>$('[name=$key]').attr( 'value', '$value' );</script>";
			break;
			
			case 'numero' :
				$valueforscript=$value;
				$value= substr($value, 1);
				$value="33".$value;
				$fields['call'] = $value;
				
				echo "<script>$('[name=$key]').attr( 'value', '$valueforscript' );</script>";
			break;
			
			case 'event' :
				$fields['event'] = $value;
				
				echo "<script>$('[name=$key]').attr( 'value', '$value' );</script>";
			break;

			case 'offset' :
				echo "<script>$('[name=$key]').attr( 'value', '$value' );</script>";
		}
	}
}

//assemblage de l'url
if(sizeof($fields)>0){
	foreach ($fields as $key => $value) {
		$url.="$key=$value&";
	}
}else{
	$url="http://clib/api_keyyo.php/appels?dateStart="."'".date('Y-m-d')."'"."&limit=9&offset=$getoff&";
}
$url = substr($url, 0, -1); 

// echo $url;

echo "<script>$('input[value=Rechercher]').click(function(){
		$.get('api_keyyo.php/appels',function(data){
			console.log('lol');
		});
	});</script>";

// Tableau contenant les options de téléchargement
$options=array(
      CURLOPT_URL            => "$url",    
      CURLOPT_HEADER         => false,      
      CURLOPT_FAILONERROR    => true,    //PROD FALSE      
      CURLOPT_RETURNTRANSFER => true,
);

// curl pour api
$CURL=curl_init();
if(empty($CURL)){die("ERREUR curl_init : Il semble que cURL ne soit pas disponible.");}
curl_setopt_array($CURL,$options);
$json=curl_exec($CURL);        
if(curl_errno($CURL)){
    echo "ERREUR curl_exec : ".curl_error($CURL);
}
curl_close($CURL);
// var_dump($json);
$json=objectToArray(json_decode($json));

	//messagerie Keyyo
	$messagerie='33819051123333836782';

	//compteur pour id des divs
	$cpt=0;
	if(sizeof($json)>0){

		//on parcourt le résultat de la requête curl
		foreach($json as $key => $value){


			//identification du numero 
			$numero=null;
			if( ($json[$key]['caller'] == "anonymous") || ($json[$key]['callee'] == "anonymous") )
				$numero="Numéro inconnu";
			else{
				if(($json[$key]['caller'])==($json[$key]['account']))
					$numero=$json[$key]['callee'];
				else
					$numero=$json[$key]['caller'];
			}
		
			if($numero!=$messagerie){

				echo "<span class='cell_log'>";
				
				echo "<div class='cell_log'>".$json[$key]['dateStart']."</div>";
				echo "<div class='cell_log'>".$json[$key]['dateEnd']."</div>";
				
				//identification du nom prenom du contact
				if($json[$key]['idcontact'] != -1){
					echo "<div class='cell_log' id=ln".$cpt."></div>";
					echo "<div class='cell_log' id=fn".$cpt."></div>";
					echo "<script>
						var nom_contact='';
						var a=Telephony.getName('$numero').done(function(data){
							if (data!='' && data.indexOf('+')<0){
								nom_contact=data;
								nom_contact=nom_contact.split('|');
							}
							$('div.cell_log#ln".$cpt."').text(nom_contact[1]);
							$('div.cell_log#fn".$cpt."').text(nom_contact[0]);
						});
					</script>";
				}else{
					if($numero!="Numéro inconnu"){
						$path=Gescom\get_path(array('c'=>'telephony', 'a'=>'form_contact', 'num' => "$numero"));
						echo '<div class="cell_log"  id=ln".$cpt."><a data-numero='.$numero.' id="add_business_c" data-tabable="true" title="Ajouter ce contact" style="cursor: pointer" href='.$path.'> Contact Inconnu</a></div>';
					}else
						echo "<div class='cell_log' id=ln".$cpt.">Contact inconnu</div>";
					echo "<div class='cell_log' id=fn".$cpt.">Contact inconnu</div>";
				}
				
				//numero
				if($numero!="Numéro inconnu"){
					$numero = substr($numero, 2);
					$numero = "0".$numero; 
				}
					echo "<div class='cell_log'><div class='cell_log'><span data-phone=$numero data-callname=''>$numero</span></div></div>";
				
				
				//evenement
				switch($json[$key]['event']){
					case "NORMAL_INC_CALL" :
						echo "<div class='cell_log'>Appel entrant</div>";
					break;

					case "NORMAL_OUT_CALL" :
						echo "<div class='cell_log'>Appel sortant</div>";
					break;

					case "MISSED_CALL" :
						echo "<div class='cell_log'>Appel manqué</div>";
					break;

					case "FAILED_CALL" :
						echo "<div class='cell_log'>Appel échoué</div>";
					break;

					default :
						echo "<div class='cell_log'>error</div>"; 		
				}

				//prise de notes
				$resume=urlencode($json[$key]['resume']);
				$ref=urlencode($json[$key]['callref']);
				$path=Gescom\get_path(array('c'=>'telephony', 'a'=>'form_note', 'resume' => $resume, 'ref' => $ref, 'event' => $json[$key]['event'] ));
				echo '<a data-tabable="true" href='.$path.'><img class="note_appel" src="/common/img/detail.png" alt="notes"></a>';
				
				echo "</span>";
				$cpt++;
			}
		}

	}else{
		echo "Aucun résultats !";
		
	}
?>

<div style= "clear :both">
<!-- Page suivante / précédente-->
<br/><a href="javascript: submitformprev()">Page précedente</a>
<a style= "float :right" href="javascript: submitformnext()">Page suivante</a>


<!-- page d'accueil -->
<br/><br/>
<a href="admin.php?op=dashboard&a=index">Retour</a>
<hr>
</div>

<!-- css -->
<style>
	div.cell_log {
		display: table-cell;
		min-width: 160px;
		padding-bottom: 5px;
	}

	div.cell_log_field {
		display: table-cell;
		min-width: 160px;
	}

	span.cell_log {
		display: table-row;
	}

	@media screen and (max-width: 1410px){
		body{
			/*background-color: red;*/
		}

		div.cell_log {
			display : block;
			min-height : 30px;
		}

		div.cell_log_field {
			display : block;
			min-height : 30px;
		}

		span.cell_log {
			min-width: 180px;
			display :inline-block;
			margin-bottom: 30px;
			margin-right: 10px;
		}

		span.cell_log#spandi {
			float : left ;
			margin-top: 14px;
		}

		span.cell_log:not(#spandi){
			 border-width:1px;
			 border-style:solid;
			 border-color:black;
			 border-radius: 5px;
			 text-align: center;
		}

		form#form_telephony{
		 min-height: 450px;
		}
	}
</style>

<!-- function js -->
<script type="text/javascript">
function submitformnext()
{
	var os = parseInt($('[name=offset]').attr( 'value' )) + 9;
	$('[name=offset]').attr( 'value', os );
	document.myform.submit();
}
</script>

<script type="text/javascript">
function submitformprev()
{
	var os = $('[name=offset]').attr( 'value' ) - 9;
	if(os<0)
		os=0;
	$('[name=offset]').attr( 'value', os );
	document.myform.submit();
}
</script>

<script type="text/javascript">
	 $("#datepickerstart").datepicker();
	 $("#datepickerend").datepicker();
</script>