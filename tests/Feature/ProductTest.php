<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase; // resetuje bazu između testova

    /** @test */
    public function authenticated_user_can_add_product()
    {
        // 1. Napravi test korisnika
        $user = User::factory()->create();

        // 2. Loguj se kao korisnik
        $this->actingAs($user);

        // 3. Pošalji POST request
        $response = $this->post(route('products.add'), [
            'name' => 'Test proizvod',
            'price' => 150,
            'quantity' => 10,
            'description' => 'Ovo je test proizvod sa dovoljnim opisom.',
        ]);

        // 4. Proveri da je redirect
        $response->assertRedirect();

        // 5. Proveri da li je proizvod dodat u bazu
        $this->assertDatabaseHas('products', [
            'name' => 'Test proizvod',
            'price' => 150,
            'quantity' => 10,
            'description' => 'Ovo je test proizvod sa dovoljnim opisom.',
        ]);
    }

    /** @test */
    public function guest_cannot_add_product()
    {
        // Pošalji POST request bez logovanja
        $response = $this->post(route('products.add'), [
            'name' => 'Neautorizovan proizvod',
            'price' => 100,
            'quantity' => 5,
            'description' => 'Opis.',
        ]);

        // Treba da bude redirect na login (Laravel default)
        $response->assertRedirect(route('login'));

        // Proveri da proizvod NIJE dodat
        $this->assertDatabaseMissing('products', [
            'name' => 'Neautorizovan proizvod',
        ]);
    }
}
