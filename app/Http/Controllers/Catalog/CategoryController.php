<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\Category\StoreCategoryRequest;
use App\Http\Requests\Catalog\Category\UpdateCategoryRequest;
use App\Http\Resources\Catalog\CategoryResource;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Category::class);
        $categories = Category::all();
        return ApiResponse::success(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);
        $category = Category::create($request->validated());
        return ApiResponse::success(new CategoryResource($category), 201);
    }

    public function show(int $id): JsonResponse
    {
        /** @var Category $category */
        $category = $this->findModelOrFail(Category::class, $id);
        $this->authorize('view', $category);
        return ApiResponse::success(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        /** @var Category $category */
        $category = $this->findModelOrFail(Category::class, $id);
        $this->authorize('update', $category);
        $category->update($request->validated());
        return ApiResponse::success(new CategoryResource($category));
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Category $category */
        $category = $this->findModelOrFail(Category::class, $id);
        $this->authorize('forceDelete', $category);
        $category->delete();
        return ApiResponse::success(null, 200);
    }
}
