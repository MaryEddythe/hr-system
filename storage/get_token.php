<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$client = new Google\Client();

$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);

$client->setScopes([
    Google\Service\Drive::DRIVE
]);

$client->setAccessType('offline');
$client->setPrompt('consent');

/*
 * FORCE OUT-OF-BAND STYLE FLOW (desktop behavior)
 */
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');

$authUrl = $client->createAuthUrl();

echo "\nOPEN THIS URL:\n\n";
echo $authUrl . "\n\n";

echo "After login, Google will show a CODE.\n";
echo "Paste ONLY the code here: ";

$code = trim(fgets(STDIN));

$token = $client->fetchAccessTokenWithAuthCode($code);

echo "\nTOKEN:\n";
print_r($token);

if (!empty($token['refresh_token'])) {
    echo "\nREFRESH TOKEN:\n";
    echo $token['refresh_token'] . "\n";
}