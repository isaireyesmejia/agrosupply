<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(protected PurchaseOrderService $service) {}

    public function index()
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $orders = $this->service->getAll();

        return view('purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $this->authorize('create', PurchaseOrder::class);

        $suppliers = Supplier::all();
        $products = Product::all();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseOrder::class);

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $order = $this->service->create(
            ['supplier_id' => $data['supplier_id'], 'notes' => $data['notes'] ?? null],
            $data['items']
        );

        return redirect()->route('purchase-orders.show', $order)
            ->with('status', 'Orden de compra creada correctamente.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('view', $purchaseOrder);

        $purchaseOrder->load('items.product', 'supplier', 'user');

        return view('purchase-orders.show', ['order' => $purchaseOrder]);
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder);

        $this->service->approve($purchaseOrder);

        return back()->with('status', 'Orden aprobada.');
    }

    public function reject(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('reject', $purchaseOrder);

        $this->service->reject($purchaseOrder);

        return back()->with('status', 'Orden rechazada.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('cancel', $purchaseOrder);

        $this->service->cancel($purchaseOrder);

        return back()->with('status', 'Orden cancelada.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('receive', $purchaseOrder);

        $this->service->receive($purchaseOrder);

        return back()->with('status', 'Orden recibida. Stock actualizado.');
    }
}