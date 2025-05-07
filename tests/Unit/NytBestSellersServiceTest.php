<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\NytBestSellersService;

class NytBestSellersServiceTest extends TestCase
{
    public function test_fetch_returns_results()
    {
        Http::fake([
            '*' => Http::response([
                'results' => ['foo','bar']
            ], 200)
        ]);

        Cache::flush();
        $svc = $this->app->make(NytBestSellersService::class);
        $first = $svc->fetch(['author'=>'test']);
        $second = $svc->fetch(['author'=>'test']);

        $this->assertEquals(['foo','bar'], $first);
        $this->assertEquals(['foo','bar'], $second);
    }

    public function test_params_passed_to_service()
    {
        $this->mock(NytBestSellersService::class, function ($mock) {
            $mock->shouldReceive('fetch')
                ->once()
                ->with(['author'=>'King','title'=>'IT','isbn' => [2222222222,2323232323232], 'offset' => 20])
                ->andReturn(['X']);
        });

        $this->getJson('/api/v1/best-sellers?author=King&title=IT&offset=20&isbn=2222222222;2323232323232')
            ->assertStatus(200)
            ->assertJson(['success'=>true,'data'=>['X']]);
    }

    public function test_fetch_throws_on_error()
    {
        Http::fake(['*' => Http::response([], 500)]);
        $this->expectException(Exception::class);

        $svc = $this->app->make(NytBestSellersService::class);
        $svc->fetch([]);
    }
}