<?php

namespace Docs\swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="MarketCore API Documentation",
 *     description="API documentation for MarketCore",
 * ),
 *@OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class InfoApi
{
}
