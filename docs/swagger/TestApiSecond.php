<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class TestApiSecond
{
    /**
     * @OA\Post(
     *     path="/api/test-second",
     *     summary="Outro endpoint para testes",
     *     tags={"TestSecond"},
     *     @OA\Response(
     *         response=200,
     *         description="POST success messageasfdsadfasfdasdfasdf",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="POST working!")
     *         )
     *     )
     * )
     */
    public function postTest()
    {
    }
}
