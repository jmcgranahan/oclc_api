<?php
   require_once('vendor/autoload.php');
   use OCLC\Auth\WSKey;
   use OCLC\User;
   use GuzzleHttp\Client;
   use GuzzleHttp\Exception\RequestException;
   
   include 'containers.inc.php';

   oclc_prod();

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