<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(): View
    {
        $products = $this->productService->getAll();
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $this->productService->create($data);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(int $id): View
    {
        $product = $this->productService->getById($id);
        return view('products.show', compact('product'));
    }

    public function edit(int $id): View
    {
        $product = $this->productService->getById($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $this->productService->update($id, $data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->productService->delete($id);

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }
}