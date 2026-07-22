<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    /**
     * 検索結果を保持する期間。
     * 使用しているフィールド（営業時間・評価など）はEnterprise SKU扱いで
     * 無料枠が月1,000回しかないため、同じ問い合わせはキャッシュから返す。
     */
    private const SEARCH_CACHE_HOURS = 24;

    private const PLACE_CACHE_DAYS = 7;

    /**
     * Googleの店舗種別（types）を、申請フォームのジャンル選択肢に対応づける表。
     * 上にあるものほど具体的なので、先に一致したものを採用する。
     */
    private const GENRE_BY_PLACE_TYPE = [
        'ramen_restaurant' => 'ラーメン',
        'sushi_restaurant' => '寿司',
        'chinese_restaurant' => '中華',
        'convenience_store' => 'コンビニ',
        'cafe' => 'カフェ',
        'coffee_shop' => 'カフェ',
        'bakery' => 'スイーツ',
        'dessert_shop' => 'スイーツ',
        'ice_cream_shop' => 'スイーツ',
        'bar' => '居酒屋',
        'pub' => '居酒屋',
        'japanese_restaurant' => '定食',
        'restaurant' => 'レストラン',
        'food' => 'レストラン',
    ];

    /**
     * 店舗名から候補を検索し、申請フォームに自動入力できる形に整えて返す。
     * 距離は学校の座標が設定されている場合のみ算出する。
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchPlaceCandidates(string $keyword, int $limit = 5): array
    {
        $apiKey = config('services.google_maps.server_key');
        $keyword = trim($keyword);

        if (empty($apiKey) || $keyword === '') {
            return [];
        }

        // 入力途中の同じ文字列で何度も課金されないよう、キーワード単位で保持する
        $cacheKey = 'place_search_' . md5($keyword . '_' . $limit);

        if (($cached = Cache::get($cacheKey)) !== null) {
            return $cached;
        }

        $candidates = $this->requestPlaceCandidates($apiKey, $keyword, $limit);

        // 通信失敗や該当なしを保存すると、復旧後もしばらく空のままになってしまう
        if ($candidates !== []) {
            Cache::put($cacheKey, $candidates, now()->addHours(self::SEARCH_CACHE_HOURS));
        }

        return $candidates;
    }

    /**
     * Places APIを実際に呼び出す部分。キャッシュが無いときだけ実行される。
     *
     * @return array<int, array<string, mixed>>
     */
    private function requestPlaceCandidates(string $apiKey, string $keyword, int $limit): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => implode(',', [
                        'places.displayName',
                        'places.formattedAddress',
                        'places.location',
                        'places.types',
                        'places.websiteUri',
                        'places.regularOpeningHours.weekdayDescriptions',
                    ]),
                ])
                ->post(
                    'https://places.googleapis.com/v1/places:searchText',
                    [
                        'textQuery' => $keyword,
                        'languageCode' => 'ja',
                        'regionCode' => 'JP',
                        'maxResultCount' => $limit,
                    ]
                );

            if ($response->failed()) {
                Log::warning('Google Places API search failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return collect($response->json('places') ?? [])
                ->map(fn ($place) => $this->formatCandidate($place))
                ->all();
        } catch (\Throwable $e) {
            Log::error('Google Places API search error.', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Places APIの生データを、申請フォームの各入力欄に対応する形へ変換する。
     *
     * @param  array<string, mixed>  $place
     * @return array<string, mixed>
     */
    private function formatCandidate(array $place): array
    {
        $latitude = $place['location']['latitude'] ?? null;
        $longitude = $place['location']['longitude'] ?? null;

        return [
            'name' => $place['displayName']['text'] ?? '',
            'address' => $place['formattedAddress'] ?? '',
            'genre' => $this->detectGenre($place['types'] ?? []),
            'business_hours' => $this->formatBusinessHours(
                $place['regularOpeningHours']['weekdayDescriptions'] ?? []
            ),
            'official_url' => $place['websiteUri'] ?? '',
            // 学校の座標が未設定なら null を返し、画面側では距離を触らない
            'distance' => $this->distanceFromSchool($latitude, $longitude),
        ];
    }

    /**
     * Googleの店舗種別から、フォームのジャンル選択肢を推定する。
     * 該当がなければ空文字を返し、利用者に選ばせる。
     *
     * @param  array<int, string>  $types
     */
    private function detectGenre(array $types): string
    {
        foreach (self::GENRE_BY_PLACE_TYPE as $placeType => $genre) {
            if (in_array($placeType, $types, true)) {
                return $genre;
            }
        }

        return '';
    }

    /**
     * 曜日ごとの営業時間を、1行の入力欄に収まる文字列へまとめる。
     * 全曜日が同じ時間帯なら時間帯だけを、異なる場合は曜日つきで並べる。
     *
     * @param  array<int, string>  $weekdayDescriptions
     */
    private function formatBusinessHours(array $weekdayDescriptions): string
    {
        if ($weekdayDescriptions === []) {
            return '';
        }

        // 「月曜日: 11時00分～22時00分」から時間帯の部分だけを取り出す
        $hours = array_map(
            fn ($description) => trim(explode(':', $description, 2)[1] ?? $description),
            $weekdayDescriptions
        );

        $uniqueHours = array_unique($hours);

        $formatted = count($uniqueHours) === 1
            ? reset($uniqueHours)
            : implode(' / ', $weekdayDescriptions);

        // business_hours カラムは255文字までのため、超える場合は切り詰める
        return mb_strimwidth($formatted, 0, 255, '…');
    }

    /**
     * 学校から対象地点までの直線距離をメートルで返す（10m単位に丸める）。
     * 学校の座標が未設定、または地点の座標が取れない場合は null。
     */
    public function distanceFromSchool(?float $latitude, ?float $longitude): ?int
    {
        $schoolLatitude = config('services.school.latitude');
        $schoolLongitude = config('services.school.longitude');

        if (
            $latitude === null || $longitude === null
            || $schoolLatitude === null || $schoolLongitude === null
            || $schoolLatitude === '' || $schoolLongitude === ''
        ) {
            return null;
        }

        // ヒュベニの簡易式ではなく、実装が短く誤差も十分小さいハバーサイン公式を使う
        $earthRadius = 6371000;

        $latitudeDifference = deg2rad($latitude - (float) $schoolLatitude);
        $longitudeDifference = deg2rad($longitude - (float) $schoolLongitude);

        $a = sin($latitudeDifference / 2) ** 2
            + cos(deg2rad((float) $schoolLatitude))
            * cos(deg2rad($latitude))
            * sin($longitudeDifference / 2) ** 2;

        $distance = $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) (round($distance / 10) * 10);
    }

    public function findPlace(string $shopName, string $address): ?array
    {
        $apiKey = config('services.google_maps.server_key');

        if (empty($apiKey)) {
            return null;
        }

        $query = trim($shopName . ' ' . $address);

        // 詳細ページを開くたびに課金されないよう、店舗ごとの評価を保持する。
        // 評価は頻繁には変わらないため、多少古くても実用上の支障はない。
        $cacheKey = 'google_place_' . md5($query);

        if (($cached = Cache::get($cacheKey)) !== null) {
            return $cached;
        }

        $place = $this->requestPlace($apiKey, $query);

        // 通信失敗や該当なしを保存すると、復旧後もしばらく評価が出なくなってしまう
        if ($place !== null) {
            Cache::put($cacheKey, $place, now()->addDays(self::PLACE_CACHE_DAYS));
        }

        return $place;
    }

    /**
     * Places APIを実際に呼び出す部分。キャッシュが無いときだけ実行される。
     *
     * @return array<string, mixed>|null
     */
    private function requestPlace(string $apiKey, string $query): ?array
    {
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
        } catch (\Throwable $e) {
            Log::error('Google Places API error.', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}