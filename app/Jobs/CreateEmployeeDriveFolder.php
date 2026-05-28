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
            // guard: skip if folder already exists
            if ($this->employee->drive_folder_id) {
                Log::info("Drive folder already exists for employee_id={$this->employee->employee_id}, skipping creation.");
                return;
            }

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

            // get id and link reliably
            $folderId   = method_exists($folder, 'getId') ? $folder->getId() : ($folder->id ?? null);
            $folderLink = method_exists($folder, 'getWebViewLink') ? $folder->getWebViewLink() : ($folder->webViewLink ?? null);

            if (!$folderId) {
                throw new \RuntimeException('Failed to retrieve created folder id from Drive response.');
            }

            // Make folder viewable by anyone with the link (no email notifications)
            $permission = new Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);
            $service->permissions->create($folderId, $permission, ['sendNotificationEmail' => false]);

            // Save folder info back to employee record
            $this->employee->update([
                'drive_folder_id'  => $folderId,
                'drive_folder_url' => $folderLink,
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

        // fetchAccessTokenWithRefreshToken() internally calls setAccessToken() on success,
        // so the client is already authenticated after this call.
        // We only need to check for errors — do NOT call setAccessToken() again.
        $accessTokenResponse = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        if (isset($accessTokenResponse['error'])) {
            $message = $accessTokenResponse['error_description'] ?? $accessTokenResponse['error'];

            if ($accessTokenResponse['error'] === 'invalid_grant') {
                $message .= ' The Google refresh token is invalid, expired, revoked, or belongs to a different OAuth client.';
            }

            throw new \RuntimeException(
                'Google token refresh failed: ' . $message
            );
        }

        if (empty($accessTokenResponse['access_token'])) {
            throw new \RuntimeException('Failed to obtain access_token from Google API response.');
        }

        // Client is already authenticated — no setAccessToken() call needed.

        return $client;
    }
}