<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="MarketCore API Documentation",
 *     description="MarketCore API is an api made to serve a digital market.",
 * ),
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class InfoApi
{
}
