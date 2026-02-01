<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CommerceState;
use Illuminate\Http\Request;

class EcommerceStateController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(CommerceState::current());
    }
}

