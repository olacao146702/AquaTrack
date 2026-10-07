<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\InventoryItem;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'products' => Product::with('item')->orderByDesc('is_active')->orderBy('product_name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('products.form', ['product' => new Product(['category' => 'Refill', 'is_active' => true]), 'items' => $this->items()]);
    }

    public function store(ProductRequest $request)
    {
        Product::create($this->data($request));

        return redirect()->route('products.index')->with('ok', 'Product added.');
    }

    public function edit(Product $product)
    {
        return view('products.form', ['product' => $product, 'items' => $this->items()]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        // Price changes only affect new sales; past sale items keep the price they were sold at.
        $product->update($this->data($request));

        return redirect()->route('products.index')->with('ok', 'Product updated.');
    }

    private function items()
    {
        return InventoryItem::orderBy('item_name')->get();
    }

    private function data(ProductRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['item_id'] = $data['item_id'] ?? null;

        return $data;
    }
}
