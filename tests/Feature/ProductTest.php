<?php

namespace Tests\Feature;

use App\Models\Products;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_upload_a_product_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'admin']);
        $image = UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('product.add'), [
            'name' => 'Proizvod sa slikom',
            'price' => 150,
            'stock' => 10,
            'description' => 'Ovo je test proizvod sa dovoljnim opisom.',
            'image' => $image,
        ]);

        $response->assertRedirect();

        $product = Products::where('name', 'Proizvod sa slikom')->firstOrFail();
        Storage::disk('public')->assertExists($product->image_path);
        $this->assertNotNull($product->image_url);
    }

    public function test_replacing_a_product_image_deletes_the_old_one(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create(['image_path' => 'products/old.jpg']);
        Storage::disk('public')->put('products/old.jpg', 'fake-content');

        $response = $this->actingAs($admin)->put(route('product.update', $product->id), [
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'description' => $product->description,
            'image' => UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('products.all'));
        Storage::disk('public')->assertMissing('products/old.jpg');
        Storage::disk('public')->assertExists($product->fresh()->image_path);
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

    public function test_product_import_requires_expected_csv_columns(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            "name,price\nImported product,100\n"
        );

        $response = $this->actingAs($user)->from(route('products.all'))->post(route('products.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('products.all'));
        $response->assertSessionHasErrors('csv_file');
        $this->assertDatabaseMissing('products', [
            'name' => 'Imported product',
        ]);
    }

    public function test_admin_can_restore_a_deleted_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create();
        $product->delete();

        $response = $this->actingAs($admin)->patch(route('product.restore', $product->id));

        $response->assertRedirect(route('products.trash'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_permanently_delete_a_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Products::factory()->create();
        $product->delete();

        $response = $this->actingAs($admin)->delete(route('product.force-delete', $product->id));

        $response->assertRedirect(route('products.trash'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_non_admin_cannot_view_trash(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('products.trash'));

        $response->assertForbidden();
    }

    public function test_csv_import_rejects_duplicate_sku_instead_of_crashing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Products::factory()->create(['sku' => 'SKU-001']);

        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            "sku,name,price,stock,description\nSKU-001,Duplicate sku product,100,5,A description long enough.\n"
        );

        $response = $this->actingAs($admin)->post(route('products.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('products.all'));
        $response->assertSessionHas('success', '0 products imported. 0 products updated. 1 rows skipped.');
        $this->assertDatabaseMissing('products', ['name' => 'Duplicate sku product']);
    }

    public function test_sku_is_freed_up_only_after_the_trashed_product_is_permanently_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $original = Products::factory()->create(['sku' => 'SKU-100']);
        $original->delete();

        $whileTrashed = $this->actingAs($admin)->from(route('products.all'))->post(route('product.add'), [
            'sku' => 'SKU-100',
            'name' => 'New product reusing sku',
            'price' => 100,
            'stock' => 10,
            'description' => 'A description that is long enough.',
        ]);
        $whileTrashed->assertSessionHasErrors('sku');

        $this->actingAs($admin)->delete(route('product.force-delete', $original->id));

        $afterPurge = $this->actingAs($admin)->post(route('product.add'), [
            'sku' => 'SKU-100',
            'name' => 'New product reusing sku',
            'price' => 100,
            'stock' => 10,
            'description' => 'A description that is long enough.',
        ]);
        $afterPurge->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'New product reusing sku',
            'sku' => 'SKU-100',
        ]);
    }
}
