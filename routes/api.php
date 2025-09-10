<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->get('/test', function () {
    return response()->json(['message' => 'API working!']);
});
