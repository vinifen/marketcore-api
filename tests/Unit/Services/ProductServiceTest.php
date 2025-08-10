<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use App\Exceptions\ApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var ProductService */
    private $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
        Storage::fake('public');
    }

    public function test_product_service_can_be_instantiated(): void
    {
        $service = new ProductService();
        $this->assertInstanceOf(ProductService::class, $service);
    }

    public function test_ensure_product_has_stock_passes_when_sufficient_stock(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

        $this->productService->ensureProductHasStock($product, 5);

        $this->addToAssertionCount(1);
    }

    public function test_ensure_product_has_stock_throws_exception_when_insufficient_stock(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 3]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Insufficient stock.');
        $this->expectExceptionCode(422);

        $this->productService->ensureProductHasStock($product, 5);
    }

    public function test_decrease_stock_reduces_product_stock(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

        $this->productService->decreaseStock($product, 3);

        $product->refresh();
        $this->assertEquals(7, $product->stock);
    }

    public function test_decrease_stock_throws_exception_when_insufficient_stock(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 2, 'name' => 'Test Product']);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Cannot decrease stock by 5. Only 2 units available for product 'Test Product'.");
        $this->expectExceptionCode(422);

        $this->productService->decreaseStock($product, 5);
    }

    public function test_increase_stock_adds_to_product_stock(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 5]);

        $this->productService->increaseStock($product, 3);

        $product->refresh();
        $this->assertEquals(8, $product->stock);
    }

    public function test_create_product_without_image(): void
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 19.99,
            'stock' => 10,
            'category_id' => $category->id
        ];

        $product = $this->productService->createProduct($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(19.99, $product->price);
        $this->assertNull($product->image_url);
    }

    public function test_update_product_remove_image(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'image_url' => '/api/storage/products/test.jpg']);

        $data = ['name' => 'Updated Product'];

        $updatedProduct = $this->productService->updateProduct($product, $data, null, true);

        $this->assertEquals('Updated Product', $updatedProduct->name);
        $this->assertNull($updatedProduct->image_url);
    }

    public function test_delete_image_removes_file_from_storage(): void
    {
        $imagePath = 'products/test-image.jpg';
        Storage::disk('public')->put($imagePath, 'fake-image-content');

        $imageUrl = '/api/storage/products/test-image.jpg';

        $this->productService->deleteImage($imageUrl);

        $this->assertFalse(Storage::disk('public')->exists($imagePath));
    }

    public function test_delete_image_handles_null_url(): void
    {
        $this->productService->deleteImage(null);

        $this->addToAssertionCount(1);
    }
}
