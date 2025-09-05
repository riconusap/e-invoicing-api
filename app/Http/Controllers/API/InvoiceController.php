<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['contractClient.placement.client', 'createdBy', 'updatedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by contract client
        if ($request->has('contract_client_id')) {
            $query->where('contract_client_id', $request->contract_client_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('invoice_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('invoice_date', '<=', $request->end_date);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoices = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'contract_client_id' => 'required|exists:contract_clients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice = Invoice::create([
            'uuid' => Str::uuid(),
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => $request->subtotal,
            'tax' => $request->tax,
            'total' => $request->total,
            'status' => $request->status,
            'notes' => $request->notes,
            'contract_client_id' => $request->contract_client_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully',
            'data' => $invoice->load(['contractClient.placement.client', 'createdBy', 'updatedBy'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): JsonResponse
    {
        $invoice = Invoice::with([
            'contractClient.placement.client',
            'createdBy',
            'updatedBy',
            'invoiceItems',
            'documentAttachments'
        ])->where('uuid', $uuid)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $invoice = Invoice::where('uuid', $uuid)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'invoice_number' => 'sometimes|required|string|unique:invoices,invoice_number,' . $uuid,
            'invoice_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after:invoice_date',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'tax' => 'sometimes|required|numeric|min:0',
            'total' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:pending,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'contract_client_id' => 'sometimes|required|exists:contract_clients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice->update([
            'invoice_number' => $request->invoice_number ?? $invoice->invoice_number,
            'invoice_date' => $request->invoice_date ?? $invoice->invoice_date,
            'due_date' => $request->due_date ?? $invoice->due_date,
            'subtotal' => $request->subtotal ?? $invoice->subtotal,
            'tax' => $request->tax ?? $invoice->tax,
            'total' => $request->total ?? $invoice->total,
            'status' => $request->status ?? $invoice->status,
            'notes' => $request->notes ?? $invoice->notes,
            'contract_client_id' => $request->contract_client_id ?? $invoice->contract_client_id,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully',
            'data' => $invoice->load(['contractClient.placement.client', 'createdBy', 'updatedBy'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $invoice = Invoice::where('uuid', $uuid)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $invoice->update(['deleted_by' => auth()->id()]);
        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    }
}
