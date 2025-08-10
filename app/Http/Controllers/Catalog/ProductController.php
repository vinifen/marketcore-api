<?php

namespace App\Http\Controllers\Catalog;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductJsonRequest;
use App\Http\Requests\Product\UpdateProductFormRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::with('category')->get();
        return ApiResponse::success(ProductResource::collection($products));
    }

    public function store(StoreProductRequest $request, ProductService $productService): JsonResponse
    {
        $this->authorize('create', Product::class);

        $product = $productService->createProduct(
            $request->validated(),
            $request->file('image')
        );

        $product->load('category');
        return ApiResponse::success(new ProductResource($product), 201);
    }

    public function show(int $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->findModelOrFail(Product::class, $id);
        $this->authorize('view', $product);

        $product->load('category');
        return ApiResponse::success(new ProductResource($product));
    }

    public function update(
        UpdateProductJsonRequest $request,
        int $id
    ): JsonResponse {
        /** @var Product $product */
        $product = $this->findModelOrFail(Product::class, $id);
        $this->authorize('update', $product);

        $product->update($request->validated());
        $product->load('category');
        return ApiResponse::success(new ProductResource($product));
    }

    public function updateWithFormData(
        UpdateProductFormRequest $request,
        int $id,
        ProductService $productService
    ): JsonResponse {
        /** @var Product $product */
        $product = $this->findModelOrFail(Product::class, $id);
        $this->authorize('update', $product);

        $product = $productService->updateProduct(
            $product,
            $request->validated(),
            $request->file('image'),
            $request->boolean('remove_image')
        );

        $product->load('category');
        return ApiResponse::success(new ProductResource($product));
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->findModelOrFail(Product::class, $id);
        $this->authorize('delete', $product);

        $product->delete();
        return ApiResponse::success(null, 200);
    }

    public function restore(int $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->findModelTrashedOrFail(Product::class, $id);
        $this->authorize('restore', $product);

        $product->restore();
        $product->load('category');

        return ApiResponse::success(new ProductResource($product));
    }

    public function forceDelete(int $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->findModelOrFailWithTrashed(Product::class, $id);
        $this->authorize('forceDelete', $product);

        $product->forceDelete();

        return ApiResponse::success(null, 200);
    }
}
