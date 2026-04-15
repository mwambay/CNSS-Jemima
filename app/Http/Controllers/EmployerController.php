<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployerRequest;
use App\Http\Requests\UpdateEmployerRequest;
use App\Models\Employer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EmployerController extends Controller
{
    public function index(): JsonResponse
    {
        $employers = Employer::query()->orderBy('id')->get();

        return response()->json($employers);
    }

    public function store(StoreEmployerRequest $request): JsonResponse
    {
        $employer = Employer::query()->create($request->validated());

        return response()->json($employer, 201);
    }

    public function show(Employer $employer): JsonResponse
    {
        return response()->json($employer);
    }

    public function update(UpdateEmployerRequest $request, Employer $employer): JsonResponse
    {
        $employer->update($request->validated());

        return response()->json($employer->refresh());
    }

    public function destroy(Employer $employer): Response
    {
        $employer->delete();

        return response()->noContent();
    }
}
