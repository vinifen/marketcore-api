<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class WebRootDoc
{
    /**
     * @OA\Get(
     *     path="/",
     *     summary="Application Root - Welcome Page",
     *     description="Returns welcome message and basic application information. This is the main entry point of the application.",
     *     operationId="getWebRoot",
     *     tags={"Web Root"},
     *     @OA\Response(
     *         response=200,
     *         description="Welcome page content",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Welcome to the Marketcore API"),
     *                 @OA\Property(property="api", type="string", example="http://localhost:8010/api"),
     *                 @OA\Property(property="docs", type="string", example="http://localhost:8010/api/documentation"),
     *                 @OA\Property(property="github", type="string", example="https://github.com/vinifen/marketcore-api"),
     *                 @OA\Property(property="version", type="string", example="1.0.0")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function getWebRoot()
    {
    }
}
