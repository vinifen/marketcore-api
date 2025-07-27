<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

class UserDoc
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Lista todos os usuários",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Usuários encontrados com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Vinicius Ferreira"),
     *                     @OA\Property(property="email", type="string", example="vinicius@example.com"),
     *                     @OA\Property(property="role", type="string", example="ADMIN")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Sem permissão para listar usuários"
     *     )
     * )
     */
    public function listUsers()
    {
    }
}
