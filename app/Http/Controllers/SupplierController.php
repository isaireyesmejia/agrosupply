<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    public function __construct(protected SupplierService $service) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:suppliers.view', only: ['index', 'show']),
            new Middleware('permission:suppliers.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $suppliers = $this->service->getAll();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $this->service->create($data);

        return redirect()->route('suppliers.index')->with('status', 'Proveedor creado correctamente.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $this->service->update($supplier, $data);

        return redirect()->route('suppliers.index')->with('status', 'Proveedor actualizado.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->service->delete($supplier);

        return redirect()->route('suppliers.index')->with('status', 'Proveedor eliminado.');
    }
}