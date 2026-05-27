<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Storage::extend('google', function ($app, $config) {
            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->addScope(Drive::DRIVE);
            $client->setAccessType('offline');

            if (empty($config['refreshToken'])) {
                throw new \RuntimeException('Missing GOOGLE_REFRESH_TOKEN.');
            }

            $accessToken = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);

            if (isset($accessToken['error'])) {
                $message = $accessToken['error_description'] ?? $accessToken['error'];

                if ($accessToken['error'] === 'invalid_grant') {
                    $message .= ' The Google refresh token is invalid, expired, revoked, or belongs to a different OAuth client.';
                }

                throw new \RuntimeException(
                    'Google token refresh failed: ' . $message
                );
            }

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folderId']);

            return new Filesystem($adapter);
        });
    }
}
