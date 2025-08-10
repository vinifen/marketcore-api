<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class ApiRootDoc
{
    /**
     * @OA\Get(
     *     path="/api/",
     *     summary="API Root - Welcome and Information",
     *     description="Returns welcome message and basic API information including available endpoints",
     *     operationId="getApiRoot",
     *     tags={"API Root"},
     *     @OA\Response(
     *         response=200,
     *         description="API information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Welcome to the Marketcore API"),
     *                 @OA\Property(property="docs", type="string", example="http://localhost:8010/api/documentation"),
     *                 @OA\Property(property="github", type="string", example="https://github.com/vinifen/marketcore-api"),
     *                 @OA\Property(property="version", type="string", example="1.0.0"),
     *             )
     *         )
     *     )
     * )
     */
    public function getApiRoot()
    {
    }
}
