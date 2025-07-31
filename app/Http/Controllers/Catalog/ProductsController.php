<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
use App\Models\Products;

class ProductsController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreProductsRequest $request)
    {
        //
    }

    public function show(Products $products)
    {
        //
    }

    public function update(UpdateProductsRequest $request, Products $products)
    {
        //
    }

    public function destroy(Products $products)
    {
        //
    }
}
