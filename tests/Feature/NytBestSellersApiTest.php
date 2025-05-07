<?php

namespace Tests\Feature;

use Exception;
use Tests\TestCase;
use App\Services\NytBestSellersService;

class NytBestSellersApiTest extends TestCase
{
    public function test_index_returns_data_on_valid_request()
    {
        $this->mock(NytBestSellersService::class, function ($mock) {
            $mock->shouldReceive('fetch')
                ->once()
                ->with(['author'=>'King'])
                ->andReturn(['A','B']);
        });

        $this->getJson('/api/v1/best-sellers?author=King')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => ['A','B'],
            ]);
    }

    public function test_service_exception_results_in_500()
    {
        $this->mock(NytBestSellersService::class, function ($mock) {
            $mock->shouldReceive('fetch')
                ->once()
                ->andThrow(new Exception('API error', 500));
        });

        $this->getJson('/api/v1/best-sellers')
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Ошибка при обращении к NYT API. API error',
            ]);
    }

    public function test_validation_error_on_bad_offset()
    {
        $this->getJson('/api/v1/best-sellers?offset=-5')
            ->assertStatus(422);
    }
}
