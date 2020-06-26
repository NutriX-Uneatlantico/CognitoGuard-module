<?php

namespace Modules\CognitoGuard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CognitoAuthController extends Controller
{
    public function authUser()
    {
        return response()->json(auth()->user());
    }
}
