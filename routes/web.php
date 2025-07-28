<?php

use Illuminate\Support\Facades\Route;
use App\Http\Responses\ApiResponse;

Route::get('/', function () {
    return ApiResponse::success([
        'message' => 'Welcome to the Marketcore API',
        'api' => $_ENV['APP_URL'] . ':' . $_ENV['WEB_SERVER_PORT'] . '/api',
        'docs' => $_ENV['APP_URL'] . ':' . $_ENV['WEB_SERVER_PORT'] . '/api/documentation',
        'github' => "https://github.com/vinifen/marketcore-api",
    ], 200);
});
