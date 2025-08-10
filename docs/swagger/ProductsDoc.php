<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class ProductsDoc
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="List Products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="name", type="string", example="Product Name"),
     *                     @OA\Property(property="stock", type="integer", example=100),
     *                     @OA\Property(property="price", type="number", format="float", example=29.99),
     *                     @OA\Property(property="category", type="string", nullable=true, example="Category Name"),
     *                     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/api/image.jpg")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create Product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "stock", "price"},
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="stock", type="integer", minimum=0, example=100),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0, example=29.99),
     *                 @OA\Property(property="image", type="string", format="binary", description="Product image (jpeg, png, jpg, svg, max 8MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="stock", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=29.99),
     *                 @OA\Property(property="category", type="string", nullable=true, example="Category Name"),
     *                 @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/api/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to create this resource.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Product creation request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="array",
     *                     @OA\Items(type="string", example="The price field must be a number.")
     *                 ),
     *                 @OA\Property(
     *                     property="stock",
     *                     type="array",
     *                     @OA\Items(type="string", example="The stock field must be an integer.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Show Product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="stock", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=29.99),
     *                 @OA\Property(property="category", type="string", nullable=true, example="Category Name"),
     *                 @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/api/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Not found.")
     *             )
     *         )
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update Product (JSON Only)",
     *     description="Update product using JSON data only. Validates with UpdateProductJsonRequest. For file uploads, use POST /products/{id}/update",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="stock", type="integer", minimum=0, example=150),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=39.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="stock", type="integer", example=150),
     *                 @OA\Property(property="price", type="number", format="float", example=39.99),
     *                 @OA\Property(property="category", type="string", nullable=true, example="Category Name"),
     *                 @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/api/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Validates JSON only (UpdateProductJsonRequest).",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="This endpoint only accepts JSON data. For file uploads, use POST /products/{id}/update", description="Possible messages: Non-JSON content type or image fields detected")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to update this resource.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Product not found.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Post(
     *     path="/products/{id}/update",
     *     summary="Update Product with Form Data",
     *     description="Update product using form-data (supports file upload). Validates with UpdateProductFormRequest. For JSON-only updates, use PUT /products/{id}",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="stock", type="integer", minimum=0, example=150),
     *                 @OA\Property(property="price", type="number", format="float", minimum=0, example=39.99),
     *                 @OA\Property(property="image", type="string", format="binary", description="Product image (jpeg, png, jpg, svg, max 8MB)"),
     *                 @OA\Property(property="remove_image", type="boolean", example=false, description="Set to true to remove current image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="stock", type="integer", example=150),
     *                 @OA\Property(property="price", type="number", format="float", example=39.99),
     *                 @OA\Property(property="category", type="string", nullable=true, example="Category Name"),
     *                 @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/api/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to update this resource.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Product not found.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Validates form-data only (UpdateProductFormRequest).",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="This endpoint only accepts multipart/form-data. For JSON updates, use PUT /products/{id}")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function updateWithFormData() {}

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete Product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to delete this resource.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Not found.")
     *             )
     *         )
     *     )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *     path="/products/{id}/restore",
     *     summary="Restore Product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product restored successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="stock", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=29.99),
     *                 @OA\Property(property="category", type="string", nullable=true, example="Category Name"),
     *                 @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/api/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to restore this resource.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trashed model not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Trashed model not found.")
     *             )
     *         )
     *     )
     * )
     */
    public function restore() {}

    /**
     * @OA\Delete(
     *     path="/products/{id}/force-delete",
     *     summary="Force Delete Product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product permanently deleted successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to force delete this resource.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Not found.")
     *             )
     *         )
     *     )
     * )
     */
    public function forceDelete() {}
}