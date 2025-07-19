<?php

namespace App\Http\Controllers;

use App\Http\Requests\Test\StoreTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\AuthException;

class TestController extends Controller
{
    public function index(): JsonResponse
    {
        $tests = Test::where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' => $tests,
        ]);
    }

    public function store(StoreTestRequest $request): JsonResponse
    {
        $test = Test::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $test,
        ], 201);
    }

    public function show(Test $test): JsonResponse
    {
        $this->authorize('view', $test);

        return response()->json([
            'success' => true,
            'data' => $test,
        ]);
    }

    public function update(UpdateTestRequest $request, Test $test): JsonResponse
    {
        if (!Gate::allows('update', $test)) {
            throw new AuthException(
                ["auth" => ['You do not have permission to update this resource.']],
                null,
                403
            );
        }

        $test->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $test,
        ]);
    }

    public function destroy(Test $test): JsonResponse
    {
        $this->authorize('delete', $test);

        $test->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully.',
        ]);
    }
}
