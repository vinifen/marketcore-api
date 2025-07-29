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
}
