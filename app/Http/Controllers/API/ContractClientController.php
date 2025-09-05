<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContractClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContractClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContractClient::with(['placement.client', 'createdBy', 'updatedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('placement', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by placement
        if ($request->has('placement_id')) {
            $query->where('placement_id', $request->placement_id);
        }

        // Filter by project type
        if ($request->has('project_type')) {
            $query->where('project_type', $request->project_type);
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
            'placement_id' => 'required|exists:placements,id',
            'contract_value' => 'required|integer|min:0',
            'start_on' => 'required|date',
            'ends_on' => 'required|date|after:start_on',
            'project_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $contract = ContractClient::create([
            'uuid' => Str::uuid(),
            'placement_id' => $request->placement_id,
            'contract_value' => $request->contract_value,
            'start_on' => $request->start_on,
            'ends_on' => $request->ends_on,
            'project_type' => $request->project_type,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract created successfully',
            'data' => $contract->load(['placement.client', 'createdBy', 'updatedBy'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): JsonResponse
    {
        $contract = ContractClient::with(['placement.client', 'createdBy', 'updatedBy', 'invoices'])->where('uuid', $uuid)->first();

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
        $contract = ContractClient::where('uuid', $uuid)->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'placement_id' => 'sometimes|required|exists:placements,id',
            'contract_value' => 'sometimes|required|integer|min:0',
            'start_on' => 'sometimes|required|date',
            'ends_on' => 'sometimes|required|date|after:start_on',
            'project_type' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $contract->update([
            'placement_id' => $request->placement_id ?? $contract->placement_id,
            'contract_value' => $request->contract_value ?? $contract->contract_value,
            'start_on' => $request->start_on ?? $contract->start_on,
            'ends_on' => $request->ends_on ?? $contract->ends_on,
            'project_type' => $request->project_type ?? $contract->project_type,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract updated successfully',
            'data' => $contract->load(['placement.client', 'createdBy', 'updatedBy'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $contract = ContractClient::where('uuid', $uuid)->first();

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
