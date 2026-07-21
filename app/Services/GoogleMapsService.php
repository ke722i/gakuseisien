<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    public function findPlace(string $shopName, string $address): ?array
    {
        $apiKey = config('services.google_maps.server_key');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => implode(',', [
                        'places.id',
                        'places.rating',
                        'places.userRatingCount',
                        'places.googleMapsUri',
                    ]),
                ])
                ->post(
                    'https://places.googleapis.com/v1/places:searchText',
                    [
                        'textQuery' => trim($shopName . ' ' . $address),
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
        } catch (\Throwable $e) {
            Log::error('Google Places API error.', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}