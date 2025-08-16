<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class OrderDoc
{
    /**
     * @OA\Get(
     *     path="/order",
     *     summary="List Orders",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="address_id", type="integer", example=1),
     *                     @OA\Property(property="coupon_id", type="integer", example=1),
     *                     @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                     @OA\Property(property="status", type="string", example="PENDING"),
     *                     @OA\Property(property="items_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z")
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
     *         description="Forbidden.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *             )
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/order",
     *     summary="Create Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "address_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="address_id", type="integer", example=1),
     *             @OA\Property(property="coupon_code", type="string", example="SAVE10"),
     *             @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="address_id", type="integer", example=1),
     *                 @OA\Property(property="coupon_id", type="integer", example=1),
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="items_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z")
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
     *         description="Forbidden.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="user_id", type="array", @OA\Items(type="string", example="The user id field is required.")),
     *                 @OA\Property(property="address_id", type="array", @OA\Items(type="string", example="The address id field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/order/{id}",
     *     summary="Show Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="address_id", type="integer", example=1),
     *                 @OA\Property(property="coupon_id", type="integer", example=1),
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="items_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z")
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
     *         description="Forbidden.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Order not found.")
     *             )
     *         )
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/order/{id}",
     *     summary="Update Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="address_id", type="integer", example=1),
     *             @OA\Property(property="coupon_id", type="integer", example=1),
     *             @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00Z"),
     *             @OA\Property(property="total_amount", type="number", format="float", example=299.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="address_id", type="integer", example=1),
     *                 @OA\Property(property="coupon_id", type="integer", example=1),
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="items_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z")
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
     *         description="Forbidden.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="Order not found.")
     *             )
     *         )
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Post(
     *     path="/order/{id}/cancel",
     *     summary="Cancel Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="address_id", type="integer", example=1),
     *                 @OA\Property(property="coupon_id", type="integer", example=1),
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="CANCELLED"),
     *                 @OA\Property(property="items_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function cancel() {}

    /**
     * @OA\Delete(
     *     path="/order/{id}",
     *     summary="Delete Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy() {}

    /**
     * @OA\Post(
     *     path="/order/{id}/restore",
     *     summary="Restore Deleted Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order restored successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="address_id", type="integer", example=1),
     *                 @OA\Property(property="coupon_id", type="integer", example=1),
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="items_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function restore() {}

    /**
     * @OA\Delete(
     *     path="/order/{id}/force",
     *     summary="Force Delete Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order permanently deleted.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function forceDelete() {}
}