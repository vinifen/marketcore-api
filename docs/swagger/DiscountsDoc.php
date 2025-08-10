<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class DiscountsDoc
{
    /**
     * @OA\Get(
     *     path="/discounts",
     *     summary="List Discounts",
     *     tags={"Discount"},
     *     @OA\Response(
     *         response=200,
     *         description="Discounts retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="description", type="string", example="Black Friday Discount"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2025-11-20"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
     *                     @OA\Property(property="discount_percentage", type="number", format="float", example=25.50),
     *                     @OA\Property(property="product", type="string", example="Smartphone XYZ"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z")
     *                 )
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
     *                 @OA\Property(property="message", type="string", example="You are not authorized to view this resource.")
     *             )
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/discounts",
     *     summary="Create Discount",
     *     tags={"Discount"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"product_id", "start_date", "end_date", "discount_percentage"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Black Friday Discount"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-11-20"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
     *             @OA\Property(property="discount_percentage", type="number", format="float", example=25.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Discount created successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Black Friday Discount"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-11-20"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=25.50),
     *                 @OA\Property(property="product", type="string", example="Smartphone XYZ"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z")
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
     *                 @OA\Property(property="message", type="string", example="Discount creation request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="product_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The product id field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="start_date",
     *                     type="array",
     *                     @OA\Items(type="string", example="The start date field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="end_date",
     *                     type="array",
     *                     @OA\Items(type="string", example="The end date must be a date after or equal to start date.")
     *                 ),
     *                 @OA\Property(
     *                     property="discount_percentage",
     *                     type="array",
     *                     @OA\Items(type="string", example="The discount percentage must be between 0.01 and 100.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/discounts/{id}",
     *     summary="Show Discount",
     *     tags={"Discount"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Black Friday Discount"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-11-20"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=25.50),
     *                 @OA\Property(property="product", type="string", example="Smartphone XYZ"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z")
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="You are not authorized to show this resource.")
     *             )
     *         )
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/discounts/{id}",
     *     summary="Update Discount",
     *     tags={"Discount"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Updated Black Friday Discount"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-11-21"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-01"),
     *             @OA\Property(property="discount_percentage", type="number", format="float", example=30.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Updated Black Friday Discount"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-11-21"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-12-01"),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=30.00),
     *                 @OA\Property(property="product", type="string", example="Smartphone XYZ"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z")
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
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Discount update request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="product_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected product id is invalid.")
     *                 ),
     *                 @OA\Property(
     *                     property="end_date",
     *                     type="array",
     *                     @OA\Items(type="string", example="The end date must be a date after or equal to start date.")
     *                 ),
     *                 @OA\Property(
     *                     property="discount_percentage",
     *                     type="array",
     *                     @OA\Items(type="string", example="The discount percentage must be between 0.01 and 100.")
     *                 )
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
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/discounts/{id}",
     *     summary="Delete Discount",
     *     tags={"Discount"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount deleted successfully.",
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
     *     path="/discounts/{id}/restore",
     *     summary="Restore Discount",
     *     tags={"Discount"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount restored successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Black Friday Discount"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-11-20"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
     *                 @OA\Property(property="discount_percentage", type="number", format="float", example=25.50),
     *                 @OA\Property(property="product", type="string", example="Smartphone XYZ"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z")
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
     *     path="/discounts/{id}/force-delete",
     *     summary="Force Delete Discount",
     *     tags={"Discount"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount permanently deleted successfully.",
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
