<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\Factory as HttpClient;

class NytBestSellersService
{
    public function __construct(protected HttpClient $http) {}

    /**
     * @param  array  $params
     * @return array
     * @throws Exception
     */
    public function fetch(array $params): array
    {
        $query = array_filter([
            'api-key' => Config::get('services.nyt.key'),
            'author'  => $params['author']  ?? null,
            'title'   => $params['title']   ?? null,
            'isbn'    => isset($params['isbn']) ? implode(';', $params['isbn']) : null,
            'offset'  => $params['offset']  ?? null,
        ]);

        $cacheKey = 'nyt:best-sellers:'.md5(serialize($query));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query) {
            $response = $this->http
                ->baseUrl(Config::get('services.nyt.base_uri'))
                ->get('history.json', $query);
            if ($response->failed()) {
                throw new Exception('NYT API error: '.$response->status());
            }

            $json = $response->json();
            return $json['results'] ?? [];
        });
    }
}