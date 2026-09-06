<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_add_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('product.add'), [
            'name' => 'Test proizvod',
            'price' => 150,
            'stock' => 10,
            'description' => 'Ovo je test proizvod sa dovoljnim opisom.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'name' => 'Test proizvod',
            'price' => 150,
            'stock' => 10,
            'description' => 'Ovo je test proizvod sa dovoljnim opisom.',
        ]);
    }

    public function test_guest_cannot_add_product(): void
    {
        $response = $this->post(route('product.add'), [
            'name' => 'Neautorizovan proizvod',
            'price' => 100,
            'stock' => 5,
            'description' => 'Opis proizvoda koji je dovoljno dugacak.',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('products', [
            'name' => 'Neautorizovan proizvod',
        ]);
    }
}
