<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\ContractEmployee;
use App\Models\Placement;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Generate NIP (Nomor Induk Pegawai)
     * Format: YYYYMMDD + 4-digit sequential number
     */
    private function generateNip()
    {
        $date = date('Ymd'); // YYYYMMDD

        // Get the last employee created today
        $lastEmployee = Employee::whereDate('created_at', date('Y-m-d'))
            ->latest('id')
            ->first();

        if ($lastEmployee && strpos($lastEmployee->nip, $date) === 0) {
            // Extract the sequence number from last NIP
            $lastSequence = (int)substr($lastEmployee->nip, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        // Pad sequence with zeros (0001, 0002, ..., 9999)
        $sequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        return $date . $sequence;
    }

    /**
     * API endpoint to suggest/generate NIP
     */
    public function suggestNip()
    {
        try {
            $suggestedNip = $this->generateNip();

            return response()->json([
                'success' => true,
                'data' => [
                    'suggested_nip' => $suggestedNip,
                    'format' => 'YYYYMMDD + 4-digit sequence (e.g., 202602160001)'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate NIP',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);

            $query = Employee::with(['createdBy', 'updatedBy', 'contractEmployees']);

            // Filter by full_name
            if ($request->has('full_name')) {
                $query->where('full_name', 'like', "%{$request->full_name}%");
            }

            // Filter by NIK
            if ($request->has('nik')) {
                $query->where('nik', 'like', "%{$request->nik}%");
            }

            // Filter by NIP
            if ($request->has('nip')) {
                $query->where('nip', 'like', "%{$request->nip}%");
            }

            // Search functionality (searches all three fields)
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            }

            $employees = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $employees->items(),
                'pagination' => [
                    'current_page' => $employees->currentPage(),
                    'last_page' => $employees->lastPage(),
                    'per_page' => $employees->perPage(),
                    'total' => $employees->total(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch employees', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return dd($placementId);


        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:employees',
            'nip' => 'nullable|string|max:255|unique:employees',
            // Contract details (optional)
            'contract_nip' => 'nullable|string|max:255',
            'start_on' => 'nullable|date',
            'ends_on' => 'nullable|date|after:start_on',
            'thp' => 'nullable|integer|min:0',
            'daily_wages' => 'nullable|integer|min:0',
            'account_number' => 'nullable|string|max:255',
            'bank_id' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'no_bpjstk' => 'nullable|string|max:255',
            'no_bpjskes' => 'nullable|string|max:255',
            'placement_id' => 'nullable|exists:placements,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate NIP if not provided
            $nip = $request->nip ?? $this->generateNip();

            $employee = Employee::create([
                'uuid' => Str::uuid(),
                'full_name' => $request->full_name,
                'nik' => $request->nik,
                'nip' => $nip,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // get placement id by request->placement_id
            $placementId = Placement::where('uuid', $request->placement_id)->first()->id ?? null;

            // Create contract employee if contract details provided
            if ($request->has('placement_id') && $request->placement_id) {
                $contractEmployee = ContractEmployee::create([
                    'uuid' => Str::uuid(),
                    'nip' => $request->contract_nip ?? $nip,
                    'start_on' => $request->start_on,
                    'ends_on' => $request->ends_on,
                    'thp' => $request->thp,
                    'daily_wages' => $request->daily_wages,
                    'account_number' => $request->account_number,
                    'bank_id' => $request->bank_id,
                    'account_holder_name' => $request->account_holder_name,
                    'no_bpjstk' => $request->no_bpjstk,
                    'no_bpjskes' => $request->no_bpjskes,
                    'employee_id' => $employee->id,
                    'placement_id' => $placementId,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Employee and contract created successfully',
                    'data' => [
                        'employee' => $employee->load(['createdBy', 'updatedBy']),
                        'contract' => $contractEmployee->load(['employee', 'placement', 'createdBy', 'updatedBy'])
                    ]
                ], 201);
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'data' => $employee->load(['createdBy', 'updatedBy'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $employee = Employee::with(['createdBy', 'updatedBy', 'employeeDocuments', 'contractEmployees'])
                ->where('uuid', $id)
                ->orWhere('id', $id)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('view', $employee);

            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $employee = Employee::where('uuid', $id)
                ->orWhere('id', $id)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('update', $employee);

            $validator = Validator::make($request->all(), [
                'full_name' => 'sometimes|required|string|max:255',
                'nik' => 'sometimes|required|string|max:255|unique:employees,nik,' . $employee->id,
                'nip' => 'sometimes|required|string|max:255|unique:employees,nip,' . $employee->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $employee->update(array_merge(
                $request->only([
                    'full_name', 'nik', 'nip'
                ]),
                ['updated_by' => Auth::id()]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'data' => $employee->fresh()->load(['createdBy', 'updatedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::where('uuid', $id)
                ->orWhere('id', $id)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            // Check authorization
            $this->authorize('delete', $employee);

            $employee->update(['deleted_by' => Auth::id()]);
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
