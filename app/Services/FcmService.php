<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Minimal Firebase Cloud Messaging (HTTP v1 API) sender. Talks to
 * fcm.googleapis.com directly using a service-account JSON — no Google
 * client library dependency, just a hand-rolled RS256 JWT for the OAuth2
 * token exchange (Laravel already ships Guzzle via Http::, and PHP's
 * built-in openssl extension covers the RS256 signature).
 */
class FcmService
{
    public function isConfigured(): bool
    {
        return filled(config('services.fcm.project_id')) && filled(config('services.fcm.credentials_path'))
            && file_exists(config('services.fcm.credentials_path'));
    }

    /**
     * @param string $deviceToken
     * @param array{title:string,body:string,data?:array<string,string>} $message
     */
    public function send(string $deviceToken, array $message): bool
    {
        if (!$this->isConfigured()) {
            Log::info('FCM push skipped: not configured', ['title' => $message['title'] ?? null]);
            return false;
        }

        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return false;
        }

        $projectId = config('services.fcm.project_id');

        $response = Http::withToken($accessToken)->post(
            "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $message['title'],
                        'body' => $message['body'],
                    ],
                    'data' => array_map('strval', $message['data'] ?? []),
                ],
            ]
        );

        if ($response->failed()) {
            Log::warning('FCM push failed', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        }

        return true;
    }

    private function getAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', 55 * 60, function () {
            $credentials = json_decode(file_get_contents(config('services.fcm.credentials_path')), true);

            $now = time();
            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64UrlEncode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $signatureInput = "{$header}.{$claims}";
            $privateKey = openssl_pkey_get_private($credentials['private_key']);

            if (!$privateKey) {
                Log::error('FCM: could not read private key from service account credentials');
                return null;
            }

            openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $jwt = $signatureInput . '.' . $this->base64UrlEncode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->failed()) {
                Log::error('FCM: OAuth2 token exchange failed', ['body' => $response->body()]);
                return null;
            }

            return $response->json('access_token');
        });
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
