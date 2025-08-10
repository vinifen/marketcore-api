<?php

namespace Tests\Feature\Catalog\Product;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 10,
            'price' => 99.99,
        ];

        $response = $this->actingAs($staff)->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'Test Product',
                'category_id' => $category->id,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'category_id' => $category->id,
        ]);
    }

    public function test_non_staff_cannot_create_product(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 10,
            'price' => 99.99,
        ];

        $response = $this->actingAs($client)->postJson('/api/products', $payload);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_create_product(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 10,
            'price' => 99.99,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(401)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_staff_can_update_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Old Name']);

        $payload = [
            'name' => 'Updated Name',
            'stock' => 20,
            'price' => 199.99,
        ];

        $response = $this->actingAs($staff)->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'Updated Name',
                'stock' => 20,
                'price' => 199.99,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'stock' => 20,
            'price' => 199.99,
        ]);
    }

    public function test_non_staff_cannot_update_product(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Old Name']);

        $payload = [
            'name' => 'Updated Name',
        ];

        $response = $this->actingAs($client)->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_staff_can_soft_delete_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_staff_can_restore_soft_deleted_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response = $this->actingAs($staff)->postJson("/api/products/{$product->id}/restore");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'id' => $product->id,
                'name' => $product->name,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_force_delete_soft_deleted_product(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->deleteJson("/api/products/{$product->id}");

        $response = $this->actingAs($admin)->deleteJson("/api/products/{$product->id}/force-delete");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_staff_cannot_force_delete_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}/force-delete");

        $response->assertStatus(403);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_restore_should_fail_if_product_not_soft_deleted(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($staff)->postJson("/api/products/{$product->id}/restore");

        $response->assertStatus(404)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_soft_delete_product_also_soft_deletes_discounts(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount1 = Discount::factory()->create(['product_id' => $product->id]);
        $discount2 = Discount::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Discount::class, $discount1);
        $this->assertInstanceOf(Discount::class, $discount2);

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(200);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertSoftDeleted('discounts', ['id' => $discount1->id]);
        $this->assertSoftDeleted('discounts', ['id' => $discount2->id]);
    }

    public function test_restore_product_also_restores_discounts(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Discount::class, $discount);

        $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");
        $this->actingAs($staff)->postJson("/api/products/{$product->id}/restore");

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('discounts', ['id' => $discount->id, 'deleted_at' => null]);
    }

    public function test_staff_can_update_product_with_new_image_and_store_file(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'image_url' => null,
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->createWithContent('produto.png', $this->tinyPng());

        $response = $this->actingAs($staff)->post("/api/products/{$product->id}/update", [
            'image' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $product->refresh();
        $this->assertNotNull($product->image_url);

        $pathFromUrl = parse_url($product->image_url, PHP_URL_PATH);
        $this->assertIsString($pathFromUrl);
        $storagePath = ltrim(str_replace('/api/storage/', '', $pathFromUrl), '/');
        $this->assertTrue(Storage::disk('public')->exists($storagePath));
    }

    public function test_staff_can_remove_existing_product_image(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();

        Storage::fake('public');

        $existingPath = 'products/existing.jpg';
        Storage::disk('public')->put($existingPath, 'fake-image-content');
        $existingUrl = Storage::url($existingPath);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'image_url' => $existingUrl,
        ]);

        $response = $this->actingAs($staff)->post("/api/products/{$product->id}/update", [
            'remove_image' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $product->refresh();
        $this->assertNull($product->image_url);
        $this->assertFalse(Storage::disk('public')->exists($existingPath));
    }

    public function test_update_with_both_image_and_remove_image_prefers_new_image(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();

        Storage::fake('public');
        $existingPath = 'products/old.jpg';
        Storage::disk('public')->put($existingPath, 'old-image');
        $existingUrl = Storage::url($existingPath);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'image_url' => $existingUrl,
        ]);

        $newFile = UploadedFile::fake()->createWithContent('novo.png', $this->tinyPng());
        $response = $this->actingAs($staff)->post("/api/products/{$product->id}/update", [
            'image' => $newFile,
            'remove_image' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $product->refresh();
        $this->assertNotNull($product->image_url);

        $this->assertFalse(Storage::disk('public')->exists($existingPath));

        $pathFromUrl = parse_url($product->image_url, PHP_URL_PATH);
        $this->assertIsString($pathFromUrl);
        $newStoragePath = ltrim(str_replace('/api/storage/', '', $pathFromUrl), '/');
        $this->assertTrue(Storage::disk('public')->exists($newStoragePath));
    }

    public function test_update_rejects_non_image_file(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('arquivo.txt', 1, 'text/plain');

        $response = $this->actingAs($staff)->post("/api/products/{$product->id}/update", [
            'image' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_update_rejects_image_over_max_size(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Storage::fake('public');
        $tooLarge = $this->pngOfSizeKB(8193); // limit is 8192 KB

        $response = $this->actingAs($staff)->post("/api/products/{$product->id}/update", [
            'image' => $tooLarge,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_update_accepts_image_at_max_size(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Storage::fake('public');
        $maxFile = $this->pngOfSizeKB(8192);

        $response = $this->actingAs($staff)->post("/api/products/{$product->id}/update", [
            'image' => $maxFile,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $product->refresh();
        $this->assertNotNull($product->image_url);
    }

    public function test_update_accepts_png_and_jpg_but_rejects_gif_and_pdf(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Storage::fake('public');

        // PNG (allowed)
        $png = UploadedFile::fake()->createWithContent('ok.png', $this->tinyPng());
        $resPng = $this->actingAs($staff)->post("/api/products/{$product->id}/update", ['image' => $png]);
        $resPng->assertStatus(200)->assertJsonFragment(['success' => true]);

        // JPEG (allowed) - use valid image content with .jpg filename
        $jpeg = UploadedFile::fake()->createWithContent('ok.jpg', $this->tinyPng());
        $resJpeg = $this->actingAs($staff)->post("/api/products/{$product->id}/update", ['image' => $jpeg]);
        $resJpeg->assertStatus(200)->assertJsonFragment(['success' => true]);

        // GIF (not allowed)
        $gifHeader = base64_decode('R0lGODdhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==', true);
        $this->assertIsString($gifHeader);
        $gif = UploadedFile::fake()->createWithContent('no.gif', $gifHeader);
        $resGif = $this->actingAs($staff)->post("/api/products/{$product->id}/update", ['image' => $gif]);
        $resGif->assertStatus(422)->assertJsonFragment(['success' => false]);

        // PDF (not allowed)
        $pdf = UploadedFile::fake()->create('doc.pdf', 1, 'application/pdf');
        $resPdf = $this->actingAs($staff)->post("/api/products/{$product->id}/update", ['image' => $pdf]);
        $resPdf->assertStatus(422)->assertJsonFragment(['success' => false]);
    }

    private function pngOfSizeKB(int $kb): UploadedFile
    {
        $base = $this->tinyPng();
        $targetBytes = $kb * 1024;
        if (strlen($base) < $targetBytes) {
            $base .= str_repeat('A', $targetBytes - strlen($base));
        } else {
            $base = substr($base, 0, $targetBytes);
        }
        return UploadedFile::fake()->createWithContent('file.png', $base);
    }
}
