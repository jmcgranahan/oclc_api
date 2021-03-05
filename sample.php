<?php
   require_once('vendor/autoload.php');
   use OCLC\Auth\WSKey;
   use OCLC\User;
   use GuzzleHttp\Client;
   use GuzzleHttp\Exception\RequestException;
   
   include("containers.inc.php");

   /* Assign the first argument passed to the service variable - sets whether Prod or Sand - and look for filename */
   if ($argc > 1) {
     $service=$argv[1];
     $filename=$argv[2];
   } else {
     print "Please supply a valid argument to the command line - either Production or Sandbox - and enter the name of the file to process.";
     die();
   }

   /* assign keys based upon user input */
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

   /* open file that contains the OCLC numbers to review */
   $file=fopen($filename,'r');

   $wskey = new WSKey($key, $secret);
   
   $baseurl = 'https://worldcat.org/bib/checkcontrolnumbers?oclcNumbers=';
   
   $user = new User($registry_id, $principal_id, $idns);
   $options = array('user'=> $user);
   
   while(!feof($file)) {
     $oclcnum=gets($file);
     $url=$baseurl.$oclcnum;
     process($url,$options);
   }

   function process($url,$options) {
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
   }
?>