<?php

use App\Http\Controllers\EmployerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('employers', EmployerController::class);
