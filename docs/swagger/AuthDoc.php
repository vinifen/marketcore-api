<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class AuthDoc
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="User Registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="example name"),
     *             @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="name"),
     *                     @OA\Property(property="email", type="string", format="email", example="name@email.com"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|tokenexample...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error example.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="User creation request failed due to invalid data."),
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field must be a valid email address.")),
     *             )
     *         )
     *     )
     * )
     */
    public function register()
    {
    }


    /**
     * @OA\Post(
     *     path="/login",
     *     summary="User Login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="name"),
     *                     @OA\Property(property="email", type="string", format="email", example="name@email.com"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|tokenexample...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized error example.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid credentials provided.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error example.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid credentials provided."),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function login()
    {
    }


    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="User Logout",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *          @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Logout successful.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized error example.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             )
     *         )
     *     )
     * )
     */
    public function logout()
    {
    }
}
