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
     *                     @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                     @OA\Property(property="status", type="string", example="PENDING"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *     path="/order",
     *     summary="Create Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"user_id", "address_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="address_id", type="integer", example=1),
     *             @OA\Property(property="coupon_code", type="string", example="SUMMER2025"),
     *             @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00Z")
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
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="User not found.")
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
     *                 @OA\Property(property="message", type="string", example="Order creation request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The user id field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="address_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The address id field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="coupon_code",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected coupon code is invalid.")
     *                 )
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
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="address", type="object"),
     *                 @OA\Property(property="coupon", type="object"),
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *     path="/order/{id}",
     *     summary="Update Order",
     *     tags={"Order"},
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
     *             @OA\Property(property="address_id", type="integer", example=2)
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
     *                 @OA\Property(property="address_id", type="integer", example=2),
     *                 @OA\Property(property="coupon_id", type="integer", example=1),
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *                 @OA\Property(property="message", type="string", example="Order update request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="address_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected address id is invalid.")
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
     * @OA\Put(
     *     path="/order/{id}/status",
     *     summary="Update Order Status",
     *     tags={"Order"},
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
     *             @OA\Property(property="status", type="string", enum={"PENDING", "PROCESSING", "SHIPPED", "COMPLETED", "CANCELED"}, example="PROCESSING")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order status updated successfully.",
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
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PROCESSING"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *                 @OA\Property(property="message", type="string", example="Order update request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="status",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected status is invalid.")
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
    public function updateStatus() {}

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
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order canceled successfully.",
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
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="CANCELED"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *                 @OA\Property(property="message", type="string", example="You are not authorized to cancel this resource.")
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
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully.",
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
     *     path="/order/{id}/restore",
     *     summary="Restore Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
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
     *                 @OA\Property(property="order_date", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="address", type="object"),
     *                 @OA\Property(property="coupon", type="object"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T10:00:00.000000Z")
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
     *     path="/order/{id}/force-delete",
     *     summary="Force Delete Order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order permanently deleted successfully.",
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
