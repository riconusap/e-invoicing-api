<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $invoices = Invoice::paginate(15);
        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'contract_client_id' => 'required|exists:contract_clients,id',
            'stamp_info_id' => 'required|exists:stamp_infos,id',
            'project_type' => 'required|in:project,termin,montly',
            'contract_value' => 'required|numeric',
            'tax_value' => 'required|numeric',
            'faktur_files' => 'nullable|json',
            'faktur_no' => 'nullable|string',
            'discount_value' => 'sometimes|string',
            'termin' => 'required|integer',
        ]);

        $invoice = Invoice::create($validatedData + [
            'uuid' => Str::uuid(),
            'created_by' => auth()->id(),
        ]);

        return response()->json($invoice, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validatedData = $request->validate([
            'contract_client_id' => 'sometimes|required|exists:contract_clients,id',
            'stamp_info_id' => 'sometimes|required|exists:stamp_infos,id',
            'project_type' => 'sometimes|required|in:project,termin,montly',
            'contract_value' => 'sometimes|required|numeric',
            'tax_value' => 'sometimes|required|numeric',
            'faktur_files' => 'nullable|json',
            'faktur_no' => 'nullable|string',
            'discount_value' => 'sometimes|string',
            'termin' => 'sometimes|required|integer',
        ]);

        $invoice->update($validatedData + ['updated_by' => auth()->id()]);

        return response()->json($invoice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(null, 204);
    }
}
