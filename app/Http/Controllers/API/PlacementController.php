<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PlacementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Placement::with(['client', 'createdBy', 'updatedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $placements = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $placements->items(),
            'pagination' => [
                'current_page' => $placements->currentPage(),
                'last_page' => $placements->lastPage(),
                'per_page' => $placements->perPage(),
                'total' => $placements->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'client_id' => 'required|exists:clients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $placement = Placement::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'description' => $request->description,
            'client_id' => $request->client_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Placement created successfully',
            'data' => $placement->load(['client', 'createdBy', 'updatedBy'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $placement = Placement::with(['client', 'createdBy', 'updatedBy', 'contractClients'])->where('uuid', $id)->first();

        if (!$placement) {
            return response()->json([
                'success' => false,
                'message' => 'Placement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $placement
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $placement = Placement::where('uuid', $id)->first();

        if (!$placement) {
            return response()->json([
                'success' => false,
                'message' => 'Placement not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'client_id' => 'sometimes|required|exists:clients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $placement->update([
            'name' => $request->name ?? $placement->name,
            'description' => $request->description ?? $placement->description,
            'client_id' => $request->client_id ?? $placement->client_id,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Placement updated successfully',
            'data' => $placement->load(['client', 'createdBy', 'updatedBy'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $placement = Placement::where('uuid', $id)->first();

        if (!$placement) {
            return response()->json([
                'success' => false,
                'message' => 'Placement not found'
            ], 404);
        }

        $placement->update(['deleted_by' => auth()->id()]);
        $placement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Placement deleted successfully'
        ]);
    }
}
