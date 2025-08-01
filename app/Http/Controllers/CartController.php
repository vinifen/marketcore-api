<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreCartRequest $request)
    {
        //
    }

    public function show(Cart $cart)
    {
        //
    }

    public function update(UpdateCartRequest $request, Cart $cart)
    {
        //
    }

    public function destroy(Cart $cart)
    {
        //
    }
}
