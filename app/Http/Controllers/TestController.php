<?php

namespace App\Http\Controllers;

use App\Http\Requests\Test\StoreTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Models\Test;

class TestController extends Controller
{
    public function index()
    {

    }

    public function store(StoreTestRequest $request)
    {
        $test = Test::create($request->validated());
        return response()->json($test, 201);
    }

    public function show(Test $test)
    {
        dd($test);
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
    }

    public function destroy(Test $test)
    {
    }
}
