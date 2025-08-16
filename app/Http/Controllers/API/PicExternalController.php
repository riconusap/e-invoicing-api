<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PicExternal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PicExternalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PicExternal::with(['createdBy', 'updatedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by position
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $picExternals = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $picExternals->items(),
            'pagination' => [
                'current_page' => $picExternals->currentPage(),
                'last_page' => $picExternals->lastPage(),
                'per_page' => $picExternals->perPage(),
                'total' => $picExternals->total(),
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
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:pic_externals,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $picExternal = PicExternal::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'position' => $request->position,
            'phone' => $request->phone,
            'email' => $request->email,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PIC External created successfully',
            'data' => $picExternal->load(['createdBy', 'updatedBy'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $picExternal = PicExternal::with(['createdBy', 'updatedBy'])->find($id);

        if (!$picExternal) {
            return response()->json([
                'success' => false,
                'message' => 'PIC External not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $picExternal
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $picExternal = PicExternal::find($id);

        if (!$picExternal) {
            return response()->json([
                'success' => false,
                'message' => 'PIC External not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:pic_externals,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $picExternal->update([
            'name' => $request->name ?? $picExternal->name,
            'position' => $request->position ?? $picExternal->position,
            'phone' => $request->phone ?? $picExternal->phone,
            'email' => $request->email ?? $picExternal->email,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PIC External updated successfully',
            'data' => $picExternal->load(['createdBy', 'updatedBy'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $picExternal = PicExternal::find($id);

        if (!$picExternal) {
            return response()->json([
                'success' => false,
                'message' => 'PIC External not found'
            ], 404);
        }

        $picExternal->update(['deleted_by' => auth()->id()]);
        $picExternal->delete();

        return response()->json([
            'success' => true,
            'message' => 'PIC External deleted successfully'
        ]);
    }
} 