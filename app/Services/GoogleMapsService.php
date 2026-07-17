<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    public function findPlace(string $shopName, string $address): ?array
    {
        $apiKey = config('services.google_maps.server_key');

        // APIキー未設定なら通信しない
        if (empty($apiKey)) {
            return null;
        }

        $query = trim($shopName . ' ' . $address);

        if ($query === '') {
            return null;
        }

        $cacheKey = 'google_place_' . sha1($query);

        return Cache::remember(
            $cacheKey,
            now()->addDays(7),
            function () use ($query, $apiKey) {
                try {
                    $response = Http::timeout(10)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'X-Goog-Api-Key' => $apiKey,
                            'X-Goog-FieldMask' => implode(',', [
                                'places.id',
                                'places.displayName',
                                'places.formattedAddress',
                                'places.rating',
                                'places.userRatingCount',
                                'places.googleMapsUri',
                            ]),
                        ])
                        ->post(
                            'https://places.googleapis.com/v1/places:searchText',
                            [
                                'textQuery' => $query,
                                'languageCode' => 'ja',
                                'regionCode' => 'JP',
                                'maxResultCount' => 1,
                            ]
                        );

                    if ($response->failed()) {
                        Log::warning('Google Places API request failed.', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);

                        return null;
                    }

                    return $response->json('places.0');
                } catch (\Throwable $exception) {
                    Log::error('Google Places API connection failed.', [
                        'message' => $exception->getMessage(),
                    ]);

                    return null;
                }
            }
        );
    }
}