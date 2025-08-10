<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class CartDoc
{
    /**
     * @OA\Get(
     *     path="/cart",
     *     summary="List Carts",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Carts retrieved successfully.",
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
     *                     @OA\Property(property="user_email", type="string", format="email", example="john@example.com"),
     *                     @OA\Property(property="user_name", type="string", example="John Doe"),
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="product_id", type="integer", example=1),
     *                             @OA\Property(property="product_name", type="string", example="Smartphone XYZ"),
     *                             @OA\Property(property="quantity", type="integer", example=2),
     *                             @OA\Property(property="unit_price", type="number", format="float", example=299.99),
     *                             @OA\Property(property="unit_price_discounted", type="number", format="float", example=254.99),
     *                             @OA\Property(property="discount_value", type="number", format="float", example=15.00),
     *                             @OA\Property(property="total_price", type="number", format="float", example=599.98),
     *                             @OA\Property(property="total_price_discounted", type="number", format="float", example=509.98)
     *                         )
     *                     ),
     *                     @OA\Property(property="cart_total", type="number", format="float", example=599.98),
     *                     @OA\Property(property="cart_total_discounted", type="number", format="float", example=509.98)
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
     * @OA\Get(
     *     path="/cart/{id}",
     *     summary="Show Cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="user_email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="user_name", type="string", example="John Doe"),
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="product_id", type="integer", example=1),
     *                         @OA\Property(property="product_name", type="string", example="Smartphone XYZ"),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(property="unit_price", type="number", format="float", example=299.99),
     *                         @OA\Property(property="unit_price_discounted", type="number", format="float", example=254.99),
     *                         @OA\Property(property="discount_value", type="number", format="float", example=15.00),
     *                         @OA\Property(property="total_price", type="number", format="float", example=599.98),
     *                         @OA\Property(property="total_price_discounted", type="number", format="float", example=509.98)
     *                     )
     *                 ),
     *                 @OA\Property(property="cart_total", type="number", format="float", example=599.98),
     *                 @OA\Property(property="cart_total_discounted", type="number", format="float", example=509.98)
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
     * @OA\Delete(
     *     path="/cart/{id}/clear",
     *     summary="Clear Cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Cart cleared successfully")
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
     *                 @OA\Property(property="message", type="string", example="You are not authorized to clear this resource.")
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
    public function clear() {}
}
