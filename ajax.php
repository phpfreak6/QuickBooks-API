<?php
session_start();
require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

$client_id = $_POST['client_id'];
$client_sec = $_POST['client_sec'];
$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => $client_id,
    'ClientSecret' =>  $client_sec,
    'RedirectURI' => 'http://localhost/quick-book/callback.php',
    'scope' => 'com.intuit.quickbooks.accounting',
    'baseUrl' => "development"
	));
	
$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
echo $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
//echo "<pre>"; print_r($authUrl); die;
exit;

?>
