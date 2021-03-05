<?php
   require_once('vendor/autoload.php');
   use OCLC\Auth\WSKey;
   use OCLC\User;
   use GuzzleHttp\Client;
   use GuzzleHttp\Exception\RequestException;
   
   include("containers.inc.php");

   /* Assign the first argument passed to the service variable - sets whether Prod or Sand */
   if ($argc > 1) {
     $service=$argv[1];
   } else {
     print "Please supply a valid argument to the command line - either Production or Sandbox.";
     die();
   }

   if ($service == "Sandbox") {
     $key = $oclc_keys["sandbox"]["key"];
     $secret = $oclc_keys["sandbox"]["secret"];
     $registry_id = $oclc_keys["sandbox"]["registry_id"];
     $principal_id = $oclc_keys["sandbox"]["principal_id"];
     $idns = $oclc_keys["sandbox"]["idns"];
   } elseif ($service == "Production") {
     $key = $oclc_keys["production"]["key"];
     $secret = $oclc_keys["production"]["secret"];
     $registry_id = $oclc_keys["production"]["registry_id"];
     $principal_id = $oclc_keys["production"]["principal_id"];
     $idns = $oclc_keys["production"]["idns"];
   } else {
    print "Please enter either Sandbox or Production from the command line";
   }

   $wskey = new WSKey($key, $secret);
   
   $url = 'https://worldcat.org/bib/checkcontrolnumbers?oclcNumbers=31777094';
   
   $user = new User($registry_id, $principal_id, $idns);
   $options = array('user'=> $user);
   
   $authorizationHeader = $wskey->getHMACSignature('GET', $url, $options);
    
   $client = new Client();
   $headers = array();
   $headers['Authorization'] = $authorizationHeader;
   
   try {
        $response = $client->request('GET', $url, ['headers' => $headers]);
        echo $response->getBody(TRUE);
   } catch (RequestException $error) {
        echo $error->getResponse()->getStatusCode();
        echo $error->getResponse()->getBody(true);
   }
?>