<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    protected function findModelOrFail(string $modelClass, int $id): Model
    {
        $model = $modelClass::find($id);

        if (!$model) {
            throw new ApiException("Not found.", null, 404);
        }

        return $model;
    }

    protected function findModelTrashedOrFail(string $modelClass, int $id): Model
    {
        $model = $modelClass::withTrashed()->find($id);

        if (!$model || !$model->trashed()) {
            throw new ApiException("Trashed model not found.", null, 404);
        }

        return $model;
    }
}
