<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$client = new Google\Client();

$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);

$client->setRedirectUri('http://localhost/oauth.php');

$client->setScopes([
    Google\Service\Drive::DRIVE
]);

$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header("Location: $authUrl");
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

echo "<pre>";
print_r($token);
echo "</pre>";

if (!empty($token['refresh_token'])) {
    echo "\nREFRESH TOKEN:\n";
    echo $token['refresh_token'];
}