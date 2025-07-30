<?php

namespace App\Http\Controllers;

use App\Http\Requests\Test\StoreTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ApiResponse;
use App\Models\User;

class TestController extends Controller
{
    public function index(User $user): JsonResponse
    {
        $this->authorize('viewAny', $user);

        $tests = $user->tests()->get();
        return ApiResponse::success($tests);
    }

    public function store(StoreTestRequest $request): JsonResponse
    {
        $this->authorize('create', Test::class);

        $test = Test::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);
        return ApiResponse::success($test, 201);
    }

    public function show(Test $test): JsonResponse
    {
        $this->authorize('view', $test);
        return ApiResponse::success($test);
    }

    public function update(UpdateTestRequest $request, Test $test): JsonResponse
    {
        $this->authorize('update', $test);
        $test->update($request->validated());
        return ApiResponse::success($test);
    }

    public function destroy(Test $test): JsonResponse
    {
        $this->authorize('delete', $test);
        $test->delete();
        return ApiResponse::success(['message' => 'Test deleted successfully.']);
    }
}
