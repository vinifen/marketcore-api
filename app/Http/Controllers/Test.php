<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class Test extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        return $this->success(null, '', 200);
    }
}
