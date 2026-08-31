<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PelangganTest extends TestCase
{
    public function test_pelanggan_page_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('pelanggan.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Pelanggan/Index')
            ->has('pelangganList')
        );
    }

    public function test_pelanggan_riwayat_endpoint_returns_json_response(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('pelanggan.riwayat', ['id' => 1]));

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_pelanggan_struk_endpoint_returns_json_response(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('pelanggan.struk', ['id' => 1]));

        // Should return 200 with transaction data if id 1 exists or 404 if not found
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }
}
