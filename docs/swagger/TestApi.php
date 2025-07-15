<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class TestApi
{
    /**
     * @OA\Get(
     *     path="/api/test",
     *     summary="Returns a test message",
     *     tags={"Test"},
     *     @OA\Response(
     *         response=200,
     *         description="Success message",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="API working correctly!")
     *         )
     *     )
     * )
     */
    public function test()
    {
    }
}
