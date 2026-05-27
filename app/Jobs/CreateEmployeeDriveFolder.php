<?php

namespace App\Jobs;

use App\Models\Employee;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateEmployeeDriveFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Employee $employee) {}

    public function handle(): void
    {
        try {
            $client = $this->getGoogleClient();
            $service = new Drive($client);

            // Folder name: "EMP-0001 - Juan Dela Cruz"
            $folderName = $this->employee->employee_id . ' - ' . $this->employee->full_name;

            $folderMetadata = new DriveFile([
                'name'     => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents'  => [config('google.folder_id')],
            ]);

            $folder = $service->files->create($folderMetadata, [
                'fields' => 'id, webViewLink',
            ]);

            // Make folder viewable by anyone with the link
            $permission = new Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);
            $service->permissions->create($folder->id, $permission);

            // Save folder info back to employee record
            $this->employee->update([
                'drive_folder_id'  => $folder->id,
                'drive_folder_url' => $folder->webViewLink,
            ]);

            Log::info("Drive folder created for {$this->employee->full_name}");

        } catch (\Exception $e) {
            Log::error("Drive folder creation failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function getGoogleClient(): Client
    {
        $client = new Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline');

        $refreshToken = config('google.refresh_token');

        if (!$refreshToken) {
            throw new \RuntimeException('Missing GOOGLE_REFRESH_TOKEN.');
        }

        $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        if (isset($accessToken['error'])) {
            $message = $accessToken['error_description'] ?? $accessToken['error'];

            if ($accessToken['error'] === 'invalid_grant') {
                $message .= ' The Google refresh token is invalid, expired, revoked, or belongs to a different OAuth client.';
            }

            throw new \RuntimeException(
                'Google token refresh failed: ' . $message
            );
        }

        return $client;
    }
}
