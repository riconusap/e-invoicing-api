<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContractEmployee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContractEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContractEmployee::with(['employee', 'placement', 'createdBy', 'updatedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by placement
        if ($request->has('placement_id')) {
            $query->where('placement_id', $request->placement_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $contracts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $contracts->items(),
            'pagination' => [
                'current_page' => $contracts->currentPage(),
                'last_page' => $contracts->lastPage(),
                'per_page' => $contracts->perPage(),
                'total' => $contracts->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'nullable|string|max:255',
            'start_on' => 'required|date',
            'ends_on' => 'required|date|after:start_on',
            'thp' => 'required|integer|min:0',
            'daily_wages' => 'required|integer|min:0',
            'account_number' => 'required|string|max:255',
            'bank_id' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'no_bpjstk' => 'nullable|string|max:255',
            'no_bpjskes' => 'nullable|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'placement_id' => 'required|exists:placements,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $contract = ContractEmployee::create([
            'uuid' => Str::uuid(),
            'nip' => $request->nip,
            'start_on' => $request->start_on,
            'ends_on' => $request->ends_on,
            'thp' => $request->thp,
            'daily_wages' => $request->daily_wages,
            'account_number' => $request->account_number,
            'bank_id' => $request->bank_id,
            'account_holder_name' => $request->account_holder_name,
            'no_bpjstk' => $request->no_bpjstk,
            'no_bpjskes' => $request->no_bpjskes,
            'employee_id' => $request->employee_id,
            'placement_id' => $request->placement_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract created successfully',
            'data' => $contract->load(['employee', 'placement', 'createdBy', 'updatedBy'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): JsonResponse
    {
        $contract = ContractEmployee::with(['employee', 'placement', 'createdBy', 'updatedBy'])->where('uuid', $uuid)->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contract
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $contract = ContractEmployee::where('uuid', $uuid)->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nip' => 'nullable|string|max:255',
            'start_on' => 'sometimes|required|date',
            'ends_on' => 'sometimes|required|date|after:start_on',
            'thp' => 'sometimes|required|integer|min:0',
            'daily_wages' => 'sometimes|required|integer|min:0',
            'account_number' => 'sometimes|required|string|max:255',
            'bank_id' => 'sometimes|required|string|max:255',
            'account_holder_name' => 'sometimes|required|string|max:255',
            'no_bpjstk' => 'nullable|string|max:255',
            'no_bpjskes' => 'nullable|string|max:255',
            'employee_id' => 'sometimes|required|exists:employees,id',
            'placement_id' => 'sometimes|required|exists:placements,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $contract->update([
            'nip' => $request->nip ?? $contract->nip,
            'start_on' => $request->start_on ?? $contract->start_on,
            'ends_on' => $request->ends_on ?? $contract->ends_on,
            'thp' => $request->thp ?? $contract->thp,
            'daily_wages' => $request->daily_wages ?? $contract->daily_wages,
            'account_number' => $request->account_number ?? $contract->account_number,
            'bank_id' => $request->bank_id ?? $contract->bank_id,
            'account_holder_name' => $request->account_holder_name ?? $contract->account_holder_name,
            'no_bpjstk' => $request->no_bpjstk ?? $contract->no_bpjstk,
            'no_bpjskes' => $request->no_bpjskes ?? $contract->no_bpjskes,
            'employee_id' => $request->employee_id ?? $contract->employee_id,
            'placement_id' => $request->placement_id ?? $contract->placement_id,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract updated successfully',
            'data' => $contract->load(['employee', 'placement', 'createdBy', 'updatedBy'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $contract = ContractEmployee::where('uuid', $uuid)->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        $contract->update(['deleted_by' => auth()->id()]);
        $contract->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contract deleted successfully'
        ]);
    }
}
