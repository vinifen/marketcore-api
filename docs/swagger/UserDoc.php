<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class UserDoc
{
    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Show User",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="name"),
     *                 @OA\Property(property="email", type="string", format="email", example="name@email.com"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:00:25.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Wrong parameter error example.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="User not found.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error example.",
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
     *     path="/users/{id}",
     *     summary="Update User (Full or Partial)",
     *         description="Update user data. The request can be full or partial.\n
     *         The field 'current_password' is required if 'email' or 'new_password' is changed.\n
     *         The field 'new_password_confirmation' is required if 'new_password' is present.",
     *     tags={"User"},
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
     *             @OA\Property(property="name", type="string", example="New Name"),
     *             @OA\Property(property="email", type="string", format="email", example="new@email.com"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newPassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newPassword123"),
     *             @OA\Property(property="current_password", type="string", format="password", example="myCurrentPassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="name"),
     *                 @OA\Property(property="email", type="string", format="email", example="vini2@email.com"),
     *                 @OA\Property(property="email_verified_at", type="string", nullable=true, example=null),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-27T14:19:55.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-27T23:19:54.000000Z")
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
     *                 @OA\Property(property="message", type="string", example="User update request failed due to invalid data."),
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field must be at least 2 characters.")
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email field must be a valid email address.")
     *                 ),
     *                 @OA\Property(
     *                     property="new_password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The new password field confirmation does not match.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - incorrect current password.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="The current password is incorrect.")
     *             )
     *         )
     *     )
     * )
     */
    public function update() {}


    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete User",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Current password is required to authorize the user deletion.",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"current_password"},
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully.")
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
     *         description="Forbidden - incorrect current password.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="string", example="The current password is incorrect.")
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
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password field is required.")
     *                 )
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
     *     )
     * )
     */
    public function destroy() {}

}
