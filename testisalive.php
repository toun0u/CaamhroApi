<?php
header('Content-Type: text/html; charset=utf-8');
// $url = "https://ws-bordeauxpreprod.dimsportal.fr";
// $pass = "XaidojeW~a7Hesia:tiegh4mi";
//$pass = '`9,>EUH\iyN7*7X$f94u`pif^'; // ORANGE
//$ip = "109.190.147.170"; // ovh


$pwd = base64_encode(hash_hmac("sha256", utf8_encode("POST127.0.0.1"), '81f0bf647bf014c935638bb1db968a85'));
//die($pwd);

//client humain en post
//$pwd = "GET127.0.0.1";
//$pwd = "DELETE127.0.0.1";
//$pwd = "OPTIONS127.0.0.1";
$pwd = "POST127.0.0.1";
$pwd .= 'Login'.'humainTest'; //dans le pos
$pwd .= 'Password'.'toto'; //dans le post
//$pwd .= 'Token'.'56b2220e3ca1d9.39423583'; //dans le post
var_dump($pwd);
//$pwd = base64_encode($pwd);
$pwd = base64_encode(hash_hmac("sha256", utf8_encode($pwd), '81f0bf647bf014c935638bb1db968a85'));
//$pwd = hash_hmac("sha256", utf8_encode($pwd), '81f0bf647bf014c935638bb1db968a85');
//var_dump($pwd);
//$pwd = base64_encode($pwd);
//$pwd = 'clientTest:'.$pwd;
//$pwd = base64_encode($pwd);
//$pwd = hashgen("toto", 'efc4c939cdf18de5ba2660c40dc82244' );
die($pwd);




//$ip = "127.0.0.1";
//$ip = "82.247.249.174";
$ip = "82.247.249.174";
//$url = "https://ws-bordeaux.dimsportal.fr";
//$url = "http://apibordeaux";
//$pass = 'ke6Aemah\s"ieFu0Yie7ieCae@'; // OW
$url = "https://ws-bordeauxpreprod.dimsportal.fr";
$pass = 'yooy7ohthei.gh$ieLio"Th$o8';
//$url = "http://caahmro-api/test/tespostman";

// $pwd = "GET$ip";
// //$pwd = "GET";
// $pwd .= 'email' . 'y.mareschal@gmail.com';

// $options = array(
//     CURLOPT_HEADER               => false,
//     CURLOPT_RETURNTRANSFER       => true,
//     CURLOPT_HTTPHEADER           => array('Expect:'),
//     CURLOPT_TIMEOUT              => 100,
//     CURLOPT_USERPWD             => "OW:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
//     CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
//     CURLOPT_SSL_VERIFYPEER      => false,
//     CURLOPT_SSL_VERIFYHOST      => false,
//     CURLOPT_CAINFO              => "/data/www/velopolis/PiscineBordeaux(Netlor).txt",
// );

// $ch = curl_init("$url/customers/412112/extuniqueemail?email=y.mareschal@gmail.com");
// curl_setopt_array($ch, $options);
// $json_response = json_decode(curl_exec($ch),true);
// $header = curl_getinfo($ch);
// curl_close($ch);
// echo "<h1>UNIQUE EMAIL</h1>";
// echo '<pre>';
// print_r($json_response);
// echo '</pre>';
// die();


// OW:YWZkMTc4MjM2NjIwNjg4Njg2YTA2YTk4YTQ5MGI1Y2UwMjcxNTVlYWI3MTM4NDRjMDNmNDI3OTJjNGMxZDdkOA==
// OW:YWZkMTc4MjM2NjIwNjg4Njg2YTA2YTk4YTQ5MGI1Y2UwMjcxNTVlYWI3MTM4NDRjMDNmNDI3OTJjNGMxZDdkOA==







/*$pwd = "POST$ip";
$logs = array(
    'firstname' => 'Thomas',
    'lastname' => 'Metois',
    'email' => 'thomas+bo7@netlor.fr',
);
foreach($logs as $k => $v){
    $pwd .= $k.$v;
}
$options = array(
    CURLOPT_HEADER             	=> false,
    CURLOPT_RETURNTRANSFER 		=> true,
    CURLOPT_HTTPHEADER         	=> array('Expect:'),
    CURLOPT_TIMEOUT        		=> 100,
    CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
    CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
    CURLOPT_POST                => true,
    CURLOPT_POSTFIELDS          => http_build_query($logs),
    CURLOPT_SSL_VERIFYPEER      => true,
    CURLOPT_SSL_VERIFYHOST      => false,
    CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
);

$ch = curl_init("$url/customers");
curl_setopt_array($ch, $options);

$json_response = curl_exec($ch);
$header = curl_getinfo($ch);
curl_close($ch);

echo "<h1>Create account</h1>";
echo '<pre>';
print_r($json_response);
echo '</pre>';

die();*/

// $pwd = "GET$ip";
// $options = array(
//     CURLOPT_HEADER             	=> false,
//     CURLOPT_RETURNTRANSFER 		=> true,
//     CURLOPT_HTTPHEADER         	=> array('Expect:'),
//     CURLOPT_TIMEOUT        		=> 100,
//     CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
//     CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
//     CURLOPT_SSL_VERIFYPEER      => false,
//     CURLOPT_SSL_VERIFYHOST      => false,
//    // CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
// );

// $ch = curl_init("$url/etabs");
// curl_setopt_array($ch, $options);

// $json_response = json_decode(curl_exec($ch),true);
// $header = curl_getinfo($ch);
// curl_close($ch);

// echo "<h1>Établissements</h1>";
// echo '<pre>';
// print_r($json_response);
// echo '</pre>';
// die();

// $pwd = "GET$ip";
// $options = array(
//     CURLOPT_HEADER             	=> false,
//     CURLOPT_RETURNTRANSFER 		=> true,
//     CURLOPT_HTTPHEADER         	=> array('Expect:'),
//     CURLOPT_TIMEOUT        		=> 100,
//     CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
//     CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
//     CURLOPT_SSL_VERIFYPEER      => false,
//     CURLOPT_SSL_VERIFYHOST      => false,
//    // CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
// );

// $ch = curl_init("$url/etabs/4/getetabfmi");
// curl_setopt_array($ch, $options);
// $json_response = json_decode(curl_exec($ch),true);
// $header = curl_getinfo($ch);
// curl_close($ch);
// echo "<h1>FMI 4</h1>";
// echo '<pre>';
// print_r($json_response);
// echo '</pre>';
// //die();


$pwd = "POST$ip";
/*$logs = array(
    'login' => 'compte1',
    'password' => 'compte1*',
);*/
/*$logs = array(
    'login' => 'ymareschal_1',
    'password' => 'x78n5hb6',
);
$logs = array(
    'login' => 'azuliani',
    'password' => 'uigyhpbq',
);*/
$logs = array(
    'login' => 'sabalea',
    'password' => 'ervxgya6',
);
foreach($logs as $k => $v){
    $pwd .= $k.$v;
}
$options = array(
    CURLOPT_HEADER             	=> false,
    CURLOPT_RETURNTRANSFER 		=> true,
    CURLOPT_HTTPHEADER         	=> array('Expect:'),
    CURLOPT_TIMEOUT        		=> 100,
    CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
    CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
    CURLOPT_POST                => true,
    CURLOPT_POSTFIELDS          => http_build_query($logs),
    CURLOPT_SSL_VERIFYPEER      => false,
    CURLOPT_SSL_VERIFYHOST      => false,
   // CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
);

$ch = curl_init("$url/customers/authuser");
curl_setopt_array($ch, $options);

$json_response = json_decode(curl_exec($ch),true);
$header = curl_getinfo($ch);
curl_close($ch);

echo "<h1>Authentification</h1>";
echo '<pre>';
print_r($logs);
echo '</pre>';
echo '<pre>';
print_r($json_response);
echo '</pre>';

die();
// $token = isset($json_response['token'])?$json_response['token']:"";
// $id = isset($json_response['id'])?$json_response['id']:0;

// $pwd = "GET$ip"."token".$token;
// $options = array(
//     CURLOPT_HEADER             	=> false,
//     CURLOPT_RETURNTRANSFER 		=> true,
//     CURLOPT_HTTPHEADER         	=> array('Expect:'),
//     CURLOPT_TIMEOUT        		=> 100,
//     CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
//     CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
//     //CURLOPT_POST                => true,
//     //CURLOPT_POSTFIELDS          => http_build_query($logs),
//     CURLOPT_SSL_VERIFYPEER      => true,
//     CURLOPT_SSL_VERIFYHOST      => false,
//     CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
// );

// $ch = curl_init("$url/customers/".$id."/isalive?token=".$token);
// curl_setopt_array($ch, $options);

// $json_response = json_decode(curl_exec($ch),true);
// $header = curl_getinfo($ch);
// curl_close($ch);

// echo '<h1>PWD</h1>'.base64_encode("Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass))).'<br>';

// echo "<h1>Is Alive</h1>";
// echo '<pre>';
// print_r($json_response);
// var_dump($json_response['alive']);
// echo '</pre>';

/*$email = "y.mareschal@gmail.com";
//$email = "thomas@netlor.fr";
$pwd = "GET$ip"."token".$token."email".$email;
$options = array(
    CURLOPT_HEADER             	=> false,
    CURLOPT_RETURNTRANSFER 		=> true,
    CURLOPT_HTTPHEADER         	=> array('Expect:'),
    CURLOPT_TIMEOUT        		=> 100,
    CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
    CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
    //CURLOPT_POST                => true,
    //CURLOPT_POSTFIELDS          => http_build_query($logs),
    CURLOPT_SSL_VERIFYPEER      => true,
    CURLOPT_SSL_VERIFYHOST      => false,
    CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
);

$ch = curl_init("$url/customers/".$id."/uniqueemail?token=".$token."&email=".$email);
curl_setopt_array($ch, $options);

$json_response = json_decode(curl_exec($ch),true);
$header = curl_getinfo($ch);
curl_close($ch);

echo "<h1>Test Email connected : $email</h1>";
echo '<pre>';
print_r($json_response);
//var_dump($json_response['unique']);
echo '</pre>';


$email = "y2.mareschal@gmail.com";
//$email = "thomas@netlor.fr";
$pwd = "GET$ip"."email".$email;
$options = array(
    CURLOPT_HEADER             	=> false,
    CURLOPT_RETURNTRANSFER 		=> true,
    CURLOPT_HTTPHEADER         	=> array('Expect:'),
    CURLOPT_TIMEOUT        		=> 100,
    CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
    CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
    //CURLOPT_POST                => true,
    //CURLOPT_POSTFIELDS          => http_build_query($logs),
    CURLOPT_SSL_VERIFYPEER      => true,
    CURLOPT_SSL_VERIFYHOST      => false,
    CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
);

$ch = curl_init("$url/customers/uniqueemail?email=".$email);
curl_setopt_array($ch, $options);

$json_response = json_decode(curl_exec($ch),true);
$header = curl_getinfo($ch);
curl_close($ch);

echo "<h1>Test Email : $email</h1>";
echo '<pre>';
print_r($json_response);
var_dump($json_response['unique']);
echo '</pre>';*/



// $pwd = "GET$ip"."id$id"."token$token";
// $options = array(
//     CURLOPT_HEADER             	=> false,
//     CURLOPT_RETURNTRANSFER 		=> true,
//     CURLOPT_HTTPHEADER         	=> array('Expect:'),
//     CURLOPT_TIMEOUT        		=> 100,
//     CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
//     CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
//     //CURLOPT_POST                => true,
//     //CURLOPT_POSTFIELDS          => http_build_query($logs),
//     CURLOPT_SSL_VERIFYPEER      => false,
//     CURLOPT_SSL_VERIFYHOST      => false,
//     //CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
// );

// $ch = curl_init("$url/etabs/getproducts?id=$id&token=$token");
// curl_setopt_array($ch, $options);

// $json_response = json_decode(curl_exec($ch),true);
// $header = curl_getinfo($ch);
// curl_close($ch);

// echo "<h1>Get Products</h1>";
// echo '<pre>';
// print_r($json_response);
// echo '</pre>';
// foreach($json_response['etab'] as $e){
//     foreach($e['tarifs'] as $t){
//         echo $t['label']." ".$t['prix']." ".($t['allowedtobuy']?"OK":"KO").'<br>';
//     }
//     echo '<br>';
// }




// $pwd = "GET$ip"."token$token";
// $options = array(
//     CURLOPT_HEADER             	=> false,
//     CURLOPT_RETURNTRANSFER 		=> true,
//     CURLOPT_HTTPHEADER         	=> array('Expect:'),
//     CURLOPT_TIMEOUT        		=> 100,
//     CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
//     CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
//     //CURLOPT_POST                => true,
//     //CURLOPT_POSTFIELDS          => http_build_query($logs),
//     CURLOPT_SSL_VERIFYPEER      => false,
//     CURLOPT_SSL_VERIFYHOST      => false,
//    // CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
// );

// $ch = curl_init("$url/customers/$id/getuserabo?token=$token");
// curl_setopt_array($ch, $options);

// $json_response = json_decode(curl_exec($ch),true);
// $header = curl_getinfo($ch);
// curl_close($ch);

// echo "<h1>Account subscriptions</h1>";
// echo '<pre>';
// print_r($json_response);
// echo '</pre>';

// die();


/*$pwd = "POST$ip";
$logs = array(
    'token' => $token,
    'firstname' => 'thomas',
    'lastname' => 'metois',
    'email' => 'thomas@netlor.fr',
    'streetnum' => '93',
    'btq' => '',
    'address' => 'route de metz',
    'postalcode' => '54320',
    'city' => 'Maxéville',
    'country' => 'France',
    'nbProduct' => 1,
    'totalprice' => 33,
    'orderlines' => array(
        array(
            'id'=> 107,
            'id_etab' => 1,
            'price' => 33,
            'quantity'=>1,
            'totalprice'=>33,
            'tva'=>20,
        ),
    ),
);
foreach($logs as $k => $v){
    if (!is_array($v)) {
        $pwd .= $k.$v;
    }else $pwd .=$k."Array";
}
$options = array(
    CURLOPT_HEADER             	=> false,
    CURLOPT_RETURNTRANSFER 		=> true,
    CURLOPT_HTTPHEADER         	=> array('Expect:'),
    CURLOPT_TIMEOUT        		=> 100,
    CURLOPT_USERPWD             => "Orange:".base64_encode(hash_hmac("sha256", utf8_encode($pwd), $pass)),
    CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
    CURLOPT_POST                => true,
    CURLOPT_POSTFIELDS          => http_build_query($logs),
    CURLOPT_SSL_VERIFYPEER      => true,
    CURLOPT_SSL_VERIFYHOST      => false,
    CURLOPT_CAINFO              => "/var/www/PiscineBordeaux(Netlor)",
);

$ch = curl_init("$url/orders/setorder?id=$id");
curl_setopt_array($ch, $options);

$json_response = json_decode(curl_exec($ch),true);
$header = curl_getinfo($ch);
curl_close($ch);

echo "<h1>Set order</h1>";
echo '<pre>';
print_r($json_response);
echo '</pre>';*/
