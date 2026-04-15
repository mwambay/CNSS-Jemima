<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\Employment;
use App\Models\Worker;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WorkerController extends Controller
{
    public function index(): JsonResponse
    {
        $workers = Worker::query()
            ->with(['employments' => function ($query): void {
                $query->with('employer')->orderByDesc('start_date');
            }])
            ->orderBy('id')
            ->get()
            ->map(fn (Worker $worker): array => $this->toPayload($worker));

        return response()->json($workers);
    }

    public function store(StoreWorkerRequest $request): JsonResponse
    {
        $data = $request->validated();

        $worker = DB::transaction(function () use ($data): Worker {
            $worker = Worker::query()->create([
                'social_security_number' => $data['social_security_number'],
                'national_id' => $data['national_id'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => $data['status'] ?? 'ACTIVE',
            ]);

            Employment::query()->create([
                'worker_id' => $worker->id,
                'employer_id' => $data['employer_id'],
                'start_date' => $data['employment_start_date'],
                'contract_type' => $data['contract_type'] ?? null,
                'base_salary' => $data['base_salary'] ?? null,
                'is_declared_active' => true,
            ]);

            return $worker;
        });

        $worker->load(['employments' => function ($query): void {
            $query->with('employer')->orderByDesc('start_date');
        }]);

        return response()->json($this->toPayload($worker), 201);
    }

    public function show(Worker $worker): JsonResponse
    {
        $worker->load(['employments' => function ($query): void {
            $query->with('employer')->orderByDesc('start_date');
        }]);

        return response()->json($this->toPayload($worker));
    }

    public function update(UpdateWorkerRequest $request, Worker $worker): JsonResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($worker, $data): void {
            $worker->update([
                'social_security_number' => $data['social_security_number'],
                'national_id' => $data['national_id'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => $data['status'] ?? 'ACTIVE',
            ]);

            /** @var Employment|null $activeEmployment */
            $activeEmployment = $worker->employments()
                ->where('is_declared_active', true)
                ->orderByDesc('start_date')
                ->first();

            $employmentPayload = [
                'employer_id' => $data['employer_id'],
                'start_date' => $data['employment_start_date'],
                'contract_type' => $data['contract_type'] ?? null,
                'base_salary' => $data['base_salary'] ?? null,
                'is_declared_active' => true,
            ];

            if ($activeEmployment !== null) {
                $activeEmployment->update($employmentPayload);
                return;
            }

            $employmentPayload['worker_id'] = $worker->id;
            Employment::query()->create($employmentPayload);
        });

        $worker->load(['employments' => function ($query): void {
            $query->with('employer')->orderByDesc('start_date');
        }]);

        return response()->json($this->toPayload($worker));
    }

    public function destroy(Worker $worker): Response
    {
        $worker->delete();

        return response()->noContent();
    }

    private function toPayload(Worker $worker): array
    {
        $employment = $worker->employments->firstWhere('is_declared_active', true)
            ?? $worker->employments->first();

        $birthDate = $worker->birth_date;
        $birthDateValue = $birthDate instanceof CarbonInterface
            ? $birthDate->format('Y-m-d')
            : (is_string($birthDate) && $birthDate !== '' ? substr($birthDate, 0, 10) : null);

        $employmentStartDate = $employment?->start_date;
        $employmentStartDateValue = $employmentStartDate instanceof CarbonInterface
            ? $employmentStartDate->format('Y-m-d')
            : (is_string($employmentStartDate) && $employmentStartDate !== '' ? substr($employmentStartDate, 0, 10) : null);

        return [
            'id' => $worker->id,
            'social_security_number' => $worker->social_security_number,
            'national_id' => $worker->national_id,
            'first_name' => $worker->first_name,
            'last_name' => $worker->last_name,
            'birth_date' => $birthDateValue,
            'gender' => $worker->gender,
            'status' => $worker->status,
            'employer_id' => $employment?->employer_id,
            'employer_name' => $employment?->employer?->legal_name,
            'employment_start_date' => $employmentStartDateValue,
            'contract_type' => $employment?->contract_type,
            'base_salary' => $employment?->base_salary,
        ];
    }
}
