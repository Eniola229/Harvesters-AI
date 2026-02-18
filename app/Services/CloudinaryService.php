<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class CloudinaryService
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $uploadUrl;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey    = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
        $this->uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}";
    }

    /**
     * Upload an image or video file to Cloudinary.
     */
    public function upload(UploadedFile $file, string $folder = 'harvesters'): array
    {
        $resourceType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
        $timestamp    = time();
        $params = [
            'folder'    => $folder,
            'timestamp' => $timestamp,
        ];
        $signature = $this->generateSignature($params);

        $response = Http::attach(
            'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
        )->post("{$this->uploadUrl}/{$resourceType}/upload", array_merge($params, [
            'api_key'   => $this->apiKey,
            'signature' => $signature,
        ]));

        if ($response->successful()) {
            return [
                'url'        => $response->json('secure_url'),
                'public_id'  => $response->json('public_id'),
                'resource_type' => $resourceType,
            ];
        }

        throw new \Exception('Cloudinary upload failed: ' . $response->body());
    }

    /**
     * Upload from URL (e.g., Twilio media)
     */
    public function uploadFromUrl(string $url, string $folder = 'harvesters'): array
    {
        $timestamp = time();
        $params = [
            'folder'    => $folder,
            'timestamp' => $timestamp,
        ];
        $signature = $this->generateSignature($params);

        $response = Http::post("{$this->uploadUrl}/image/upload", array_merge($params, [
            'file'      => $url,
            'api_key'   => $this->apiKey,
            'signature' => $signature,
        ]));

        if ($response->successful()) {
            return [
                'url'       => $response->json('secure_url'),
                'public_id' => $response->json('public_id'),
            ];
        }

        throw new \Exception('Cloudinary URL upload failed: ' . $response->body());
    }

    /**
     * Delete a resource from Cloudinary.
     */
    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        $timestamp = time();
        $params    = ['public_id' => $publicId, 'timestamp' => $timestamp];
        $signature = $this->generateSignature($params);

        $response = Http::post("{$this->uploadUrl}/{$resourceType}/destroy", array_merge($params, [
            'api_key'   => $this->apiKey,
            'signature' => $signature,
        ]));

        return $response->successful() && $response->json('result') === 'ok';
    }

    protected function generateSignature(array $params): string
    {
        ksort($params);
        $paramString = implode('&', array_map(
            fn($k, $v) => "{$k}={$v}",
            array_keys($params),
            array_values($params)
        ));
        return sha1($paramString . $this->apiSecret);
    }
}