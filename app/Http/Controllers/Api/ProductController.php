<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // LIST semua produk (staff)
    public function index()
    {
        return Product::latest()->get();
    }

    // CREATE produk (staff)
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'  => 'required|string|max:100',
        'price' => 'required|numeric|min:1',
        'stock' => 'required|integer|min:0',
    ]);

    $product = Product::create($validated);

    return response()->json([
        'message' => 'Produk berhasil ditambahkan',
        'data'    => $product
    ], 201);
}


    // UPDATE produk (staff)
    public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }

    $validated = $request->validate([
        'name'  => 'sometimes|required|string|max:100',
        'price' => 'sometimes|required|numeric|min:1',
        'stock' => 'sometimes|required|integer|min:0',
    ]);

    $product->update($validated);

    return response()->json([
        'message' => 'Produk berhasil diperbarui',
        'data'    => $product
    ]);
}


    // DELETE produk (staff)
    public function destroy($id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }

    $product->delete();

    return response()->json([
        'message' => 'Produk berhasil dihapus'
    ]);
}
}