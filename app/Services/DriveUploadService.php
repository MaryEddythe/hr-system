<?php

namespace App\Services;

use App\Models\Employee;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class DriveUploadService
{
    private Drive $service;

    public function __construct()
    {
        $client = new Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline');
        
        // Set the refresh token and let it auto-refresh
        $token = ['refresh_token' => config('google.refresh_token')];
        $client->setAccessToken($token);
        
        // If the token is expired, this will refresh it
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        }
        
        $this->service = new Drive($client);
    }

    public function uploadToEmployeeFolder(UploadedFile $file, Employee $employee): array
    {
        if (!$employee->drive_folder_id) {
            throw new \Exception("No Drive folder found for this employee.");
        }

        $fileMetadata = new DriveFile([
            'name'    => $file->getClientOriginalName(),
            'parents' => [$employee->drive_folder_id],
        ]);

        $uploaded = $this->service->files->create($fileMetadata, [
            'data'       => file_get_contents($file->getRealPath()),
            'mimeType'   => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields'     => 'id, webViewLink, name',
        ]);

        Log::info("File uploaded for {$employee->full_name}: {$uploaded->name}");

        return [
            'file_id'   => $uploaded->id,
            'file_name' => $uploaded->name,
            'file_url'  => $uploaded->webViewLink,
        ];
    }
}